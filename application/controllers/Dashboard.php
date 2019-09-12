<?php

class Dashboard extends NS_Rental_Controller {
  function __construct($config = array()) {
    parent::__construct($config);
    
    $this->loadTranslationCodes();
  }
  
  function index(){
    if ($this->userInfo['role']=='admin'){
      $this->setTemplate('backend','Dashboard');
    }
  }
  
  function filtered($config=array()){
    $this->load->library('DataTable',array('count'=>'l.item_lock_id'));

    $this->datatable->from('item_lock AS l');
    
    $searchTerm='';
    if (!empty($_POST['search'])){
      if (is_array($_POST['search'])){
        if (!empty($_POST['search']['value'])){
          $searchTerm=$this->security->xss_clean($_POST['search']['value']);
        }
      }
      else {
        $searchTerm=$this->security->xss_clean($_POST['search']);
      }
    }
    
    $this->datatable
      ->select('l.*,item_title.content AS title,item_description.content AS description')
      ->join('translation AS item_title','item_title.target_object_id=l.item_id AND item_title.target_object_type_field_id='.ITEM_OBJECT_TITLE.' AND item_title.language_id=1'.((!empty($searchTerm))?' AND `item_title`.`content` LIKE \'%'.$this->db->escape_like_str($searchTerm).'%\' ESCAPE \'!\'':''),'inner')
      ->join('translation AS item_description','item_description.target_object_id=l.item_id AND item_description.target_object_type_field_id='.ITEM_OBJECT_DESCRIPTION.' AND item_description.language_id=1','left');
    
    
    
    $order='';
    $this->datatable
      ->order_by('item_title.content ASC,l.item_id ASC, l.start_timestamp ASC')
      //->order_by($order,'asc',TRUE)
      ->run($this->reply['data']);
  }
  
  
  

}

?>