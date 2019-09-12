<div id="quoteVariationTemplate" class="hidden">
  <div id="variation_X_" class="panel panel-primary">
    <div class="panel-heading"><div class="row">
      <div class="col-sm-11">
        <input type="text" class="form-control col-sm-8" name="variation[_X_][name]" value="_NAME_" placeholder="Variation Name"/>
      </div>
      <div class="col-sm-1">
        <a class="btn btn-danger pull-right" onclick="_NS.alert.confirm('quotes.variations.remove(_X_);');"><span class="glyphicon glyphicon-remove"></span></a>
      </div>
      <input type="hidden" name="variations[]" value="_X_"/>
      <input type="hidden" name="variation[_X_][quote_variation_id]" value="_QUOTE_VARIATION_ID_"/>
      <input type="hidden" name="variation[_X_][remove]" data-reset_value="0" value="0"/>
    </div></div>
    <div class="panel-body">
      <div class="list">
        <div class="panel panel-default">
          <div class="panel-heading"><h4 style="margin:0px;">Items<span onclick="quotes.variations.requestEntry('item', _X_);" class="glyphicon glyphicon-plus pull-right text-success"></span></h4></div>

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
          <div class="panel-heading"><h4 style="margin:0px;">Services<span onclick="quotes.variations.requestEntry('service', _X_);" class="glyphicon glyphicon-plus pull-right text-success"></span></h4></div>

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
                <td><span class="totalItemMultipliedPrice_X_ pull-right">0</span></td><td><span class="totalItemDiscount_X_ pull-right">0</span></td><td><span class="totalItemVariationPrice_X_ pull-right">0</span></td>
                <td><span class="totalServiceMultipliedPrice_X_ pull-right">0</span></td><td><span class="totalServiceDiscount_X_ pull-right">0</span></td><td><span class="totalServiceVariationPrice_X_ pull-right">0</span></td>
                <td><span class="subtotalVariationPrice_X_ pull-right">0</span></td>
                <td>
                  <input type="hidden" name="variation[_X_][previous_discount_type]" value="_INITIAL_DISCOUNT_TYPE_"/>
                  <input type="radio" name="variation[_X_][discount_type]" checked="_PERCENTAGE_DISCOUNT_" onclick="quotes.variations.recalculate(_X_);" value="percentage" />&nbsp;%&nbsp;
                  <input type="radio" name="variation[_X_][discount_type]" checked="_AMOUNT_DISCOUNT_" onclick="quotes.variations.recalculate(_X_);" value="amount"/>&nbsp;$
                </td>
                <td style="max-width: 50px;"><input type="text" class="form-control" name="variation[_X_][discount_value]" value="_DISCOUNT_VALUE_" onchange="quotes.variations.recalculate(_X_);"/></th>
                <th><span class="finalVariationRawPrice_X_ pull-right"></span></th>
                <th><span class="finalVariationDiscount_X_ pull-right"></span></th>
                <th><span class="finalVariationPrice_X_ pull-right"></span></th></tr>
            </tbody>
          </table></div>

        <div class="panel panel-info">
          <div class="panel-heading"><h4 style="margin:0px;">Payment Options</h4></div>
          <table class="table table-condensed table-responsive">
            <thead><tr>
                <th><?php echo $this->lang->phrase('PURCHASE_ORDER'); ?></th>
                <th colspan="2"><?php echo $this->lang->phrase('DEPOSIT'); ?></th>
                <th colspan="2"><?php echo $this->lang->phrase('FINAL_DUE_DATE'); ?></th>
              </tr></thead>
            <tbody><tr>
                <td><input type="checkbox" name="variation[_X_][purchase_order]" value="1"/></td>
                <td>
                  <input type="radio" name="variation[_X_][deposit_type]" value="percentage" />&nbsp;%&nbsp;
                  <input type="radio" name="variation[_X_][deposit_type]" value="amount"/>&nbsp;$
                </td>
                <td>
                  <input class="form-control" type="text" name="variation[_X_][deposit_value]" value=""/>
                </td>
                <td>
                  <input type="radio" name="variation[_X_][due_direction]" value="-"/> -&nbsp;&nbsp;
                  <input type="radio" name="variation[_X_][due_direction]" value="+"/> +
                </td>
                <td>
                  <input class="form-control" type="text" name="variation[_X_][due_days]" value=""/>
                </td>
              </tr></tbody>
          </table>
        </div>
        <div>
          <label>Notes</label>
          <textarea class="form-control" name="variation[_X_][notes]"></textarea>
        </div>
      </div>
    </div>
  </div>
</div>

<table class="hidden"><tbody class="regularItemTemplate">
    <tr class="entry_X_ attachedItem_I_">
      <td style="max-height: 30px;"><img style="max-height: 30px;" class="img-responsive center-block" src="_THUMBNAIL_"/></td>
      <td>_TITLE_</td>
      <td>_QUANTITY_LABEL_</td>
      <td><input class="form-control" name="variation[_V_][_X_][quantity]" data-previous_quantity="0" value="_QUANTITY_" data-max_quantity="_MAX_QUANTITY_" onchange="quotes.variations.updatePrice(_V_, _X_);"/></td>
      <td><input class="form-control" name="variation[_V_][_X_][price]" onchange="quotes.variations.updatePrice(_V_, _X_);" value="_START_PRICE_"/><?php /** / ?><span class="pull-right">_START_PRICE_</span><?php /* */ ?></td>
      <td><span class="multipliedPrice_X_ pull-right">0</span></td>
      <td>
        <input type="hidden" name="variation[_V_][_X_][previous_discount_type]" value="percentage"/>
        <input type="radio" name="variation[_V_][_X_][discount_type]" checked="true" onclick="quotes.variations.updatePrice(_V_, _X_);" value="percentage" />&nbsp;%&nbsp;
        <input type="radio" name="variation[_V_][_X_][discount_type]" onclick="quotes.variations.updatePrice(_V_, _X_);" value="amount"/>&nbsp;$
      </td>
      <td style="max-width: 50px;"><input type="text" class="form-control" name="variation[_V_][_X_][discount_value]" value="_DISCOUNT_VALUE_" onchange="quotes.variations.updatePrice(_V_, _X_);"/></td>
      <td><span class="fullDiscount_X_ pull-right">0</span></td>
      <td><span class="fullPrice_X_ pull-right">0</span></td>
      <td>
        <input type="hidden" name="variation[_V_][entries][]" value="_X_"/>
        <input type="hidden" name="variation[_V_][_X_][type]" value="regularItem"/>
        <input type="hidden" name="variation[_V_][_X_][item_id]" value="_I_"/>
        <span onclick="_NS.alert.confirm('quotes.variations.entries.remove(_V_,_X_);');" class="glyphicon glyphicon-minus pull-right text-danger"></span>
      </td>
    </tr>
    <tr class="entry_X_">
      <td colspan="11">_DESCRIPTION_</td>
    </tr>
  </tbody>
</table>

<table class="hidden"><tbody class="additionalItemTemplate">
    <tr class="entry_X_">
      <td colspan="2"><input class="form-control" type="text" name="variation[_V_][_X_][title]" value="_TITLE_"></td>
      <td colspan="2"><input class="form-control" name="variation[_V_][_X_][quantity]" onchange="quotes.variations.updatePrice(_V_, _X_);"value="_QUANTITY_"/></td>
      <td><input class="form-control" name="variation[_V_][_X_][price]" onchange="quotes.variations.updatePrice(_V_, _X_);" value="_PRICE_"/></td>
      <td><span class="multipliedPrice_X_ pull-right">0</span></td>
      <td>
        <input type="hidden" name="variation[_V_][_X_][previous_discount_type]" value="_INITIAL_DISCOUNT_TYPE_"/>
        <input type="radio" name="variation[_V_][_X_][discount_type]" checked="_PERCENTAGE_DISCOUNT_" onclick="quotes.variations.updatePrice(_V_, _X_);" value="percentage" />&nbsp;%&nbsp;
        <input type="radio" name="variation[_V_][_X_][discount_type]" checked="_AMOUNT_DISCOUNT_" onclick="quotes.variations.updatePrice(_V_, _X_);" value="amount"/>&nbsp;$
      </td>
      <td style="max-width: 50px;"><input type="text" class="form-control" name="variation[_V_][_X_][discount_value]" value="_DISCOUNT_VALUE_" onchange="quotes.variations.updatePrice(_V_, _X_);"/></td>
      <td><span class="fullDiscount_X_ pull-right">0</span></td>
      <td><span class="fullPrice_X_ pull-right">0</span></td>
      <td>
        <input type="hidden" name="variation[_V_][entries][]" value="_X_"/>
        <input type="hidden" name="variation[_V_][_X_][type]" value="additionalItem"/>
        <span onclick="_NS.alert.confirm('quotes.variations.entries.remove(_V_,_X_);');" class="glyphicon glyphicon-minus pull-right text-danger"></span>
      </td>
    </tr>
    <tr class="entry_X_">
      <td colspan="11"><textarea class="form-control" name="variation[_V_][_X_][description]" placeholder="Description">_DESCRIPTION_</textarea></td>
    </tr>
  </tbody>
</table>


<table class="hidden"><tbody class="serviceTemplate">
    <tr class="entry_X_">
      <td><input class="form-control" type="text" name="variation[_V_][_X_][title]" value="_TITLE_"></td>
      <td><input class="form-control" name="variation[_V_][_X_][price]" value="_PRICE_" onchange="quotes.variations.updatePrice(_V_, _X_);"/></td>
      <td><input class="form-control" name="variation[_V_][_X_][people]" value="_PEOPLE_" onchange="quotes.variations.updatePrice(_V_, _X_);"/></td>
      <td><input class="form-control" name="variation[_V_][_X_][quantity]" value="_QUANTITY_" onchange="quotes.variations.updatePrice(_V_, _X_);"/></td>
      <td><span class="multipliedPrice_X_ pull-right">0</span></td>
      <td>
        <input type="hidden" name="variation[_V_][_X_][previous_discount_type]" value="percentage"/>
        <input type="radio" name="variation[_V_][_X_][discount_type]" checked="true" onclick="quotes.variations.updatePrice(_V_, _X_);" value="percentage" />&nbsp;%&nbsp;
        <input type="radio" name="variation[_V_][_X_][discount_type]" onclick="quotes.variations.updatePrice(_V_, _X_);" value="amount"/>&nbsp;$
      </td>
      <td style="max-width: 50px;"><input type="text" class="form-control" name="variation[_V_][_X_][discount_value]" value="_DISCOUNT_VALUE_" onchange="quotes.variations.updatePrice(_V_, _X_);"/></td>
      <td><span class="fullDiscount_X_ pull-right">0</span></td>
      <td><span class="fullPrice_X_ pull-right">0</span></td>
      <td>
        <input type="hidden" name="variation[_V_][entries][]" value="_X_"/>
        <input type="hidden" name="variation[_V_][_X_][type]" value="service"/>
        <span onclick="_NS.alert.confirm('quotes.variations.entries.remove(_V_,_X_);');" class="glyphicon glyphicon-minus pull-right text-danger"></span>
      </td>
    </tr>
    <tr class="entry_X_">
      <td colspan="11"><textarea class="form-control" name="variation[_V_][_X_][description]" placeholder="Description">_DESCRIPTION_</textarea></td>
    </tr>
  </tbody>
</table>