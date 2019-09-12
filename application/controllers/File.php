<?php

class File extends NS_Rental_Controller{
  function __construct() {
    parent::__construct();
  }
  
  function upload(){
    $this->load->model('File_model');
    $fileFilterID=$this->File_model->getFilterID($this->input->post('hash',true));
    
    $filename=$this->input->post('filename',true);
    
    switch($_POST['mode']){
      case 'binary_encoded':
        $mode='wb';
        $content=base64_decode(substr($_POST['content'],(strpos($_POST['content'],',')+1)));
      break;
    }
    $this->reply['data']['mode']=$mode;

    $hash=md5(MAIN_TIMESTAMP.'|'.$filename);
    $folderLevel=3;
    $startDir='uploads';
    $dir=$startDir;
    $folder='';

    for($i=0;$i<$folderLevel;$i++){
      $dir.='/'.substr($hash,($i*2),2);
      if (!is_dir($dir)){
        mkdir($dir,0777);
      }
    }

    $fl=fopen($dir.'/'.$filename,$mode);
    fwrite($fl,$content);
    fclose($fl);
    
    $location=$dir.'/'.$filename;
    
    $dataSet=array(
      'file_filter_id'=>$fileFilterID
      ,'timestamp'=>MAIN_TIMESTAMP
      ,'folder'=>substr($dir,(strlen($startDir)+1))
      ,'name'=>$filename
      ,'mime'=>mime_content_type($location)
    );
    $this->db->insert('file',$dataSet);
    $this->reply['data']['file_id']=$this->db->insert_id();

    $this->reply['data']['folder']=$dataSet['folder'];
    $this->reply['data']['name']=$dataSet['name'];
    $this->reply['data']['location']=$location;
    $this->reply['data']['mime']=mime_content_type($location);
    $this->reply['data']['name']=$filename;
    
    unset($this->reply['request']);
  }
  function remove(){
    if ($this->continueIfAllowed(array('allowed'=>array('admin')))){
    $fileID=$this->input->post('file_id')*1;
    $this->db->delete('file',array('file_id'=>$fileID));
    }
  }
}

?>