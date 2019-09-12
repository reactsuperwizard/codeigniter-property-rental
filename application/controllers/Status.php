<?php

class Status extends NS_Rental_Controller {

  function __construct() {
    parent::__construct(array('no_cache'=>1));
  }

  function index() {
    $page_data['targetObjectTypes'] = $this->db->order_by('name asc')->get('target_object_type')->result_array();
    $page_data['page_name'] = 'status';
    $page_data['page_title'] = $this->translate('manage_statuses');
    $this->setTemplate('backend','status/management',$page_data);
    //$this->load->view('backend/index', $page_data);
  }
  
  function filtered(){
    $this->load->library('DataTable',array('count'=>'s.status_id'));

    $this->datatable->from('status AS s')
      ->join('target_object_type AS tot','tot.target_object_type_id=s.target_object_type_id','inner');
      
    $searchTerm=trim($_POST['search']['value']);
    if ($searchTerm!=''){
      $this->datatable->filter()
        ->like('s.code',$searchTerm,'both',true)
        ->or_like('s.name',$searchTerm,'both',true)
        ->or_like('tot.code',$searchTerm,'both',true)
        ->or_like('tot.name',$searchTerm,'both',true);
    }
    //$order='tot.name';
    $this->datatable
      ->order_by('tot.name asc, s.name asc',TRUE)
      ->select('tot.code AS object_code,tot.name AS object_name,s.code,s.name,s.status_id')
      ->run($this->reply['data']);
    
  }
  
  function edit() {
    $this->reply['data']= $this->db->get_where('status', array('status_id' => $this->input->post('status_id')),1)->result_array()[0];
  }

  function save() {
    $statusID=$this->input->post('status_id')*1;
    $dataSet=$this->input->post(array('name','description'));
    foreach($dataSet AS $k=>$v){
      $dataSet[$k]=trim($v);
    }
    if ($dataSet['name']==''){
      $this->error($this->translate('name_should_be_set'),'name',true);
    }

    if ($statusID==0){
      $dataSet['target_object_type_id']=$this->input->post('target_object_type_id')*1;
      $dataSet['code']=trim($this->input->post('code'));
      if ($dataSet['code']==''){
        $this->error($this->translate('code_should_be_set'),'code',true);
      }
      $this->db->insert('status', $dataSet);
      $this->success($this->translate('data_added_successfully'));
    }
    else {
      $this->db->where('status_id',$statusID)->update('status', $dataSet);
    }
  }
  
  function remove(){
    $this->db->where('status_id', $this->input->post('status_id'));
    $this->db->delete('status');
    
    $this->success($this->translate('data_deleted'));
  }
}

?>