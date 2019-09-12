<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class DataTable {
  protected $mode=NULL;
  protected $localConfig=array('join'=>array(),'conditions'=>array());
  protected $count=NULL;
  protected $fullConfig=array('from'=>'','select'=>array(),'order'=>array());
  protected $counters=array('total'=>1,'filtered'=>1);
  
  function __construct($config=array()){
    if (!empty($config['count'])){
      $this->count=$config['count'];
    }
    $this->total();
  }

  protected function loadMode($mode){
    if ($this->mode!=$mode){
      $this->fullConfig[$mode]=$this->localConfig;
    }
    $this->mode=$mode;
    return $this;
  }
  
  function total(){
    $this->loadMode('total');
    return $this;
  }
  
  function filter(){
    $this->loadMode('filtered');
    return $this;
  }
  
  public function from($table){
    $this->fullConfig['from']=$table;
    return $this;
  }
  
  public function join($table, $cond, $type = '', $escape = NULL){
    $this->fullConfig[$this->mode]['join'][]=array($table, $cond, $type, $escape);
    return $this;
  }
  
  public function group_start(){
    $this->fullConfig[$this->mode]['conditions'][]=array('group_start');
    return $this;
  }
  public function or_group_start(){
    $this->fullConfig[$this->mode]['conditions'][]=array('or_group_start');
    return $this;
  }
  public function group_end(){
    $this->fullConfig[$this->mode]['conditions'][]=array('group_end');
    return $this;
  }
  
  public function where($key, $value = NULL, $escape = NULL){
    $this->fullConfig[$this->mode]['conditions'][]=array('where',$key, $value, $escape);
    return $this;
  }
  public function or_where($key, $value = NULL, $escape = NULL){
    $this->fullConfig[$this->mode]['conditions'][]=array('or_where',$key, $value, $escape);
    return $this;
  }
  
  public function where_in($key, $value = NULL, $escape = NULL){
    $this->fullConfig[$this->mode]['conditions'][]=array('where_in',$key, $value, $escape);
    return $this;
  }
  public function or_where_in($key, $value = NULL, $escape = NULL){
    $this->fullConfig[$this->mode]['conditions'][]=array('or_where_in',$key, $value, $escape);
    return $this;
  }
  
  public function where_not_in($key, $value = NULL, $escape = NULL){
    $this->fullConfig[$this->mode]['conditions'][]=array('where_not_in',$key, $value, $escape);
    return $this;
  }
  public function or_where_not_in($key, $value = NULL, $escape = NULL){
    $this->fullConfig[$this->mode]['conditions'][]=array('or_where_not_in',$key, $value, $escape);
    return $this;
  }
  
  public function like($field, $match = '', $side = 'both', $escape = NULL){
    $this->fullConfig[$this->mode]['conditions'][]=array('like',$field,$match,$side,$escape);
    return $this;
  }
  
  public function or_like($field, $match = '', $side = 'both', $escape = NULL){
    $this->fullConfig[$this->mode]['conditions'][]=array('or_like',$field,$match,$side,$escape);
    return $this;
  }

  public function select($select = '*', $escape = NULL){
    $this->fullConfig['select'][]=array($select,$escape);
    return $this;
  }
  
  public function order_by($orderby, $direction = '', $escape = NULL){
    $this->fullConfig['order'][]=array($orderby, $direction, $escape);
    return $this;
  }
  
  public function skipCounter($mode){
    $modes=(is_array($mode))?$mode:(($mode=='any')?array_keys($this->counters):array($mode));
    
    foreach($modes AS $m){
      $this->counters[$m]=0;
    }
  }
  public function resetCounters(){
    $modes=array_keys($this->counters);
    foreach($modes AS $m){
      $this->counters[$m]=1;
    }
  }
  
  public function run(&$target){
    $CI=& get_instance();
    
    //echo '<pre>'; print_r($CI->db); echo '</pre>';
    $CI->db->start_cache();
    $CI->db->from($this->fullConfig['from']);
    $modes=array('total','filtered');
    
    foreach ($modes AS $m){
      $CI->db->start_cache();
      if (!empty($this->fullConfig[$m])){
        if (!empty($this->fullConfig[$m]['join'])){
          foreach($this->fullConfig[$m]['join'] AS $j){
            $CI->db->join($j[0],$j[1],$j[2],$j[3]);
          }
        }
        if (!empty($this->fullConfig[$m]['conditions'])){
          foreach($this->fullConfig[$m]['conditions'] AS $c){
            switch($c[0]){
              case 'where':
              case 'where_in':
              case 'where_not_in':
                $CI->db->{$c[0]}($c[1],$c[2],$c[3]);
              break;
              case 'like':
              case 'or_like':
                $CI->db->{$c[0]}($c[1],$c[2],$c[3]);//,$c[4]);
              break;
              case 'group_start';
              case 'or_group_start':
              case 'group_end':
                $CI->db->{$c[0]}();
              break;
            }
          }
        }
      }
      $CI->db->stop_cache();
      if ($this->counters[$m]==1){
        $target[$m]=$CI->db->select('COUNT('.$this->count.') AS total')->get()->row()->total;
      }
    }
    
    $start=$CI->input->post('start')*1;
    $length=$CI->input->post('length')*1;
    
    if (!empty($this->fullConfig['select'])){
      foreach($this->fullConfig['select'] AS $s){
        $CI->db->select($s[0],$s[1]);
      }
    }
    if (!empty($this->fullConfig['order'])){
      foreach($this->fullConfig['order'] AS $o){
        $CI->db->order_by($o[0],$o[1],$o[2]);
      }
    }
    
    //$target['datatable_config']=$this->fullConfig;
    $target['entries']=$CI->db
      ->limit($length,$start)
      ->get()->result_array();
    //$target['queries']=$CI->db->queries;
    $CI->db->flush_cache();
    
    $this->mode='total';
    $this->counters=array('total'=>1,'filtered'=>1);
    $this->localConfig=array('join'=>array(),'conditions'=>array());
    
    $this->fullConfig=array('from'=>'','select'=>array(),'order'=>array());

    //$target=$this->fullConfig;
  }
}

?>