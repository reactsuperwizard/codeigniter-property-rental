<?php

class ItemLock_model extends CI_Model{
  function __construct() {
    parent::__construct();
    /**
    $itemLockerTypes=$this->db->get('item_locker_type')->result_array();
    foreach($itemLockerTypes AS $t){
      $varCode='ITEM_LOCKER_TYPE_ID_'.strtoupper($t['code']);
      if (!defined($varCode)){
        define($varCode,$t['item_locker_type_id']);
      }
    }**/
  }
  
  function unlock($lockerType,$lockerObjectID,$startTimestamp,$endTimestamp,$isDirect=false){
    $lockerTypeID=$this->getLockerTypeID($lockerType);
    
    $lockHash=md5(microtime(TRUE).'|'.$lockerType.'|'.$lockerObjectID.'|'.$startTimestamp.'|'.$endTimestamp);
    $this->db->query("INSERT IGNORE INTO `item_lock_skipper` "
      ."SELECT '$lockHash' AS `item_lock_hash`,`l`.`item_id`,`l`.`start_timestamp`,(`l`.`quantity`-IFNULL(`ll`.`quantity`,0)) AS `quantity` "
      ."FROM `item_lock` AS `l` "
        ."LEFT JOIN `item_lock_locker` AS `ll` ON ("
          ."ll.item_locker_type_id='$lockerTypeID' "
          ."AND ll.item_locker_object_id='$lockerObjectID' AND ll.item_lock_id=l.item_lock_id"
        .") "
      ."WHERE l.start_timestamp<'$endTimestamp' AND l.end_timestamp>'$startTimestamp' AND `l`.`quantity`>0");

    if (!$isDirect){
      $this->db->query("INSERT IGNORE INTO `item_lock_skipper` "
        ."SELECT '$lockHash' AS `item_lock_hash`,`l`.`item_id`,`l`.`start_timestamp`,(`l`.`quantity`-IFNULL(`ll`.`quantity`,0)) AS `quantity` "
        ."FROM `item_lock` AS `l` "
          ."JOIN `item` AS `i` ON (`i`.`item_id`=`l`.`item_id`) "
          ."JOIN `schedule_logistics_gap` AS `lg` ON (`lg`.`schedule_id`=`i`.`schedule_id` AND `lg`.`weekday`='".date('N',$startTimestamp)."' AND `lg`.`direction`='backwards' AND `lg`.`gap`>0) "
          ."LEFT JOIN `item_lock_locker` AS `ll` ON ("
            ."ll.item_locker_type_id='$lockerTypeID' "
            ."AND ll.item_locker_object_id='$lockerObjectID' AND ll.item_lock_id=l.item_lock_id"
          .") "
        ."WHERE l.start_timestamp<'$endTimestamp' AND l.end_timestamp>('$startTimestamp'-`lg`.`gap`) AND `l`.`quantity`>0");
      $this->db->query("INSERT IGNORE INTO `item_lock_skipper` "
        ."SELECT '$lockHash' AS `item_lock_hash`,`l`.`item_id`,`l`.`start_timestamp`,(`l`.`quantity`-IFNULL(`ll`.`quantity`,0)) AS `quantity` "
        ."FROM `item_lock` AS `l` "
          ."JOIN `item` AS `i` ON (`i`.`item_id`=`l`.`item_id`) "
          ."JOIN `schedule_logistics_gap` AS `lg` ON (`lg`.`schedule_id`=`i`.`schedule_id` AND `lg`.`weekday`='".date('N',$endTimestamp)."' AND `lg`.`direction`='forward' AND `lg`.`gap`>0) "
          ."LEFT JOIN `item_lock_locker` AS `ll` ON ("
            ."ll.item_locker_type_id='$lockerTypeID' "
            ."AND ll.item_locker_object_id='$lockerObjectID' AND ll.item_lock_id=l.item_lock_id"
          .") "
        ."WHERE `l`.`start_timestamp`<('$endTimestamp'+`lg`.`gap`) AND `l`.`end_timestamp`>'$startTimestamp' AND `l`.`quantity`>0");
    }
    
    return $lockHash;
  }
  
  function releaseHash($hash){
    $this->db->delete('item_lock_skipper',array('item_lock_hash'=>$hash));
  }
  
  function unlock__($itemLockerType,$itemLockerObjectID){
    if(is_array($itemLockerObjectID)){
      $lockersToCheck=&$itemLockerObjectID;
    }
    else {
      $lockersToCheck=array($itemLockerObjectID);
    }
    $uniqueLockerIDs=array();
    $validLockerIDs=array();

    foreach($lockersToCheck AS $lockerID){
      $lockerID=$lockerID*1;
      if ($lockerID>0 && !isset($uniqueLockerIDs[$lockerID])){
        $validLockerIDs[]=$lockerID;
        $uniqueLockerIDs[$lockerID]=1;
      }
    }
    if (empty($validLockerIDs)){
      return false;
    }
    
    $itemLockHash=md5(MAIN_TIMESTAMP.'|'.json_encode($validLockerIDs));
    $itemLocks=$this->db
      ->where('item_locker_type_id',constant('ITEM_LOCKER_TYPE_ID_'.strtoupper($itemLockerType)))
      ->where_in('item_locker_object_id',$validLockerIDs)->get('item_lock_locker')->result_array();
    
    $passedItemLocks=array();
    if(!empty($itemLocks)){
      foreach ($itemLocks AS $itemLock){
        if (empty($passedItemLocks[$itemLock['item_lock_id']])){
          $passedItemLocks[$itemLock['item_lock_id']]=1;
          $this->db->where('item_lock_id',$itemLock['item_lock_id'])->update('item_lock',array('subtraction_hash'=>NULL,'subtraction_quantity'=>0));
        }
        $this->db->where(array('item_lock_id'=>$itemLock['item_lock_id'],'subtraction_quantity <'=>$itemLock['quantity']))->update('item_lock',array('subtraction_hash'=>$itemLockHash,'subtraction_quantity'=>$itemLock['quantity']));
      }
    }
    
    return $itemLockHash;
  }
  
  function add($itemID,$quantity,$startTimestamp,$endTimestamp,&$result){
    //$result=array('copy'=>array(),'create'=>array(),'update'=>array());
    
    $existingLocks=$this->db
      ->where('item_id='.($itemID*1).' AND start_timestamp<\''.$endTimestamp.'\' AND end_timestamp>\''.$startTimestamp.'\'')
      ->order_by('start_timestamp','ASC')->get('item_lock')->result_array();
    $ex=count($existingLocks);
    $ei=0;
    if ($ex>0){
      for($ei=0;$ei<$ex;$ei++){
        $entry=$existingLocks[$ei];
        $endEntry=$entry;
        $newEntry=$entry;
        unset($endEntry['item_lock_id'],$newEntry['item_lock_id']);

        if ($entry['start_timestamp']<$startTimestamp){
          $this->db
            ->where('item_lock_id',$entry['item_lock_id'])
            ->update('item_lock',array('end_timestamp'=>$startTimestamp,'dates'=>date('Y-m-d H:i',$entry['start_timestamp']).' - '.date('Y-m-d H:i',$startTimestamp)));

          if ($entry['end_timestamp']>$endTimestamp){
            $endEntry['start_timestamp']=$endTimestamp;
            $endEntry['dates']=date('Y-m-d H:i',$endEntry['start_timestamp']).' - '.date('Y-m-d H:i',$endEntry['end_timestamp']);
            $this->db->insert('item_lock',$endEntry);
            $this->copyLocker($entry['item_lock_id'], $this->db->insert_id());

            $newEntry['start_timestamp']=$startTimestamp;
            $newEntry['end_timestamp']=$endTimestamp;
            $newEntry['quantity']=$entry['quantity']+$quantity;
            $newEntry['dates']=date('Y-m-d H:i',$startTimestamp).' - '.date('Y-m-d H:i',$endTimestamp);
            $this->db->insert('item_lock',$newEntry);
            $newLockID=$this->db->insert_id();
            $this->copyLocker($entry['item_lock_id'], $newLockID);
            $result[$newLockID]=$quantity;
          }
          else {
            if ($entry['end_timestamp']<=$endTimestamp){
              $endEntry['start_timestamp']=$startTimestamp;
              $endEntry['quantity']=$entry['quantity']+$quantity;
              $endEntry['dates']=date('Y-m-d H:i',$startTimestamp).' - '.date('Y-m-d H:i',$endEntry['end_timestamp']);
              $this->db->insert('item_lock',$endEntry);
              $newLockID=$this->db->insert_id();
              $this->copyLocker($entry['item_lock_id'], $newLockID);
              $result[$newLockID]=$quantity;
              
              if ($entry['end_timestamp']<$endTimestamp && $ei==($ex-1)){
                $endEntry['start_timestamp']=$entry['end_timestamp'];
                $endEntry['end_timestamp']=$endTimestamp;
                $endEntry['quantity']=$quantity;
                $endEntry['dates']=date('Y-m-d H:i',$endEntry['start_timestamp']).' - '.date('Y-m-d H:i',$endEntry['end_timestamp']);
                $this->db->insert('item_lock',$endEntry);
                $result[$this->db->insert_id()]=$quantity;
              }
            }
          }
        }
        else {
          if ($ei==0){
            if ($entry['start_timestamp']>$startTimestamp){
              $this->db->insert('item_lock',array('item_id'=>$itemID,'start_timestamp'=>$startTimestamp,'end_timestamp'=>$entry['start_timestamp'],'quantity'=>$quantity,'dates'=>date('Y-m-d H:i',$startTimestamp).' - '.date('Y-m-d H:i',$entry['start_timestamp'])));
              $result[$this->db->insert_id()]=$quantity;
            }
          }
          elseif ($existingLocks[$ei-1]['end_timestamp']<$entry['start_timestamp']){
            $this->db->insert('item_lock',array('item_id'=>$itemID,'start_timestamp'=>$existingLocks[$ei-1]['end_timestamp'],'end_timestamp'=>$entry['start_timestamp'],'quantity'=>$quantity,'dates'=>date('Y-m-d H:i',$existingLocks[$ei-1]['end_timestamp']).' - '.date('Y-m-d H:i',$entry['start_timestamp'])));
            $result[$this->db->insert_id()]=$quantity;
          }

          if ($entry['end_timestamp']>$endTimestamp) {
            $endEntry['start_timestamp']=$endTimestamp;
            $endEntry['dates']=date('Y-m-d H:i',$endTimestamp).' - '.date('Y-m-d H:i',$endEntry['end_timestamp']);
            $this->db->insert('item_lock',$endEntry);
            $this->copyLocker($entry['item_lock_id'], $this->db->insert_id());

            $this->db->where('item_lock_id',$entry['item_lock_id'])
              ->update('item_lock',array('end_timestamp'=>$endTimestamp,'quantity'=>($entry['quantity']+$quantity),'dates'=>date('Y-m-d H:i',$entry['start_timestamp']).' - '.date('Y-m-d H:i',$endTimestamp)));
            $result[$entry['item_lock_id']]=$quantity;
          }
          else {
            $this->db->where('item_lock_id',$entry['item_lock_id'])->update('item_lock',array('quantity'=>($entry['quantity']+$quantity)));
            $result[$entry['item_lock_id']]=$quantity;
            
            if ($entry['end_timestamp']<$endTimestamp && $ei==($ex-1)){
              $endEntry['start_timestamp']=$entry['end_timestamp'];
              $endEntry['end_timestamp']=$endTimestamp;
              $endEntry['quantity']=$quantity;
              $endEntry['dates']=date('Y-m-d H:i',$entry['end_timestamp']).' - '.date('Y-m-d H:i',$endTimestamp);
              $this->db->insert('item_lock',$endEntry);
              $result[$this->db->insert_id()]=$quantity;
            }
          }
        }
      }
    }
    else {
      $this->db->insert('item_lock',array('item_id'=>$itemID,'start_timestamp'=>$startTimestamp,'end_timestamp'=>$endTimestamp,'quantity'=>$quantity,'dates'=>date('Y-m-d H:i',$startTimestamp).' - '.date('Y-m-d H:i',$endTimestamp)));
      $result[$this->db->insert_id()]=$quantity;
    }
  }
  
  function copyLocker($sourceID,$destinationID){
    $this->db->query("INSERT INTO `item_lock_locker` SELECT `item_locker_type_id`,`item_locker_object_id`,'".($destinationID*1)."' AS `item_lock_id`, `quantity` FROM `item_lock_locker` WHERE `item_lock_id`='".($sourceID*1)."'");
  }
  
  function removeLockers($lockerType,$lockerObjectID,$all=false){
    if(is_int($lockerType)){
      $lockerTypeID=$lockerType;
    }
    else {
      $lockerTypeID=$this->getLockerTypeID($lockerType);
    }
    
    $this->db->query("UPDATE `item_lock` AS `l` "
      ."JOIN `item_lock_locker` AS `ll` ON (`ll`.`item_locker_type_id`='$lockerTypeID' AND `ll`.`item_locker_object_id`='$lockerObjectID' AND `ll`.`item_lock_id`=`l`.`item_lock_id`) SET `l`.`quantity`=(`l`.`quantity`-`ll`.`quantity`)");
    if ($all){
      $this->db->delete('item_lock_locker',array('item_locker_type_id'=>$lockerTypeID,'item_locker_object_id'=>$lockerObjectID));
      return false;
    }
    $toRemove=$this->db
      ->select('l.item_lock_id')
      ->join('item_lock AS l','l.item_lock_id=ll.item_lock_id AND l.quantity=0')
      ->get_where('item_lock_locker AS ll',array('ll.item_locker_type_id'=>$lockerTypeID,'ll.item_locker_object_id'=>$lockerObjectID))->result_array();
    
    if(!empty($toRemove)){
      foreach($toRemove AS $lockData){
        //$this->db->delete('item_lock',array('item_lock_id'=>$lockData['item_lock_id'],'quantity'=>0));
        $this->db->delete('item_lock_locker',array('item_locker_type_id'=>$lockerTypeID,'item_locker_object_id'=>$lockerObjectID,'item_lock_id'=>$lockData['item_lock_id']));
      }
    }
  }
  
  function replaceLockers($lockerType,$lockerObjectID,$list){
    
    $lockerTypeID=$this->getLockerTypeID($lockerType);
    
    $this->removeLockers($lockerType, $lockerObjectID);
    
    if (!empty($list)){
      foreach($list AS $itemLockID=>$quantity){
        $this->db->query("INSERT INTO `item_lock_locker` SET `item_locker_type_id`='".$lockerTypeID."',`item_locker_object_id`='".($lockerObjectID*1)."',`item_lock_id`='".($itemLockID*1)."',`quantity`='".$quantity."' ON DUPLICATE KEY UPDATE `quantity`='".$quantity."'");
      }
    }
  }
  
  function cancelLocks($list){
    foreach($list AS $itemLockID=>$quantity){
      $this->db->where('item_lock_id',$itemLockID)
        ->set('`quantity`','(`quantity`-'.(int)$quantity.')',FALSE)
        ->update('item_lock');
    }
  }
  
  function refreshLocks($lockerType,$lockerObjectID){
    $lockerTypeID=$this->getLockerTypeID($lockerType);
    $this->db->query("UPDATE `item_lock` AS `l` "
      ."JOIN `item_lock_locker` AS `ll` ON (`ll`.`item_locker_type_id`='$lockerTypeID' AND `ll`.`item_locker_object_id`='$lockerObjectID' AND `ll`.`item_lock_id`=`l`.`item_lock_id`) SET `l`.`quantity`=(`l`.`quantity`+`ll`.`quantity`)");
  }
  
  
  function validateTimestamps($lockerType,$lockerObjectID,$initialTimestamps){
    $CI=& get_instance();

    $lockerTypeID=$this->getLockerTypeID($lockerType);
    $existing=$this->db->select('l.*')
      ->join('item_lock AS l','l.item_lock_id=ll.item_lock_id')
      ->get_where('item_lock_locker AS ll',array(
        'll.item_locker_type_id'=>$lockerTypeID
        ,'ll.item_locker_object_id'=>$lockerObjectID
      ))->result_array();
    
    if (!empty($existing)){
      $ordered=array();
      $itemIDs=array();
      foreach($existing AS $lockData){
        if(empty($ordered[$lockData['item_id']])){
          $ordered[$lockData['item_id']]=array();
          $itemIDs[]=$lockData['item_id'];
        }
        $ordered[$lockData['item_id']][$lockData['start_timestamp']]=$lockData['end_timestamp'];
      }
      foreach($itemIDs AS $itemID){
        ksort($ordered[$itemID]);
        $t=0;
        $previousTimestamp=$initialTimestamps['start'];
        $CI->reply['data']['timestamp_validation'][]=$itemID.' '.date('Y-m-d H:i:s',$previousTimestamp);
        foreach($ordered[$itemID] AS $startTimestamp=>$endTimestamp){
          $CI->reply['data']['timestamp_validation'][]=$itemID.' '.date('Y-m-d H:i:s',$startTimestamp).' - '.date('Y-m-d H:i:s',$endTimestamp);
          if ($startTimestamp!=$previousTimestamp){
            $CI->error('Period has problems from '.date('Y-m-d H:i:s',$previousTimestamp).' to '.date('Y-m-d H:i:s',$startTimestamp));
            return false;
          }
          
          $previousTimestamp=$endTimestamp;
        }
        if ($previousTimestamp!=$initialTimestamps['end']){
          $CI->error('Period has problems from '.date('Y-m-d H:i:s',$previousTimestamp).' to '.date('Y-m-d H:i:s',$initialTimestamps['end']));
          return false;
        }
      }
    }
    return true;
  }
  
  function getLockerTypeID($lockerType){
    return get_instance()->getTargetObjectTypeID($lockerType);
    //return constant('ITEM_LOCKER_TYPE_ID_'.strtoupper($lockerType));
  }
  
  function getLockedItemIDs($lockerType,$lockerObjectID){
    $lockerTypeID=$this->getLockerTypeID($lockerType);
    return $this->db->select('DISTINCT `l`.`item_id`,`ip`.`item_package_id`',false)
      ->join('item_lock AS l','l.item_lock_id=ll.item_lock_id')
      ->join('item_package AS ip','ip.item_id=l.item_id','left')
      ->get_where('item_lock_locker AS ll',array(
        'll.item_locker_type_id'=>$lockerTypeID
        ,'ll.item_locker_object_id'=>$lockerObjectID
      ))->result_array();
  }
}

?>