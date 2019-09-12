<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class File_model extends CI_Model {

  function __construct() {
    parent::__construct();
  }
  
  function getHash($data){
    if (is_string($data)){
      $initialLength=strlen($data);
      $parsedString=preg_replace('/[^0-9a-f_]/','',$data);
      $parsedLength=strlen($parsedString);
      if ($parsedLength==$initialLength && $parsedLength==32){
        $hash=$parsedString;
      }
      else {
        $hash=md5($data);
      }
      return $hash;
    }
    if (is_array($data) || is_object($data)){
      return $this->getHash(md5(json_encode($data)));
    }
  }
          
  function getFilterID($data,$createIfNone=true){
    $hash=$this->getHash($data);
    
    $fileFilterID=0;
    $fileFilterData=$this->db->get_where('file_filter',array('hash'=>$hash),1)->row_array();
    if (!empty($fileFilterData)){
      $fileFilterID=$fileFilterData['file_filter_id'];
    }
    elseif($createIfNone) {
      $this->db->insert('file_filter',array('hash'=>$hash));
      $fileFilterID=$this->db->insert_id();
    }
    return $fileFilterID;
  }
  
  function updateFilter($source,$target){
    $sourceFilterID=$this->getFilterID($source,false);
    if ($sourceFilterID>0){
      $file=$this->db->where('file_filter_id',$sourceFilterID)->get('file',1)->row_array();
      if ($file){
        $targetFilterID=$this->getFilterID($target,false);
        if ($targetFilterID>0){
          $this->db->where('file_filter_id',$sourceFilterID)
            ->update('file',array('file_filter_id'=>$targetFilterID));
          $this->db->where('file_filter_id',$sourceFilterID)->delete('file_filter');
          return $targetFilterID;
        }
        else {
          $this->db->where('file_filter_id',$sourceFilterID)
            ->update('file_filter',array('hash'=>$this->getHash($target)));
          return $sourceFilterID;
        }
      }
      else {
        $this->db->where('file_filter_id',$sourceFilterID)->delete('file_filter');
      }
    }
    else {
      return $this->getFilterID($target,false);
    }
    return 0;
  }
  
  function remove($fileID){
    $this->db->where('file_id',$fileID*1)->delete('file');
  }
  
  function prepareRemoval($fileID){
    $this->db->where('file_id',$fileID*1)->update('file',array('file_filter_id'=>0));
  }
  
  function getList($fileFilterID){
    $fileFilterID*=1;
    if ($fileFilterID>0){
      return $this->db->order_by('file_filter_id ASC, order ASC')->get_where('file',array('file_filter_id'=>$fileFilterID))->result_array();
    }
    return false;
  }

}

?>