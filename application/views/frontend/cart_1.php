<style type="text/css">
/*progressbar*/
#bookingCreationProgress {
    margin-bottom: 30px;
    overflow: hidden;
    /*CSS counters to number the steps*/
    counter-reset: step;
    text-align: center;
}

#bookingCreationProgress li {
    list-style-type: none;
    width: 33.33%;
    float: left;
    position: relative;
    letter-spacing: 1px;
}

#bookingCreationProgress li:before {
    content: counter(step);
    counter-increment: step;
    width: 24px;
    height: 24px;
    line-height: 26px;
    display: block;
    font-size: 12px;
    color: #333;
    //background: white;
    border-radius: 25px;
    margin: 0 auto 5px auto;
}

#bookingCreationProgress li:after {
    content: '';
    width: 100%;
    height: 8px;
    background: white;
    position: absolute;
    left: -50%;
    top: 9px;
    z-index: -1; 
}

#bookingCreationProgress li:first-child:after {
    content: none;
}

#bookingCreationProgress li.active:before, #bookingCreationProgress li.active:after {
    background: rgb(51,122,183);
    color: white;
}

</style>
<div class="body cart hidden" data-section="cart">
  <h3><?php echo 'Booking Creation Progress' ?></h3>
  <ul id="bookingCreationProgress" class="text-primary">
    <li class="cartStep active" onclick="NS_Rental['cart'].init();">Fill the cart</li>
    <li class="customerStep">Enter customer details</li>
    <li class="deliveryStep">Enter delivery details</li>
  </ul>
  <form id="bookingRequestForm">
  <input type="hidden" name="start_date" value="" autocomplete="off"/>
  <input type="hidden" name="end_date" value="" autocomplete="off"/>
  <input type="hidden" name="customer_id" data-reset_value="0"/>
  <input type="hidden" name="residential_address_id" data-reset_value="0"/>
  <input type="hidden" name="delivery_address_id" data-reset_value="0"/>
  <div class="listStep">
    <div class="list"></div>
    <div class="row">
      <div class="col-sm-12"><a class="btn btn-primary" onclick="NS_Rental.cart.validateRequest();">Continue</a></div>
    </div>
  </div>
  <div class="customer clearfix hidden">
    <input type="hidden" class="form-control" name="customer[phone]" value=""/>
    <div class="header">
      <p>
        Please check all details are correct below.
      </p>
      <p>
        <span class="base">Customer Details</span> 
        <span class="note">(For billing and insurance purposes)</span>
      </p>
    </div>
    <div class="row">
      <div class="col-sm-4">
        <label>First Name *</label>
        <input type="text" class="form-control" name="customer[first_name]" value=""/>
      </div>
      <div class="col-sm-4">
        <label>Last Name *</label>
        <input type="text" class="form-control" name="customer[last_name]" value=""/>
      </div>
      <div class="col-sm-4 company">
        <label>Company</label>
        <input type="text" class="form-control" name="customer[company]" value=""/>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-4">
        <label>Address Street *</label>
        <input type="text" class="form-control" name="residential_address[line_1]" value=""/>
      </div>
      <div class="col-sm-4">
        <label>Address Street 2</label>
        <input type="text" class="form-control" name="residential_address[line_2]" value=""/>
      </div>
      <div class="col-sm-4">
        <label>City *</label>
        <input type="text" class="form-control" name="residential_address[city]" value=""/>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-4">
        <label>Phone Number *</label>
        <input type="text" class="form-control" name="residential_address[phone]" value=""/>
      </div>
      <div class="col-sm-4">
        <label>Email *</label>
        <input type="text" class="form-control" name="customer[email]" value=""/>
      </div>
      <div class="col-sm-2">
        <label>State</label>
        <input type="text" class="form-control" name="residential_address[state]"/>
      </div>
      <div class="col-sm-2">
        <label>Postcode</label>
        <input type="text" class="form-control" name="residential_address[postcode]"/>
      </div>
    </div>
    <div>
      <a class="btn btn-lg btn-primary pull-right" onclick="NS_Rental.cart.acceptCustomer();">Continue</a>
    </div>
  </div>
  <div class="delivery clearfix hidden">
    <div class="header">
      <p>
        Please check all details are correct below.
      </p>
      <p>
        <span class="base">Delivery Details</span> 
        <span class="note">(Please include all information)</span>
        <span>(</span><input type="checkbox" name="residential_delivery" value="1" onclick="NS_Rental.cart.setResidentialDelivery();"/><span>)</span>
        <span>Use Customer Address</span>
      </p>
    </div>
    <div class="addressFields">
    <div class="row">
      <div class="col-sm-4">
        <label>Venue Name</label>
        <input type="text" class="form-control" name="delivery_contact[venue]" value=""/>
      </div>
      <div class="col-sm-4">
        <label>Venue Contact Person</label>
        <input type="text" class="form-control" name="delivery_contact[name]" value=""/>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-4">
        <label>Address Street *</label>
        <input type="text" class="form-control" name="delivery_address[line_1]" value=""/>
      </div>
      <div class="col-sm-4">
        <label>Address Street 2</label>
        <input type="text" class="form-control" name="delivery_address[line_2]" value=""/>
      </div>
      <div class="col-sm-4">
        <label>City *</label>
        <input type="text" class="form-control" name="delivery_address[city]" value=""/>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-4">
        <label>Delivery Phone Number *</label>
        <input type="text" class="form-control" name="delivery_contact[phone]" value=""/>
      </div>
      <div class="col-sm-4">
        <label>Delivery Email</label>
        <input type="text" class="form-control" name="delivery_contact[email]" value=""/>
      </div>
      <div class="col-sm-2">
        <label>State</label>
        <input type="text" class="form-control" name="delivery_address[state]"/>
      </div>
      <div class="col-sm-2">
        <label>Postcode</label>
        <input type="text" class="form-control" name="delivery_address[postcode]"/>
      </div>
    </div>
    </div>
    <div class="row">
      <div class="col-sm-8">
        <div class="bookingNotes hidden">
        <label>Booking notes</label>
        <p class="bookingNotes">_BOOKING_NOTES_</p>
        </div>
        <label>Customer Notes (Add any relevant information here)</label>
        <textarea name="extra_notes" class="form-control"></textarea>
      </div>
      <div class="col-sm-4">
        <a class="btn btn-lg btn-primary pull-right" onclick="NS_Rental.cart.acceptDelivery();">Confirm Booking</a>
      </div>
    </div>
  </div>
  
  </form>
</div>
<script type="text/javascript">
NS_Rental['cart']={
  'initiated':false

  ,'content':<?php echo ((!empty($_SESSION['NS_Rental']['cart']))?json_encode($_SESSION['NS_Rental']['cart']):'{}'); ?>
  ,'totalInCart':0
  ,'add':function(itemID,quantity){
    var itemID=itemID, quantity=quantity, itemData={},chosen=false,old=false, packed=[],packedItemID=0;
    this['template']=document.querySelector('#NS_Rental .templates .cartItem').innerHTML;
    if (typeof(itemID)=='undefined'){
      itemID=NS_Rental.items.chosen;
      chosen=true;
    }
    if (typeof(NS_Rental.config.items[itemID]['package'])!='undefined'){
      for (packedItemID in NS_Rental.config.items[itemID]['package']){
        packed.push(NS_Rental.config.items[itemID]['package'][packedItemID]+' x '
          +(
            (typeof(NS_Rental.config.items[packedItemID])!='undefined')
            ?NS_Rental.config.items[packedItemID]['title']
            :NS_Rental.config.items['p'+packedItemID]['title']
          )
        );
      }
    }

    if (typeof(quantity)=='undefined'){
      quantity=document.querySelector('#NS_Rental '+((chosen)?'.item':'#NS_Rental__item'+itemID)+' .action .quantity').value;
    }
/** /
    if (typeof(quantity)!='undefined'){
      this.content[itemID]=quantity;
    }
    else {
      this.content[itemID]=document.querySelector('#NS_Rental '+((chosen)?'.item':'#NS_Rental__item'+itemID)+' .action .quantity').value;
    }/**/

    if (quantity > NS_Rental['availability']['current'][itemID]){
      return _NS.alert.open('fail','Fail','Can be chosen up to '+NS_Rental['availability']['current'][itemID],2);
    }
    this.content[itemID]=quantity;
    //if (NS_Rental.cart.initiated){
      NS_Rental.loadSection('cart');
    //}
    if (document.querySelector('#NS_Rental > .cart .list .item'+itemID)==null){
      itemData=NS_Rental.config.items[itemID];
      document.querySelector('#NS_Rental > .cart .list').insertAdjacentHTML('beforeend',this.template
        .replace('_THUMBNAIL_',((itemData['thumbnail']!='')?'<img class="img-responsive" src="'+itemData['thumbnail']+'"/>':''))
        .replace('_TITLE_',itemData['title'])
        .replace('_PLACEHOLDER_',('up to '+NS_Rental['availability']['current'][itemID]))
        .replace(/_X_/g,itemID)
        .replace('_PACKED_',packed.join('<br/>'))
      );
    }
    else {
      document.querySelector('#NS_Rental__item'+itemID+' .quantity').value=this.content[itemID];
    }

    document.querySelector('#NS_Rental > .cart .list .item'+itemID+' .quantity').value=this['content'][itemID];
    document.querySelector('#NS_Rental .items .item'+itemID+' .action').className='action available update';
    
    NS_Rental.cart.updatePrices();
  }
  ,'update':function(itemID){
    var itemID=itemID, newValue=document.querySelector('#NS_Rental .body.cart input[name="quantity['+itemID+']"]').value;
    NS_Rental.cart.add(itemID,newValue);
    //console.log('old quantity'+itemID+': '+this.content[itemID]+' -> '+newValue);
    //document.querySelector('#NS_Rental .items .item'+itemID+' .quantity .v'+newValue).selected=true;
    //NS_Rental.cart.updatePrices();
  }
  ,'updatePrices':function(){
    _NS.post(NS_Rental.config.baseURL+'cart/update',{'content':NS_Rental.cart.content},{'success':function(reply){
      var itemID=0;
      for (itemID in reply.data){
        console.log('checking price update for item '+itemID+': '+reply['data'][itemID]['price']);
        document.querySelector('#NS_Rental .cart .item'+itemID+' .price').innerHTML=reply.data[itemID]['price'];
      }
    }},1);
  }
  ,'remove':function(itemID){
    var itemID=itemID;
    document.querySelector('#NS_Rental .cart .item'+itemID).remove();

    NS_Rental.cart.content[itemID]=0;
    NS_Rental.cart.updatePrices();

    if (document.querySelector('#NS_Rental .items .item'+itemID+' .action.update')!=null){
      document.querySelector('#NS_Rental .items .item'+itemID+' .action').className='action add';
      document.querySelector('#NS_Rental .items .item'+itemID+' .quantity .v1').selected=true;
    }
  }
  ,'show':function(){

  }
  ,'init':function(){
    var _this=this,_that=NS_Rental,content=Object.assign({},this.content),itemID=0,quantity=0;
    _that.booking.reset();

    for(itemID in content){
      quantity=content[itemID];
      if (quantity>0){
        this.add(itemID,quantity);
      }
    }

    _NS.DOM.addClass('#NS_Rental .body.cart #bookingRequestForm > div','hidden');
    _NS.DOM.removeClass('#NS_Rental .body.cart #bookingCreationProgress .active','active');
    _NS.DOM.addClass('#NS_Rental .body.cart #bookingCreationProgress .cartStep','active');
    _NS.DOM.removeClass('#NS_Rental .body.cart #bookingRequestForm .listStep','hidden');

    _that.loadSection('cart');
    
    this.initiated=true;
  }
  ,'acceptCustomer':function(){
    var _this=this,data=_NS.DOM.getFormData('#NS_Rental .body.cart #bookingRequestForm')
    ,formSelector='#NS_Rental .body.cart #bookingRequestForm',el=null;
    data['customer']['phone']=data['residential_address']['phone'];
    _NS.post('<?php echo NS_BASE_URL; ?>booking/acceptCustomer',data,{'success':function(reply){
      var reply=reply,fields=['customer_id','residential_address_id'];
      fields.forEach(function(f){
        var el=document.querySelector(formSelector+' input[name="'+f+'"]');
        if (el!=null && typeof(reply['data'][f])!='undefined'){
          el.value=reply.data[f];
        }
      });
      _NS.DOM.addClass(formSelector+' > div','hidden');
      _NS.DOM.addClass('#NS_Rental .body.cart #bookingCreationProgress .deliveryStep','active');
      _NS.DOM.removeClass(formSelector+' > .delivery','hidden');
      
      //_NS.DOM.addClass(_this.container+', '+formSelector+' > div','hidden');
      //_NS.DOM.removeClass(formSelector+' > div.delivery','hidden');
    },'fail':function(reply){
      var field='',el=null,className='';
      for (field in reply['data']){
        jQuery(formSelector+' *[name="'+field+'"]').val(reply['data'][field]);
      }
      _NS.defaultReplyActions.fail(reply);
    }/**/},1);
  }
  ,'acceptDelivery':function(){
    var _this=this,formSelector='#NS_Rental .body.cart #bookingRequestForm'
    ,data={},el=null;
    
    ['start','end'].forEach(function(mode){
      document.querySelector(formSelector+' input[name="'+mode+'_date"]').value=NS_Rental['period'][mode];
    });
    
    data=_NS.DOM.getFormData(formSelector);
    data['customer']['phone']=data['residential_address']['phone'];
    _NS.post('<?php echo NS_BASE_URL; ?>booking/acceptDelivery',data,{'success':function(reply){
      var reply=reply,fields=['delivery_address_id'];
      fields.forEach(function(f){
        var el=document.querySelector(formSelector+' *[name="'+f+'"]');
        if (el!=null && typeof(reply['data'][f])!='undefined'){
          el.value=reply.data[f];
        }
      });
      
      //_NS.DOM.addClass(_this.container+', '+formSelector+' > div','hidden');
      
      _NS.post('<?php echo NS_BASE_URL; ?>booking/create',data,{'success':function(reply){
        NS_Rental['booking'].load(reply['data']['code']);//NS_Rental.booking.load(code);
      }},1);
    },'fail':function(reply){
      var field='',el=null,className='';
      for (field in reply['data']){
        jQuery(formSelector+' *[name="'+field+'"]').val(reply['data'][field]);
      }
      _NS.defaultReplyActions.fail(reply);
    }},1);
  }
  ,'setResidentialDelivery':function(){
    var formSelector='#NS_Rental .body.cart #bookingRequestForm'
    ,el=document.querySelector(formSelector+' input[name="residential_delivery"]');
    if (el.checked==true){
      _NS.DOM.addClass(formSelector+' > div.delivery > .addressFields','hidden');
    }
    else {
      _NS.DOM.removeClass(formSelector+' > div.delivery > .addressFields.hidden','hidden');
    }
  }
  ,'validateRequest':function(){  
    _NS.DOM.addClass('#NS_Rental .body.cart #bookingRequestForm > div','hidden');
    _NS.DOM.addClass('#NS_Rental .body.cart #bookingCreationProgress .customerStep','active');
    _NS.DOM.removeClass('#NS_Rental .body.cart #bookingRequestForm .customer','hidden');
  }
};
</script>