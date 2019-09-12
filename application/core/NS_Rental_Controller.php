<?php

//error_reporting(E_ALL^E_NOTICE);
require_once(__DIR__.'/NS_Controller.php');

class NS_Rental_Controller extends NS_Controller{
  function __construct($config = array()) {
    parent::__construct($config);
  }
  
  function getStatusList($objectCode){
    $data=$this->db->select('s.*')
      ->join('status AS s','s.target_object_type_id=tot.target_object_type_id','inner')
      ->get_where('target_object_type AS tot',array('tot.code'=>$objectCode))->result_array();
    foreach($data AS $e){
      $labelToID=strtoupper($objectCode.'_status_'.$e['code']);
      $labelToCode=strtoupper('status_'.$e['status_id']);
      if (!defined($labelToID)){
        define($labelToID, $e['status_id']);
        define($labelToCode,$e['code']);
      }
    }
    return $data;
  }
  
  function getStatusOption($objectCode,$statusCode){
    if (is_array($statusCode)){
      $result=[];
      foreach($statusCode AS $sc){
        $result[]=$this->getStatusOption($objectCode,$sc);
      }
      return $result;
    }
    $label=strtoupper($objectCode.'_status_'.$statusCode);
    if (defined($label)){
      return constant($label);
    }
    else {
      $this->getStatusList($objectCode);
      $label=strtoupper($objectCode.'_status_'.$statusCode);
      if (defined($label)){
        return constant($label);
      }
    }
    return false;
  }
  
  function getTargetObjectTypeID($code){
    return $this->db->get_where('target_object_type',array('code'=>$code),1)->row_array()['target_object_type_id'];
  }
  
  function getObjectID($table,$lookup){
    return $this->db->get_where($table,$lookup,1)->row_array()[$table.'_id'];
  }
  
  function validateRentTimestamps(&$dataSource){
    $map=array('start'=>'delivery','end'=>'collection');
    if (empty($dataSource[$map['start'].'_date'])){
      $map=array('start'=>'start','end'=>'end');
    }
    
    $datePattern='/^20(1[8-9]|[2-9][0-9])-(0[1-9]|1[0-2])-([0-2][0-9]|3[0-1])$/';
    $timePattern='/^([0-1]{1}[0-9]|2[0-3]):([0-5][0-9])$/';

    $startDate=preg_replace('[^0-9\-]','',$dataSource[$map['start'].'_date']);
    $startDateParts=explode('-',$startDate);
    if (!empty($dataSource[$map['start'].'_time'])){
      $startTime=preg_replace('[^0-9:]','',$dataSource[$map['start'].'_time']);
    }
    else {
      $startTime='14:00';
    }

    $endDate=preg_replace('[^0-9\-]','',$dataSource[$map['end'].'_date']);
    $endDateParts=explode('-',$endDate);
    if (!empty($dataSource[$map['end'].'_time'])){
      $endTime=preg_replace('[^0-9:]','',$dataSource[$map['end'].'_time']);
    }
    else {
      $endTime='10:00';
    }

    if (!preg_match($datePattern,$startDate)){
      $this->error($this->lang->phrase('start_date_should_be_valid'));
    }
    if (!preg_match($timePattern,$startTime)){
      $this->error($this->lang->phrase('start_time_should_be_valid'));
    }
    if (!preg_match($datePattern,$endDate)){
      $this->error($this->lang->phrase('end_date_should_be_valid'));
    }
    if (!preg_match($timePattern,$endTime)){
      $this->error($this->lang->phrase('end_time_should_be_valid'));
    }

    if ($this->hasErrors()){
      $this->returnJSON();
    }

    $startTimeParts=explode(':',$startTime);
    $startTimestamp=mktime($startTimeParts[0],$startTimeParts[1],0,$startDateParts[1]*1,$startDateParts[2]*1,$startDateParts[0]*1);
    
    if (empty($dataSource['ignore_start_timestamp']) && $startTimestamp<(MAIN_TIMESTAMP+MINIMAL_RENT_TIME_PREFIX)){
      return $this->error($this->lang->phrase('start_should_be_after_24_hours'));
    }

    $endTimeParts=explode(':',$endTime);
    $endTimestamp=mktime($endTimeParts[0],$endTimeParts[1],0,$endDateParts[1],$endDateParts[2],$endDateParts[0]);

    if ($endTimestamp<$startTimestamp){
      return $this->error($this->lang->phrase('end_should_be_after_start'),false,true);
    }

    $this->reply['data']['timestamps']=array(
      'now'=>MAIN_TIMESTAMP
      ,'start'=>$startTimestamp,'start_time'=>$startTime
      ,'end'=>$endTimestamp,'end_time'=>$endTime
    );

    $this->rentTimestamps=array(
      'prefix'=>DELIVERY_TIME_PREFIX
      ,'suffix'=>COLLECTION_TIME_SUFFIX
      ,'start'=>$startTimestamp
      ,'end'=>$endTimestamp
      ,'final'=>array(
        'start'=>($startTimestamp-DELIVERY_TIME_PREFIX)
        ,'end'=>($endTimestamp+COLLECTION_TIME_SUFFIX)
      )
    );
    $this->reply['data']['rentTimestamps']=&$this->rentTimestamps;
  }
  
  function makeCode(){
    return strtoupper(uniqid(chr(rand(65, 90)) . chr(rand(65, 90))));
  }
  
  function mailReport($subject){
    $this->load->library('EmailHandler',false,'EmailHandler');
    $this->EmailHandler->basicReport($subject);
  }
  
  function sendZapierHook($hookURL,$data){
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $hookURL,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => json_encode($data),
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json"
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      //echo "cURL Error #:" . $err;
    } else {
      //echo $response;
    }
  }
}

?>