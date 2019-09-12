<?php

class Venue extends NS_Rental_Controller {

  function __construct() {
    parent::__construct();
    $this->continueIfAllowed(array(
      'allowed'=>array('admin','client')
      ,'ignore'=>array('address/login','address/logout','address/autocomplete','address/profile','address/update_password')
    ));
  }

  function index() {
    $this->reply['config']['statuses']=$this->getStatusList('venue');

    $this->setTemplate('backend','venue/management');
  }

  function autocomplete(){
    
    $this->reply['data']=$this->db->select('u.address_id,u.name,u.email')->limit(20);
    $term=trim($this->input->post('term'));
    if ($term!=''){
      $this->db->like('u.email',$this->input->post('term'),'right',true)
      ->or_like('u.name',$this->input->post('term'),'right',true);
    }
    $this->db
      ->from('address AS u')
      ->join('role AS r','r.role_id=u.role_id AND r.code=\'customer\'','left')->get()->result_array();
  }

  function filtered(){
    $this->load->library('DataTable',array('count'=>'v.venue_id'));

    $this->datatable->from('venue AS v')
      ->join('address AS a','a.address_id=v.address_id','left');
      
    $searchTerm=trim($_POST['search']['value']);
    if ($searchTerm!=''){
      $this->datatable->filter()
        ->like('v.name',$searchTerm,'both',true)
        ->or_like('a.city',$searchTerm,'both',true)
        ->or_like('a.state',$searchTerm,'both',true);
    }
    $order='v.name';
    $orderDirection='ASC';
    if (!empty($_POST['order'])){
      if (!empty($_POST['order'][0])){
        if (!empty($_POST['order'][0]['dir'])){
          $orderDirection=$_POST['order'][0]['dir'];
        }
      }
    }
    $this->datatable
      ->order_by($order,$orderDirection,TRUE)
      ->select('v.*, a.*')
      ->run($this->reply['data']);
  }

  function edit() {
    $this->load->model('Address_model');
    
    $data=$this->db->get_where('venue AS v', array('v.venue_id' => $this->input->post('venue_id')),1)->result_array();
    if (!empty($data)){
      $this->reply['data']=&$data[0];
      $this->reply['data']['address']=$this->Address_model->get($this->reply['data']['address_id']);
    }
    else {
      $this->error($this->lang->phrase('data_is_not_found'));
    }
  }
  
  function save(){
    $this->load->model('Address_model');

    $fields=array('name','contact_name','contact_email','contact_phone','status_id');
    $dataSet=$this->input->post($fields,true);
    foreach ($fields AS $f){
      $dataSet[$f]=trim($dataSet[$f]);
    }
    if (empty($dataSet['name'])){
      $this->error($this->translate('name_should_be_set'));
    }

    if (empty($dataSet['status_id'])){
      $this->error($this->translate('status_should_be_chosen'));
    }

    $venueID=$this->input->post('venue_id')*1;
    $_POST['address']['type']='address';
    $dataSet['address_id']=$this->Address_model->save($_POST['address'],true);

    if (!$this->hasErrors()){
      if ($venueID==0){
        $this->db->insert('venue',$dataSet);
        $venueID=$this->db->insert_id();
      }
      else {
        $this->db->where('venue_id',$venueID)->update('venue',$dataSet);
      }
    }
  }
}

?>