<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Item_model extends CI_Model {

  function __construct() {
    parent::__construct();
    
  }
  
  function addItem($data,$ignoreProblem){
    
  }
  
  function getFiltered($config=array()){
    $this->load->library('DataTable',array('count'=>'DISTINCT i.item_id'));

    $this->datatable->from('item AS i')
      ->select('DISTINCT `i`.`item_id`,`i`.`quantity`,`i`.`price`',FALSE);
    if (!empty($config['publicOnly'])){
      $this->datatable->where(array('i.is_active'=>1,'i.is_public'=>1));
    }
    
    if (isset($config['onlyChosen'])){
      $onlyChosen=array();
      $uniqueChosenItems=array();
      $ci=0;
      foreach ($config['onlyChosen'] AS $ID){
        $ID=$ID*1;
        if ($ID>0 && empty($uniqueChosenItems[$ID])){
          $onlyChosen[]=$ID;
          $ci++;
        }
      }
      if ($ci>1){
        $this->datatable->where('i.item_id IN ('.join(',',$onlyChosen).')');
      }
      elseif ($ci==1) {
        $this->datatable->where('i.item_id',$onlyChosen[0]);
      }
    }
    
    if (empty($config['skipText'])){
      $this->datatable
        ->select('item_title.content AS title,item_description.content AS description')
        ->join('translation AS item_title','item_title.target_object_id=i.item_id AND item_title.target_object_type_field_id='.ITEM_OBJECT_TITLE.' AND item_title.language_id=1'.((!empty($config['textLookup']))?' AND `item_title`.`content` LIKE \'%'.$this->db->escape_like_str($config['textLookup']).'%\' ESCAPE \'!\'':''),'inner')
        ->join('translation AS item_summary','item_summary.target_object_id=i.item_id AND item_summary.target_object_type_field_id='.ITEM_OBJECT_SUMMARY.' AND item_summary.language_id=1','left')
        ->join('translation AS item_description','item_description.target_object_id=i.item_id AND item_description.target_object_type_field_id='.ITEM_OBJECT_DESCRIPTION.' AND item_description.language_id=1','left');
    }
    if (!empty($config['thumbnails'])){
      $this->datatable->select('IFNULL(f.folder,\'\') AS folder,f.name AS filename')
        ->join('file AS f','f.file_filter_id=i.file_filter_id AND f.file_filter_id>0 AND `order`=1','left');
    }
    
    if (!empty($config['nonPackage'])){
      $this->datatable
        ->join('item_package AS ip','ip.item_package_id=i.item_id','left outer')
        ->where('ip.item_package_id IS NULL');
    }


    if (!empty($config['packageID'])){
      $this->datatable
        ->select('ip.quantity AS packed_quantity, ip.percentage AS packed_percentage')
        ->join('item_package AS ip','ip.item_package_id='.($config['packageID']*1).' AND ip.item_id=i.item_id');
    }
    else {
      if (!empty($config['getPackages']) || !empty($config['checkPackages'])){
        $this->datatable
          ->select('IFNULL(ip.item_package_id,0) AS item_package_id')
          ->join('item_package AS ip','ip.item_package_id=i.item_id','left');
      }  
    }
    
    if (!empty($config['rentTimestamps'])){
      $this->datatable->skipCounter('any');
      
      if (empty($config['strictRentTimestamps'])){
        $this->datatable->select('lg1.gap AS lg1gap,lg2.gap AS lg2gap')
          ->join('schedule_logistics_gap AS lg1','lg1.schedule_id=i.schedule_id AND lg1.weekday='.date('N',$config['rentTimestamps']['start']).' AND lg1.direction=\'backwards\'')
          ->join('schedule_logistics_gap AS lg2','lg2.schedule_id=i.schedule_id AND lg2.weekday='.date('N',$config['rentTimestamps']['end']).' AND lg2.direction=\'forward\'');
        $startTimestampGap='(\''.$config['rentTimestamps']['start'].'\'-lg1.gap)';
        $endTimestampGap='(\''.$config['rentTimestamps']['end'].'\'+lg2.gap)';
      }
      else {
        $startTimestampGap='\''.$config['rentTimestamps']['start'].'\'';
        $endTimestampGap='\''.$config['rentTimestamps']['end'].'\'';
      }
      $this->datatable->select($startTimestampGap.' AS `final_timestamp_start`, '.$endTimestampGap.' AS `final_timestamp_end`',FALSE);
      
      $this->datatable
        ->join('item_quantity_fixed AS iqf1','iqf1.item_id=i.item_id AND iqf1.is_active=\'1\''
          .' AND iqf1.start_timestamp<'.$endTimestampGap
          .' AND iqf1.end_timestamp>'.$startTimestampGap,'left')
        ->join('item_quantity_fixed AS iqf2','iqf2.item_id=i.item_id AND iqf2.is_active=\'1\''
          .' AND iqf2.start_timestamp<'.$endTimestampGap
          .' AND iqf2.end_timestamp>'.$startTimestampGap
          .' AND iqf2.quantity<iqf1.quantity','left')
        ->select('IFNULL(iqf1.quantity,NULL) AS fixed_quantity')
        ->where('iqf2.quantity IS NULL');

      

      if (!empty($config['lockHash'])){
        
        $this->datatable
          ->join('item_lock_skipper AS ls1','ls1.item_lock_hash=\''.$config['lockHash'].'\' AND ls1.item_id=i.item_id','left');
        $this->datatable
          ->join('item_lock_skipper AS ls2','ls2.item_lock_hash=\''.$config['lockHash'].'\' AND ls2.item_id=i.item_id'
            .' AND (ls2.quantity>ls1.quantity)','left')
          ->select('IFNULL(ls1.quantity,0) AS booked')
          ->where('ls2.quantity IS NULL');
      }
      else {
        $this->datatable
          ->join('item_lock AS l1','l1.item_id=i.item_id'
            .' AND l1.start_timestamp<'.$endTimestampGap
            .' AND l1.end_timestamp>'.$startTimestampGap,'left');
        $this->datatable
          ->join('item_lock AS l2','l2.item_id=i.item_id'
            .' AND l2.start_timestamp<'.$endTimestampGap
            .' AND l2.end_timestamp>'.$startTimestampGap
            .' AND (l2.quantity>l1.quantity)','left')
          ->select('IFNULL(l1.quantity,0) AS booked')
          ->where('l2.quantity IS NULL');
      }
    }
    else {
      $this->datatable->select('\'\' AS fixed_quantity, 0 AS booked');
    }
    /** /
    if ($this->userInfo['role']=='admin'){
      $nonPackage=$this->input->post('non_package');
      if (!empty($nonPackage)){
        
      }
      if (isset($config['public_only'])){
        
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
    }/**/
    
    /** /
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
        ->like('item_title.content',$searchTerm,'both')
        ->group_end();
    }
    /**/
    
    $order='i.item_id';
    if (!empty($config['order'])){
      switch($config['order']){
        case 'alpha_asc':
        break;
        default:
          $order='i.item_id ASC';
        break;
      }
    }
    
    if (!isset($config['replyData'])){
      $CI=& get_instance();
      $replyData=&$CI->reply['data'];
    }
    else {
      $replyData=&$config['replyData'];
    }
    
    //$_POST['order'][0]['dir']
    $this->datatable
      ->order_by($order)
      //->order_by('item_title.target_object_type_field_id ASC, item_title.content ASC')
      ->run($replyData);
    
    if (!empty($config['lockHash'])){
      if (empty($config['stickLockHash'])){
        $this->ItemLock_model->releaseHash($config['lockHash']);
      }
    }
    //echo '<pre>'; print_r($CI->reply); echo '</pre>';
    
  }
  
  function getPackage($itemPackageID){
    $this->db
      ->select('item_title.content AS title,i.item_id,ip.quantity,i.quantity AS max_quantity')
      ->join('item AS i','i.item_id=ip.item_id','inner')
      ->join('translation AS item_title','item_title.target_object_id=i.item_id AND item_title.target_object_type_field_id='.ITEM_OBJECT_TITLE.' AND item_title.language_id=1','inner');
    return $this->db->get_where('item_package AS ip',array('item_package_id'=>$itemPackageID))->result_array();
  }
  
  function getOne($itemID){
    return $this->db
      ->select('DISTINCT `i`.`item_id`, IFNULL(`ip`.`item_package_id`,0) AS `item_package_id`',FALSE)
      ->join('item_package AS ip','ip.item_package_id=i.item_id','left')
      ->select('item_title.content AS title,item_description.content AS description')
      ->join('translation AS item_title','item_title.target_object_id=i.item_id AND item_title.target_object_type_field_id='.ITEM_OBJECT_TITLE.' AND item_title.language_id=1','inner')
      ->join('translation AS item_description','item_description.target_object_id=i.item_id AND item_description.target_object_type_field_id='.ITEM_OBJECT_DESCRIPTION.' AND item_description.language_id=1','left')
      ->select('IFNULL(f.folder,\'\') AS folder,f.name AS filename')
      ->join('file AS f','f.file_filter_id=i.file_filter_id AND f.file_filter_id>0 AND `order`=1','left')
      ->get_where('item AS i',array('i.item_id'=>$itemID),1)->row_array();
  }
}

?>