<div class="base">
  <div class="row">
    <div class="col-sm-6">
      <h3>Booking {CODE} {STATUS}</h3>
    </div>
    <div class="col-sm-6 TRIGGER_BALANCE">
      <div class="clearfix TRIGGER_BALANCE_BUTTON">
        <a class="pull-right btn btn-primary clearfix" onclick="NS_Rental.booking.validateRequest('finalization');">Pay Balance <span class="balance">({CURRENCY} {BALANCE_AMOUNT})</span></a>
      </div>
      <div class="clearfix"><span class="pull-right">Payment due: {DUE_DATE_STRING}</span></div>
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
  <div class="row">
    <table class="col-sm-4 pull-right">
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
      <tr>
        <td>Tax (GST)</td><td>{CURRENCY} {TAX}</td>
      </tr>
    </table>
  </div>
  <br/><br/>
  <div class="row">
    <div class="col-sm-8">
      <div>
        <h4>Customer Details</h4>
        <div class="row">
          <div class="col-sm-5">
            <label>Name</label>
            <p>{CUSTOMER_FIRST_NAME} {CUSTOMER_LAST_NAME}</p>
          </div>
          <div class="col-sm-7">
            <label>Postal Address</label>
            <p>{RESIDENTIAL_ADDRESS_LINE_1}<span class="TRIGGER_RESIDENTIAL_ADDRESS_LINE_2">, {RESIDENTIAL_ADDRESS_LINE_2}</span><br/>
              {RESIDENTIAL_ADDRESS_CITY}<span class="TRIGGER_RESIDENTIAL_ADDRESS_STATE">, {RESIDENTIAL_ADDRESS_STATE}</span><span class="TRIGGER_RESIDENTIAL_ADDRESS_POSTCODE"> {RESIDENTIAL_ADDRESS_POSTCODE}</span></p>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-5">
            <label>Phone</label>
            <p>{RESIDENTIAL_ADDRESS_PHONE}</p>
          </div>
          <div class="col-sm-7">
            <label>Email</label>
            <p>{CUSTOMER_EMAIL}</p>
          </div>
        </div>
      </div>
      <div>
        <h4>Delivery Details</h4>
        <div class="row">
          <div class="col-sm-5">
            <label>Contact Person</label>
            <p class="TRIGGER_DELIVERY_CONTACT_NAME">{DELIVERY_CONTACT_NAME}</p>
          </div>
          <div class="col-sm-7">
            <label>Delivery Address</label>
            <p><span class="TRIGGER_DELIVERY_CONTACT_VENUE">{DELIVERY_CONTACT_VENUE}</span><br class="TRIGGER_DELIVERY_CONTACT_VENUE"/>
              {DELIVERY_ADDRESS_LINE_1}<span class="TRIGGER_DELIVERY_ADDRESS_LINE_2">, {DELIVERY_ADDRESS_LINE_2}</span><br/>
              {DELIVERY_ADDRESS_CITY}<span class="TRIGGER_DELIVERY_ADDRESS_STATE">, {DELIVERY_ADDRESS_STATE}</span><span class="TRIGGER_DELIVERY_ADDRESS_POSTCODE">, {DELIVERY_ADDRESS_POSTCODE}</span></p>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-5">
            <label>Phone</label>
            <p>{DELIVERY_CONTACT_PHONE}</p>
          </div>
          <div class="col-sm-7">
            <label>Email</label>
            <p>{DELIVERY_CONTACT_EMAIL}</p>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-4">
      <h4>Booking Notes</h4>
      <p><b>Start:</b> {DELIVERY_DATE_STRING}<br/><b>End:</b> {COLLECTION_DATE_STRING}</p>
      <p class="TRIGGER_PURCHASE_ORDER"><b>Purchase Order:</b> {PURCHASE_ORDER}</p>
      <p>{NOTES}</p>
      <p>{EXTRA_NOTES}</p>
    </div>
  </div>
  <div class="row TRIGGER_BALANCE">
    <div class="col-sm-6 col-sm-offset-6">
      <div class="clearfix TRIGGER_BALANCE_BUTTON">
        <a class="pull-right btn btn-primary clearfix" onclick="NS_Rental.booking.validateRequest('finalization');">Pay Balance <span class="balance">({CURRENCY} {BALANCE_AMOUNT})</span></a>
      </div>
      <div class="clearfix"><span class="pull-right">Payment due: {DUE_DATE_STRING}</span></div>
    </div>
  </div>
<?php /** / ?>    
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
<?php /**/ ?>  
<hr/>
<fieldset class="bookingDetailsFieldset hidden">
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