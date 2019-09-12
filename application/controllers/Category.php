<?php

class Category extends NS_Rental_Controller {
  function __construct($config = array()) {
    parent::__construct($config);
    
    
    $IP=$_SERVER['REMOTE_ADDR'];
    if (!empty($_GET['debug']) && $_GET['debug']=='profiler'){
      $this->output->enable_profiler(TRUE);
    }
    //echo $IP;
    switch ($IP){
      case '89.28.40.213':
      case '192.168.0.7':
      case '127.0.0.1':
      case '220.235.231.200':
      
        //
      break;
      default:
        //die();
      break;
    }
    $this->loadTranslationCodes();
    //
  }
  
  function index(){
    if ($this->userInfo['role']=='admin'){
      $this->setTemplate('backend','category/management');
      $this->output->enable_profiler(TRUE);
    }
  }
  
  function filtered($config=array()){
    $this->load->library('DataTable',array('count'=>'c.category_id'));

    $this->datatable->from('category AS c')
      ->select('c.category_id,t1.content AS title,t2.content AS description')
      ->join('translation AS t1','t1.target_object_id=c.category_id AND t1.target_object_type_field_id='.CATEGORY_OBJECT_TITLE.' AND t1.language_id=1','inner')
      ->join('translation AS t2','t2.target_object_id=c.category_id AND t2.target_object_type_field_id='.CATEGORY_OBJECT_DESCRIPTION.' AND t2.language_id=1','left');
    
    if ($this->userInfo['role']=='admin'){
    }
    else {
      $this->datatable->where(array('i.is_active'=>1,'i.is_public'=>1));
    }
    
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
    if (!empty($searchTerm)){
      $this->datatable->filter()->group_start()
        ->like('t1.content',$searchTerm,'after')
        ->group_end();
    }
    
    $order='t1.category_id';
    //$_POST['order'][0]['dir']
    $this->datatable
      ->order_by('t1.target_object_type_field_id ASC,t1.content ASC')
      //->order_by($order,'asc',TRUE)
      ->run($this->reply['data']);
  }
  
  
  function save(){
    $dataSet=$this->input->post(array('is_active','is_public'));
    foreach($dataSet AS $k=>$v){
      $dataSet[$k]=$v*1;
    }
    
    if (!$this->hasErrors()){
      $translationVersions=array(1);
      $translations=array();
      foreach($translationVersions AS $v){
        $title=trim($this->security->xss_clean($_POST['title'][$v]));
        if (!empty($title)){
          $translations[$v]=array(
            'title'=>$title
            ,'description'=>trim($this->security->xss_clean($_POST['description'][$v]))
          );
        }
      }
    
      if (empty($translations[1])){
        return $this->error($this->lang->phrase('title_should_be_set'));
      }
    }
    
    if (!$this->hasErrors()){
      $categoryID=$this->input->post('category_id')*1;
      if ($categoryID==0){
        $this->db->insert('category',$dataSet);
        $categoryID=$this->db->insert_id();
      }
      else {
        $this->db->where('category_id',$categoryID)->update('category',$dataSet);
      }
      $this->reply['data']['category_id']=$categoryID;
      
      $existingTranslations=$this->db->select('t.language_id AS l, tof.code AS f')
        ->join('target_object_type_field AS tof','tof.target_object_type_field_id=t.target_object_type_field_id AND tof.target_object_type_id='.CATEGORY_OBJECT,'inner')
        ->get_where('translation AS t',array('t.target_object_id'=>$categoryID))->result_array();
      $toUpdate=array();
      foreach($existingTranslations AS $et){
        $toUpdate[$et['l'].'_'.$et['f']]=1;
      }
      
      foreach($translations AS $v=>$data){
        foreach($data AS $f=>$c){
          
          $dataSet=array(
            'target_object_id'=>$categoryID
            ,'target_object_type_field_id'=>constant(strtoupper('CATEGORY_OBJECT_'.$f))
            ,'language_id'=>$v
          );
          if (!empty($toUpdate[$v.'_'.$f])){
            $this->db->where($dataSet)->update('translation',array('content'=>$c));
          }
          else {
            $dataSet['content']=$c;
            $this->db->insert('translation',$dataSet);
          }
        }
      }
    }
  }
  

  
  function loadDetails($categoryID=0,$sections=array()){
    if ($categoryID==0){
      return $this->error($this->lang->phrase('category_is_not_chosen'));
    }
    
    $this->reply['data']=$this->db->select('c.*,t1.content AS title,t2.content AS description')
      ->join('translation AS t1','t1.target_object_id=c.category_id AND t1.target_object_type_field_id='.CATEGORY_OBJECT_TITLE.' AND t1.language_id=1','inner')
      ->join('translation AS t2','t2.target_object_id=c.category_id AND t2.target_object_type_field_id='.CATEGORY_OBJECT_DESCRIPTION.' AND t2.language_id=1','left')
      ->get_where('category AS c',array('c.category_id'=>$categoryID),1)->row_array();
    if (empty($this->reply['data'])){
      return $this->error($this->lang->phrase('category_is_not_found'));
    }
  }

  function edit($categoryID=0){
    if ($categoryID==0){
      $categoryID=$this->input->post('category_id')*1;
    }
    $this->loadDetails($categoryID);
  }  
}

?>