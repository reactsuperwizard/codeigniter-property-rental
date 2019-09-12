<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class User_model extends CI_Model {

  function __construct() {
    parent::__construct();
    
    $this->BaseModule=get_instance();
    $this->load->library('EmailHandler',false,'EmailHandler');
  }
  
  function removeNonFinalized($email){
    $this->db->where(array('email'=>$email,'is_final'=>0))->delete('user');
  }
  
  function finalize($userID){
    $this->db->where(array('user_id'=>$userID))->update('user',array('is_final'=>1));
  }
  
  function emailCanBeAdded($email,$userID){
    $userData=$this->db
      ->get_where('user',array('email'=>$email,'user_id <>'=>intval($userID)))
      ->row_array();

    if ($userData){
      return false;
    }
    return true;
  }
  
  function save($dataToValidate,$validationCodes=array(),$errorRules=array()){
    $dataSet=array();
    

    $errorTarget=(isset($errorRules['target']))?$errorRules['target']:false;
    $errorStop=(isset($errorRules['stop']))?$errorRules['stop']:false;

    $validationRules=array();
    foreach($validationCodes AS $code){
      $validationRules[$code]=1;
    }
    
    $userID=@intval($dataToValidate['user_id']);
    
    $validNameParts=array();
    $dataSet['first_name']=@trim($dataToValidate['first_name']);
    if (preg_match("/^[a-zA-Z`']{2,}$/",$dataSet['first_name'])){
      $validNameParts['first_name']=1;
    }
    $dataSet['last_name']=@trim($dataToValidate['last_name']);
    if (preg_match("/^[a-zA-Z`']{2,}$/",$dataSet['last_name'])){
      $validNameParts['last_name']=1;
    }
    if (empty($validationRules['partial_name']) || empty($validNameParts)){
      if (empty($validNameParts['first_name'])){
        $this->BaseModule->error('First name should be valid',(($errorTarget)?$errorTarget.'[first_name]':false),$errorStop);
      }
      if (empty($validNameParts['last_name'])){
        $this->BaseModule->error('Last name should be valid',(($errorTarget)?$errorTarget.'[last_name]':false),$errorStop);
      }
    }
    
    $email=@trim($dataToValidate['email']);
    $validEmail=0;
    /* Email unique part*/
    /*if (preg_match('/^[a-zA-Z0-9\._]+(\+[a-zA-Z0-9\._]+)?@[a-zA-Z0-9_]+(\.[a-zA-Z]{2,8}){1,2}$/', $email)){
      if (!$this->emailCanBeAdded($email,$userID)){
        $this->BaseModule->error('This email can not be added',(($errorTarget)?$errorTarget.'[email]':false),$errorStop);
      }
      else {
        $dataSet['email']=$email;
        $validEmail=1;
      }
    }*/


    /*  for Test model*/
    $dataSet['email']=$email;
    $validEmail=1;
    /*  */

    $phone=preg_replace('/[^0-9A-Za-z]/','',@trim($dataToValidate['phone']));
    $validPhone=0;

    $this->BaseModule->reply['data']['customer_phone']=$phone;
    if (preg_match('/^(0[0-9])?[0-9]{8}$/',$phone)){
      $dataSet['phone']=$phone;
      $validPhone=1;
    }
    /*Not make email or phone _ must both are available */
    //  else {
    //   $this->BaseModule->error('Phone should be valid',(($errorTarget)?$errorTarget.'[phone]':false),$errorStop);
    // }
    
    if (empty($validationRules['phone_or_email']) || !$validEmail && !$validPhone){
      if (!$validPhone){
        $this->BaseModule->error('Phone should be valid',(($errorTarget)?$errorTarget.'[phone]':false),$errorStop);
      }
      if (!$validEmail){
        $this->BaseModule->error('Email should be valid',(($errorTarget)?$errorTarget.'[email]':false),$errorStop);
      }
    }
    elseif (!empty($validationRules['phone_or_email']) && $validPhone && $email==''){
      $dataSet['email']=$this->BaseModule->makeCode().'@rentevent.com.au';
    }

    $validPassword=0;
    $password='';
    if (isset($dataToValidate['password'])){
      $password=@trim($dataToValidate['password']);
    }
    elseif ($userID==0 && !empty($validationRules['skip_password'])){
      $password=$this->BaseModule->makeCode();
    }

    /* Send email to customer*/

    $emailSubject = 'Dear '.$dataSet['first_name'].' Welcome to RentEvent';
    $message_body = 'Here is your password to access our system in the future. '.$password.
    ', You will need this password to log in and view your current and previous bookings and to update any address or booking details. Thanks for being a customer and we look forward to exceeding your expectations every step of the way. Thanks, The RentEvent Team.';
    $this->EmailHandler->send_email_rental($emailSubject, $dataSet['email'], $message_body);

    /* End */

    if ($password!=''){
      $dataSet['password']=password_hash($password,PASSWORD_DEFAULT);
      $validPassword=1;
    }
    
    if (empty($validationRules['skip_password']) && $password==''){
      $this->BaseModule->error('Password should be set',(($errorTarget)?$errorTarget.'[password]':false),$errorStop);
    }

    if ($validationRules['update_company_name']){
      $dataSet['company_name']=@((isset($dataToValidate['company_name']))?trim($dataToValidate['company_name']):trim($dataToValidate['company']));
      if ($validationRules['valid_company_name'] && $dataSet['company_name']==''){
        $this->BaseModule->error('Company name should be valid',(($errorTarget)?$errorTarget.'[company_name]':false),$errorStop);
      }
    }
    
    if (!empty($validationRules['update_status'])){
      $dataSet['status_id']=intval($dataToValidate['status_id']);
      if ($dataSet['status_id']==0){
        $this->BaseModule->error('Status should be set',(($errorTarget)?$errorTarget.'[status_id]':false),$errorStop);
      }
    }
    
    if (!empty($validationRules['update_role'])){
      $roleID=@intval($dataToValidate['role_id']);
      if ($roleID>0){
        $dataSet['role_id']=$roleID;
      }
    }
    elseif ($userID==0) {
      $dataSet['role_id']=$this->db->get_where('role',array('code'=>'customer'),1)->row_array()['role_id'];
      
    }
    
    if (!$this->BaseModule->hasErrors()){
      if ($userID==0){
        $dataSet['creation_timestamp']=time();
        $this->db->insert('user',$dataSet);
        $userID=$this->db->insert_id();
        $this->BaseModule->log('user creation: just basic report, no real notification yet');
        $this->BaseModule->mailReport('User created #'.$userID);

      }
      else {
        $oldUser=$this->db->get_where('user',array('user_id'=>$userID),1)->row_array();
        $updateNeeded=false;
        foreach($dataSet AS $f=>$v){
          if ($oldUser[$f]!=$dataSet[$f]){
            $updateNeeded=true;
            break;
          }
        }
        if ($updateNeeded){
          $this->BaseModule->log('user update: just basic report, no real notification yet');
          $this->db->where('user_id',$userID)->update('user',$dataSet);
          $this->BaseModule->mailReport('User updated');
        }
      }
    }
    return $userID;
  }
  
  function save_($extraConfig=array()){
    $CI= & get_instance();
    
    $userID=$this->input->post('user_id')*1;
    
    $dataSet=$this->input->post(array('first_name','last_name'),true);
    foreach($dataSet AS $k=>$v){
      $dataSet[$k]=trim($v);
      if ($v==''){
        $CI->error($CI->translate($k.'_should_be_set'));
      }
    }
    $moreFields=array('company_name','phone');
    foreach($moreFields AS $f){
      if (!empty($_POST[$f])){
        $dataSet[$f]=$_POST[$f];
      }
    }
    
    $password=trim($this->input->post('password'));
    if ($password!=''){
      $dataSet['password']=password_hash($password,PASSWORD_DEFAULT);
    }
    //$CI->load->model('File_model');
    
    $dataSet['status_id']=$this->input->post('status_id')*1;
    if (!$CI->hasErrors()){
      if ($userID==0){
        $dataSet['role_id']=$this->input->post('role_id')*1;
        if (empty($dataSet['role_id'])){
          $roleData=$this->db->get_where('role',array(
            'code'=>((empty($extraConfig['role']))?'customer':$extraConfig['role'])
          ))->row_array();
          if (!$roleData){
            $CI->error($CI->translate($role).' '.$CI->translate('role_not_found'));
          }
          else {
            $dataSet['role_id']=$roleData['role_id'];
          }
        }

        if ($password==''){
          $CI->error($CI->translate('password_should_be_set'));
        }

        $email=trim($this->input->post('email'),true);
        if ($email==''){
          $CI->error($CI->translate('email_should_be_set'));
        }
        else {
          $this->removeNonFinalized($email);
          $dataSet['email']=$email;
          $existing=$this->db->limit(1)->get_where('user',array('email'=>$dataSet['email']))->row_array();
          if (!empty($existing)){
            if (empty($extraConfig['existingID'])){
              return $existing['user_id'];
            }
            return $CI->error($CI->translate('email_is_already_registered'),'email');
          }
        }

        if (!$CI->hasErrors()){
          $dataSet['creation_timestamp']=time();
          $this->db->insert('user',$dataSet);
          $userID=$this->db->insert_id();
        }
      }
      else {
        $this->db->where('user_id',$userID)->update('user',$dataSet);
      }
    }
    return $userID;
  }

  function login(){
    $email=trim($this->input->post('email'));
    if ($email==''){
      $this->BaseModule->error($this->lang->phrase('email_should_be_set'),'email');
    }
    $password=trim($this->input->post('password'));
    if ($password==''){
      $this->BaseModule->error($this->lang->phrase('password_should_be_set'),'password');
    }
    
    if (!$this->BaseModule->hasErrors()){
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
      return $user;
    }
  }
}

?>