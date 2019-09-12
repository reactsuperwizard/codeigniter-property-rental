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
    $cachedDir=APPPATH.'../uploads/cached/';
    include($cachedDir.'item_json.js');
    include($cachedDir.'tag_json.js');
    include($cachedDir.'category_json.js');
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
    _this.items.init();
    
  }
  ,'items':{
    'initiated':false
    ,'chosen':0
    ,'elements':{
      'init':function(){
        if (this.initiated===true) return false;
        this['chosenCategoryID']=0;
        this['title']=document.querySelector('#NS_Rental > .item .title');
        this['categories']=document.querySelector('#NS_Rental > .item .categories');
        this['description']=document.querySelector('#NS_Rental > .item .description');
        this['action']=document.querySelector('#NS_Rental > .item .action');
        this['quantity']=document.querySelector('#NS_Rental > .item .action .quantity');
        this['initiated']=true;
      }
      ,'initiated':false
    }
    ,'init':function(){
      var _this=this,_that=NS_Rental, tags=[], items=[]
        , categories=[], categoryUL=['<li class="list-group-item category0"><a onclick="NS_Rental.items.updateCategory(0);">All items</a></li>'], labels=_that.config.labels[_that.config.languages.chosen]
      ,startTimestamp=(new Date()).getTime()
      ,endTimestamp=0;
      
      console.log('going to initiate items at '+startTimestamp);
      if (_this.initiated===false){
        _this.elements.init();
        _that.config.categories['default'].forEach(function(c){
          //categories.push('<option value="'+c+'">'+_that.config.categories[c]['title']+'</option>');
          if (c>0){
            categoryUL.push('<li class="list-group-item category'+c+'" onclick="NS_Rental.items.updateCategory('+c+');"><a class="list-group-item-link">'+_that.config.categories[c]['title']+'</a></li>');
          }
          //console.log(_that.config.categories[c]['title']+'('+c+'): '+_that.config.categories[c]['items'].length);
        });
        //document.querySelector('#NS_Rental > .header select[name="category_id"]').innerHTML=categories.join('');
        document.querySelector('#NS_Rental > .items .leftPanel .categories ul').innerHTML=categoryUL.join('');

        _that.config.tags['default'].forEach(function(t){
          tags.push('<li class="tag'+t+' btn btn-sm btn-default my-1" onclick="NS_Rental.tags.toggle('+t+');">'+_that.config.tags[t]['code']+'</li>');
        });
        document.querySelector('#NS_Rental > .items .tags ul').innerHTML=tags.join(' ');

        _that.config.items['default'].forEach(function(i){
          items.push('<div id="NS_Rental__item'+i+'" class="item'+i+' col-xs-4 col-sm-3 col-md-3 col-lg-3 '+((_that.config.items[i]['quantity']==0)?' bg-warning':'')+'" >'
            +'<div class="thumbnail">'
              +'<div class="image" onclick="NS_Rental.items.view('+i+');" style="position:relative;">'
                +'<img class="img-responsive img-fluid" src="'+_that.config.items[i]['thumbnail']+'"/>'
                +'<div class="overlay py-auto"><p class="py-auto my-auto">'+_that.config.items[i]['description']+'</p></div>'
              +'</div>'
              +'<div class="caption">'
                +'<span class="title" onclick="NS_Rental.items.view('+i+');">'+_that.config.items[i]['title']+'</span>'
                //+((typeof(_that.config.items[i]['package'])!='undefined')?('<div>is_package<pre>'+JSON.stringify(_that.config.items[i]['package'],null,2)+'</pre></div>'):'')
                +'<div class="action '+((_that.period['ready'])?'checking':'period')+'">'
                  +'<div class="available">'
                    +'<input type="text" class="quantity quantity'+i+' form-control"/>'
                    //+'<select class="quantity quantity'+i+'"></select>'
                    +'<a class="btn btn-primary btn-sm" onclick="NS_Rental.cart.add('+i+');"><span class="add">'+labels.ADD_TO_CART+'</span><span class="update">'+labels.UPDATE_CART+'</span></a>'
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
        
        //_that.items.getPaginated(1);
        _that.period.init();
      }
      endTimestamp=(new Date()).getTime();
      console.log('items initiated at '+endTimestamp);
      console.log('items initiated within '+(endTimestamp-startTimestamp)+'ms');
      _this.initiated=true;
      _that.loadSection('items');
    }
    ,'updateCategory':function(categoryID){
      var _this=this,_that=NS_Rental,categoryID=categoryID,title='';
      
      //console.log('categoryID: '+categoryID);
      //_this['elements']['chosenCategoryID']=(typeof(categoryID)!='undefined')?categoryID:document.querySelector('#NS_Rental > .header select[name="category_id"]').value;
      _this['elements']['chosenCategoryID']=(typeof(categoryID)=='undefined' || _this['elements']['chosenCategoryID']==categoryID)?0:categoryID;
      if (_this['elements']['chosenCategoryID']!=0){
        document.querySelector('#NS_Rental__itemSearch').value='';
      }
      _that.tags.resetValues();
      
      _this.getPaginated(1);
    }
    ,'updateSearch':function(){
      var searchValue=document.querySelector('#NS_Rental__itemSearch').value;
      if (searchValue.trim().length>=3){
        NS_Rental['items'].updateCategory(0);
      }
    }
    ,'getPaginated':function(page){
      var _this=NS_Rental,itemDiv=null
      ,finalItems=false, taggedItems=false
      ,page=page,perPage=18,totalPages=0,startPage=0,endPage=0,pageChoices=21
      ,tagID=0
      ,startTimestamp=(new Date()).getTime(),endTimestamp=0
      ,categoryID=_this['items']['elements']['chosenCategoryID']
      ,itemIndex=0,itemIndexCount=0
      ,searchValue=document.querySelector('#NS_Rental__itemSearch').value
      ,searchValueRegex=(new RegExp(searchValue,'i'));
      this.chosen=0;
      
      
      _NS.DOM.removeClass('#NS_Rental > .items .categories .active','active');
      _NS.DOM.addClass('#NS_Rental > .items .categories .category'+categoryID,'active');
      if (categoryID>0){
        document.querySelector('#NS_Rental > .items p.category .title').innerHTML='Items > '+_this.config['categories'][categoryID]['title'];
        _NS.DOM.removeClass('#NS_Rental > .items p.category .back','hidden');
        //_NS.DOM.addClass('#NS_Rental > .items .categories','hidden');  
      }
      else {
        _NS.DOM.addClass('#NS_Rental > .items p.category .back','hidden');
        document.querySelector('#NS_Rental > .items p.category .title').innerHTML='Items';
        _NS.DOM.removeClass('#NS_Rental > .items .categories','hidden');
      }
      
      
      console.log('starting calculations '+startTimestamp);
      //console.log('chosen category: '+categoryID+"\n"+JSON.stringify(NS_Rental['config']['categories'][categoryID],null,2));
      _this.tags.reset();
      if (typeof(_this['config']['items']['sorted'])=='undefined'){
        _this['config']['items']['sorted']=[].concat(_this['config']['items']['default']);
      }
      
      if (categoryID==0){
        finalItems=_this['config']['items']['sorted'];
      }
      else {
        finalItems=_this['config']['items']['sorted'].filter(function(n){
          return _this['config']['categories'][categoryID]['items'].indexOf(n) !== -1;
        });
      }

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
      
      if (categoryID==0 && searchValue!=''){
        finalItems=finalItems.filter(function(n){
          var n=n,r=false;
          r=searchValueRegex.test(NS_Rental['config']['items'][n]['title']);
          if (r==false){
            //console.log('item'+n+': '+NS_Rental['config']['items'][n]['title']);
            if (typeof(NS_Rental['config']['items'][n]['tags'])!='undefined'){
              NS_Rental['config']['items'][n]['tags'].forEach(function(t){
                if (searchValueRegex.test(NS_Rental['config']['tags'][t]['code'])){
                  r=true;
                }
              });
            }
          }
          return r;
        });
      }

      if (finalItems===false){
        finalItems=_this.config.items['default'];
      }
      console.log('found '+finalItems.length+' elements');
      console.log('calculated indexes within '+((new Date()).getTime()-startTimestamp)+'ms');
      
      _NS.DOM.removeClass('#NS_Rental > .items .list > div','active');
      
      finalItems.forEach(function(i){
        if (_this.config.items[i]){
          //_NS.DOM.addClass('#NS_Rental > .items .list > .item'+i,'active');
          _this.config.items[i]['tags'].forEach(function(t){
            _this.tags.show(t);
          });
        }
      });
      
      var shownTagID='';
      for(shownTagID in _this['tags']['shown']){
        
      }
      if (shownTagID==''){
        _NS.DOM.addClass('#NS_Rental > .items .tags','hidden');
      }
      else {
        _NS.DOM.removeClass('#NS_Rental > .items .tags','hidden');
      }
      
      
      
      
      var itemsX=finalItems.length, itemsY=page*perPage;
      if (itemsY>itemsX){
        itemsY=itemsX;
      }
      var availableQuantity=0,itemClassToSet='',itemID=0;
      for (itemIndex=(page-1)*perPage;itemIndex<itemsY;itemIndex++){
        itemID=finalItems[itemIndex];
        itemDiv=document.querySelector('#NS_Rental__item'+itemID);
        itemClassToSet='period';
        _NS.DOM.addClass(itemDiv,'active');
        availableQuantity=NS_Rental.availability['current'][itemID];
   
        
        if (availableQuantity>0){
          itemClassToSet='available '+((NS_Rental.cart.content[finalItems[itemIndex]]>0)?'update':'add');
        }
        else if (availableQuantity===0) {
          itemClassToSet='booked';
        }/** /
        else {
          
          _NS.DOM.addClass(itemDiv.querySelector('.action'),'checking');
        }/**/
        itemDiv.querySelector('.action').className='action '+itemClassToSet;
    /**/
        itemDiv.querySelector('.quantity').setAttribute('placeholder','up to '+availableQuantity);
        itemDiv.querySelector('.quantity').value=(NS_Rental.cart['content'][itemID]>0)?NS_Rental.cart['content'][itemID]:'';
      }
      
      totalPages=Math.ceil(finalItems.length/perPage);
      if (totalPages>pageChoices){
        var pageChoiceChanger=parseInt(Math.floor(pageChoices/2));
        if (page-pageChoiceChanger<=1){
          startPage=1;
          endPage=pageChoices;
        }
        else {
          if ((parseInt(page)+parseInt(pageChoiceChanger))>=totalPages){
            endPage=totalPages;
            startPage=totalPages-pageChoices+1;
          }
          else {
            startPage=page-pageChoiceChanger;
            endPage=page+pageChoiceChanger;
          }
        }
      }
      else {
        startPage=1;
        endPage=totalPages;
      }
      
      var pagination=[];
      for (var pI=startPage;pI<=endPage;pI++){
        pagination.push((pI==page)?pI:'<a onclick="NS_Rental.items.getPaginated('+pI+');">'+pI+'</a>');
      }
      document.querySelector('#NS_Rental > .items .pagination').innerHTML=pagination.join(' ');
      
      console.log(pagination.join(' '));
      NS_Rental.loadSection('items');
      endTimestamp=(new Date()).getTime();
      
      console.log('calculations ended '+endTimestamp);
      console.log('processed within '+(endTimestamp-startTimestamp)+'ms');
      //console.dir(filteredItems);
      NS_Rental.updateFilters('paginatedItems');
    }
    ,'view':function(itemID){
      var itemID=itemID,itemDetails=NS_Rental.config.items[itemID],categories=[];
      this.chosen=itemID;
      this.elements.init();
      this.elements['title'].innerHTML=itemDetails.title;
      this.elements['description'].innerHTML=itemDetails.description;
      this.elements['quantity'].value=document.querySelector('#NS_Rental__item'+itemID+' .action .quantity').value;
      this.elements['quantity'].setAttribute('placeholder',document.querySelector('#NS_Rental__item'+itemID+' .action .quantity').getAttribute('placeholder'));
      this.elements['action'].className=document.querySelector('#NS_Rental > .items .item'+itemID+' .action').className;
      NS_Rental.config.categories.default.forEach(function(c){
        if (c>0){
          if (NS_Rental.config.categories[c]['items'].indexOf(''+itemID+'')>=0){
            categories.push(NS_Rental.config.categories[c]['title']);
          }
        }
      });
      var tags=[];
      NS_Rental.config.tags['default'].forEach(function(t){
        if (itemDetails.tags.indexOf(t)!=-1){
          tags.push('<li class="tag'+t+'"'+/** /' onclick="NS_Rental.tags.toggle('+t+');"'+/**/'>'+NS_Rental.config.tags[t]['code']+'</li>');
        }
      });
      document.querySelector('#NS_Rental > .item .tags ul').innerHTML=tags.join(' ');

      
      //document.querySelector('#NS_Rental .body.item .rawInput').innerHTML='<pre>'+JSON.stringify(itemDetails,null,2)+'</pre>';
      document.querySelector('#NS_Rental .body.item .gallery .chosen img').src=itemDetails['thumbnail'];
      this.elements['categories'].innerHTML=categories.join(',');
      NS_Rental.loadSection('item');
    }
  }
  ,'availability':{
    'current':{}
    ,'previous':{}
    ,'cache':{}
    ,'update':function(){
      var _this=this,data={};
      if (NS_Rental.period.ready){
        data['period']={'start_date':NS_Rental.period.start,'end_date':NS_Rental.period.end};
      }
      
      this.previous=Object.assign({},this.current);
      //console.log('availability');
      //_NS.post(NS_Rental.config.baseURL+'item/availability',data,{'success':function(reply){}},1);
      
      _NS.post(NS_Rental.config.baseURL+'item/availability',data,{'success':function(reply){
        var itemID=0,quantity,available=[],unavailable=[],classToSet='';
        
        NS_Rental.config['items']['default'].forEach(function(itemID){
          if (reply.data[itemID]>0){
            available.push(itemID);
          }
          else {
            unavailable.push(itemID);
          }
        });
        NS_Rental.config['items']['sorted']=available.concat(unavailable);
        _this.current=reply.data;
        NS_Rental.items.getPaginated(1);
      /** /
        for (itemID in reply.data){
          quantity=reply.data[itemID];
          if (quantity=)
          classToSet='available '+((NS_Rental.cart.content[itemID]>0)?'update':'add');
          //document.querySelector('#NS_Rental__item'+itemID+' .action').className='action '+classToSet;
          //document.querySelector('#NS_Rental > .items .list .item'+itemID+' .action').className='action '+classToSet;
          //document.querySelector('#NS_Rental > .items .list > .item'+itemID+' .quantity').innerHTML=quantity;
          //document.querySelector('#NS_Rental > .items .list .item'+itemID+' .action').className='action '+classToSet;
        }/**/
      }},1);
      
      /** /
      _NS.post(NS_Rental.config.baseURL+'item/filtered',data,{'success':function(reply){
        reply.data.entries.forEach(function(item){
          var i=0,item=item,max=(item['quantity']-item['booked']), final=0, options=[],classToSet='booked';
          NS_Rental.availability.current[item['item_id']]=max;
          if (document.querySelector('#NS_Rental > .items .list > .item'+item['item_id'])!=null){
          if (typeof(NS_Rental.config.items[item['item_id']])!='undefined'){
            if (max>0){
              //console.log('item'+item['item_id']+': '+max+' VS '+NS_Rental.availability.previous[item['item_id']]);
              if (max!=NS_Rental.availability.previous[item['item_id']]){
                if (max>1000){max=1000;}
                //console.log('max is new: '+max);
                for (i=1;i<=max;i++){
                  options.push('<option class="v'+i+'" value="'+i+'"'+((NS_Rental.cart.content[item['item_id']]==i)?' selected="selected"':'')+'>'+i+'</option>');
                }
                if (document.querySelector('#NS_Rental > .items .list > .item'+item['item_id'])!=null){
                  //console.log('#NS_Rental > .items > .list > .item'+item['item_id']+' .action .quantity');
                  //console.log(options.join(''));
                  document.querySelector('#NS_Rental > .items .list > .item'+item['item_id']+' .action .quantity').innerHTML=options.join('');
                  if (NS_Rental.items.chosen==item['item_id']){
                    document.querySelector('#NS_Rental > .item .action .quantity').innerHTML=options.join('');
                  }
                }
              }
              
              classToSet='available '+((NS_Rental.cart.content[item['item_id']]>0)?'update':'add');
            }
            
            document.querySelector('#NS_Rental > .items .list > .item'+item['item_id']+' .action').className='action '+classToSet;
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
  ,'_______period':{
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
        echo 'NS_Rental.period.prepare(\''.$_SESSION['NS_Rental']['period']['start_date'].'\',\'start\');NS_Rental.period.prepare(\''.$_SESSION['NS_Rental']['period']['end_date'].'\',\'end\');NS_Rental.period.update();';
      } /**/?>
    }
    ,'prepare':function(dateString,target){
      var dateString=dateString,dateParts=dateString.split('-');
      
      NS_Rental.period.elements[target+'_date'].dataset['current']=dateString;
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
      _NS.DOM.removeClass('#NS_Rental > .items .tags .shown','shown');
    }
    ,'resetValues':function(){
      this.chosen={};
      _NS.DOM.removeClass('#NS_Rental > .items .tags .active','active');
    }
    ,'init':function(){
      var tagID=0;
      for(tagID in NS_Rental.tags.chosen){
        if (NS_Rental.tags.chosen[tagID]>0){
          NS_Rental.tags.totalChosen++;
          _NS.DOM.addClass('#NS_Rental > .items .tags .tag'+tagID,'active');
        }
      }
    }
    ,'toggle':function(tagID){
      var tagID=tagID,isChosen=NS_Rental.tags.chosen[tagID];
      if (isChosen==1){
        NS_Rental.tags.chosen[tagID]=0;
        NS_Rental.tags.totalChosen--;
        _NS.DOM.removeClass('#NS_Rental > .items .tags .tag'+tagID,'active');
      }
      else {
        NS_Rental.tags.chosen[tagID]=1;
        NS_Rental.tags.totalChosen++;
        _NS.DOM.addClass('#NS_Rental > .items .tags .tag'+tagID,'active');
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
        _NS.DOM.addClass('#NS_Rental > .items .tags ul .tag'+tagID,'shown');
        _this.tags.shown[tagID]=1;
      }
    }
  }
  ,'__cart':{
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
      if (typeof(quantity)!='undefined'){
        this.content[itemID]=quantity;
      }
      else {
        this.content[itemID]=document.querySelector('#NS_Rental '+((chosen)?'.item':'#NS_Rental__item'+itemID)+' .action .quantity').value;
      }
      //if (NS_Rental.cart.initiated){
        NS_Rental.loadSection('cart');
      //}
      if (document.querySelector('#NS_Rental > .cart .list .item'+itemID)==null){
        itemData=NS_Rental.config.items[itemID];
        document.querySelector('#NS_Rental > .cart .list').insertAdjacentHTML('beforeend',this.template
          .replace('_THUMBNAIL_',((itemData['thumbnail']!='')?'<img class="img-responsive" src="'+itemData['thumbnail']+'"/>':''))
          .replace('_TITLE_',itemData['title'])
          .replace(/_X_/g,itemID)
          /** /.replace('_QUANTITY_',document
            .querySelector('#NS_Rental '+((chosen)?'.item':'> .items .item'+itemID)+' .action .quantity')
            .innerHTML.replace('value="'+this.content[itemID]+'"','value="'+this.content[itemID]+'" selected="selected"')
          )/**/
          .replace('_PACKED_',packed.join('<br/>'))
        );
      }
      else {
        document.querySelector('#NS_Rental__item'+itemID+' .quantity').value=this.content[itemID];
      }
      document.querySelector('#NS_Rental > .cart .list .item'+itemID+' .quantity').value=this['content'][itemID];
      //document.querySelector('#NS_Rental .cart select[name="quantity['+itemID+']"] .v'+this.content[itemID]).selected=true;
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