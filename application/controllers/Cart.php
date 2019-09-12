<?php

class Cart extends NS_Rental_Controller{
  function __construct($config = array()) {
    parent::__construct($config);
  }
  
  function update(){
    if (empty($_SESSION['NS_Rental'])){
      $_SESSION['NS_Rental']=array();
    }
    $content=array();
    
    if (!empty($_POST['content'])){
      $content=array();
      foreach($_POST['content'] AS $i=>$q){
        if ($q>0){
          $content[$i]=$q;
        }
      }
    }
    $_SESSION['NS_Rental']['cart']=$content;
    
    $this->load->model('ItemPrice_model');
    
    foreach($content AS $itemID=>$quantity){
      $this->reply['data'][$itemID]=$this->ItemPrice_model->getOne($itemID,$quantity);
    }
  }
}

?>