<div class="body booking" data-section="booking">
  <div class="templateContent"></div>
  <pre class="plainContent hidden"></pre>
  <div class="templates hidden">
<?php include(APPPATH.'/views/booking/view.php'); ?>
  </div>
</div>
<script type="text/javascript">
NS_Rental['booking']={
  'container':null
  ,'templates':{
    'init':function(){
      if (this.initiated) return false;
      this['base']=document.querySelector('#NS_Rental > .body.booking > .templates > .base').innerHTML;
      this['item']=document.querySelector('#NS_Rental > .body.booking > .templates .bookingItem').innerHTML;
      this['service']=document.querySelector('#NS_Rental > .body.booking > .templates .bookingService').innerHTML;
      this['initiated']=true;
    }
    ,'initiated':false
    /** /
    ,'base':document.querySelector('#NS_Rental .templates .booking').innerHTML
    ,'item':document.querySelector('#NS_Rental .templates .bookingItem').innerHTML
    ,'service':document.querySelector('#NS_Rental .templates .bookingService').innerHTML/**/
  }
  ,'current':null
  ,'newPayment':{}
  ,'reset':function(){
    var _this=this,_that=NS_Rental;
    _this.container=document.querySelector('#NS_Rental .body.booking');
    _this.container.querySelector('.templateContent').innerHTML='';
    _this.current=null;
    _this.templates.init();
  }
  ,'toggleResidentialDelivery':function(){

  }
  ,'logistics':{
    'items':{}
    ,'delivery':{}
    ,'collection':{}
    ,'reset':function(){
      var _this=this;
      _this.itemIDs={};
      _this.delivery={};
      _this.collection={};

      document.querySelector('#NS_Rental .body.booking .logisticsDetails .deliveryPanel').innerHTML='';
      document.querySelector('#NS_Rental .body.booking .logisticsDetails .collectionPanel').innerHTML='';
    }
    ,'addItem':function(itemID,title,quantity){
      var _this=this,itemID=itemID,title=title;
      if (typeof(_this.items[itemID])=='undefined'){
        _this.items[itemID]={'quantity':quantity,'title':title};
      }
    }
    ,'add':function(itemID,mode,code){
      var _this=this;

      if (typeof(_this.items[itemID])=='undefined'){
        _this.items[itemID]={'quantity':0};
      }
      if (typeof(_this.items[itemID][mode])=='undefined'){
        _this.items[itemID][mode]=code;
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
    ,'render':function(mode,code){
      var _this=this,itemID='',result=[]
      ,container=document.querySelector('#NS_Rental .body.booking .logisticsDetails .'+mode+'Panel .'+mode+code)
      ,dateString=(new Date(code.substr(0,4),(parseInt(code.substr(4,2))-1),code.substr(6,2),code.substr(8,2),code.substr(10,2))).toLocaleString();
      for (itemID in _this[mode][code]){
        if (_this[mode][code][itemID]==1){
          //console.log('attempt to render item '+itemID);
          if (itemID.charAt(0)=='E'){
            result.push(((_this.items[itemID]['quantity']>0)?'<span class="quantity'+itemID+'">'+_this.items[itemID]['quantity']+'</span> x ':'')+_this.items[itemID]['title']);
          }
          else {
            result.push(((_this.items[itemID]['quantity']>0)?'<span class="quantity'+itemID+'">'+_this.items[itemID]['quantity']+'</span> x ':'')+_this.items[itemID]['title']);
          }
        }
      }
      if (result.length>0){
        if (container==null){
          document.querySelector('#NS_Rental .body.booking .logisticsDetails .'+mode+'Panel').insertAdjacentHTML('beforeend','<div class="'+mode+code+'"><label>'+dateString+'</label><ul></ul></div>');
        }
        document.querySelector('#NS_Rental .body.booking .logisticsDetails .'+mode+'Panel .'+mode+code+' ul').innerHTML='<li>'+result.join('</li><li>')+'</li>';
      }
      else if (container!=null) {
        container.remove();
      }
    }
  }
  ,'init':function(){
    var _this=this,_that=NS_Rental;
    _this.load(_that.config.requestParts[1]);
  }
  ,'load':function(bookingCode){
    var _this=this,_that=NS_Rental;

    _this.reset();

    _NS.post(_that.config.baseURL+'booking/load',{'code':bookingCode},{'success':function(reply){
      var reply=reply,items=[],services=[],globalFields=[
        'booking_id','code','status','currency'
        ,'total_items','total_services','subtotal_discount','discount_text'
        ,'discount_amount'
        ,'deposit_required','no_deposit_required'
        ,'deposit_allowed','deposit_amount','final_amount','grand_total','paid_amount'
        ,'customer_id','residential_address_id','delivery_address_id'
        ,'delivery_date','delivery_time','delivery_date_string'
        ,'collection_date','collection_time','collection_date_string'
        ,'due_date_string'
        ,'residential_address_string','delivery_address_string'
        ,'notes','extra_notes'
      ],f='',r='',t=_this.templates.base, logisticsData={'items':{},'delivery':{}};

      reply.data['currency']='$';
      /** /
      ['residential','delivery'].forEach(function(at){
        if (reply['data'][at+'_address_id']>0){
          reply['data'][at+'_address_string']=reply['data'][at+'_address']['city']+', '+reply['data'][at+'_address']['line_1']+', '+reply['data'][at+'_address']['state']+', '+reply['data'][at+'_address']['postcode'];
        }
        else {
          reply['data'][at+'_address_string']='To be advised';
        }
      });/**/
      
      if (reply.data['quote_variation_id']>0){

      }
      reply.data.entries.items.forEach(function(i){
        var t=_this.templates.item,k='',r='';
        for(k in i){
          r=new RegExp('{'+k.toUpperCase()+'}','g');
          t=t.replace(r,(k=='total')?parseFloat(i[k]).toFixed(2):i[k]);
        }
        items.push(t);
      });
      reply.data.entries.services.forEach(function(s){
        var t=_this.templates.service,k='',r='';
        for(k in s){
          r=new RegExp('{'+k.toUpperCase()+'}','g');
          t=t.replace(r,(k=='total')?parseFloat(s[k]).toFixed(2):s[k]);
        }
        services.push(t);
      });

      reply.data['deposit_required']=reply.data['no_deposit_required']='';
      if (reply.data['paid_amount']>0){
        reply.data['deposit_allowed']=0;
      }
      else {
        if (reply.data['deposit_value']>0){
          switch(reply.data['deposit_type']){
            case 'percentage':
              reply.data['deposit_amount']=parseFloat(parseFloat(reply.data['grand_total'])*(reply.data['deposit_value']/100)).toFixed(2);
            break;
            case 'amount':
              reply.data['deposit_amount']=parseFloat(reply.data['deposit_value']).toFixed(2);
            break;
          }
          reply.data['deposit_required']=1;
        }
        else {
          if (!reply.data['booking_id']){
            reply.data['no_deposit_required']=1;
          }
        }
      }
      reply.data['final_amount']=parseFloat(reply.data['grand_total']-reply.data['paid_amount']).toFixed(2);
      reply.data['grand_total']=parseFloat(reply.data['grand_total']).toFixed(2);

      reply.data['total_items']=parseFloat(reply.data['total_items']).toFixed(2);
      reply.data['total_services']=parseFloat(reply.data['total_services']).toFixed(2);


      _this.current=Object.assign({},reply.data);
      globalFields.forEach(function(f){
        var k='',r='';
        r=new RegExp('{'+f.toUpperCase()+'}','g');
        t=t.replace(r,reply.data[f]);
      });
      
      for (f in reply.data['customer']){
        t=t.replace((new RegExp('{'+('customer_'+f).toUpperCase()+'}','g')),reply.data['customer'][f]);
      }
      
      
      
      for (f in reply.data['delivery_contact']){
        t=t.replace((new RegExp('{'+('delivery_contact_'+f).toUpperCase()+'}','g')),reply.data['delivery_contact'][f]);
      }
      
      ['residential','delivery'].forEach(function(mode){
        for (f in reply.data[mode+'_address']){
          t=t.replace((new RegExp('{'+(mode+'_address_'+f).toUpperCase()+'}','g')),reply.data[mode+'_address'][f]);
        }
      });
      
      _this.container.querySelector('.templateContent').innerHTML=t;
      _this.container.querySelector('.templateContent .items').innerHTML=items.join('').replace(/{CURRENCY}/g,reply.data['currency']);
      _this.container.querySelector('.templateContent .services').innerHTML=services.join('').replace(/{CURRENCY}/g,reply.data['currency']);
<?php /** / ?>
      _NS.DOM.fillFields('#NS_Rental .body.booking .bookingDetailsFieldset',reply.data['customer'],'customer');
      _NS.DOM.fillFields('#NS_Rental .body.booking .bookingDetailsFieldset',reply.data['residential_address'],'residential_address');
      _NS.DOM.fillFields('#NS_Rental .body.booking .bookingDetailsFieldset',reply.data['delivery_address'],'delivery_address');
      if (reply.data['residential_address_id']>0 && reply.data['delivery_address_id']==reply.data['residential_address_id']){
        _NS.DOM.addClass('#NS_Rental .body.booking .bookingDetailsFieldset .deliveryAddress .addressDetails','hidden');
        _NS.DOM.removeClass('#NS_Rental .body.booking .bookingDetailsFieldset .deliveryAddress .addressDetails.contactDetails','hidden');
        document.querySelector('#NS_Rental .body.booking .bookingDetailsFieldset input[name="residential_delivery"]').checked=true;
      }
      _NS.DOM.fillFields('#NS_Rental .body.booking .bookingDetailsFieldset',reply.data['delivery_contact'],'delivery_address');
      if (reply.data['grand_total']!=reply.data['paid_amount']){
        _NS.DOM.removeClass('#NS_Rental .body.booking .bookingDetailsFieldset .depositPayment'+reply.data['deposit_allowed'],'hidden');
      }

      if (typeof(reply.data.logistics)!='undefined'){
        ['delivery','collection'].forEach(function(o){
          var result=[],o=o;

          reply.data.logistics[o]['codes'].forEach(function(code){

            var content=[];
            reply.data.logistics[o][code]['entries'].forEach(function(e){
              var p=e;


              if (typeof(p['atomic_item_id'])!='undefined'){
                _this.logistics.addItem(p['atomic_item_id'],reply['itemCache'][p['atomic_item_id']]['title'],p.quantity);
                //console.log('regular logistics '+p['atomic_item_id']+' '+o+' '+code+' '+reply['itemCache'][p['atomic_item_id']]['title']);
                _this.logistics.add(p['atomic_item_id'],o,code);
                //p['title']=itemCache[p['atomic_item_id']]['title'];
              }
              else {
                _this.logistics.addItem(p['extra_item_id'],p.title,p.quantity);
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
<?php /**/ ?>
      //_this.container.querySelector('.plainContent').innerHTML=JSON.stringify(reply,null,2);
    }},1);
    _that.loadSection('booking');
  }
  ,'parseDeliveryData':function(template,data){
  
  }
  ,'validateRequest':function(mode){
    var _this=this,_that=NS_Rental,dataSelector='', mode=mode,params={},amount=0,title='',action='create'
    ,requiredPayment=true;

    switch(mode){
      case 'confirm':
        requiredPayment=false;
      break;
      case 'deposit':
        amount=_this.current['deposit_amount'];
        title='Initial payment';
      break;
      case 'finalization':
        amount=_this.current['final_amount'];
        title='Final payment';
      break;
    }

    _that.booking.newPayment={
      'mode':mode
      ,'amount':parseFloat(amount).toFixed(2)
      ,'final':_this.current['final_amount']
    };

    if (NS_Rental.booking.current!==null){
      //console.log(JSON.stringify(NS_Rental.booking.current,null,2));
      params['code']=NS_Rental.booking.current.code;
      if (NS_Rental.booking.current.booking_id>0){
        NS_Rental.booking.newPayment['booking_id']=NS_Rental.booking.current.booking_id;
        return NS_Rental.Stripe.run(title,amount,NS_Rental.booking.pay);
      }
      else {
        //console.log("form data:\n"+JSON.stringify(_NS.DOM.getFormData('#NS_Rental .body.booking'),null,2));
        dataSelector='#NS_Rental .body.booking .bookingDetailsFieldset';
        params=Object.assign(params,_NS.DOM.getFormData(dataSelector));
      }
    }
    else {

    }
    params['payment']=NS_Rental.booking.newPayment;


    _NS.post('<?php echo NS_BASE_URL; ?>booking/'+action,params,{'success':function(reply){
      if (typeof(NS_Rental.booking.current)==null){
        NS_Rental.booking.current={};
      }
      NS_Rental.booking.newPayment['booking_id']=reply.data['booking_id'];
      if (requiredPayment){
        NS_Rental.Stripe.run(title,amount,NS_Rental.booking.pay);
      }
      else {
        NS_Rental.booking.load(NS_Rental.booking.current['code']);
      }
    },'fail':function(reply){
      var addressTypes=['residential','delivery'];
      addressTypes.forEach(function(t){
        if (typeof(reply.data[t+'_address_id'])!='undefined'){
          document.querySelector(dataSelector+' input[name="'+t+'_address_id"]').value=reply.data[t+'_address_id'];
        }
      });

      _NS.defaultReplyActions.fail(reply);
    }},1);


    /** /
    params=_NS.DOM.getFormData('#bookingRequestForm');

    _NS.post(NS_Rental.config.baseURL+'booking/validateRequest',params,{
      'success':function(reply){
        reply.data
        NS_Rental.Stripe.run('Final payment','{FINAL_AMOUNT}',NS_Rental.booking.confirm);
        console.log(JSON.stringify(reply,null,2));
      }
    },1);/**/
    //console.log(JSON.stringify(,null,2));
  }
  ,'pay':function(params){
    var params=params;
    params=Object.assign(params,NS_Rental.booking.newPayment);

    console.log('booking params for payment: '+JSON.stringify(params,null,2));
    _NS.post('<?php echo NS_BASE_URL; ?>booking/pay',params,{'success':function(reply){
      NS_Rental.booking.load(reply.data.code);
    }},1);
  }
  ,'confirm':function(){
    var _this=this,_that=NS_Rental,params={},action='create';
    if (NS_Rental.booking.current!==null){
      //console.log(JSON.stringify(NS_Rental.booking.current,null,2));
      params['code']=NS_Rental.booking.current.code;
      if (NS_Rental.booking.current.booking_id>0){
        action='finalize';
      }
      else {
        //console.log("form data:\n"+JSON.stringify(_NS.DOM.getFormData('#NS_Rental .body.booking'),null,2));
        params=Object.assign(params,_NS.DOM.getFormData('#NS_Rental .body.booking .bookingDetailsFieldset'));
      }
    }
    params['payment']=NS_Rental.booking.newPayment;

    _NS.post('<?php echo NS_BASE_URL; ?>booking/'+action,params,{'success':function(reply){
      //_this.load(reply.data.code);
    }},1);
  }
  ,'confirm_':function(){
    var _this=this,_that=NS_Rental,params={},action='create';
    if (NS_Rental.booking.current!==null){
      //console.log(JSON.stringify(NS_Rental.booking.current,null,2));
      params['code']=NS_Rental.booking.current.code;
      if (NS_Rental.booking.current.booking_id>0){
        action='finalize';
      }
      else {
        //console.log("form data:\n"+JSON.stringify(_NS.DOM.getFormData('#NS_Rental .body.booking'),null,2));
        params=Object.assign(params,_NS.DOM.getFormData('#NS_Rental .body.booking .bookingDetailsFieldset'));
      }
    }
    params['payment']=NS_Rental.booking.newPayment;

    _NS.post('<?php echo NS_BASE_URL; ?>booking/'+action,params,{'success':function(reply){
      //_this.load(reply.data.code);
    }},1);
  }
};
</script>
