<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class PaymentHub {
  private $transactionID=null;
  private $failReason=null;
  private $transactionData=array();

  function __construct() {
    $this->CI=& get_instance();
  }
  
  function prepareTransactionData($config){
    $this->transactionData['timestamp']=MAIN_TIMESTAMP;
    $this->transactionData['target_object_type_id']=$this->CI->getTargetObjectTypeID($config['targetObjectType']);
    $this->transactionData['target_object_id']=$config['targetObjectID'];
    $this->transactionData['amount']=$config['amount'];
    $this->transactionData['payment_processor_id']=$this->CI->getObjectID(
      'payment_processor'
      ,array('code'=>$config['processorCode'])
    );
    
    $this->transactionData['currency_id']=$this->CI->getObjectID(
      'currency'
      ,array('code'=>$config['currencyCode'])
    );
    
    $this->transactionData['credit_card_type_id']=$this->CI->getObjectID(
      'credit_card_type'
      ,array('code'=>$config['cardType'])
    );
    
  }
  
  function fail($message){
    $this->transactionData['status_id']=$this->CI->getStatusOption('payment_transaction','failed');
    $this->transactionData['fail_reason']=$message;
    
    $this->registerTransaction(false);
    $this->CI->error('Problem with payment processing, please contact our admin',false,true);
  }
  
  function registerTransaction($transactionID){
    if (!empty($transactionID)){
      $this->transactionData['transaction_id']=$transactionID;
    }
    $this->CI->db->insert('payment_transaction',$this->transactionData);
  }

  function charge($config){
    if (!is_array($config)){
      $this->CI->error('Problem with payment config',false,true);
    }
    $fields=array(
      'processorCode'
      ,'targetObjectType','targetObjectID'
      ,'description'
      ,'amount','currencyCode','cardType'
    );
    
    foreach($fields AS $f){
      if (empty($config['base'][$f])){
        $this->CI->error('Problem with payment config',false,true);
      }
    }
    
    $this->prepareTransactionData($config['base']);
    
    $method='charge_'.$config['base']['processorCode'];
    if (!method_exists($this,$method)){
      $this->CI->error('Payment method is not defined',false,true);
    }
    $this->registerTransaction($this->{$method}($config));
  }
  
  function charge_stripe($config){
    require_once(APPPATH.'libraries/Stripe/Stripe.php');
    \Stripe\Stripe::setApiKey(STRIPE_KEY_SECRET);

    try {
      $chargeObject=\Stripe\Charge::create(array(
        "amount" => round($config['base']['amount'],2)*100,
        "currency" => $config['base']['currencyCode'],
        "source" => $config['custom']['token'], // obtained with Stripe.js
        "description" => $config['base']['description']
      ));
      $chargeData=$chargeObject->getLastResponse()->json;
      $this->transactionData['status_id']=$this->CI->getStatusOption('payment_transaction','complete');
      return $chargeData['id'];
    }
    catch (Exception $e){
      $this->fail($e->getMessage());
      return false;
    }
  }
  
}

