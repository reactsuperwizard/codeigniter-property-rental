<div class="body login hidden" data-section="login">
<form id="loginForm">
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
        <a class="btn btn-primary" onclick="NS_Rental.user.login();"><?php echo $this->lang->phrase('login'); ?></a>
      </div>
    </div>
</form>
</div>
<div class="body user hidden" data-section="user">
  <?php require_once(VIEWPATH.'user/profile.php'); ?>
</div>
<script type="text/javascript">

NS_Rental['user']={
  'ID':<?php echo (!empty($this->reply['session']['userID']))?$this->reply['session']['userID']:0; ?>
  ,'prepareLogin':function (){
    var _this=this,_that=NS_Rental;
    _that.loadSection('login');
  }
  ,'login':function(){
    var _this=this,_that=NS_Rental;
    _NS.post('<?php echo NS_BASE_URL; ?>user/login',_NS.getFormData('#loginForm'),{
      'success':function(reply){
        _this.ID=reply.session.userID;
        
        
        _this.loadHeader();
        _that.init();
        //_this.profile();        
        /** /
        if (document.location.href=='<?php echo NS_BASE_URL; ?>'){
          document.location.replace('<?php echo NS_BASE_URL; ?>');
        }
        else {
          _NS.alert.open('success','<?php echo $this->lang->phrase('success'); ?>','<?php echo $this->lang->phrase('page_will_refresh'); ?>',2);
          setTimeout(function(){document.location.replace(document.location.href);},2000);
        }/**/
      }
    },1);
  }
  ,'profile':function(){
    var _this=this,_that=NS_Rental;
  //For Test    
    // _NS.runRequest('POST','<?php echo base_url(); ?>user/edit',{},{
    //   'success':function(reply){
    //     _NS.fillFields('#userForm',reply.data);
    //     _that.loadSection('user');
    //   }
    // },1);
  //END TEST
    _NS.runRequest('POST','<?php echo base_url(); ?>user/edit_relation',{},{
      'success':function(reply){
        _NS.fillFields('#userForm',reply.data);
        _that.loadSection('user');
      }
    },1);
  }
  ,'save':function(){
    var _this=this,_that=NS_Rental;
    // _NS.post('<?php echo base_url(); ?>user/save',_NS.DOM.getFormData('#NS_Rental #userForm'),{
    // },1);
    _NS.post('<?php echo base_url(); ?>user/save_relation',_NS.DOM.getFormData('#NS_Rental #userForm'),{
    },1);
  }
  ,'loadHeader':function(){
    var _this=this, _that=NS_Rental
    ,headerLabels={
      'bookings':['active','previous']
      ,'quotes':['active','previous']
    },headerType='';
    if (_this.ID>0){
      _NS.post('<?php echo NS_BASE_URL; ?>customer/header',{},{'success':function(reply){
        document.querySelector('#NS_Rental > .header .profile').innerHTML=reply.data['name'];
        for (headerType in headerLabels){
          console.log('headerLabels: '+headerType);
          headerLabels[headerType].forEach(function(l){
            document.querySelector('#NS_Rental > .header .'+headerType+' .'+l).innerHTML=reply.data[headerType][l];
          });
        }/** /
        ['quotes','bookings'].forEach(function(mode){
          document.querySelector('#NS_Rental > .header .'+mode+' .active').innerHTML=reply.data[mode]['active'];
          document.querySelector('#NS_Rental > .header .'+mode+' .closed').innerHTML=reply.data[mode]['closed'];
        });/**/
        _NS.DOM.addClass('#NS_Rental > .header .login','hidden');
        _NS.DOM.removeClass('#NS_Rental > .header .authorized','hidden');
      }},1);
    }
    else {
      _NS.DOM.addClass('#NS_Rental > .header .authorized','hidden');
      _NS.DOM.removeClass('#NS_Rental > .header .login','hidden');
    }
  }
  ,'logout':function(){
    _NS.post('<?php echo NS_BASE_URL; ?>user/logout',{},{
      'success':function(reply){
        _NS.alert.open('success','<?php echo $this->lang->phrase('success'); ?>','<?php echo $this->lang->phrase('page_will_refresh'); ?>',2);
        setTimeout(function(){document.location.replace(document.location.href);},2000);
      }
    },1);
  }
};
</script>