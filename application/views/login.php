<?php $loginID=uniqid('l'); ?>
<div id="<?php echo $loginID; ?>">
  <h3><?php echo $this->lang->phrase('login'); ?></h3>
  <form>
    <div>
      <div class="row form-group">
        <label class="col-xs-3 col-xs-offset-1 control-label"><?php echo $this->lang->phrase('email'); ?></label>
        <div class="col-xs-7">
          <input type="text" class="form-control" name="email"/>
        </div>
      </div>
      <div class="row form-group">
        <label class="col-xs-3 col-xs-offset-1 control-label"><?php echo $this->lang->phrase('password'); ?></label>
        <div class="col-xs-7">
          <input type="password" class="form-control" name="password"/>
        </div>
      </div>
    </div>
    <div class="row form-group">
      <div class="col-xs-offset-4 col-xs-7">
        <a class="btn btn-primary" onclick="login();"><?php echo $this->lang->phrase('login'); ?></a>
      </div>
    </div>
  </form>
</div>
<script type="text/javascript">
function login(){
  _NS.post('<?php echo NS_BASE_URL; ?>user/login',_NS.getFormData('#<?php echo $loginID; ?> form'),{
    'success':function(reply){
      if (document.location.href=='<?php echo NS_BASE_URL; ?>'){
        document.location.replace('<?php echo NS_BASE_URL; ?>');
      }
      else {
        _NS.alert.open('success','<?php echo $this->lang->phrase('success'); ?>','<?php echo $this->lang->phrase('page_will_refresh'); ?>',2);
        setTimeout(function(){document.location.replace(document.location.href);},2000);
      }
    }
  },1);
}
</script>