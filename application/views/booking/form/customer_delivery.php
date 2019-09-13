<?php 
$venueAddresses=array();
$venueOptions=array();
foreach ($this->reply['config']['venues'] AS &$v){
  $venueAddresses[$v['address_id']]=$v;
  $venueOptions[]='<option class="v'.$v['address_id'].'" data-is_venue="1" value="'.$v['address_id'].'">Venue: '.$v['venue'].'</option>';
}
$venueMenu=join('',$venueOptions);

?>
<div class="form-group">
    <div class="col-sm-3 col-md-2">
      <div class="row">
        <label class="control-label col-xs-12"><?php echo $this->lang->phrase('customer'); ?></label>
        <div class="col-xs-12 newCustomer"><a class="btn btn-sm btn-warning pull-right" onclick="<?php echo $customerDeliveryPrefix; ?>s.customer.choose();">Add</a></div>
      </div>
    </div>
    <div class="col-sm-9 col-md-10" id="<?php echo $customerDeliveryPrefix; ?>Customer">
      <input type="hidden" name="customer_id" value="0"/>
      <div class="panel panel-default" style="margin:0px;">
          <div class="panel-body">
            <div class="choice">
              <input type="text" class="form-control" name="customer_search" placeholder="<?php echo $this->lang->phrase('choose_one'); ?>"/>
            </div>
            <div class="customerDetails" style="display: none;">
              <div class="row">
                <div class="col-sm-6">
                  <label class="control-label"><?php echo $this->lang->phrase('first name'); ?></label>
                  <input type="text" class="form-control" name="customer[first_name]"/>
                  <label class="control-label"><?php echo $this->lang->phrase('email'); ?></label>
                  <input type="text" class="form-control" name="customer[email]"/>
                </div>
                <div class="col-sm-6">
                  <label class="control-label"><?php echo $this->lang->phrase('last name'); ?></label>
                  <input type="text" class="form-control" name="customer[last_name]"/>
                  <label class="control-label"><?php echo $this->lang->phrase('phone'); ?></label>
                  <input type="text" class="form-control" name="customer[phone]"/>
                </div>
                <div class="col-sm-6">
                <label class="control-label">company</label>
                <input type="text" class="form-control" name="customer[company]" value=""/>
                </div>
              </div>
            </div>
          </div>
        </div>
    </div>
  </div>
  <div class="form-group residentialAddress">
    <label class="control-label col-sm-3 col-md-2"><?php echo $this->lang->phrase('residential address'); ?></label>
    <div class="col-sm-9 col-md-10">
      
      <div class="panel panel-default" style="margin:0px;">
          <div class="panel-body">
            <div>
            <select class="form-control" name="residential_address_id" onchange="<?php echo $customerDeliveryPrefix; ?>s.address.choose('residential');" data-chosen="">
              <option class="skip" value="skip"><?php echo $this->lang->phrase('skip'); ?></option>
              <!-- <option value="current" class="userAddresses"><?php echo $this->lang->phrase('current'); ?></option> -->
              <optgroup label="current" class="userAddresses"></optgroup>
              <option value="0"><?php echo $this->lang->phrase('add_new'); ?></option>
            </select>
            </div>
            <div class="addressDetails" style="display: none;">
              <div class="row">
                <div class="col-sm-6">
                  <label class="control-label"><?php echo $this->lang->phrase('line_1'); ?></label>
                  <input type="text" class="form-control" name="residential_address[line_1]"/>
                  <label class="control-label"><?php echo $this->lang->phrase('line_2'); ?></label>
                  <input type="text" class="form-control" name="residential_address[line_2]"/>
                  <label class="control-label"><?php echo $this->lang->phrase('phone'); ?></label>
                  <input type="text" class="form-control" name="residential_address[phone]"/>
                </div>
                <div class="col-sm-6">
                  <label class="control-label"><?php echo $this->lang->phrase('city'); ?></label>
                  <input type="text" class="form-control" name="residential_address[city]"/>
                  <label class="control-label"><?php echo $this->lang->phrase('state'); ?></label>
                  <input type="text" class="form-control" name="residential_address[state]"/>
                  <label class="control-label"><?php echo $this->lang->phrase('postcode'); ?></label>
                  <input type="text" class="form-control" name="residential_address[postcode]"/>
                </div>
              </div>
            </div>
          </div>
        </div>
    </div>
  </div>
  <div class="form-group deliveryAddress">
    <label class="col-sm-3 col-md-2 control-label"><?php echo $this->lang->phrase('delivery_address'); ?></label>
    <div class="col-sm-9 col-md-10" id="<?php echo $customerDeliveryPrefix; ?>Delivery">
        <div class="panel panel-default" title="residential address" style="margin:0px;">
          <div class="panel-body">
            <div class="deliveryAddressChoice">
              <select class="form-control" name="delivery_address_id" onchange="<?php echo $customerDeliveryPrefix; ?>s.address.choose('delivery');" data-chosen="">
              
              <optgroup label="Venues"><?php echo $venueMenu; ?></optgroup>
              <optgroup label="Other">
                <option class="skip" value="skip" selected="selected"><?php echo $this->lang->phrase('skip'); ?></option>
                <option class="residential" value="residential"><?php echo $this->lang->phrase('residential'); ?></option>
                <option value="0"><?php echo $this->lang->phrase('add_new'); ?></option>
              </optgroup>
              </select></div>
              <div class="row">
                <div class="col-sm-6 addressDetails">
                  <label class="control-label"><?php echo $this->lang->phrase('line_1'); ?>(<span class="line_1" onclick="<?php echo $customerDeliveryPrefix; ?>s.address.chooseOldDeliveryAddressDetails('line_1');"></span>)</label>
                  <input type="text" class="form-control" name="delivery_address[line_1]"/>
                  <label class="control-label"><?php echo $this->lang->phrase('line_2'); ?>(<span class="line_2" onclick="<?php echo $customerDeliveryPrefix; ?>s.address.chooseOldDeliveryAddressDetails('line_2');"></span>)</label>
                  <input type="text" class="form-control" name="delivery_address[line_2]"/>
                  <label class="control-label"><?php echo $this->lang->phrase('phone'); ?>(<span class="phone" onclick="<?php echo $customerDeliveryPrefix; ?>s.address.chooseOldDeliveryAddressDetails('phone');"></span>)</label>
                  <input type="text" class="form-control" name="delivery_address[phone]"/>
                </div>
                <div class="col-sm-6 addressDetails">
                  <label class="control-label"><?php echo $this->lang->phrase('city'); ?>(<span class="city" onclick="<?php echo $customerDeliveryPrefix; ?>s.address.chooseOldDeliveryAddressDetails('city');"></span>)</label>
                  <input type="text" class="form-control" name="delivery_address[city]"/>
                  <label class="control-label"><?php echo $this->lang->phrase('state'); ?>(<span class="state" onclick="<?php echo $customerDeliveryPrefix; ?>s.address.chooseOldDeliveryAddressDetails('state');"></span>)</label>
                  <input type="text" class="form-control" name="delivery_address[state]"/>
                  <label class="control-label"><?php echo $this->lang->phrase('postcode'); ?>(<span class="postcode" onclick="<?php echo $customerDeliveryPrefix; ?>s.address.chooseOldDeliveryAddressDetails('postcode');"></span>)</label>
                  <input type="text" class="form-control" name="delivery_address[postcode]"/>
                </div>
                <div class="col-sm-6<?php /**/ ?> addressDetails contactDetails<?php /**/ ?>">
                  <label class="control-label"><?php echo $this->lang->phrase('venue'); ?> (<span class="venue" onclick="<?php echo $customerDeliveryPrefix; ?>s.address.chooseOldDeliveryContactDetails('venue');"></span>)</label>
                  <input type="text" class="form-control" name="delivery_contact[venue]"/>
                  <label class="control-label"><?php echo $this->lang->phrase('contact_name'); ?> (<span class="name" onclick="<?php echo $customerDeliveryPrefix; ?>s.address.chooseOldDeliveryContactDetails('name');"></span>)</label>
                  <input type="text" class="form-control" name="delivery_contact[name]"/>
                </div>
                <div class="col-sm-6<?php /**/ ?> addressDetails contactDetails<?php /**/ ?>">
                  <label class="control-label"><?php echo $this->lang->phrase('contact_email'); ?> (<span class="email" onclick="<?php echo $customerDeliveryPrefix; ?>s.address.chooseOldDeliveryContactDetails('email');"></span>)</label>
                  <input type="text" class="form-control" name="delivery_contact[email]"/>
                  <label class="control-label"><?php echo $this->lang->phrase('contact_phone'); ?> (<span class="phone" onclick="<?php echo $customerDeliveryPrefix; ?>s.address.chooseOldDeliveryContactDetails('phone');"></span>)</label>
                  <input type="text" class="form-control" name="delivery_contact[phone]"/>
                </div>
              </div>
          </div>
        </div>  
    </div>
  </div>
<script type="text/javascript">
if (typeof(addressCache)=='undefined'){
  var addressCache={};
}
Object.assign(addressCache,<?php echo json_encode($venueAddresses); ?>);

//if (typeof(customerDelivery)=='undefined'){
<?php echo $customerDeliveryPrefix; ?>s['customer']={
  'prefix':'<?php echo $customerDeliveryPrefix ?>'
  ,'reset':function(){
    var _this=this;
    _NS.DOM.enable(_this.prefix+'#Customer input');
    document.querySelector('#'+_this.prefix+'Form .newCustomer').style.display='block';
    document.querySelector('#'+_this.prefix+'Customer .customerDetails').style.display='none';
    document.querySelector('#'+_this.prefix+'Customer input[name="customer_id"]').value=0;
    document.querySelector('#'+_this.prefix+'Customer .choice').style.display='block';
    
    document.querySelectorAll('#'+_this.prefix+'Form .residentialAddress, #'+_this.prefix+'Form .deliveryAddress').forEach(function(a){
      a.style.display='none';
    });
    
    <?php echo $customerDeliveryPrefix; ?>s.address.reset('residential');
    <?php echo $customerDeliveryPrefix; ?>s.address.reset('delivery');
  }
  ,'choose':function(userData){
    var _this=this, field='',el=null;
    _this.reset();
    document.querySelectorAll('#'+_this.prefix+'Form .residentialAddress, #'+_this.prefix+'Form .deliveryAddress').forEach(function(a){
      a.style.display='block';
    });
    _this.cache={};
    if (typeof(userData)=='undefined'){
      document.querySelector('#'+_this.prefix+'Customer input[name="customer_id"]').value=0;
      _NS.DOM.resetFields('#'+_this.prefix+'Form #'+_this.prefix+'Customer .customerDetails');
      _NS.DOM.enable('#'+_this.prefix+'Customer input');
      
      document.querySelector('#'+_this.prefix+'Form .newCustomer').style.display='block';
      document.querySelector('#'+_this.prefix+'Customer .customerDetails').style.display='block';
      <?php echo $customerDeliveryPrefix; ?>s.address.updateList('residential');
      <?php echo $customerDeliveryPrefix; ?>s.address.updateList('delivery');
      return false;
    }
    
    _this.cache=userData;
    //console.log('user: '+JSON.stringify(userData,null,2));
    document.querySelector('#'+_this.prefix+'Form .newCustomer').style.display='none';

    //console.log('before customer disabling');
    //_NS.DOM.disable('#'+_this.prefix+'Customer input');
    
    document.querySelector('#'+_this.prefix+'Customer input[name="customer_id"]').value=userData['user_id'];
    for(field in userData){
      el=document.querySelector('#'+_this.prefix+'Customer input[name="customer['+field+']"]');
      if (el!=null){
        el.value=userData[field];
      }
    }
    document.querySelector('#'+_this.prefix+'Customer .customerDetails').style.display='block';
    <?php echo $customerDeliveryPrefix; ?>s.address.updateList('residential',userData['user_id']);
    <?php echo $customerDeliveryPrefix; ?>s.address.updateList('delivery',userData['user_id']);
  }
  ,'unlock':function(){
    var _this=this;
    _NS.DOM.enable('#'+_this.prefix+'Customer input');
    document.querySelector('#'+_this.prefix+'Form .newCustomer').style.display='block';
document.querySelector('#'+_this.prefix+'Customer .customerDetails').style.display='none';
    document.querySelector('#'+_this.prefix+'Customer input[name="customer_id"]').value=0;
    document.querySelector('#'+_this.prefix+'Customer .choice').style.display='block';
    
    <?php echo $customerDeliveryPrefix; ?>s.address.updateList('residential');
    <?php echo $customerDeliveryPrefix; ?>s.address.updateList('delivery');
  }
  ,'cache':{}
};
<?php echo $customerDeliveryPrefix; ?>s['address']={
  'prefix':'<?php echo $customerDeliveryPrefix ?>'
  ,'reset':function(mode){
    var _this=this,mode=mode;
    document.querySelectorAll('#'+_this.prefix+'Form select[name="'+mode+'_address_id"] .userAddresses').innerHTML='';
    _NS.DOM.resetFields('#'+_this.prefix+'Form div.'+mode+'Address');
    document.querySelectorAll('#'+_this.prefix+'Form div.'+mode+'Address .addressDetails').forEach(function(el){
      el.style.display='none';
    });
  }
  ,'choose':function(mode,chosenAddressID){
    var _this=this,mode=mode,chosenAddressID=chosenAddressID;
    if (typeof(chosenAddressID)=='undefined'){
      chosenAddressID=document.querySelector('#'+_this.prefix+'Form select[name="'+mode+'_address_id"]').value;
    }
    else {
      document.querySelector('#'+_this.prefix+'Form select[name="'+mode+'_address_id"]').value=chosenAddressID;
    }
    document.querySelectorAll('#'+_this.prefix+'Form div.'+mode+'Address .addressDetails input').forEach(function(f){
      
      if (typeof(f.dataset['chosen'])!='undefined' && f.dataset['chosen']!=''){
        f.value=f.dataset['chosen'];
      }
      else {
        f.value='';
      }
      f.dataset['chosen']='';
    });
    switch(chosenAddressID){
      case 'skip':
      case 'residential':
        document.querySelectorAll('#'+_this.prefix+'Form div.'+mode+'Address .addressDetails').forEach(function(el){
          el.style.display='none';
        });
        document.querySelectorAll('#'+_this.prefix+'Form div.'+mode+'Address .contactDetails').forEach(function(el){
          el.style.display='block';
        });
        
        _NS.DOM.enable('#'+_this.prefix+'Form div.'+mode+'Address .addressDetails input');
      break;
      case '':
        document.querySelectorAll('#'+_this.prefix+'Form div.'+mode+'Address .addressDetails').forEach(function(el){
          el.style.display='none';
        });
      break;
      case '0': 
        document.querySelectorAll('#'+_this.prefix+'Form div.'+mode+'Address .addressDetails input').forEach(function(f){
          f.value='';
        });
        document.querySelectorAll('#'+_this.prefix+'Form div.'+mode+'Address .addressDetails').forEach(function(el){
          el.style.display='block';
        });
        
        _NS.DOM.enable('#'+_this.prefix+'Form div.'+mode+'Address .addressDetails input');
      break;
      default:
        _NS.DOM.fillFields('#'+_this.prefix+'Form div.'+mode+'Address',addressCache[chosenAddressID],mode+'_address');
        if ( addressCache[chosenAddressID] && typeof(addressCache[chosenAddressID]['venue'])!='undefined' ){
          console.log(JSON.stringify(addressCache[chosenAddressID],null,2));
          ['venue','contact_name','contact_phone','contact_email'].forEach(function(f){
            document.querySelector('#'+_this.prefix+'Form div.'+mode+'Address input[name="'+mode+'_contact['+f.replace('contact_','')+']"]').value=addressCache[chosenAddressID][f];
          });
        }
        
        _NS.DOM.disable('#'+_this.prefix+'Form div.'+mode+'Address input');
        
        document.querySelectorAll('#'+_this.prefix+'Form div.'+mode+'Address .addressDetails').forEach(function(el){
          el.style.display='block';
        });
        if (document.querySelector('#'+_this.prefix+'Form select[name="'+mode+'_address_id"] .v'+chosenAddressID).dataset['is_venue']=='0'){
          _NS.DOM.enable('#'+_this.prefix+'Form div.'+mode+'Address .contactDetails input'); 
        }
      break;
    }
    document.querySelector('#'+_this.prefix+'Form select[name="'+mode+'_address_id"]').dataset['chosen']='';
  }
  ,'chooseOldDeliveryContactDetails':function(field){
    var _this=this,el=document.querySelector('#'+_this.prefix+'Form input[name="delivery_contact['+field+']"]')
      ,value=document.querySelector('#'+_this.prefix+'Form .contactDetails label span.'+field).innerHTML;
    
    el.dataset['chosen']=value;
    el.value=value;
  }
  ,'chooseOldDeliveryAddressDetails' : function (field) {
    var _this=this,el=document.querySelector('#'+_this.prefix+'Form input[name="delivery_address['+field+']"]')
      ,value=document.querySelector('#'+_this.prefix+'Form .addressDetails label span.'+field).innerHTML;
    
    el.dataset['chosen']=value;
    el.value=value;
  }
  ,'resetDeliveryContactDetails':function(){
    var _this=this;
    document.querySelectorAll('#'+_this.prefix+'Form .contactDetails label span').forEach(function(el){
      el.innerHTML='';
      el.dataset['chosen']='';
    });
  }
  ,'updateList':function(mode,userID){
    var _this=this,mode=mode,userID=userID;
    _this.reset(mode);
    if (typeof(userID)!='undefined'){
      _NS.post('<?php echo NS_BASE_URL; ?>customer/addresses',{'user_id':userID,'address_type':mode},{'success':function(reply){
        var result=[];

        reply.data.forEach(function(e){
          alert(e['distance']);
          result.push('<option class="v'+e['address_id']+'" data-is_venue="0" value="'+e['address_id']+'">'+e['city']+', '+e['line_1']+((e['line_2']!=null)?', '+e['line_2']:'')+((e['state']!=null)?', '+e['state']:'')+((e['postcode']!=null)?', '+e['postcode']:'')+((e['distance']!=null)?', '+e['distance']:'')+'</option>');
          addressCache[e['address_id']]=e;
        });
        if (mode == "residential") {
          document.querySelector('#'+_this.prefix+'Form select[name="'+mode+'_address_id"] .userAddresses').innerHTML=result.join('');
        }
        setTimeout(function(_this,mode){_this.choose(mode,document.querySelector('#'+_this.prefix+'Form select[name="'+mode+'_address_id"]').dataset['chosen']);},100,_this,mode);
        
      }},1);
    }
    else {
      document.querySelector('#'+_this.prefix+'Form select[name="'+mode+'_address_id"] .userAddresses').innerHTML='';
    }
  }
};
//}
</script>