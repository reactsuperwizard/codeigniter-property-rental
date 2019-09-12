<?php

class Tag_model extends CI_model{
  
  function __construct() {
    parent::__construct();
    $this->BaseModule=get_instance();
    $this->loadCodes();
  }
  
  function loadCodes(){
    if (!defined('TAG_CODES')){
      $list=$this->db->get('target_object_type')->result_array();
      foreach($list AS $e){
        define(strtoupper($e['code'].'_tag'),$e['target_object_type_id']);
      }
      define('TAG_CODES',true);
    }
  }
  
  function updateByTarget($targetObjectType,$targetID,$newTags){
    $targetObjectTypeID=$this->BaseModule->getTargetObjectTypeID($targetObjectType);
    $this->db->where(array('target_object_type_id'=>$targetObjectTypeID,'target_object_id'=>$targetID))->delete('tag_target');
    foreach($newTags AS $t){
      $t*=1;
      if ($t>0){
        $this->db->insert('tag_target',array(
          'tag_id'=>$t
          ,'target_object_type_id'=>$targetObjectTypeID
          ,'target_object_id'=>$targetID
        ));
      }
    }
  }
  
  function getForTarget($targetObjectType,$targetID){
    $targetObjectTypeID=$this->BaseModule->getTargetObjectTypeID($targetObjectType);
    //$targetObjectID=constant(strtoupper($targetObject.'_tag'));
    return $this->db->select('t.*')
      ->join('tag AS t','t.tag_id=tt.tag_id','inner')
      ->where(array('tt.target_object_type_id'=>$targetObjectTypeID,'tt.target_object_id'=>$targetID))
      ->get('tag_target AS tt')->result_array();

  }
  
  
}