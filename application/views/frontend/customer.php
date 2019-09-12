<?php /** CUSTOMER START **/ ?>
      
<div class="row">      
  <div class="col-md-4 col-sm-6 col-xs-12">
    <div class="form-group">
      <label for="" class="control-label">Your Full Name *</label>
      <input type="text" name="customer_name" class="form-control erRequired field_customerName" value="" data-msg-required="*Required Field"/>
      <div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
    </div><!-- /.form-group -->
  </div>
<!-- /.col-lg-4 col-md-4 col-sm-6 col-xs-12 -->
  <div class="col-md-4 col-sm-6 col-xs-12">
    <div class="form-group">
      <label for="" class="control-label">Email *</label>
      <input type="text" name="customer_email" class="form-control email erRequired field_customerEmail" value="" data-msg-required="*Required Field" data-msg-email="Email is not valid."/>
      <div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
    </div><!-- /.form-group -->
  </div><!-- /.col-md-4 col-sm-6 col-xs-12 -->
</div>

<script type="text/javascript">


function updateElementValue(e,v){
  switch(e.tagName){
    case 'INPUT':
    case 'TEXTAREA':
      e.value=v;
    break;
    case 'SELECT':
      e.querySelector('option[value="'+v+'"]').selected=true;
    break;
    default:
      //console.log("tag: "+e.tagName+"\nname: "+e.name+"\nvalue: "+e.value+"\nsession: "+v);
    break;
  }
}

function updateAddress(){
  var modes=['residential','delivery'],m=0,mode='';
  
  for (m=0;m<2;m++){
    mode=modes[m];
    //console.log('checking '+mode);    
    document.querySelectorAll('*[name^="'+mode+'_address"').forEach(function(e){
      var l=e.name.length, s=(e.name.indexOf('[')+1), f=e.name.substr(s,(l-s-1));
      //console.log(e.name+': '+f);
      if (typeof(cartSession[mode+'_address'])!='undefined'){
        if (typeof(cartSession[mode+'_address'][f])!='undefined'){
          updateElementValue(e,cartSession[mode+'_address'][f]);
        }
      }
    });
  }
  toggleDeliveryAddress();
}

function toggleDeliveryAddress(){
  if (document.querySelector('input[name="residential_delivery_address"]').checked){
    document.querySelector('div.deliveryAddress').className=document.querySelector('div.deliveryAddress').className.replace(' isSet','');
  }
  else {
    if (document.querySelector('div.deliveryAddress').className.indexOf(' isSet')<0){
      document.querySelector('div.deliveryAddress').className+=' isSet';
    }
  }
}
</script>
<style type="text/css">
.deliveryAddress {display:none;}
.deliveryAddress.isSet {display: block;}
</style>

<div class="row">
  <h4 class="col-sm-12 form-group" style="font-weight: bold; margin-top: 0px;">Residential Address (for insurance purposes)</h4>
</div>
<div class="residentialAddress"><div class="row">  <div class="col-md-4 col-sm-6 col-xs-12">
    <div class="form-group">
      <label for="" class="control-label">Phone <font color ="#808080"><small>(10 digits, no spaces)</font></small> *</label>
      <input type="text" name="residential_address[phone]" class="form-control erRequired" value="" data-msg-required="*Required Field"/>
      <div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
    </div><!-- /.form-group -->
  </div><!-- /.col-md-4 col-sm-6 col-xs-12 -->
    <div class="col-md-4 col-sm-6 col-xs-12">
    <div class="form-group">
      <label for="" class="control-label">Address Line 1 *</label>
      <input type="text" name="residential_address[line_1]" class="form-control erRequired" value="" data-msg-required="*Required Field"/>
      <div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
    </div><!-- /.form-group -->
  </div><!-- /.col-md-4 col-sm-6 col-xs-12 -->
    <div class="col-md-4 col-sm-6 col-xs-12">
    <div class="form-group">
      <label for="" class="control-label">Address Line 2</label>
      <input type="text" name="residential_address[line_2]" class="form-control" value="" data-msg-required="*Required Field"/>
      <div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
    </div><!-- /.form-group -->
  </div><!-- /.col-md-4 col-sm-6 col-xs-12 -->
  </div>
  <div class="row">  <div class="col-md-4 col-sm-6 col-xs-12">
    <div class="form-group">
      <label for="" class="control-label">City *</label>
      <input type="text" name="residential_address[city]" class="form-control erRequired" value="" data-msg-required="*Required Field"/>
      <div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
    </div><!-- /.form-group -->
  </div><!-- /.col-md-4 col-sm-6 col-xs-12 -->
    <div class="col-md-4 col-sm-6 col-xs-12">
    <div class="form-group">
      <label for="" class="control-label">State</label>
      <input type="text" name="residential_address[state]" class="form-control" value="" data-msg-required="*Required Field"/>
      <div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
    </div><!-- /.form-group -->
  </div><!-- /.col-md-4 col-sm-6 col-xs-12 -->
    <div class="col-md-4 col-sm-6 col-xs-12">
    <div class="form-group">
      <label for="" class="control-label">Postcode</label>
      <input type="text" name="residential_address[postcode]" class="form-control" value="" data-msg-required="*Required Field"/>
      <div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
    </div><!-- /.form-group -->
  </div><!-- /.col-md-4 col-sm-6 col-xs-12 -->
  </div>
</div><!-- .residentialAddress -->
<div class="row">
  <div class="col-md-4 col-sm-6 col-xs-12">
  <h4 class="form-group" style="font-weight: bold; margin-top: 0px;">Delivery Address</h4>
  </div>
  <div class="col-md-4 col-sm-6 col-xs-12">
    <div class="form-group">
      <input type="checkbox" value="1" name="residential_delivery_address" onclick="toggleDeliveryAddress();"> Use residential address
    </div><!-- /.form-group -->
  </div><!-- /.col-md-4 col-sm-6 col-xs-12 -->
</div>
<div class="row">
  <div class="col-md-6 col-sm-6 col-xs-12">
    <div class="form-group">
      <label for="" class="control-label"> <h4><b><font color="red">Delivery Region</font></b></h4> </font> <font color="green">choose closest to your booking location</font>  *</label>
      <select name="delivery_region_id" class="form-control pjErDelivery erRequired erNonZero" data-msg-required="*Required Field" data-msg-min="Delivery region should be chosen"><option value="0">Choose one</option><option value="1" data-price="0.00">Darwin, Palmerston and Northern Suburbs</option><option value="17" data-price="45.00">Howard Springs, Coolalinga, Bees Creek</option><option value="18" data-price="59.00">Humpty Doo CBD <5km, Girraween, Noonamah</option><option value="19" data-price="79.00">Berry Springs, Acacia Hills, Livingstone</option><option value="548" data-price="70.00">Humpty Doo CBD >5km</option></select>
      <div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
    </div><!-- /.form-group -->
  </div><!-- /.col-md-4 col-sm-6 col-xs-12 -->
</div>
<div class="deliveryAddress isSet"><div class="row">  <div class="col-md-4 col-sm-6 col-xs-12">
    <div class="form-group">
      <label for="" class="control-label">Venue (if applicable)</label>
      <input type="text" name="delivery_address[venue]" class="form-control" value=""/>
    </div><!-- /.form-group -->
  </div><!-- /.col-md-4 col-sm-6 col-xs-12 -->
    <div class="col-md-4 col-sm-6 col-xs-12">
    <div class="form-group">
      <label for="" class="control-label">Address Line 1 *</label>
      <input type="text" name="delivery_address[line_1]" class="form-control erRequired" value="" data-msg-required="*Required Field"/>
      <div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
    </div><!-- /.form-group -->
  </div><!-- /.col-md-4 col-sm-6 col-xs-12 -->
    <div class="col-md-4 col-sm-6 col-xs-12">
    <div class="form-group">
      <label for="" class="control-label">Address Line 2</label>
      <input type="text" name="delivery_address[line_2]" class="form-control" value="" data-msg-required="*Required Field"/>
      <div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
    </div><!-- /.form-group -->
  </div><!-- /.col-md-4 col-sm-6 col-xs-12 -->
  </div>
  <div class="row">  <div class="col-md-4 col-sm-6 col-xs-12">
    <div class="form-group">
      <label for="" class="control-label">City *</label>
      <input type="text" name="delivery_address[city]" class="form-control erRequired" value="" data-msg-required="*Required Field"/>
      <div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
    </div><!-- /.form-group -->
  </div><!-- /.col-md-4 col-sm-6 col-xs-12 -->
    <div class="col-md-4 col-sm-6 col-xs-12">
    <div class="form-group">
      <label for="" class="control-label">Phone <font color ="#808080"><small>(10 digits, no spaces)</font></small></label>
      <input type="text" name="delivery_address[phone]" class="form-control" value="" data-msg-required="*Required Field"/>
      <div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
    </div><!-- /.form-group -->
  </div><!-- /.col-md-4 col-sm-6 col-xs-12 -->
    <div class="col-md-4 col-sm-6 col-xs-12">
    <div class="form-group">
      <label for="" class="control-label">Postcode</label>
      <input type="text" name="delivery_address[postcode]" class="form-control" value="" data-msg-required="*Required Field"/>
      <div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
    </div><!-- /.form-group -->
  </div><!-- /.col-md-4 col-sm-6 col-xs-12 -->
  </div>
  </div><!-- .deliveryAddress -->
  <div class="row">
  <div class="col-xs-12">
    <div class="form-group">
      <label for="" class="control-label">Pleae add any important details here. <font color = "#808080"><small>(e.g. early start times or access information)</small></font color></label>
      <textarea name="notes" rows="10" class="form-control" data-msg-required="*Required Field"></textarea>
      <div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
    </div><!-- /.form-group -->
  </div><!-- /.col-xs-12 -->
</div>

<?php /** CUSTOMER END **/ ?>