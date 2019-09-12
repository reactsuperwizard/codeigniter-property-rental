<script type="text/javascript">
var items = {
  'dataTable':{}
  ,'config':{
    'tabsList':'itemTabs'
    ,'dataTable':{
      'dataID':'table_export'
      ,'URL':'<?php echo NS_BASE_URL; ?>item/filtered'
      ,'columns':[{'sortable':false},{'name':'title','sorted':'asc'},{'sortable':false}]
      ,'parser':function(e){
        var template=document.querySelector('#itemActionTemplate').innerHTML;
        return [
          '<img class="img-responsive" src="<?php echo NS_BASE_URL; ?>'+((e.folder!='')?'uploads/'+e['folder']+'/'+e['filename']:'images/no_photo.png')+'"/>'
          ,'<a href="<?php echo NS_BASE_URL; ?>item/view/'+e['item_id']+'">'+e['title']+'</a>'
          ,template.replace(/_X_/g,e['item_id'])
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
    _NS.runRequest('POST','<?php echo NS_BASE_URL; ?>item/remove','item_id='+ID,{
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
    <ul id="itemTabs" class="nav nav-tabs bordered">
      <li class="active">
        <a href="#list" data-toggle="tab" onclick="items.load();"><i class="entypo-menu"></i> 
          <?php echo $this->lang->phrase('item_list'); ?>
        </a></li>
      <li class="edit">
        <a href="#save" data-toggle="tab">
          <span class="add"><i class="entypo-plus-circled add"></i>
          <?php echo $this->lang->phrase('add_item'); ?></span>
          <span class="edit hidden"><i class="entypo-pencil edit"></i>
          <?php echo $this->lang->phrase('edit_item'); ?></span>
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
                  <th width="80"></th>
                  <th><div><?php echo $this->lang->phrase('title');?></div></th>
                  <th><div><?php echo $this->lang->phrase('options');?></div></th>
              </tr>
          </thead>
        </table>
      </div>
      <!----TABLE LISTING ENDS--->


      <!----FORM STARTS---->
      <div class="tab-pane box" id="save" style="padding: 5px">
        <div class="box-content">
          <?php include(APPPATH.'views/item/form.php'); ?>
        </div>                
      </div>
      <!----CREATION FORM ENDS-->
      
      <div style="display:none;" id="itemActionTemplate">
        <div class="btn-group">
          <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
            Action <span class="caret"></span>
          </button>
          <ul class="dropdown-menu dropdown-default pull-right" role="menu">
            <!-- EDITING LINK -->
            <li>
              <a onclick="items.edit(_X_);">
                <i class="entypo-pencil"></i><?php echo $this->lang->phrase('edit'); ?>
              </a>
            </li>
            <li class="divider"></li>
            <!-- DELETION LINK -->
            <li>
              <a onclick="confirmRemoval('items.remove(_X_);');">
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
items.load();
</script>