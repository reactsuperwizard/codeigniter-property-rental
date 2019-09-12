<?php require_once(VIEWPATH.'frontend/quote/styles.php'); ?>
<div class="body quotes" data-section="quotes">
  <div class="activeQuotes hidden">
    <h3>Active Quotes</h3>
    <div class="list"></div>
  </div>
  <div class="previousQuotes hidden">
    <h3>Previous Quotes</h3>
    <div class="list"></div>
  </div>
  <div class="templates hidden">
    <div class="activeEntry">
      <div class="panel panel-default" style="margin: 5px 25px;"><div class="panel-body">
        <div class="row">
          <div class="col-sm-4">Name: _NAME_</div>
          <div class="col-sm-4">Status: _STATUS_<br/>Valid until: _EXPIRATION_DATETIME_<br/>Starts: _START_</div>
          <div class="col-sm-4">Location: _LOCATION_</div>
        </div>
      </div></div>
    </div>
    <div class="previousEntry">
      <div class="panel panel-default" style="margin: 5px 25px;"><div class="panel-body">
        <div class="row">
          <div class="col-sm-4">Name: _NAME_</div>
          <div class="col-sm-4">Status: _STATUS_<br/>Valid until: _EXPIRATION_DATETIME_<br/>Starts: _START_</div>
          <div class="col-sm-4">Location: _LOCATION_</div>
        </div>
      </div></div>
    </div>
  </div>
</div>
<div class="body quote" data-section="quote" style="padding:25px;">
  <div class="content"></div>
  <form id="quoteAcceptanceForm">
  <input type="hidden" name="quote_id" data-reset_value="0"/>
  <input type="hidden" name="code" value=""/>
  <input type="hidden" name="purchase_order" value=""/>
  <input type="hidden" name="customer_id" data-reset_value="0"/>
  <input type="hidden" name="residential_address_id" data-reset_value="0"/>
  <input type="hidden" name="delivery_address_id" data-reset_value="0"/>
  <div class="customer clearfix hidden">
    <input type="hidden" class="form-control" name="customer[phone]" value=""/>
    <div class="header">
      <p>
        <span class="customerName"></span>, thanks for accepting quote <span class="variationName"></span>,
        <br/>Please check all details are correct below.
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
      <a class="btn btn-lg btn-primary pull-right" onclick="NS_Rental.quote.acceptCustomer();">Continue</a>
    </div>
  </div>
  <div class="delivery clearfix hidden">
    <div class="header">
      <p>
        <span class="customerName"></span>, thanks for accepting quote <span class="variationName"></span>,
        <br/>Please check all details are correct below.
      </p>
      <p>
        <span class="base">Delivery Details</span> 
        <span class="note">(Please include all information)</span>
        <span>(</span><input type="checkbox" name="residential_delivery" value="1" onclick="NS_Rental.quote.setResidentialDelivery();"/><span>)</span>
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
        <a class="btn btn-lg btn-primary pull-right" onclick="NS_Rental.quote.acceptDelivery();">Confirm Booking</a>
      </div>
    </div>
  </div>
  </form>
  <div class="templates hidden">
    <div class="base">
<div class="row">
  <div class="col-sm-4">
    <table cellpadding="10">
      <tr><td><img style="width: 70%" src="https://rentevent.com.au/wp-content/uploads/2017/09/Rent_Event_Logo_Ochre.png" /></td></tr>
      <tr><td>​E. rebookings@ntes.net.au</td></tr>
      <tr><td>​Tel <b>08 8989 1767</b></td></tr>
      <tr><td>PO Box 3335, Palmerston NT 0831</td></tr>
    </table>
  </div>
  <div class="col-sm-8">
    <table cellpadding="10">
      <tr><td class="title"><u>QUOTATION</u></td></tr>
      <tr><td><br><br></td></tr>
      <tr>
        <td><b>Attention {CUSTOMER_FIRST_NAME} {CUSTOMER_LAST_NAME}</b></td>
        <td><b>Date {DATE_TODAY}</b></td>
      </tr>
      <tr>
        <td>Job Description</td><td>{NAME}</td>
      </tr>
      <tr>
        <td>Job Location</td><td>{DELIVERY_ADDRESS_STRING}</td>
      </tr>
      <tr>
        <td>Start Date: {DELIVERY_DATE_STRING}</td>
        <td>End Date: {COLLECTION_DATE_STRING}</td>
      </tr>
      <tr>
        <td><b>Valid Until:</b></td>
        <td><b>{EXPIRATION_DATE_STRING} {EXPIRATION_TIME}</b></td>
      </tr>	
    </table>
  </div>
</div>
<br>
<br>
<div>
  <p>Dear {CUSTOMER_FIRST_NAME} {CUSTOMER_LAST_NAME},</p>

  <p>Thank you for the opportunity to quote ​{NAME}.</p>

  <div class="totalVariations_VARIATION_COUNT_">
    <p class="singleVariation">Below we have included a solution we feel will meet or exceed your expectations.</p>
    <p class="totalVariations_VARIATION_COUNT_ manyVariations">Below we have _VARIATION_COUNT_ solutions  to choose from.</p>
  </div>

  <p>Our staff will deliver all equipment to {DELIVERY_ADDRESS_STRING} on {DELIVERY_DATE_STRING}. Our team will also set up anything as described.</p>

  <p class="has_SERVICE_DATA_FLAG_Data">Following your event we will pack away all of the rented equipment according to the description within ‘services’ as well.</p>

  <p>We look forward to working with you soon, please check that everything is included and that all of the details are correct so we can proceed.</p>
</div>
<div class="expired_EXPIRATION_FLAG_">
{VARIATIONS}
</div>
    </div>
    <div class="variation">
    <?php require_once(VIEWPATH.'frontend/quote/variation.php'); ?>
    </div>
    <table class="itemRow"><tbody><tr>
      <td>{QUANTITY}</td>
      <td><img class="itemThumbnail" src="{THUMBNAIL}"/></td>
      <td>{TITLE}<p class="description">{DESCRIPTION}</p></td>
      <td><span style="display:inline-block;">{CURRENCY}&nbsp;{PRICE}</span></td>
      <td>{DISCOUNT_TEXT}</td>
      <td>{TOTAL}</td>
    </tr></tbody></table>
    <table class="serviceRow"><tbody><tr>
      <td>{QUANTITY}</td>
      <td>{PEOPLE}</td>
      <td>{TITLE}<p class="description">{DESCRIPTION}</p></td>
      <td><span style="display:inline-block;">{CURRENCY}&nbsp;{PRICE}</span></td>
      <td>{DISCOUNT_TEXT}</td>
      <td>{TOTAL}</td>
    </tr></tbody></table>
  </div>
</div>
<script type="text/javascript">
NS_Rental['quote']={
  'listParsers':{
    'active':function(e,extraConfig){
      var deliveryContact=((e['delivery_contact_json']!=null)?JSON.parse(e['delivery_contact_json']):{})
        ,entryHTML=extraConfig['template']
          .replace('_NAME_','<a onclick="NS_Rental.quote.load(\''+e['code']+'\');">'+((e['name']!=null)?(e['name']+'<br/>'):'')+e['code']+'</a>')
          .replace('_LOCATION_',(
            (e['delivery_address_id']>0)?(
              (typeof(deliveryContact['venue'])!='undefined')?deliveryContact['venue']:((e['address_line_1']+((e['address_line_2']!='')?(', '+e['address_line_2']):'')+'<br/>'+e['address_city']))
            ):''))
          .replace('_START_',e['rent_start']).replace('_STATUS_',e['status'])
          .replace('_EXPIRATION_DATETIME_',e['expiration_datetime']);
      return entryHTML;
    }
    ,'previous':function(e,extraConfig){
      var deliveryContact=((e['delivery_contact_json']!=null)?JSON.parse(e['delivery_contact_json']):{})
        ,entryHTML=extraConfig['template']
          .replace('_NAME_','<a onclick="NS_Rental.quote.load(\''+e['code']+'\');">'+((e['name']!=null)?(e['name']+'<br/>'):'')+e['code']+'</a>')
          .replace('_LOCATION_',(
            (e['delivery_address_id']>0)?(
              (typeof(deliveryContact['venue'])!='undefined')?deliveryContact['venue']:((e['address_line_1']+((e['address_line_2']!='')?(', '+e['address_line_2']):'')+'<br/>'+e['address_city']))
            ):''))
          .replace('_START_',e['rent_start']).replace('_STATUS_',e['status'])
          .replace('_EXPIRATION_DATETIME_',e['expiration_datetime']);
      return entryHTML;
    }
  }
  ,'loadList':function(mode){
    var _this=this,_that=NS_Rental,mode=mode;
    
    ['active','previous'].forEach(function(m){
      if (m!=mode){
        _NS.DOM.addClass('#NS_Rental > .body.quotes div.'+m+'Quotes','hidden');
      }
    });
    
    _NS.post('<?php echo NS_BASE_URL; ?>quote/filtered',{'mode':mode},{'success':function(reply){
      var reply=reply,result=[]
        ,template=document.querySelector('#NS_Rental > .body.quotes .templates > .'+mode+'Entry').innerHTML;

      reply.data.entries.forEach(function(e){
        var e=e;
        result.push(_this.listParsers[mode](e,{'template':template}));
      });
      document.querySelector('#NS_Rental > .body.quotes div.'+mode+'Quotes .list').innerHTML=result.join('');
    }},1);
    
    _NS.DOM.removeClass('#NS_Rental > .body.quotes div.'+mode+'Quotes','hidden');
    _that.loadSection('quotes');
  }
  ,'data':false
  ,'container':'#NS_Rental > .body.quote > .content'
  ,'chosenVariation':{}
  ,'reset':function(){
    var _this=this;
    _this['chosenVariation']={
      'code':''
      ,'customer':{}
      ,'residential_address':{}
      ,'delivery_address':{}
    };
    _this['data']=false;
    _NS.DOM.resetFields('#NS_Rental .body.quote #quoteAcceptanceForm');
  }
  ,'init':function(){
    var _this=this,_that=NS_Rental;
    _this.load(_that.config.requestParts[1]);
  }
  ,'loadList__':function(mode){
    var _this=this,_that=NS_Rental,mode=mode,el=document.querySelector('#NS_Rental > .body.quotes #'+mode+'QuotesList');
    
    ['active','closed'].forEach(function(m){
      if (m!=mode){
        _NS.DOM.addClass('#NS_Rental > .body.quotes div.'+m+'Quotes','hidden');
      }
    });
    
    if (el.dataset['dataTables']==1){
      jQuery('#'+mode+'QuotesList').DataTable().ajax.reload();
    }
    else {
      _NS.jQuery.dataTable({
        'dataID':mode+'QuotesList'
        ,'URL':'<?php echo NS_BASE_URL; ?>quote/filtered'
        ,'columns':[{'name':'name','sorted':'asc'},{'sortable':false},{'sortable':false},{'sortable':false},{'sortable':false}]
        ,'parser':function(e){
          var template=''
            ,maxTimestamp=e['delivery_timestamp'],currentTimestamp=(new Date).getTime();
          if (e['expiration_timestamp']>maxTimestamp){
            maxTimestamp=e['expiration_timestamp'];
          }
          return [
            '<a onclick="NS_Rental.quote.load(\''+e['code']+'\');">'+e['name']+'</a>',e['delivery_datetime']+' - '+e['collection_datetime'],e['expiration_datetime'],e['status']
            ,((e['delivery_address_id']>0)?(e['address_line_1']+((e['address_line_2']!='')?(', '+e['address_line_2']):'')+'<br/>'+e['address_city']+', '+e['address_state']+', '+e['address_postcode']):'')
              
          ];
        }
      });
      el.dataset['dataTables']=1;
    }
    _NS.DOM.removeClass('#NS_Rental > .body.quotes div.'+mode+'Quotes','hidden');
    _that.loadSection('quotes');
  }
  ,'load':function(code){
    var _this=this,_that=NS_Rental,variations=[];
    _this.reset();
    _NS.get('<?=NS_BASE_URL;?>quote/load/'+code,{},{'success':function(reply){
      var reply=reply,viewTemplate=document.querySelector('#NS_Rental .body.quote > .templates > .base').innerHTML
      ,keys={
        '{}':[
          'delivery_time','delivery_date_string'
          ,'collection_date_string','collection_time'
          ,'expiration_time','expiration_date_string'
          ,'delivery_address_string','name','date_today'
          ,'variation_count'
        ]
        ,'__':['expiration_flag','variation_count']
      }
      ,addressTypes=['residential','delivery']
      ,vatiationCodes={};
      
      addressTypes.forEach(function(at){
        keys['{}'].push(at+'_address_string');
        if (reply['data'][at+'_address_id']>0){
          reply['data'][at+'_address_string']=reply['data'][at+'_address']['city']+', '+reply['data'][at+'_address']['line_1']+', '+reply['data'][at+'_address']['state']+', '+reply['data'][at+'_address']['postcode'];
        }
        else {
          reply['data'][at+'_address_string']='To be advised';
        }
      });
      
      reply['data']['delivery_address']=Object.assign(reply['data']['delivery_address'],reply['data']['delivery_contact']);
      
      _this['data']=reply['data'];
      _this['data']['variationCodes']={};
      
      reply['data']['variations'].forEach(function(v,vi){
        _this['data']['variationCodes'][v['code']]=vi;
        variations.push(_this.parseVariation(v));
      });
      
      for (var k in reply['data']['customer']){
        reply['data']['customer_'+k]=reply['data']['customer'][k];
        keys['{}'].push('customer_'+k);
      }
      viewTemplate=_this.parseTemplateVars(viewTemplate,keys,reply['data'])
        .replace('{VARIATIONS}',variations.join(''))
        .replace('{CUSTOMER_COMPANY}',reply['data']['customer_company'])
        .replace(/{CURRENCY}/g,'$');
      //console.log(variations.join());
      document.querySelector(_this.container)
        .insertAdjacentHTML('beforeend',viewTemplate);
      _that.loadSection('quote');
    }},1);
  }
  ,'parseVariation':function(variationData){
    var _this=this
    ,variationData=variationData
    ,variationKeys={
      '{}':['name','code','total_items','total_services','discount_amount','grand_total','tax','deposit_mode','deposit_text','due_datetime','notes','discussion_link']
      ,'__':['code','purchase_order','service_data_flag','item_data_flag','booking_flag','deposit_mode']
    }
    ,baseTemplate=document.querySelector('#NS_Rental .body.quote > .templates > .variation').innerHTML
    ,itemRowTemplate=document.querySelector('#NS_Rental .body.quote > .templates > table.itemRow > tbody').innerHTML
    ,itemKeys={'{}':['quantity','price','title','description','discount_text','total', 'thumbnail']}
    ,itemRows=[]
    ,serviceRowTemplate=document.querySelector('#NS_Rental .body.quote > .templates > table.serviceRow > tbody').innerHTML
    ,serviceKeys={'{}':['quantity','price','title','description','discount_text','total','people']}
    ,serviceRows=[];
    
    variationData['entries']['items'].forEach(function(itemData){
      itemRows.push(_this.parseTemplateVars(itemRowTemplate,itemKeys,itemData));
    });
    variationData['entries']['services'].forEach(function(serviceData){
      serviceRows.push(_this.parseTemplateVars(serviceRowTemplate,serviceKeys,serviceData));
    });
    
    return _this.parseTemplateVars(baseTemplate,variationKeys,variationData)
      .replace('<tbody class="itemRows"></tbody>','<tbody class="itemRows">'+itemRows.join()+'</tbody>')
      .replace('<tbody class="serviceRows"></tbody>','<tbody class="serviceRows">'+serviceRows.join()+'</tbody>');
  }
  ,'parseTemplateVars':function(templateText,templateKeys,templateData){
    var templateText=templateText,mode='';
    for (mode in templateKeys){
      //console.log(mode+': '+JSON.stringify(templateKeys[mode],null,2));
      //console.log(templateText);
      templateKeys[mode].forEach(function(k){
        templateText=templateText
          .replace(new RegExp(mode[0]+k.toUpperCase()+mode[1],'g'),templateData[k]);
      });
    }
    return templateText;
  }
  ,'acceptVariation':function(code){
    var _this=this,code=code,formElement='#NS_Rental > .body.quote #quoteAcceptanceForm'
    ,customerName=_this['data']['customer']['first_name']
    ,variationData=_this['data']['variations'][_this['data']['variationCodes'][code]]
    ,variationName=_this['data']['variations'][_this['data']['variationCodes'][code]]['name']
    ,purchaseOrderElement=$('#NS_Rental > .body.quote #quoteVariation'+code+' input[name="purchase_order"]')
    ,purchaseOrderString=document.querySelector('#NS_Rental > .body.quote #quoteVariation'+code+' input[name="purchase_order"]').value.trim();
    
    _NS.DOM.fillFields(formElement,_this['data']);

    if (variationData['purchase_order']==1){
      if (purchaseOrderString==''){
        $(purchaseOrderElement).attr('title','Purchase order should be set');
        NS_Rental.setFailedField(purchaseOrderElement);
        return false;
      }
      console.log('purchaseOrder: '+purchaseOrderString);
      document.querySelector(formElement+' input[name="purchase_order"]').value=purchaseOrderString;
    }

    document.querySelector(formElement+' input[name="code"]').value=code;
    document.querySelectorAll(formElement+' span.customerName').forEach(function(e){
      e.innerHTML=customerName;
    });
    document.querySelectorAll(formElement+' span.variationName').forEach(function(e){
      e.innerHTML=variationName;
    });
    if (variationData['notes']!=null){
      _NS.DOM.removeClass(formElement+' div.bookingNotes.hidden','hidden');
      document.querySelector(formElement+' div.bookingNotes p.bookingNotes').innerHTML=variationData['notes'];
    }
    else {
      _NS.DOM.addClass(formElement+' div.bookingNotes','hidden');
    }

    if (_this['data']['residential_address']['phone']==null){
      _this['data']['residential_address']['phone']=_this['data']['customer']['phone'];
    }
    if (typeof(_this['data']['delivery_contact']['phone'])=='undefined' && _this['data']['delivery_address_id']>0 && _this['data']['delivery_address']['phone']!=null){
      _this['data']['delivery_contact']['phone']=_this['data']['delivery_address']['phone'];
    }
    
    _NS.DOM.fillFields(formElement,_this['data']['customer'],'customer');
    _NS.DOM.fillFields(formElement,_this['data']['residential_address'],'residential_address');
    _NS.DOM.fillFields(formElement,_this['data']['delivery_address'],'delivery_address');
    _NS.DOM.fillFields(formElement,_this['data']['delivery_contact'],'delivery_contact');/**/
    
    _NS.DOM.addClass(_this.container+', '+formElement+' > div','hidden');
    _NS.DOM.removeClass(formElement+' > div.customer','hidden');
    //_NS.DOM.removeClass(formElement+' > div.delivery','hidden');
  }
  ,'acceptCustomer':function(){
    var _this=this,data=_NS.DOM.getFormData('#NS_Rental .body.quote #quoteAcceptanceForm')
    ,formSelector='#NS_Rental .body.quote #quoteAcceptanceForm',el=null;
    data['customer']['phone']=data['residential_address']['phone'];
    _NS.post('<?php echo NS_BASE_URL; ?>booking/acceptCustomer',data,{'success':function(reply){
      var reply=reply,fields=['customer_id','residential_address_id'];
      fields.forEach(function(f){
        var el=document.querySelector(formSelector+' input[name="'+f+'"]');
        if (el!=null && typeof(reply['data'][f])!='undefined'){
          el.value=reply.data[f];
        }
      });
      _NS.DOM.addClass(_this.container+', '+formSelector+' > div','hidden');
      _NS.DOM.removeClass(formSelector+' > div.delivery','hidden');
    },'fail':function(reply){
      var field='',el=null,className='';
      for (field in reply['error_fields']){
        el=$(formSelector+' input[name="'+field+'"]');
        el.attr('title',reply['error_fields'][field]);
        NS_Rental.setFailedField(el);
      }
    }},1);
  }
  ,'acceptDelivery':function(){
    var _this=this,data=_NS.DOM.getFormData('#NS_Rental .body.quote #quoteAcceptanceForm')
    ,formSelector='#NS_Rental .body.quote #quoteAcceptanceForm',el=null;
    data['customer']['phone']=data['residential_address']['phone'];
    _NS.post('<?php echo NS_BASE_URL; ?>booking/acceptDelivery',data,{'success':function(reply){
      var reply=reply,fields=['delivery_address_id'],code=document.querySelector(formSelector+' input[name="code"]').value;
      fields.forEach(function(f){
        var el=document.querySelector(formSelector+' input[name="'+f+'"]');
        if (el!=null && typeof(reply['data'][f])!='undefined'){
          el.value=reply.data[f];
        }
      });
      _NS.DOM.addClass(_this.container+', '+formSelector+' > div','hidden');
      
      _NS.post('<?=NS_BASE_URL;?>booking/create',_NS.DOM.getFormData(formSelector),{'success':function(reply){
        NS_Rental.booking.load(code);
      }},1);
    },'fail':function(reply){
      var field='',el=null,className='';
      for (field in reply['error_fields']){
        el=$(formSelector+' input[name="'+field+'"]');
        el.attr('title',reply['error_fields'][field]);
        NS_Rental.setFailedField(el);
      }
    }},1);
  }
  
  ,'setResidentialDelivery':function(){
    var formSelector='#NS_Rental .body.quote #quoteAcceptanceForm'
    ,el=document.querySelector(formSelector+' input[name="residential_delivery"]');
    if (el.checked==true){
      _NS.DOM.addClass(formSelector+' > div.delivery > .addressFields','hidden');
    }
    else {
      _NS.DOM.removeClass(formSelector+' > div.delivery > .addressFields.hidden','hidden');
    }
  }<?php /** / ?>
  ,'accept':function(code){
    var _this=this,_that=NS_Rental,code=code,params={};
    if (typeof(code)!='undefined'){
      _this.chosenVariation['code']=code;
    }
    else {
      code=_this.chosenVariation['code'];
    }
    _this.chosenVariation['purchase_order']=document.querySelector(_this.container+' #quoteVariation'+code+' input[name="purchase_order"]').value;
    _NS.post('<?=NS_BASE_URL;?>booking/create',_this.chosenVariation,{'success':function(reply){
      _that.bookings.load(code);
    },'fail':function(reply){
      _NS.DOM.addClass(_this.container+' > .content','hidden');
      _NS.DOM.addClass(_this.container+' > fieldset','hidden');
      _NS.defaultReplyActions.fail(reply);
      switch(reply.data['failed_section']){
        case 'customer':
        case 'residential_address':
        case 'delivery_address':
          _NS.DOM.fillFields(_this.container+' > .'+reply.data['failed_section'],reply.data[reply.data['failed_section']],reply.data['failed_section']);
          _NS.DOM.removeClass(_this.container+' > .'+reply.data['failed_section']+'.hidden','hidden');
        break;
        default:
          _NS.DOM.removeClass(_this.container+' > .content.hidden','hidden');
        break;
      }
    }},1);
  }
  ,'updateAccept':function(sectionCode){
    var _this=this,_that=NS_Rental,sectionCode=sectionCode;

    switch(sectionCode){
      case 'customer':
      case 'residential_address':
      case 'delivery_address':
        Object.assign(_this.chosenVariation,_NS.DOM.getFormData(_this.container+' > .'+sectionCode));
      break;
    }
    _this.accept();
  }<?php /**/ ?>
};
</script>