<?php

class NS_Controller extends CI_Controller {
  function __construct($config=array()) {
    parent::__construct();
    if (!defined('NS_BASE_URL')){
      define('NS_BASE_URL',$this->config->slash_item('base_url'));
    }
    if (!defined('MAIN_TIMESTAMP')){
      define('MAIN_TIMESTAMP',time());
    }
    $this->load->database();
    $this->load->library('Session');

    $this->errors=array('stack'=>array(),'targets'=>array(array('global',array())),'targetMap'=>array('global'=>0),'mx'=>1,'x'=>0);
    $this->reply=array('status'=>'success','errors'=>&$this->errors['stack'],'errorMap'=>&$this->errors['targets'],'error_fields'=>array(),'logs'=>array(),'data'=>array(),'config'=>array(),'request'=>array('get'=>$_GET,'post'=>$_POST));
    
    $this->reply['queries']=&$this->db->queries;
    $this->reply['session']=&$_SESSION;
    $this->template=array('layout'=>'','folder'=>'','view'=>'');

    $this->checkUser();
    
    //$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    //$this->output->set_header('Pragma: no-cache');
  }
  
  function getOrCreateID($table,$lookup,$dataSet,$update=false){
    $existingEntry=$this->db->get_where($table,$lookup,1)->row_array();
    if (!$existingEntry){
      $this->db->insert($table,$dataSet);
      return $this->db->insert_id();
    }
    else {
      if ($update){
        $this->db->where($lookup)->update($table,$dataSet);
      }
      if (!empty($existingEntry[$table.'_id'])){
        return $existingEntry[$table.'_id'];
      }
    }
  }
  
  function log($message){
    $this->reply['logs'][]=$message;
  }
          
  function getStatusList($objectCode){
    $data=$this->db->select('s.*')
      ->join('status AS s','s.status_target_object_id=sto.status_target_object_id','inner')
      ->get_where('status_target_object AS sto',array('sto.code'=>$objectCode))->result_array();
    foreach($data AS $e){
      $labelToID=strtoupper($objectCode.'_status_'.$e['code']);
      $labelToCode=strtoupper('status_'.$e['status_id']);
      if (!defined($labelToID)){
        define($labelToID, $e['status_id']);
        define($labelToCode,$e['code']);
      }
    }
    return $data;
  }
  
  function getStatusOption($objectCode,$statusCode){
    $label=strtoupper($objectCode.'_status_'.$statusCode);
    if (defined($label)){
      return constant($label);
    }
    else {
      $this->getStatusList($objectCode);
      $label=strtoupper($objectCode.'_status_'.$statusCode);
      if (defined($label)){
        return constant($label);
      }
    }
    return false;
  }
  
  function loadTranslationCodes(){
    if (!defined('OBJECT_CODES')){
      $list=$this->db->select('tof.target_object_type_id AS oi,to.code AS o, tof.code AS f,tof.target_object_type_field_id AS i')
        ->join('target_object_type AS to','to.target_object_type_id=tof.target_object_type_id','inner')
        ->get('target_object_type_field AS tof')->result_array();
      $objects=array();
      foreach ($list AS $e){
        define(strtoupper($e['o'].'_OBJECT_'.$e['f']),$e['i']);
        if (empty($objects[$e['oi']])){
          define(strtoupper($e['o'].'_OBJECT'),$e['oi']);
          $objects[$e['oi']]=1;
        }
      }
      define('OBJECT_CODES',true);
    }
  }
  

  
  function checkUser(){
    $userID=$this->session->userdata('userID')*1;
    if ($userID>0){
      $this->userInfo=$this->db
      ->select('u.user_id,u.email,CONCAT(u.first_name,\' \',u.last_name) AS name,r.code AS role',false)
      ->from('user AS u')->join('role AS r','r.role_id=u.role_id','left')
      ->where('u.user_id',$userID)->limit(1)->get()->row_array();
    }
    else {
      $this->userInfo=null;
    }
    $this->reply['userInfo']=$this->userInfo;
  }
  
  function notAllowed($message=null){
    $this->reply['status']='not_allowed';
    if (empty($message)){
      $message=$this->lang->phrase('action_not_allowed');
    }
    return $this->error($message);
  }

  function continueIfAllowed($config){
    $loginRoles=array();
    
    if (!empty($config['ignore'])){
      foreach($config['ignore'] AS $s){
        if ($this->uri->uri_string==$s){
          return true;
        }
      }
    }
    if (!empty($config['authorized'])){
      if (!empty($this->userInfo['user_id'])){
        if ($config['authorized']=='any'){
          return true;
        }
        elseif (is_array($config['authorized'])) {
          foreach($config['authorized'] AS $s){
            if ($this->uri->uri_string==$s){
              return true;
            }
          }
        }
      }
    }
    if (!empty($config['allowed'])){
      foreach($config['allowed'] AS $key=>$value){
        if (is_numeric($key)){
          if ($value==$this->userInfo['role']){
            return true;
          }
          $loginRoles[]=$value;
        }
        else {
          if ($key==$this->userInfo['role']){
            foreach($value AS $s){
              if ($this->uri->uri_string==$s){
                return true;
              }
            }
          }
          $loginRoles[]=$key;
        }
      }
    }

    //$this->reply['status']='not_allowed';
    //$this->reply['status']='fail';
    return $this->notAllowed();
  }
  
          
  function success($message){
    $this->reply['success_message']=$message;
  }
  
  
  function error($message,$field=false,$return=false){
    if ($this->reply['status']=='success'){
      $this->reply['status']='fail';
    }
    $field=(!empty($field))?$field:'global';
    
    if (!isset($this->errors['targetMap'][$field])){
      $this->errors['targetMap'][$field]=$this->errors['mx'];
      $this->errors['targets'][]=array(0=>$field,1=>array());
      $this->errors['mx']++;
    }
    
    $this->errors['stack'][]=$message;
    $this->errors['targets'][$this->errors['targetMap'][$field]][1][]=$this->errors['x'];
/**
    $this
    $this->errors['targets']['']
    if ($field){
      $
      $this->reply['error_fields'][$field]=$message;
      $this
    }
    else {
      $this->reply['errors'][]=$message;
    }**/
    
    if ($return){
      $this->returnReply();
    }
    $this->errors['x']++;
    return false;
  }
  
  function setErrors($errors){
    foreach($errors AS $e){
      $this->error($e);
    }
    //$this->reply['errors']=array_merge($this->reply['errors'],$errors);
    //$this->reply['status']='fail';
  }
  
  function hasErrors(){
    switch($this->reply['status']){
      case 'not_allowed':
      case 'fail':
        return true;
      break;
    }
    return isset($this->reply['errors'][0]);
  }
  function includeLocalJS($path){
    echo '<script type="text/javascript">'; 
    include(APPPATH.'../js/'.$path);
    echo '</script>';
  }
  
  function setTemplate($layout,$view=false,$config=array()){

    switch(REQUEST_TYPE){
      case 'JSON':
        $this->returnJSON();
      break;
      case 'AJAX':
        if ($this->reply['status']=='not_allowed'){
          $view='login.php';
        }
        $this->load->view($view,$config);
      break;
      default:
        switch($this->reply['status']){
          case 'not_allowed':
            if (is_file(APPPATH.'/views/forbidden.php')){
              $view='forbidden.php';
            }
            else {
              $view='basic.php';
            }
          break;
          case 'fail':
            $view='basic.php';
          break;
        }
        $config['view']=$view;
        //echo '<pre>'; print_r($data); echo '</pre>';
        
        $this->load->view($layout,$config);
      break;
    }
  }
  
  function returnJSON(){
    if ($this->reply['status']=='success' && !empty($this->reply['errors'][0])){
      $this->reply['status']='fail';
    }
    echo json_encode($this->reply);
    die();
  }
  
  function returnReply(){
    //echo REQUEST_TYPE;
    switch(REQUEST_TYPE){
      case 'JSON':
        $this->returnJSON();
      break;
      default:
        //echo '<pre>';print_r($this->reply); echo '</pre>';
        switch($this->reply['status']){
          case 'not_allowed':
            if (is_file(APPPATH.'views/forbidden.php')){
              $view='forbidden.php';
            }
            else {
              $view='basic.php';
            }
          break;
          case 'fail':
            $view='basic.php';
          break;
        }
        //echo $view;
        //$config['view']=$view;
        echo $this->load->view($view,false,true);   
      break;
    }
    die();
  }
  
  
  function redirect($URI,$method = 'auto', $code = NULL){  
    if (function_exists('addUniqueLog')){
      addUniqueLog('redirect requested');
      sleep(1);
    }
    if (REQUEST_TYPE=='JSON'){
      $this->reply['status']='redirect';
      $this->reply['redirect_url']=$URI;
      
      return $this->returnJSON();
    }    
    redirect($URI,$method,$code);
  }
  
  function translate($message){
    if (function_exists('get_phrase')){
      return get_phrase($message);
    }
    else {
      return $this->lang->phrase($message);
    }
  }
  
  function loadStoreSource($storeID=0){
    $storeID*=1;
    if ($storeID==0){
      return $this->error($this->translate('store_not_set'));
    }
    
    $storeData=$this->db->select('at.controller,as.config_code,s.store_id,s.api_source_id,at.api_type_id'
      .',IFNULL(s.remote_config,\'{}\') AS remote_config,IFNULL(s.local_config,\'{}\') AS local_config')
      ->join('api_source AS as','as.api_source_id=s.api_source_id','left')
      ->join('api_type AS at','at.api_type_id=as.api_type_id','left')
      ->get_where('store AS s',array('s.store_id'=>$storeID),1)->row_array();
    
    if (empty($storeData)){
      return $this->error($this->translate('store_source_not_found'));
    }
    
    require_once(APPPATH.'libraries/APIs/'.$storeData['controller'].'/config/'.$storeData['config_code'].'.php');
    if (empty($appConfig)){
      $appConfig=array();
    }
    
    $remoteConfig=json_decode(trim($storeData['remote_config']),TRUE);
    $localConfig=json_decode(trim($storeData['local_config']),TRUE);
    
    $this->storeSourceRemoteConfig=&$remoteConfig;
    $localConfig['instance']=&$this;
    $localConfig['storeID']=$storeData['store_id'];
    $localConfig['APITypeID']=$storeData['api_type_id'];
    $localConfig['APISourceID']=$storeData['api_source_id'];
    $localConfig['configUpdater']='updateStoreSourceRemoteConfig';
    $localConfig['errorLogger']='error';
    $localConfig['requestLogger']='updateStoreSourceLog';
    
    $config=array(
      'remote'=>array_merge($appConfig,$remoteConfig)
      ,'local'=>&$localConfig
    );

    $this->load->library('APIs/'.$storeData['controller'].'/'.$storeData['controller'],$config,'storeSource');
  }
  
  function updateStoreSourceRemoteConfig($dataSet){
    foreach($dataSet AS $k=>$v){
      $this->storeSourceRemoteConfig[$k]=$v;
    }
    $this->db->where('store_id',$this->storeSource->localConfig['storeID'])
      ->update('store',array('remote_config'=>json_encode($this->storeSourceRemoteConfig)));
  }
  
  function updateStoreSourceLog($logID,$storeID,$data){
    $logID*=1;
    if ($logID>0){
      $this->db->where('store_log_entry_id',$logID)->update('store_log',$data);
    }
    else {
      $this->db->insert('store_log',array_merge(array('store_id'=>$storeID,'timestamp'=>time()),$data));
      return $this->db->insert_id();
    }
  }
}

?>