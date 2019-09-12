<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Category_model extends CI_Model {

  function __construct() {
    parent::__construct();
  }
  
  function getAll(){
    $this->db
      ->from('category AS c')
      ->select('c.category_id,t1.content AS title')
      ->join('translation AS t1','t1.target_object_id=c.category_id AND t1.target_object_type_field_id='.CATEGORY_OBJECT_TITLE.' AND t1.language_id=1','inner')
      ->order_by('t1.target_object_type_field_id ASC, t1.content ASC');
    
    if ($this->userInfo['role']!='admin'){
      $this->db->where(array('c.is_active'=>1,'c.is_public'=>1));
    }
            
    return $this->db->get()->result_array();
  }
  
  function getFiltered($config=array()){
    $this->db
      ->from('category AS c')
      ->select('c.category_id,t1.content AS title')
      ->join('translation AS t1','t1.target_object_id=c.category_id AND t1.target_object_type_field_id='.CATEGORY_OBJECT_TITLE.' AND t1.language_id=1','inner')
      ->order_by('t1.target_object_type_field_id ASC, t1.content ASC');
    
    if (!empty($config['publicOnly'])){
      $this->db->where(array('c.is_active'=>1,'c.is_public'=>1));
    }
    if (!empty($config['items'])){
      $this->db->join('item_category AS ic','ic.category_id=c.category_id','inner')
        ->join('item AS i','i.item_id=ic.item_id'.((!empty($config['publicOnly']))?' AND i.is_active=1 AND i.is_public=1':''),'inner')
        ->select('ic.item_id');
    }
            
    return $this->db->get()->result_array();
  }
}

?>