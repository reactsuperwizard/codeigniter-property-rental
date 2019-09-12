<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tag extends NS_Rental_Controller {
  function __construct(){
    parent::__construct();
  }

  function autocomplete(){
    $term=trim($this->input->post('term'));
    if ($term!=''){
      $this->db->like('t.code',$term,'after',true);
    }
    $this->reply['data']=$this->db->limit(10)->get('tag AS t')->result_array();
  }
  
  function targets(){
    $dataSet=array(
      'tt.target_object_type_id'=>$this->input->post('target_object_type_id')*1
      ,'tt.target_object_id'=>$this->input->post('target_object_id')*1
    );
    $this->reply['data']=$this->db->select('t.*')
      ->join('tag AS t','t.tag_id=tt.tag_id','inner')
      ->get_where('tag_target AS tt',$dataSet)->result_array();
  }
  
  function save(){
    $code=$this->input->post('code',true);
    $dataSet=array('code'=>$code);
    $query=str_replace('INSERT INTO','INSERT IGNORE INTO',$this->db->set($dataSet)->get_compiled_insert('tag'));
    $this->db->query($query);
    
    $this->reply['data']=$this->db->like('code',strtolower($code),'none')->get('tag',1)->row_array();
  }
  
  function attach(){
    $dataSet=$this->input->post(array('tag_id','target_object_type_id','target_object_id'));
    foreach($dataSet AS $k=>$v){
      $v*=1;
      if ($v==0){
        $this->error($this->translate($k.'_should_be_set'));
      }
    }
    if (!$this->hasErrors()){
      $this->db->insert('tag_target',$dataSet);
    }
  }
  
  function remove(){
    $dataSet=$this->input->post(array('tag_id','target_object_type_id','target_object_id'));
    foreach($dataSet AS $k=>$v){
      $v*=1;
      if ($v==0){
        $this->error($this->translate($k.'_should_be_set'));
      }
    }
    if (!$this->hasErrors()){
      $this->db->where($dataSet)->delete('tag_target');
    }
  }
}
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

