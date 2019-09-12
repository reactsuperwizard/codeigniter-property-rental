<?php

class Venue_model extends CI_Model {
  function __construct() {
    parent::__construct();
    
    $this->CI=get_instance();
  }

  function getAll(){
    return $this->db
      ->select('a.*,v.*,v.name AS venue')
      ->join('address AS a','a.address_id=v.address_id','inner')
      ->order_by('v.name','ASC')
      ->get('venue AS v')->result_array();
  }
}
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

