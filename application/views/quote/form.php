<script type="text/javascript">
if (typeof(quotes)=='undefined'){
  var quotes={'config':{}};
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
<form method="POST" action="<?php echo NS_BASE_URL . 'quote/save'; ?>" class="form-horizontal form-groups-bordered validate" target="_top" id="quoteForm" onsubmit="quotes.save();return false;">
<fieldset>
<input type="hidden" name="quote_id" value="0" data-reset_value="0"/>
<div class="row padded"><div class="col-sm-12">
  <div class="form-group">
    <label class="col-sm-3 col-md-2 control-label"><?php echo $this->lang->phrase('name'); ?></label>
    <div class="col-sm-9 col-md-10">
      <input type="text" class="form-control" name="name"/>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-3 col-md-2 control-label"><?php echo $this->lang->phrase('status'); ?></label>
    <div class="col-sm-9 col-md-10 quoteStatus">
      <table><tr><td><select name="status_id" class="form-control" onchange="quotes.updateStatus();"><option value="0" class="v0" data-code="none"><?php echo $this->lang->phrase('choose'); ?></option><?php foreach($this->reply['config']['statuses'] AS $status){
        echo '<option class="v'.$status['status_id'].'" data-code="'.$status['code'].'" value="'.$status['status_id'].'">'.$status['name'].'</option>';
        } ?></select></td><td class="ifActive hidden"> 
      <table><tr><td>&nbsp;until&nbsp;</td>
        <td><input type="text" class="form-control" name="expiration_date" placeholder="Date"/></td>
        <td><input type="text" class="form-control" name="expiration_time" placeholder="Time"/></td>
      </tr></table></td></tr></table>
    </div>
  </div>
<?php 
$customerDeliveryPrefix='quote';
include(VIEWPATH.'booking/form/customer_delivery.php');
?>
  <div class="form-group">
    <label class="col-sm-3 col-md-2 control-label"><?php echo $this->lang->phrase('timestamps'); ?></label>
    <div class="col-sm-9 col-md-10">
      <div class="row">
        <div class="col-sm-6">
          <label class="control-label"><?php echo $this->lang->phrase('delivery'); ?></label>
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
          <label class="control-label"><?php echo $this->lang->phrase('collection'); ?></label>
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
      <input type="text" class="form-control" name="chargeable_days" data-reset_value="1" value="1" onchange="quotes.variations.recalculate('all',true);"/>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-3 col-md-2 control-label"><?php echo $this->lang->phrase('deposit settings'); ?></label>
    <div class="col-sm-9 col-md-10">
      <table class="table table-condensed table-responsive">
          <thead><tr>
            <th><?php echo $this->lang->phrase('PURCHASE_ORDER'); ?></th>
            <th colspan="2"><?php echo $this->lang->phrase('DEPOSIT'); ?></th>
            <th colspan="2"><?php echo $this->lang->phrase('FINAL_DUE_DATE'); ?></th>
          </tr></thead>
          <tbody><tr>
            <td><input type="checkbox" name="purchase_order"  value="1"/></td>
            <td>
              <input type="radio" name="deposit_type"  data-reset_value="percentage" value="percentage" />&nbsp;%&nbsp;
              <input type="radio" name="deposit_type" value="amount"/>&nbsp;$
            </td>
            <td>
              <input class="form-control" type="text" name="deposit_value" data-reset_value="20" value="20"/>
            </td>
            <td>
              <input type="radio" name="due_direction" value="-" data-reset_value="-" /> -&nbsp;&nbsp;
              <input type="radio" name="due_direction" value="+"/> +
            </td>
            <td>
              <input class="form-control" type="text" name="due_days" data-reset_value="7" value="7"/>
            </td>
          </tr></tbody>
        </table>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-3 col-md-2">
      <div class="row">
        <label class="control-label col-xs-12"><?php echo $this->lang->phrase('Variations'); ?></label>
        <div class="col-xs-12"><a class="btn btn-sm btn-warning pull-right" onclick="quotes.variations.add();">Add</a></div>
      </div>
    </div>
    <div class="col-sm-9 col-md-10" id="quoteAttachedItems">
      <div id="quoteVariations"></div>
    </div>
  </div>  
  <div class="form-group">
    <div class="col-sm-offset-3 col-sm-9 col-md-offset-2 col-md-10">
      <a class="btn btn-primary" onclick="quotes.requestSave();"><?php echo $this->lang->phrase('save_quote'); ?></a>
    </div>
  </div>
  <?php //echo '<pre>'; print_r($this->reply['config']); echo '</pre>'; ?>
</div></div>
</fieldset>
</form>
<div id="quoteVariationEntryAttachment" style="display:none;position: fixed;top:0px;left:0px;bottom:0px;right:0px;width:100%;height:100%;background-color:rgba(0,0,0,0.5);padding: 5%;">
  
  <div class="panel panel-default">
    <div class="panel-heading">Here you can add new <span class="type"></span> to variations<span class="text-danger pull-right glyphicon glyphicon-remove" onclick="document.querySelector('#quoteVariationEntryAttachment').style.display='none';"></span></div>
    <div class="panel-body">
      <div class="row">
    <div class="col-xs-6">
      <p><b>Variations to choose from</b></p>
    <ul class="reservedVariations"></ul>
    </div>
    <div class="col-xs-6">
      <form id="quoteVariationItemForm" onsubmit="quotes.variations.attachItem(_NS.DOM.getFormData('#quoteVariationItemForm'));return false;">
      <input type="hidden" name="is_additional" data-reset_value="1"/>
      <input type="text" class="form-control" name="item_search" placeholder="<?php echo $this->lang->phrase('choose_some_items'); ?>">
      <hr/>OR<hr/>
      <input class="form-control" type="text" name="title" placeholder="Title"/>
      <textarea class="form-control" name="description" placeholder="Description"></textarea>
      <input class="form-control" name="quantity" placeholder="Quantity"/>
      <input class="form-control" name="price" placeholder="Price"/>
      <input type="radio" name="discount_type" value="percentage" data-reset_value="percentage"/>&nbsp;%&nbsp;
      <input type="radio" name="discount_type" value="amount"/>&nbsp;$
      <input type="text" class="form-control" name="discount_value" value="0"/>
      <a class="btn btn-warning" onclick="quotes.variations.attachItem(_NS.DOM.getFormData('#quoteVariationItemForm'));">Add new</a></form>
      <form id="quoteVariationServiceForm" onsubmit="quotes.variations.attachItem(_NS.DOM.getFormData('#quoteVariationServiceForm'));return false;">
      <input class="form-control" type="text" name="title" placeholder="Name"/>
      <textarea class="form-control" name="description" placeholder="Description"></textarea>
      
      <input class="form-control" name="price" placeholder="Rate"/>
      <input class="form-control" name="people" placeholder="No. staff"/>
      <input class="form-control" name="quantity" placeholder="Quantity"/>
      <input type="radio" name="discount_type" value="percentage" data-reset_value="percentage"/>&nbsp;%&nbsp;
      <input type="radio" name="discount_type" value="amount"/>&nbsp;$
      <input type="text" class="form-control" name="discount_value" value="0"/>
      <a class="btn btn-warning" onclick="quotes.variations.attachService(_NS.DOM.getFormData('#quoteVariationServiceForm'));">Add new</a></form>
    </div>
  </div>
    </div>
  </div>
</div>

<div id="quoteTemplates" class="hidden">
<?php include(__DIR__.'/form/template.php'); ?>
</div>
<script type="text/javascript">
if (typeof(quotes)=='undefined'){
  var quotes={'config':{}};
}


Object.assign(quotes,{
  'reset':function (){
    var _this=this, timestamp=(new Date()).getTime(),itemID=0;
    
    if (typeof(_this.config.tabsList)!='undefined'){
      $('#'+_this.config.tabsList+' li.edit .edit').addClass('hidden');
      $('#'+_this.config.tabsList+' li.edit .add').removeClass('hidden');
    }
    _NS.DOM.enable('#quoteForm .disabled');
    _NS.resetFields('#quoteForm');
    
    _this.address.resetDeliveryContactDetails();
    _this.updateStatus();
    _this.customer.reset();
    document.querySelector('#quoteForm select[name="residential_address_id"]').dataset['chosen']='';
    document.querySelector('#quoteForm select[name="delivery_address_id"]').dataset['chosen']='';

    for (itemID in itemCache){
      if (typeof(itemCache[itemID]['availability'])!='undefined'){
        itemCache[itemID]['availability']={};
      }
    }
    quotes.variations.reset();
  }
  ,'edit': function (ID){
    var _this=this;
    _NS.post('<?php echo NS_BASE_URL; ?>quote/edit','quote_id='+ID,{
      'success':function(reply){
        var contactField='';
        var deliveryField='';
        _this.reset();

        // document.querySelector('#HSPAN').innerHTML=reply.data['users_delivery_address'][0];
        _NS.fillFields('#quoteForm',reply.data);
        if(reply.data['residential_address_id']=='0'){
          reply.data['residential_address_id']='skip';
        }
        if(reply.data['delivery_address_id']=='0'){
          reply.data['delivery_address_id']='skip';
        } else {

          // document.querySelector('#quoteForm .contactDetails label span.'+contactField).innerHTML=reply.data['delivery_address_id'];

        }
        document.querySelector('#quoteForm select[name="residential_address_id"]').dataset['chosen']=reply.data['residential_address_id'];
        document.querySelector('#quoteForm select[name="delivery_address_id"]').dataset['chosen']=
        (reply.data['residential_address_id']!=reply.data['delivery_address_id'] || reply.data['delivery_address_id']=='skip')?
        reply.data['delivery_address_id']:'residential';
        quotes.customer.choose(reply.data['customer']);
        
        
        for(contactField in reply.data['delivery_contact']){
          document.querySelector('#quoteForm input[name="delivery_contact['+contactField+']"]').dataset['chosen']=reply.data['delivery_contact'][contactField];
          document.querySelector('#quoteForm .contactDetails label span.'+contactField).innerHTML=reply.data['delivery_contact'][contactField];
        }
        
        for(deliveryField in reply.data['delivery_address']){
          if (reply.data['delivery_address'][deliveryField] && document.querySelector('#quoteForm input[name="delivery_address['+deliveryField+']"]') && document.querySelector('#quoteForm .addressDetails label span.'+deliveryField)) {
            // document.querySelector('#quoteForm input[name="delivery_address['+'line_1'+']"]').value=reply.data['delivery_address'][deliveryField];
            document.querySelector('#quoteForm .addressDetails label span.'+deliveryField).innerHTML=reply.data['delivery_address'][deliveryField];
            document.querySelector('#quoteForm input[name="delivery_address['+deliveryField+']"]').dataset['chosen']=reply.data['delivery_address'][deliveryField];
            document.querySelector('#quoteForm input[name="delivery_address['+deliveryField+']"]').value=reply.data['delivery_address'][deliveryField];
          }
        }
        
        _this.updateStatus();
        
        itemCache=reply.itemCache;
        reply.data.variations.forEach(function(v){
          console.log(': '+JSON.stringify(v,null,2));
          quotes.variations.add(v);
        });
        
        document.querySelector('#quoteForm input[name="name"]').value=reply.data.name;
        
        if (typeof(_this.config.tabsList)!='undefined'){
          $('#'+_this.config.tabsList+' li.edit .add').addClass('hidden');
          $('#'+_this.config.tabsList+' li.edit .edit').removeClass('hidden');        
          document.querySelectorAll('#'+_this.config.tabsList+' a')[1].click();
        }
      }
    },1);
  }
  ,'updateStatus':function(){
    var chosenStatus=document.querySelector('#quoteForm .quoteStatus select[name="status_id"]').value,hide=true;
    switch(document.querySelector('#quoteForm .quoteStatus select[name="status_id"] option.v'+chosenStatus).dataset['code']){
      case 'active':
        hide=false;
      break;
      default:
      break;
    }
    if (hide){
      _NS.DOM.addClass('#quoteForm .quoteStatus .ifActive','hidden');
    }
    else {
      _NS.DOM.removeClass('#quoteForm .quoteStatus .ifActive','hidden');
    }
  }
  ,'requestSave':function(){
    var chosenStatus=document.querySelector('#quoteForm .quoteStatus select[name="status_id"]').value;
    switch(document.querySelector('#quoteForm .quoteStatus select[name="status_id"] option.v'+chosenStatus).dataset['code']){
      case 'active':
        _NS.alert.open('success','success','Success',2);
        this.save();
      break;
      case 'none':
        _NS.alert.open('fail','Fail','You should choose status first',2);
      break;
      case 'inactive':
        _NS.alert.confirm('quotes.save();','Quote will get inactive status and all allocated items will be released');
      break;
    }
  }
  ,'save': function () {
    var _this=quotes, data=_NS.DOM.getFormData('#quoteForm');
    _NS.alert.close();

    _NS.post('<?php echo NS_BASE_URL; ?>quote/save',data,{
      'success':function(reply){
        ['customer_id'].forEach(function(key){
          document.querySelector('#quoteForm input[name="'+key+'"]').value=reply.data[key];
        });
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
    var _this=quotes;
    if (typeof(_this.config.tabsList)!='undefined'){
      document.querySelectorAll('#'+_this.config.tabsList+' a')[0].click();
    }
    _this.reset();
  }
  ,'chooseCustomer':function(userData){
    var field='',el=null;
    _NS.DOM.disable('#quoteCustomer input');
    //document.querySelector('#quoteCustomer .chosen').innerHTML=userData['first_name']+' '+userData['last_name']+' ('+userData['email']+')';
    document.querySelector('#quoteCustomer input[name="customer_id"]').value=userData['user_id'];
    for(field in userData){
      el=document.querySelector('#quoteCustomer input[name="customer['+field+']"]');
      if (el!=null){
        el.value=userData[field];
      }
    }
    //document.querySelector('#quoteCustomer .choice').style.display='none';
    document.querySelector('#quoteCustomer .customerDetails').style.display='block';
    quotes.residentialAddress.updateList(userData['user_id']);
    quotes.deliveryAddress.updateList(userData['user_id']);
  }
  
  ,'periodID':''
  ,'chosenPeriod':false
  ,'updateChosenPeriod':function(){
    var errors=[]
      ,deliveryDate=document.querySelector('#quoteForm input[name="delivery_date"]').value
      ,deliveryTime=document.querySelector('#quoteForm input[name="delivery_time"]').value
      ,collectionDate=document.querySelector('#quoteForm input[name="collection_date"]').value
      ,collectionTime=document.querySelector('#quoteForm input[name="collection_time"]').value
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
    quotes.periodID=deliveryDate.replace(/-/g,'')+deliveryTime.replace(':','')+'_'+collectionDate.replace(/-/g,'')+collectionTime.replace(':','');

    quotes.chosenPeriod={
      'start_date':deliveryDate,'start_time':deliveryTime
      ,'end_date':collectionDate,'end_time':collectionTime
    };
    return true;
  }
});


quotes['variations']={
  'total':0
  ,'current':0
  ,'active':{}
  ,'discountTypes':{}
  ,'multipliedPrices':{}
  ,'discounts':{}
  ,'quantities':{}
  ,'entries':{}
  ,'values':{
    'fullPrice':{}
    ,'fullDiscount':{}
    ,'discountType':{}
  }
  ,'reset':function(){
    var _this=this, variationID=0;
    for(variationID in _this.active){
      _this.active[variationID]=0;
    }
    document.querySelector('#quoteVariations').innerHTML='';
    
  }
  ,'add':function(data){
    var _this=this,data=data,template=document.querySelector('#quoteVariationTemplate').innerHTML
    ,entries=[],name='',discountType='percentage',discountValue=0,quoteVariationID=0
    ,quotePurchaseOrderElement=document.querySelector('#quoteForm input[name="purchase_order"]:checked')
    ,purchaseOrder=0
    ,quoteDepositTypeElement=document.querySelector('#quoteForm input[name="deposit_type"]:checked')
    ,depositType=null,depositValue=0
    ,quoteDueDirectionElement=document.querySelector('#quoteForm input[name="due_direction"]:checked')
    ,dueDirection=null,dueDays=0,notes='';
    
    if (quotePurchaseOrderElement!=null){
      purchaseOrder=quotePurchaseOrderElement.value;
    }
    if (quoteDepositTypeElement!=null){
      depositType=quoteDepositTypeElement.value;
    }
    depositValue=document.querySelector('#quoteForm input[name="deposit_value"]').value;
    if (quoteDueDirectionElement!=null){
      dueDirection=quoteDueDirectionElement.value;
    }
    dueDays=document.querySelector('#quoteForm input[name="due_days"]').value;
    
    if (typeof(data)!='undefined'){
      name=data['name'];
      quoteVariationID=data['quote_variation_id'];
      discountType=data['discount_type'];
      discountValue=data['discount_value'];
      entries=JSON.parse(data['entries_json']);
      purchaseOrder=data['purchase_order'];
      depositType=data['deposit_type'];
      depositValue=data['deposit_value'];
      dueDays = 1 * dueDays;
      if (data['due_days']<0){
        dueDirection='-';
        dueDays = dueDays * -1;
      }
      else {
        dueDirection='+';
      }
      // dueDays=Math.abs(data['due_days']);
      if (data['notes']!=null){
        notes=data['notes'];
      }
      //console.log('variationData: '+JSON.stringify(data,null,2));
    }
    
    
    if (quotes.updateChosenPeriod()){
      template=template
        .replace('_NAME_',name)
        .replace('_QUOTE_VARIATION_ID_',quoteVariationID)
        .replace('_INITIAL_DISCOUNT_TYPE_',discountType)
        .replace('_DISCOUNT_VALUE_',discountValue)
        .replace('checked="_PERCENTAGE_DISCOUNT_"',((discountType=='percentage')?'checked="true"':''))
        .replace('checked="_AMOUNT_DISCOUNT_"',((discountType=='amount')?'checked="true"':''))
        .replace(/_X_/g,_this.total);

      document.querySelector('#quoteVariations').insertAdjacentHTML('beforeend',template);
      //console.log('#quoteForm input[name="variation['+_this.total+'][notes]"]');
      document.querySelector('#quoteForm textarea[name="variation['+_this.total+'][notes]"]').value=notes;

      if (purchaseOrder==1){
        document.querySelector('#quoteForm input[name="variation['+_this.total+'][purchase_order]"]').checked=true;
      }
      if (depositType!=null){
        document.querySelector('#quoteForm input[name="variation['+_this.total+'][deposit_type]"][value="'+depositType+'"]').checked=true;
      }
      document.querySelector('#quoteForm input[name="variation['+_this.total+'][deposit_value]"]').value=depositValue;
      if (dueDirection!=null){
        document.querySelector('#quoteForm input[name="variation['+_this.total+'][due_direction]"][value="'+dueDirection+'"]').checked=true;
      }
      document.querySelector('#quoteForm input[name="variation['+_this.total+'][due_days]"]').value=dueDays;

      _this.active[_this.total]=1;

      _this.discounts[_this.total]={};
      _this.discountTypes[_this.total]={};
      _this.multipliedPrices[_this.total]={};
      _this.quantities[_this.total]={};
      
      if (typeof(data)!='undefined' && typeof(data.items)!='undefined'){
        data.items.forEach(function(e){
          e['attached_quantity']=e['quantity']*1;
          e['price']=e['unit_price'];
          //console.log("item: \n"+JSON.stringify(e,null,2));
          quotes.variations.entries.addItem(_this.total,'regular',Object.assign({},itemCache[e['item_id']],e));
        });
      }

      entries.forEach(function(e){
        //console.log(JSON.stringify(e,null,2));
        switch(e.type){
          case 'service':
            quotes.variations.entries.addService(_this.total,e);
          break;
          case 'additionalItem':
            quotes.variations.entries.addItem(_this.total,'additional',e);
          break;
          case 'regularItem':
            e['attached_quantity']=e['quantity']*1;
            //console.log("clean cache "+e['item_id']+"\n"+JSON.stringify(itemCache[e['item_id']],null,2));
            //console.log('item to load '+e['item_id']+"\n"+JSON.stringify(e,null,2));
            quotes.variations.entries.addItem(_this.total,e.type.replace('Item',''),Object.assign({},itemCache[e['item_id']],e));
          break;
        }
      });
      _this.total++;
    }
  }
  ,'remove':function(variationID){
    var _this=this,variationID=variationID, el=document.querySelector('#quoteVariations #variation'+variationID),entryID=0;
    if (el.querySelector('input[name="variation['+variationID+'][quote_variation_id]"]').value==0){
      el.remove();
    }
    else {
      for(entryID in _this.entries.active[variationID]){
        if (entryID!='total' && _this.entries.active[variationID][entryID]!==null){
          _this.entries.remove(variationID,entryID);
        }
      }
      document.querySelector('#quoteVariations #variation'+variationID+' > .panel-body').className='hidden';
      //document.querySelector('#quoteVariations #variation'+variationID+' > .panel-footer').remove();
      document.querySelector('#quoteVariations #variation'+variationID+' input[name="variation['+variationID+'][remove]"]').value=1;
      el.className='panel panel-danger';
      _NS.DOM.disable(el.querySelectorAll('input'));
    }
    this.active[variationID]=0;
    _NS.alert.close();
  }
  ,'requestEntry':function(type,targetVariationID){
    var _this=this,type=type,targetVariationID=targetVariationID,variationID=0
    ,variations=[],variationName=''
    ,el=document.querySelector('#quoteVariationEntryAttachment');
    
    if (quotes.updateChosenPeriod()){
      el.querySelector('.panel-heading span.type').innerHTML=type;
      el.querySelectorAll('form').forEach(function(f){
        _NS.DOM.resetFields(f);
        f.className='hidden';
      });
      switch(type){
        case 'service':
          el.querySelector('#quoteVariationServiceForm').className='';
        break;
        default:
          el.querySelector('#quoteVariationItemForm').className='';
        break;
      }

      el.style.display='block';
      el.dataset['variationID']=variationID;

      for(variationID in _this.active){
        if (_this.active[variationID]==1){
          variationName=document.querySelector('#quoteForm #quoteVariations input[name="variation['+variationID+'][name]"]').value;
          variations.push('<input type="checkbox" name="reservedVariations[]" value="'+variationID+'" '+((variationID==targetVariationID)?' checked="checked"':'')+'/>&nbsp;'+((variationName=='')?'no name':variationName));
        }
      }
      el.querySelector('.reservedVariations').innerHTML='<li>'+variations.join('</li><li>')+'</li>';
    }
  }
  ,'attachItem':function(data){
    var _this=quotes.variations,data=data,type='regular';
    
    if (typeof(data['is_additional'])!='undefined'){
      type='additional';
    }
    document.querySelectorAll('#quoteVariationEntryAttachment .reservedVariations input:checked').forEach(function(c){
      if (_this.active[c.value]==1){
        if (type=='additional' || document.querySelector('#quoteForm #quoteVariations #variation'+c.value+' tbody.items .attachedItem'+data['item_id'])==null){
          _this.entries.addItem(c.value,type,data);
        }
      }
      else {
        console.log('no variation '+c.value+' is found');
      }
    });
  }
  ,'attachService':function(data){
    var _this=quotes.variations,data=data;
    
    document.querySelectorAll('#quoteVariationEntryAttachment .reservedVariations input:checked').forEach(function(c){
      if (_this.active[c.value]==1){
        _this.entries.addService(c.value,data);
      }
      else {
        console.log('no variation '+c.value+' is found');
      }
    });
  }
  ,'entries':{
    'active':{}
    
    ,'addItem':function(variationID,entryType,data){
      var _this=this,variationID=variationID,entryType=entryType
        ,data=data,variationQuantities=null
        ,entryID=0,template='',params={},periodID=''
        ,updateRequired=false,packedItemID=''
        ,maxAvailableQuantity='',packedItemQuantity=0;
      
      if (typeof(data['item_id'])!='undefined'){
        if (document.querySelector('#quoteForm #quoteVariations #variation'+variationID+' tbody.items .attachedItem'+data['item_id'])!=null){
          return false;
        }
      }
      //console.log("addItem\n"+JSON.stringify(data,null,2));
      if (entryType=='regular'){
        variationQuantities=quotes.variations.quantities[variationID];
        periodID=quotes.chosenPeriod['start_date'].replace(/-/g,'')+quotes.chosenPeriod['start_time'].replace(':','')+'_'+quotes.chosenPeriod['end_date'].replace(/-/g,'')+quotes.chosenPeriod['end_time'].replace(':','');
        
        params={'itemID':data['item_id'],'period':quotes.chosenPeriod,'skipQuote':document.querySelector('#quoteForm input[name="quote_id"]').value};
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
        
        if (updateRequired){/** /
          if (typeof(data['random_delay'])=='undefined'){
            data['random_delay']=parseInt(Math.floor(Math.random() * 25)) + parseInt(Math.floor(Math.random() * 25));
            setTimeout(function(variationID,entryType,data){_this.addItem(variationID,entryType,data);},data['random_delay'],variationID,entryType,data);
            return false;
          }/**/
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
                if (typeof(itemCache[itemData['item_id']]['availability'])=='undefined'){
                  itemCache[itemData['item_id']]['availability']={};
                }
                itemCache[itemData['item_id']]['availability'][periodID]=((itemData['fixed_quantity']!==null)?itemData['fixed_quantity']:itemData['quantity'])-itemData['booked'];
                
                if (params['isPackage']==1){
                  itemCache[params['itemID']]['packed'][itemData['item_id']]={
                    'quantity':itemData['packed_quantity']
                    ,'percentage':itemData['packed_percentage']
                  };
                }
              }
              _this.addItem(variationID,entryType,data);
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
              if (typeof(variationQuantities[packedItemID])=='undefined'){
                variationQuantities[packedItemID]={'total':0,'parts':{}};
              }
              if (typeof(variationQuantities[packedItemID]['parts'][params['itemID']])=='undefined'){
                variationQuantities[packedItemID]['parts'][params['itemID']]=0;
              }
              if (typeof(variationQuantities[packedItemID]['parts'][packedItemID])=='undefined'){
                variationQuantities[packedItemID]['parts'][packedItemID]=0;
              }
            }
            data['max_quantity']=maxAvailableQuantity;
          }
          else {
            if (typeof(itemCache[data['item_id']]['availability'])=='undefined'){
              itemCache[data['item_id']]['availability']={};
            }
            itemCache[data['item_id']]['availability'][quotes.periodID]=((itemCache[data['item_id']]['fixed_quantity']!==null)?itemCache[data['item_id']]['fixed_quantity']:itemCache[data['item_id']]['quantity'])-itemCache[data['item_id']]['booked'];

            if (typeof(variationQuantities[data['item_id']])=='undefined'){
              variationQuantities[data['item_id']]={'total':0,'parts':{}};
              variationQuantities[data['item_id']]['parts'][data['item_id']]=0;
            }
            data['max_quantity']=itemCache[data['item_id']]['availability'][quotes.periodID]*1;
          }
        }
      }
      document.querySelector('#quoteVariationEntryAttachment').style.display='none';
      
      template=_this['prepare'+entryType.ucFirst()+'ItemTemplate'](data);

      if (typeof(_this.active[variationID])=='undefined'){
        _this.active[variationID]={'total':0};
      }
      entryID=_this.active[variationID].total;
      
      document.querySelector('#quoteVariations #variation'+variationID+' .list tbody.items')
        .insertAdjacentHTML('beforeend',template.replace(/_V_/g,variationID).replace(/_X_/g,entryID));

      _this.active[variationID][_this.active[variationID].total]={'type':'item','itemID':((entryType=='regular')?data['item_id']:0)};
      _this.active[variationID].total++;

      quotes['variations'].discountTypes[variationID][entryID]='percentage';
      quotes['variations'].discounts[variationID][entryID]=0;
      quotes['variations'].multipliedPrices[variationID][entryID]=0;
      
      quotes['variations'].updatePrice(variationID,entryID);
      return true;
    }
    ,'addService':function(variationID,data){
       var _this=this,variationID=variationID
        ,data=data
        ,entryID=0,template='';
      
      console.log("addService\n"+JSON.stringify(data,null,2));
      document.querySelector('#quoteVariationEntryAttachment').style.display='none';
      
      template=document.querySelector('#quoteTemplates .serviceTemplate').innerHTML
        .replace('_TITLE_',data['title'])
        .replace('_QUANTITY_',data['quantity'])
        .replace('_PEOPLE_',data['people'])
        .replace('_PRICE_',data['price'])
.replace('_INITIAL_DISCOUNT_TYPE_',data['discount_type'])
        .replace('_DISCOUNT_VALUE_',data['discount_value'])
        .replace('checked="_PERCENTAGE_DISCOUNT_"',((data['discount_type']=='percentage')?'checked="true"':''))
        .replace('checked="_AMOUNT_DISCOUNT_"',((data['discount_type']=='amount')?'checked="true"':''))
        .replace('_DESCRIPTION_',data['description']);

      if (typeof(_this.active[variationID])=='undefined'){
        _this.active[variationID]={'total':0};
      }
      entryID=_this.active[variationID].total;
      
      document.querySelector('#quoteVariations #variation'+variationID+' .list tbody.services')
        .insertAdjacentHTML('beforeend',template.replace(/_V_/g,variationID).replace(/_X_/g,entryID));

      _this.active[variationID][_this.active[variationID].total]={'type':'service'};
      _this.active[variationID].total++;
      
      quotes['variations'].discountTypes[variationID][entryID]='percentage';
      quotes['variations'].discounts[variationID][entryID]=0;
      quotes['variations'].multipliedPrices[variationID][entryID]=0;
      quotes['variations'].updatePrice(variationID,entryID);
    }
    ,'prepareAdditionalItemTemplate':function(data){
      var template=document.querySelector('#quoteTemplates .additionalItemTemplate').innerHTML;
      
      
      template=template
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
      var data=data,template=document.querySelector('#quoteTemplates .regularItemTemplate').innerHTML
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
        .replace(/_I_/g,data['item_id']);
      
      return template;
    }
    ,'remove':function(variationID,entryID){
      var _this=this,variationID=variationID,entryID=entryID;
      document.querySelector('#quoteForm #quoteVariations #variation'+variationID+' *[name="variation['+variationID+']['+entryID+'][quantity]"]').value=0;
      quotes.variations.updatePrice(variationID,entryID);
      
      _NS.DOM.disable('#quoteForm #quoteVariations #variation'+variationID+' *[name^="variation['+variationID+']['+entryID+']');
      
      if (document.querySelector('#quoteForm #quoteVariations #variation'+variationID+' *[name^="variation['+variationID+'][quote_variation_id]').value==0 || document.querySelector('#quoteForm #quoteVariations #variation'+variationID+' *[name="variation['+variationID+']['+entryID+'][type]"]').value!='regularItem'){
        document.querySelectorAll('#quoteForm #quoteVariations #variation'+variationID+' .entry'+entryID).forEach(function(el){el.remove();});
      }
      _this.active[variationID][entryID]=null;
      //quotes['variations'].recalculate(variationID);
      _NS.alert.close();
    }
  }
  ,'updatePrice':function(variationID,entryID){
    var _this=this,variationID=variationID,entryID=entryID
    ,itemID=0,packedItemID=0,extraQuantity={},variationQuantities=quotes.variations.quantities[variationID]
    ,deltaQuantity=0,packageID=0
    ,variationElement=document.querySelector('#quoteForm #quoteVariations #variation'+variationID)
    ,quantityElement=variationElement.querySelector('*[name="variation['+variationID+']['+entryID+'][quantity]"]')
    ,quantity=0,chargeableDays=0,multipliedPrice=0
    ,discountType='percentage',discountValue=0,fullDiscount=0
    ,fullPrice=0,errors=[];

    quantity=quantityElement.value;
    chargeableDays=parseFloat(document.querySelector('#quoteForm input[name="chargeable_days"]').value);
    
    switch(_this.entries.active[variationID][entryID]['type']){
      case 'item':
        itemID=_this.entries.active[variationID][entryID]['itemID'];
        if (itemID>0){
          if (quantityElement.dataset['previous_quantity']!=quantity){
            if (typeof(itemCache[itemID]['packed'])!='undefined'){
              for(packedItemID in itemCache[itemID]['packed']){
                extraQuantity[packedItemID]=quantity*itemCache[itemID]['packed'][packedItemID]['quantity'];
                deltaQuantity=-((itemCache[packedItemID]['availability'][quotes.periodID]-variationQuantities[packedItemID]['total'])-(extraQuantity[packedItemID]-variationQuantities[packedItemID]['parts'][itemID]));
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
              deltaQuantity=-((itemCache[itemID]['availability'][quotes.periodID]-variationQuantities[itemID]['total'])-(extraQuantity[itemID]-variationQuantities[itemID]['parts'][itemID]));
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
              
              document.querySelectorAll('#variation'+variationID+' tr.entry'+entryID).forEach(function(tr){
                _NS.DOM.addClass(tr,'danger');
                //tr.style['background-color']='#FF9999';
              });
              //quantityElement.value=quantityElement.dataset['previous_quantity'];
              return false;
            }
            document.querySelectorAll('#variation'+variationID+' tr.entry'+entryID).forEach(function(tr){
              _NS.DOM.removeClass(tr,'danger');
              //tr.style['background']='none';
            });
            quantityElement.dataset['previous_quantity']=quantity;
            for(packedItemID in extraQuantity){
              variationQuantities[packedItemID]['total']-=variationQuantities[packedItemID]['parts'][itemID]-extraQuantity[packedItemID];
              variationQuantities[packedItemID]['parts'][itemID]=extraQuantity[packedItemID];
            }
          }
          else {
            document.querySelectorAll('#variation'+variationID+' tr.entry'+entryID).forEach(function(tr){
              _NS.DOM.removeClass(tr,'danger');
              //tr.style['background']='none';
            });
          }
        }
        
      multipliedPrice=(parseFloat(variationElement.querySelector('input[name="variation['+variationID+']['+entryID+'][price]"]').value)*quantity*chargeableDays).toFixed(2);

      break;
      case 'service':
        multipliedPrice=(parseFloat(variationElement.querySelector('input[name="variation['+variationID+']['+entryID+'][price]"]').value)*parseFloat(variationElement.querySelector('input[name="variation['+variationID+']['+entryID+'][people]"]').value)*quantity).toFixed(2);
      break;
    }



    discountType=variationElement.querySelector('input[name="variation['+variationID+']['+entryID+'][discount_type]"]:checked').value;
    discountValue=variationElement.querySelector('input[name="variation['+variationID+']['+entryID+'][discount_value]"]').value;
    if (discountType!=variationElement.querySelector('input[name="variation['+variationID+']['+entryID+'][previous_discount_type]"]').value){
      switch(discountType){
        case 'percentage':
          discountValue=((multipliedPrice>0)?(100*discountValue/multipliedPrice):0).toFixed(2);
        break;
        case 'amount':
          discountValue=(multipliedPrice*discountValue/100).toFixed(2);
        break;
      }
      variationElement.querySelector('input[name="variation['+variationID+']['+entryID+'][previous_discount_type]"]').value=discountType;
    }
    
    variationElement.querySelector('input[name="variation['+variationID+']['+entryID+'][discount_value]"]').value=discountValue;


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

    _this.multipliedPrices[variationID][entryID]=multipliedPrice;
    _this.discounts[variationID][entryID]=fullDiscount;
    _this.recalculate(variationID);
  }
  ,'recalculate':function(variationID,updateRequired){
    var _this=this,variationID=variationID,entryID=0,variationElement=null,totals={
      'item':{'multipliedPrice':0,'discount':0,'subtotal':0}
      ,'service':{'multipliedPrice':0,'discount':0,'subtotal':0}
    },type='item',subtotal=0
    ,updateRequired=updateRequired
    ,discountType='percentage',discountValue=0,discountAmount=0;

    if (variationID=='all'){
      for (variationID in _this.active){
        if (_this.active[variationID]==1){
          _this.recalculate(variationID,updateRequired);
        }
      }
      return false;
    }
    if (typeof(updateRequired)=='undefined'){
      updateRequired=false;
    }

    variationElement=document.querySelector('#quoteForm #quoteVariations #variation'+variationID);
    
    for(entryID in _this.entries.active[variationID]){
      if (entryID!='total' && _this.entries.active[variationID][entryID]!==null){
        if (updateRequired==true){
          _this.updatePrice(variationID,entryID);
        }
        type=_this.entries.active[variationID][entryID]['type'];
        totals[type]['discount']+=parseFloat(_this.discounts[variationID][entryID]);
        totals[type]['multipliedPrice']+=parseFloat(_this.multipliedPrices[variationID][entryID]);
      }
    }
    subtotal=totals['item']['multipliedPrice']-totals['item']['discount']+totals['service']['multipliedPrice']-totals['service']['discount'];
    
    
    discountType=variationElement.querySelector('input[name="variation['+variationID+'][discount_type]"]:checked').value;
    discountValue=parseFloat(variationElement.querySelector('input[name="variation['+variationID+'][discount_value]"]').value);
    if (discountType!=variationElement.querySelector('input[name="variation['+variationID+'][previous_discount_type]"]').value){
      switch(discountType){
        case 'percentage':
          discountValue=((subtotal>0)?(100*discountValue/totals[type]['subtotal']).toFixed(2):0);
        break;
        case 'amount':
          discountValue=(subtotal*discountValue/100).toFixed(2);
        break;
      }
      variationElement.querySelector('input[name="variation['+variationID+'][previous_discount_type]"]').value=discountType;
    }
    
    variationElement.querySelector('input[name="variation['+variationID+'][discount_value]"]').value=discountValue;

    switch(discountType){
      case 'percentage':
        discountAmount=(subtotal*discountValue/100).toFixed(2);
      break;
      case 'amount':
        discountAmount=discountValue.toFixed(2);
      break;
    }
    
    totals['final']={
      'total':(subtotal-discountAmount)
      ,'raw':(totals['item']['multipliedPrice']+totals['service']['multipliedPrice'])
      ,'discount':(parseFloat(discountAmount)+parseFloat(totals['item']['discount'])+parseFloat(totals['service']['discount']))
    };
    
    totals['final']['discountPercentage']=(totals['final']['raw']>0)?parseFloat(totals['final']['discount']/totals['final']['raw']*100).toFixed(2):0;

    variationElement.querySelector('.totalItemMultipliedPrice'+variationID).innerHTML=parseFloat(totals['item']['multipliedPrice']).toFixed(2);
    variationElement.querySelector('.totalItemDiscount'+variationID).innerHTML='-'+parseFloat(totals['item']['discount']).toFixed(2);
    variationElement.querySelector('.totalItemVariationPrice'+variationID).innerHTML=parseFloat(totals['item']['multipliedPrice']-totals['item']['discount']).toFixed(2);
    variationElement.querySelector('.totalServiceMultipliedPrice'+variationID).innerHTML=parseFloat(totals['service']['multipliedPrice']).toFixed(2);
    variationElement.querySelector('.totalServiceDiscount'+variationID).innerHTML='-'+parseFloat(totals['service']['discount']).toFixed(2);
    variationElement.querySelector('.totalServiceVariationPrice'+variationID).innerHTML=parseFloat(totals['service']['multipliedPrice']-totals['service']['discount']).toFixed(2);
    variationElement.querySelector('.subtotalVariationPrice'+variationID).innerHTML=parseFloat(subtotal).toFixed(2);
    
    variationElement.querySelector('.finalVariationRawPrice'+variationID).innerHTML=parseFloat(totals['final']['raw']).toFixed(2);
    variationElement.querySelector('.finalVariationDiscount'+variationID).innerHTML='-'+parseFloat(totals['final']['discount']).toFixed(2)+'('+parseFloat(totals['final']['discountPercentage']).toFixed(2)+'%)';
    variationElement.querySelector('.finalVariationPrice'+variationID).innerHTML=parseFloat(totals['final']['total']).toFixed(2);
  }
};


if (typeof(itemCache)=='undefined'){
  var itemCache={};
}
if (typeof(usersFromAutocomplete)=='undefined'){
  var usersFromAutocomplete={};
}
if (typeof(venuesFromAutocomplete)=='undefined'){
  var venuesFromAutocomplete={};
}

runWhenReady(function(){
  jQuery("#quoteForm :input[name=\"customer_search\"]").autocomplete({
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
      content='<p>'+user.name+' ('+user.email+')</p><a class="btn btn-default" onclick="quotes.customer.choose(usersFromAutocomplete['+user['user_id']+']);"><?php echo $this->lang->phrase('choose'); ?></a>';
    }
    else {
      content='<?php echo $this->lang->phrase('not_found'); ?>';
    }
    return jQuery( '<li style="background-color:#FFFFFF;">' )
      .append(content)
      .appendTo( ul );
  };

  jQuery("#quoteVariationEntryAttachment :input[name=\"item_search\"]").autocomplete({
    source: function( request, response ) {
      var request=request,response=response;
    
    request['search']={'value':request['term']};
      request['length']=10;
      request['period']=quotes.chosenPeriod;
      request['skipQuote']=document.querySelector('#quoteForm input[name="quote_id"]').value;

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
      itemCache[item.item_id]['availability'][quotes.periodID]=item.quantity;
    }/**/
    if (item.item_id>0){
      content='<p>'+item.title+'<br/>'+item.description+'</p><a class="btn btn-default" onclick="quotes.variations.attachItem(itemCache['+item['item_id']+']);"><?php echo $this->lang->phrase('attach'); ?></a>';
    }
    else {
      content='<?php echo $this->lang->phrase('not_found'); ?>';
    }
    return jQuery( '<li style="background-color:#FFFFFF;">' )
      .append(content)
      .appendTo( ul );
  };
      
  $('#quoteForm input[name="expiration_date"]').datepicker({'dateFormat':'yy-mm-dd'/** /,'minDate':'today'/**/});
  
  $('#quoteForm input[name="collection_date"]').datepicker({'dateFormat':'yy-mm-dd','minDate':'+1d'});
  
  $('#quoteForm input[name="delivery_date"]').datepicker({
    'dateFormat':'yy-mm-dd','minDate':'+1d'
    ,'onSelect':function(dateString,objectData){
      $('#quoteForm input[name="collection_date"]').datepicker('option','minDate',new Date(objectData.selectedYear,objectData.selectedMonth,objectData.selectedDay));
    }
  });
  
  quotes.reset();
});
</script>