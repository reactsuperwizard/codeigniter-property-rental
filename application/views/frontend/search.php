<input class="form-control" type="text" id="NS_Rental__itemSearch" value="" autocomplete="off"/>
<script type="text/javascript">
document.querySelector('#NS_Rental__itemSearch').addEventListener('keyup',function(){NS_Rental.items.updateSearch();});
/**
jQuery(document).ready(function(){
  jQuery( "#NS_Rental__search___" ).autocomplete({
    source: function( request, response ) {
      console.log()
      _NS.post('<?php echo NS_BASE_URL; ?>item/filtered',{'search':{'value':request.term},'start':0,'length':3,'categoryID':NS_Rental['items']['elements']['chosenCategoryID']},{'success':function(reply){
        response(reply.data.entries);
      }},1);
    },
    minLength: 2,
    select: function( event, ui ) {
      log( "Selected: " + ui.item.value + " aka " + ui.item.id );
    }
  } )
  .autocomplete( "instance" )._renderItem = function( ul, item ) {
    return jQuery( "<li>" )
    .append( "<div>" + item.label + "<br>" + item.desc + "</div>" )
    .appendTo( ul );
  };
});
**/
</script>