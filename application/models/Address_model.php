<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Address_model extends CI_Model {
  protected $locationType='text';
  protected $defaultFields=array(
    'text'=>array('line_1','line_2','city','state','postcode','phone')
  );
  protected $errorContainer=false;
  public $dataToValidate=false;
  
  function __construct($config=array()) {
    parent::__construct();
  }
  
  function locationType($type){
    $this->locationType=$type;
    return $this;
  }
  
  function get($addressID,$asString=false){
    $addressID*=1;
    
    switch ($this->locationType){
      case 'text':
        $data=$this->db->get_where('address',array('address_id'=>$addressID),1)->row_array();
      break;
      default:
        $data=$this->db->select('a.*,l.location_id,s.state_id,c.country_id')
          ->join('location AS l','l.location_id=a.location_id','inner')
          ->join('state AS s','s.state_id=l.state_id','inner')
          ->join('country AS c','c.country_id=s.country_id','inner')
          ->get_where('address AS a',array('a.address_id'=>$addressID),1)->row_array();
        if ($data){
          $data['countries']=$this->db->get('country')->result_array();
          $data['states']=$this->db->get_where('state',array('country_id'=>$data['country_id']))->result_array();
          $data['locations']=$this->db->get_where('location',array('state_id'=>$data['state_id']))->result_array();
        }
      break;
    }
    if ($asString){
      return $data['city'].', '.$data['line_1'].((!empty($data['line_2']))?', '.$data['line_2']:'').', '.$data['state'].', '.$data['postcode'];
    }
    
    return $data;
  }
  
  function errorContainer($code=false){
    $this->errorContainer=$code;
    return $this;
  }
  
  function save($data,$required=false,$fields=array()){
    $CI=& get_instance();

    $addressData=$CI->input->_fetch_from_array($data,NULL,TRUE);
    $addressID=0;
    if (!empty($addressData['address_id'])){
      $addressID=$addressData['address_id']*1;
    }
    
    $dataSet=array();
    $errors=array();

    if (empty($fields)){
      switch ($this->locationType){
        case 'text':
          // $fields=array('line_1','city','state','postcode');
          $fields=array('line_1','city');
        break;
        default:
          // $fields=array('line_1','city_id','postcode');
          $fields=array('line_1','city_id');
        break;
      }
    }
    foreach($fields AS $f){
      if (empty($addressData[$f])){
        if (!$this->errorContainer){
          $errors[]=$CI->translate($f.'_should_be_set');
        }
        else {
          $errors[$f]=$CI->translate($f.'_should_be_set');
        }
      }
      else {
        $dataSet[$f]=$addressData[$f];
      }
    }
    if (!empty($addressData['line_2'])){
      $dataSet['line_2']=$addressData['line_2'];
    }
    if ($addressData['type'] == "delivery"){
      $dataSet['address_type_id']=2;
    } else {
      $dataSet['address_type_id']=1;
    }
    
    if (array_key_exists('state', $addressData)) {
      $dataSet['state']=$addressData['state'];
    }
    if (array_key_exists('postcode', $addressData)) {
      $dataSet['postcode']=$addressData['postcode'];
    }

    if ($required || $dataSet!==array()){
      if ($errors!==array()){
        foreach($errors AS $k=>$e){
          if (is_int($k)){
            $CI->error($e);
          }
          else {
            $CI->error($e,$this->errorContainer.'['.$k.']');
          }
        }
      }
    }
    // echo "Fields /";
    // print_r($dataSet);
    // echo "/";
    $this->errorContainer();
    if (!$CI->hasErrors() && $dataSet!==array()){
      if ($addressID==0){
        $this->db->insert('address',$dataSet);
        return $this->db->insert_id();
      }
      else {
        $this->db->where('address_id',$addressID)->update('address',$dataSet);
        return $addressID;
      }
    }
    /** /
    
    if ($dataSet!==array()){
    }
    elseif ($required){
      $CI->error($CI->translate($addressData['type'].'_address_should_be_set'));
    }/**/
    return false;
  }
  
  function validate($addressID,$type,$requiredFields,$newData=false,$update=false){
    if ($addressID>0){
      $oldData=$this->Address_model->get($addressID);
    }
    
    if (!empty($newData)){
      $result=$newData;
    }
    else {
      $result=$oldData;
    }
    
    if (!empty($oldData)){
      foreach($requiredFields AS $f){
        $result[$f]=trim($result[$f]);
        if (!$update && !empty($oldData[$f]) && $oldData[$f]!=$result[$f]){
          $addressID=0;
          break;
        }
      }
    }
    else {
      $addressID=0;
    }
    
    $this->dataToValidate=$result;
    
    $result['address_id']=$addressID;
    if ($addressID==0 || empty($result['type'])){
      $result['type']=$type;
    }
    
    reset($requiredFields);

    return (int)$this->save($result,true,$requiredFields);
  }

  function updateOwnership($userID,$addressIDs){
    $this->db->where_in('address_id',$addressIDs)->update('address',array('user_id'=>$userID));
  }
  
  function getAssigned($userID,$addressType){
    return $this->db->get_where('address AS a',array(
      'user_id'=>($userID*1)
      ,'address_type_id'=>$this->db->get_where('address_type',array('code'=>$addressType),1)->row_array()['address_type_id']
    ))->result_array();
  }

}

?>