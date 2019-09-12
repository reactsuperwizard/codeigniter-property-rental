<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(GLOBAL_LIBRARIES_PATH. 'PHPMailer/PHPMailer.php');
require_once(GLOBAL_LIBRARIES_PATH. 'PHPMailer/SMTP.php');

class EmailHandler {
  function __construct($config=array()){
    $this->BaseModule=get_instance();
    if (!is_array($config)){
      $this->BaseModule->warning('Email configuration is not set',true);
      return false;
    }
    
    if (!empty($config['mailer'])){
      $mailer=$config['mailer'];
    }
    else {
      $mailer='default';
    }
    
    $this->updateMailer($mailer);
  }
  
  function updateMailer($set='default'){
    include(APPPATH.'config/mailer.php');
    if (empty($mailer[$set])){
      $this->BaseModule->warning('Missing email configuration '.$set,true);
    }
    else {
      $this->mailer=$mailer[$set];
    }
  }
  
  
  function send($to,$subject,$body,$extraConfig=array()){
    $mail = new PHPMailer;
    
    switch($this->mailer['method']){
      case 'SMTP':
        $mail->isSMTP();
        $mail->isHTML($this->mailer['html']);
        $mail->Host = $this->mailer['host'];
        $mail->Port = $this->mailer['port'];
        //Enable SMTP debugging
        // 0 = off (for production use)
        // 1 = client messages
        // 2 = client and server messages
        $mail->SMTPDebug =  0;
        //Whether to use SMTP authentication
        $mail->SMTPAuth = $this->mailer['auth'];
        if ($this->mailer['auth']){
          $mail->Username=$this->mailer['username'];
          $mail->Password=$this->mailer['password'];
        }
        $mail->SMTPSecure = 'ssl';
        
      break;
    }
    $mail->setFrom($this->mailer['from']['email'],$this->mailer['from']['name']);

    if (is_array($to)){
      $mail->addAddress($to[0],$to[1]);
    }
    else {
      $mail->addAddress($to);
    }
    $mail->Subject=$subject;
    $mail->Body=$body;
    
    if (!empty($extraConfig['to'])){
      foreach($extraConfig['to'] AS $recipient){
        if (is_array($recipient)){
          $mail->addAddress($recipient[0],$recipient[1]);
        }
        else {
          $mail->addAddress($recipient);
        }
      }
    }
    
    if (!empty($extraConfig['cc'])){
      foreach($extraConfig['cc'] AS $recipient){
        if (is_array($recipient)){
          $mail->addCC($recipient[0],$recipient[1]);
        }
        else {
          $mail->addCC($recipient);
        }
      }
    }
    
    if (!empty($extraConfig['bcc'])){
      foreach($extraConfig['bcc'] AS $recipient){
        if (is_array($recipient)){
          $mail->addBCC($recipient[0],$recipient[1]);
        }
        else {
          $mail->addBCC($recipient);
        }
      }
    }
    //For test email not send
//    $mail->send();
  }
  
  function basicReport($subject='Full report'){
    $message=((!empty($this->BaseModule->reply['logs']))?('<h3>LOG</h3><p>'.join('<br/>',$this->BaseModule->reply['logs']).'</p>'):'<p>No logs registered</p>')
    .'<h3>Request GET:</h3><pre>'.print_r($_GET,true).'</pre>'
    .'<h3>Request POST:</h3><pre>'.print_r($_POST,true).'</pre>';
    
    $this->send('glenn@tisevents.com.au',$subject,$message,array('to'=>array('harrypotter990409@gmail.com')));
  }


  function send_email_rental($subject, $email, $message_body)
  {
    $this->send($email,$subject,$message_body,array('to'=>array('harrypotter990409@gmail.com')));
    $this->send($email,$subject,$message_body,array('to'=>array('glenn@tisevents.com.au')));
  }
  
}
