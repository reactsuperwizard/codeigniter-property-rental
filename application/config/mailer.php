<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// $mailer=array();

// $mailer['default']=array(
//   'method'=>'SMTP'
//   ,'host'=>'smtp.gmail.com'
//   ,'port'=>587
//   ,'username'=>'nickolas.tests@gmail.com'
//   ,'password'=>'qaz!12345'
//   ,'from'=>array('email'=>'nickolas.tests@gmail.com','name'=>'NS Rental')
//   ,'html'=>true
//   ,'auth'=>true
// );

// $mailer=array();

// $mailer['default']=array(
//   'method'=>'SMTP'
//   ,'host'=>'smtp.postmarkapp.com'
//   ,'port'=>587
//   ,'username'=>'92d2aeb6-47c6-4ac2-92aa-88c721fa4dbe'
//   ,'password'=>'92d2aeb6-47c6-4ac2-92aa-88c721fa4dbe'
//   ,'from'=>array('email'=>'hello@rentalmagnet.app','name'=>'NS Rental@@@')
//   ,'html'=>true
//   ,'auth'=>true
// );


// $mailer=array();

// $mailer['default']=array(
//   'method'=>'SMTP'
//   ,'host'=>'smtp.zoho.com'
//   ,'port'=>587
//   ,'username'=>'enquiry@tisevents.com.au'
//   ,'password'=>'8dNpvmJWkPdq'
//   ,'smtp_crypto' => 'TLS'
//   ,'mailtype' => 'html'
//   ,'html'=>true
//   ,'charset' => 'iso-8859-1'
//   ,'auth' => true
//   ,'from'=>array('email'=>'glenn@tisevents.com.au','name'=>'NS Rental@@@')
//   );

$mailer=array();

$mailer['default']=array(
  'method'=>'SMTP'
  ,'host'=>'pro.turbo-smtp.com'
  ,'port'=>465
  ,'username'=>'rebookings@ntes.net.au'
  ,'password'=>'qCMsaZqK'
  ,'smtp_crypto' => 'SSL'
  ,'mailtype' => 'html'
  ,'html'=>true
  ,'charset' => 'iso-8859-1'
  ,'auth' => true
  ,'from'=>array('email'=>'rebookings@ntes.net.au','name'=>'RentalMagnet')
  );