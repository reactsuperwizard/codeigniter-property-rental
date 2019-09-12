<div id="statuses" class="row">
  <div class="col-md-12">

    <!------CONTROL TABS START------>
    <ul class="nav nav-tabs bordered">
      <li class="active">
        <a href="#list" data-toggle="tab" onclick="statuses.load();"><i class="entypo-menu"></i> 
          <?php echo $this->lang->phrase('status_list'); ?>
        </a></li>
      <li class="edit">
        <a href="#save" data-toggle="tab">
          <span class="add"><i class="entypo-plus-circled add"></i>
          <?php echo $this->lang->phrase('add_status'); ?></span>
          <span class="edit hidden"><i class="entypo-pencil edit"></i>
          <?php echo $this->lang->phrase('edit_status'); ?></span>
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
              <th><div><?php echo $this->lang->phrase('object'); ?></div></th>
              <th><div><?php echo $this->lang->phrase('code'); ?></div></th>
              <th><div><?php echo $this->lang->phrase('name'); ?></div></th>
              <?php /**/ ?><th><div><?php echo $this->lang->phrase('options'); ?></div></th><?php /**/ ?>
            </tr>
          </thead>
        </table>
      </div>
      <!----TABLE LISTING ENDS--->


      <!----FORM STARTS---->
      <div class="tab-pane box" id="save" style="padding: 5px">
        <div class="box-content">
          <form method="POST" action="<?php echo NS_BASE_URL . 'course/save'; ?>" class="form-horizontal form-groups-bordered validate" target="_top" id="statusForm" onsubmit="statuses.save();return false;">
          <fieldset>
          <input type="hidden" name="status_id" value="0"/>
          <div class="padded">
            <div class="form-group">
              <label class="col-sm-3 control-label"><?php echo $this->lang->phrase('object'); ?></label>
              <div class="col-sm-5">
                <select class="form-control" name="target_object_type_id"><option><?php echo $this->lang->phrase('choose_one'); foreach ($targetObjectTypes AS $tot){ echo '</option><option value="'.$tot['target_object_type_id'].'">('.$tot['code'].') '.$tot['name']; } ?></option></select>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-3 control-label"><?php echo $this->lang->phrase('code'); ?></label>
              <div class="col-sm-5">
                <input type="text" class="form-control" name="code" data-validate="required" data-message-required="<?php echo $this->lang->phrase('value_required'); ?>"/>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-3 control-label"><?php echo $this->lang->phrase('name'); ?></label>
              <div class="col-sm-5">
                <input type="text" class="form-control" name="name" data-validate="required" data-message-required="<?php echo $this->lang->phrase('value_required'); ?>"/>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-3 control-label"><?php echo $this->lang->phrase('description'); ?></label>
              <div class="col-sm-5">
                <textarea class="form-control" name="description"></textarea>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-offset-3 col-sm-5">
              <a class="btn btn-info" onclick="statuses.save();"><?php echo $this->lang->phrase('save_status'); ?></a>
            </div>
          </div>
          </fieldset>
          </form>
          
        </div>                
      </div>
      <!----CREATION FORM ENDS-->
      
      <div style="display:none;" id="statusActionTemplate">
        <div class="btn-group">
          <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
            Action <span class="caret"></span>
          </button>
          <ul class="dropdown-menu dropdown-default pull-right" role="menu">
            <!-- EDITING LINK -->
            <li>
              <a onclick="statuses.edit(_X_);">
                <i class="entypo-pencil"></i><?php echo $this->lang->phrase('edit'); ?>
              </a>
            </li>
            <li class="divider"></li>
            <!-- DELETION LINK -->
            <li>
              <a onclick="confirmRemoval('statuses.remove(_X_);');">
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
var statuses = {
  'dataTable':{}
  ,'load': function (clickNeeded) {
    var _this=this;
    _this.reset();
    
    if (clickNeeded){
      document.querySelectorAll('ul.nav-tabs a')[0].click();
    }
    if (document.querySelector('#table_export').dataset['datatable']!='true'){
      statuses.dataTable=jQuery('#table_export').DataTable({
        "processing": true
        ,"serverSide": true
        ,'columns':[{'sortable':false},{'sortable':false},{'sortable':false},{'sortable':false}]
        ,'ajax':function (data, callback, settings) {
          var callback=callback,data=data,template=document.querySelector('#statusActionTemplate').innerHTML;
          _NS.post('<?php echo base_url(); ?>status/filtered',JSON.stringify(data),{
            'success':function(reply){
              var e=0,finalData={'data':[]};
              finalData['recordsTotal']=reply.data['total'];
              finalData['recordsFiltered']=reply.data['filtered'];
              reply.data['entries'].forEach(function(e){
                finalData.data.push(['('+e.object_code+') '+e.object_name,e.code,e.name,template.replace(/_X_/g,e.status_id)]);
              });/** /
              for (e=0;e<reply.data['entries'].length;e++){
                finalData.data.push([reply.data['entries'][e]['status_id'],reply.data['entries'][e]['name'],template.replace(/_X_/g,reply.data['entries'][e]['status_id'])]);
              }/**/
              callback(finalData);
            }
          },1);
        }
      });
      document.querySelector('#table_export').dataset['datatable']='true';
    }
    else {
      statuses.dataTable.ajax.reload();
    }
  }
  ,'reset':function (){
    $('ul.nav-tabs li.edit .edit').addClass('hidden');
    $('ul.nav-tabs li.edit .add').removeClass('hidden');
    document.querySelectorAll('#statusForm .disabled').forEach(function(el){
      el.className=el.className.replace(' disabled ','');
      el.disabled=false;
    });
    _NS.resetFields('#statusForm');
  }
  ,'edit': function (ID){
    _this=this;
    _NS.runRequest('POST','<?php echo base_url(); ?>status/edit','status_id='+ID,{
      'success':function(reply){
        _NS.fillFields('#statusForm',reply.data);
        $('ul.nav-tabs li.edit .add').addClass('hidden');
        $('ul.nav-tabs li.edit .edit').removeClass('hidden');
        document.querySelector('#statusForm input[name="code"]').disabled=true;
        document.querySelector('#statusForm input[name="code"]').className+=' disabled ';
        document.querySelectorAll('ul.nav-tabs a')[1].click();
      }
    },1);
  }
  , 'save': function () {
    var _this=this,params=_NS.getFormData('#statusForm');
    _NS.post('<?php echo base_url(); ?>status/save',JSON.stringify(params),{
      'success':function(reply){
        _this.dataTable.ajax.reload();
        _this.load(true)
      }
    },1);
  }
  ,'remove':function(ID){
    _this=this;
    _NS.runRequest('POST','<?php echo base_url(); ?>status/remove','status_id='+ID,{
      'success':function(reply){
        jQuery('#removalConfirmation').modal('hide');
        _this.dataTable.ajax.reload();
      }
    },1);
  }
};

statuses.load();

</script>