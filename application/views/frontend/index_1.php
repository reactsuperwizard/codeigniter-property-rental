<?php
get_instance()->includeLocalJS('jquery-3.2.1.min.js');
get_instance()->includeLocalJS('jquery-ui.min.js');
get_instance()->includeLocalJS('bootstrap.min.js');
get_instance()->includeLocalJS('nickolas_solutions.js');
?>
<script type="text/javascript" src="https://checkout.stripe.com/checkout.js"></script>
<link rel="stylesheet" href="<?php echo NS_BASE_URL; ?>css/jquery-ui.min.css"/>
<style type="text/css">
<?php include(APPPATH.'../css/bootstrap.min.css'); ?>
<?php include(APPPATH.'../css/main.css'); ?>
#NS_Rental > .items .list > div { display:none; }
#NS_Rental > .items .list > div.active { display:block; border:none;}

#NS_Rental > .items .list > div .image {height:80px;overflow:none;}
#NS_Rental > .items .list > div .image img {height:80px;}
#NS_Rental > .items .list > div .image p {display:none;}

#NS_Rental > .items .list > div .caption {height:220px;overflow:none;}

#NS_Rental > .header > .tags .list > * { display:none; }
#NS_Rental > .header > .tags .list > *.shown
,#NS_Rental > .header > .tags .list > *.active { display:inline-block; }
#NS_Rental > .header > .tags .list > *.active { border: 1px solid #000000; background-color:#FEEFB3; }

#NS_Rental .action > div {display:none;}
#NS_Rental .action.period > div.period
,#NS_Rental .action.checking > div.checking
,#NS_Rental .action.booked > div.booked
,#NS_Rental .action.add > div.available
,#NS_Rental .action.update > div.available { display:block; }
#NS_Rental .action > div.available span { display:none; }
#NS_Rental .action.add > div.available span.add
,#NS_Rental .action.update > div.available span.update { display:inline-block; }

#NS_Rental .booking .bookingActions a.depositPayment {
  display:none;
}
</style>
<?php require_once(APPPATH.'views/alert.php'); ?>
<?php //require_once(VIEWPATH.'frontend/base_js.php'); ?>
<div id="NS_Rental">
  <div class="header clearfix">
    <div  class="clearfix">
      <div class="pull-left">
        <select name="category_id" onchange="NS_Rental.items.getPaginated(1);"></select>
        <div class="periodLabel">
          <span onclick="NS_Rental.period.show();"><?php echo $this->lang->phrase('choose_period');?></span>
        </div>
      </div>
      <div class="pull-right">
        <div class="btn-group">
          <span class="items btn btn-default" onclick="NS_Rental.items.init();"><?php echo $this->lang->phrase('items'); ?></span>
          <span class="cart btn btn-default" onclick="NS_Rental.cart.init();"><?php echo $this->lang->phrase('cart'); ?></span><span class="account btn btn-default">Login</span>
        </div>
      </div>
    </div>
    <div class="period hidden">
      <div class="form-group">
        <label class="col-sm-3 control-label"><?php echo $this->lang->phrase('start'); ?></label>
        <div class="col-sm-8">
          <input class="form-control" name="period_start" value="" data-current="" autocomplete="off"/>
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-3 control-label"><?php echo $this->lang->phrase('end'); ?></label>
        <div class="col-sm-8">
          <input class="form-control" name="period_end" value="" data-current="" autocomplete="off"/>
        </div>
      </div>
      <div class="form-group">
        <div class="col-sm-offset-3 col-sm-8"><a class="btn btn-primary updater" onclick="NS_Rental.period.update();"><?php echo $this->lang->phrase('update'); ?></a> <a class="btn btn-default closer" onclick="NS_Rental.period.hide();"><?php echo $this->lang->phrase('cancel'); ?></a></div>
      </div>
    </div>
    <div class="tags">
      <div>Tags</div>
      <div class="list"></div>
    </div>
  </div>
  <div class="body items" data-section="items">
    <div class="list"></div>
    <div class="pagination"></div>
  </div>
  <div class="body item" data-section="item">
    <div class="row">
      <div class="gallery col-md-5 col-sm-6 col-sx-12">
        <div class="chosen"></div>
        <div class="list"></div>
      </div>
      <div class="col-md-7 col-sm-6 col-xs-12">
        <div class="title"></div>
        <div class="categories"></div>
        <div class="description"></div>
        <div class="embed"></div>
        <div class="action">
          <div class="available">
            <select class="quantity"></select><a class="btn btn-primary" onclick="NS_Rental.cart.add();">'+labels.ADD_TO_CART+'</a>
          </div>
          <div class="booked"><span>'+labels.ALL_BOOKED+'</span></div>
          <div class="checking"><span>'+labels.CHECKING_AVAILABILITY+'</span></div>
          <div class="period"><span onclick="NS_Rental.period.show();">'+labels.PERIOD_NOT_DEFINED+'</span></div>
        </div>
      </div>
    </div>
  </div>
  <div class="body cart" data-section="cart">
    <form id="bookingRequestForm">
    <div class="list"></div>
    <div class="customer">
      <?php /** / ?>
      <div class="row">
        <div class="col-xs-6">
          <input type="text" name="customer[email]" value="" class="form-control" placeholder="Email"/>
        </div>
        <div class="col-xs-6">
          <input type="text" name="customer[password]" value="" class="form-control" placeholder="Password"/>
        </div>
      </div>
      <div class="row">
        <div class="col-xs-6">
          <div>I'm a returning customer</div>
          <a class="btn btn-primary" onclick="NS_Rental.customers.login();">Login</a>
        </div>
        <div class="col-xs-6">
          <div>I'm new customer and would like to create an account</div>
          <div><a class="btn btn-default" onclick="NS_Rental.customers.prepareForm();">Create Acccount</a></div>
        </div>
      </div><?php /**/ ?>
      <div class="">

      <?php //include(APPPATH.'views/frontend/customer.php'); ?>

      </div>
      <div class="row">
        <div class="col-sm-12"><a class="btn btn-primary" onclick="NS_Rental.bookings.validateRequest();">Attempt</a></div>
      </div>
    </div>
    </form>
  </div>
  <div class="body booking" data-section="booking">
    <div class="templateContent"></div>
    <pre class="plainContent hidden"></pre>
  </div>
  <div class="body quote" data-section="quote" style="padding:25px;">
    <div class="content">
      
    </div>
    <fieldset class="customer hidden">
      <div class="panel panel-default customerDetails">
        <div class="panel-heading">Customer Details</div>
        <div class="panel-body">
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
          </div>
          <div><a class="btn btn-primary" onclick="NS_Rental.quotes.updateAccept('customer');">Update</a></div>
        </div>
      </div>
    </fieldset>
    <fieldset class="residential_address hidden">
      <div class="panel panel-default residentialAddress">
        <div class="panel-heading">Residential Address</div>
        <div class="panel-body">
          <input type="hidden" name="residential_address_id" value="0"/>
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
          <div><a class="btn btn-primary" onclick="NS_Rental.quotes.updateAccept('residential_address');">Update</a></div>
        </div>
      </div>
    </fieldset>
    <fieldset class="delivery_address hidden">
    <div class="panel panel-default deliveryAddress">
      <div class="panel-heading">Delivery Address</div>
      <div class="panel-body">
        <input type="checkbox" name="residential_delivery" value="1" onclick=""/> Use residential
        <div class="row">
            <div class="col-sm-6 addressDetails">
              <label class="control-label"><?php echo $this->lang->phrase('line_1'); ?></label>
              <input type="text" class="form-control" name="delivery_address[line_1]"/>
              <label class="control-label"><?php echo $this->lang->phrase('line_2'); ?></label>
              <input type="text" class="form-control" name="delivery_address[line_2]"/>
              <label class="control-label"><?php echo $this->lang->phrase('phone'); ?></label>
              <input type="text" class="form-control" name="delivery_address[phone]"/>
            </div>
            <div class="col-sm-6 addressDetails">
              <label class="control-label"><?php echo $this->lang->phrase('city'); ?></label>
              <input type="text" class="form-control" name="delivery_address[city]"/>
              <label class="control-label"><?php echo $this->lang->phrase('state'); ?></label>
              <input type="text" class="form-control" name="delivery_address[state]"/>
              <label class="control-label"><?php echo $this->lang->phrase('postcode'); ?></label>
              <input type="text" class="form-control" name="delivery_address[postcode]"/>
            </div>
            <div class="col-sm-6 addressDetails contactDetails">
              <label class="control-label"><?php echo $this->lang->phrase('venue'); ?></label>
              <input type="text" class="form-control" name="delivery_address[name]"/>
              <label class="control-label"><?php echo $this->lang->phrase('contact_name'); ?></label>
              <input type="text" class="form-control" name="delivery_address[contact_name]"/>
            </div>
            <div class="col-sm-6 addressDetails contactDetails">
              <label class="control-label"><?php echo $this->lang->phrase('contact_email'); ?></label>
              <input type="text" class="form-control" name="delivery_address[contact_email]"/>
              <label class="control-label"><?php echo $this->lang->phrase('contact_phone'); ?></label>
              <input type="text" class="form-control" name="delivery_address[contact_phone]"/>
            </div>
          </div>
        <div><a class="btn btn-primary" onclick="NS_Rental.quotes.updateAccept('delivery_address');">Update</a></div>
      </div>
    </div>
    </fieldset>
  </div>
  <div class="templates hidden">
    <div class="cartItem">
      <div class="row item_X_">
        <div class="col-sm-5">
          <div>_TITLE_</div>
          <div>_PACKED_</div>
        </div>
        <div class="col-sm-7">
          <div><select class="quantity quantity_X_" name="quantity[_X_]" onchange="NS_Rental.cart.update(_X_);">_QUANTITY_</select></div>
          <div class="price"></div>
          <i class="glyphicon glyphicon-remove" onclick="NS_Rental.cart.remove(_X_);">&nbsp;&nbsp;&nbsp;</i>
        </div>
      </div>
    </div>
    <?php include(APPPATH.'/views/booking/view.php'); ?>
  </div>
</div>
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
  ,'Stripe':{
    'key': '<?php echo STRIPE_KEY_PUBLIC; ?>'
    ,'currency':'AUD'
    ,'checkout':null
    ,'token':null
    ,'run':function(title,amount,callback){
      var _this=this,title=title,amount=amount,recalculatedAmount=0,callback=callback;
      console.log('title: '+title+"\namount: "+amount);
      if (amount==0){
        _NS.alert.open('fail','Fail','Payment is not needed, call admin',1);
        return false;
      }
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
          //NS_Rental.bookings.newPayment['token']=token.id;
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
        return _this.bookings.init(); 
      break;
      case 'quote':
        return _this.quotes.init();
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
      'title':document.querySelector('#NS_Rental > .item .title')
      ,'categories':document.querySelector('#NS_Rental > .item .categories')
      ,'description':document.querySelector('#NS_Rental > .item .description')
      ,'action':document.querySelector('#NS_Rental > .item .action')
      ,'quantity':document.querySelector('#NS_Rental > .item .action .quantity')
    }
    ,'init':function(){
      var _this=this,_that=NS_Rental, tags=[], items=[], categories=[], labels=_that.config.labels[_that.config.languages.chosen];
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
      'label':document.querySelector('#NS_Rental > .header .periodLabel')
      ,'start_date':document.querySelector('#NS_Rental > .header .period input[name="period_start"]')
      ,'end_date':document.querySelector('#NS_Rental > .header .period input[name="period_end"]')
      ,'updater':document.querySelector('#NS_Rental > .header .period .updater')
      ,'closer':document.querySelector('#NS_Rental > .header .period .closer')
    }
    ,'init':function(){
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
    ,'template':document.querySelector('#NS_Rental .templates .cartItem').innerHTML
    ,'content':<?php if (!empty($_SESSION['NS_Rental']['cart'])){ echo json_encode($_SESSION['NS_Rental']['cart']); } else { echo '{}'; } ?>
    ,'totalInCart':0
    ,'add':function(itemID,quantity){
      var itemID=itemID, quantity=quantity, chosen=false,old=false, packed=[],packedItemID=0;
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
      _that.bookings.reset();
      
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
  ,'bookings':{
    'container':document.querySelector('#NS_Rental .body.booking')
    ,'templates':{
      'base':document.querySelector('#NS_Rental .templates .booking').innerHTML
      ,'item':document.querySelector('#NS_Rental .templates .bookingItem').innerHTML
      ,'service':document.querySelector('#NS_Rental .templates .bookingService').innerHTML
    }
    ,'current':null
    ,'newPayment':{}
    ,'reset':function(){
      var _this=this,_that=NS_Rental;
      _this.container.querySelector('.templateContent').innerHTML='';
      _this.current=null;
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
          'booking_id','code','status'
          ,'total_items','total_services','subtotal_discount','discount_text'
          ,'discount_amount'
          ,'deposit_required','no_deposit_required'
          ,'deposit_allowed','deposit_amount','final_amount','grand_total','paid_amount'
          ,'customer_id','residential_address_id','delivery_address_id'
          ,'delivery_date','delivery_time','collection_date','collection_time'
        ],f='',r='',t=_this.templates.base, logisticsData={'items':{},'delivery':{}};

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
        _this.container.querySelector('.templateContent').innerHTML=t;
        _this.container.querySelector('.templateContent .items').innerHTML=items.join('');
        _this.container.querySelector('.templateContent .services').innerHTML=services.join('');
        
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
        //_this.container.querySelector('.plainContent').innerHTML=JSON.stringify(reply,null,2);
      }},1);
      _that.loadSection('booking');
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
      
      _that.bookings.newPayment={
        'mode':mode
        ,'amount':parseFloat(amount).toFixed(2)
        ,'final':_this.current['final_amount']
      };
      
      if (NS_Rental.bookings.current!==null){
        //console.log(JSON.stringify(NS_Rental.bookings.current,null,2));
        params['code']=NS_Rental.bookings.current.code;
        if (NS_Rental.bookings.current.booking_id>0){
          NS_Rental.bookings.newPayment['booking_id']=NS_Rental.bookings.current.booking_id;
          return NS_Rental.Stripe.run(title,amount,NS_Rental.bookings.pay);
        }
        else {
          //console.log("form data:\n"+JSON.stringify(_NS.DOM.getFormData('#NS_Rental .body.booking'),null,2));
          dataSelector='#NS_Rental .body.booking .bookingDetailsFieldset';
          params=Object.assign(params,_NS.DOM.getFormData(dataSelector));
        }
      }
      else {
        
      }
      params['payment']=NS_Rental.bookings.newPayment;
      
      
      _NS.post('<?php echo NS_BASE_URL; ?>booking/'+action,params,{'success':function(reply){
        if (typeof(NS_Rental.bookings.current)==null){
          NS_Rental.bookings.current={};
        }
        NS_Rental.bookings.newPayment['booking_id']=reply.data['booking_id'];
        if (requiredPayment){
          NS_Rental.Stripe.run(title,amount,NS_Rental.bookings.pay);
        }
        else {
          NS_Rental.bookings.load(NS_Rental.bookings.current['code']);
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
          NS_Rental.Stripe.run('Final payment','{FINAL_AMOUNT}',NS_Rental.bookings.confirm);
          console.log(JSON.stringify(reply,null,2));
        }
      },1);/**/
      //console.log(JSON.stringify(,null,2));
    }
    ,'pay':function(params){
      var params=params;
      params=Object.assign(params,NS_Rental.bookings.newPayment);
      
      console.log('booking params for payment: '+JSON.stringify(params,null,2));
      _NS.post('<?php echo NS_BASE_URL; ?>booking/pay',params,{'success':function(reply){
        NS_Rental.bookings.load(reply.data.code);
      }},1);
    }
    ,'confirm':function(){
      var _this=this,_that=NS_Rental,params={},action='create';
      if (NS_Rental.bookings.current!==null){
        //console.log(JSON.stringify(NS_Rental.bookings.current,null,2));
        params['code']=NS_Rental.bookings.current.code;
        if (NS_Rental.bookings.current.booking_id>0){
          action='finalize';
        }
        else {
          //console.log("form data:\n"+JSON.stringify(_NS.DOM.getFormData('#NS_Rental .body.booking'),null,2));
          params=Object.assign(params,_NS.DOM.getFormData('#NS_Rental .body.booking .bookingDetailsFieldset'));
        }
      }
      params['payment']=NS_Rental.bookings.newPayment;
      
      _NS.post('<?php echo NS_BASE_URL; ?>booking/'+action,params,{'success':function(reply){
        //_this.load(reply.data.code);
      }},1);
    }
    ,'confirm_':function(){
      var _this=this,_that=NS_Rental,params={},action='create';
      if (NS_Rental.bookings.current!==null){
        //console.log(JSON.stringify(NS_Rental.bookings.current,null,2));
        params['code']=NS_Rental.bookings.current.code;
        if (NS_Rental.bookings.current.booking_id>0){
          action='finalize';
        }
        else {
          //console.log("form data:\n"+JSON.stringify(_NS.DOM.getFormData('#NS_Rental .body.booking'),null,2));
          params=Object.assign(params,_NS.DOM.getFormData('#NS_Rental .body.booking .bookingDetailsFieldset'));
        }
      }
      params['payment']=NS_Rental.bookings.newPayment;
      
      _NS.post('<?php echo NS_BASE_URL; ?>booking/'+action,params,{'success':function(reply){
        //_this.load(reply.data.code);
      }},1);
    }
  }
  ,'quotes':{
    'container':'#NS_Rental .body.quote'
    ,'chosenVariation':{}
    ,'reset':function(){
      this['chosenVariation']={
        'code':''
        ,'customer':{}
        ,'residential_address':{}
        ,'delivery_address':{}
      };
    }
    ,'init':function(){
      var _this=this,_that=NS_Rental;
      _this.load(_that.config.requestParts[1]);
    }
    ,'load':function(code){
      var _this=this,_that=NS_Rental;
      _this.reset();
      _NS.get('<?=NS_BASE_URL;?>quote/load/'+code,{},{'load':function(reply){/**/
        document.querySelector(_this.container+' > .content').innerHTML=reply;
        console.log(JSON.stringify(JSON.parse(document.querySelector('#quoteJSON').innerHTML)),null,2);
        _that.loadSection('quote');/**/
      }});
    }
    ,'loadVariation':function(code){
      
    }
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
    }
  }
};

NS_Rental.init();
</script>
<?php /** / echo '<pre>'; print_r($_SESSION); echo '</pre>'; ?>
<?php echo '<pre>'; print_r($this->reply); echo '</pre>'; /**/ ?>