<?php

class Address extends NS_Rental_Controller {

  function __construct() {
    parent::__construct();
    /** /
    $this->continueIfAllowed(array(
      'allowed'=>array('admin','client')
      ,'ignore'=>array('address/login','address/logout','address/autocomplete','address/profile','address/update_password')
    ));/**/
  }

  function index() {
    $this->setTemplate('backend','address/management');
  }

  function autocomplete(){
    if (!empty($_POST['user_id'])){
      $this->db->where('a.user_id',$_POST['user_id']);
    }
    $this->reply['data']=$this->db->select('a.*')->get('address AS a',20)->result_array();
    
    
    $term=trim($this->input->post('term'));
    if ($term!=''){
      $this->db->like('u.email',$this->input->post('term'),'right',true)
      ->or_like('u.name',$this->input->post('term'),'right',true);
    }
    $this->db
      ->from('address AS u')
      ->join('role AS r','r.role_id=u.role_id AND r.code=\'customer\'','left')->get()->result_array();
  }/**/

  function filtered(){
    $this->load->library('DataTable',array('count'=>'a.address_id'));

    $this->datatable->from('address AS a')
      ->join('user AS u','u.user_id=a.user_id','left');

    $searchTerm=trim(@$_POST['search']['value']);
    if ($searchTerm!=''){
      $this->datatable->filter()->group_start()
        ->like('u.email',$searchTerm,'both',true)
        ->or_like('u.name',$searchTerm,'both',true)
        ->or_like('a.city',$searchTerm,'both',true)
        ->or_like('a.state',$searchTerm,'both',true)
        ->group_end();
    }
    $order='u.name';
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
      ->select('a.*, u.name,u.company_name')
      ->run($this->reply['data']);
  }

  function edit() {
    $data=$this->db->select('a.*,u.name,u.company_name')
      ->join('user AS u','u.user_id=a.user_id','left')
      ->get_where('address AS a', array('a.address_id' => $this->input->post('address_id')),1)->result_array();
    if (!empty($data)){
      $this->reply['data']=&$data[0];
    }
    else {
      $this->error($this->lang->phrase('data_is_not_found'));
    }
  }
  
  function save(){
    //$this->load->model('User_model');
    //$this->User_model->save();
  }
}

?>