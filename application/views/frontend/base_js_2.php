<script type="text/javascript">
Array.prototype.remove = function() {
    var what, a = arguments, L = a.length, ax;
    while (L && this.length) {
        what = a[--L];
        while ((ax = this.indexOf(what)) !== -1) {
            this.splice(ax, 1);
        }
    }
    return this;
};
var NS_Rental={
  'config':{
    'baseURL':'<?php echo NS_BASE_URL; ?>'
<?php
    include(APPPATH.'views/item/json.js');
    include(APPPATH.'views/tag/json.js');
    include(APPPATH.'views/category/json.js');
?>
    ,'labels':{
      '2':{
        'ALL_BOOKED':'all are booked'
        ,'CHECKING_AVAILABILITY':'checking availability'
        ,'PERIOD_NOT_DEFINED':'define period'
        ,'ADD_TO_CART':'add to cart'
        ,'UPDATE_CART':'update cart'
      }
    }
    ,'languages':{
      '2':'English'
      ,'list':[2]
      ,'chosen':2
    }
    ,'requestParts':[]
    
  }
  ,'setFailedField':function(el){
    var el=el,className='';
    console.log('set failed field');
        //el.attr('title',reply['error_fields'][field]);
    className=el.attr('class');
    console.log(className);
    el.attr('class',className+=' failedField');
    el.tooltip('show');
    setTimeout(function(el){
      var el=el;
      el.on('focus',function(){
        var className=$(this).attr('class');
        className=className.replace(' failedField','');
        el.tooltip('destroy');
        el.attr('class',className);
      });
    },50,el);
  }
  ,'Stripe':{
    'key': '<?php echo STRIPE_KEY_PUBLIC; ?>'
    ,'currency':'AUD'
    ,'checkout':null
    ,'token':null
    ,'run':function(title,amount,callback){
      var _this=this,title=title,amount=amount,recalculatedAmount=(parseFloat(amount).toFixed(2)*100),callback=callback;
      console.log('title: '+title+"\namount: "+amount);
      if (amount==0){
        _NS.alert.open('fail','Fail','Payment is not needed, call admin',1);
        return false;
      }/** /
      if (amount.indexOf('.')!==false){
        recalculatedAmount=amount*100;
      }
      /** /
      window.addEventListener('popstate', function () {
        if (typeof(stripeConfigs.bookingFinalization)!='undefined'){
          stripeConfigs.bookingFinalization.close();
        }
      });/**/
      _this.checkout=StripeCheckout.configure({
        key: _this.key,
        locale: 'auto',
        'currency':_this.currency,
        token: function (token) {
          _this.token=token.id;
          //NS_Rental.booking.newPayment['token']=token.id;
          //console.log(JSON.stringify(token,null,2));
          callback({
            'processor':'stripe'
            ,'token':token.id
            ,'currency_code':_this.currency
            ,'amount':amount
            ,'card':{
              'ending':token.card.last4
              ,'type':token.card.brand
            }
          });
          //stripeConfigs.frontController.finalizeBooking();
        }
        ,'closed':function(){
          console.log('closed checkout');
        }
      });
      _this.checkout.open({'name':title,'amount':recalculatedAmount});
      window.addEventListener('popstate', function () {
        _this.checkout.close();
      });
      /** /
      stripeConfigs.popup['finalization']['amount']=document.getElementById('bookingFinalizationButton').dataset['amount'];
        stripeConfigs['bookingFinalization'] = 
        window.addEventListener('popstate', function () {
          if (typeof(stripeConfigs.bookingFinalization)!='undefined'){
            stripeConfigs.bookingFinalization.close();
          }
        });
        
        document.getElementById('bookingFinalizationButton').addEventListener('click',function(e){
          self.disableButtons.call(self);
          stripeConfigs['frontController']=self;
          stripeConfigs.bookingFinalization.open(stripeConfigs.popup['finalization']);
          //self.enableButtons.call(self);

          e.preventDefault();
          return false;
        });/**/
    }
    ,'popup': {
      'full': {'name': 'Full payment'}
      ,'deposit': {'name': 'Deposit payment'}
      ,'finalization':{'name':'Finalize payment'}
    }
  }
  ,'updateFilters':function(action){
    console.log('updateFilters: '+action);
    _NS.post(NS_Rental.config.baseURL+'frontend/updateFilters',{
      'tags':NS_Rental.tags.chosen
      ,'period':((NS_Rental.period.ready)?{'start_date':NS_Rental.period.start,'end_date':NS_Rental.period.end}:false)
      ,'cart':NS_Rental.cart.content
    },{'success':function(reply){}},1);
  }
  ,'loadSection':function(sectionID){
    var sectionID=sectionID, sectionElement={};
    document.querySelectorAll('#NS_Rental > .body').forEach(function(s){
      if (s.dataset['section']!=sectionID){
        _NS.DOM.addClass(s,'hidden');
      }
      else {
        sectionElement=s;
      }
    });
    _NS.DOM.removeClass(sectionElement,'hidden');
  }
  ,'init':function(){
    var _this=NS_Rental,  tags=[], items=[], categories=[], labels=_this.config.labels[_this.config.languages.chosen];
    
    _this.config.requestParts=window.location.href.substr(_this.config.baseURL.length).split('/');
    
    switch(_this.config.requestParts[0]){
      case 'bookings':
      case 'booking':
        return _this.booking.init(); 
      break;
      case 'quote':
        return _this.quote.init();
      break;
    }
    
    _this.config.categories['default'].forEach(function(c){
      categories.push('<option value="'+c+'">'+_this.config.categories[c]['title']+'</option>');
    });
    document.querySelector('#NS_Rental > .header select[name="category_id"]').innerHTML=categories.join('');
    
    _this.config.tags['default'].forEach(function(t){
      tags.push('<div class="tag'+t+'" onclick="NS_Rental.tags.toggle('+t+');">'+_this.config.tags[t]['code']+'</div>');
    });
    document.querySelector('#NS_Rental > .header > .tags .list').innerHTML=tags.join(' ');
    
    _this.config.items['default'].forEach(function(i){
      items.push('<div class="item'+i+' col-sm-4" >'
        +'<div class="thumbnail">'
          +'<div class="image" onclick="NS_Rental.items.view('+i+');">'
            +'<img class="img-responsive" src="'+_this.config.items[i]['thumbnail']+'"/>'
            +'<p class="white-text">'+_this.config.items[i]['description']+'</p>'
          +'</div>'
          +'<div class="caption">'
            +'<span class="title" onclick="NS_Rental.items.view('+i+');">'+i+'. '+_this.config.items[i]['title']+'</span>'
            +((typeof(_this.config.items[i]['package'])!='undefined')?'<div>is_package<pre>'+JSON.stringify(_this.config.items[i]['package'],null,2)+'</pre></div>':'')
            +'<div class="action '+((_this.period['ready'])?'checking':'period')+'">'
              +'<div class="available">'
                +'<select class="quantity quantity'+i+'"></select><a class="btn btn-primary" onclick="NS_Rental.cart.add('+i+');"><span class="add">'+labels.ADD_TO_CART+'</span><span class="update">'+labels.UPDATE_CART+'</span></a>'
              +'</div>'
              +'<div class="booked"><span>'+labels.ALL_BOOKED+'</span></div>'
              +'<div class="checking"><span>'+labels.CHECKING_AVAILABILITY+'</span></div>'
              +'<div class="period"><span onclick="NS_Rental.period.show();">'+labels.PERIOD_NOT_DEFINED+'</span></div>'
            +'</div>'
          +'</div>'
        +'</div>'
      +'</div>');
    });
    document.querySelector('#NS_Rental > .items .list').innerHTML=items.join('');
    
    _this.tags.init();
    _this.items.getPaginated(1);
    _this.period.init();
    
    //_NS.post('<?php echo NS_BASE_URL; ?>items');
    //console.dir(NS_Rental.config);
  }
  ,'items':{
    'initiated':false
    ,'chosen':0
    ,'elements':{
      'init':function(){
        if (this.initiated) return false;
        this['title']=document.querySelector('#NS_Rental > .item .title');
        this['categories']=document.querySelector('#NS_Rental > .item .categories');
        this['description']=document.querySelector('#NS_Rental > .item .description');
        this['action']=document.querySelector('#NS_Rental > .item .action');
        this['quantity']=document.querySelector('#NS_Rental > .item .action .quantity');
        this['initiated']=true;
      }
      ,'initiated':false/**
      ,'title':document.querySelector('#NS_Rental > .item .title')
      ,'categories':document.querySelector('#NS_Rental > .item .categories')
      ,'description':document.querySelector('#NS_Rental > .item .description')
      ,'action':document.querySelector('#NS_Rental > .item .action')
      ,'quantity':document.querySelector('#NS_Rental > .item .action .quantity')**/
    }
    ,'init':function(){
      var _this=this,_that=NS_Rental, tags=[], items=[], categories=[], labels=_that.config.labels[_that.config.languages.chosen];
      this.elements.init();
      if (!_this.initiated){
        _that.config.categories['default'].forEach(function(c){
          categories.push('<option value="'+c+'">'+_that.config.categories[c]['title']+'</option>');
        });
        document.querySelector('#NS_Rental > .header select[name="category_id"]').innerHTML=categories.join('');

        _that.config.tags['default'].forEach(function(t){
          tags.push('<div class="tag'+t+'" onclick="NS_Rental.tags.toggle('+t+');">'+_that.config.tags[t]['code']+'</div>');
        });
        document.querySelector('#NS_Rental > .header > .tags .list').innerHTML=tags.join(' ');

        _that.config.items['default'].forEach(function(i){
          items.push('<div class="item'+i+' col-sm-4" >'
            +'<div class="thumbnail">'
              +'<div class="image" onclick="NS_Rental.items.view('+i+');">'
                +'<img class="img-responsive" src="'+_that.config.items[i]['thumbnail']+'"/>'
                +'<p class="white-text">'+_that.config.items[i]['description']+'</p>'
              +'</div>'
              +'<div class="caption">'
                +'<span class="title" onclick="NS_Rental.items.view('+i+');">'+i+'. '+_that.config.items[i]['title']+'</span>'
                +((typeof(_that.config.items[i]['package'])!='undefined')?'<div>is_package<pre>'+JSON.stringify(_that.config.items[i]['package'],null,2)+'</pre></div>':'')
                +'<div class="action '+((_that.period['ready'])?'checking':'period')+'">'
                  +'<div class="available">'
                    +'<select class="quantity quantity'+i+'"></select><a class="btn btn-primary" onclick="NS_Rental.cart.add('+i+');"><span class="add">'+labels.ADD_TO_CART+'</span><span class="update">'+labels.UPDATE_CART+'</span></a>'
                  +'</div>'
                  +'<div class="booked"><span>'+labels.ALL_BOOKED+'</span></div>'
                  +'<div class="checking"><span>'+labels.CHECKING_AVAILABILITY+'</span></div>'
                  +'<div class="period"><span onclick="NS_Rental.period.show();">'+labels.PERIOD_NOT_DEFINED+'</span></div>'
                +'</div>'
              +'</div>'
            +'</div>'
          +'</div>');
        });
        document.querySelector('#NS_Rental > .items .list').innerHTML=items.join('');

        _that.tags.init();
        _that.items.getPaginated(1);
        _that.period.init();
      }
      _this.initiated=true;
      _that.loadSection('items');
    }
    ,'getPaginated':function(page){
      var _this=NS_Rental
      ,finalItems=false, taggedItems=false, page=page,tagID=0;
      this.chosen=0;
      
      //,startTimestamp=(new Date()).getTime(),endTimestamp=0;
      
      //console.log('starting calculations '+startTimestamp);
      _this.tags.reset();
      
      finalItems=_this.config.categories[document.querySelector('#NS_Rental > .header select[name="category_id"]').value]['items'];
      
      taggedItems=_this.tags.getItems();
      if (taggedItems!==false){
        if (finalItems===false){
          finalItems=taggedItems;
        }
        else {
          finalItems=finalItems.filter(function(n){
            return taggedItems.indexOf(n) !== -1;
          });
        }
      }
      
      if (finalItems===false){
        finalItems=_this.config.items['default'];
      }
      _NS.DOM.removeClass('#NS_Rental > .items .list > div','active');
      
      finalItems.forEach(function(i){
        if (_this.config.items[i]){
          _NS.DOM.addClass('#NS_Rental > .items .list > .item'+i,'active');
          _this.config.items[i]['tags'].forEach(function(t){
            _this.tags.show(t);
          });
        }
      });
      NS_Rental.loadSection('items');
      endTimestamp=(new Date()).getTime();
      
      //console.log('calculations ended '+endTimestamp);
      //console.log('processed within '+(endTimestamp-startTimestamp)+'ms')
      //console.dir(filteredItems);
      NS_Rental.updateFilters('paginatedItems');
    }
    ,'view':function(itemID){
      var itemID=itemID,itemDetails=NS_Rental.config.items[itemID],categories=[];
      this.chosen=itemID;
      this.elements.init();
      this.elements['title'].innerHTML=itemDetails.title;
      this.elements['description'].innerHTML=itemDetails.description;
      this.elements['quantity'].innerHTML=document.querySelector('#NS_Rental > .items .item'+itemID+' .action .quantity').innerHTML;
      this.elements['action'].className=document.querySelector('#NS_Rental > .items .item'+itemID+' .action').className;
      NS_Rental.config.categories.default.forEach(function(c){
        if (c>0){
          if (NS_Rental.config.categories[c]['items'].indexOf(''+itemID+'')>=0){
            categories.push(NS_Rental.config.categories[c]['title']);
          }
        }
      });
      this.elements['categories'].innerHTML=categories.join(',');
      NS_Rental.loadSection('item');
    }
  }
  ,'availability':{
    'current':{}
    ,'previous':{}
    ,'cache':{}
    ,'update':function(){
      var data={};
      if (NS_Rental.period.ready){
        data['period']={'start_date':NS_Rental.period.start,'end_date':NS_Rental.period.end};
      }
      
      this.previous=Object.assign({},this.current);
      //console.log('availability');
      /**/
      _NS.post(NS_Rental.config.baseURL+'item/filtered',data,{'success':function(reply){
        reply.data.entries.forEach(function(item){
          var i=0,item=item,max=(item['quantity']-item['booked']), final=0, options=[],classToSet='booked';
          NS_Rental.availability.current[item['item_id']]=max;
          if (document.querySelector('#NS_Rental > .items > .list > .item'+item['item_id'])!=null){
          if (typeof(NS_Rental.config.items[item['item_id']])!='undefined'){
            if (max>0){
              //console.log('item'+item['item_id']+': '+max+' VS '+NS_Rental.availability.previous[item['item_id']]);
              if (max!=NS_Rental.availability.previous[item['item_id']]){
                if (max>1000){max=1000;}
                //console.log('max is new: '+max);
                for (i=1;i<=max;i++){
                  options.push('<option class="v'+i+'" value="'+i+'"'+((NS_Rental.cart.content[item['item_id']]==i)?' selected="selected"':'')+'>'+i+'</option>');
                }
                if (document.querySelector('#NS_Rental > .items > .list > .item'+item['item_id'])!=null){
                  //console.log('#NS_Rental > .items > .list > .item'+item['item_id']+' .action .quantity');
                  //console.log(options.join(''));
                  document.querySelector('#NS_Rental > .items > .list > .item'+item['item_id']+' .action .quantity').innerHTML=options.join('');
                  if (NS_Rental.items.chosen==item['item_id']){
                    document.querySelector('#NS_Rental > .item .action .quantity').innerHTML=options.join('');
                  }
                }
              }
              
              classToSet='available '+((NS_Rental.cart.content[item['item_id']]>0)?'update':'add');
            }
            
            document.querySelector('#NS_Rental > .items > .list > .item'+item['item_id']+' .action').className='action '+classToSet;
            //_NS.DOM.removeClass('#NS_Rental > .items > .list > .item'+item['item_id']+' .action','checking');
            //_NS.DOM.addClass('#NS_Rental > .items > .list > .item'+item['item_id']+' .action',classToSet);
            
            if (NS_Rental.items.chosen==item['item_id']){
              document.querySelector('#NS_Rental > .item .action').className='action '+classToSet;
              //_NS.DOM.removeClass('#NS_Rental > .item .action','checking');
              //_NS.DOM.addClass('#NS_Rental > .item .action',classToSet);
            }
          }
        }
        });
        if (!NS_Rental.cart.initiated){
          NS_Rental.cart.init();
        }
        NS_Rental.updateFilters('availabilityUpdate');
        //console.log(JSON.stringify(NS_Rental.availability,null,2));
      }},1);/**/
    }
  }
  ,'period':{
    'start_date':''
    ,'end_date':''
    ,'ready':false
    ,'elements':{
      'init':function(){
        if (this.initiated) return false;
        this['label']=document.querySelector('#NS_Rental > .header .periodLabel');
        this['start_date']=document.querySelector('#NS_Rental > .header .period input[name="period_start"]');
        this['end_date']=document.querySelector('#NS_Rental > .header .period input[name="period_end"]');
        this['updater']=document.querySelector('#NS_Rental > .header .period .updater');
        this['closer']=document.querySelector('#NS_Rental > .header .period .closer');
        this['initiated']=true;
      }
      ,'initiated':false/** /
      'label':document.querySelector('#NS_Rental > .header .periodLabel')
      ,'start_date':document.querySelector('#NS_Rental > .header .period input[name="period_start"]')
      ,'end_date':document.querySelector('#NS_Rental > .header .period input[name="period_end"]')
      ,'updater':document.querySelector('#NS_Rental > .header .period .updater')
      ,'closer':document.querySelector('#NS_Rental > .header .period .closer')/**/
    }
    ,'init':function(){
      this.elements.init();
      jQuery('#NS_Rental > .header > .period input[name="period_start"]').datepicker({
        'dateFormat':'M d, yy'
        ,'minDate':(new Date((new Date().getTime())+86400000))
        ,'onSelect':function(dateText,data){
          var m=(parseInt(data.selectedMonth)+1);
          NS_Rental.period.elements.end_date.value='';
          jQuery('#NS_Rental > .header > .period input[name="period_end"]').datepicker('option',{
            'minDate':(new Date(data.selectedYear,data.selectedMonth,data.selectedDay,25))
          });
          //jQuery('#NS_Rental > .header > .period input[name="period_end"]').datepicker('refresh');
          //jQuery('#NS_Rental > .header > .period input[name="period_end"]').datepicker('show');
          NS_Rental.period.elements.start_date.dataset['current']=data.selectedYear+'-'+((m<10)?'0':'')+m+'-'+data.selectedDay;
          NS_Rental.period.elements.end_date.dataset['current']='';
        }
      });
      jQuery('#NS_Rental > .header > .period input[name="period_end"]').datepicker({
        'dateFormat':'M d, yy'
        ,'minDate':(new Date((new Date().getTime())+86400000*2))
        ,'onSelect':function(dateText,data){
          var m=(parseInt(data.selectedMonth)+1);
          NS_Rental.period.elements.end_date.dataset['current']=data.selectedYear+'-'+((m<10)?'0':'')+m+'-'+data.selectedDay;
        }
      });
      <?php /**/ //unset($_SESSION['NS_Rental']['period']); 
      if (!empty($_SESSION['NS_Rental']['period'])){ 
        echo 'NS_Rental.period.prepare(\''.$_SESSION['NS_Rental']['period']['start'].'\',\'start\');NS_Rental.period.prepare(\''.$_SESSION['NS_Rental']['period']['end'].'\',\'end\');NS_Rental.period.update();';
      } /**/?>
    }
    ,'prepare':function(dateString,target){
      var dateString=dateString,dateParts=dateString.split('-');
      NS_Rental.period.elements[target].dataset['current']=dateString;
      jQuery('#NS_Rental > .header > .period input[name="period_'+target+'"]').datepicker('setDate',new Date(dateParts[0],(parseInt(dateParts[1])-1),dateParts[2]));
    }
    ,'show':function(){
      _NS.DOM.removeClass('#NS_Rental > .header > .period','hidden');
    }
    ,'hide':function(){
      _NS.DOM.addClass('#NS_Rental > .header > .period','hidden');
    }
    ,'update':function(){
      this.start=this.elements.start_date.dataset['current'];
      this.end=this.elements.end_date.dataset['current'];
      this.ready=true;
      document.querySelectorAll('#NS_Rental div.action').forEach(function(d){
        d.className='action checking';
      });
      //_NS.DOM.removeClass('#NS_Rental > .items > div .action','period');
      //_NS.DOM.addClass('#NS_Rental > .items > div .action','checking');
      
      NS_Rental.availability.update();
      
      this.elements.label.querySelector('span').innerText=this.elements.start_date.value+' to '+this.elements.end_date.value;
      this.hide();
    }
  }
  ,'tags':{
    'chosen':<?php if (!empty($_SESSION['NS_Rental']['tags'])){ echo json_encode($_SESSION['NS_Rental']['tags']); } else { echo '{}'; } ?>
    ,'shown':{}
    ,'totalChosen':0
    ,'reset':function(){
      this.shown={};
      _NS.DOM.removeClass('#NS_Rental > .header > .tags .shown','shown');
    }
    ,'init':function(){
      var tagID=0;
      for(tagID in NS_Rental.tags.chosen){
        if (NS_Rental.tags.chosen[tagID]>0){
          NS_Rental.tags.totalChosen++;
          _NS.DOM.addClass('#NS_Rental > .header > .tags .tag'+tagID,'active');
        }
      }
    }
    ,'toggle':function(tagID){
      var tagID=tagID,isChosen=NS_Rental.tags.chosen[tagID];
      if (isChosen==1){
        NS_Rental.tags.chosen[tagID]=0;
        NS_Rental.tags.totalChosen--;
        _NS.DOM.removeClass('#NS_Rental > .header > .tags .tag'+tagID,'active');
      }
      else {
        NS_Rental.tags.chosen[tagID]=1;
        NS_Rental.tags.totalChosen++;
        _NS.DOM.addClass('#NS_Rental > .header > .tags .tag'+tagID,'active');
      }
      NS_Rental.items.getPaginated(1);
    }
    ,'getItems':function(){
      var _this=NS_Rental, tagID=0, items=false;
      for (tagID in _this.tags.chosen){
        if (_this.tags.chosen[tagID]==1){
          if (items===false){
            items=_this.config.tags[tagID]['items'];
          }
          else {
            items=items.filter(function(n) {
              return _this.config.tags[tagID]['items'].indexOf(n) !== -1;
            });
          }
        }
      }
      return items;
    }
    ,'show':function(tagID){
      var _this=NS_Rental,tagID=tagID;
      //console.log('show tag '+tagID);
      if (_this.tags.shown[tagID]!=1){
        _NS.DOM.addClass('#NS_Rental > .header > .tags .list .tag'+tagID,'shown');
        _this.tags.shown[tagID]=1;
      }
    }
  }
  ,'cart':{
    'initiated':false
    
    ,'content':<?php if (!empty($_SESSION['NS_Rental']['cart'])){ echo json_encode($_SESSION['NS_Rental']['cart']); } else { echo '{}'; } ?>
    ,'totalInCart':0
    ,'add':function(itemID,quantity){
      var itemID=itemID, quantity=quantity, chosen=false,old=false, packed=[],packedItemID=0;
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
      if (typeof(quantity)!='undefined'){
        this.content[itemID]=quantity;
      }
      else {
        this.content[itemID]=document.querySelector('#NS_Rental '+((chosen)?'.item':'> .items .item'+itemID)+' .action .quantity').value;
      }
      if (NS_Rental.cart.initiated){
        NS_Rental.loadSection('cart');
      }
      if (document.querySelector('#NS_Rental > .cart .list .item'+itemID)==null){
        document.querySelector('#NS_Rental > .cart .list').insertAdjacentHTML('beforeend',this.template
          .replace('_TITLE_',NS_Rental.config.items[itemID]['title'])
          .replace(/_X_/g,itemID)
          .replace('_QUANTITY_',document
            .querySelector('#NS_Rental '+((chosen)?'.item':'> .items .item'+itemID)+' .action .quantity')
            .innerHTML.replace('value="'+this.content[itemID]+'"','value="'+this.content[itemID]+'" selected="selected"')
          )
          .replace('_PACKED_',packed.join(''))
        );
      }
      else {
        document.querySelector('#NS_Rental .items .item'+itemID+' .quantity .v'+this.content[itemID]).selected=true;
      }
      document.querySelector('#NS_Rental .cart select[name="quantity['+itemID+']"] .v'+this.content[itemID]).selected=true;
      document.querySelector('#NS_Rental .items .item'+itemID+' .action').className='action available update';
      NS_Rental.cart.updatePrices();
    }
    ,'update':function(itemID){
      var itemID=itemID, newValue=document.querySelector('#NS_Rental .body.cart select[name="quantity['+itemID+']"]').value;
      NS_Rental.cart.add(itemID,newValue);
      //console.log('old quantity'+itemID+': '+this.content[itemID]+' -> '+newValue);
      //document.querySelector('#NS_Rental .items .item'+itemID+' .quantity .v'+newValue).selected=true;
      //NS_Rental.cart.updatePrices();
    }
    ,'updatePrices':function(){
      _NS.post(NS_Rental.config.baseURL+'cart/update',{'content':NS_Rental.cart.content},{'success':function(reply){
        var itemID=0;
        for (itemID in reply.data){
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
      
      _that.loadSection('cart');
      this.initiated=true;
    }
  }
  ,'customers':{
    'logout':function(){
      _NS.post(NS_Rental.config.baseURL+'user/logout',{},{
        'success':function(reply){
          document.location.replace('<?php echo NS_BASE_URL; ?>');
        }
      },1);
    }
    ,'login':function(){
      _NS.post(NS_Rental.config.baseURL+'customer/login',{'email':document.querySelector('#NS_Rental .cart input[name="customer[email]"]').value,'password':document.querySelector('#NS_Rental .cart input[name="customer[password]"]').value},{
        'success':function(reply){
          //document.location.replace('<?php echo NS_BASE_URL; ?>');
        }
      },1);
    }
  }
};

</script>