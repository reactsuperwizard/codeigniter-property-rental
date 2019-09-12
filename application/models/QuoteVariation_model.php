<?php

class QuoteVariation_model extends CI_Model{
  function __construct() {
    parent::__construct();

    $this->load->model('Item_model');
  }
  
  function parseDetails(& $variationData,$code=false){
    if (empty($variationData['quote_variation_id'])){
      $initialData=$this->db->select('q.name AS quote,q.*,qv.*,IFNULL(qv.notes,\'\') AS notes')
      ->join('quote AS q','q.quote_id=qv.quote_id')
      ->get_where('quote_variation AS qv',array('qv.code'=>$code),1)->row_array();
      foreach($initialData AS $f=>$v){
        $variationData[$f]=$v;
      }
      $variationData['expiration_date']=date('Y-m-d',$variationData['expiration_timestamp']);
      $variationData['expiration_time']=date('H:i',$variationData['expiration_timestamp']);
      
      $variationData['delivery_date']=date('Y-m-d',$variationData['delivery_timestamp']);
      $variationData['delivery_time']=date('H:i',$variationData['delivery_timestamp']);
      $variationData['collection_date']=date('Y-m-d',$variationData['collection_timestamp']);
      $variationData['collection_time']=date('H:i',$variationData['collection_timestamp']);
      
      $variationData['delivery_contact']=json_decode($variationData['delivery_contact_json'],true);
      
      
      unset($initialData);
    }
    
    $variationData['due_date']=date('Y-m-d',($variationData['collection_timestamp']+$variationData['due_days']*86400));
    $variationData['booking_flag']=($variationData['booking_id']>0)?1:0;

    $variationData['deposit_mode']='';
    
    if ($variationData['deposit_value']>0){
      if ($variationData['deposit_type']=='percentage' && $variationData['deposit_value']=='100'){
        $variationData['deposit_mode']='full';
      }
      else {
        $variationData['deposit_mode']='partial';
        switch($variationData['deposit_type']){
          case 'percentage':
            $variationData['deposit_text']=$variationData['deposit_value'].' %';
          break;
          case 'amount':
            $variationData['deposit_text']='$ '.$variationData['deposit_value'];
          break;
        }
      }
      if ($variationData['purchase_order']==0){
        $variationData['purchase_order']='';
      }
    }
    else {
      if ($variationData['purchase_order']==''){
        $variationData['purchase_order']=0;
      }
    }
 
    $variationData['total_items']=0;
    $variationData['total_services']=0;
    $variationData['subtotal_discount']=0;

    $variationData['itemQuantities']=array();

    if (!empty($variationData['entries'])){
      $entries=$variationData['entries'];
    }
    else {
      $entries=json_decode($variationData['entries_json'],TRUE);
    }
    $variationData['entries']=array('items'=>array(),'services'=>array());
    
    $items=$this->db->select('*,\'regularItem\' AS `type`, `unit_price` AS `price`')
      ->get_where('quote_variation_item',array(
        'quote_variation_id'=>$variationData['quote_variation_id']
      ))->result_array();
    
    $entries=array_merge($items,$entries);
    
    foreach($entries AS $e){
      unset($e['previous_discount_type']);
      $e['thumbnail']='';

      switch($e['type']){
        case 'regularItem':
          $e=array_merge($e,$this->Item_model->getOne($e['item_id']));
          $variationData['itemQuantities'][$e['item_id']]=$e['quantity'];
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
      $e['discount_value']=floatval($e['discount_value']);

      switch($e['discount_type']){
        case 'percentage':
          $e['discount_text']=$e['discount_value'].' %';
          $e['discount_amount']=round($e['start_price']*$e['discount_value']/100,2);
        break;
        case 'amount':
          $e['discount_text']=$e['discount_value'];
          $e['discount_amount']=$e['discount_value'];
        break;
      }
      $e['total']=round(($e['start_price']-$e['discount_amount']),2);
      $variationData['entries'][$target][]=$e;
      //$variationData['total_'.$target]+=$e['total'];
      $variationData['total_'.$target]+=$e['start_price'];
      $variationData['subtotal_discount']+=$e['discount_amount'];
    }
    switch($variationData['discount_type']){
      case 'percentage':
        $variationData['discount_text']=$variationData['discount_value'].' %';
        $variationData['discount_amount']=round(($variationData['total_items']+$variationData['total_services'])*$variationData['discount_value']/100,2);
      break;
      case 'amount':
        $variationData['discount_text']=$variationData['discount_value'];
        $variationData['discount_amount']=$variationData['discount_value'];
      break;
    }
    
    $variationData['discount_amount']+=round($variationData['subtotal_discount'],2);
    $variationData['grand_total']=round(($variationData['total_items']+$variationData['total_services']-$variationData['discount_amount']),2);
    $taxPercentage=10;
    $variationData['tax']=round($variationData['grand_total']/(100+$taxPercentage)*$taxPercentage,2);
  }
  
  function getLogistics($quoteVariationID,$deliveryTimestamp,$collectionTimestamp){
    $startData=$this->db
      ->select('IF((ip.item_package_id>0),ip.item_id,qvi.item_id) AS atomic_item_id,IF((ip.item_package_id>0),ip.quantity,1)*qvi.quantity AS quantity,'.($deliveryTimestamp*1).' AS delivery_timestamp,'.($collectionTimestamp*1).' AS collection_timestamp')
      ->join('item_package AS ip','ip.item_package_id=qvi.item_id','left')
      ->get_where('quote_variation_item AS qvi',array('qvi.quote_variation_id'=>$quoteVariationID))
      ->result_array();

    $result=[];
    $map=[];
    $e=0;
    foreach($startData AS $entry){
      $itemID=$entry['atomic_item_id'];
      if (!isset($map[$itemID])){
        $map[$itemID]=$e;
        $e++;
      }
      if (empty($result[$map[$itemID]])){
        $result[$map[$itemID]]=$entry;
      }
      else {
        $result[$map[$itemID]]['quantity']+=$entry['quantity'];
      }
    }
    return $result;
  }
  
  function getLockerID($quoteVariationID){
    $data=$this->db
      ->select('IF((pq.quote_id>0),\'parent\',\'usual\') AS quote_mode'
        .',q.status_id AS usual_status_id,q.quote_id AS usual_quote_id'
        .',pq.status_id AS parent_status_id,pq.quote_id AS parent_quote_id')
      ->join('quote AS q','q.quote_id=qv.quote_id')
      ->join('quote AS pq','pq.quote_id=q.parent_id','left')
      ->get_where('quote_variation AS qv',array('quote_variation_id'=>$quoteVariationID),1)
      ->row_array();
    return array('quote_id'=>$data[$data['quote_mode'].'_quote_id'],'status_id'=>$data[$data['quote_mode'].'_status_id']);
  }
}

?>