<div>
  <hr/>
  <div><h3>{NAME}</h3></div>
  <div class="has_ITEM_DATA_FLAG_Data">
    <h3><u>Items</u></h3>
    <table class="table table-striped">
      <thead>
        <tr>
          <th scope="col-1">Qty</th>
          <th scope="col-1">Image</th>
          <th scope="col-7">Title</th>
          <th scope="col-1">Price</th>
          <th scope="col-1">Discount</th>
          <th scope="col-1">Total</th>
        </tr>
      </thead>
      <tbody class="itemRows"></tbody>
      <tfoot></tfoot>
    </table>
  </div>
  <div class="has_SERVICE_DATA_FLAG_Data">
    <h3><u>Services</u></h3>
    <table class="table table-striped">
      <thead>
        <tr>
          <th scope="col-1">Qty</th>
          <th scope="col-1">Staff</th>
          <th scope="col-7">Title</th>
          <th scope="col-1">Price</th>
          <th scope="col-1">Discount</th>
          <th scope="col-1">Total</th>
        </tr>
      </thead>
      <tbody class="serviceRows"></tbody>
      <tfoot></tfoot>
    </table>
  </div>
  <div id="quoteVariation_CODE_" class="PurchaseOrder v_PURCHASE_ORDER_">
    <div class="v1">
      <input class="form-control" type="text" name="purchase_order" value="" placeholder="Purchase Order"/>
    </div>
  </div>
  <div style="width: 100%;float:none;clear: both;">
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
      <tr>
        <td>Tax (GST)</td><td>{CURRENCY} {TAX}</td>
      </tr>
      <tr class="variationActions b_BOOKING_FLAG_">
        <td class="nonBooked"><a class="btn btn-primary" href="{DISCUSSION_LINK}">Revise this Quote</a></td>
        <td class="nonBooked"><a class="btn btn-secondary" onclick="NS_Rental.quote.acceptVariation('{CODE}');">Accept this Quote</a></td>
        <td class="alreadyBooked"><a class="btn btn-primary-label">Booked</a></td>
        <td class="expired"><a class="btn btn-primary-label">Expired</a></td>
        <td class="alreadyBooked expired"><a class="btn btn-secondary">Request the same quote</a></td>
        
      </tr>
    </table>
  </div>
  
  <div style="font-size: 10px;float:none;clear: both;padding-top:25px;">
    <div class="hidden">DepositMode: {DEPOSIT_MODE}<br/>DepositText: {DEPOSIT_TEXT}</div>
    <div class="DepositMode _DEPOSIT_MODE_">
      <div class="fullDeposit">
        ** This booking requires full payment to secure your order. Click the accept button for credit card payment.
      </div>
      <div class="partialDeposit">
        ** This booking requires a payment of {DEPOSIT_MODE} {DEPOSIT_TEXT} to secure your booking.<br/>
        ** Payment for this booking is required on or before {DUE_DATETIME}.
      </div>
    </div>
    <div class="PurchaseOrder v_PURCHASE_ORDER_">
      <div class="v1">
        ** This booking requires a signed purchase order to proceed. You will be invoiced after your event.<br/>
        ** Payment for this booking is required on or before {DUE_DATETIME}.
      </div>
      <div class="v0">
        ** I authorise all items and services quoted above on behalf of {CUSTOMER_COMPANY}.<br/>
        ** Payment for this booking is required on or before {DUE_DATETIME}.
      </div>
    </div>
    
  </div>
  <div>
    {NOTES}
  </div>
</div>