<form method="POST" action="<?php echo NS_BASE_URL . 'category/save'; ?>" class="form-horizontal form-groups-bordered validate" target="_top" id="categoryForm" onsubmit="categories.save();return false;">
<fieldset>
<input type="hidden" name="category_id" value="0"/>
<div class="row padded"><div class="col-sm-12">
  <div class="form-group">
    <label class="col-sm-3 control-label"><?php echo $this->lang->phrase('title'); ?></label>
    <div class="col-sm-8">
      <input type="text" class="form-control" name="title[1]" data-validate="required"/>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-3 control-label"><?php echo $this->lang->phrase('description'); ?></label>
    <div class="col-sm-8">
      <textarea class="form-control" name="description[1]"></textarea>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-3 control-label"><?php echo $this->lang->phrase('is_active'); ?></label>
    <div class="col-sm-8">
      <input type="checkbox" value="1" name="is_active"/>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-3 control-label"><?php echo $this->lang->phrase('is_public'); ?></label>
    <div class="col-sm-8">
      <input type="checkbox" value="1" name="is_public"/>
    </div>
  </div>
  <div class="form-group">
  <div class="col-sm-offset-3 col-sm-8">
    <a class="btn btn-primary" onclick="categories.save();"><?php echo $this->lang->phrase('save_category'); ?></a>
  </div>
</div>
</div></div>
</fieldset>
</form>

<script type="text/javascript">
if (typeof(categories)=='undefined'){
  var categories={'config':{}};
}
Object.assign(categories,{
  'reset':function (){
    var _this=this, timestamp=(new Date()).getTime();
    
    if (typeof(_this.config.tabsList)!='undefined'){
      $('#'+_this.config.tabsList+' li.edit .edit').addClass('hidden');
      $('#'+_this.config.tabsList+' li.edit .add').removeClass('hidden');
    }
    _NS.DOM.enable('#categoryForm .disabled');
    _NS.resetFields('#categoryForm');
  }
  ,'edit': function (ID){
    var _this=this;
    _NS.post('<?php echo NS_BASE_URL; ?>category/edit','category_id='+ID,{
      'success':function(reply){
        _this.reset();
        _NS.fillFields('#categoryForm',reply.data);
        
        document.querySelector('#categoryForm input[name="title[1]"]').value=reply.data.title;
        document.querySelector('#categoryForm textarea[name="description[1]"]').value=reply.data.description;
        
        
        if (typeof(_this.config.tabsList)!='undefined'){
          $('#'+_this.config.tabsList+' li.edit .add').addClass('hidden');
          $('#'+_this.config.tabsList+' li.edit .edit').removeClass('hidden');        
          document.querySelectorAll('#'+_this.config.tabsList+' a')[1].click();
        }
      }
    },1);
  }
  ,'save': function (data) {
    var _this=categories, data=_NS.DOM.getFormData('#categoryForm');
    _NS.post('<?php echo base_url(); ?>category/save',data,{
      'success':function(reply){
        _this.close();
      }
    },1);
  }
  ,'close':function(){
    var _this=categories;
    if (typeof(_this.config.tabsList)!='undefined'){
      document.querySelectorAll('#'+_this.config.tabsList+' a')[0].click();
    }
    _this.reset();
  }
});


runWhenReady(function(){
//categoryMaker.init(document.querySelector('#categoryForm'));
});
</script>