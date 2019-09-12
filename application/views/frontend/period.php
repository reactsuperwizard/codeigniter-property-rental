<?php /** / ?>
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
    </div><?php /**/ ?>
<div class="modal fade" id="NS_Rental_periodUpdaterModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalCenterTitle">Update rent period</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body"><form id="NS_Rental_periodUpdaterForm">
        <div class="form-group row">
          <label class="col-sm-3 control-label"><?php echo $this->lang->phrase('start'); ?></label>
          <div class="col-sm-8">
            <input type="hidden" name="period_start" value="" data-current="" autocomplete="off"/>
            <input class="form-control" name="period_start_datepicker" value="" autocomplete="off"/>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3 control-label"><?php echo $this->lang->phrase('end'); ?></label>
          <div class="col-sm-8">
            <input type="hidden" name="period_end" data-current="" value="" autocomplete="off"/>
            <input class="form-control" name="period_end_datepicker" value="" autocomplete="off"/>
          </div>
        </div>
      </form></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo $this->lang->phrase('cancel'); ?></button>
        <button type="button" class="btn btn-primary" onclick="NS_Rental.period.update();"><?php echo $this->lang->phrase('update'); ?></button>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
NS_Rental['period']={  
  'start_date':''
  ,'end_date':''
  ,'ready':false
  ,'elements':{
    'init':function(){
      if (this.initiated) return false;
      this['label']=document.querySelector('#NS_Rental > .header .periodLabel');
      this['start_date']=document.querySelector('#NS_Rental_periodUpdaterForm input[name="period_start"]');
      this['start_date_datepicker']=document.querySelector('#NS_Rental_periodUpdaterForm input[name="period_start_datepicker"]');
      this['end_date']=document.querySelector('#NS_Rental_periodUpdaterForm input[name="period_end"]');
      this['end_date_datepicker']=document.querySelector('#NS_Rental_periodUpdaterForm input[name="period_end_datepicker"]');
      //this['updater']=document.querySelector('#NS_Rental > .header .period .updater');
      //this['closer']=document.querySelector('#NS_Rental > .header .period .closer');
      this['initiated']=true;
    }
    ,'initiated':false/** /
    'label':document.querySelector('#NS_Rental > .header .periodLabel')
    ,'start_date':document.querySelector('#NS_Rental > .header .period input[name="period_start"]')
    ,'end_date':document.querySelector('#NS_Rental > .header .period input[name="period_end"]')
    ,'updater':document.querySelector('#NS_Rental > .header .period .updater')
    ,'closer':document.querySelector('#NS_Rental > .header .period .closer')/**/
  }
  ,'init':function(){
    this.elements.init();
    jQuery('#NS_Rental_periodUpdaterForm input[name="period_start_datepicker"]').datepicker({
      'dateFormat':'M d, yy'
      ,'minDate':(new Date((new Date().getTime())+86400000))
      ,'onSelect':function(dateText,data){
        var m=(parseInt(data.selectedMonth)+1);
        
        console.log('dateText: '+dateText);
        NS_Rental.period.elements.start_date.value=data.selectedYear+'-'+((m<10)?'0':'')+m+'-'+data.selectedDay;
        NS_Rental.period.elements.end_date.value='';
         NS_Rental.period.elements.end_date_datepicker.value='';
        jQuery('#NS_Rental_periodUpdaterForm input[name="period_end_datepicker"]').datepicker('option',{
          'minDate':(new Date(data.selectedYear,data.selectedMonth,data.selectedDay,25))
        });
        jQuery('#NS_Rental_periodUpdaterForm input[name="period_end_datepicker"]').datepicker('refresh');
        
        setTimeout(function(){jQuery('#NS_Rental_periodUpdaterForm input[name="period_end_datepicker"]').datepicker('show');},300);
        //console.log('data: '+JSON.stringify(data,null,2));
        /**
        NS_Rental.period.elements.end_date.value='';
        jQuery('#NS_Rental > .header > .period input[name="period_end_datepicker"]').datepicker('option',{
          'minDate':(new Date(data.selectedYear,data.selectedMonth,data.selectedDay,25))
        });
        //jQuery('#NS_Rental > .header > .period input[name="period_end"]').datepicker('refresh');
        //jQuery('#NS_Rental > .header > .period input[name="period_end"]').datepicker('show');
        NS_Rental.period.elements.start_date.dataset['current']=data.selectedYear+'-'+((m<10)?'0':'')+m+'-'+data.selectedDay;
        NS_Rental.period.elements.end_date.dataset['current']='';/**/
      }
    });
    jQuery('#NS_Rental_periodUpdaterForm input[name="period_end_datepicker"]').datepicker({
      'dateFormat':'M d, yy'
      ,'minDate':(new Date((new Date().getTime())+86400000*2))
      ,'onSelect':function(dateText,data){
        var m=(parseInt(data.selectedMonth)+1);
        NS_Rental.period.elements.end_date.value=data.selectedYear+'-'+((m<10)?'0':'')+m+'-'+data.selectedDay;
      }
    });
    <?php /**/ //unset($_SESSION['NS_Rental']['period']); 
    if (!empty($_SESSION['NS_Rental']['period'])){ 
      echo 'NS_Rental.period.prepare(\''.$_SESSION['NS_Rental']['period']['start_date'].'\',\'start\');NS_Rental.period.prepare(\''.$_SESSION['NS_Rental']['period']['end_date'].'\',\'end\');NS_Rental.period.update();';
    } 
    else {
      echo 'NS_Rental.items.getPaginated(1);';
    }/**/?>
  }
  ,'prepare':function(dateString,target){
    var dateString=dateString,dateParts=dateString.split('-');

    NS_Rental.period.elements[target+'_date'].value=dateString;
    jQuery('#NS_Rental_periodUpdaterForm input[name="period_'+target+'_datepicker"]').datepicker('setDate',new Date(dateParts[0],(parseInt(dateParts[1])-1),dateParts[2]));
  }
  ,'show':function(){
    jQuery('#NS_Rental_periodUpdaterModal').modal('show');
    //_NS.DOM.removeClass('#NS_Rental > .header > .period','hidden');
  }
  ,'hide':function(){
    jQuery('#NS_Rental_periodUpdaterModal').modal('hide');
    //_NS.DOM.addClass('#NS_Rental > .header > .period','hidden');
  }
  ,'update':function(){
    this.start=this.elements.start_date.value;
    this.end=this.elements.end_date.value;
    this.ready=true;
    document.querySelectorAll('#NS_Rental div.action').forEach(function(d){
      d.className='action checking';
    });
    //_NS.DOM.removeClass('#NS_Rental > .items > div .action','period');
    //_NS.DOM.addClass('#NS_Rental > .items > div .action','checking');

    NS_Rental.availability.update();

    this.elements.label.querySelector('span').innerText=this.elements.start_date_datepicker.value+' to '+this.elements.end_date_datepicker.value;
    this.hide();
  }
  
};
</script>