<script type="text/javascript">
var itemLocks = {
  'dataTable':{}
  ,'config':{
    'tabsList':'itemLockTabs'
    ,'dataTable':{
      'dataID':'table_export'
      ,'URL':'<?php echo NS_BASE_URL; ?>item_lock/filtered'
      ,'columns':[{'sortable':false},{'sortable':false},{'sortable':false},{'sortable':false}]
      ,'parser':function(e){
        return [
          e['title'],e['description'],e['dates'],e['quantity']
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
    _NS.runRequest('POST','<?php echo NS_BASE_URL; ?>item_lock/remove','item_lock_id='+ID,{
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
    <ul id="itemLockTabs" class="nav nav-tabs bordered">
      <li class="active">
        <a href="#list" data-toggle="tab" onclick="itemLocks.load();"><i class="entypo-menu"></i> 
          <?php echo $this->lang->phrase('item_lock_list'); ?>
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
                  <th><div><?php echo $this->lang->phrase('Title');?></div></th>
        <th><div><?php echo $this->lang->phrase('Description');?></div></th>
        <th><div><?php echo $this->lang->phrase('Dates');?></div></th>
        <th><div><?php echo $this->lang->phrase('Quantity');?></div></th>
              </tr>
          </thead>
        </table>
      </div>
      <!----TABLE LISTING ENDS--->

    </div>
  </div>
</div>
<!-----  DATA TABLE EXPORT CONFIGURATIONS ---->                      
<script type="text/javascript">
itemLocks.load();
</script>