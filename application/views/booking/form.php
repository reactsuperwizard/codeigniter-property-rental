<script type="text/javascript">
if (typeof(bookings)=='undefined'){
  var bookings={'config':{}};
}
</script>
<?php

$timeMenuOptions=array();
for ($i=0;$i<24;$i++){
  $hour=$i%12;
  if ($i==0){
    $hour=12;
    $ampm='AM';
  }
  elseif ($i==12){
    $hour=12;
    $ampm='PM';
  }
  
  for($j=0;$j<60;$j+=5){
    $value=(($i<10)?'0':'').$i.':'.(($j<10)?'0':'').$j;
    $text=$hour.':'.(($j<10)?'0':'').$j.' '.$ampm;
    
    $timeMenuOptions[]='<option value="'.$value.'">'.$text.'</option>';
  }
}
$timeMenu=join('',$timeMenuOptions);



?>
<form method="POST" action="<?php echo NS_BASE_URL . 'booking/save'; ?>" class="form-horizontal form-groups-bordered validate" target="_top" id="bookingForm" onsubmit="bookings.save();return false;">
<fieldset>
<input type="hidden" name="booking_id" value="0" data-reset_value="0"/>
<div class="row padded"><div class="col-sm-12">
  
  <div class="form-group">
    <label class="col-sm-3 col-md-2 control-label"><?php echo $this->lang->phrase('status'); ?></label>
    <div class="col-sm-9 col-md-10 bookingStatus">
      <table><tr><td><select name="status_id" class="form-control" onchange="bookings.updateStatus();"><option value="0" class="v0" data-code="none"><?php echo $this->lang->phrase('choose'); ?></option><?php foreach($this->reply['config']['statuses'] AS $status){
        echo '<option class="v'.$status['status_id'].'" data-code="'.$status['code'].'" value="'.$status['status_id'].'">'.$status['name'].'</option>';
        } ?></select></td><td class="ifActive hidden"> 
      <table><tr><td>&nbsp;until&nbsp;</td>
        <td><input type="text" class="form-control" name="expiration_date" placeholder="Date"/></td>
        <td><input type="text" class="form-control" name="expiration_time" placeholder="Time"/></td>
      </tr></table></td></tr></table>
    </div>
  </div>
  <?php $customerDeliveryPrefix='booking'; include(VIEWPATH.'booking/form/customer_delivery.php');?>
  <div class="form-group">
    <label class="col-sm-3 col-md-2 control-label"><?php echo $this->lang->phrase('rent_period'); ?></label>
    <div class="col-sm-9 col-md-10">
      <div class="row">
        <div class="col-sm-6">
          <label class="control-label"><?php echo $this->lang->phrase('start'); ?></label>
          <div class="row">
            <div class="col-xs-6">
              <input class="form-control" type="text" name="delivery_date"/>
            </div>
            <div class="col-xs-6">
              <input type="text" class="form-control" name="delivery_time" data-reset_value="14:00"/>
            </div>
          </div>
        </div>
        <div class="col-sm-6">
          <label class="control-label"><?php echo $this->lang->phrase('end'); ?></label>
          <div class="row">
            <div class="col-xs-6">
              <input type="text" class="form-control" name="collection_date"/>
            </div>
            <div class="col-xs-6">
              <input type="text" class="form-control" name="collection_time" data-reset_value="10:00"/>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-3 col-md-2 control-label"><?php echo $this->lang->phrase('chargeable_days'); ?></label>
    <div class="col-sm-9 col-md-10">
      <input type="text" class="form-control" name="chargeable_days" data-reset_value="1" value="1" onchange="bookings.entries.recalculate(true);"/>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-3 col-md-2 control-label">Customer Notes (Add any relevant information here)</label>
    <div class="col-sm-9 col-md-10">
        <textarea name="extra_notes" class="form-control"></textarea>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-3 col-md-2 control-label">Settings</label>
    <div class="col-sm-9 col-md-10">
      <table class="table table-condensed table-responsive">
          <thead><tr>
            <th><label class="col-sm-3 col-md-2 control-label"><?php echo $this->lang->phrase('purchase_order'); ?></label></th>
            <th colspan="2"><?php echo $this->lang->phrase('FINAL_DUE_DATE'); ?></th>
          </tr></thead>
          <tbody><tr>
            <td><input class="form-control" type="text" name="purchase_order" value="" placeholder="Purchase Order"></td>
            <td>
              <input type="radio" name="due_direction" value="-"/> -&nbsp;&nbsp;
              <input type="radio" name="due_direction" value="+" data-reset_value="+"/> +
            </td>
            <td>
              <input class="form-control" type="text" name="due_days" data-reset_value="7" value="7"/>
            </td>
          </tr></tbody>
        </table>
    </div>
  </div>
  <!-- <div class="form-group">
    <label class="col-sm-3 col-md-2 control-label"><?php echo $this->lang->phrase('purchase_order'); ?></label>
    <div class="col-sm-9 col-md-10">
        <input class="form-control" type="text" name="purchase_order" value="" placeholder="Purchase Order">
    </div>
  </div> -->
  <div class="form-group">
    <label class=" control-label col-sm-3 col-md-2"><?php echo $this->lang->phrase('content'); ?></label>
    <div class="col-sm-9 col-md-10" id="bookingContent">
      
    </div>
  </div>
  <div class="form-group">
    <label class=" control-label col-sm-3 col-md-2"><?php echo $this->lang->phrase('logistics'); ?></label>
    <div class="col-sm-9 col-md-10" id="bookingLogistics">
      <div class="panel panel-default">
        <div class="panel-body">
          <div class="panel panel-default">
            <div class="panel-heading"><?php echo $this->lang->phrase('delivery'); ?></div>
            <div class="panel-body deliveryPanel"></div>
          </div>
          <div class="panel panel-default">
            <div class="panel-heading"><?php echo $this->lang->phrase('collection'); ?></div>
            <div class="panel-body collectionPanel"></div>
          </div>
        </div>
      </div>
    </div>
  </div> 
  <div class="form-group">
    <div class="col-sm-offset-3 col-sm-9 col-md-offset-2 col-md-10">
      <a class="btn btn-primary" onclick="bookings.requestSave();"><?php echo $this->lang->phrase('save_booking'); ?></a>
    </div>
  </div>
  <?php //echo '<pre>'; print_r($this->reply['config']); echo '</pre>'; ?>
</div></div>
</fieldset>
</form>
<div id="bookingEntryAttachment" style="display:none;position: fixed;top:0px;left:0px;bottom:0px;right:0px;width:100%;height:100%;background-color:rgba(0,0,0,0.5);padding: 5%;">
  
  <div class="panel panel-default">
    <div class="panel-heading">Here you can add new <span class="type"></span> to this booking<span class="text-danger pull-right glyphicon glyphicon-remove" onclick="document.querySelector('#bookingEntryAttachment').style.display='none';"></span></div>
    <div class="panel-body">
      <div class="row">
    <div class="col-sm-6">
      <form id="bookingItemForm" onsubmit="bookings.entries.attachItem(_NS.DOM.getFormData('#bookingItemForm'));return false;">
        <input type="hidden" name="is_additional" data-reset_value="1"/>
        <input type="hidden" name="extra_item_id" value=""/>
        <input type="text" class="form-control" name="item_search" placeholder="<?php echo $this->lang->phrase('choose_some_items'); ?>">
        <hr/>OR<hr/>
        <input class="form-control" type="text" name="title" placeholder="Title"/>
        <textarea class="form-control" name="description" placeholder="Description"></textarea>
        <input class="form-control" name="quantity" placeholder="Quantity"/>
        <input class="form-control" name="price" placeholder="Price"/>
        <input type="radio" name="discount_type" value="percentage" data-reset_value="percentage"/>&nbsp;%&nbsp;
        <input type="radio" name="discount_type" value="amount"/>&nbsp;$
        <input type="text" class="form-control" name="discount_value" value="0"/>
        <a class="btn btn-warning" onclick="bookings.entries.attachItem(_NS.DOM.getFormData('#bookingItemForm'));">Add new</a>
      </form>
      <form id="bookingServiceForm" onsubmit="bookings.entries.attachService(_NS.DOM.getFormData('#bookingServiceForm'));return false;">
        <input type="hidden" name="extra_item_id" value=""/>
        <input class="form-control" type="text" name="title" placeholder="Name"/>
        <textarea class="form-control" name="description" placeholder="Description"></textarea>

        <input class="form-control" name="price" placeholder="Rate"/>
        <input class="form-control" name="people" placeholder="No. staff"/>
        <input class="form-control" name="quantity" placeholder="Quantity"/>
        <input type="radio" name="discount_type" value="percentage" data-reset_value="percentage"/>&nbsp;%&nbsp;
        <input type="radio" name="discount_type" value="amount"/>&nbsp;$
        <input type="text" class="form-control" name="discount_value" value="0"/>
        <a class="btn btn-warning" onclick="bookings.entries.attachService(_NS.DOM.getFormData('#bookingServiceForm'));">Add new</a>
      </form>
    </div>
  </div>
    </div>
  </div>
</div>
<div id="bookingLogisticsUpdate" class="hidden" style="position: fixed;top:0px;left:0px;bottom:0px;right:0px;width:100%;height:100%;background-color:rgba(0,0,0,0.5);padding: 5%;">
  <div class="panel panel-default">
    <div class="panel-heading">choose new <span class="operationMode"></span> for <span class="itemTitle"></span></div>
    <div class="panel-body">
      <form id="bookingLogisticsUpdateForm">
        <input type="text" name="date" value="" placeholder="Date"/>
        <input type="text" name="time" value="" placeholder="Time"/>
        <input type="hidden" name="item_id" value=""/>
        <input type="hidden" name="mode" value=""/>
        <a class="btn btn-primary" onclick="bookings.logistics.confirmUpdate();">Update</a><a class="btn btn-danger" onclick="_NS.DOM.addClass('#bookingLogisticsUpdate','hidden');">Cancel</a>
      </form>
    </div>
  </div>
  <div></div>
  <div>
    
  </div>
</div>
<div id="bookingTemplates" class="hidden">
<?php include(__DIR__.'/form/template.php'); ?>
</div>
<script type="text/javascript">
if (typeof(bookings)=='undefined'){
  var bookings={'config':{}};
}

Object.assign(bookings,{
  'reset':function (){
    var _this=this, timestamp=(new Date()).getTime(),itemID=0;
    document.querySelector('#bookingForm #bookingContent').innerHTML=document.querySelector('#bookingContentTemplate').innerHTML;
    if (typeof(_this.config.tabsList)!='undefined'){
      $('#'+_this.config.tabsList+' li.edit .edit').addClass('hidden');
      $('#'+_this.config.tabsList+' li.edit .add').removeClass('hidden');
    }
    _NS.DOM.enable('#bookingForm .disabled');
    _NS.resetFields('#bookingForm');
    _this.address.resetDeliveryContactDetails();
    _this.updateStatus();
    _this.customer.reset();
    _this.logistics.reset();
    document.querySelector('#bookingForm select[name="residential_address_id"]').dataset['chosen']='';
    document.querySelector('#bookingForm select[name="delivery_address_id"]').dataset['chosen']='';

    for (itemID in itemCache){
      if (typeof(itemCache[itemID]['availability'])!='undefined'){
        itemCache[itemID]['availability']={};
      }
    }
    bookings.entries.reset();
  }
  ,'edit': function (ID){
    var _this=this;
    _NS.post('<?php echo NS_BASE_URL; ?>booking/edit','booking_id='+ID,{
      'success':function(reply){
        var contactField=''
        ,logisticsOperations=['delivery','collection'];
        
        _this.reset();
        _NS.fillFields('#bookingForm',reply.data);
        if(reply.data['residential_address_id']=='0'){
          reply.data['residential_address_id']='skip';
        }
        if(reply.data['delivery_address_id']=='0'){
          reply.data['delivery_address_id']='skip';
        }
        document.querySelector('#bookingForm select[name="residential_address_id"]').dataset['chosen']=reply.data['residential_address_id'];
        document.querySelector('#bookingForm select[name="delivery_address_id"]').dataset['chosen']=(reply.data['residential_address_id']!=reply.data['delivery_address_id'] || reply.data['delivery_address_id']=='skip')?reply.data['delivery_address_id']:'residential';
        bookings.customer.choose(reply.data['customer']);
        
        
        for(contactField in reply.data['delivery_contact']){
          document.querySelector('#bookingForm input[name="delivery_contact['+contactField+']"]').dataset['chosen']=reply.data['delivery_contact'][contactField];
        }
        
        _this.updateStatus();
        
        //reply.data['items']=reply.data.entries['items'];
        //reply.data['services']=reply.data.entries['services'];
        itemCache=reply.itemCache;
        //reply.data.variations.forEach(function(v){
        //console.log(JSON.stringify(reply.data.entries,null,2));
        
        
        //});
        
        //_that.logistics.add(itemData['item_id'],'delivery',itemData['delivery_code']);
        //_that.logistics.add(itemData['item_id'],'collection',itemData['collection_code']);
        if (typeof(reply.data.logistics)!='undefined'){
          logisticsOperations.forEach(function(o){
            var result=[],o=o;
            
            reply.data.logistics[o]['codes'].forEach(function(code){
              
              var content=[];
              reply.data.logistics[o][code]['entries'].forEach(function(e){
                var p=e;
                
                if (typeof(p['atomic_item_id'])!='undefined'){
                  //console.log('regular logistics '+p['atomic_item_id']+' '+o+' '+code+' '+itemCache[p['atomic_item_id']]['title']);
                  _this.logistics.add(p['atomic_item_id'],o,code);
                  //p['title']=itemCache[p['atomic_item_id']]['title'];
                }
                else {
                  _this.logistics.addExtraItem(p['extra_item_id'],p.title);
                  _this.logistics.add(p['extra_item_id'],o,code);
                  //console.log((new Date()).getTime()+': '+p.title);
                  //content.push(p.quantity+' x '+p.title);
                }
              });
              //result.push('<label>'+reply.data.logistics[o][code]['label']+'</label><ul><li>'+content.join('</li><li>')+'</li></ul>');
            });
            
            //document.querySelector('#bookingForm #bookingLogistics .'+o+'Panel').innerHTML=result.join('');
          });
        }
        
        bookings.entries.init(reply.data.entries);

    //document.querySelector('#quoteForm input[name="code"]').value=reply.data.code;
        
        if (typeof(_this.config.tabsList)!='undefined'){
          $('#'+_this.config.tabsList+' li.edit .add').addClass('hidden');
          $('#'+_this.config.tabsList+' li.edit .edit').removeClass('hidden');        
          document.querySelectorAll('#'+_this.config.tabsList+' a')[1].click();
        }
      }
    },1);
  }
  ,'updateStatus':function(){
    var chosenStatus=document.querySelector('#bookingForm .bookingStatus select[name="status_id"]').value,hide=true;
    switch(document.querySelector('#bookingForm .bookingStatus select[name="status_id"] option.v'+chosenStatus).dataset['code']){
      case 'active':
        hide=false;
      break;
      default:
      break;
    }
    if (hide){
      _NS.DOM.addClass('#bookingForm .bookingStatus .ifActive','hidden');
    }
    else {
      _NS.DOM.removeClass('#bookingForm .bookingStatus .ifActive','hidden');
    }
  }
  ,'requestSave':function(){
    var _this=this
    ,chosenStatus=document.querySelector('#bookingForm .bookingStatus select[name="status_id"]').value;
    switch(document.querySelector('#bookingForm .bookingStatus select[name="status_id"] option.v'+chosenStatus).dataset['code']){
      case 'active':
        _this.save();
      break;
      case 'none':
        _NS.alert.open('fail','Fail','You should choose status first',2);
      break;
      case 'cancelled':
        _NS.alert.confirm('bookings.save();','Booking will get  status \'cancelled\' and all allocated items will be released');
      break;
      default:
        _this.save();
      break;
    }
  }
  ,'save': function () {
    var _this=bookings, data=_NS.DOM.getFormData('#bookingForm');
    _NS.alert.close();
    data['logistics']=_this.logistics.itemIDs;
    _NS.post('<?php echo NS_BASE_URL; ?>booking/save',data,{
      'success':function(reply){
        _this.close();
      }
      ,'fail':function(reply){
        var reply=reply;
        ['customer_id'].forEach(function(key){
          document.querySelector('#quoteForm input[name="'+key+'"]').value=reply.data[key];
        });
        _NS.defaultReplyActions.fail(reply);
      }
    },1);
  }
  ,'close':function(){
    var _this=bookings;
    if (typeof(_this.config.tabsList)!='undefined'){
      document.querySelectorAll('#'+_this.config.tabsList+' a')[0].click();
    }
    _this.reset();
  }
  
  ,'chooseCustomer':function(userData){
    var field='',el=null;
    _NS.DOM.disable('#bookingCustomer input');
    //document.querySelector('#bookingCustomer .chosen').innerHTML=userData['first_name']+' '+userData['last_name']+' ('+userData['email']+')';
    document.querySelector('#bookingCustomer input[name="customer_id"]').value=userData['user_id'];
    for(field in userData){
      el=document.querySelector('#bookingCustomer input[name="customer['+field+']"]');
      if (el!=null){
        el.value=userData[field];
      }
    }
    //document.querySelector('#bookingCustomer .choice').style.display='none';
    document.querySelector('#bookingCustomer .customerDetails').style.display='block';
    bookings.residentialAddress.updateList(userData['user_id']);
    bookings.deliveryAddress.updateList(userData['user_id']);
  }
  
  ,'periodID':''
  ,'chosenPeriod':false
  ,'updateChosenPeriod':function(){
    var errors=[]
      ,deliveryDate=document.querySelector('#bookingForm input[name="delivery_date"]').value
      ,deliveryTime=document.querySelector('#bookingForm input[name="delivery_time"]').value
      ,collectionDate=document.querySelector('#bookingForm input[name="collection_date"]').value
      ,collectionTime=document.querySelector('#bookingForm input[name="collection_time"]').value
      ,datePattern=/^20(1[8-9]|[2-9][0-9])-(0[1-9]|1[0-2])-([0-2][0-9]|3[0-1])$/
      ,timePattern=/^([0-1]{1}[0-9]|2[0-3]):([0-5][0-9])$/;

    if (!datePattern.test(deliveryDate)){
      errors.push('Delivery date should be chosen');
    }
    if (!timePattern.test(deliveryTime)){
      errors.push('Delivery time should be chosen');
    }
    if (!datePattern.test(collectionDate)){
      errors.push('Collection date should be chosen');
    }
    if (!timePattern.test(collectionTime)){
      errors.push('Collection time should be chosen');
    }
    if (deliveryDate>collectionDate || deliveryDate==collectionDate && deliveryTime>=collectionTime){
      errors.push('Collection date and time should be after delivery');
    }

    if (errors.length>0){
      _NS.alert.open('fail','Fail',errors.join('<br/>'),2);
      return false;
    }
    bookings.periodID=deliveryDate.replace(/-/g,'')+deliveryTime.replace(':','')+'_'+collectionDate.replace(/-/g,'')+collectionTime.replace(':','');

    bookings.chosenPeriod={
      'start_date':deliveryDate,'start_time':deliveryTime
      ,'end_date':collectionDate,'end_time':collectionTime
    };
    return true;
  }
});

bookings['entries']={
  'total':0
  ,'active':{'total':0}
  ,'partialData':{
    'discountTypes':{}
    ,'multipliedPrices':{}
    ,'discounts':{}
  }
  ,'init':function(data){
    var _this=this,data=data;
    _this.reset();
    bookings.updateChosenPeriod();
      data.items.forEach(function(e){
        //console.log("item: \n"+JSON.stringify(e,null,2));
        switch(e.type){
          case 'additionalItem':
            _this.addItem('additional',e);
          break;
          case 'regularItem':
            e['attached_quantity']=e['quantity']*1;
            //console.log("clean cache "+e['item_id']+"\n"+JSON.stringify(itemCache[e['item_id']],null,2));
            //console.log('item to load '+e['item_id']+"\n"+JSON.stringify(e,null,2));
            _this.addItem(e.type.replace('Item',''),Object.assign({},itemCache[e['item_id']],e));
          break;
        }
        //bookings.variations.entries.addItem(_this.total,'regular',Object.assign({},itemCache[e['item_id']],e));
      });
      
      data.services.forEach(function(e){
        _this.addService(e);
      });
  }
  ,'reset':function(){
    var _this=this;
    
    _this.total=0;
    _this.active={'total':0};
    _this.partialData={
      'discountTypes':{}
      ,'multipliedPrices':{}
      ,'discounts':{}
    };

  }
  ,'request':function(type){
    var _this=this,type=type
    ,extraItemID='E'+(new Date()).getTime()
    ,el=document.querySelector('#bookingEntryAttachment');
    
    if (bookings.updateChosenPeriod()){
      el.querySelector('.panel-heading span.type').innerHTML=type;
      el.querySelectorAll('form').forEach(function(f){
        _NS.DOM.resetFields(f);
        f.querySelector('input[name="extra_item_id"]').value=extraItemID;
        f.className='hidden';
      });
      switch(type){
        case 'service':
          el.querySelector('#bookingServiceForm').className='';
        break;
        default:
          el.querySelector('#bookingItemForm').className='';
        break;
      }

      el.style.display='block';
    }
  }
  ,'attachItem':function(data){
    var _this=this,data=data,type='regular';
    
    if (typeof(data['is_additional'])!='undefined'){
      type='additional';
    }
    if (type=='additional' || document.querySelector('#bookingForm #bookingContent tbody.items .attachedItem'+data['item_id'])==null){
      _this.addItem(type,data);
    }
  }
  ,'attachService':function(data){
    var _this=this,_that=bookings,data=data;
     _that.logistics.addExtraItem(data['extra_item_id'],data['title']);
        
    _that.logistics.add(data['extra_item_id'],'delivery',_that.chosenPeriod['start_date'].replace(/-/g,'')+_that.chosenPeriod['start_time'].replace(/:/g,''));
    _that.logistics.add(data['extra_item_id'],'collection',_that.chosenPeriod['end_date'].replace(/-/g,'')+_that.chosenPeriod['end_time'].replace(/:/g,''));
    _this.addService(data);
  }
  ,'quantities':{}
  ,'addItem':function(entryType,data){
      var _this=this,_that=bookings,entryType=entryType
        ,data=data,variationQuantities=null
        ,entryID=0,template='',params={},periodID=''
        ,updateRequired=false,packedItemID=''
        ,maxAvailableQuantity='',packedItemQuantity=0;
      
      if (typeof(data['item_id'])!='undefined'){
        if (document.querySelector('#bookingForm #bookingContent tbody.items .attachedItem'+data['item_id'])!=null){
          return false;
        }
      }
      //console.log("addItem\n"+JSON.stringify(data,null,2));
      if (entryType=='regular'){
        //variationQuantities=bookings.variations.quantities[variationID];
        periodID=bookings.chosenPeriod['start_date'].replace(/-/g,'')+bookings.chosenPeriod['start_time'].replace(':','')+'_'+bookings.chosenPeriod['end_date'].replace(/-/g,'')+bookings.chosenPeriod['end_time'].replace(':','');
        
        params={'itemID':data['item_id'],'period':bookings.chosenPeriod,'unlock':{'type':'booking','id':document.querySelector('#bookingForm input[name="booking_id"]').value}};
        if (data['item_package_id']==data['item_id']){
          if (typeof(itemCache[data['item_id']]['packed'])=='undefined'){
            itemCache[data['item_id']]['packed']={};
            updateRequired=true;
          }
          else {
            for(packedItemID in itemCache[data['item_id']]['packed']){
              if (typeof(itemCache[packedItemID])=='undefined'){
                updateRequired=true;
                break;
              }
              if (typeof(itemCache[packedItemID]['availability'])=='undefined'){
                updateRequired=true;
                break;
              }
              if (typeof(itemCache[packedItemID]['availability'][periodID])=='undefined'){
                updateRequired=true;
                break;
              }
            }
          }
          params['isPackage']=1;
        }
        else {
          if (
            typeof(itemCache[data['item_id']]['availability'])=='undefined'
            || typeof(itemCache[data['item_id']]['availability'][periodID])=='undefined'
          ){
            updateRequired=true;
          }
          params['isPackage']=0;
        }
        
        if (updateRequired){
          _NS.post('<?php echo NS_BASE_URL; ?>item/filtered',params,{
            'success':function(reply){
              var e=0,x=reply.data.entries.length,itemData=null;

              if (params['isPackage']==1){
                itemCache[params['itemID']]['packed']={};
              }
              for (e=0;e<x;e++){
                itemData=reply.data.entries[e];
                if (typeof(itemCache[itemData['item_id']])=='undefined'){
                  itemCache[itemData['item_id']]=itemData;
                }
                _that.logistics.add(itemData['item_id'],'delivery',itemData['delivery_code']);
                _that.logistics.add(itemData['item_id'],'collection',itemData['collection_code']);
                
                if (params['isPackage']==1){
                  itemCache[params['itemID']]['packed'][itemData['item_id']]={
                    'quantity':itemData['packed_quantity']
                    ,'percentage':itemData['packed_percentage']
                  };
                }
                
                if (typeof(itemCache[itemData['item_id']]['availability'])=='undefined'){
                  itemCache[itemData['item_id']]['availability']={};
                }
                if (typeof(itemCache[itemData['item_id']]['availability'][periodID])=='undefined'){
                  itemCache[itemData['item_id']]['availability'][periodID]=((itemData['fixed_quantity']!==null)?itemData['fixed_quantity']:itemData['quantity'])-itemData['booked'];
                }
              }
              _this.addItem(entryType,data);
            }
          },1);
          return false;
        }
        else {
          if (data['item_package_id']==data['item_id']){
            for(packedItemID in itemCache[params['itemID']]['packed']){
              packedItemQuantity=Math.floor(itemCache[packedItemID]['availability'][periodID]/itemCache[params['itemID']]['packed'][packedItemID]['quantity']);
              if (maxAvailableQuantity===''){
                maxAvailableQuantity=packedItemQuantity*1;
              }
              else if(packedItemQuantity<maxAvailableQuantity) {
                maxAvailableQuantity=packedItemQuantity;
              }
              if (typeof(_this.quantities[packedItemID])=='undefined'){
                _this.quantities[packedItemID]={'total':0,'parts':{}};
              }
              if (typeof(_this.quantities[packedItemID]['parts'][params['itemID']])=='undefined'){
                _this.quantities[packedItemID]['parts'][params['itemID']]=0;
              }
              if (typeof(_this.quantities[packedItemID]['parts'][packedItemID])=='undefined'){
                _this.quantities[packedItemID]['parts'][packedItemID]=0;
              }
            }
            //console.log('after package '+data['item_id']+': '+JSON.stringify(bookings.entries.quantities,null,2));
            data['max_quantity']=maxAvailableQuantity;
          }
          else {
            if (typeof(itemCache[data['item_id']]['availability'])=='undefined'){
              itemCache[data['item_id']]['availability']={};
            }
            itemCache[data['item_id']]['availability'][bookings.periodID]=((itemCache[data['item_id']]['fixed_quantity']!==null)?itemCache[data['item_id']]['fixed_quantity']:itemCache[data['item_id']]['quantity'])-itemCache[data['item_id']]['booked'];

            if (typeof(_this.quantities[data['item_id']])=='undefined'){
              _this.quantities[data['item_id']]={'total':0,'parts':{}};
              _this.quantities[data['item_id']]['parts'][data['item_id']]=0;
            }
            data['max_quantity']=itemCache[data['item_id']]['availability'][bookings.periodID]*1;
          }
        }
      }
      else {
        _that.logistics.addExtraItem(data['extra_item_id'],data['title']);
        console.log(JSON.stringify(bookings.periodID,null,2));
        _that.logistics.add(data['extra_item_id'],'delivery',_that.chosenPeriod['start_date'].replace(/-/g,'')+_that.chosenPeriod['start_time'].replace(/:/g,''));
        _that.logistics.add(data['extra_item_id'],'collection',_that.chosenPeriod['end_date'].replace(/-/g,'')+_that.chosenPeriod['end_time'].replace(/:/g,''));
      }
      document.querySelector('#bookingEntryAttachment').style.display='none';
      
      template=_this['prepare'+entryType.ucFirst()+'ItemTemplate'](data);
      entryID=_this.total;
      console.log('processing entry'+entryID);
      
      document.querySelector('#bookingContent .list tbody.items')
        .insertAdjacentHTML('beforeend',template.replace(/_X_/g,entryID));

      _this.active[_this.total]={'type':'item','itemID':((entryType=='regular')?data['item_id']:data['extra_item_id'])};/** /
      if (typeof(data['extra_item_id'])!='undefined'){
        _this.active[_this.total]['extraitem_id']=
      }/**/

      _this.partialData.discountTypes[entryID]='percentage';
      _this.partialData.discounts[entryID]=0;
      _this.partialData.multipliedPrices[entryID]=0;
      
      _this.total++;
      _this.updatePrice(entryID);
      
      return true;
    }
    ,'addService':function(data){
       var _this=this
        ,data=data
        ,entryID=0,template='';
      
      //console.log("addService\n"+JSON.stringify(data,null,2));
      document.querySelector('#bookingEntryAttachment').style.display='none';
      
      template=document.querySelector('#bookingTemplates .serviceTemplate').innerHTML
        .replace('_EXTRA_ITEM_ID_',data['extra_item_id'])
        .replace('_TITLE_',data['title'])
        .replace('_QUANTITY_',data['quantity'])
        .replace('_PEOPLE_',data['people'])
        .replace('_PRICE_',data['price'])
.replace('_INITIAL_DISCOUNT_TYPE_',data['discount_type'])
        .replace('_DISCOUNT_VALUE_',data['discount_value'])
        .replace('checked="_PERCENTAGE_DISCOUNT_"',((data['discount_type']=='percentage')?'checked="true"':''))
        .replace('checked="_AMOUNT_DISCOUNT_"',((data['discount_type']=='amount')?'checked="true"':''))
        .replace('_DESCRIPTION_',data['description']);

      entryID=_this.total;
      
      document.querySelector('#bookingContent .list tbody.services')
        .insertAdjacentHTML('beforeend',template.replace(/_X_/g,entryID));

      _this.active[_this.total]={'type':'service'};
      
      
      _this.partialData.discountTypes[entryID]='percentage';
      _this.partialData.discounts[entryID]=0;
      _this.partialData.multipliedPrices[entryID]=0;
      _this.updatePrice(entryID);
      _this.total++;
    }
    ,'prepareAdditionalItemTemplate':function(data){
      var template=document.querySelector('#bookingTemplates .additionalItemTemplate').innerHTML;
      
      template=template
        .replace('_EXTRA_ITEM_ID_',data['extra_item_id'])
        //.replace('_THUMBNAIL_','<?php echo NS_BASE_URL; ?>uploads/'+data.folder+'/'+data.filename)
        .replace('_TITLE_',data['title'])
        .replace('_QUANTITY_',data['quantity'])
        .replace('_PRICE_',data['price'])
        .replace('_INITIAL_DISCOUNT_TYPE_',data['discount_type'])
        .replace('_DISCOUNT_VALUE_',data['discount_value'])
        .replace('checked="_PERCENTAGE_DISCOUNT_"',((data['discount_type']=='percentage')?'checked="true"':''))
        .replace('checked="_AMOUNT_DISCOUNT_"',((data['discount_type']=='amount')?'checked="true"':''))
        .replace('_DESCRIPTION_',data['description']);

      return template;
    }
    ,'prepareRegularItemTemplate':function(data){
      var data=data,template=document.querySelector('#bookingTemplates .regularItemTemplate').innerHTML
        ,maxQuantity=data.quantity,attachedQuantity=0,q=0,qb=[]
        ,variationID=0
        ,discountType='percentage',discountValue='0';
      
      
      if (typeof(data['max_quantity'])!='undefined'){
        maxQuantity=data['max_quantity'];
      }
      else {
        if (data['fixed_quantity']!=null){
          maxQuantity=data['fixed_quantity'];
        }
        maxQuantity=maxQuantity-data['booked'];
        data['max_quantity']=maxQuantity;
      }

      if (typeof(data['attached_quantity'])!='undefined'){
        attachedQuantity=data['attached_quantity'];
      }
      else {
        attachedQuantity=0;
      }
      
      if (typeof(data['discount_type'])!='undefined'){
        discountType=data['discount_type'];
        discountValue=data['discount_value'];
      }
    
      template=template
        .replace('_THUMBNAIL_','<?php echo NS_BASE_URL; ?>uploads/'+data.folder+'/'+data.filename)
        .replace('_TITLE_',data.title)
        .replace('_QUANTITY_LABEL_',maxQuantity)
        .replace('_MAX_QUANTITY_',maxQuantity)
        .replace('_QUANTITY_',attachedQuantity)
        .replace('_INITIAL_DISCOUNT_TYPE_',discountType)
        .replace('_DISCOUNT_VALUE_',discountValue)
        .replace('checked="_PERCENTAGE_DISCOUNT_"',((discountType=='percentage')?'checked="true"':''))
        .replace('checked="_AMOUNT_DISCOUNT_"',((discountType=='amount')?'checked="true"':''))
        .replace('_START_PRICE_',data['price'])
        .replace('_DESCRIPTION_',data['description'])
        .replace('_DISCOUNT_VALUE_',discountValue)
        .replace(/_I_/g,data['item_id']);
        
      return template;
    }
  ,'updatePrice':function(entryID){
    var _this=this,entryID=entryID
    ,itemID=0,packedItemID=0,extraQuantity={},variationQuantities=this.quantities
    ,deltaQuantity=0,packageID=0
    ,variationElement=document.querySelector('#bookingForm #bookingContent')
    ,quantityElement=variationElement.querySelector('*[name="entry['+entryID+'][quantity]"]')
    ,quantity=0,chargeableDays=0,multipliedPrice=0
    ,discountType='percentage',previousDiscountType='',discountValue=0,fullDiscount=0
    ,fullPrice=0,errors=[];

    quantity=quantityElement.value;
    chargeableDays=parseFloat(document.querySelector('#bookingForm input[name="chargeable_days"]').value);
    
    console.log('updatePrice '+JSON.stringify(_this.active[entryID],null,2));
    switch(_this.active[entryID]['type']){
      case 'item':
        itemID=_this.active[entryID]['itemID'];
        if (itemID.charAt(0)=='E'){
          bookings.logistics.updateQuantity(itemID,quantity);
        }
        else if (itemID>0){
          //if (quantityElement.dataset['previous_quantity']!=quantity){
            if (typeof(itemCache[itemID]['packed'])!='undefined'){
              for(packedItemID in itemCache[itemID]['packed']){
                extraQuantity[packedItemID]=quantity*itemCache[itemID]['packed'][packedItemID]['quantity'];
                deltaQuantity=-((itemCache[packedItemID]['availability'][bookings.periodID]-variationQuantities[packedItemID]['total'])-(extraQuantity[packedItemID]-variationQuantities[packedItemID]['parts'][itemID]));
                if (deltaQuantity>0){
                  errors.push('Overlap detected for '+deltaQuantity+'x'+itemCache[packedItemID]['title']+' which are: ');
                  for(packageID in variationQuantities[packedItemID]['parts']){

                    if (packageID!=packedItemID){
                      console.log('checking '+packageID+' in '+packedItemID);
                      errors.push(Math.ceil(deltaQuantity/itemCache[packageID]['packed'][packedItemID]['quantity'])+'x'+itemCache[packageID]['title']);
                    }
                  }
                }
              }
            }
            else {
              extraQuantity[itemID]=quantity;
              deltaQuantity=-((itemCache[itemID]['availability'][bookings.periodID]-variationQuantities[itemID]['total'])-(extraQuantity[itemID]-variationQuantities[itemID]['parts'][itemID]));
              if (deltaQuantity>0){
                errors.push('Overlap detected for '+deltaQuantity+'x'+itemCache[itemID]['title']+' which are: ');
                for(packageID in variationQuantities[itemID]['parts']){
                  if (packageID!=itemID){
                    errors.push(Math.ceil(deltaQuantity/itemCache[packageID]['packed'][itemID]['quantity'])+'x'+itemCache[packageID]['title']);
                  }
                }
              }
            }

            if (errors.length>0){
              //errors.push('Quantity for '+itemCache[itemID]['title']+' will be set back to previous valid value of '+quantityElement.dataset['previous_quantity']);
              errors.push('Background for invalid quantities will be set to red until updated to valid values');
              _NS.alert.open('fail','Change of quantity for '+itemCache[itemID]['title']+' to '+quantity+' produced the following problems:',errors.join('<br/>'),5);
              
              document.querySelectorAll('#bookingContent tr.entry'+entryID).forEach(function(tr){
                _NS.DOM.addClass(tr,'danger');
                //tr.style['background-color']='#FF9999';
              });
              //quantityElement.value=quantityElement.dataset['previous_quantity'];
              return false;
            }
            document.querySelectorAll('#bookingContent tr.entry'+entryID).forEach(function(tr){
              _NS.DOM.removeClass(tr,'danger');
              //tr.style['background']='none';
            });
            quantityElement.dataset['previous_quantity']=quantity;
            for(packedItemID in extraQuantity){
              variationQuantities[packedItemID]['total']-=variationQuantities[packedItemID]['parts'][itemID]-extraQuantity[packedItemID];
              variationQuantities[packedItemID]['parts'][itemID]=extraQuantity[packedItemID];
              bookings.logistics.updateQuantity(packedItemID,variationQuantities[packedItemID]['total']);
            }/** /
          }
          else {
            document.querySelectorAll('#bookingContent tr.entry'+entryID).forEach(function(tr){
              _NS.DOM.removeClass(tr,'danger');
            });
          }/**/
        }
        else {
          
        }
        
    multipliedPrice=(parseFloat(variationElement.querySelector('input[name="entry['+entryID+'][price]"]').value)*quantity*chargeableDays).toFixed(2);

      break;
      case 'service':
        multipliedPrice=(parseFloat(variationElement.querySelector('input[name="entry['+entryID+'][price]"]').value)*parseFloat(variationElement.querySelector('input[name="entry['+entryID+'][people]"]').value)*quantity).toFixed(2);
      break;
    }


    previousDiscountType=variationElement.querySelector('input[name="entry['+entryID+'][previous_discount_type]"]').value;
    discountType=variationElement.querySelector('input[name="entry['+entryID+'][discount_type]"]:checked').value;
    discountValue=parseFloat(variationElement.querySelector('input[name="entry['+entryID+'][discount_value]"]').value);
    if (discountType!=previousDiscountType && previousDiscountType!=''){
      switch(discountType){
        case 'percentage':
          discountValue=((multipliedPrice>0)?(100*discountValue/multipliedPrice):0).toFixed(2);
        break;
        case 'amount':
          discountValue=parseFloat(multipliedPrice*discountValue/100).toFixed(2);
        break;
      }
      
    }
    variationElement.querySelector('input[name="entry['+entryID+'][previous_discount_type]"]').value=discountType;
    variationElement.querySelector('input[name="entry['+entryID+'][discount_value]"]').value=discountValue;

    switch(discountType){
      case 'percentage':
        fullDiscount=(multipliedPrice*discountValue/100).toFixed(2);
      break;
      case 'amount':
        fullDiscount=discountValue.toFixed(2);
      break;
    }
    fullPrice=(multipliedPrice-fullDiscount).toFixed(2);
    
    variationElement.querySelector('.fullDiscount'+entryID).innerHTML=fullDiscount;
    variationElement.querySelector('.fullPrice'+entryID).innerHTML=fullPrice;
    variationElement.querySelector('.multipliedPrice'+entryID).innerHTML=multipliedPrice;

    _this.partialData.multipliedPrices[entryID]=multipliedPrice;
    _this.partialData.discounts[entryID]=fullDiscount;
    _this.recalculate();
  }
  ,'recalculate':function(updateRequired){
    var _this=this,entryID=0,variationElement=null,totals={
      'item':{'multipliedPrice':0,'discount':0,'subtotal':0}
      ,'service':{'multipliedPrice':0,'discount':0,'subtotal':0}
    },type='item',subtotal=0
    ,updateRequired=updateRequired
    ,discountType='percentage',discountValue=0,discountAmount=0;

    if (typeof(updateRequired)=='undefined'){
      updateRequired=false;
    }
    //console.log('recalculate');
    variationElement=document.querySelector('#bookingForm #bookingContent');
    
    for(entryID in _this.active){
      //console.log('entry'+entryID+JSON.stringify(_this.active[entryID]));
      if (entryID!='total' && _this.active[entryID]!==null){
        if (updateRequired==true){
          _this.updatePrice(entryID);
        }
        type=_this.active[entryID]['type'];
        totals[type]['discount']+=parseFloat(_this.partialData.discounts[entryID]);
        totals[type]['multipliedPrice']+=parseFloat(_this.partialData.multipliedPrices[entryID]);
      }
    }
    subtotal=parseFloat(totals['item']['multipliedPrice']-totals['item']['discount']+totals['service']['multipliedPrice']-totals['service']['discount']).toFixed(2);
    
    if (variationElement.querySelector('input[name="discount_type"]:checked')){
      discountType=variationElement.querySelector('input[name="discount_type"]:checked').value;
    }
    else {
      discountType='percentage';
      variationElement.querySelector('input[name="previous_discount_type"]').value='percentage';
    }
    discountValue=parseFloat(variationElement.querySelector('input[name="discount_value"]').value);
   console.log('Subtotal: '+subtotal+'; Discount: '+discountType+'('+discountValue+')');
    if (discountType!=variationElement.querySelector('input[name="previous_discount_type"]').value){
      console.log(discountType+' VS '+variationElement.querySelector('input[name="previous_discount_type"]').value);
      switch(discountType){
        case 'percentage':
          discountValue=((subtotal>0)?parseFloat(100*discountValue/subtotal).toFixed(2):0);
        break;
        case 'amount':
          discountValue=parseFloat(subtotal*discountValue/100).toFixed(2);
        break;
      }
      variationElement.querySelector('input[name="previous_discount_type"]').value=discountType;
    }
    console.log('discount value: '+discountValue+'; new type: '+variationElement.querySelector('input[name="previous_discount_type"]').value);
    
    variationElement.querySelector('input[name="discount_value"]').value=discountValue;

    switch(discountType){
      case 'percentage':
        discountAmount=parseFloat(subtotal*discountValue/100).toFixed(2);
      break;
      case 'amount':
        discountAmount=parseFloat(discountValue).toFixed(2);
      break;
    }
    
    totals['final']={
      'total':(subtotal-discountAmount)
      ,'raw':parseFloat(totals['item']['multipliedPrice']+totals['service']['multipliedPrice']).toFixed(2)
      ,'discount':(parseFloat(discountAmount)+parseFloat(totals['item']['discount'])+parseFloat(totals['service']['discount']))
    };
    
    totals['final']['discountPercentage']=(totals['final']['raw']>0)?(totals['final']['discount']/totals['final']['raw']*100).toFixed(2):0;

    variationElement.querySelector('.totalItemMultipliedPrice').innerHTML=parseFloat(totals['item']['multipliedPrice']).toFixed(2);
    variationElement.querySelector('.totalItemDiscount').innerHTML='-'+parseFloat(totals['item']['discount']).toFixed(2);
    variationElement.querySelector('.totalItemPrice').innerHTML=parseFloat(totals['item']['multipliedPrice']-totals['item']['discount']).toFixed(2);
    variationElement.querySelector('.totalServiceMultipliedPrice').innerHTML=parseFloat(totals['service']['multipliedPrice']).toFixed(2);
    variationElement.querySelector('.totalServiceDiscount').innerHTML='-'+parseFloat(totals['service']['discount']).toFixed(2);
    variationElement.querySelector('.totalServicePrice').innerHTML=parseFloat(totals['service']['multipliedPrice']-totals['service']['discount']).toFixed(2);
    variationElement.querySelector('.subtotalPrice').innerHTML=parseFloat(subtotal).toFixed(2);
    
    variationElement.querySelector('.finalRawPrice').innerHTML=totals['final']['raw'];
    variationElement.querySelector('.finalDiscount').innerHTML='-'+totals['final']['discount'].toFixed(2)+'('+totals['final']['discountPercentage']+'%)';
    variationElement.querySelector('.finalPrice').innerHTML=totals['final']['total'].toFixed(2);
  }
  ,'remove':function(entryID){
    var _this=this,_that=bookings, itemID='';
    _this.active[entryID]=null;
    itemID=document.querySelector('#bookingForm #bookingContent .entry'+entryID+' input[name$="item_id]"]').value;
    document.querySelectorAll('#bookingForm #bookingContent .entry'+entryID).forEach(function(r){
      r.remove();
    });

    _that.logistics.remove(itemID,'delivery');
    _that.logistics.remove(itemID,'collection');
    _NS.alert.close();
  }
};

bookings['logistics']={
  'itemIDs':{}
  ,'delivery':{}
  ,'collection':{}
  ,'reset':function(){
    var _this=this;
    _this.itemIDs={};
    _this.delivery={};
    _this.collection={};

    document.querySelector('#bookingLogistics .deliveryPanel').innerHTML='';
    document.querySelector('#bookingLogistics .collectionPanel').innerHTML='';
  }
  ,'addExtraItem':function(itemID,title){
    var _this=this,itemID=itemID,title=title;
    if (typeof(_this.itemIDs[itemID])=='undefined'){
      _this.itemIDs[itemID]={'quantity':0,'title':title};
    }
  }
  ,'add':function(itemID,mode,code){
    var _this=this;

    if (typeof(_this.itemIDs[itemID])=='undefined'){
      _this.itemIDs[itemID]={'quantity':0};
    }
    if (typeof(_this.itemIDs[itemID][mode])=='undefined'){
      _this.itemIDs[itemID][mode]=code;
      //console.log(mode+' for item '+itemID+' is not defined');
      if (typeof(_this[mode][code])=='undefined'){
        _this[mode][code]={};
      }
      if (typeof(_this[mode][code][itemID])=='undefined'){
        _this[mode][code][itemID]=1;
      }
    }

    _this.render(mode,code);
  }
  ,'remove':function(itemID,mode){
    var _this=this,itemID=itemID,mode=mode,code='';
    code=_this.itemIDs[itemID][mode]+'';
    delete _this[mode][code][itemID];
    delete _this.itemIDs[itemID][mode];
    _this.render(mode,code);
  }
  ,'render':function(mode,code){
    var _this=this,itemID='',result=[]
    ,container=document.querySelector('#bookingForm #bookingLogistics .'+mode+'Panel .'+mode+code)
    ,dateString=(new Date(code.substr(0,4),(parseInt(code.substr(4,2))-1),code.substr(6,2),code.substr(8,2),code.substr(10,2))).toLocaleString();
    for (itemID in _this[mode][code]){
      if (_this[mode][code][itemID]==1){
        //console.log('attempt to render item '+itemID);
        if (itemID.charAt(0)=='E'){
          result.push(((_this.itemIDs[itemID]['quantity']>0)?'<span class="quantity'+itemID+'">'+_this.itemIDs[itemID]['quantity']+'</span> x ':'')+_this.itemIDs[itemID]['title']+' <a onclick="bookings.logistics.prepareUpdate(\''+itemID+'\',\''+mode+'\');">change</a>');
        }
        else if (typeof(itemCache[itemID])!='undefined'){
          result.push(((_this.itemIDs[itemID]['quantity']>0)?'<span class="quantity'+itemID+'">'+_this.itemIDs[itemID]['quantity']+'</span> x ':'')+itemCache[itemID]['title']+' <a onclick="bookings.logistics.prepareUpdate(\''+itemID+'\',\''+mode+'\');">change</a>');
        }
      }
    }
    if (result.length>0){
      if (container==null){
        document.querySelector('#bookingForm #bookingLogistics .'+mode+'Panel').insertAdjacentHTML('beforeend','<div class="'+mode+code+'"><label>'+dateString+'</label><ul></ul></div>');
      }
      document.querySelector('#bookingForm #bookingLogistics .'+mode+'Panel .'+mode+code+' ul').innerHTML='<li>'+result.join('</li><li>')+'</li>';
    }
    else if (container!=null) {
      container.remove();
    }
  }
  ,'updateQuantity':function(itemID,quantity){
    var _this=this,itemID=itemID,quantity=quantity,modes=['delivery','collection'];
    _this.itemIDs[itemID]['quantity']=quantity;
    modes.forEach(function(mode){
      _this.render(mode,_this.itemIDs[itemID][mode]);
    });
    return false;
    //_this.render('delivery',)
    document.querySelectorAll('#bookingForm #bookingLogistics .quantity'+itemID).forEach(function(i){
      i.innerHTML=quantity;
    });
  }
  ,'prepareUpdate':function(itemID,mode){
    var _this=this,itemID=itemID,mode=mode,dateParts=[],dateDirection='minDate',dateOppositeDirection='maxDate'
    ,container=document.querySelector('#bookingLogisticsUpdate');
    _NS.DOM.removeClass('#bookingLogisticsUpdate.hidden','hidden');
    _NS.DOM.resetFields('#bookingLogisticsUpdateForm');
    container.querySelector('.operationMode').innerHTML=mode;
    container.querySelector('.itemTitle').innerHTML=(itemID.charAt(0)=='E')?_this.itemIDs[itemID]['title']:itemCache[itemID]['title'];
    container.querySelector('input[name="item_id"]').value=itemID;
    container.querySelector('input[name="mode"]').value=mode;
    container.style.display='block';
    switch(mode){
      case 'delivery':
        dateDirection='maxDate';
        dateOppositeDirection='minDate';
      break;
      case 'collection':
        dateDirection='minDate';
        dateOppositeDirection='maxDate';
      break;
    }
    dateParts=document.querySelector('#bookingForm input[name="'+mode+'_date"]').value.split('-');
    //console.log(mode+' '+dateDirection+' dateParts: '+JSON.stringify(dateParts,null,2)+'; '+dateOppositeDirection);
    jQuery('#bookingLogisticsUpdateForm input[name="date"]').datepicker('option',dateOppositeDirection,null);
    jQuery('#bookingLogisticsUpdateForm input[name="date"]').datepicker('option',dateDirection,new Date(dateParts[0],(dateParts[1]-1),dateParts[2]));
  }
  ,'confirmUpdate':function(){
    var _this=this,data=_NS.DOM.getFormData('#bookingLogisticsUpdateForm')
    ,errors=[],dateToCompare='',timeToCompare='',timestampError=''
    ,datePattern=/^20(1[8-9]|[2-9][0-9])-(0[1-9]|1[0-2])-([0-2][0-9]|3[0-1])$/
    ,timePattern=/^([0-1]{1}[0-9]|2[0-3]):([0-5][0-9])$/;

    dateToCompare=document.querySelector('#bookingForm input[name="'+data.mode+'_date"]').value;
    timeToCompare=document.querySelector('#bookingForm input[name="'+data.mode+'_time"]').value;
      
    if (!datePattern.test(data.date)){
      errors.push('Date should be chosen');
    }
    if (!timePattern.test(data.time)){
      errors.push('Time should be chosen');
    }
    timestampError='New '+data.mode+' date and time should be ';
    switch(data.mode){
      case 'delivery':
        if (data.date>dateToCompare || data.date==dateToCompare && data.time>timeToCompare){
          errors.push(timestampError+'before rent start');
        }
      break;
      case 'collection':
        if (data.date<dateToCompare || data.date==dateToCompare && data.time<timeToCompare){
          errors.push(timestampError+'after rent end');
        }
      break;
    }

    if (errors.length>0){
      _NS.alert.open('fail','Fail',errors.join('<br/>'),2);
      return false;
    }
    _this.remove(data['item_id'],data['mode']);
    _this.add(data['item_id'],data['mode'],data['date'].replace(/-/g,'')+data.time.replace(':',''));
    _NS.DOM.addClass('#bookingLogisticsUpdate','hidden');

    _this.updateAvailability(data['item_id']);
  }
  ,'updateAvailability':function(itemID){
    var _this=this,itemID=itemID
      ,params={
        'itemID':itemID
        ,'unlock':{
          'id':document.querySelector('#bookingForm input[name="booking_id"]').value
          ,'type':'booking'
        }
        ,'period':{}
        ,'strictRentTimestamps':1
      }
      ,periodParts={'delivery':'start','collection':'end'},periodPart='';
    if (itemID.charAt(0)=='E'){
      return false;
    }

    for (periodPart in periodParts){
      params.period[periodParts[periodPart]+'_date']=_this.itemIDs[itemID][periodPart].substr(0,4)+'-'+_this.itemIDs[itemID][periodPart].substr(4,2)+'-'+_this.itemIDs[itemID][periodPart].substr(6,2);
      params.period[periodParts[periodPart]+'_time']=_this.itemIDs[itemID][periodPart].substr(8,2)+':'+_this.itemIDs[itemID][periodPart].substr(10,2);
    }

    _NS.post('<?php echo NS_BASE_URL; ?>item/filtered',params,{
      'success':function(reply){
        var itemData=null,localItemID=0,quantity=0,packedItemID=0,packedQuantity=0,separateQuantity='',itemsToRecalculate=[];
        itemData=reply.data.entries[0];
        itemCache[itemData['item_id']]['availability'][bookings.periodID]=((itemData['fixed_quantity']!==null)?itemData['fixed_quantity']:itemData['quantity'])-itemData['booked'];
        for (localItemID in bookings.entries.quantities[itemData['item_id']]['parts']){
          //console.log('checking '+localItemID);
          if (typeof(itemCache[localItemID]['packed'])!='undefined'){
            //console.log('is package');
            separateQuantity='';
            for (packedItemID in itemCache[localItemID]['packed']){
              packedQuantity=itemCache[packedItemID]['availability'][bookings.periodID]/itemCache[localItemID]['packed'][packedItemID]['quantity'];
              //console.log('packedID '+packedItemID+': '+packedQuantity);
              if (separateQuantity==''){
                separateQuantity=packedQuantity;
              }
              else if (packedQuantity<separateQuantity){
                separateQuantity=packedQuantity;
              }
            }
          }
          else {
            separateQuantity=itemCache[localItemID]['availability'][bookings.periodID];
          }
          //console.log(localItemID+': '+separateQuantity);
          if (document.querySelector('#bookingForm .attachedItem'+localItemID+' span.quantity')!=null){
            document.querySelector('#bookingForm .attachedItem'+localItemID+' span.quantity').innerHTML=separateQuantity;
          }
        }
        bookings.entries.recalculate(true);
      }
    },1);
  }
};

if (typeof(itemCache)=='undefined'){
  var itemCache={};
}
if (typeof(extraItemCache)=='undefined'){
  var extraItemCache={};
}
if (typeof(usersFromAutocomplete)=='undefined'){
  var usersFromAutocomplete={};
}
if (typeof(venuesFromAutocomplete)=='undefined'){
  var venuesFromAutocomplete={};
}

runWhenReady(function(){
  jQuery("#bookingForm :input[name=\"customer_search\"]").autocomplete({
    source: function( request, response ) {
      var request=request,response=response;
      request['search']={'value':request['term']};
      request['length']=10;
      request['role']='customer';
      _NS.post('<?php echo NS_BASE_URL; ?>user/filtered',request,{
        'success':function(reply){
          if (reply.data.entries.length==0){
            reply.data.entries.push({'user_id':0});
          }
          response(reply.data.entries);
        }
      },1);
    },
    minLength: 2
  })
  .autocomplete( "instance" )._renderItem = function( ul, user ) {
    var content='';
    usersFromAutocomplete[user.user_id]=user;
    if (user.user_id>0){
      content='<p>'+user.name+' ('+user.email+')</p><a class="btn btn-default" onclick="bookings.customer.choose(usersFromAutocomplete['+user['user_id']+']);"><?php echo $this->lang->phrase('choose'); ?></a>';
    }
    else {
      content='<?php echo $this->lang->phrase('not_found'); ?>';
    }
    return jQuery( '<li style="background-color:#FFFFFF;">' )
      .append(content)
      .appendTo( ul );
  };

  jQuery("#bookingEntryAttachment :input[name=\"item_search\"]").autocomplete({
    source: function( request, response ) {
      var request=request,response=response;
    
    request['search']={'value':request['term']};
      request['length']=10;
      request['period']=bookings.chosenPeriod;
      request['unlock']={
        'type':'booking','id':document.querySelector('#bookingForm input[name="booking_id"]').value
      };

      _NS.post('<?php echo NS_BASE_URL; ?>item/filtered',request,{
        'success':function(reply){
          if (reply.data.entries.length==0){
            reply.data.entries.push({'item_id':0});
          }
          response(reply.data.entries);
        }
      },1);
    },
    minLength: 2
  })
  .autocomplete( "instance" )._renderItem = function( ul, item ) {
    var content='';
    if (typeof(itemCache[item['item_id']])=='undefined'){
      itemCache[item.item_id]=item;
    }
    else {
      itemCache[item.item_id]=Object.assign(itemCache[item['item_id']],item);
    }/** 
    if (item['item_package_id']==0){
      if (typeof(itemCache[item.item_id]['availability'])=='undefined'){
        itemCache[item.item_id]['availability']={};
      }
      itemCache[item.item_id]['availability'][bookings.periodID]=item.quantity;
    }/**/
    if (item.item_id>0){
      content='<p>'+item.title+'<br/>'+item.description+'</p><a class="btn btn-default" onclick="bookings.entries.attachItem(itemCache['+item['item_id']+']);"><?php echo $this->lang->phrase('attach'); ?></a>';
    }
    else {
      content='<?php echo $this->lang->phrase('not_found'); ?>';
    }
    return jQuery( '<li style="background-color:#FFFFFF;">' )
      .append(content)
      .appendTo( ul );
  };
      
  //$('#bookingForm input[name="expiration_date"]').datepicker({'dateFormat':'yy-mm-dd','minDate':'today'});
  
  $('#bookingForm input[name="collection_date"]').datepicker({'dateFormat':'yy-mm-dd','minDate':'+1d'});
  
  $('#bookingForm input[name="delivery_date"]').datepicker({
    'dateFormat':'yy-mm-dd','minDate':'+1d'
    ,'onSelect':function(dateString,objectData){
      $('#bookingForm input[name="collection_date"]').datepicker('option','minDate',new Date(objectData.selectedYear,objectData.selectedMonth,objectData.selectedDay));
    }
  });

  $('#bookingLogisticsUpdateForm input[name="date"]').datepicker({'dateFormat':'yy-mm-dd','minDate':'today'});
  
  bookings.reset();
});
</script>