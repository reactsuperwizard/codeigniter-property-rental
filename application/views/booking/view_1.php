<div class="base">
  <div class="row">
    <div class="col-sm-6">
      <h3>Booking {CODE} {STATUS}</h3>
    </div>
    <div class="col-sm-6">
      <a class="pull-right btn btn-primary">Pay Balance <span class="balance">({CURRENCY} {FINAL_AMOUNT})</span></a>
    </div>
  </div>
  <input type="hidden" name="booking_id" value="{BOOKING_ID}"/>
  <input type="hidden" name="code" value="{CODE}"/>
  <div class="panel panel-default">
    <div class="panel-heading"><h4 style="margin:0px;">Items</h4></div>
    <table class="table table-striped table-condensed table-responsive" style="margin:0px;">
      <thead>
        <tr>
          <th>Quantity</th>
          <th>Image</th>
          <th>Title</th>
          <th>Description</th>
          <th>Price</th>
          <th>Discount</th>
          <th>Total</th>
        </tr>
      </thead>
      <tbody class="items"></tbody>
    </table>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading"><h4 style="margin:0px;">Services</h4></div>
    <table class="table table-striped table-condensed table-responsive" style="margin:0px;">
      <thead>
        <tr>
          <th>Title</th>
          <th>Description</th>
          <th>Rate</th>
          <th>No. staff</th>
          <th>Quantity</th>
          <th>Discount</th>
          <th>Total</th><th></th>
        </tr>
      </thead>
      <tbody class="services"></tbody>
    </table>
  </div>  
  <div class="clearfix">
    <table align="right" style="width: 40%;">
      <tr class="has_ITEM_DATA_FLAG_Data">
        <td>Items Total</td><td>{CURRENCY} {TOTAL_ITEMS}</td>
      </tr>
      <tr class="has_SERVICE_DATA_FLAG_Data">
        <td>Services Total</td><td>{CURRENCY} {TOTAL_SERVICES}</td>
      </tr>
      <tr>
        <td>Discount Applied</td><td>{CURRENCY} {DISCOUNT_AMOUNT}</td>
      </tr>
      <tr>
        <td><b>Grand Total</b></td><td><b>{CURRENCY} {GRAND_TOTAL}</b></td>
      </tr>
    </table>
  </div>
  <div>
    <table class="table table-responsive">
      <tr><td>
        {CUSTOMER_FULL_NAME} {CUSTOMER_COMPANY}<br/>
        {RESIDENTIAL_ADDRESS_STRING} {CUSTOMER_EMAIL}<br/>
        {CUSTOMER_PHONE}
      </td><td>{NOTES}</td></tr>
      <tr><td colspan="2">{DELIVERY_DATE_STRING} to {COLLECTION_DATE_STRING}</td></tr>
      <tr><td>
        {DELIVERY_ADDRESS_STRING}<br/>
        {DELIVERY_CONTACT_NAME} {DELIVERY_CONTACT_EMAIL}<br/>
        {DELIVERY_PHONE}
      </td><td>{EXTRA_NOTES}</td></tr>
    </table>
  </div>
<hr/>
<fieldset class="bookingDetailsFieldset hidden">
  <div class="panel panel-default customerDetails">
    <div class="panel-heading">Customer Details</div>
    <div class="panel-body">
      <input type="hidden" name="customer_id" value="{CUSTOMER_ID}"/>
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
    </div>
  </div>
  <div class="panel panel-default residentialAddress">
    <div class="panel-heading">Residential Address</div>
    <div class="panel-body">
      <input type="hidden" name="residential_address_id" value="{RESIDENTIAL_ADDRESS_ID}"/>
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
  <div class="panel panel-default deliveryAddress">
    <div class="panel-heading">Delivery Address</div>
    <div class="panel-body">
      <input type="checkbox" name="residential_delivery" value="1" onclick=""/> Use residential
      
      <input type="hidden" name="delivery_address_id" value="{DELIVERY_ADDRESS_ID}"/>
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
    </div>
  </div>
  <div class="panel panel-primary">
    <div class="panel-heading"><div class="row"><div class="col-xs-6"><span class="pull-left">{CODE}</span></div><div class="col-xs-6"><span class="pull-right">{STATUS}</span></div></div></div>
    <div class="panel-body">


      <div class="panel panel-warning">
        <div class="panel-heading"><h4 style="margin:0px;">Totals</h4></div>
        
      <table class="table table-striped table-condensed table-responsive" style="margin:0px;">
        <thead>
          <tr><th>Items</th><th>Services</th><th>Discount</th><th>Final</th><th>Paid</th></tr>
        </thead>
        <tbody>
          <tr>
            <td>{TOTAL_ITEMS}</td>
            <td>{TOTAL_SERVICES}</td>
            <td>{DISCOUNT_TEXT}</td>
            <td>{GRAND_TOTAL}</td>
            <td>{PAID_AMOUNT}</td>
          </tr>
        </tbody>
      </table></div>
      <?php /**/ ?>
      <div class="bookingActions pull-right">
        <a class="btn btn-default depositPayment{DEPOSIT_REQUIRED}" onclick="NS_Rental.bookings.validateRequest('deposit');">Deposit</a>
        <a class="btn btn-default depositPayment{NO_DEPOSIT_REQUIRED}" onclick="NS_Rental.bookings.validateRequest('confirm');">Confirm</a>
        <a class="btn btn-primary" onclick="NS_Rental.bookings.validateRequest('finalization');">Finalize</a>
      </div><?php /** / ?>
      <div class="depositPayment{DEPOSIT_ALLOWED} hidden pull-right">
        <a class="btn btn-default depositPayment" onclick="NS_Rental.bookings.validateRequest('deposit');">Deposit</a>
        <a class="btn btn-primary" onclick="NS_Rental.bookings.validateRequest('finalization');">Finalize</a>
      </div><?php /**/ ?>
    </div>

  </div>
  
  <div class="panel panel-default logisticsDetails">
    <div class="panel-heading"><?php echo $this->lang->phrase('Logistics Details'); ?></div>
    <div class="panel-body">
      <div class="panel panel-default">
        <div class="panel-heading"><?php echo $this->lang->phrase('delivery'); ?></div>
        <div class="panel-body deliveryPanel"></div>
      </div>
      <div class="panel panel-default">
        <div class="panel-heading"><?php echo $this->lang->phrase('collection'); ?></div>
        <div class="panel-body collectionPanel"></div>
      </div>
    </div>
  </div>
</fieldset>
</div>
<table><tbody class="bookingItem"><tr>
    <td>{QUANTITY}</td>
    <td><img class="img-responsive" src="{THUMBNAIL}"/></td>
    <td>{TITLE}</td>
    <td>{DESCRIPTION}</td>
    <td>{CURRENCY}&nbsp;{PRICE}</td>
    <td>{DISCOUNT_TEXT}</td>
    <td>{TOTAL}</td>
  </tr>
  </tbody>
</table>
<table class="hidden"><tbody class="bookingService">
  <tr>
    <td>{TITLE}</td>
    <td>{DESCRIPTION}</td>
    <td>{CURRENCY}&nbsp;{PRICE}</td>
    <td>{PEOPLE}</td>
    <td>{QUANTITY}</td>
    <td>{DISCOUNT_TEXT}</td>
    <td>{TOTAL}</td>
  </tr>
  </tbody>
</table>