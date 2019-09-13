<?php

class Customer extends NS_Rental_Controller{
  function __construct($config = array()) {
    parent::__construct($config);
  }
  
  function header(){
    if (!$this->userInfo['user_id']){
      $this->error('You are not authorized',false,true);
    }
    $this->reply['data']=$this->userInfo;
    
    $this->load->model('Booking_model');
    $this->reply['data']['bookings']=$this->Booking_model->getAssigned($this->userInfo['user_id']);
    
    $this->load->model('Quote_model');
    $this->reply['data']['quotes']=$this->Quote_model->getAssigned($this->userInfo['user_id']);
  }
  function login(){
    $this->load->model('User_model');
    $userData=$this->User_model->login();
    
    if (!$this->hasErrors()){
      $this->reply['data']=array('name'=>$userData['first_name'].' '.$userData['last_name']);
      $this->reply['data']['residential_address']=$this->db->order_by('address_id','DESC')->get_where('address',array('user_id'=>$userData['user_id']),1)->row_array();
    }
  }

  
  function addresses(){
    $addressTypeData=$this->db->get('address_type')->result_array();
    $addressTypes=array();
    foreach($addressTypeData AS $addressType){
      $addressTypes[$addressType['code']]=$addressType['address_type_id'];
    }

    $this->reply['data']=$this->db
      ->where(array(
        'a.user_id'=>$this->input->post('user_id')*1
        ,'a.address_type_id'=>$addressTypes[$this->input->post('address_type')]
      ))
      ->get('address AS a')->result_array();
    // $this->reply['data']['distance'] = get_distance($this->reply['data']['postcode']);
    $this->returnJSON();
  }
  
}

?>