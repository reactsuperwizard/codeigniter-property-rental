<script type="text/javascript">
var tags={
  'configs':{}
  ,'init':function(config,data){
    var config=config,data=data,containerObject={},timestamp=(new Date()).getTime(),tagSearchID='tagSearch'+timestamp;
    switch(typeof(config.container)){
      case 'string':
        containerObject=document.querySelector(config.container);
      break;
      case 'object':
        containerObject=config.container;
      break;
    }
    if (typeof(config['prefixStart'])=='undefined' || typeof(config['prefixEnd'])=='undefined'){
      config['prefixStart']='';
      config['prefixEnd']='';
    }
    containerObject.innerHTML='<div class="tagSearch row"><div class="col-sm-8"><input id="'+tagSearchID+'" type="text" name="term" value="" class="form-control"/></div><div class="col-sm-4"><a class="btn btn-warning" onclick="tags.create(\''+timestamp+'\');"><?php echo $this->lang->phrase('create'); ?></a> </div></div><div class="tagList"></div>';
    
    tags.configs[timestamp]=Object.assign(config,{'container':containerObject});
    
    setTimeout(function(tagSearchID,timestamp){
      var tagSearchID=tagSearchID,timestamp=timestamp;
    jQuery('#'+tagSearchID).autocomplete({
      source: function( request, response ) {
        var request=request,response=response;
        _NS.post('<?php echo NS_BASE_URL; ?>tag/autocomplete',request,{
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
        .append( '<div>'+item.code+'<a class="btn btn-default" onclick="tags.append(\''+timestamp+'\','+item.tag_id+',\''+item.code+'\');"><?php echo $this->lang->phrase('append'); ?></a></div>')
        .appendTo( ul );
    };
    },100,tagSearchID,timestamp);
    
    if (typeof(data)=='object'){
      data.forEach(function(t){
        tags.append(timestamp,t['tag_id'],t['code']);
      });
    }
    
  }
  ,'append':function(configID,tagID,code){
    var configID=configID,tagID=tagID,code=code,config=tags.configs[configID];
    
    config['container'].querySelector('.tagList').insertAdjacentHTML('beforeend','<div class="tag'+tagID+' btn btn-default" onclick="tags.toggle(\''+configID+'\','+tagID+');"><input type="checkbox" value="'+tagID+'" name="'+config['prefixStart']+'tags'+config['prefixEnd']+'[]" checked="true"/> '+code+'</div>');
  }
  ,'toggle':function(configID,tagID){
    var checkBox=tags.configs[configID]['container'].querySelector('.tagList > .tag'+tagID+' input[type="checkbox"]');
    if (checkBox.checked){
      checkBox.checked=false;
    }
    else {
      checkBox.checked=true;
    }
  }
  ,'create':function(configID){
    var _this=this, configID=configID, data={'code':this.configs[configID]['container'].querySelector('#tagSearch'+configID).value};
    _NS.post('<?php echo NS_BASE_URL; ?>tag/save',data,{'success':function(reply){
      tags.append(configID,reply.data['tag_id'],reply.data['code']);
    }},1);
  }
  ,'remove':function(tagID){
    var tagID=tagID,params=Object.assign({'tag_id':tagID},this.config);
    _NS.post('<?php echo NS_BASE_URL; ?>tag/remove',params,{'success':function(reply){
      document.querySelector('#tag'+tagID).remove();
    }},1);
  }
};
</script>