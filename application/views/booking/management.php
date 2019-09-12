<script type="text/javascript">
var bookings = {
  'dataTable':{}
  ,'config':{
    'tabsList':'bookingTabs'
    ,'dataTable':{
      'dataID':'table_export'
      ,'URL':'<?php echo NS_BASE_URL; ?>booking/filtered'
      ,'columns':[{'name':'code','sorted':'asc'},{'sortable':false},{'sortable':false},{'sortable':false}]
      ,'parser':function(e,config){
        var template=document.querySelector('#bookingActionTemplate').innerHTML
        ,replacements={
          '_X_':e['booking_id']
          ,'_CODE_':e['code']
          ,'CSS_editAction':''
          ,'TRIGGER_moveBack':'hidden'
        },replacementKey='';
        if ((e['rent_period_start_timestamp']-config['minimalRentTimestampPrefix'])*1000<(new Date()).getTime()){
          //replacements['CSS_editAction']='hidden';
        }
        if (e['status_id']==<?php echo get_instance()->getStatusOption('booking','moving'); ?>){
          replacements['TRIGGER_moveBack']='';
        }
        for (replacementKey in replacements){
          template=template.replace((new RegExp(replacementKey,'g')),replacements[replacementKey]);
        }
        
        console.log('parsing booking '+e['code']+' '+config['minimalRentTimestampPrefix']);
        return [
          e['code'],e['rent_period'],e.status,template
        ];
      }
      ,'parserConfigs':function(reply){
        return {
          'minimalRentTimestampPrefix':reply.config['minimalRentTimestampPrefix']
        };
      }
    }
  }
  ,'load': function () {
    var _this=this;
    
    if (typeof(_this.reset)!='undefined'){
      _this.reset();
    }
    if (document.querySelector('#'+_this.config.dataTable.dataID).dataset['datatable']!='true'){
      _this.dataTable=_NS.jQuery.dataTable(_this.config.dataTable);
      document.querySelector('#'+_this.config.dataTable.dataID).dataset['datatable']='true';
    }
    else {
      _this.dataTable.ajax.reload();
    }
  }
  ,'remove':function(ID){
    _this=this;
    _NS.runRequest('POST','<?php echo NS_BASE_URL; ?>booking/remove','booking_id='+ID,{
      'success':function(reply){
        document.querySelectorAll('ul.nav-tabs a')[0].click();
      }
    },1);
  }
  ,'moveBack':function(bookingID){
    var _this=this;
    _NS.post('<?php echo NS_BASE_URL; ?>booking/removeInvalid',{'booking_id':bookingID},{'success':function(reply){
      _this.load();
    }},1);
  }
};
</script>
<div class="row">
  <div class="col-md-12">

    <!------CONTROL TABS START------>
    <ul id="bookingTabs" class="nav nav-tabs bordered">
      <li class="active">
        <a href="#list" data-toggle="tab" onclick="bookings.load();"><i class="entypo-menu"></i> 
          <?php echo $this->lang->phrase('booking_list'); ?>
        </a></li>
      <li class="edit">
        <a href="#save" data-toggle="tab">
          <span class="add"><i class="entypo-plus-circled add"></i>
          <?php echo $this->lang->phrase('add_booking'); ?></span>
          <span class="edit hidden"><i class="entypo-pencil edit"></i>
          <?php echo $this->lang->phrase('edit_booking'); ?></span>
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
                  <th><div><?php echo $this->lang->phrase('code');?></div></th>
        <th><div><?php echo $this->lang->phrase('rent_period'); ?></div></th>
        <th><div><?php echo $this->lang->phrase('status');?></div></th>
                  <th><div><?php echo $this->lang->phrase('options');?></div></th>
              </tr>
          </thead>
        </table>
      </div>
      <!----TABLE LISTING ENDS--->


      <!----FORM STARTS---->
      <div class="tab-pane box" id="save" style="padding: 5px">
        <div class="box-content">
          <?php include(APPPATH.'views/booking/form.php'); ?>
        </div>                
      </div>
      <!----CREATION FORM ENDS-->
      
      <div style="display:none;" id="bookingActionTemplate">
        <div class="btn-group">
          <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
            Action <span class="caret"></span>
          </button>
          <ul class="dropdown-menu dropdown-default pull-right" role="menu">
            <li><b># _X_</b></li>
            <li class="TRIGGER_moveBack"><a onclick="bookings.moveBack('_X_');">move back</a></li>
            <!-- EDITING LINK -->
            <li class="CSS_editAction">
              <a onclick="bookings.edit(_X_);">
                <i class="entypo-pencil"></i><?php echo $this->lang->phrase('edit'); ?>
              </a>
            </li><li class="divider"></li>
            <li>
              <a href="<?php echo NS_BASE_URL.'booking/'; ?>_CODE_" target="_blank">
                <i class="entypo-pencil"></i><?php echo $this->lang->phrase('view'); ?>
              </a>
            </li>
            
            <!-- DELETION LINK -->
            <li>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
<!-----  DATA TABLE EXPORT CONFIGURATIONS ---->                      
<script type="text/javascript">
bookings.load();
</script>