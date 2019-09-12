<div id="NS_alert" class="hidden">
<?php 
$panels=array('successAlert'=>'success','failAlert'=>'danger','confirmationAlert'=>'warning');

foreach ($panels AS $ID=>$class){
  echo '<div id="'.$ID.'" class="panel panel-'.$class.' hidden">
    <div class="panel-heading">';  echo '</div>
    <div class="panel-body">';  echo '</div>
  </div>';
}
?>
  <div id="forbiddenAlert" class="panel panel-danger hidden">
    <div class="panel-heading"><?php echo $this->lang->phrase('action_not_allowed'); ?></div>
    <div class="panel-body"><?php require_once(APPPATH.'views/login.php'); ?></div>
  </div>
</div>