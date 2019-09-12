<?php

class User extends NS_Rental_Controller {

  function __construct() {
    parent::__construct();
    $this->continueIfAllowed(array(
      'allowed'=>array('admin','customer')
      ,'ignore'=>array('user/login','user/logout','user/autocomplete','user/profile','user/update_password')
    ));/**/
  }

  function index() {
    $this->reply['config']['roles']=$this->db->get('role')->result_array();
    $this->reply['config']['statuses']=$this->getStatusList('user');
    $this->setTemplate('backend','user/management');
  }
  
  function autocomplete(){
    $this->reply['data']=$this->db->select('u.user_id,CONCAT(u.first_name,\' \',u.last_name) AS name,u.email')->limit(20);
    $term=trim($this->input->post('term'));
    if ($term!=''){
      $this->db->like('u.email',$this->input->post('term'),'right',true)
      ->or_like('u.first_name',$this->input->post('term'),'right',true)
      ->or_like('u.last_name',$this->input->post('term'),'right',true);
    }
    switch($this->input->post['role']){
      default:
        $role='customer';
      break;
    }
  
    $this->db
      ->join('role AS r','r.role_id=u.role_id AND r.code=\''.$role.'\'','left')
      ->get('user AS u')->result_array();
  }

  function filtered(){
    /** /
    $users=$this->db->get('user')->result_array();
    foreach($users AS $u){
      $nameParts=explode(' ',$u['first_name']);
      if ($nameParts>1){
        $lastName=trim(substr($u['first_name'],strlen($nameParts[0])));
        $this->db->where('user_id',$u['user_id'])->update('user',array('first_name'=>$nameParts[0],'last_name'=>$lastName));
      }
    }/**/
    
    $this->load->library('DataTable',array('count'=>'u.user_id'));

    $role='customer';
    $inputRole=$this->input->post('role');
    switch($inputRole){
      case 'customer':
        $role=$inputRole;
      break;
      default:
        if ($this->userInfo['role']=='admin'){
          $role='any';
        }
      break;
    }
    $this->datatable->from('user AS u')
      ->join('role AS r','r.role_id=u.role_id'.(($role!='any')?(' AND r.code=\''.$role.'\''):''),'inner');
      
    $searchTerm=trim($_POST['search']['value']);
    if ($searchTerm!=''){
      $this->datatable->filter()->group_start()
        ->like('u.email',$searchTerm,'both',true)
        ->or_like('u.first_name',$searchTerm,'both',true)
        ->or_like('u.last_name',$searchTerm,'both',true)
        ->or_like('u.phone',$searchTerm,'both',true)
        ->group_end();
    }
    $order='u.first_name ASC,u.last_name ASC';
    $this->datatable
      ->order_by($order)
      //->order_by($order,$_POST['order'][0]['dir'],TRUE)
      ->select('u.*,u.company_name AS company,CONCAT(u.first_name,\' \',u.last_name) AS name, \'\' AS password, r.code AS role')
      ->run($this->reply['data']);
  }

  function edit() {
    $userID=@intval($_POST['user_id']);
    if ($userID==0){
      $userID=$this->userInfo['user_id'];
    }
    if ($userID==0){
      $this->error('User should be chosen',false,true);
    }
    
    $this->reply['data']=$this->db->get_where('user', array('user_id' => $userID),1)->row_array();
    if (!empty($this->reply['data'])){
      unset($this->reply['data']['password']);
    }
    else {
      $this->error($this->lang->phrase('data_is_not_found'));
    }
  }
//Retrieve user&address
  function edit_relation() {
    $userID=@intval($_POST['user_id']);
    if ($userID==0){
      $userID=$this->userInfo['user_id'];
    }
    if ($userID==0){
      $this->error('User should be chosen',false,true);
    }
//NOT SURE ABOUT EXEUCTION TIME
    $this->reply['data'] = $this->db->select('u.*, a.line_1, a.line_2, a.city, a.state, a.postcode')
      ->from('user as u')
      ->where('u.user_id', $userID)
      ->join('address as a', 'u.user_id = a.user_id', 'LEFT')
      ->get()->row_array();
    
    if (!empty($this->reply['data'])){
      unset($this->reply['data']['password']);
    }
    else {
      $this->error($this->lang->phrase('data_is_not_found'));
    }
  }

  
  function save_relation(){
    $userID=$_POST['user_id'];
    if ($userID!=$this->userInfo['user_id']){
      $this->continueIfAllowed(array('allowed'=>array('admin')));
    }
    if ($this->hasErrors()){
      $this->returnReply();
    }
    
    
    $this->load->model('User_model');
    //$dataSet=$this->input->post(array('first_name','last_name','company_name','phone','email','user_id','role_id'));
    $validationRules=array('phone_or_email','update_company_name');
    if ($this->userInfo['user_id']!=$userID){
      $validationRules[]='update_status';
    }
    if ($_POST['user_id']>0){
      $validationRules[]='skip_password';
    }
    if ($this->userInfo['role']=='admin'){
      $validationRules[]='update_role';
    }
    $isValidate = 1;
    $requiredFields1=array('line_1','city');

    foreach($requiredFields1 AS $f){
      if (empty($_POST[$f])){
        $isValidate = 0;
      }
    }

    if ($isValidate) {

      $userID = $this->User_model->save($_POST,$validationRules);
      
      //Address Saving Section
      if ($userID) {
        $this->load->model('Address_model');
        
        // $requiredFields=array('user_id', 'line_1','line_2','city','phone', 'postcode', 'state');
        $requiredFields=array('user_id', 'line_1','city');
        $_POST['residential_address']['user_id'] = $userID;
        $_POST['residential_address']['line_1'] = $_POST['line_1'];
        $_POST['residential_address']['line_2'] = $_POST['line_2'];
        $_POST['residential_address']['city'] = $_POST['city'];
        $_POST['residential_address']['phone'] = $_POST['phone'];
        $_POST['residential_address']['postcode'] = $_POST['postcode'];
        $_POST['residential_address']['state'] = $_POST['state'];
        
        $address_id=$this->db->get_where('address',array('user_id'=>$userID, 'address_type_id'=>'1'))->row_array();
        
        $_POST['residential_address']['address_id'] = $address_id["address_id"];
        
        $address_id = $residentialAddressID=$this->Address_model
        ->errorContainer('residential_address')
        ->save(array_merge(
          $_POST['residential_address']
        ,array('type'=>'residential')
        ),true,$requiredFields);
      }
    }
  }
  
  function save(){
    $userID=$_POST['user_id'];
    if ($userID!=$this->userInfo['user_id']){
      $this->continueIfAllowed(array('allowed'=>array('admin')));
    }
    if ($this->hasErrors()){
      $this->returnReply();
    }
    
    
    $this->load->model('User_model');
    //$dataSet=$this->input->post(array('first_name','last_name','company_name','phone','email','user_id','role_id'));
    $validationRules=array('phone_or_email','update_company_name');
    if ($this->userInfo['user_id']!=$userID){
      $validationRules[]='update_status';
    }
    if ($_POST['user_id']>0){
      $validationRules[]='skip_password';
    }
    if ($this->userInfo['role']=='admin'){
      $validationRules[]='update_role';
    }
    $this->User_model->save($_POST,$validationRules);
  }
  function save__() {
    $dataSet=$this->input->post(array('user_id','name'));
    $password=trim($this->input->post('password'));
    if ($password!=''){
      $dataSet['password']=password_hash($password, PASSWORD_DEFAULT);
    }
    
    if ($dataSet['user_id']==0){
      $dataSet['email']=trim($this->input->post('email'));
      if ($dataSet['email']==''){
        $this->error($this->lang->phrase('email_should_be_set'),'email');
      }
      if ($password==''){
        $this->error($this->lang->phrase('password_should_be_set'),'password');
      }
      
      if ($this->hasErrors()){
        return false;
      }

      
      $existing=$this->db->limit(1)->get_where('user',array('email'=>$dataSet['email']))->result_array();
      if (!empty($existing)){
        return $this->error($this->lang->phrase('email_is_already_registered'),'email');
      }
      unset($dataSet['user_id']);
      


      $allRoles=$this->db->get('role')->result_array();
      $roleMap=array();
      foreach($allRoles AS $r){
        $roleMap[$r['role_id']]=$r['code'];
        $roleMap[$r['code']]=$r['role_id'];
      }
      
      if ($this->userInfo['role']=='client'){
        $dataSet['role_id']=$roleMap['employee'];
        $clientID=$this->userInfo['user_id'];
      }
      $clientID=0;
      if ($this->userInfo['role']=='admin'){
        $dataSet['role_id']=$this->input->post('role_id')*1;
        
        if ($dataSet['role_id']==0){
          return $this->error($this->lang->phrase('role_should_be_chosen'),'role_id');
        }
        if ($dataSet['role_id']==$roleMap['employee']){
          $clientID=$this->input->post('client_id')*1;
          if ($clientID==0){
            return $this->error($this->lang->phrase('client_should_be_chosen'),'client_id');
          }
        }
      }
      $this->reply['config']['client']=$clientID;
      
      $this->db->insert('user', $dataSet);

      $userID = $this->db->insert_id();
      if ($clientID>0){
        $this->db->insert('employee',array('employee_id'=>$userID,'employer_id'=>$clientID));
      }
      $this->success($this->lang->phrase('data_added_successfully'));
    }
    else {
      $this->db->where('user_id',$dataSet['user_id']);
      $this->db->update('user', $dataSet);
    }
  }
  
  function login(){
    $email=trim($this->input->post('email'));
    if ($email==''){
      $this->error($this->lang->phrase('email_should_be_set'),'email');
    }
    $password=trim($this->input->post('password'));
    if ($password==''){
      $this->error($this->lang->phrase('password_should_be_set'),'password');
    }
    
    if (!$this->hasErrors()){
      $user=$this->db->where('email',$email,true)->get('user',1)->row_array();
      //$this->reply['config']['user']=$user;
      //$this->reply['config']['pass']=$password;
      if (!$user){
        return $this->error($this->lang->phrase('email_is_not_found'),'email');
      }
      if (!password_verify($password,$user['password'])){
        return $this->error($this->lang->phrase('password_is_wrong'),'password');
      }
      $this->session->set_userdata('userID',$user['user_id']);
    }
  }
  
  function logout(){
    $this->session->sess_destroy();
  }
  
  function remove(){
    $this->db->where('user_id', $this->input->post('user_id'));
    $this->db->delete('user');
    
    $this->success($this->lang->phrase('data_deleted'));
  }
}

?>