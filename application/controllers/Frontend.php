<?php

class Frontend extends NS_Rental_Controller{
  function __construct($config = array()) {
    parent::__construct($config);
  }
  
  function index(){
    if (!empty($_GET['dropSession'])){
      unset($_SESSION['NS_Rental']);
    }
    $this->setTemplate('frontend/index');
    
    
    //$this->output->enable_profiler(true);
  }
  
  function includeLocalJS($path){
    echo '<script type="text/javascript">'; 
    include(APPPATH.'../js/'.$path);
    echo '</script>';
  }
  
  function updateFilters(){
    if (empty($_SESSION['NS_Rental'])){
      $_SESSION['NS_Rental']=array();
    }

    if (!empty($_POST['period'])){
      $period=$_POST['period'];
      $_SESSION['NS_Rental']['period']=$period;
    }
    else {
      $period=false;
    }
    

    $tags=array();
    if (!empty($_POST['tags'])){
      foreach($_POST['tags'] AS $t=>$s){
        if ($s==1){
          $tags[$t]=1;
        }
      }
    }
    $_SESSION['NS_Rental']['tags']=$tags;
  }
  
  function updateCache(){
    $this->loadTranslationCodes();
    
    $divider=3;
    
    $this->load->model('Item_model');
    $this->Item_model->getFiltered(array('publicOnly'=>1,'thumbnails'=>1,'getPackages'=>1));
    
    $this->load->model('Tag_model');
    
    $this->load->model('Category_model');
    $categories=$this->Category_model->getFiltered(array('publicOnly'=>1,'items'=>1));
    
    $itemCache=array();
    $tagCache=array('default'=>array());
    $itemTagCache=array('default'=>array());
    $categoryCache=array('default'=>array(0),'0'=>array('title'=>$this->lang->phrase('category'),'items'=>false));
    
    foreach($categories AS $c){
      if (empty($categoryCache[$c['category_id']])){
        $categoryCache[$c['category_id']]=array('title'=>$c['title'],'items'=>array());
        $categoryCache['default'][]=$c['category_id']*1;
      }
      $categoryCache[$c['category_id']]['items'][]=$c['item_id'];
    }
    
    
    foreach($this->reply['data']['entries'] AS $e){
      if (!empty($itemCache['p'.$e['item_id']])){
        unset($itemCache['p'.$e['item_id']]);
      }
      $quantity=$e['quantity'];
      
      $itemCache[$e['item_id']]=array(
        'title'=>((($quantity==0)?'(not in stock) ':'').$e['title'])
        ,'description'=>$e['description']
        ,'price'=>$e['price'],'quantity'=>$quantity
        ,'thumbnail'=>NS_BASE_URL.'uploads/'.$e['folder'].'/'.$e['filename']
        ,'tags'=>array()
      );
      
      $itemCache['default'][]=$e['item_id'];
      if ($e['item_package_id']>0){
        $itemCache[$e['item_id']]['package']=array();
        $packageContent=$this->Item_model->getPackage($e['item_id']);
        foreach($packageContent AS $packedItem){
          $itemCache[$e['item_id']]['package'][$packedItem['item_id']]=$packedItem['quantity'];
          if (!isset($itemCache['p'.$packedItem['item_id']]) && !isset($itemCache[$packedItem['item_id']])){
            $itemCache[$packedItem['item_id']]=array('title'=>$packedItem['title'],'quantity'=>$packedItem['max_quantity']);
          }
        }
      }
      
      $tags=$this->Tag_model->getForTarget('item',$e['item_id']);
      foreach($tags AS $t){
        if (empty($tagCache[$t['tag_id']])){
          $tagCache[$t['tag_id']]=array('code'=>$t['code'],'items'=>array());
          $tagCache['default'][]=$t['tag_id'];
        }
        $itemCache[$e['item_id']]['tags'][]=$t['tag_id'];
        $tagCache[$t['tag_id']]['items'][]=$e['item_id'];
      }
    }
    
    $cacheDir=APPPATH.'../uploads/cached';
    if (!is_dir($cacheDir)){
      mkdir($cacheDir);
    }
    error_reporting(E_ALL);
    ini_set('display_errors','1');
    
    $fl=fopen($cacheDir.'/item_json.js','w');
    fwrite($fl,',"items":'.json_encode($itemCache));
    fclose($fl);
    
    $fl=fopen($cacheDir.'/tag_json.js','w');
    fwrite($fl,',"tags":'.json_encode($tagCache));
    fclose($fl);
    
    $fl=fopen($cacheDir.'/category_json.js','w');
    fwrite($fl,',"categories":'.json_encode($categoryCache));
    fclose($fl);
    
    $endTimestamp=MAIN_TIMESTAMP+86400*365;
    
    for ($timestamp=(MAIN_TIMESTAMP+86400);$timestamp<=$endTimestamp;$timestamp+=86400){
      $daily=array();
      foreach($itemCache['default'] AS $itemID){
        $daily[$itemID]=$itemCache[$itemID]['quantity'];
        /** /
        $daily[$itemID]=rand(0,1000);
        if ($itemID%$divider==0){
          $daily[$itemID]=0;
        }/**/
      }
      $fl=fopen($cacheDir.'/item_'.date('Ymd',$timestamp).'.php','w');
      fwrite($fl,'<?php $itemAvailability='.str_replace(
        array('{','}',':')
        ,array('[',']','=>')
        ,json_encode($daily)
      ).'; ?>');
      fclose($fl);
    }
    //$this->setTemplate('basic');
    $this->output->enable_profiler(true);
  }
  
  
}