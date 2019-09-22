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
  function get_distance($dest) {

// Google Map API which returns the distance between 2 postcodes
    $result    = array();
    // ( [destination_addresses] => Array ( [0] => Coningsby Rd, Scunthorpe DN17 2HJ, UK ) [origin_addresses] => Array ( [0] => Wragby Rd, Scunthorpe DN17 2HG, UK ) [rows] => Array ( [0] => Array ( [elements] => Array ( [0] => Array ( [distance] => Array ( [text] => 0.4 km [value] => 411 ) [duration] => Array ( [text] => 2 mins [value] => 102 ) [status] => OK ) ) ) ) [status] => OK ) {"status":"success","errors":[],"errorMap":[["global",[]]],"error_fields":[],"logs":["user update: just basic report, no real notification yet"],"data":{"customer_phone":"0154236523"},"config":[],"request":{"get":[],"post":{"user_id":"360","email":"user@adding.com","password":"user","role_id":"2","status_id":"1","first_name":"userFirst","last_name":"userLast","company_name":"company","phone":"0154236523","line_1":"Line1","line_2":"Line2","city":"city","state":"1","postcode":"1"}},"queries":["SELECT u.user_id, u.email, CONCAT(u.first_name, ' ', u.last_name) AS name, r.code AS role\nFROM `user` AS `u`\nLEFT JOIN `role` AS `r` ON `r`.`role_id`=`u`.`role_id`\nWHERE `u`.`user_id` = 1\n LIMIT 1","SELECT *\nFROM `user`\nWHERE `user_id` = 360\n LIMIT 1","UPDATE `user` SET `first_name` = 'userFirst', `last_name` = 'userLast', `email` = 'user@adding.com', `phone` = '0154236523', `password` = '$2y$10$DYep2VTrP.bgbfLd\/8UytuCdLJc3Y54peRL8t4RBzm0RxcKkGPyE2', `company_name` = 'company', `status_id` = 1, `role_id` = 2\nWHERE `user_id` = 360","SELECT *\nFROM `address`\nWHERE `user_id` = 360\nAND `address_type_id` = '1'","UPDATE `address` SET `user_id` = '360', `line_1` = 'Line1', `city` = 'city', `line_2` = 'Line2', `address_type_id` = 1, `state` = '1', `postcode` = '1', `distance` = 0.411\nWHERE `address_id` = 163"],"session":{"__ci_last_regenerate":1568345917,"NS_Rental":{"tags":[]},"userID":"1"},"userInfo":{"user_id":"1","email":"admin@freelance.nws","name":"Test Admin","role":"
    $address1 = urlencode('152 Winnellie Road, Darwin');
    $address2 = urlencode('Crafty Divas, Darwin');
    // $address2 = urlencode($dest);
    // $address1 = 'DN17%202HG';
    // $address2 = 'DN17%202HJ';
    $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$address1."&destinations=".$address2."&mode=driving&language=en-EN&sensor=false&key=AIzaSyD--bS6u1ee4mHY_yjXs5ZmOY2B_EeTdGQ";
    // print_r($url);
    // $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=Darwin&destinations=jilin&mode=driving&language=en-EN&sensor=false&key=AIzaSyD--bS6u1ee4mHY_yjXs5ZmOY2B_EeTdGQ";
    // $url = "https://www.mapdevelopers.com/distance_from_to.php?&from=MCLACHLAN%2C%20Darwin&to=jilin";
    $data   = @file_get_contents($url);
    $result = json_decode($data, true);
    // print_r($result);

    $duration = $result["rows"][0]["elements"][0]["duration"]["value"];

    if ($result["rows"][0]["elements"][0]["status"] == "OK") {
      $distance = array( // converts the units
          "meters" => $result["rows"][0]["elements"][0]["distance"]["value"],
          "kilometers" => $result["rows"][0]["elements"][0]["distance"]["value"] / 1000,
          "yards" => $result["rows"][0]["elements"][0]["distance"]["value"] * 1.0936133,
          "miles" => $result["rows"][0]["elements"][0]["distance"]["value"] * 0.000621371
      );
      return $distance['kilometers'];
    }
    return 15;
      // return -1;
    // exit(1);
  }
  
// https://maps.googleapis.com/maps/api/distancematrix/json?origins=152+Winnellie+Road%2C+Darwin&destinations=Crafty+Divas%2C+Darwin&mode=driving&language=en-EN&sensor=false&key=AIzaSyD--bS6u1ee4mHY_yjXs5ZmOY2B_EeTdGQArray ( [destination_addresses] => Array ( ) [error_message] => This IP, site or mobile application is not authorized to use this API key. Request received from IP address 188.138.1.126, with empty referer [origin_addresses] => Array ( ) [rows] => Array ( ) [status] => REQUEST_DENIED ) {"status":"fail","errors":["distance_should_be_valid"],"errorMap":[["global",[0]]],"error_fields":[],"logs":[],"data":{"customer_phone":"0123232323","customer_id":419},"config":[],"request":{"get":[],"post":{"quote_id":"0","name":"quoteabc","status_id":"4","expiration_date":"2019-09-30","expiration_time":"10:00","customer_id":"419","customer_search":"","customer":{"first_name":"quoteabcFirst","email":"quoteabc@Email.com","last_name":"quoteabcLast","phone":"0123232323","company":"quoteabc"},"residential_address_id":"0","residential_address":{"line_1":"Crafty Divas","line_2":"","phone":"","city":"Darwin","state":"1","postcode":"1"},"delivery_address_id":"residential","delivery_address":{"line_1":"","line_2":"","phone":"","city":"","state":"","postcode":""},"delivery_contact":{"venue":"quoteabcVe","name":"","email":"","phone":""},"delivery_date":"2019-09-19","delivery_time":"14:00","collection_date":"2019-09-21","collection_time":"10:00","chargeable_days":"1","deposit_type":"percentage","deposit_value":"20","due_direction":"-","due_days":"7","variation":[{"0":{"quantity":"1","price":"110.00","previous_discount_type":"percentage","discount_type":"percentage","discount_value":"0","type":"regularItem","item_id":"75"},"name":"","quote_variation_id":"0","remove":"0","entries":["0"],"previous_discount_type":"percentage","discount_type":"percentage","discount_value":"0","deposit_type":"percentage","deposit_value":"20","due_direction":"-","due_days":"7","notes":""}],"variations":["0"]}},"queries":["SELECT u.user_id, u.email, CONCAT(u.first_name, ' ', u.last_name) AS name, r.code AS role\nFROM `user` AS `u`\nLEFT JOIN `role` AS `r` ON `r`.`role_id`=`u`.`role_id`\nWHERE `u`.`user_id` = 1\n LIMIT 1","SELECT `tof`.`target_object_type_id` AS `oi`, `to`.`code` AS `o`, `tof`.`code` AS `f`, `tof`.`target_object_type_field_id` AS `i`\nFROM `target_object_type_field` AS `tof`\nINNER JOIN `target_object_type` AS `to` ON `to`.`target_object_type_id`=`tof`.`target_object_type_id`","SELECT `s`.*\nFROM `target_object_type` AS `tot`\nINNER JOIN `status` AS `s` ON `s`.`target_object_type_id`=`tot`.`target_object_type_id`\nWHERE `tot`.`code` = 'quote'","SELECT *\nFROM `user`\nWHERE `user_id` = 419\n LIMIT 1","SELECT *\nFROM `user`\nWHERE `user_id` = 419\n LIMIT 1"],"session":{"__ci_last_regenerate":1568604346,"NS_Rental":{"tags":[]},"userID":"1"},"userInfo":{"user_id":"1","ema
// https://maps.googleapis.com/maps/api/distancematrix/json?origins=152+Winnellie+Road%2C+Darwin&destinations=Crafty+Divas%2C+Darwin&mode=driving&language=en-EN&sensor=false&key=AIzaSyD--bS6u1ee4mHY_yjXs5ZmOY2B_EeTdGQArray ( [destination_addresses] => Array ( ) [error_message] => This IP, site or mobile application is not authorized to use this API key. Request received from IP address 188.138.1.126, with empty referer [origin_addresses] => Array ( ) [rows] => Array ( ) [status] => REQUEST_DENIED ) {"status":"fail","errors":["address should be valid_should_be_set"],"errorMap":[["global",[0]]],"error_fields":[],"logs":[],"data":[],"config":[],"request":{"get":[],"post":{"user_id":"","email":"abc@abc.com","password":"abc","role_id":"2","status_id":"1","first_name":"abcabc","last_name":"abcabc","company_name":"acacb","phone":"0123456789","line_1":"Crafty Divas","line_2":"","city":"Darwin","state":"1","postcode":"1"}},"queries":["SELECT u.user_id, u.email, CONCAT(u.first_name, ' ', u.last_name) AS name, r.code AS role\nFROM `user` AS `u`\nLEFT JOIN `role` AS `r` ON `r`.`role_id`=`u`.`role_id`\nWHERE `u`.`user_id` = 1\n LIMIT 1"],"session":{"__ci_last_regenerate":1568604891,"NS_Rental":{"tags":[]},"userID":"1"},"userInfo":{"user_id":"1","email":"admin@freelance.nws","name":"Test Admin","role":"adm
// https://maps.googleapis.com/maps/api/distancematrix/json?origins=152+Winnellie+Road%2C+Darwin&destinations=Crafty+Divas%2C+Darwin&mode=driving&language=en-EN&sensor=false&key=AIzaSyD--bS6u1ee4mHY_yjXs5ZmOY2B_EeTdGQArray ( [destination_addresses] => Array ( [0] => 305 Lowther Rd, Virginia NT 0835, Australia ) [origin_addresses] => Array ( [0] => 152 Winnellie Rd, Winnellie NT 0820, Australia ) [rows] => Array ( [0] => Array ( [elements] => Array ( [0] => Array ( [distance] => Array ( [text] => 24.5 km [value] => 24533 ) [duration] => Array ( [text] => 22 mins [value] => 1346 ) [status] => OK ) ) ) ) [status] => OK ) {"status":"success","errors":[],"errorMap":[["global",[]]],"error_fields":[],"logs":[],"data":[],"config":[],"request":{"get":[],"post":{"user_id":"","email":"user11@email.com","password":"user","role_id":"2","status_id":"1","first_name":"useroo","last_name":"useroo","company_name":"","phone":"0123456789","line_1":"Crafty Divas","line_2":"","city":"Darwin","state":"1","postcode":"1"}},"queries":["SELECT u.user_id, u.email, CONCAT(u.first_name, ' ', u.last_name) AS name, r.code AS role\nFROM `user` AS `u`\nLEFT JOIN `role` AS `r` ON `r`.`role_id`=`u`.`role_id`\nWHERE `u`.`user_id` = 1\n LIMIT 1"],"session":{"__ci_last_regenerate":1568605629,"NS_Rental":{"tags":[]},"userID":"1"},"userInfo":{"user_id":"1","email":"admin@freelance.nws","name":"Test Admin","role":"admin"}}
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
    $dataSet['distance'] = $this->get_distance($dataSet['line_1'].', '.$dataSet['city']);
      if ( $dataSet['distance'] == -1){
        if (!$this->errorContainer){
          $errors[]=$CI->translate('distance'.'_should_be_valid');
        }
        else {
          $errors['distance']=$CI->translate('distance'.'_should_be_valid');
        }
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
                        //data, true, requiredfields
  function isValidateData($data,$required=false,$fields=array()){
    $CI=& get_instance();

    $addressData=$CI->input->_fetch_from_array($data,NULL,TRUE);

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
    $dataSet['distance'] = $this->get_distance($dataSet['line_1'].', '.$dataSet['city']);
    if ($dataSet['distance'] == -1) {
      if (!$this->errorContainer){
        $errors[]=$CI->translate('address should be valid'.'_should_be_set');
      }
      else {
        $errors['address should be valid']=$CI->translate('address should be valid'.'_should_be_set');
      }
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
        return false;
      }
    }
    if (!$CI->hasErrors() && $dataSet!==array()){
      return true;
    }
    return false;
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