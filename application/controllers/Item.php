<?php

ini_set('display_errors',1);
//error_reporting(E_ALL^E_NOTICE);

class Item extends NS_Rental_Controller {
  function __construct($config = array()) {
    parent::__construct($config);
    
    
    $IP=$_SERVER['REMOTE_ADDR'];
    if (!empty($_GET['debug']) && $_GET['debug']=='profiler'){
      $this->output->enable_profiler(TRUE);
    }
    $this->loadTranslationCodes();
  }
  
  function index(){
    $this->load->model('Category_model');
    $this->reply['config']['categories']=$this->Category_model->getAll();
    if ($this->userInfo['role']=='admin'){
      
      $this->setTemplate('backend','item/management');
      $this->output->enable_profiler(TRUE);
    }
  }
  /** /
  function migrate_files(){
    $items=$this->db->get('item')->result_array();
    foreach($items AS $i){
      $this->db->insert('file_filter',array('file_filter_id'=>$i['item_id'],'hash'=>md5('item_'.$i['item_id'].'_gallery')));
      $this->db->where('item_id',$i['item_id'])->update('item',array('file_filter_id'=>$i['item_id']));
    }
    
    $this->db->query('INSERT IGNORE INTO `file` (`file_filter_id`,`timestamp`,`folder`,`name`,`order`) SELECT `foreign_id` AS `file_filter_id`, '.time().' AS `timestamp`, \'old\' AS `folder`, REPLACE(`source_path`,\'app/web/upload/source/\',\'\') AS `name`,`sort` AS `order` FROM `equipment_plugin_gallery` WHERE `model`=\'pjItem\'');
  }/**/

  function generate1k(){
    $lowItems=$this->db->get_where('item',array('item_id <'=>1000))->result_array();
    $lowPacked=$this->db->get_where('item_package',array('item_package_id <'=>1000))->result_array();
    $lowCategorized=$this->db->get_where('item_category',array('item_id <'=>1000))->result_array();
    $lowTags=$this->db->get_where('tag_target',array('target_object_id <'=>1000))->result_array();
    $lowTranslation=$this->db->select('t.*')
      ->join('target_object_type_field AS ttof','ttof.target_object_type_field_id=t.target_object_type_field_id')
      ->join('target_object_type AS tto','tto.target_object_type_id=ttof.target_object_type_id AND tto.code=\'item\'')
      ->get_where('translation AS t',array('t.target_object_id <'=>1000))->result_array();

    for($step=1000;$step<=100000;$step+=1000){
      $newItems=array();
      foreach($lowItems AS $i){
        $i['item_id']+=$step;
        $newItems[]=$i;
      }
      $this->db->insert_batch('item',$newItems);

      $newPacked=array();
      foreach($lowPacked AS $pi){
        $pi['item_id']+=$step;
        $pi['item_package_id']+=$step;
        $newPacked[]=$pi;
      }
      $this->db->insert_batch('item_package',$newPacked);

      $newCategorized=array();
      foreach($lowCategorized AS $c){
        $c['item_id']+=$step;
        $newCategorized[]=$c;
      }
      $this->db->insert_batch('item_category',$newCategorized);

      $newTags=array();
      foreach($lowTags AS $t){
        $t['target_object_id']+=$step;
        $newTags[]=$t;
      }
      $this->db->insert_batch('tag_target',$newTags);

      $newTranslation=array();
      foreach($lowTranslation AS $t){
        $t['target_object_id']+=$step;
        $newTranslation[]=$t;
      }
      $this->db->insert_batch('translation',$newTranslation);
    }
  }

  function filtered(){
    $config=array();
    $this->load->model('Item_model');

    $strictTimestamps=false;
    
    if (!empty($_POST['period'])){
      $this->validateRentTimestamps($_POST['period']);
      $config['rentTimestamps']=$this->rentTimestamps['final'];
      if (!empty($_POST['strictRentTimestamps'])){
        $strictTimestamps=true;
      }
      $config['strictRentTimestamps']=$strictTimestamps;
      
      
      if (!empty($_POST['skipQuote']) && empty($_POST['unlock'])){
        $_POST['unlock']=array('type'=>'quote','id'=>$_POST['skipQuote']);
      }
      if (!empty($_POST['unlock'])){
        $this->load->model('ItemLock_model');
        $config['lockHash']=$this->ItemLock_model->unlock($_POST['unlock']['type'],($_POST['unlock']['id']*1),$config['rentTimestamps']['start'],$config['rentTimestamps']['end'],$strictTimestamps);
      }
      
    }
    
    if (!empty($_POST['search'])){
      if (!empty($_POST['search']['value'])){
        $textLookup=trim($this->security->xss_clean($_POST['search']['value']));
        if ($textLookup!=''){
          $config['textLookup']=$textLookup;
        }
      }
    }
    
    if (isset($_POST['itemID'])){
      $itemID=$_POST['itemID']*1;
      
      if ($itemID==0){
        $this->error('bad_item_id',null,true);
      }

      if (isset($_POST['isPackage'])){
        if ($_POST['isPackage']==1){
          $config['packageID']=$itemID;
        }
        else {
          $config['onlyChosen']=array($itemID);
        }
      }
      else {
        $config['onlyChosen']=array($itemID);
        $config['checkPackages']=1;
      }
      /** /
      if (isset($_POST['isPackage'])){
        if ($_POST['isPackage']==1){
          $config['packageID']=$itemID;
          if ($itemID==0){
            $this->error('bad_package_id',null,true);
          }
        }
        else {
          if ($itemID==0){
            $this->error('bad_item_id',null,true);
          }
          $config['onlyChosen']=array($itemID);
        }
      }
      else {
        $config['checkPackages']=1;
      }/**/
    }
    else {
      $config['checkPackages']=1;
    }

    if ($this->userInfo['role']=='admin'){
      $config['thumbnails']=1;
    }
    else {
      $config['publicOnly']=1;
      $config['skipText']=1;
    }
    
    //$config['stickLockHash']=true;
    $this->Item_model->getFiltered($config);
    
    if (!empty($config['rentTimestamps'])){
      foreach($this->reply['data']['entries'] AS &$e){
        if (!empty($e['final_timestamp_start'])){
          $e['delivery_code']=date('YmdHi',($e['final_timestamp_start']+DELIVERY_TIME_PREFIX));
          $e['collection_code']=date('YmdHi',($e['final_timestamp_end']-COLLECTION_TIME_SUFFIX));
        }
      }
    }
  }
  
  function filtered_($config=array()){
    $this->load->library('DataTable',array('count'=>'i.item_id'));

    $this->datatable->from('item AS i')
      ->select('i.item_id,i.quantity,t1.content AS title,t2.content AS description,IFNULL(f.folder,\'\') AS folder,f.name AS filename')
      ->join('translation AS t1','t1.target_object_id=i.item_id AND t1.target_object_type_field_id='.ITEM_OBJECT_TITLE.' AND t1.language_id=1','inner')
      ->join('translation AS t2','t2.target_object_id=i.item_id AND t2.target_object_type_field_id='.ITEM_OBJECT_DESCRIPTION.' AND t2.language_id=1','left')
      ->join('file AS f','f.file_filter_id=i.file_filter_id AND f.file_filter_id>0 AND `order`=1','left');
    
    if ($this->userInfo['role']=='admin'){
      $nonPackage=$this->input->post('non_package');
      if (!empty($nonPackage)){
        $this->datatable
        ->join('item_package AS ip','ip.item_package_id=i.item_id','right outer');
      }
      if (isset($config['public_only'])){
        $this->datatable->where(array('i.is_active'=>1,'i.is_public'=>1));
      }
    }
    else {
      $periodDefined=false;
      if (!empty($_SESSION['ERent_Cart']['date_from'])){
        $startRentDate=$_SESSION['ERent_Cart']['date_from'].' '.date('H:i:s');
        $endRentDate=$_SESSION['ERent_Cart']['date_to'].' '.date('H:i:s');
        $periodDefined=true;
      
        $this->datatable
          ->join('booked_item_log AS l1','l1.item_id=i.id AND l1.start_time<\''.$endRentDate.'\' AND l1.end_time>\''.$startRentDate.'\'','left')
          ->join('booked_item_log AS l2','l2.item_id=i.id AND l2.start_time<\''.$endRentDate.'\' AND l2.end_time>\''.$startRentDate.'\' AND l2.quantity>l1.quantity','left')
          ->select('IFNULL(l1.quantity,0) AS booked')
          ->where('l2.quantity IS NULL');
      }
      else {
        $this->datatable->select('0 AS booked');
      }
      $this->datatable->where(array('i.is_active'=>1,'i.is_public'=>1));
    }
    
    $categoryID=$this->input->post('category_id')*1;
    if ($categoryID>0){
      $this->datatable
        ->join('item_category AS ic','ic.item_id=i.item_id AND ic.category_id='.$categoryID,'inner');
      if ($this->userInfo['user_id']==0){
        $this->datatable->join('category AS c','c.category_id=ic.category_id AND ic.is_active=1 AND ic.is_public=1','inner');
      }
    }
    
    $searchTerm='';
    if (!empty($_POST['search'])){
      if (is_array($_POST['search'])){
        if (!empty($_POST['search']['value'])){
          $searchTerm=$this->security->xss_clean($_POST['search']['value']);
        }
      }
      else {
        $searchTerm=$this->security->xss_clean($_POST['search']);
      }
    }
    if (!empty($searchTerm)){
      $this->datatable->filter()->group_start()
        ->like('t1.content',$searchTerm,'both')
        ->group_end();
    }
    
    $order='i.item_id';
    //$_POST['order'][0]['dir']
    $this->datatable
      //->order_by($order,'asc',TRUE)
      ->order_by('t1.target_object_type_field_id ASC, t1.content ASC')
      ->run($this->reply['data']);
  }
  
  function availability(){
    if (REQUEST_TYPE!='JSON'){
      $periodSource=&$_GET;
      $this->output->enable_profiler(TRUE);
    }
    else {
      $periodSource=&$_POST['period'];
    }
    
    $startDateParts=explode('-',$periodSource['start_date']);
    $startTimestamp=mktime(0,0,0,$startDateParts[1],$startDateParts[2],$startDateParts[0]);
    $endDateParts=explode('-',$periodSource['end_date']);
    $endTimestamp=mktime(0,0,0,$endDateParts[1],$endDateParts[2],$endDateParts[0]);
    
    $cacheDir=APPPATH.'../uploads/cached';
    $result=array();
    $this->reply['config']['dateLabels']=[];
    for ($timestamp=$startTimestamp;$timestamp<=$endTimestamp;$timestamp+=86400){
      $dateLabel=date('Ymd',$timestamp);
      $this->reply['config']['dateLabels'][]=$dateLabel;
      include($cacheDir.'/item_'.$dateLabel.'.php');
      if (empty($result)){
        $result=$itemAvailability;
      }
      else {
        foreach($itemAvailability AS $itemID=>$quantity){
          if ($quantity<$result[$itemID]){
            $result[$itemID]=$quantity;
          }
        }
      }
      /** /
      $tempResult=json_decode(file_get_contents($cacheDir.'/item_'.$dateLabel.'_json.js'),true);
      
      /**/
    }
    $this->reply['data']=&$result;
    
    echo json_encode($this->reply);
    die();
    if (REQUEST_TYPE!='JSON'){
      echo json_encode($this->reply);
      $this->setTemplate('basic');
      
    }
  }
  
  function save(){
    $dataSet=$this->input->post(array('price','quantity','is_active','is_public'));
    foreach($dataSet AS $k=>$v){
      $dataSet[$k]=$v*1;
    }
    if ($dataSet['price']==0 && $dataSet['is_active']==1 && $dataSet['is_public']==1){
      $this->error($this->lang->phrase('price_should_be_set_for_active_and_public'));
    }
    if ($dataSet['quantity']==0 && $dataSet['is_active']==1){
      $this->error($this->lang->phrase('quantity_should_be_set_for_active'));
    }
    
    if (!$this->hasErrors()){
      $translationVersions=array(1);
      $translations=array();
      foreach($translationVersions AS $v){
        $title=trim($this->security->xss_clean($_POST['title'][$v]));
        if (!empty($title)){
          $translations[$v]=array(
            'title'=>$title
            ,'summary'=>trim($this->security->xss_clean($_POST['summary'][$v]))
            ,'description'=>trim($this->security->xss_clean($_POST['description'][$v]))
            ,'embed'=>(!empty($_POST['embed']))?trim($_POST['embed'][$v]):''
          );
        }
      }
    
      if (empty($translations[1])){
        return $this->error($this->lang->phrase('title_should_be_set'));
      }
    }
    
    if (!$this->hasErrors()){
      $itemID=$this->input->post('item_id')*1;
      if ($itemID==0){
        $dataSet['file_filter_id']=0;
        $this->db->insert('item',$dataSet);
        $itemID=$this->db->insert_id();
      }
      else {
        $this->db->where('item_id',$itemID)->update('item',$dataSet);
      }
      $this->reply['data']['item_id']=$itemID;
      
      $existingTranslations=$this->db->select('t.language_id AS l, tof.code AS f')
        ->join('target_object_type_field AS tof','tof.target_object_type_field_id=t.target_object_type_field_id AND tof.target_object_type_id='.ITEM_OBJECT,'inner')
        ->get_where('translation AS t',array('t.target_object_id'=>$itemID))->result_array();
      $toUpdate=array();
      foreach($existingTranslations AS $et){
        $toUpdate[$et['l'].'_'.$et['f']]=1;
      }
      
      foreach($translations AS $v=>$data){
        foreach($data AS $f=>$c){
          
          $dataSet=array(
            'target_object_id'=>$itemID
            ,'target_object_type_field_id'=>constant(strtoupper('ITEM_OBJECT_'.$f))
            ,'language_id'=>$v
          );
          if (!empty($toUpdate[$v.'_'.$f])){
            $this->db->where($dataSet)->update('translation',array('content'=>$c));
          }
          else {
            $dataSet['content']=$c;
            $this->db->insert('translation',$dataSet);
          }
        }
      }
      
      $this->updatePackage($itemID);
      $this->updateFixedQuantity($itemID);
      $this->updateFixedPricing($itemID);
      
      $this->db->where('item_id',$itemID)->delete('item_category');
      foreach($_POST['categories'] AS $c){
        $c*=1;
        if ($c>0){
          $this->db->insert('item_category',array('item_id'=>$itemID,'category_id'=>$c));
        }
      }
      
      
      
      if (!empty($_POST['tags'])){
        $this->load->model('Tag_model');
        $this->Tag_model->updateByTarget('item',$itemID,$_POST['tags']);
      }
      
      if (!$this->hasErrors()){
        $this->load->model('File_model');
        $removedFiles=array();
        foreach($_POST['removed_files'] AS $rf){
          $rf*=1;
          if ($rf>0){
            $this->File_model->prepareRemoval($rf);
            $removedFiles[$rf]=1;
          }
        }
        $o=1;
        foreach($_POST['gallery_files'] AS $gf){
          $gf*=1;
          if ($gf>0 && !isset($removedFiles[$gf])){
            $this->db->where('file_id',$gf)->update('file',array('order'=>$o));
            $o++;
          }
        }
        
        //$oldFileFilterID=$this->db->get_where('item',array('item_id'=>$itemID),1)->row_array()['file_filter_id'];
        
        $fileFilterID=$this->File_model->updateFilter('gallery_file_hash_'.($_POST['gallery_file_hash']*1),'item_'.$itemID.'_gallery');
        
        $this->db->where(array('item_id'=>$itemID,'file_filter_id'=>0))->update('item',array('file_filter_id'=>$fileFilterID));
      }
      
      
      
      $this->loadDetails($itemID);
      if (!empty($_POST['callbackConfig'])){
        $this->reply['data']['callbackConfig']=$_POST['callbackConfig'];
      }
    }
  }
  
  function updateFilters(){
    $this->load->model('File_model');
    $items=$this->db->get('item')->result_array();
    foreach($items AS $i){
      if ($i['file_filter_id']>0){
        $ff=$this->File_model->getFilterID('item_'.$i['item_id'].'_gallery',true);
        $this->db->where('file_filter_id',$ff)
          ->update('file_filter',array('file_filter_id'=>$i['file_filter_id']));
      }
    }    
  }
  
  function updatePackage($packageID=0){
    if ($packageID==0){
      $packageID=$this->input->post('item_id')*1;
    }
    if ($packageID==0){
      return $this->error($this->lang->phrase('package_not_recognized'));
    }
    
    if (!empty($_POST['packed_items'])){
      $oldData=$this->db->select('ip.*')
        ->get_where('item_package AS ip',array('ip.item_package_id'=>$packageID))->result_array();
      
      $oldSet=array();
      foreach($oldData AS $i){
        $oldSet[]=$i['item_package_id'].'_'.$i['item_id'].'_'.$i['quantity'].'_'.$i['percentage'];
      }
      sort($oldSet);
      
      $newSet=array();
      foreach($_POST['packed_items'] AS $itemID){
        $itemID*=1;        
        if ($itemID==0){
          return $this->error($this->lang->phrase('empty_item_for_package_detected'));
        }
        $quantity=$_POST['quantity_'.$itemID]*1;
        if ($quantity==0){
          return $this->error($this->lang->phrase('quantity_for_packed_item_should_be_set'));
        }
        $percentage=$_POST['percentage_'.$itemID]*1;
        if ($percentage==0){
          return $this->error($this->lang->phrase('percentage_for_packed_item_should_be_set'));
        }
        
        $dataSets[]=array('item_package_id'=>$packageID,'item_id'=>$itemID,'quantity'=>$quantity,'percentage'=>$percentage);
        $newSet[]=$packageID.'_'.$itemID.'_'.$quantity.'_'.$percentage;
      }
      sort($newSet);
      
      if ($oldSet!==$newSet){
        $this->db->where('item_package_id',$packageID)->delete('item_package');
        foreach($dataSets AS $dataSet){
          $this->db->insert('item_package',$dataSet);
        }
        
        $minQuantity=$this->db
          ->select('MIN(FLOOR(`i`.`quantity`/`ip`.`quantity`*`ip`.`percentage`/100)) AS `min_quantity`',FALSE)
          ->join('item AS i','i.item_id=ip.item_id','left')
          ->get_where('item_package AS ip',array('ip.item_package_id'=>$packageID),1)->row_array()['min_quantity'];
        if ($minQuantity==0){
          $minQuantity=1;
        }
        $this->db->where('item_id',$packageID)->update('item',array('quantity'=>$minQuantity));
      }
    }
  }
  
  function getFixedQuantity($itemID,$plainDates=false){
    $data=$this->db->order_by('item_id','ASC')->order_by('is_active','DESC')->order_by('start_timestamp','ASC')->get_where('item_quantity_fixed',array('item_id'=>$itemID,'is_active'=>'1','end_timestamp'>MAIN_TIMESTAMP))->result_array();
    if ($plainDates){
      $x=count($data);
      for($i=0;$i<$x;$i++){
        $data[$i]['start']=date('Ymd',$data[$i]['start_timestamp']);
        $data[$i]['end']=date('Ymd',$data[$i]['end_timestamp']);
      }
    }
    return $data;
  }
  
  function updateFixedQuantity($itemID){
    $oldSet=$this->getFixedQuantity($itemID);
    
    $oldI=0;
    $oldX=0;
    if (!empty($oldSet)){
      $oldX=count($oldSet);
    }
    
    $newSet=array();
    if(!empty($_POST['fixed_quantity'])){
      foreach($_POST['fixed_quantity'] AS $entry){
        if (!preg_match_all('/^20(1[8-9]|[2-9][0-9])(0[1-9]|1[0-2])([0-2][0-9]|3[0-1])$/',$entry['start'],$matches)){
          return $this->error($this->lang->phrase('FIXED_QUANTITY_BAD_START_DATE').' ('.$entry['start'].')');
        }
        if (!preg_match_all('/^20(1[8-9]|[2-9][0-9])(0[1-9]|1[0-2])([0-2][0-9]|3[0-1])$/',$entry['end'],$matches)){
          return $this->error($this->lang->phrase('FIXED_QUANTITY_BAD_END_DATE').' ('.$entry['end'].')');
        }
        $newSet[]=array(
          'start_timestamp'=>mktime(
            14,0,0
            ,substr($entry['start'],4,2),substr($entry['start'],6,2),substr($entry['start'],0,4)
          )
          ,'end_timestamp'=>mktime(
            13,59,59
            ,substr($entry['end'],4,2),substr($entry['end'],6,2),substr($entry['end'],0,4)
          )
          ,'quantity'=>$entry['quantity']
        );
      }
    }
    
    foreach($newSet AS $newEntry){
      $updateRequired=true;
      if ($oldI<$oldX){
        for($i=$oldI;$i<$oldX;$i++){
          if($oldSet[$i]['is_active']==1){
            if (
              $oldSet[$i]['start_timestamp']==$newEntry['start_timestamp'] 
              && $oldSet[$i]['end_timestamp']==$newEntry['end_timestamp'] 
              && $oldSet[$i]['quantity']==$newEntry['quantity']
            ){
              $updateRequired=false;
              $oldSet[$i]['is_active']=0;
            }
            elseif (
              $oldSet[$i]['start_timestamp']>=$newEntry['start_timestamp']
            ){
              if ($oldSet[$i]['start_timestamp']<$newEntry['end_timestamp']){
                if ($oldSet[$i]['end_timestamp']<=$newEntry['end_timestamp']){
                  $this->db->where($oldSet[$i])->update('item_quantity_fixed',array('is_active'=>0));
                  $oldSet[$i]['is_active']=0;
                }
                else {
                  $this->db->where($oldSet[$i])->update('item_quantity_fixed',array('is_active'=>0));
                  $oldSet[$i]['start_timestamp']=$newEntry['end_timestamp']+1;
                  $oldSet[$i]['request_timestamp']=MAIN_TIMESTAMP;
                  $this->db->insert('item_quantity_fixed',$oldSet[$i]);
                }
              }
            }
            elseif ($oldSet[$i]['end_timestamp']>$newEntry['start_timestamp']) {
              $this->db->where($oldSet[$i])->update('item_quantity_fixed',array('is_active'=>0));
              $oldSet[$i]['end_timestamp']=$newEntry['start_timestamp']-1;
              $oldSet[$i]['request_timestamp']=MAIN_TIMESTAMP;
              $this->db->insert('item_quantity_fixed',$oldSet[$i]);
              $oldSet[$i]['is_active']=0;
            }
            else {
              $oldSet[$i]['is_active']=0;
            }
          }
        }
      }
      if ($updateRequired){
        $this->db->insert('item_quantity_fixed',array(
          'item_id'=>$itemID
          ,'is_active'=>1
          ,'start_timestamp'=>$newEntry['start_timestamp']
          ,'end_timestamp'=>$newEntry['end_timestamp']
          ,'quantity'=>$newEntry['quantity']
          ,'request_timestamp'=>MAIN_TIMESTAMP
        ));
      }
    }
  }
  
  function getFixedPricing($itemID,$plainDates=false){
    $data=$this->db->order_by('item_id','ASC')->order_by('is_active','DESC')->order_by('start_timestamp','ASC')
      ->get_where('item_price_fixed',array('item_id'=>$itemID,'is_active'=>'1','end_timestamp'=>MAIN_TIMESTAMP))
      ->result_array();
    if ($plainDates){
      $x=count($data);
      for($i=0;$i<$x;$i++){
        $data[$i]['start']=date('Ymd',$data[$i]['start_timestamp']);
        $data[$i]['end']=date('Ymd',$data[$i]['end_timestamp']);
      }
    }
    return $data;
  }
  
  function updateFixedPricing($itemID){
    $oldSet=$this->getFixedPricing($itemID);
    
    $oldI=0;
    $oldX=0;
    if (!empty($oldSet)){
      $oldX=count($oldSet);
    }
    
    $newSet=array();
    if(!empty($_POST['fixed_pricing'])){
      foreach($_POST['fixed_pricing'] AS $entry){
        if (!preg_match_all('/^20(1[8-9]|[2-9][0-9])(0[1-9]|1[0-2])([0-2][0-9]|3[0-1])$/',$entry['start'],$matches)){
          return $this->error($this->lang->phrase('FIXED_PRICE_BAD_START_DATE').' ('.$entry['start'].')');
        }
        if (!preg_match_all('/^20(1[8-9]|[2-9][0-9])(0[1-9]|1[0-2])([0-2][0-9]|3[0-1])$/',$entry['end'],$matches)){
          return $this->error($this->lang->phrase('FIXED_PRICE_BAD_END_DATE').' ('.$entry['end'].')');
        }
        $newSet[]=array(
          'start_timestamp'=>mktime(DELIVERY_HOUR,0,0,substr($entry['start'],4,2),substr($entry['start'],6,2),substr($entry['start'],0,4))
          ,'end_timestamp'=>mktime(COLLECTION_HOUR,0,0,substr($entry['end'],4,2),substr($entry['end'],6,2),substr($entry['end'],0,4))
          ,'price'=>$entry['price']
        );
      }
    }
    
    foreach($newSet AS $newEntry){
      $updateRequired=true;
      if ($oldI<$oldX){
        for($i=$oldI;$i<$oldX;$i++){
          if($oldSet[$i]['is_active']==1){
            if (
              $oldSet[$i]['start_timestamp']==$newEntry['start_timestamp'] 
              && $oldSet[$i]['end_timestamp']==$newEntry['end_timestamp'] 
              && $oldSet[$i]['price']==$newEntry['price']
            ){
              $updateRequired=false;
              $oldSet[$i]['is_active']=0;
            }
            elseif (
              $oldSet[$i]['start_timestamp']>=$newEntry['start_timestamp']
            ){
              if ($oldSet[$i]['start_timestamp']<$newEntry['end_timestamp']){
                if ($oldSet[$i]['end_timestamp']<=$newEntry['end_timestamp']){
                  $this->db->where($oldSet[$i])->update('item_price_fixed',array('is_active'=>0));
                  $oldSet[$i]['is_active']=0;
                }
                else {
                  $this->db->where($oldSet[$i])->update('item_price_fixed',array('is_active'=>0));
                  $oldSet[$i]['start_timestamp']=$newEntry['end_timestamp']+1;
                  $oldSet[$i]['request_timestamp']=MAIN_TIMESTAMP;
                  $this->db->insert('item_price_fixed',$oldSet[$i]);
                }
              }
            }
            elseif ($oldSet[$i]['end_timestamp']>$newEntry['start_timestamp']) {
              $this->db->where($oldSet[$i])->update('item_price_fixed',array('is_active'=>0));
              $oldSet[$i]['end_timestamp']=$newEntry['start_timestamp']-1;
              $oldSet[$i]['request_timestamp']=MAIN_TIMESTAMP;
              $this->db->insert('item_price_fixed',$oldSet[$i]);
              $oldSet[$i]['is_active']=0;
            }
            else {
              $oldSet[$i]['is_active']=0;
            }
          }
        }
      }
      if ($updateRequired){
        $this->db->insert('item_price_fixed',array(
          'item_id'=>$itemID
          ,'is_active'=>1
          ,'start_timestamp'=>$newEntry['start_timestamp']
          ,'end_timestamp'=>$newEntry['end_timestamp']
          ,'price'=>$newEntry['price']
          ,'request_timestamp'=>MAIN_TIMESTAMP
        ));
      }
    }
  }
  
  function loadDetails($itemID=0,$sections=array()){
    if ($itemID==0){
      return $this->error($this->lang->phrase('item_is_not_chosen'));
    }
    
    $this->reply['data']=$this->db->select('i.*,t1.content AS title,IFNULL(t2.content,\'\') AS summary,IFNULL(t3.content,\'\') AS description')
      ->join('translation AS t1','t1.target_object_id=i.item_id AND t1.target_object_type_field_id='.ITEM_OBJECT_TITLE.' AND t1.language_id=1','inner')
      ->join('translation AS t2','t2.target_object_id=i.item_id AND t2.target_object_type_field_id='.ITEM_OBJECT_SUMMARY.' AND t2.language_id=1','left')
      ->join('translation AS t3','t3.target_object_id=i.item_id AND t3.target_object_type_field_id='.ITEM_OBJECT_DESCRIPTION.' AND t3.language_id=1','left')
      ->join('file AS f','f.file_filter_id=i.file_filter_id AND f.file_filter_id>0 AND `order`=1','left')
      ->select('IFNULL(f.folder,\'\') AS folder,f.name AS filename')
      ->get_where('item AS i',array('i.item_id'=>$itemID),1)->row_array();
    if (empty($this->reply['data'])){
      return $this->error($this->lang->phrase('item_is_not_found'));
    }
    $this->load->model('File_model');
    $this->reply['data']['gallery']=$this->File_model->getList($this->reply['data']['file_filter_id']);
  }

  function edit($itemID=0){
    if ($itemID==0){
      $itemID=$this->input->post('item_id')*1;
    }
    $this->loadDetails($itemID);
    if (!$this->hasErrors()){
      $this->reply['data']['packed']=$this->db
      ->select('i.*,t1.content AS title,IFNULL(f.folder,\'\') AS folder,f.name AS filename,ip.quantity AS packed_quantity,ip.percentage')
      ->join('item AS i','i.item_id=ip.item_id','inner')
      ->join('translation AS t1','t1.target_object_id=i.item_id AND t1.target_object_type_field_id='.ITEM_OBJECT_TITLE.' AND t1.language_id=1','inner')
      ->join('file AS f','f.file_filter_id=i.file_filter_id AND f.file_filter_id>0 AND `order`=1','left')
      
      ->get_where('item_package AS ip',array('ip.item_package_id'=>$this->reply['data']['item_id']))->result_array();
      $this->reply['data']['categories']=$this->db->get_where('item_category AS ic',array('ic.item_id'=>$itemID))->result_array();
      
      $this->load->model('Tag_model');
      $this->reply['data']['tags']=$this->Tag_model->getForTarget('item',$itemID);
      
      $this->reply['data']['fixed_quantity']=$this->getFixedQuantity($itemID,true);
      $this->reply['data']['fixed_pricing']=$this->getFixedPricing($itemID,true);
    }
  }
  
  function setPublicCache(){
    $this->load->model('Item_model');
    $this->Item_model->getFiltered(array('publicOnly'=>1,'thumbnails'=>1,'nonPackage'=>1));
    $cacheData=array();
    foreach($this->reply['data']['entries'] AS $e){
      $cacheData[$e['item_id']]=array(
        'title'=>$e['title'],'description'=>$e['description']
        ,'price'=>$e['price'],'quantity'=>$e['quantity']
        ,'thumbnail'=>NS_BASE_URL.'uploads/'.$e['folder'].'/'.$e['filename']);
    }
    $fl=fopen(APPPATH.'views/item/json.js','w');
    fwrite($fl,',\'items\':'.json_encode($cacheData));
    fclose($fl);
    
    $this->setTemplate('basic');
    $this->output->enable_profiler(true);
  }
  function view($itemID){
  }
}

?>