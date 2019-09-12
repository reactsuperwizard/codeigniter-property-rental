<html>
<head>
<base href="<?php echo NS_BASE_URL;//'http'.((is_https())?'s':'').'://'.$_SERVER['HTTP_HOST'].EXTRA_URL; ?>"/>
<script type="text/javascript" src="js/run_when_ready.js"></script>
<link rel="stylesheet" type="text/css" href="css/jquery-ui.min.css"/>
<script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="js/jquery-ui.min.js"></script>

<link rel="stylesheet" href="css/bootstrap.min.css"/>
<script src="js/bootstrap.min.js"></script>

<link rel="stylesheet" type="text/css" href="css/jquery.dataTables.css"/>
<script type="text/javascript" src="js/jquery.dataTables.min.js"></script>

<script type="text/javascript" src="js/helpers.js?<?php echo time(); ?>"></script>

<script type="text/javascript" src="js/nickolas_solutions.js?<?php echo time(); ?>"></script>
<script type="text/javascript">

function logout(){
  _NS.post('<?php echo NS_BASE_URL; ?>user/logout',{},{
    'success':function(reply){
      document.location.replace('<?php echo NS_BASE_URL; ?>');
    }
  },1);
}
</script>
<link rel="stylesheet" type="text/css" href="css/main.css?<?php echo time(); ?>"/>
</head>
<body>
<div class="row">
<div id="menu" class="" <?php /** / ?>style="height: 100%;"<?php /**/ ?>>
  <ul>
    <?php function menuSection($section){
      echo 'href="'.NS_BASE_URL.$section.'"';
      //echo 'onclick="_NS.sections.load(\'#sections\',\''.$section.'\');"';
    } ?>
    <li><a <?php menuSection('dashboard'); ?>><?php echo $this->lang->phrase('dashboard'); ?></a></li>
    
    <li><a <?php menuSection('bookings'); ?>><?php echo $this->lang->phrase('bookings'); ?></a></li>
    <li><a <?php menuSection('quotes'); ?>><?php echo $this->lang->phrase('quotes'); ?></a></li>
    <?php if ($this->userInfo['role']=='admin' || 1!=2){ ?>
    <li><a <?php menuSection('categories'); ?>><?php echo $this->lang->phrase('categories'); ?></a></li>
    <li><a <?php menuSection('items'); ?> ><?php echo $this->lang->phrase('items'); ?></a></li>
    <li><a <?php menuSection('item_locks'); ?>><?php echo $this->lang->phrase('item locks'); ?></a></li>
    <li><a <?php menuSection('users'); ?>><?php echo $this->lang->phrase('users'); ?></a></li>
    <li><a <?php menuSection('venues'); ?>><?php echo $this->lang->phrase('venues'); ?></a></li>
    <li><a <?php menuSection('statuses'); ?>><?php echo $this->lang->phrase('statuses'); ?></a></li>
    <div class="pull-right" style="margin-right: 20px;">
      <li style="color:white;margin-right: 5px;margin-top: 13px;"><?php echo $this->userInfo['name'].' ('.$this->userInfo['email'].')'?></li>
      <li><?php echo '<a id="logout_btn" class="btn btn-success" onclick="logout();">'.$this->lang->phrase('logout').'</a>'; ?></li>
    </div>
    <?php } ?>
  </ul>
</div>
<div id="sections" class="col-xs-12"><hr/><?php 
switch($this->reply['status']){
  case 'success':
    require_once(APPPATH.'views/'.$view.((substr($view,-4)=='.php')?'':'.php'));
  break;
  default:
    echo $this->reply['status'];
    echo '<script type="text/javascript">runWhenReady(function(){_NS.defaultReplyActions[\''.$this->reply['status'].'\']('.json_encode($this->reply).');});</script>';
  break;
}
?></div>
</div>
<?php 
//echo APPPATH.'views/alert.php';
require_once(APPPATH.'views/alert.php'); ?>


<?php //echo '<pre>';print_r($_SERVER); echo '</pre>'; echo $this->config->slash_item('base_url').'<hr/>'.str_replace($_SERVER['DOCUMENT_ROOT'],'',dirname($_SERVER['SCRIPT_FILENAME'])).'<br/>'.__FILE__.'<br/>'.__DIR__;?>
<?php /** /
echo '<pre>';print_r($this->reply); echo '</pre>';
$sessionData=$this->session->userdata();
unset($sessionData['fields']);
echo '<pre>';print_r($sessionData); echo '</pre>'; /**/ ?>
</body>
</html>