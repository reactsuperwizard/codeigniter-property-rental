<div id="venues" class="row">
  <div class="col-md-12">

    <!------CONTROL TABS START------>
    <ul class="nav nav-tabs bordered">
      <li class="active">
        <a href="#list" data-toggle="tab" onclick="venues.load();"><i class="entypo-menu"></i> 
          <?php echo $this->lang->phrase('venue_list'); ?>
        </a></li>
      <li class="edit">
        <a href="#save" data-toggle="tab">
          <span class="add"><i class="entypo-plus-circled add"></i>
          <?php echo $this->lang->phrase('add_venue'); ?></span>
          <span class="edit hidden"><i class="entypo-pencil edit"></i>
          <?php echo $this->lang->phrase('edit_venue'); ?></span>
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
              <th><div><?php echo $this->lang->phrase('city'); ?></div></th>
              <th><div><?php echo $this->lang->phrase('state'); ?></div></th>
              <?php /**/ ?><th><div><?php echo $this->lang->phrase('options'); ?></div></th><?php /**/ ?>
            </tr>
          </thead>
        </table>
      </div>
      <!----TABLE LISTING ENDS--->


      <!----FORM STARTS---->
      <div class="tab-pane box" id="save" style="padding: 5px">
        <div class="box-content">
          <form method="POST" action="<?php echo NS_BASE_URL . 'venue/save'; ?>" class="form-horizontal form-groups-bordered validate" target="_top" id="venueForm" onsubmit="venues.save();return false;">
          <fieldset>
          <input type="hidden" name="venue_id" value="0"/>
          <div class="padded">
            <div class="form-group">
              <label class="col-sm-3 control-label"><?php echo $this->lang->phrase('name'); ?></label>
              <div class="col-sm-5">
                <input type="text" class="form-control" name="name" data-validate="required" data-message-required="<?php echo $this->lang->phrase('required'); ?>"/>
              </div>
            </div>
            <input type="hidden" name="address[address_id]" value="0"/>
            <div class="form-group">
              <label class="col-sm-3 control-label"><?php echo $this->lang->phrase('line_1'); ?></label>
              <div class="col-sm-5">
                <input type="text" class="form-control" name="address[line_1]" data-validate="required" data-message-required="<?php echo $this->lang->phrase('required'); ?>"/>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-3 control-label"><?php echo $this->lang->phrase('line_2'); ?></label>
              <div class="col-sm-5">
                <input type="text" class="form-control" name="address[line_2]"/>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-3 control-label"><?php echo $this->lang->phrase('city'); ?></label>
              <div class="col-sm-5">
                <input type="text" class="form-control" name="address[city]" data-validate="required" data-message-required="<?php echo $this->lang->phrase('required'); ?>"/>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-3 control-label"><?php echo $this->lang->phrase('state'); ?></label>
              <div class="col-sm-5">
                <input type="text" class="form-control" name="address[state]" data-validate="required" data-message-required="<?php echo $this->lang->phrase('required'); ?>"/>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-3 control-label"><?php echo $this->lang->phrase('postcode'); ?></label>
              <div class="col-sm-5">
                <input type="text" class="form-control" name="address[postcode]" data-validate="required" data-message-required="<?php echo $this->lang->phrase('required'); ?>"/>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-3 control-label"><?php echo $this->lang->phrase('contact_name'); ?></label>
              <div class="col-sm-5">
                <input type="text" class="form-control" name="contact_name"/>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-3 control-label"><?php echo $this->lang->phrase('contact_email'); ?></label>
              <div class="col-sm-5">
                <input type="text" class="form-control" name="contact_email"/>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-3 control-label"><?php echo $this->lang->phrase('contact_phone'); ?></label>
              <div class="col-sm-5">
                <input type="text" class="form-control" name="contact_phone"/>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-3 control-label"><?php echo $this->lang->phrase('status'); ?></label>
              <div class="col-sm-5">
                <select class="form-control" name="status_id"><option><?php echo $this->lang->phrase('choose_one'); foreach ($this->reply['config']['statuses'] AS $s){ echo '</option><option value="'.$s['status_id'].'">'.$s['name']; } ?></option></select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-offset-3 col-sm-5">
              <a class="btn btn-info" onclick="venues.save();"><?php echo $this->lang->phrase('save_venue'); ?></a>
            </div>
          </div>
          </fieldset>
          </form>
          
        </div>                
      </div>
      <!----CREATION FORM ENDS-->
      
      <div style="display:none;" id="venueActionTemplate">
        <div class="btn-group">
          <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
            Action <span class="caret"></span>
          </button>
          <ul class="dropdown-menu dropdown-default pull-right" role="menu">
            <!-- EDITING LINK -->
            <li>
              <a onclick="venues.edit(_X_);">
                <i class="entypo-pencil"></i><?php echo $this->lang->phrase('edit'); ?>
              </a>
            </li>
            <li class="divider"></li>
            <!-- DELETION LINK -->
            <li>
              <a onclick="confirmRemoval('venues.remove(_X_);');">
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
var venues = {
  'dataTable':{}
  ,'load': function (clickNeeded) {
    var _this=this;
    _this.reset();
    /** /
    if (clickNeeded){
      document.querySelectorAll('ul.nav-tabs a')[0].click();
    }/**/
    if (document.querySelector('#table_export').dataset['datatable']!='true'){
      venues.dataTable=jQuery('#table_export').DataTable({
        "processing": true
        ,"serverSide": true
        ,'columns':[{'sortable':false},{'sortable':false},{'sortable':false},{'sortable':false}]
        ,'ajax':function (data, callback, settings) {
          var callback=callback,data=data,template=document.querySelector('#venueActionTemplate').innerHTML;
          _NS.post('<?php echo NS_BASE_URL; ?>venue/filtered',JSON.stringify(data),{
            'success':function(reply){
              var e=0,finalData={'data':[]};
              finalData['recordsTotal']=reply.data['total'];
              finalData['recordsFiltered']=reply.data['filtered'];
              reply.data['entries'].forEach(function(e){
                finalData.data.push([e.name,e.city,e.state,template.replace(/_X_/g,e.venue_id)]);
              });
              callback(finalData);
              console.log(JSON.stringify(reply.queries,null,2));
            }
          },1);
        }
      });
      document.querySelector('#table_export').dataset['datatable']='true';
    }
    else {
      venues.dataTable.ajax.reload();
    }/**/
  }
  ,'reset':function (){
    $('ul.nav-tabs li.edit .edit').addClass('hidden');
    $('ul.nav-tabs li.edit .add').removeClass('hidden');
    _NS.DOM.enable('#venueForm .disabled');
    _NS.resetFields('#venueForm');
  }
  ,'edit': function (ID){
    _this=this;
    _NS.runRequest('POST','<?php echo base_url(); ?>venue/edit','venue_id='+ID,{
      'success':function(reply){
        _NS.DOM.fillFields('#venueForm',reply.data);
        _NS.DOM.fillFields('#venueForm',reply.data.address,'address');
        $('ul.nav-tabs li.edit .add').addClass('hidden');
        $('ul.nav-tabs li.edit .edit').removeClass('hidden');
        document.querySelectorAll('ul.nav-tabs a')[1].click();
      }
    },1);
  }
  , 'save': function () {
    var _this=this,params=_NS.getFormData('#venueForm');
    _NS.post('<?php echo base_url(); ?>venue/save',JSON.stringify(params),{
      'success':function(reply){
        _this.dataTable.ajax.reload();
        _this.load(true)
      }
    },1);
  }
  ,'remove':function(ID){
    _this=this;
    _NS.runRequest('POST','<?php echo base_url(); ?>venue/remove','venue_id='+ID,{
      'success':function(reply){
        jQuery('#removalConfirmation').modal('hide');
        _this.dataTable.ajax.reload();
      }
    },1);
  }
};

venues.load();

</script>