<div id="bookingContentTemplate">
<div class="panel panel-primary">
      <div class="panel-heading">
        <input type="text" class="form-control" name="code" value="" placeholder="Code"/>
      </div>
      <div class="panel-body">
    
    
    <div class="list">
        <div class="panel panel-default">
          <div class="panel-heading"><h4 style="margin:0px;">Items<span onclick="bookings.entries.request('item');" class="glyphicon glyphicon-plus pull-right text-success"></span></h4></div>
          
            <table class="table table-striped table-condensed" style="margin:0px;">
              <thead>
                <tr><th></th><th>Title</th>
                  <th colspan="2">Quantity</th>
                  <th colspan="2">Price</th>
                  <th colspan="3">Discount</th>
                  <th colspan="2">Total</th>
                </tr>
                <tr><td colspan="2"></td>
                  <td>Available</td><td>Chosen</td>
                  <td>Per Unit</td><td>Full</td>
                  <td>Type</td><td style="max-width:50px;">Value</td><td>Full</td>
                  <td></td><td></td>
                </tr>
              </thead>
              <tbody class="items"></tbody>
            </table>
        </div>
      <div class="panel panel-default">
        <div class="panel-heading"><h4 style="margin:0px;">Services<span onclick="bookings.entries.request('service');" class="glyphicon glyphicon-plus pull-right text-success"></span></h4></div>
        
      <table class="table table-striped table-condensed" style="margin:0px;">
        <thead>
          <tr><th>Name</th><th>Rate</th><th>No. staff</th><th>Quantity</th>
                  <th colspan="3">Discount</th><th>Total</th><th></th></tr>
          <tr><th colspan="4"></th>
            <td>Type</td><td style="max-width:50px;">Value</td><td>Full</td>
          <th colspan="2"></th></tr>
        </thead>
        <tbody class="services"></tbody>
      </table>
      </div>

      <div class="panel panel-warning">
        <div class="panel-heading"><h4 style="margin:0px;">Totals</h4></div>
        
      <table class="table table-striped table-condensed" style="margin:0px;">
        <thead>
          <tr><th colspan="3">Items</th><th colspan="3">Services</th><th>Subtotal</th><th colspan="2">Discount</th><th colspan="3">Final</th></tr>
        </thead>
        <tbody>
          <tr>
            <td><span class="totalItemMultipliedPrice pull-right">0</span></td><td><span class="totalItemDiscount pull-right">0</span></td><td><span class="totalItemPrice pull-right">0</span></td>
            <td><span class="totalServiceMultipliedPrice pull-right">0</span></td><td><span class="totalServiceDiscount pull-right">0</span></td><td><span class="totalServicePrice pull-right">0</span></td>
            <td><span class="subtotalPrice pull-right">0</span></td>
            <td>
      <input type="hidden" name="previous_discount_type" data-reset_value="percentage" value=""/>
      <input type="radio" name="discount_type" onclick="bookings.entries.recalculate();" data-reset_value="percentage" value="percentage" />&nbsp;%&nbsp;
      <input type="radio" name="discount_type" onclick="bookings.entries.recalculate();" value="amount"/>&nbsp;$
    </td>
    <td style="max-width: 50px;"><input type="text" class="form-control" name="discount_value" data-reset_value="0"  value="_DISCOUNT_VALUE_" onchange="bookings.entries.recalculate();"/></th>
    <th><span class="finalRawPrice pull-right"></span></th>
    <th><span class="finalDiscount pull-right"></span></th>
    <th><span class="finalPrice pull-right"></span></th></tr>
        </tbody>
      </table></div>

      </div></div>
  </div>
</div>

<div id="bookingLogisticsTemplate">

        <div class="panel panel-default">
          <div class="panel-heading"><h4 style="margin:0px;">Items<span onclick="bookings.entries.request('item');" class="glyphicon glyphicon-plus pull-right text-success"></span></h4></div>
          
            <table class="table table-striped table-condensed" style="margin:0px;">
              <thead>
                <tr><th></th><th>Title</th>
                  <th colspan="2">Quantity</th>
                  <th colspan="2">Price</th>
                  <th colspan="3">Discount</th>
                  <th colspan="2">Total</th>
                </tr>
                <tr><td colspan="2"></td>
                  <td>Available</td><td>Chosen</td>
                  <td>Per Unit</td><td>Full</td>
                  <td>Type</td><td style="max-width:50px;">Value</td><td>Full</td>
                  <td></td><td></td>
                </tr>
              </thead>
              <tbody class="items"></tbody>
            </table>
        </div>

</div>

<table class="hidden"><tbody class="regularItemTemplate">
  <tr class="entry_X_ attachedItem_I_">
    <td style="max-height: 30px;"><img style="max-height: 30px;" class="img-responsive center-block" src="_THUMBNAIL_"/></td>
    <td>_TITLE_</td>
    <td><span class="quantity">_QUANTITY_LABEL_</span></td>
    <td><input class="form-control" name="entry[_X_][quantity]" data-previous_quantity="0" value="_QUANTITY_" data-max_quantity="_MAX_QUANTITY_" onchange="bookings.entries.updatePrice(_X_);"/></td>
    <td><input class="form-control" name="entry[_X_][price]" onchange="bookings.entries.updatePrice(_X_);" value="_START_PRICE_"/><?php /** / ?><span class="pull-right">_START_PRICE_</span><?php /**/ ?></td>
    <td><span class="multipliedPrice_X_ pull-right">0</span></td>
    <td>
      <input type="hidden" name="entry[_X_][previous_discount_type]" value=""/>
      <input type="radio" name="entry[_X_][discount_type]" checked="true" onclick="bookings.entries.updatePrice(_X_);" value="percentage" />&nbsp;%&nbsp;
      <input type="radio" name="entry[_X_][discount_type]" onclick="bookings.entries.updatePrice(_X_);" value="amount"/>&nbsp;$
    </td>
    <td style="max-width: 50px;"><input type="text" class="form-control" name="entry[_X_][discount_value]" value="_DISCOUNT_VALUE_" onchange="bookings.entries.updatePrice(_X_);"/></td>
    <td><span class="fullDiscount_X_ pull-right">0</span></td>
    <td><span class="fullPrice_X_ pull-right">0</span></td>
    <td>
      <input type="hidden" name="entry[entries][]" value="_X_"/>
      <input type="hidden" name="entry[_X_][type]" value="regularItem"/>
      <input type="hidden" name="entry[_X_][item_id]" value="_I_"/>
      
      <span onclick="_NS.alert.confirm('bookings.entries.remove(_X_);');" class="glyphicon glyphicon-minus pull-right text-danger"></span>
    </td>
  </tr>
  <tr class="entry_X_">
    <td colspan="11">_DESCRIPTION_</td>
  </tr>
  </tbody>
</table>

<table class="hidden"><tbody class="additionalItemTemplate">
  <tr class="entry_X_">
    <td colspan="2"><input class="form-control" type="text" name="entry[_X_][title]" value="_TITLE_"></td>
    <td colspan="2"><input class="form-control" name="entry[_X_][quantity]" onchange="bookings.entries.updatePrice(_X_);"value="_QUANTITY_"/></td>
    <td><input class="form-control" name="entry[_X_][price]" onchange="bookings.entries.updatePrice(_X_);" value="_PRICE_"/></td>
    <td><span class="multipliedPrice_X_ pull-right">0</span></td>
    <td>
      <input type="hidden" name="entry[_X_][previous_discount_type]" value="_INITIAL_DISCOUNT_TYPE_"/>
      <input type="radio" name="entry[_X_][discount_type]" checked="_PERCENTAGE_DISCOUNT_" onclick="bookings.entries.updatePrice(_X_);" value="percentage" />&nbsp;%&nbsp;
      <input type="radio" name="entry[_X_][discount_type]" checked="_AMOUNT_DISCOUNT_" onclick="bookings.entries.updatePrice(_X_);" value="amount"/>&nbsp;$
    </td>
    <td style="max-width: 50px;"><input type="text" class="form-control" name="entry[_X_][discount_value]" value="_DISCOUNT_VALUE_" onchange="bookings.entries.updatePrice(_X_);"/></td>
    <td><span class="fullDiscount_X_ pull-right">0</span></td>
    <td><span class="fullPrice_X_ pull-right">0</span></td>
    <td>
      <input type="hidden" name="entry[_X_][extra_item_id]" value="_EXTRA_ITEM_ID_"/>
      <input type="hidden" name="entry[entries][]" value="_X_"/>
      <input type="hidden" name="entry[_X_][type]" value="additionalItem"/>
      <span onclick="_NS.alert.confirm('bookings.entries.remove(_X_);');" class="glyphicon glyphicon-minus pull-right text-danger"></span>
    </td>
  </tr>
  <tr class="entry_X_">
    <td colspan="11"><textarea class="form-control" name="entry[_X_][description]" placeholder="Description">_DESCRIPTION_</textarea></td>
  </tr>
  </tbody>
</table>


<table class="hidden"><tbody class="serviceTemplate">
  <tr class="entry_X_">
    <td><input class="form-control" type="text" name="entry[_X_][title]" value="_TITLE_"></td>
    <td><input class="form-control" name="entry[_X_][price]" value="_PRICE_" onchange="bookings.entries.updatePrice(_X_);"/></td>
    <td><input class="form-control" name="entry[_X_][people]" value="_PEOPLE_" onchange="bookings.entries.updatePrice(_X_);"/></td>
    <td><input class="form-control" name="entry[_X_][quantity]" value="_QUANTITY_" onchange="bookings.entries.updatePrice(_X_);"/></td>
    <td><span class="multipliedPrice_X_ pull-right">0</span></td>
    <td>
      <input type="hidden" name="entry[_X_][previous_discount_type]" value="percentage"/>
      <input type="radio" name="entry[_X_][discount_type]" checked="true" onclick="bookings.entries.updatePrice(_X_);" value="percentage" />&nbsp;%&nbsp;
      <input type="radio" name="entry[_X_][discount_type]" onclick="bookings.entries.updatePrice(_X_);" value="amount"/>&nbsp;$
    </td>
    <td style="max-width: 50px;"><input type="text" class="form-control" name="entry[_X_][discount_value]" value="_DISCOUNT_VALUE_" onchange="bookings.entries.updatePrice(_X_);"/></td>
    <td><span class="fullDiscount_X_ pull-right">0</span></td>
    <td><span class="fullPrice_X_ pull-right">0</span></td>
    <td>
      <input type="hidden" name="entry[_X_][extra_item_id]" value="_EXTRA_ITEM_ID_"/>
      <input type="hidden" name="entry[entries][]" value="_X_"/>
      <input type="hidden" name="entry[_X_][type]" value="service"/>
      <span onclick="_NS.alert.confirm('bookings.entries.remove(_X_);');" class="glyphicon glyphicon-minus pull-right text-danger"></span>
    </td>
  </tr>
  <tr class="entry_X_">
    <td colspan="11"><textarea class="form-control" name="entry[_X_][description]" placeholder="Description">_DESCRIPTION_</textarea></td>
  </tr>
  </tbody>
</table>