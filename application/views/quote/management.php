<script type="text/javascript">
var quotes = {
  'dataTable':{}
  ,'config':{
    'tabsList':'quoteTabs'
    ,'dataTable':{
      'dataID':'table_export'
      ,'URL':'<?php echo NS_BASE_URL; ?>quote/filtered'
      ,'columns':[{'name':'name','sorted':'asc'},{'sortable':false},{'sortable':false},{'sortable':false},{'sortable':false}]
      ,'parser':function(e){
        var template=document.querySelector('#quoteActionTemplate').innerHTML
          ,maxTimestamp=e['delivery_timestamp'],currentTimestamp=(new Date).getTime();
        if (e['expiration_timestamp']>maxTimestamp){
          maxTimestamp=e['expiration_timestamp'];
        }
        return [
          e['name'],e['delivery_datetime']+' - '+e['collection_datetime'],e['expiration_datetime'],e['status']
          ,template.replace(/_X_/g,e['quote_id']).replace(/_CODE_/g,e['code'])
            //.replace(/{ACTIVE_CLASS}/,(e['status_id']!=<?php echo QUOTE_STATUS_ACTIVE; ?> || ((currentTimestamp/1000)>maxTimestamp))?'hidden':'')
        ];
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
    _NS.runRequest('POST','<?php echo NS_BASE_URL; ?>quote/remove','quote_id='+ID,{
      'success':function(reply){
        document.querySelectorAll('ul.nav-tabs a')[0].click();
      }
    },1);
  }
};
</script>
<div class="row">
  <div class="col-md-12">

    <!------CONTROL TABS START------>
    <ul id="quoteTabs" class="nav nav-tabs bordered">
      <li class="active">
        <a href="#list" data-toggle="tab" onclick="quotes.load();"><i class="entypo-menu"></i> 
          <?php echo $this->lang->phrase('quote_list'); ?>
        </a></li>
      <li class="edit">
        <a href="#save" data-toggle="tab">
          <span class="add"><i class="entypo-plus-circled add"></i>
          <?php echo $this->lang->phrase('add_quote'); ?></span>
          <span class="edit hidden"><i class="entypo-pencil edit"></i>
          <?php echo $this->lang->phrase('edit_quote'); ?></span>
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
                  <th><div><?php echo $this->lang->phrase('title');?></div></th>
        <th><div><?php echo $this->lang->phrase('delivery').' & '.$this->lang->phrase('collection'); ?></div></th>
        <th><div><?php echo $this->lang->phrase('expiration'); ?></div></th>
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
          <?php include(APPPATH.'views/quote/form.php'); ?>
        </div>                
      </div>
      <!----CREATION FORM ENDS-->
      
      <div style="display:none;" id="quoteActionTemplate">
        <div class="btn-group">
          <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
            Action <span class="caret"></span>
          </button>
          <ul class="dropdown-menu dropdown-default pull-right" role="menu">
            <li class="title"><b># _X_</b></li>
            <!-- EDITING LINK -->
            <li class="{ACTIVE_CLASS}">
              <a onclick="quotes.edit(_X_);">
                <i class="entypo-pencil"></i><?php echo $this->lang->phrase('edit'); ?>
              </a>
            </li>
            <li class="divider"></li>
            <!-- DELETION LINK -->
            <li>
              <a target="_blank" href="<?php echo NS_BASE_URL; ?>quote/_CODE_">
                <i class="glyphicon glyphicon-print"></i><?php echo $this->lang->phrase('view'); ?>
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
quotes.load();
</script>