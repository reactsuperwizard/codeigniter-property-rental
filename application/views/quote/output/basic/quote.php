<p>
  Attention: {CUSTOMER_FIRST_NAME} {CUSTOMER_LAST_NAME}
</p>
<p>
  {DATE_TODAY}
</p>
<p>
  Job description: {NAME}
</p>
<p>
  Job location: {DELIVERY_ADDRESS}
</p>
<p>
  Start date: {DELIVERY_DATE} {DELIVERY_TIME}<br/>
  End date: {COLLECTION_DATE} {COLLECTION_TIME}
</p>
<p>
  Chargeable Days: {CHAREGEABLE_DAYS}
</p>
<p>
  Valid until: {EXPIRATION_DATE} {EXPIRATION_TIME}
</p>

<p>
  Dear {CUSTOMER_FIRST_NAME} {CUSTOMER_LAST_NAME},<br/>
  Thank you for the opportunity to quote {NAME}
</p>
<style type="text/css">
.manyVariations {display:block;}
.singleVariation {display:none;}
.totalVariations1 .manyVariations {display:none;}
.totalVariations1 .singleVariation {display:block;}
</style>
<div class="totalVariations{VARIATION_COUNT}">
  <p class="singleVariation">
    Below we have included a solution we feel will meet or exceed your expectations.
  </p>
  <p class="manyVariations">
    Below you will see {VARIATION_COUNT} solutions we can offer  to make your event a success. Each solution has a different price point.
  </p>
  <p>
    Following your event we will pack away all of the rented equipment according to the description within ‘services’ as well
  </p>
  <p>
    We look forward to working with you soon, please check that everything is included and that all of the details are correct so we can proceed.
  </p>
</div>
{VARIATIONS}