<?php $tagContainer=uniqid('t'); ?>

<?php
$basePath=NS_BASE_URL;
if (!empty($oldVersion)){
  $tagTargetObjectID=1;
  $basePath=PJ_INSTALL_URL.'../rental_ci/';
?>
<?php
}

?>
<div id="<?php echo $tagContainer; ?>" class="row hidden">
  <label class="col-sm-3 title">Tags</label>
  <div class="col-sm-8">
    <div class="row">
      <div class="col-sm-6"><input class="form-control" type="text" name="term"/></div>
      <div class="col-sm-6"><a class="btn btn-info pj-button" style="display:inline-block;" onclick="tags.create();">Create</a>
      </div>
    </div>
    <div class="list">
      
    </div>
  </div>
</div>
<script type="text/javascript">
var tags={
  'config':{
    'target_object_type_id':<?php echo $tagTargetObjectID; ?>,'target_object_id':<?php echo $tagTargetID; ?>}
  ,'list':document.querySelector('#<?php echo $tagContainer; ?> .list')
  ,'init':function(){
    _NS.DOM.removeClass('#<?php echo $tagContainer; ?>','hidden');
    this.load(this.config);
  }
  ,'load':function(params){
    var _this=this;
    _NS.post('<?php echo $basePath?>tag/targets',params,{'success':function(reply){
      reply.data.forEach(function(t){
        _this.append(t.tag_id,t.code);
      });
    }},1);
  }
  ,'append':function(tagID,code){
    if (document.querySelectorAll('#tag'+tagID).length==0){
      this.list.insertAdjacentHTML('beforeend','<a id="tag'+tagID+'" class="btn btn-default pj-button">'+code+'&nbsp;&nbsp;<i class="entypo-close" onclick="tags.remove('+tagID+');">remove</i></a>');
    }
  }
  ,'create':function(){
    var _this=this, data={'code':document.querySelector('#<?php echo $tagContainer; ?> input[name="term"]').value};
    _NS.post('<?php echo $basePath; ?>tag/save',data,{'success':function(reply){
      tags.append(reply.data['tag_id'],reply.data['code']);
    }},1);
  }
  ,'attach':function(tagID,code){
    var tagID=tagID,code=code,params=Object.assign({'tag_id':tagID},this.config);
    _NS.post('<?php echo $basePath; ?>tag/attach',params,{'success':function(reply){
      tags.append(tagID,code);
      document.querySelector('#<?php echo $tagContainer; ?> input[name="term"]').value='';
    }},1);
  }
  ,'remove':function(tagID){
    var tagID=tagID,params=Object.assign({'tag_id':tagID},this.config);
    _NS.post('<?php echo $basePath; ?>tag/remove',params,{'success':function(reply){
      document.querySelector('#tag'+tagID).remove();
    }},1);
  }
};
jQuery(document).ready(function(){
jQuery('#<?php echo $tagContainer; ?> input[name="term"]').autocomplete({
    source: function( request, response ) {
      var request=request,response=response;
      _NS.post('<?php echo $basePath; ?>tag/autocomplete',request,{
        'success':function(reply){
          response(reply.data);
        }
      },1);
    },
    minLength: 2,
    select: function( event, ui ) {
      console.log( "Selected: " + ui.item.value + " aka " + ui.item.id );
    }
  })
  .autocomplete( "instance" )._renderItem = function( ul, item ) {
    return jQuery( '<li style="background-color:#FFFFFF;">' )
      .append( '<div>'+item.code+'<a class="btn btn-default" onclick="tags.attach('+item.tag_id+',\''+item.code+'\');"><?php //echo get_phrase('append'); ?>Append</a></div>')
      .appendTo( ul );
  };
tags.init();
});

</script>