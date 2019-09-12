<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class ItemPrice_model extends CI_Model {
  function __construct() {
    parent::__construct();
  }
  
  function getOne($itemID){
    $itemID*=1;
    $startData=$this->db->get_where('item',array('item_id'=>$itemID))->row_array();
    if (!$startData){
      return false;
    }
    return array('price'=>$startData['price']);
  }
  
  function getForPeriod($itemID,$startTimestamp,$endTimestamp){
    $itemID=@intval($itemID);
    $result=array();
    $default=$this->db->get_where('item',array('item_id'=>$itemID),1)->row_array();
    if ($default){
      $result[]=$default['price'];
      $special=$this->db
        ->order_by('start_timestamp ASC')
        ->get_where('item_price_fixed',array(
          'item_id'=>$itemID
          ,'is_active'=>1
          ,'start_timestamp <='=>$endTimestamp
          ,'end_timestamp >='=>$startTimestamp
        ))
        ->result_array();
      if (!empty($special)){
        $result[]=&$special;
      }
    }
    return $result;
  }

  function getDependencies(){
  }
}

?>