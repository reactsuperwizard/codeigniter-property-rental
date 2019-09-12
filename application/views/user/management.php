<div id="users" class="row">
  <div class="col-md-12">

    <!------CONTROL TABS START------>
    <ul class="nav nav-tabs bordered">
      <li class="active">
        <a href="#list" data-toggle="tab" onclick="users.load();"><i class="entypo-menu"></i> 
          <?php echo $this->lang->phrase('user_list'); ?>
        </a></li>
      <li class="edit">
        <a href="#save" data-toggle="tab">
          <span class="add"><i class="entypo-plus-circled add"></i>
          <?php echo $this->lang->phrase('add_user'); ?></span>
          <span class="edit hidden"><i class="entypo-pencil edit"></i>
          <?php echo $this->lang->phrase('edit_user'); ?></span>
        </a></li>
    </ul>
    <!------CONTROL TABS END------>

    <div class="tab-content">
      <br>
      <!----TABLE LISTING STARTS-->
      <div class="tab-pane box active" id="list">

        <table class="table table-bordered datatable" id="table_export">
          <thead>
            <tr>
              <th><div><?php echo $this->lang->phrase('name'); ?></div></th>
              <th><div><?php echo $this->lang->phrase('email'); ?></div></th>
              <th><div><?php echo $this->lang->phrase('registration_date'); ?></div></th>
              <?php /**/ ?><th><div><?php echo $this->lang->phrase('role'); ?></div></th><?php /**/ ?>
              <?php /**/ ?><th><div><?php echo $this->lang->phrase('options'); ?></div></th><?php /**/ ?>
            </tr>
          </thead>
        </table>
      </div>
      <!----TABLE LISTING ENDS--->


      <!----FORM STARTS---->
      <div class="tab-pane box" id="save" style="padding: 5px">
        <div class="box-content">
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
              <label class="col-sm-3 control-label"><?php echo $this->lang->phrase('role'); ?></label>
              <div class="col-sm-5">
                <select class="form-control" name="role_id"><option><?php echo $this->lang->phrase('choose_one'); foreach ($this->reply['config']['roles'] AS $r){ echo '</option><option value="'.$r['role_id'].'">'.$r['code']; } ?></option></select>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-3 control-label"><?php echo $this->lang->phrase('status'); ?></label>
              <div class="col-sm-5">
                <select class="form-control" name="status_id"><option><?php echo $this->lang->phrase('choose_one'); foreach ($this->reply['config']['statuses'] AS $s){ echo '</option><option value="'.$s['status_id'].'">'.$s['name']; } ?></option></select>
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
              <a class="btn btn-info" onclick="users.save();"><?php echo $this->lang->phrase('save_user'); ?></a>
            </div>
          </div>
          </fieldset>
          </form>
          
        </div>                
      </div>
      <!----CREATION FORM ENDS-->
      
      <div style="display:none;" id="userActionTemplate">
        <div class="btn-group">
          <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
            Action <span class="caret"></span>
          </button>
          <ul class="dropdown-menu dropdown-default pull-right" role="menu">
            <!-- EDITING LINK -->
            <li>
              <a onclick="users.edit(_X_);">
                <i class="entypo-pencil"></i><?php echo $this->lang->phrase('edit'); ?>
              </a>
            </li>
            <li class="divider"></li>
            <!-- DELETION LINK -->
            <li>
              <a onclick="confirmRemoval('users.remove(_X_);');">
                <i class="entypo-trash"></i><?php echo $this->lang->phrase('delete'); ?>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>



<!-----  DATA TABLE EXPORT CONFIGURATIONS ---->                      
<script type="text/javascript">
var users = {
  'dataTable':{}
  ,'load': function (clickNeeded) {
    var _this=this;
    _this.reset();
    
    if (clickNeeded){
      document.querySelectorAll('ul.nav-tabs a')[0].click();
    }
    if (document.querySelector('#table_export').dataset['datatable']!='true'){
      users.dataTable=jQuery('#table_export').DataTable({
        "processing": true
        ,"serverSide": true
        ,'columns':[{'sortable':false},{'sortable':false},{'sortable':false},{'sortable':false},{'sortable':false}]
        ,'ajax':function (data, callback, settings) {
          var callback=callback,data=data,template=document.querySelector('#userActionTemplate').innerHTML;
          _NS.post('<?php echo base_url(); ?>user/filtered',JSON.stringify(data),{
            'success':function(reply){
              var e=0,finalData={'data':[]};
              finalData['recordsTotal']=reply.data['total'];
              finalData['recordsFiltered']=reply.data['filtered'];
              reply.data['entries'].forEach(function(e){
                finalData.data.push([e.name,e.email,(new Date(e.creation_timestamp*1000)),e.role,template.replace(/_X_/g,e.user_id)]);
              });
              callback(finalData);
            }
          },1);
        }
      });
      document.querySelector('#table_export').dataset['datatable']='true';
    }
    else {
      users.dataTable.ajax.reload();
    }
  }
  ,'reset':function (){
    $('ul.nav-tabs li.edit .edit').addClass('hidden');
    $('ul.nav-tabs li.edit .add').removeClass('hidden');
    document.querySelectorAll('#userForm .disabled').forEach(function(el){
      el.className=el.className.replace(' disabled ','');
      el.disabled=false;
    });
    _NS.resetFields('#userForm');
  }
  ,'edit': function (ID){
    var _this=this;
    _NS.runRequest('POST','<?php echo base_url(); ?>user/edit_relation','user_id='+ID,{
      'success':function(reply){
        _NS.fillFields('#userForm',reply.data);
        _NS.DOM.disable('#userForm input[name="email"]');
        
        _NS.DOM.disable('#userForm select[name="role_id"]');
        $('ul.nav-tabs li.edit .add').addClass('hidden');
        $('ul.nav-tabs li.edit .edit').removeClass('hidden');
        document.querySelectorAll('ul.nav-tabs a')[1].click();
      }
    },1);
  }
  , 'save': function () {
    var _this=this,params=_NS.getFormData('#userForm');
    _NS.post('<?php echo base_url(); ?>user/save_relation',JSON.stringify(params),{
      'success':function(reply){
        _this.dataTable.ajax.reload();
        _this.load(true)
      }
    },1);
  }
  ,'remove':function(ID){
    _this=this;
    _NS.runRequest('POST','<?php echo base_url(); ?>user/remove','user_id='+ID,{
      'success':function(reply){
        jQuery('#removalConfirmation').modal('hide');
        _this.dataTable.ajax.reload();
      }
    },1);
  }
};

users.load();

</script>