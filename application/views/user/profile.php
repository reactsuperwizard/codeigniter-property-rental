<form method="POST" action="<?php echo NS_BASE_URL . 'user/save'; ?>" class="form-horizontal form-groups-bordered validate" target="_top" id="userForm" onsubmit="users.save();return false;">
  <fieldset>
    <input type="hidden" name="user_id" value="0"/>
    <div class="padded">
      <div class="form-group">
        <label class="col-sm-3 control-label"><?php echo $this->lang->phrase('email'); ?></label>
        <div class="col-sm-5">
          <input type="text" class="form-control" name="email" data-validate="required" data-message-required="<?php echo $this->lang->phrase('email_required'); ?>"/>
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-3 control-label"><?php echo $this->lang->phrase('password'); ?></label>
        <div class="col-sm-5">
          <input type="password" class="form-control" name="password"/>
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-3 control-label"><?php echo $this->lang->phrase('first name'); ?></label>
        <div class="col-sm-5">
          <input type="text" class="form-control" name="first_name" data-validate="required" data-message-required="<?php echo $this->lang->phrase('first_name_required'); ?>"/>
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-3 control-label"><?php echo $this->lang->phrase('last name'); ?></label>
        <div class="col-sm-5">
          <input type="text" class="form-control" name="last_name" data-validate="required" data-message-required="<?php echo $this->lang->phrase('last_name_required'); ?>"/>
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-3 control-label"><?php echo $this->lang->phrase('company_name'); ?></label>
        <div class="col-sm-5">
          <input type="text" class="form-control" name="company_name"/>
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-3 control-label"><?php echo $this->lang->phrase('phone'); ?></label>
        <div class="col-sm-5">
          <input type="text" class="form-control" name="phone"/>
        </div>
      </div>

      
      <div class="form-group">
        <label class="col-sm-3 control-label"><?php echo $this->lang->phrase('line_1'); ?></label>
        <div class="col-sm-5">
          <input type="text" class="form-control" name="line_1"/>
        </div>
      </div>
      
      <div class="form-group">
        <label class="col-sm-3 control-label"><?php echo $this->lang->phrase('line_2'); ?></label>
        <div class="col-sm-5">
          <input type="text" class="form-control" name="line_2"/>
        </div>
      </div>
      
      <div class="form-group">
        <label class="col-sm-3 control-label"><?php echo $this->lang->phrase('city'); ?></label>
        <div class="col-sm-5">
          <input type="text" class="form-control" name="city"/>
        </div>
      </div>
      
      <div class="form-group">
        <label class="col-sm-3 control-label"><?php echo $this->lang->phrase('state'); ?></label>
        <div class="col-sm-5">
          <input type="text" class="form-control" name="state"/>
        </div>
      </div>
      
      <div class="form-group">
        <label class="col-sm-3 control-label"><?php echo $this->lang->phrase('postcode'); ?></label>
        <div class="col-sm-5">
          <input type="text" class="form-control" name="postcode"/>
        </div>
      </div>

    </div>
    <div class="form-group">
      <div class="col-sm-offset-3 col-sm-5">
        <a class="btn btn-primary" onclick="NS_Rental.user.save();"><?php echo $this->lang->phrase('save'); ?></a>
      </div>
    </div>
  </fieldset>
</form>