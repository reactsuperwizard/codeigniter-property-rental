<form method="POST" action="<?php echo NS_BASE_URL . 'item/save'; ?>" class="form-horizontal form-groups-bordered validate" target="_top" id="itemForm" onsubmit="items.save();return false;">
<fieldset>
<input type="hidden" name="item_id" value="0"/>

</fieldset>
</form>
<div id="itemMakerTemplate" class="hidden">
  <div class="base">
<div class="row padded"><div class="col-sm-12">
  <div class="form-group">
    <label class="col-sm-3 control-label"><?php echo $this->lang->phrase('title'); ?></label>
    <div class="col-sm-8">
      <input type="text" class="form-control" name="_PREFIX_START_title_PREFIX_END_[1]" data-validate="required"/>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-3 control-label"><?php echo $this->lang->phrase('summary'); ?></label>
    <div class="col-sm-8">
      <textarea class="form-control" name="_PREFIX_START_summary_PREFIX_END_[1]"></textarea>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-3 control-label"><?php echo $this->lang->phrase('description'); ?></label>
    <div class="col-sm-8">
      <textarea class="form-control" name="_PREFIX_START_description_PREFIX_END_[1]"></textarea>
    </div>
  </div>
  <div class="form-group">
    <label for="field-2" class="col-sm-3 control-label"><?php echo $this->lang->phrase('gallery');?></label>
    <div class="col-sm-8">
      <div class="row itemGalleryFiles">
      </div>
      <div class="itemGalleryFileTemplate hidden">
        <div class="col-sm-4 col-xs-6 file_X_">
          <div class="thumbnail">
          <a href="_FILE_URL_" target="_blank">
          <img class="img-responsive center-block" src="_THUMBNAIL_"/>
          </a>

          <a class="btn btn-danger" onclick="items.removeGalleryFile(_X_);"><?php echo $this->lang->phrase('remove'); ?></a><input class="hidden" type="text" name="_PREFIX_START_gallery_files_PREFIX_END_[]" value="_X_"/>
          <input type="hidden" class="file_X_" name="_PREFIX_START_removed_files_PREFIX_END_[]" value="0"/>
          </div>
        </div>
      </div>
      <input type="file" class="hidden" id="<?php $galleryFile=uniqid('f'); echo $galleryFile; ?>__MAKER_ID_" onchange="itemMaker.uploadGalleryFile(_MAKER_ID_);"  name="_PREFIX_START_gallery_file_PREFIX_END_" data-hash=""/>
      <input type="hidden" id="itemGalleryFileHash__MAKER_ID_" name="_PREFIX_START_gallery_file_hash_PREFIX_END_" value=""/>
      <a class="btn btn-default" onclick="document.querySelector('#<?php echo $galleryFile; ?>__MAKER_ID_').click();"><?php echo $this->lang->phrase('upload'); ?></a>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-3 control-label"><?php echo $this->lang->phrase('categories'); ?></label>
    <div class="col-sm-8"><?php foreach($this->reply['config']['categories'] AS $c) { echo '<div class="btn btn-default category__MAKER_ID__'.$c['category_id'].'" style="display:inline-block" onclick="itemMaker.toggleCategory(_MAKER_ID_,'.$c['category_id'].');"><input type="checkbox" name="_PREFIX_START_categories_PREFIX_END_[]" value="'.$c['category_id'].'"/> '.$c['title'].'</div> '; } ?></div>
  </div>
  <div class="form-group">
    <label class="col-sm-3 control-label"><?php echo $this->lang->phrase('tags'); ?></label>
    <div class="col-sm-8 tags"></div>
  </div>
  <div class="form-group">
    <label class="col-sm-3 control-label"><?php echo $this->lang->phrase('is_active'); ?></label>
    <div class="col-sm-8">
      <input type="checkbox" value="1" name="_PREFIX_START_is_active_PREFIX_END_"/>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-3 control-label"><?php echo $this->lang->phrase('is_public'); ?></label>
    <div class="col-sm-8">
      <input type="checkbox" value="1" name="_PREFIX_START_is_public_PREFIX_END_"/>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-3 control-label"><?php echo $this->lang->phrase('price'); ?></label>
    <div class="col-sm-8">
      <div>
        <input type="text" class="form-control" name="_PREFIX_START_price_PREFIX_END_"/>
      </div>
      <div class="fixedPricing">
        <div class="list">
          <p><b>Fixed</b></p>
          <table class="table table-bordered"><tr><th>Start</th><th>End</th><th>Price</th></tr></table>
        </div>
        <div class="form">          
          <input type="text" class="pricingStart" name="_PREFIX_START_pricing_start_PREFIX_END_"/>
          <input type="text" class="pricingEnd" name="_PREFIX_START_pricing_end_PREFIX_END_"/>
          <input type="text" class="pricingValue" name="_PREFIX_START_pricing_value_PREFIX_END_"/>
          <a class="btn btn-warning" onclick="itemFixedPricing.add(_MAKER_ID_);"><?php echo $this->lang->phrase('add price'); ?></a>
        </div>
      </div>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-3 control-label"><?php echo $this->lang->phrase('quantity'); ?></label>
    <div class="col-sm-8">
      <div>
        <input type="text" class="form-control" name="_PREFIX_START_quantity_PREFIX_END_"/>
      </div>
      
      <div class="fixedQuantity">
        <div class="list">
          <p><b>Fixed</b></p>
          <table class="table table-bordered"><tr><th>Start</th><th>End</th><th>Quantity</th></tr></table>
        </div>
        <div class="form">          
          <input type="text" class="availabilityStart" name="_PREFIX_START_availability_start_PREFIX_END_"/>
          <input type="text" class="availabilityEnd" name="_PREFIX_START_availability_end_PREFIX_END_"/>
          <input type="text" class="availabilityQuantity" name="_PREFIX_START_availability_quantity_PREFIX_END_"/>
          <input type="checkbox" class="availability0Quantity" onclick="itemFixedQuantity.toggle0Quantity(_MAKER_ID_);"/> Unavailable
          <a class="btn btn-warning" onclick="itemFixedQuantity.add(_MAKER_ID_);"><?php echo $this->lang->phrase('add quantity'); ?></a>
        </div>
      </div>
    </div>
  </div>
  _PACKAGE_TEMPLATE_
</div></div>
<div class="form-group">
  <div class="col-sm-offset-3 col-sm-8">
    <a class="btn btn-primary" onclick="itemMaker.save(_MAKER_ID_);"><?php echo $this->lang->phrase('save_item'); ?></a>
    _CLOSER_TEMPLATE_
  </div>
</div>
  </div>
  <div class="package">
    <div class="form-group">
      <label class="col-sm-3 control-label"><?php echo $this->lang->phrase('pack'); ?></label>
      <div class="col-sm-8" id="itemPackage_X_">
        <div class="row">
          <div class="col-xs-8">
            <input type="text" class="form-control" name="packed_search" placeholder="<?php echo $this->lang->phrase('choose_some'); ?>"/>
          </div>
          <div class="col-xs-4">
            <a class="btn btn-warning" onclick="itemMaker.init('#itemPackage .list');"><?php echo $this->lang->phrase('add'); ?></a>
          </div>
        </div>
        <div class="list row"></div>
      </div>
    </div>
  </div>
  <div class="closer">
    <a class="btn btn-danger" onclick="itemMaker.cancel(_MAKER_ID_);"><?php echo $this->lang->phrase('cancel'); ?></a>
  </div>
</div>
<div id="packedItemTemplate" class="hidden">
  <div id="packedItem_X_" class="col-sm-4">
    <div class="thumbnail">
    <img class="img-responsive center-block" src="_THUMBNAIL_"/>
    <p class="title">_TITLE_</p>
    <input type="hidden" name="packed_items[]" value="_X_"/>
    <div class="row">
      <div class="col-xs-6">
        <select class="form-control" name="quantity__X_">_QUANTITY_</select>
      </div>
      <div class="col-xs-6">
        <select class="form-control" name="percentage__X_"><?php 
          for($i=100;$i>=10;$i-=5){ echo '<option class="o'.$i.'" value="'.$i.'">'.$i.'%</option>'; } 
        ?></select>
      </div>
      <a class="btn btn-default" onclick="itemPackage.removeItem(_X_);"><span class="text-danger" style="font-weight:bold;">&times;</span></a>
    </div>
    
    </div>
  </div>
</div>
<?php include(APPPATH.'views/tag/controller.php'); ?>
<script type="text/javascript">
if (typeof(items)=='undefined'){
  var items={'config':{}};
}
Object.assign(items,{
  'reset':function (){
    var _this=this, timestamp=(new Date()).getTime();
    
    if (typeof(_this.config.tabsList)!='undefined'){
      $('#'+_this.config.tabsList+' li.edit .edit').addClass('hidden');
      $('#'+_this.config.tabsList+' li.edit .add').removeClass('hidden');
    }
    if (itemMaker.length>0){
      _NS.DOM.enable('#itemForm .disabled');
      _NS.resetFields('#itemForm');

      itemPackage.reset();
      itemFixedQuantity.reset();
      itemFixedPricing.reset();

      document.querySelector('#itemForm input[name="gallery_file"]').dataset['hash']='gallery_file_hash_'+timestamp;
      document.querySelector('#itemForm input[name="gallery_file_hash"]').value=timestamp;
      document.querySelector('#itemForm .itemGalleryFiles').innerHTML='';
    }
  }
  ,'updatePhoto':function(data){
    //var base=document.querySelector('base').href;
    document.querySelector('#itemForm .photo img').src='<?php echo NS_BASE_URL; ?>'+((data!=null)?('uploads/'+data.folder+'/'+data.name):'assets/images/admin.png');
  }
  ,'uploadPhoto':function(){
    _NS.upload(document.querySelector('#<?php echo 'photoFile'; ?>'),items.updatePhoto);
  }
  ,'removeGalleryFile':function(fileID){
    document.querySelector('#itemForm .itemGalleryFiles input.file'+fileID).value=fileID;
    _NS.DOM.addClass('#itemForm .itemGalleryFiles div.file'+fileID,'hidden');
    //jQuery('#removalConfirmation').modal('hide');
  }
  ,'edit': function (ID){
    var _this=this;
    _NS.post('<?php echo NS_BASE_URL; ?>item/edit','item_id='+ID,{
      'success':function(reply){
        _this.reset();
        _NS.fillFields('#itemForm',reply.data);
        
        document.querySelector('#itemForm input[name="title[1]"]').value=reply.data.title;
        document.querySelector('#itemForm textarea[name="description[1]"]').value=reply.data.description;
        document.querySelector('#itemForm textarea[name="summary[1]"]').value=reply.data.summary;
        
        reply.data['gallery'].forEach(function(f){
          itemMaker.appendGalleryFile(f);
        });
        
        reply.data.packed.forEach(function(i){
          itemPackage.addItem(i);
        });
        
        reply.data['fixed_quantity'].forEach(function(e){
          itemFixedQuantity.add(0,e);
        });
        reply.data['fixed_pricing'].forEach(function(e){
          itemFixedPricing.add(0,e);
        });
        
        tags.init({'container':'#itemMaker0 div.tags'},reply.data['tags']);
        
        reply.data.categories.forEach(function(c){
          itemMaker.toggleCategory(0,c['category_id']);
        });
        if (typeof(_this.config.tabsList)!='undefined'){
          $('#'+_this.config.tabsList+' li.edit .add').addClass('hidden');
          $('#'+_this.config.tabsList+' li.edit .edit').removeClass('hidden');        
          document.querySelectorAll('#'+_this.config.tabsList+' a')[1].click();
        }
      }
    },1);
  }
  ,'save': function (data) {
    var _this=items, data=data;
    _NS.post('<?php echo base_url(); ?>item/save',data,{
      'success':function(reply){
        if (typeof(reply.data.callbackConfig)!='undefined'){
          switch(reply.data.callbackConfig.action){
            case 'addToPackage':
              itemPackage.addItem(reply.data,reply.data.callbackConfig.data.itemMaker);
            break;
            default:
              _this.close();
            break;
          }
        }
        else {
          itemFixedQuantity.reset();
          _this.close();
        }
      }
    },1);
  }
  ,'close':function(){
    var _this=items;
    if (typeof(_this.config.tabsList)!='undefined'){
      document.querySelectorAll('#'+_this.config.tabsList+' a')[0].click();
    }
    _this.reset();
    _NS.post('<?php echo NS_BASE_URL; ?>frontend/updateCache');
  }
});

var itemPackage={
  'reset':function(){
    
    document.querySelector('#itemForm #itemPackage .list').innerHTML='';
    
  }
  ,'addItem':function(data,makerID){
    var data=data,template=document.querySelector('#packedItemTemplate').innerHTML
      ,maxQuantity=data.quantity,packedQuantity=0,q=0,qb=[];
    if (typeof(makerID)!='undefined'){
      itemMaker.cancel(makerID);
    }
    if (typeof(data['packed_quantity'])!='undefined'){
      packedQuantity=data['packed_quantity'];
      template=template.replace('"o'+data['percentage']+'"','"o'+data['percentage']+'" selected="selected"');
    }
    console.log(data.title+': '+q+', '+maxQuantity+', '+packedQuantity);
    for(q=0;q<=maxQuantity;q++){
      qb.push('<option value="'+q+'" '+((q==packedQuantity)?' selected="selected"':'')+'>'+q+'</option>');
    }
    //console.dir(qb);
    
    document.querySelector('#itemForm #itemPackage .list').insertAdjacentHTML('beforeend',template
      .replace('_THUMBNAIL_','<?php echo NS_BASE_URL; ?>uploads/'+data.folder+'/'+data.filename)
      .replace('_TITLE_',data.title)
      .replace('_QUANTITY_',qb.join(''))
      .replace(/_X_/g,data['item_id'])
    );
    this.recalculateTotal();
  }
  ,'removeItem':function(itemID){
    document.querySelector('#packedItem'+itemID).remove();
    this.recalculateTotal();
  }
  ,'recalculateTotal':function(){
    return false;
  }
};

var itemsFromAutocomplete={};

var itemMaker={
  'activeID':0
  ,'length':0
  ,'ready':true
  ,'init':function(container){
    var container=container
      ,baseTemplate=''
      ,packageTemplate=''
      ,closerTemplate=''
      ,template=''
      ,prefixStart='item['+this.length+'][',prefixEnd=']'
      ,makerID=this.length;
    if (makerID>0 && this.activeID>0){
      return alert('first save or close new one');
    }
    baseTemplate=document.querySelector('#itemMakerTemplate .base').innerHTML
    if (this.length==0){
      prefixStart='';prefixEnd='';
      packageTemplate=document.querySelector('#itemMakerTemplate .package').innerHTML.replace('_X_','');
    }
    else {
      closerTemplate=document.querySelector('#itemMakerTemplate .closer').innerHTML.replace('_X_','');
    }
    template='<div id="itemMaker'+this.length+'">'
      +baseTemplate
        .replace('_CLOSER_TEMPLATE_',closerTemplate)
        .replace('_PACKAGE_TEMPLATE_',packageTemplate)
        .replace(/_PREFIX_START_/g,prefixStart)
        .replace(/_PREFIX_END_/g,prefixEnd)
        .replace(/_MAKER_ID_/g,makerID)
    +'</div>';
    
    switch(typeof(container)){
      case 'string':
        document.querySelector(container).insertAdjacentHTML('beforeend',template);
      break;
      case 'object':
        container.insertAdjacentHTML('beforeend',template);
      break;
    }
    
    if (this.length==0){
      jQuery("#itemForm :input[name=\"packed_search\"]").autocomplete({
        source: function( request, response ) {
          var request=request,response=response;
          request['for_package']=1;
          request['search']={'value':request['term']};
          request['length']=10;
          _NS.post('<?php echo NS_BASE_URL; ?>item/filtered',request,{
            'success':function(reply){
              if (reply.data.entries.length==0){
                reply.data.entries.push({'item_id':0});
              }
              response(reply.data.entries);
            }
          },1);
        },
        minLength: 2/** /,
        select: function( event, ui ) {
          console.log( "Selected: " + ui.item.title + " aka " + ui.item.item_id );
        }/**/
      })
      .autocomplete( "instance" )._renderItem = function( ul, item ) {
        var content='';
        itemsFromAutocomplete[item.item_id]=item;
        if (item.item_id>0){
          content='<p>'+item.title+'<br/>'+item.description+'</p><a class="btn btn-default" onclick="itemPackage.addItem(itemsFromAutocomplete['+item['item_id']+']);"><?php echo $this->lang->phrase('attach'); ?></a>';
        }
        else {
          content='<?php echo $this->lang->phrase('not_found'); ?>';
        }
        return jQuery( '<li style="background-color:#FFFFFF;">' )
          .append(content)
          .appendTo( ul );
      };
    }
    
    $('#itemMaker'+this.length+' div.fixedQuantity .availabilityStart').datepicker({'dateFormat':'yy-mm-dd','minDate':'+1d'});
    $('#itemMaker'+this.length+' div.fixedQuantity .availabilityEnd').datepicker({'dateFormat':'yy-mm-dd','minDate':'+1d'});
    
    $('#itemMaker'+this.length+' div.fixedPricing .pricingStart').datepicker({'dateFormat':'yy-mm-dd','minDate':'+1d'});
    $('#itemMaker'+this.length+' div.fixedPricing .pricingEnd').datepicker({'dateFormat':'yy-mm-dd','minDate':'+1d'});
    
    tags.init({'container':'#itemMaker'+this.length+' div.tags','prefixStart':prefixStart,'prefixEnd':prefixEnd});
    this.activeID=this.length*1;
    this.length++;    
  }
  ,'uploadGalleryFile':function(){
    var hash=document.querySelector('#itemGalleryFileHash_'+itemMaker.activeID).value;
    if (hash==''){
      hash=(new Date).getTime();
      document.querySelector('#<?php echo $galleryFile; ?>_'+itemMaker.activeID).dataset['hash']='gallery_file_hash_'+hash;
      document.querySelector('#itemGalleryFileHash_'+itemMaker.activeID).value=hash;
    }
    _NS.upload(document.querySelector('#<?php echo $galleryFile; ?>_'+this.activeID),itemMaker.appendGalleryFile);
  }
  ,'appendGalleryFile':function(data){
    var fileURL='<?php echo NS_BASE_URL; ?>uploads/'+data.folder+'/'+data.name,thumbnailURL='';
    switch(data.mime){
      case 'image':
      case 'image/png':
      case 'image/jpg':
      case 'image/jpeg':
        thumbnailURL=fileURL;
      break;
    }
    document.querySelector('#itemMaker'+itemMaker.activeID+' .itemGalleryFiles')
      .insertAdjacentHTML('beforeend',document.querySelector('#itemMaker'+itemMaker.activeID+' .itemGalleryFileTemplate').innerHTML
        .replace(/_X_/g,data['file_id'])
        .replace('_NAME_',data['name'])
        .replace('_FILE_URL_',fileURL)
        .replace('_THUMBNAIL_',thumbnailURL)
      );
  }
  ,'toggleCategory':function(makerID,categoryID){
    var checkBox=document.querySelector('#itemMaker'+makerID+' .category_'+makerID+'_'+categoryID+' input[type="checkbox"]');
    if (checkBox.checked){
      checkBox.checked=false;
    }
    else {
      checkBox.checked=true;
    }
  }
  ,'save':function(makerID){
    var fullData=_NS.DOM.getFormData('#itemForm'),data={};
    
    if (makerID!=this.activeID){
      alert('first save or close new one');
    }
    else {
      if (makerID>0){
        data=fullData.item[makerID];
        
        
        data['callbackConfig']={
          'action':'addToPackage'
          ,'data':{
            'itemMaker':makerID
          }
        };
      }
      else {
        data=fullData;
      }
      data['fixed_quantity']=itemFixedQuantity.getData(makerID);
      data['fixed_pricing']=itemFixedPricing.getData(makerID);
      items.save(data);
    }
  }
  ,'cancel':function(makerID){
    if (makerID>0){
      document.querySelector('#itemMaker'+makerID).remove();
      this.activeID=0;
    }
    else {
      items.close();
    }
  }
};

var itemFixedQuantity={
  'list':{
    'global':[]
  }
  ,'reset':function(){
    this.list[0]=[];
    this.parse(0);
  }
  ,'add':function(itemMakerID,readyData){
    var itemMakerID=itemMakerID,readyData=readyData,i=0,j=0,positionFound=false,globalID=0,startEntry={},endEntry={}
    ,errors=[]
    ,dates={ 
      'start':document.querySelector('#itemMaker'+itemMakerID+' div.fixedQuantity div.form .availabilityStart').value.replace(/-/g,'')
      ,'end':document.querySelector('#itemMaker'+itemMakerID+' div.fixedQuantity div.form .availabilityEnd').value.replace(/-/g,'')
    }
    ,quantity=document.querySelector('#itemMaker'+itemMakerID+' div.fixedQuantity div.form .availabilityQuantity').value;
    if (typeof(readyData)!='undefined'){
      dates.start=readyData.start;
      dates.end=readyData.end;
      quantity=readyData.quantity;
    }
    
    if (dates.start==''){
      errors.push('Start date should be set');
    }
    else {
      dates.start=parseInt(dates.start);
    }
    
    if (dates.end==''){
      errors.push('End date should be set');
    }
    else {
      dates.end=parseInt(dates.end);
    }
    if (dates.start>dates.end){
      errors.push('Start date should earlier or equal to the end');
    }
    if (quantity==''){
      errors.push('Quantity should be set');
    }
    
    if (errors.length>0){
      return _NS.alert.open('fail','Fail',errors.join('<br/>'),2);
    }

    globalID=this.list.global.length;
    this.list.global.push({
      'start':{'initial':dates.start,'calculated':dates.start}
      ,'end':{'initial':dates.end,'calculated':dates.end}
      ,'quantity':quantity
      ,'overridden':0
    });

    if (typeof(this.list[itemMakerID])=='undefined' || this.list[itemMakerID].length==0){
      this.list[itemMakerID]=[globalID];
    }
    else {
      for(i=0;i<this.list[itemMakerID].length;i++){
        startEntry=this.list.global[this.list[itemMakerID][i]];
        if (positionFound===false && startEntry.overridden==0){
          console.log('checking '+dates.start+' - '+dates.end+' VS '+JSON.stringify(startEntry,null,2));
          if (startEntry.start.calculated>=dates.start){
            positionFound=i;
            
            if (startEntry.start.calculated<dates.end){
              for(j=i;j<this.list[itemMakerID].length;j++){
                
                endEntry=this.list.global[this.list[itemMakerID][j]];
                console.log('checking '+dates.end+' VS '+JSON.stringify(endEntry,null,2));
                if (endEntry.end.calculated<=dates.end){
                  console.log('overridden');
                  //this.list.global[this.list[itemMakerID][j]]['overridden']=1;
                  endEntry['overridden']=1;
                }
                else {
                  if (endEntry.start.calculated<dates.end){
                    endEntry.start.calculated=dates.end;
                  }
                }
              }
            }
          }
          else {
            if (startEntry.end.calculated>dates.start){
              if (startEntry.end.calculated<=dates.end){
                startEntry.end.calculated=dates.start;
                positionFound=i+1;
                
                for(j=positionFound;j<this.list[itemMakerID].length;j++){
                
                  endEntry=this.list.global[this.list[itemMakerID][j]];
                  //console.log('checking '+dates.end+' VS '+JSON.stringify(endEntry,null,2));
                  if (endEntry.end.calculated<=dates.end){
                    //console.log('overridden');
                    //this.list.global[this.list[itemMakerID][j]]['overridden']=1;
                    endEntry['overridden']=1;
                  }
                  else {
                    if (endEntry.start.calculated<dates.end){
                      endEntry.start.calculated=dates.end;
                    }
                  }
                }
              }
              else {
                this.list.global.push(Object.assign({},startEntry,{
                  'start':{'initial':dates.end,'calculated':dates.end}
                  ,'end':{'initial':startEntry.end.calculated,'calculated':startEntry.end.calculated}
                }));
                this.list[itemMakerID].splice((i+1),0,(globalID+1));
                
                startEntry.end.calculated=dates.start;
                positionFound=i+1;
              }
            }
          }
        }
      }
      if (positionFound===false){
        this.list[itemMakerID].push(globalID);
      }
      else {
        this.list[itemMakerID].splice(positionFound,0,globalID);
      }
    }
    
    document.querySelectorAll('#itemMaker'+itemMakerID+' div.fixedQuantity div.form input').forEach(function(e){
      e.value='';
      if (e.type=='checkbox'){
        e.checked=false;
      }
    });
    
    this.parse(itemMakerID);
    
  }
  ,'toggle0Quantity':function(itemMakerID){
    var itemMakerID=itemMakerID, quantity=0;
    
    if (document.querySelector('#itemMaker'+itemMakerID+' div.fixedQuantity div.form .availability0Quantity').checked==false){
      quantity='';
    }
    else {
      quantity='0';
    }
    document.querySelector('#itemMaker'+itemMakerID+' div.fixedQuantity div.form .availabilityQuantity').value=quantity;
  }
  ,'parse':function(itemMakerID){
    var itemMakerID=itemMakerID,rows=[];
    
    document.querySelectorAll('#itemMaker'+itemMakerID+' div.fixedQuantity div.list table tr.entryRow').forEach(function(r){r.remove();});
    
    this.list[itemMakerID].forEach(function(e){
      var entry=itemFixedQuantity.list.global[e];
      if (entry.overridden===0){
        rows.push('<tr class="entryRow"><td>'+entry.start.calculated+'</td><td>'+entry.end.calculated+'</td><td>'+entry.quantity+'</td></tr>');
      }
    });
    
    
    document.querySelector('#itemMaker'+itemMakerID+' div.fixedQuantity div.list table').insertAdjacentHTML('beforeend',rows.join(''));
  }
  ,'getData':function(itemMakerID){
    var itemMakerID=itemMakerID,result=[];
    if (typeof(this.list[itemMakerID])!='undefined'){
      this.list[itemMakerID].forEach(function(i){
        var entry=itemFixedQuantity.list.global[i];
        if (entry.overridden===0){
          result.push({'start':entry.start.calculated,'end':entry.end.calculated,'quantity':entry.quantity});
        }
      });
    }
    return result;
  }
};

var itemFixedPricing={
  'list':{
    'global':[]
  }
  ,'reset':function(){
    this.list[0]=[];
    this.parse(0);
  }
  ,'add':function(itemMakerID,readyData){
    var itemMakerID=itemMakerID,readyData=readyData,i=0,j=0,positionFound=false,globalID=0,startEntry={},endEntry={}
    ,errors=[]
    ,dates={ 
      'start':document.querySelector('#itemMaker'+itemMakerID+' div.fixedPricing div.form .pricingStart').value.replace(/-/g,'')
      ,'end':document.querySelector('#itemMaker'+itemMakerID+' div.fixedPricing div.form .pricingEnd').value.replace(/-/g,'')
    }
    ,price=document.querySelector('#itemMaker'+itemMakerID+' div.fixedPricing div.form .pricingValue').value;
    if (typeof(readyData)!='undefined'){
      dates.start=readyData.start;
      dates.end=readyData.end;
      price=readyData.price;
    }
    
    if (dates.start==''){
      errors.push('Start date should be set');
    }
    else {
      dates.start=parseInt(dates.start);
    }
    
    if (dates.end==''){
      errors.push('End date should be set');
    }
    else {
      dates.end=parseInt(dates.end);
    }
    if (dates.start>dates.end){
      errors.push('Start date should earlier or equal to the end');
    }
    if (price<1){
      errors.push('Price should be >=1');
    }
    
    if (errors.length>0){
      return _NS.alert.open('fail','Fail',errors.join('<br/>'),2);
    }

    globalID=this.list.global.length;
    this.list.global.push({
      'start':{'initial':dates.start,'calculated':dates.start}
      ,'end':{'initial':dates.end,'calculated':dates.end}
      ,'price':price
      ,'overridden':0
    });

    if (typeof(this.list[itemMakerID])=='undefined' || this.list[itemMakerID].length==0){
      this.list[itemMakerID]=[globalID];
    }
    else {
      for(i=0;i<this.list[itemMakerID].length;i++){
        startEntry=this.list.global[this.list[itemMakerID][i]];
        if (positionFound===false && startEntry.overridden==0){
          
          if (startEntry.start.calculated>=dates.start){
            positionFound=i;
            
            if (startEntry.start.calculated<dates.end){
              for(j=i;j<this.list[itemMakerID].length;j++){
                
                endEntry=this.list.global[this.list[itemMakerID][j]];
                if (endEntry.end.calculated<=dates.end){
                  endEntry['overridden']=1;
                }
                else {
                  if (endEntry.start.calculated<dates.end){
                    endEntry.start.calculated=dates.end;
                  }
                }
              }
            }
          }
          else {
            if (startEntry.end.calculated>dates.start){
              if (startEntry.end.calculated<=dates.end){
                startEntry.end.calculated=dates.start;
                positionFound=i+1;
                
                for(j=positionFound;j<this.list[itemMakerID].length;j++){
                
                  endEntry=this.list.global[this.list[itemMakerID][j]];
                  if (endEntry.end.calculated<=dates.end){
                    endEntry['overridden']=1;
                  }
                  else {
                    if (endEntry.start.calculated<dates.end){
                      endEntry.start.calculated=dates.end;
                    }
                  }
                }
              }
              else {
                this.list.global.push(Object.assign({},startEntry,{
                  'start':{'initial':dates.end,'calculated':dates.end}
                  ,'end':{'initial':startEntry.end.calculated,'calculated':startEntry.end.calculated}
                }));
                this.list[itemMakerID].splice((i+1),0,(globalID+1));
                
                startEntry.end.calculated=dates.start;
                positionFound=i+1;
              }
            }
          }
        }
      }
      if (positionFound===false){
        this.list[itemMakerID].push(globalID);
      }
      else {
        this.list[itemMakerID].splice(positionFound,0,globalID);
      }
    }
    
    document.querySelectorAll('#itemMaker'+itemMakerID+' div.fixedPricing div.form input').forEach(function(e){
      e.value='';
      if (e.type=='checkbox'){
        e.checked=false;
      }
    });
    
    this.parse(itemMakerID);
    
  }
  ,'parse':function(itemMakerID){
    var itemMakerID=itemMakerID,rows=[];
    
    document.querySelectorAll('#itemMaker'+itemMakerID+' div.fixedPricing div.list table tr.entryRow').forEach(function(r){r.remove();});
    
    this.list[itemMakerID].forEach(function(e){
      var entry=itemFixedPricing.list.global[e];
      if (entry.overridden===0){
        rows.push('<tr class="entryRow"><td>'+entry.start.calculated+'</td><td>'+entry.end.calculated+'</td><td>'+entry.price+'</td></tr>');
      }
    });
    
    
    document.querySelector('#itemMaker'+itemMakerID+' div.fixedPricing div.list table').insertAdjacentHTML('beforeend',rows.join(''));
  }
  ,'getData':function(itemMakerID){
    var itemMakerID=itemMakerID,result=[];
    if (typeof(this.list[itemMakerID])!='undefined'){
      this.list[itemMakerID].forEach(function(i){
        var entry=itemFixedPricing.list.global[i];
        if (entry.overridden===0){
          result.push({'start':entry.start.calculated,'end':entry.end.calculated,'price':entry.price});
        }
      });
    }
    return result;
  }
};
runWhenReady(function(){
itemMaker.init(document.querySelector('#itemForm'));
});
</script>