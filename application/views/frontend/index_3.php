<?php
get_instance()->includeLocalJS('jquery-3.2.1.min.js');
get_instance()->includeLocalJS('jquery-ui.min.js');
get_instance()->includeLocalJS('jquery.dataTables.min.js');
get_instance()->includeLocalJS('bootstrap.min.js');
get_instance()->includeLocalJS('nickolas_solutions.js');
?>
<script type="text/javascript" src="https://checkout.stripe.com/checkout.js"></script>
<link rel="stylesheet" href="<?php echo NS_BASE_URL; ?>css/jquery-ui.min.css"/>
<link rel="stylesheet" href="<?php echo NS_BASE_URL; ?>css/jquery.dataTables.css"/>
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

#NS_Rental > .header .quotes .closed
,#NS_Rental > .header .bookings .previous { color:#D0D0D0; }

#NS_Rental > .header .quotes .active
,#NS_Rental > .header .bookings .active { color:#b2dba1; }

</style>
<?php require_once(APPPATH.'views/alert.php'); ?>
<?php require_once(VIEWPATH.'frontend/base_js.php'); ?>
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
          <span class="items" onclick="NS_Rental.items.init();"><?php echo $this->lang->phrase('items'); ?></span>
          <span class="cart" onclick="NS_Rental.cart.init();"><?php echo $this->lang->phrase('cart'); ?></span>
          <span class="login hidden" onclick="NS_Rental.user.prepareLogin();">Login</span>
          <span class="authorized quotes hidden">Quotes: 
            <span class="active" title="active" onclick="NS_Rental.quote.loadList('active');">0</span> / 
            <span class="previous" title="previous" onclick="NS_Rental.quote.loadList('previous');">0</span>
          </span>
          <span class="authorized bookings hidden">Bookings: 
            <span class="active" title="active" onclick="NS_Rental.booking.loadList('active');">0</span> / 
            <span class="previous" title="previous" onclick="NS_Rental.booking.loadList('previous');">0</span>
          </span>
          <span class="authorized profile hidden" onclick="NS_Rental.user.profile();"></span>
          <span class="authorized logout hidden" onclick="NS_Rental.user.logout();">Logout</span>
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
  <?php require_once(VIEWPATH.'frontend/user.php'); ?>
  <div class="body items" data-section="items">
    <div class="row">
      <div class="col-sm-3">
        <ul class="categories">
          
        </ul>
        <ul class="tags">
          
        </ul>
      </div>
      <div class="col-sm-9">
        <div class="list"></div>
        <div class="pagination"></div>
      </div>
    </div>
    
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
  <?php require_once(VIEWPATH.'frontend/booking.php'); ?>
  <?php require_once(VIEWPATH.'frontend/quote/base.php'); ?>
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
    <?php //include(APPPATH.'/views/booking/view.php'); ?>
  </div>
</div>
<script type="text/javascript">
NS_Rental.user.loadHeader();
NS_Rental.init();
</script>
<?php /** / echo '<pre>'; print_r($_SESSION); echo '</pre>'; ?>
<?php echo '<pre>'; print_r($this->reply); echo '</pre>'; /**/ ?>