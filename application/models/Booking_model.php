<?php

class Booking_model extends CI_Model{
  function __construct() {
    parent::__construct();

    $this->load->model('Item_model');
    $this->BaseModule=get_instance();
  }

  function getAssigned($customerID){
    $result=array();
    $this->BaseModule=get_instance();
    $activeStatuses=$this->BaseModule->getStatusOption('booking',array('active','confirmed','finalized'));
    $result['total']=$this->db->select('COUNT(booking_id) AS total')
      ->get_where('booking',array('customer_id'=>$customerID))->row_array()['total'];
    $result['active']=$this->db->select('COUNT(booking_id) AS total')
      ->where('customer_id',$customerID)
      ->where_in('status_id',$activeStatuses)
      ->where('rent_period_start_timestamp >',MAIN_TIMESTAMP)
      ->get_where('booking')->row_array()['total'];
    $result['previous']=$result['total']-$result['active'];
    return $result;
  }
  
  function getFiltered($config=array()){

    
    $this->load->library('DataTable',array('count'=>'b.booking_id'));

    $this->datatable->from('booking AS b')
      ->select('b.*,s.name AS status'
        .',a.line_1 AS address_line_1,IFNULL(a.line_2,\'\') AS address_line_2'
        .',a.city AS address_city,a.state AS address_state,a.postcode AS address_postcode,a.phone AS address_phone')
      ->join('status AS s','s.status_id=b.status_id')
      ->join('address AS a','a.address_id=b.delivery_address_id','left');

    $customerID=@intval($config['customerID']);
    if ($customerID>0){
      $this->datatable->where('b.customer_id',$customerID);
    }
    if (!empty($config['mode'])){
      $activeStatuses=$this->BaseModule->getStatusOption('booking',array('confirmed','finalized'));
      switch($config['mode']){
        case 'active':
          $this->datatable
            ->where_in('b.status_id',$activeStatuses)
            ->where('b.rent_period_start_timestamp >',MAIN_TIMESTAMP);
        break;
        case 'previous':
          $this->datatable->group_start()
            ->where_not_in('b.status_id',$activeStatuses)
            ->or_where('b.rent_period_end_timestamp <',MAIN_TIMESTAMP)
          ->group_end();
        break;
      }
    }
    
    if (!empty($config['searchTerm'])){
      $this->datatable->filter()->group_start()
        ->like('b.code',$config['searchTerm'],'both')
        ->group_end();
    }
    
    $order='b.booking_id';
    
    if (!isset($config['replyData'])){
      $CI=& get_instance();
      $replyData=&$CI->reply['data'];
    }
    else {
      $replyData=&$config['replyData'];
    }
    
    $this->datatable
      ->order_by($order)
      ->run($replyData);
  }
  
  function parseDetails(& $bookingData,$code=false){
    if (empty($bookingData['booking_id'])){
      $initialData=$this->db->select('b.*,s.name AS status')
        ->join('status AS s','s.status_id=b.status_id')
        ->get_where('booking AS b',array('b.code'=>$code),1)->row_array();
      if (!$initialData){
        return false;
      }

      foreach($initialData AS $f=>$v){
        $bookingData[$f]=$v;
      }
      
      $bookingData['delivery_date']=date('Y-m-d',$bookingData['rent_period_start_timestamp']);
      $bookingData['delivery_time']=date('H:i',$bookingData['rent_period_start_timestamp']);
      $bookingData['collection_date']=date('Y-m-d',$bookingData['rent_period_end_timestamp']);
      $bookingData['collection_time']=date('H:i',$bookingData['rent_period_end_timestamp']);
      
      $bookingData['delivery_contact']=json_decode($bookingData['delivery_contact_json'],true);
      unset($initialData);
    }
 
    $bookingData['total_items']=0;
    $bookingData['total_services']=0;
    $bookingData['subtotal_discount']=0;

    $bookingData['itemQuantities']=array();

    if (!empty($bookingData['entries'])){
      $entries=$bookingData['entries'];
    }
    elseif (!empty($bookingData['entries_json'])) {
      $entries=json_decode($bookingData['entries_json'],TRUE);
    }
    else {
      $entries=array();
    }
    $bookingData['entries']=array('items'=>array(),'services'=>array());
    
    $items=$this->db->select('*,\'regularItem\' AS `type`, `unit_price` AS `price`')
      ->get_where('booking_item',array(
        'booking_id'=>$bookingData['booking_id']
      ))->result_array();
    
    $entries=array_merge($items,$entries);
    
    foreach($entries AS $e){
      unset($e['previous_discount_type']);
      $e['thumbnail']='';

      switch($e['type']){
        case 'regularItem':
          $e=array_merge($e,$this->Item_model->getOne($e['item_id']));
          $bookingData['itemQuantities'][$e['item_id']]=$e['quantity'];
        case 'additionalItem':
          $e['start_price']=$e['price']*$e['quantity'];
          $target='items';
        break;
        case 'service':
          $e['start_price']=$e['price']*$e['quantity']*$e['people'];
          $target='services';
        break;
      }
      if (!empty($e['folder'])){
        $e['thumbnail']=NS_BASE_URL.'uploads/'.$e['folder'].'/'.$e['filename'];
      }

      switch($e['discount_type']){
        case 'percentage':
          $e['discount_text']=$e['discount_value'].' %';
          $e['discount_amount']=$e['start_price']*$e['discount_value']/100;
        break;
        case 'amount':
          $e['discount_text']=$e['discount_value'];
          $e['discount_amount']=$e['discount_value'];
        break;
      }
      $e['total']=$e['start_price']-$e['discount_amount'];
      $bookingData['entries'][$target][]=$e;
      //$bookingData['total_'.$target]+=$e['total'];
      $bookingData['total_'.$target]+=$e['start_price'];
      $bookingData['subtotal_discount']+=$e['discount_amount'];
    }
    switch($bookingData['discount_type']){
      case 'percentage':
        $bookingData['discount_text']=$bookingData['discount_value'].' %';
        //$bookingData['discount_amount']=($bookingData['total_items']+$bookingData['total_services'])*$bookingData['discount_value']/100;
        $bookingData['discount_amount']=round((($bookingData['total_items']+$bookingData['total_services']-$bookingData['subtotal_discount'])*$bookingData['discount_value']/100),2);
      break;
      case 'amount':
        $bookingData['discount_text']=$bookingData['discount_value'];
        $bookingData['discount_amount']=$bookingData['discount_value'];
      break;
    }

    $bookingData['grand_total']=$bookingData['total_items']+$bookingData['total_services']-$bookingData['subtotal_discount']-$bookingData['discount_amount'];
    //$bookingData['grand_total']=$bookingData['total']-$bookingData['discount_amount'];
    $bookingData['discount_amount']=round(($bookingData['discount_amount']+$bookingData['subtotal_discount']),2);
  }
  
  function getLogistics($bookingID){
    return $this->db
        ->get_where('booking_atomic_item AS b',array('b.booking_id'=>$bookingID))
        ->result_array();
  }
}

?>