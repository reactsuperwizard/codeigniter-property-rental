<?php

class Quote_model extends CI_Model{
  function __construct(){
    parent::__construct();
    $this->BaseModule=get_instance();
  }  

  function getAssigned($customerID){
    $result=array();
    $this->BaseModule=get_instance();
    $activeStatus=$this->BaseModule->getStatusOption('quote','active');
    $result['total']=$this->db->select('COUNT(quote_id) AS total')->get_where('quote',array('customer_id'=>$customerID))->row_array()['total'];
    $result['active']=$this->db->select('COUNT(quote_id) AS total')->get_where('quote',array('customer_id'=>$customerID,'status_id'=>$activeStatus))->row_array()['total'];
    $result['previous']=$result['total']-$result['active'];
    return $result;
  }

  function getFiltered($config=array()){
    $this->load->library('DataTable',array('count'=>'q.quote_id'));

    $this->datatable->from('quote AS q')
      ->select('q.*,s.name AS status'
        .',a.line_1 AS address_line_1,IFNULL(a.line_2,\'\') AS address_line_2'
        .',a.city AS address_city,a.state AS address_state,a.postcode AS address_postcode,a.phone AS address_phone')
      ->join('status AS s','s.status_id=q.status_id')
      ->join('address AS a','a.address_id=q.delivery_address_id','left');

    $customerID=@intval($config['customerID']);
    if ($customerID>0){
      $this->datatable->where('q.customer_id',$customerID);
    }

    if (!empty($config['mode'])){
      $activeStatuses=$this->BaseModule->getStatusOption('quote',array('active'));
      switch($config['mode']){
        case 'active':
          $this->datatable
            ->where_in('q.status_id',$activeStatuses)
            ->where('q.delivery_timestamp >',MAIN_TIMESTAMP);
        break;
        case 'previous':
          $this->datatable->group_start()
            ->where_not_in('q.status_id',$activeStatuses)
            ->or_where('q.collection_timestamp <',MAIN_TIMESTAMP)
          ->group_end();
        break;
      }
    }

    if (!empty($config['searchTerm'])){
      $this->datatable->filter()->group_start()
        ->like('q.name',$config['searchTerm'],'after')
        ->group_end();
    }

    $order='q.quote_id';
    if (!empty($config['order'])){
      switch($config['order']){
        case 'alpha_asc':
        break;
        default:
          $order='q.quote_id ASC';
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
    
    $this->datatable
      ->order_by($order)
      ->run($replyData);
  }
}

?>