<?php

class Booking extends NS_Rental_Controller{
  function __construct($config = array()) {
    parent::__construct($config);
    
    $this->load->model('Item_model');
    $this->loadTranslationCodes();
    
    
    $this->getStatusList('booking');
  }
  
  function index(){
    $this->continueIfAllowed(array('authorized'=>'any'));
    $this->reply['config']['venues']=$this->db
      ->join('address AS a','a.address_id=v.address_id','inner')
      ->order_by('v.name','ASC')
      ->get('venue AS v')->result_array();
    $this->reply['config']['statuses']=$this->getStatusList('booking');
    $this->setTemplate('backend','booking/management');
    $this->output->enable_profiler(TRUE);
  }
  
  function filtered($config=array()){
    $this->reply['config']['minimalRentTimestampPrefix']=MINIMAL_RENT_TIME_PREFIX;
    $this->load->library('DataTable',array('count'=>'b.booking_id'));

    $this->datatable->from('booking AS b')
      ->select('b.*,s.name AS status')
      ->join('status AS s','s.status_id=b.status_id');
        
    $searchTerm='';
    if (!empty($_POST['search'])){
      if (is_array($_POST['search'])){
        if (!empty($_POST['search']['value'])){
          $searchTerm=$this->security->xss_clean($_POST['search']['value']);
        }
      }
      else {
        $searchTerm=$this->security->xss_clean($_POST['search']);
      }
    }
    if (!empty($searchTerm)){
      $this->datatable->filter()->group_start()
        ->like('b.code',$searchTerm,'both')
        ->group_end();
    }
    
    $order='b.booking_id';
    $this->datatable
      ->order_by('b.booking_id DESC')
      ->run($this->reply['data']);
  }
  
  function edit(){
    $bookingID=$_POST['booking_id']*1;
    
    if ($bookingID==0){
      return $this->error($this->lang->phrase('booking_is_not_chosen'));
    }
    
    $this->loadDetails($this->db->select('code')->get_where('booking',array('booking_id'=>$bookingID),1)->row_array()['code']);
    $this->loadLogistics();
    /**/
    $this->load->model('Item_model');
    $this->load->model('ItemLock_model');
    
    $items=array();
    $config=array('rentTimestamps'=>array(
      'start'=>($this->reply['data']['delivery_timestamp']-DELIVERY_TIME_PREFIX)
      ,'end'=>($this->reply['data']['collection_timestamp']+COLLECTION_TIME_SUFFIX)
    ),'replyData'=>& $items,'getPackages'=>1,'thumbnails'=>1);
    
    $lockedItems=$this->ItemLock_model->getLockedItemIDs('booking',$bookingID);
    if (!empty($lockedItems)){
      $config['onlyChosen']=array();
      foreach ($lockedItems AS $lockedItem){
        $config['onlyChosen'][]=$lockedItem['item_id'];
        $config['onlyChosen'][]=$lockedItem['item_package_id'];
      }
    }
    $config['lockHash']=$this->ItemLock_model->unlock('booking',$bookingID,$config['rentTimestamps']['start'],$config['rentTimestamps']['end']);

    $this->Item_model->getFiltered($config);
    if (empty($this->reply['itemCache'])){
      $this->reply['itemCache']=array();
    }
    foreach($items['entries'] AS &$i){
      if (empty($this->reply['itemCache'][$i['item_id']])){
        $this->reply['itemCache'][$i['item_id']]=$i;
      }
    }/**/
  }
  
  function loadDetails($code){
    if (empty($code)){
      $this->error('Booking code is not set',false,true);
    }
    
    $this->load->model('Booking_model');
    $this->Booking_model->parseDetails($this->reply['data'],$code);
    
    $dataLabels=array('delivery'=>'rent_period_start','collection'=>'rent_period_end');
    
    //$this->reply['data']=$this->db->get_where('booking',array('code'=>$code),1)->row_array();
    if (!$this->reply['data']){
      $this->loadQuoteVariation($code);
      $this->reply['booking_id']=0;
      $dataLabels['delivery']='delivery';
      $dataLabels['collection']='collection';
    }
    
    $this->load->model('Address_model');
    $this->reply['data']['residential_address']=$this->Address_model->get($this->reply['data']['residential_address_id']);
    $this->reply['data']['delivery_address']=$this->Address_model->get($this->reply['data']['delivery_address_id']);

    $this->reply['data']['delivery_date']=date('Y-m-d',$this->reply['data'][$dataLabels['delivery'].'_timestamp']);
    $this->reply['data']['delivery_time']=date('H:i',$this->reply['data'][$dataLabels['delivery'].'_timestamp']);
    $this->reply['data']['collection_date']=date('Y-m-d',$this->reply['data'][$dataLabels['collection'].'_timestamp']);
    $this->reply['data']['collection_time']=date('H:i',$this->reply['data'][$dataLabels['collection'].'_timestamp']);
    if ($this->reply['data']['customer_id']>0){
      $this->reply['data']['customer']=$this->db
        ->select('user_id,first_name,last_name,email,IFNULL(phone,\'\') AS phone')
        ->get_where('user',array('user_id'=>$this->reply['data']['customer_id']),1)->row_array();
    }
    $this->reply['data']['delivery_contact']=json_decode($this->reply['data']['delivery_contact_json'],true);
  }
  
  function calculateScheduleGaps(){
    $schedules=$this->db->get('schedule')->result_array();
    $weekdays=array(1,2,3,4,5,6,7);
    $queryParts=array();
    $directions=array('forward','backwards');
    foreach($schedules AS $s){
      $scheduleWeekdays=array();
      $swx=explode(',',$s['weekdays']);
      foreach($swx AS $swi){
        $scheduleWeekdays[$swi]=1;
      }
      foreach($weekdays AS $weekday){
        $backwards=0;
        $forward=0;
        if (empty($scheduleWeekdays[$weekday])){
          for($dwd=$weekday-1;$dwd>($weekday-7);$dwd--){
            $backwards+=86400;
            $checker=$dwd;
            if ($dwd<1){
              $checker=$dwd+7;
            }
            if (!empty($scheduleWeekdays[$checker])){
              break;
            }
          }
          for($cwd=$weekday+1;$cwd<($weekday+7);$cwd++){
            $forward+=86400;
            $checker=$cwd;
            if ($cwd>7){
              $checker=$cwd-7;
            }
            if (!empty($scheduleWeekdays[$checker])){
              break;
            }
          }
        }
        foreach ($directions AS $d){
          $queryParts[]=$s['schedule_id'].','.$weekday.',\''.$d.'\','.$$d;
        }
      }
    }
    $this->db->query("INSERT IGNORE INTO `schedule_logistics_gap` VALUES (".join('),(',$queryParts).")");
  }
  
  function loadLogistics(){
    $this->load->model('Item_model');
    $this->load->model('ItemLock_model');

    $this->reply['data']['logistics']=array(
      'delivery'=>array('codes'=>array())
      ,'collection'=>array('codes'=>array())
    );
    $this->reply['data']['plainLogistics']=array();
    $this->reply['itemCache']=array();
    $logisticsOperations=array('delivery','collection');

    if (!empty($this->reply['data']['booking_id'])){
      $this->load->model('Booking_model');
      $data=$this->Booking_model->getLogistics($this->reply['data']['booking_id']);
      $this->reply['data']['delivery_timestamp']=$this->reply['data']['rent_period_start_timestamp'];
      $this->reply['data']['collection_timestamp']=$this->reply['data']['rent_period_end_timestamp'];
      $lockerObjectType='booking';
      $lockerObjectID=$this->reply['data']['booking_id'];
    }
    else {
      $this->load->model('QuoteVariation_model');
      $data=$this->QuoteVariation_model->getLogistics($this->reply['data']['quote_variation_id'],$this->reply['data']['delivery_timestamp'],$this->reply['data']['collection_timestamp']);
      $lockerObjectType='quote';
      $lockerObjectID=$this->reply['data']['quote_id'];
    }
    $availabilityCode=date('YmdHi',$this->reply['data']['delivery_timestamp']).'_'.date('YmdHi',$this->reply['data']['collection_timestamp']);
    
    ///$this->reply['rawLogisticsData']=$data;

    //if (!empty($this->reply['data']['booking_id'])){
      if (!empty($data)){
        foreach($data AS $entry){
          //$this->reply['data']['plain'][]=$entry;
          $this->reply['data']['plainLogistics'][$entry['atomic_item_id']]=array();
          $deliveryTimestamp=$entry['delivery_timestamp']-DELIVERY_TIME_PREFIX;
          $collectionTimestamp=$entry['collection_timestamp']+COLLECTION_TIME_SUFFIX;
          $lockHash=$this->ItemLock_model
            ->unlock($lockerObjectType,$lockerObjectID,$deliveryTimestamp,$collectionTimestamp,true);
/**/
          $config=array(
            'rentTimestamps'=>array('start'=>$deliveryTimestamp,'end'=>$collectionTimestamp)
            ,'strictRentTimestamps'=>true
            ,'replyData'=>&$this->reply['data']['plainLogistics'][$entry['atomic_item_id']]
            ,'onlyChosen'=>array($entry['atomic_item_id']),'lockHash'=>$lockHash
          );
          $this->Item_model->getFiltered($config);/**/
          foreach($this->reply['data']['plainLogistics'][$entry['atomic_item_id']]['entries'] AS &$chosenItem){
            $chosenItem['availability']=array($availabilityCode=>((($chosenItem['fixed_quantity']!==null)?$chosenItem['fixed_quantity']:$chosenItem['quantity'])-$chosenItem['booked']));
            $this->reply['itemCache'][$chosenItem['item_id']]=$chosenItem;
          }
          

          foreach($logisticsOperations AS $o){
            $code=date('YmdHi',$entry[$o.'_timestamp']);
            if (!isset($this->reply['data']['logistics'][$o][$code])){
              $this->reply['data']['logistics'][$o][$code]=array(
                'entries'=>array()
              );
              $this->reply['data']['logistics'][$o]['codes'][]=$code;
            }
            $this->reply['data']['logistics'][$o][$code]['entries'][]=$entry;
          }
        }
      }
      
      
      /**/
      foreach($this->reply['data']['entries']['items'] AS &$i){
        if (empty($i['item_id'])){
          $i['extra_item_id']=uniqid('E');
          foreach($logisticsOperations AS $o){
            $code=(!empty($i[$o.'_code']))?$i[$o.'_code']:date('YmdHi',$this->reply['data'][$o.'_timestamp']);
            if (!isset($this->reply['data']['logistics'][$o][$code])){
              $this->reply['data']['logistics'][$o][$code]=array(
                'entries'=>array()
              );
              $this->reply['data']['logistics'][$o]['codes'][]=$code;
            }
            $this->reply['data']['logistics'][$o][$code]['entries'][]=$i;
          }
        }
      }/**/

      foreach($this->reply['data']['entries']['services'] AS &$i){
        if (empty($i['item_id'])){
          $i['extra_item_id']=uniqid('E');
          foreach($logisticsOperations AS $o){
            
            $code=(!empty($i[$o.'_code']))?$i[$o.'_code']:date('YmdHi',$this->reply['data'][$o.'_timestamp']);
            if (!isset($this->reply['data']['logistics'][$o][$code])){
              $this->reply['data']['logistics'][$o][$code]=array(
                'entries'=>array()
              );
              $this->reply['data']['logistics'][$o]['codes'][]=$code;
            }
            $this->reply['data']['logistics'][$o][$code]['entries'][]=$i;
          }
        }
      }
    //}
  }

  function loadQuoteVariation($code){
    if (empty($code)){
      $this->error('Booking code for quote is not set',false,true);
    }

    $this->load->model('QuoteVariation_model');
    $this->QuoteVariation_model->parseDetails($this->reply['data'],$code);
   
    $this->reply['data']['paid_amount']=0;
    
    $this->getStatusList('quote');
    $this->reply['data']['status']=constant('STATUS_'.$this->reply['data']['status_id']);
  }

  function load(){
    $this->loadDetails(@$_POST['code']);
    $this->loadLogistics();
  }
  
  function view($code){
    $this->reply['config']['frontendSection']='booking';
    if (empty($code)){
      $code=$_REQUEST['code']; 
    }
    
    $this->loadDetails($code);
    $this->loadLogistics();
    
    $this->setTemplate('frontend/index','quote_variation/view');
  }

  function validateRequest(){
    $customerName=trim($_POST['customer_name']);
    if (!preg_match_all('/^([a-zA-Z]{2,} ?){2}$/',$customerName,$matches)){
      $this->error($this->lang->phrase('empty_name'),'customer_name');
    }

    $customerEmail=trim($_POST['customer_email']);
    if (!preg_match_all('/^[a-zA-Z0-9\._]+(\+[a-zA-Z0-9\._]+)?@[a-zA-Z0-9_]+(\.[a-zA-Z]{2,8}){1,2}$/',$customerEmail,$matches)){
      $this->error($this->lang->phrase('empty_email'),'customer_email');
    }
    
    $residentialAddress=$this->input->_fetch_from_array($_POST['residential_address']);
    //echo '<h3>residential</h3><pre>'; print_r($residentialAddress); echo '</pre>';
    foreach($residentialAddress AS $field=>$value){
      if ($field!='line_2'){
        if (trim($value)==''){
          $this->error($this->lang->phrase('empty_'.$field),'residential_address['.$field.']');
        }
      }
    }
    
    if (empty($_POST['residential_delivery_address'])){
      $deliveryAddress=$this->input->_fetch_from_array($_POST['delivery_address']);
      foreach($deliveryAddress AS $field=>$value){
        if ($field!='line_2'){
          if (trim($value)==''){
            $this->error($this->lang->phrase('empty_'.$field),'delivery_address['.$field.']');
          }
        }
      }
    }
  }

  function validateCustomerDetails($initialCustomerID=0){
    if ($customerID>0){
      $customerDetails=$this->db->get_where('user',array('user_id'=>$initialCustomerID),1)->row_array();
      $this->customerID=$customerDetails['user_id'];
    }
    else {
      if (empty($_POST['customer'])){
        $this->error('Customer should be set',false,true);
      }
      $customerDetails=&$_POST['customer'];
      $this->customerID=$_POST['customer_id']*1;
    }

    $resultFields=array('first_name','last_name','phone','email');
    $result=array();
    foreach($resultFields AS $f){
      $result[$f]=trim($customerDetails[$f]);
      if ($initialCustomerID>0 && !empty($_POST['customer'][$f])){
        $result[$f]=trim($_POST['customer'][$f]);
      }
    }

    if (!preg_match("/^[a-zA-Z`']{2,}$/", $result['first_name'])){
      $this->error('Customer first name should be valid');
    }
    if (!preg_match('/^[a-zA-Z`\']{2,}$/', $result['last_name'])){
      $this->error('Customer last name should be set');
    }
    if (!preg_match('/^[a-zA-Z0-9\._]+(\+[a-zA-Z0-9\._]+)?@[a-zA-Z0-9_]+(\.[a-zA-Z]{2,8}){1,2}$/', $result['email'])){
      $this->error('Customer email should be valid');
    }
    $result['phone']=preg_replace('/[^0-9]/','',$result['phone']);
    if (!preg_match('/^(0[0-9])?[0-9]{8}$/',$result['phone'])){
      $this->error('Customer phone should be valid');
    }
    if ($this->hasErrors()){
      $this->returnJSON();
    }

    $this->customerToCreate=$result;
  }

  function validateAddresses($initialResidentialAddressID=0,$initialDeliveryAddressID=0){
    $this->newAddresses=array();
    $this->load->model('Address_model');

    $requiredFields=array('line_1','city','state','postcode','phone');

    
    $initialResidentialAddressID=$residentialAddressID=@(($initialResidentialAddressID>0)?$initialResidentialAddressID:$_POST['residential_address_id'])*1;
    
    
    if ($residentialAddressID>0){
      $oldResidentialAddress=$this->Address_model->get($residentialAddressID);
      $this->reply['oldResidentialAddress']=$oldResidentialAddress;
      foreach($requiredFields AS $f){
        if (!empty($oldResidentialAddress[$f])){
          if (trim($_POST['residential_address'][$f])!=$oldResidentialAddress[$f]){
            $residentialAddressID=0;
            break;
          }
        }
        else {
          $residentialAddressID=0;
          break;
        }
      }
    }
    $this->reply['residentialAddressID']=$residentialAddressID;
    
    $residentialAddressID=(int)$this->Address_model->save(array_merge(
      $_POST['residential_address']
      ,array('type'=>'residential','address_id'=>$residentialAddressID)
    ),true,$requiredFields);
    $this->reply['newResidentialAddressID']=$residentialAddressID;
    
    if ($residentialAddressID>0){
      $this->reply['data']['residential_address_id']=$residentialAddressID;
      if ($initialResidentialAddressID!=$residentialAddressID){
        $this->newAddresses[]=$residentialAddressID;
      }
    }
    
    if (!empty($_POST['residential_delivery']) || $_POST['delivery_address_id']=='residential'){
      $deliveryAddressID=$residentialAddressID;
    }
    else {
      $initialDeliveryAddressID=$deliveryAddressID=@(($initialDeliveryAddressID==0)?$initialDeliveryAddressID:@$_POST['delivery_address_id'])*1;
      
      if ($deliveryAddressID>0){
        $oldDeliveryAddress=$this->Address_model->get($deliveryAddressID);
        foreach($requiredFields AS $f){
          if (!empty($oldDeliveryAddress[$f])){
            if (trim($_POST['delivery_address'][$f])!=$oldDeliveryAddress[$f]){
              $deliveryAddressID=0;
              break;
            }
          }
          else {
            $deliveryAddressID=0;
            break;
          }
        }
      }
      
      $deliveryAddressID=(int)$this->Address_model->save(array_merge(
        $_POST['delivery_address']
        ,array('type'=>'delivery','address_id'=>$deliveryAddressID)
      ),true,$requiredFields);
    
      if ($deliveryAddressID>0){
        $this->reply['data']['delivery_address_id']=$deliveryAddressID;
        if ($initialDeliveryAddressID!=$deliveryAddressID){
          $this->newAddresses[]=$deliveryAddressID;
        }
      }
    }
    
    if ($this->hasErrors()){
      $this->returnJSON();
    }

    $deliveryContact=array();
    $fields=array('name','contact_name','contact_phone','contact_email');
    foreach($fields AS $f){
      if (!empty($_POST['delivery_address'][$f])){
        $deliveryContact[$f]=$_POST['delivery_address'][$f];
      }
    }
    return array('residential_address_id'=>$residentialAddressID,'delivery_address_id'=>$deliveryAddressID,'delivery_contact_json'=>json_encode($deliveryContact));
  }

  function create(){
    //$this->validateCustomerDetails();
    $dataSet=$this->validateAddresses();
    $dataSet['creation_timestamp']=MAIN_TIMESTAMP;
    $dataSet['customer_id']=$this->customerID;
    $this->reply['data']['dataSet']=$dataSet;

    $code=(!empty($_POST['code']))?$_POST['code']:'';

    $request=array();
    $itemQuantities=array();
    $lockHash='';
    $this->newItemLocks=array();

    $this->load->model('ItemLock_model');
    $this->load->model('Item_model');

    $fromQuote=false;
    $toUnlock=array();
    $quoteVariationID=0;
    $quoteLockerID=0;
    $strictTimestamps=false;
    
    if(!empty($code)){
      $existingBooking=$this->db->get_where('booking',array('code'=>$code),1)->row_array();

      if (!empty($existingBooking)){
        $this->error('This booking was already created before',false,true);
      }
      $fromQuote=true;
      
      $data=array();
      $this->load->model('QuoteVariation_model');
      $this->QuoteVariation_model->parseDetails($data,$code);

      $this->validateCustomerDetails($data['customer_id']);
      $quoteVariationID=$data['quote_variation_id'];
      $quoteLocker=$this->QuoteVariation_model->getLockerID($quoteVariationID);
      $quoteLockerID=($quoteLocker['status_id']==$this->getStatusOption('quote','active'))?$quoteLocker['quote_id']:0;
      
      $dataSet['chargeable_days']=$data['chargeable_days'];
      $dataSet['discount_type']=$data['discount_type'];
      $dataSet['discount_value']=$data['discount_value'];
      $dataSet['total_amount']=$data['grand_total'];
      $dataSet['entries_json']=$data['entries_json'];
      $dataSet['deposit_type']=$data['deposit_type'];
      $dataSet['deposit_value']=$data['deposit_value'];
      $dataSet['purchase_order']=$data['purchase_order'];
      $dataSet['due_days']=$data['due_days'];
      $dataSet['due_timestamp']=$data['delivery_timestamp']+$data['due_days']*86400;
      $dataSet['status_id']=BOOKING_STATUS_MOVING;
      
      $this->reply['initial']=$data;

      $request['itemQuantities']=$data['itemQuantities'];
      $this->validateRentTimestamps($data);

      $strictTimestamps=true;
      $lockedTimestamps=$this->rentTimestamps['final'];
    }
    else {
      $this->validateCustomerDetails();
      $code=$this->makeCode();
      $request['itemQuantities']=$_POST['quantity'];

      $this->validateRentTimestamps($_POST);

      $lockedTimestamps=false;
      $dataSet['chargeable_days']=ceil(($this->rentTimestamps['end']-$this->rentTimestamps['start'])/86400);
      $dataSet['status_id']=BOOKING_STATUS_PENDING;
    }

    $dataSet['creation_timestamp']=MAIN_TIMESTAMP;
    $dataSet['quote_variation_id']=$quoteVariationID;
    $dataSet['rent_period_start_timestamp']=$this->rentTimestamps['start'];
    $dataSet['rent_period_end_timestamp']=$this->rentTimestamps['end'];
    $dataSet['code']=$code;

    
    $simpleQuantities=array();
    $simpleItemIDs=array();

    
    foreach($request['itemQuantities'] AS $itemID=>$quantity){
      if ($quantity>0){
        $packageInfo=$this->Item_model->getPackage($itemID);
        if (empty($packageInfo)){
          if (empty($simpleQuantities[$itemID])){
            $simpleQuantities[$itemID]=0;
            $simpleItemIDs[]=$itemID;
          }
          $simpleQuantities[$itemID]+=$quantity;
        }
        else {
          foreach($packageInfo AS $packedItem){
            if (empty($simpleQuantities[$packedItem['item_id']])){
              $simpleQuantities[$packedItem['item_id']]=0;
              $simpleItemIDs[]=$packedItem['item_id'];
            }
            $simpleQuantities[$packedItem['item_id']]+=($packedItem['quantity']*$quantity);
          }
        }
      }
      if (!$fromQuote){
        /**
         *  Price checker to put here 
        **/
      }
    }

    $dataSet['total_amount']=round($dataSet['total_amount'],2);
    $remoteFinalAmount=round($_POST['payment']['final'],2);

    if ($remoteFinalAmount!=$dataSet['total_amount']){
      $this->error('Final amount ('.$remoteFinalAmount.') doesn\'t match ('.$dataSet['total_amount'].')',false,true);
    }

    $initialTimestamps=$this->rentTimestamps['final'];
    $initialStartWeekday=date('N',$this->rentTimestamps['final']['start']);
    $initialEndWeekday=date('N',$this->rentTimestamps['final']['end']);
    
    if (empty($simpleItemIDs)){
    }
    else {
      $scheduleTemplates=array();
      if (!empty($lockedTimestamps)){
        $scheduleTemplates[$this->rentTimestamps['final']['start'].'_'.$this->rentTimestamps['final']['end']]=$simpleItemIDs;
      }
      else {
        $scheduleData=$this->db->where_in('i.item_id',$simpleItemIDs)
          ->select('i.item_id,s.weekdays')
          ->join('schedule AS s','s.schedule_id=i.schedule_id')
          ->get('item AS i')->result_array();

        foreach ($scheduleData AS $itemSchedule){
          $prefix=0;
          $suffix=0;

          $weekdays=explode(',',$itemSchedule['weekdays']);
          $weekdayCodes=array();

          foreach($weekdays AS $weekday){
            $weekdayCodes[$weekday]=1;
          }

          if (empty($weekdayCodes[$initialStartWeekday])){
            $wdx=$initialStartWeekday-7;
            for ($wds=$initialStartWeekday-1;$wds>$wdx;$wds++){
              $prefix+=86400;
              if ($wds<1){
                $wd=$wds+7;
              }
              else {
                $wd=$wds;
              }
              if (!empty($weekdayCodes[$wd])){
                break;
              }
            }
          }

          if (empty($weekdayCodes[$initialEndWeekday])){
            $wdx=$initialEndWeekday+7;
            for ($wde=$initialEndWeekday+1;$wde<$wdx;$wde++){
              $suffix+=86400;
              if ($wde>7){
                $wd=$wde-7;
              }
              else {
                $wd=$wde;
              }
              if (!empty($weekdayCodes[$wd])){
                break;
              }
            }
          }

          $scheduleCode=($initialTimestamps['start']-$prefix).'_'.($initialTimestamps['end']+$suffix);

          if (empty($scheduleTemplates[$scheduleCode])){
            $scheduleTemplates[$scheduleCode]=array();
          }
          $scheduleTemplates[$scheduleCode][]=$itemSchedule['item_id'];
        }
      }
      
      $this->reply['data']['scheduleTemplates']=$scheduleTemplates;
      
      //$this->error('just testing (LINE '.__LINE__.'), no data will be set',null,true);
      
      foreach($scheduleTemplates AS $scheduleCode=>$scheduledItemIDs){
        $timestampParts=explode('_',$scheduleCode);
        $this->reply['data']['items']=array();
        if ($quoteLockerID>0){
          $lockHash=$this->ItemLock_model->unlock('quote',$quoteLockerID,$timestampParts[0],$timestampParts[1],$strictTimestamps);
        }
        $config=array(
          'rentTimestamps'=>array('start'=>$timestampParts[0],'end'=>$timestampParts[1])
          ,'strictRentTimestamps'=>$strictTimestamps
          ,'replyData'=>& $this->reply['data']['items']
          ,'onlyChosen'=>$scheduledItemIDs,'lockHash'=>$lockHash,'stickLockHash'=>true
        );

        $this->Item_model->getFiltered($config);
        $availableQuantity=0;
        foreach($this->reply['data']['items']['entries'] AS $item){
          $availableQuantity=(($item['fixed_quantity']!==null)?$item['fixed_quantity']:$item['quantity'])-$item['booked'];
          if ($availableQuantity<$simpleQuantities[$item['item_id']]){
            $this->error('Overlap for '.$item['title']);
          }
        }
        
        if (!$this->hasErrors()){
          foreach($scheduledItemIDs AS $scheduledItemID){
            $this->ItemLock_model->add($scheduledItemID,$simpleQuantities[$scheduledItemID],$config['rentTimestamps']['start'],$config['rentTimestamps']['end'],$this->newItemLocks);
          }
        }
        
        $this->reply['data']['items']=array();

        $this->Item_model->getFiltered($config);
        foreach($this->reply['data']['items']['entries'] AS $item){
          $availableQuantity=(($item['fixed_quantity']!==null)?$item['fixed_quantity']:$item['quantity'])-$item['booked'];
          if ($availableQuantity<0){
            $this->error('Overlap for '.$item['title']);
          }
        }
      }
      
    }

    //$this->error('just testing (LINE '.__LINE__.'), no data will be set',null,true);

    $this->db->insert('booking',$dataSet);
    $bookingID=$this->db->insert_id();
    $this->reply['data']['booking_id']=$bookingID;

    if ($this->hasErrors()){
      if (!empty($this->newItemLocks)){
        $this->ItemLock_model->cancelLocks($this->newItemLocks);
      }
      if (!empty($bookingID)){
        $this->db->delete('booking',array('booking_id'=>$bookingID));
      }
    }
    else {
      $this->ItemLock_model->replaceLockers('booking',$bookingID,$this->newItemLocks);
      $this->reply['data']['afterReplacement']=$this->db->get('item_lock')->result_array();
      foreach($scheduleTemplates AS $scheduleCode=>$scheduledItemIDs){
        $timestampParts=explode('_',$scheduleCode);
        $timestampParts[0]+=$this->rentTimestamps['prefix'];
        $timestampParts[1]-=$this->rentTimestamps['suffix'];
        
        foreach($scheduledItemIDs AS $simpleItemID){
          $this->db->insert('booking_atomic_item',array(
            'booking_id'=>$bookingID
            ,'atomic_item_id'=>$simpleItemID
            ,'quantity'=>$simpleQuantities[$simpleItemID]
            ,'delivery_timestamp'=>$timestampParts[0]
            ,'collection_timestamp'=>$timestampParts[1]
          ));
        }
      }
      if ($quoteVariationID>0){
        $this->db->query('INSERT INTO `booking_item` SELECT '.$bookingID.' AS `booking_id`,`item_id`,`quantity`,`unit_price`,`discount_type`,`discount_value` FROM `quote_variation_item` WHERE `quote_variation_id`='.$quoteVariationID);
        if ($quoteLockerID>0){
          $this->ItemLock_model->cancelLocks($this->newItemLocks);
          $this->db->where('quote_id',$quoteLockerID)
            ->update('quote',array('status_id'=>$this->getStatusOption('quote','moving')));
          $this->db->where('booking_id',$bookingID)
            ->update('booking',array('status_id'=>$this->getStatusOption('booking','moving')));
        }
      }
      else {
        
      }
    }
    $this->reply['data']['itemLocks']=$this->newItemLocks;
  }
  
  function validateLogistics(){
    $this->load->model('Item_model');
    $this->load->model('ItemLock_model');
    $extraItems=array();
    $chosenItems=array();
    $simpleItemIDs=array();
    $simpleQuantities=array();
    $modes=array('delivery','collection');

    if (empty($_POST['entry'])){
      $this->error('No content has been chosen',false,true);
    }
    $unitItems=array();
    $atomicItems=array();
    
    $subtotal=0;
    
    foreach ($_POST['entry']['entries'] AS $entryID){
      $entry=&$_POST['entry'][$entryID];
      $entryPrice=$entry['quantity']*$entry['price'];
      if ($entry['type']=='service'){
        $entryPrice*=$entry['people'];
      }
      else {
        $entryPrice*=$_POST['chargeable_days'];
      }
      if (!empty($entry['item_id'])){
        $itemID=$entry['item_id'];
        $quantity=$entry['quantity'];
        $unitItems[]=array(
          'booking_id'=>&$this->bookingID
          ,'item_id'=>$entry['item_id']
          ,'quantity'=>$entry['quantity']
          ,'unit_price'=>$entry['price']
          ,'discount_type'=>$entry['discount_type']
          ,'discount_value'=>$entry['discount_value']
        );
        $packageInfo=$this->Item_model->getPackage($itemID);
        if (empty($packageInfo)){
          if (empty($simpleQuantities[$itemID])){
            $simpleQuantities[$itemID]=0;
            $simpleItemIDs[]=$itemID;
          }
          $simpleQuantities[$itemID]+=$quantity;
        }
        else {
          foreach($packageInfo AS $packedItem){
            if (empty($simpleQuantities[$packedItem['item_id']])){
              $simpleQuantities[$packedItem['item_id']]=0;
              $simpleItemIDs[]=$packedItem['item_id'];
            }
            $simpleQuantities[$packedItem['item_id']]+=($packedItem['quantity']*$quantity);
          }
        }
      }
      else {
        $extraItemID=$entry['extra_item_id'];
        foreach($modes AS $m){
          $entry[$m.'_code']=$_POST['logistics'][$extraItemID][$m];
        }
        $extraItems[]=$_POST['entry'][$entryID];
      }
      
      $subtotal+=($entryPrice-number_format(
        ($entry['discount_type']=='percentage')?($entryPrice*$entry['discount_value']/100):$entry['discount_value'],2
      ));
    }
    
    $this->grandTotal=$subtotal-number_format(($_POST['discount_type']=='percentage')?($subtotal*$_POST['discount_value']/100):$_POST['discount_value'],2
    );
    
    $this->reply['data']['extraItems']=&$extraItems;
    
    $logisticsSimpleItems=array();
    foreach($_POST['logistics'] AS $itemID=>$itemData){
      if ($itemID>0){
        $logisticsSimpleItems[$itemID]=(int)$itemData['quantity'];
      }
    }

    ksort($simpleQuantities,SORT_NUMERIC);
    ksort($logisticsSimpleItems,SORT_NUMERIC);
    $this->reply['data']['simpleQuantities']=$simpleQuantities;
    $this->reply['data']['logisticsSimpleItems']=$logisticsSimpleItems;
    if ($simpleQuantities!==$logisticsSimpleItems){
      $this->error('Chosen logistics doesn\'t match calculated',null,true);
    }
    
    $timestampTemplates=array();
    $this->scheduleTemplates=array();
    
    foreach($simpleQuantities AS $itemID=>$quantity){
      $deliveryTimestamp=$this->codeToTimestamp($_POST['logistics'][$itemID]['delivery']);
      $collectionTimestamp=$this->codeToTimestamp($_POST['logistics'][$itemID]['collection']);
      $atomicItems[]=array(
        'booking_id'=>&$this->bookingID
        ,'atomic_item_id'=>$itemID,'quantity'=>$quantity
        ,'delivery_timestamp'=>$deliveryTimestamp,'collection_timestamp'=>$collectionTimestamp
      );
      $scheduleCode=($deliveryTimestamp-DELIVERY_TIME_PREFIX).'_'.($collectionTimestamp+COLLECTION_TIME_SUFFIX);
      if (empty($this->scheduleTemplates[$scheduleCode])){
        $this->scheduleTemplates[$scheduleCode]=array();
      }
      $this->scheduleTemplates[$scheduleCode][]=$itemID;
    }
    $this->bookingItems=array('unit'=>&$unitItems,'atomic'=>&$atomicItems,'extra'=>&$extraItems);
    $this->reply['data']['scheduleTemplates']=&$this->scheduleTemplates;
    $this->updateLogistics($simpleQuantities);
  }

  function codeToTimestamp($code){
    return mktime(substr($code,8,2),substr($code,-2),0,substr($code,4,2),substr($code,6,2),substr($code,0,4));
  }

  function updateLogistics($simpleQuantities){
    $this->load->model('ItemLock_model');
    $this->load->model('Item_model');

    $this->newItemLocks=array();
    $this->reply['data']['items']=array();
    $this->reply['data']['logisticsLog']=array();
    
    $itemSet=0;
    foreach($this->scheduleTemplates AS $scheduleCode=>$scheduledItemIDs){
      $this->reply['data']['items'][$itemSet]=array();
      $timestampParts=explode('_',$scheduleCode);
      $lockHash=false;
      if (!empty($this->bookingID)){
        $lockHash=$this->ItemLock_model
          ->unlock('booking',$this->bookingID,$timestampParts[0],$timestampParts[1],true);
      }
      /** /if ($quoteLockerID>0){
        $lockHash=$this->ItemLock_model->unlock('quote',$quoteLockerID,$timestampParts[0],$timestampParts[1],true);
      }/**/
      $config=array(
        'rentTimestamps'=>array('start'=>$timestampParts[0],'end'=>$timestampParts[1])
        ,'strictRentTimestamps'=>true
        ,'replyData'=>& $this->reply['data']['items'][$itemSet]
        ,'onlyChosen'=>$scheduledItemIDs,'lockHash'=>$lockHash,'stickLockHash'=>true
      );

      $this->Item_model->getFiltered($config);
      $availableQuantity=0;
      if (!empty($config['replyData'])){
        foreach($this->reply['data']['items'][$itemSet]['entries'] AS &$item){
          $availableQuantity=(($item['fixed_quantity']!==null)?$item['fixed_quantity']:$item['quantity'])-$item['booked'];
          if ($availableQuantity<$simpleQuantities[$item['item_id']]){
            $this->error('Overlap for '.$item['title']);
          }
        }
      }

      if (!$this->hasErrors()){
        
        foreach($scheduledItemIDs AS $scheduledItemID){/** /
          $this->reply['data']['logisticsLog'][]=array(
            'timestamp'=>array($config['rentTimestamps']['start'],$config['rentTimestamps']['end'])
            ,'itemID'=>$scheduledItemID
            ,'quantity'=>$simpleQuantities[$scheduledItemID]
          );/**/
          $this->ItemLock_model->add($scheduledItemID,$simpleQuantities[$scheduledItemID],$config['rentTimestamps']['start'],$config['rentTimestamps']['end'],$this->newItemLocks);
        }
      }

      $this->reply['data']['items'][$itemSet]=array();

      $this->Item_model->getFiltered($config);
      foreach($this->reply['data']['items'][$itemSet]['entries'] AS $item){
        $availableQuantity=(($item['fixed_quantity']!==null)?$item['fixed_quantity']:$item['quantity'])-$item['booked'];
        if ($availableQuantity<0){
          $this->error('Overlap for '.$item['title']);
        }
      }
      $itemSet++;
    }
    //$this->error('cancel locks');
    if ($this->hasErrors()){
      if (!empty($this->newItemLocks)){
        $this->ItemLock_model->cancelLocks($this->newItemLocks);
      }
    }
  }
  
  function save(){
    $this->continueIfAllowed(array('allowed'=>array('admin')));
    
    $dataSet=$this->input->post(array('booking_id','status_id','customer_id','chargeable_days'));
    foreach($dataSet AS $k=>$v){
      $dataSet[$k]=$v*1;
    }
    if ($dataSet['status_id']==0){
      $this->error('status_empty',false,true);
    }

    if (empty($_POST['discount_type'])){
      $this->error('global discount type should be chosen',false,true);
    }
    $this->bookingID=$dataSet['booking_id'];
    
    $this->load->model('ItemLock_model');
    
    if ($_POST['status_id']!=$this->getStatusOption('booking','cancelled')){
      $this->validateRentTimestamps($_POST);
      $this->validateCustomerDetails();
      $dataSet=array_merge($dataSet,$this->validateAddresses());
      $this->validateLogistics();
      
      if (!$this->hasErrors()){
        $dataSet['rent_period_start_timestamp']=$this->rentTimestamps['start'];
        $dataSet['rent_period_end_timestamp']=$this->rentTimestamps['end'];
        $dataSet['entries_json']=json_encode($this->bookingItems['extra']);
        $dataSet['discount_type']=$_POST['discount_type'];
        $dataSet['discount_value']=$_POST['discount_value'];
        $dataSet['total_amount']=$this->grandTotal;

        if ($this->bookingID==0){
          $dataSet['customer_id']=$this->customerID;
          if ($dataSet['customer_id']==0){
            $this->db->insert('user',array_merge($this->customerToCreate,array('role_id'=>$this->db->get_where('role',array('code'=>'customer'),1)->row_array()['role_id'])));
            $dataSet['customer_id']=$this->db->insert_id();
            $this->customerID=$dataSet['customer_id'];
          }

          $dataSet['creation_timestamp']=MAIN_TIMESTAMP;
          
          $dataSet['code']=$this->makeCode();
          
          $this->db->insert('booking',$dataSet);
          $this->bookingID=$this->db->insert_id();
        }
        else {
          $this->db->where('booking_id',$this->bookingID)->update('booking',$dataSet);
        }
        if (!empty($this->newAddresses)){
          $this->Address_model->updateOwnership($this->customerID,$this->newAddresses);
        }
        
        $this->db->delete('booking_item',array('booking_id'=>$this->bookingID));
        $this->db->insert_batch('booking_item',$this->bookingItems['unit']);

        $this->db->delete('booking_atomic_item',array('booking_id'=>$this->bookingID));
        $this->db->insert_batch('booking_atomic_item',$this->bookingItems['atomic']);
        
        $this->ItemLock_model->replaceLockers('booking',$this->bookingID,$this->newItemLocks);
        
        $this->reply['bookingItems']=&$this->bookingItems;
      }
    }
    //$this->error('Testing booking save');
  }

  function pay(){
    $bookingID=$_POST['booking_id']*1;
    if ($bookingID==0){
      $this->error('Booking is not chosen',false,true);
    }
    $bookingData=$this->db->get_where('booking AS b',array('b.booking_id'=>$bookingID),1)->row_array();
    $amount=round($_POST['amount']*1,2);
    $remainedAmount=$bookingData['total_amount']-$bookingData['paid_amount'];
    
    
    if ($amount>$remainedAmount){
      $this->error('Too big amount chosen, please make sure that there is no error',false,true);
    }
    
    $paymentConfig=array(
      'base'=>array(
        'processorCode'=>$_POST['processor']
        ,'targetObjectType'=>'booking'
        ,'targetObjectID'=>$bookingID
        ,'description'=>'Payment for order '.$bookingData['code']
        ,'amount'=>$amount
        ,'currencyCode'=>''
      )
      ,'custom'=>array()
    );
    
    switch($_POST['processor']){
      case 'stripe':
        $paymentConfig['base']['cardType']=$_POST['card']['type'];
        $paymentConfig['base']['currencyCode']=$_POST['currency_code'];
        $paymentConfig['base']['card_ending']=$_POST['card']['ending'];
        $paymentConfig['custom']['token']=$_POST['token'];
      break;
    }
    
    $this->load->library('PaymentHub',null,'BookingPayment');
    
    //echo '<pre>'; print_r($this); echo '</pre>'; die();
    
    $this->BookingPayment->charge($paymentConfig);
    
    $dataToUpdate=array(
      'paid_amount'=>($bookingData['paid_amount']+$amount)
      ,'status_id'=>((($bookingData['total_amount']-$amount)<=$bookingData['paid_amount'])?BOOKING_STATUS_FINALIZED:BOOKING_STATUS_CONFIRMED)
    );
    
    $this->db->where('booking_id',$bookingID)->update('booking',$dataToUpdate);
    
    $bookingStatusCode=constant('STATUS_'.$bookingData['status_id']);
    switch($bookingStatusCode){
      case 'moving':
        $this->load->model('ItemLock_model');
        $this->ItemLock_model->refreshLocks('booking',$bookingID);
        $this->load->model('QuoteVariation_model');
        $quoteLocker=$this->QuoteVariation_model->getLockerID($bookingData['quote_variation_id']);
        if ($quoteLocker['status_id']==$this->getStatusOption('quote','moving')){
          $this->ItemLock_model->removeLockers('quote',$quoteLocker['quote_id'],true);
          $this->db->where('quote_id',$quoteLocker['quote_id'])
            ->update('quote',array('status_id'=>$this->getStatusOption('quote','booked')));
        }
      break;
    }
    $this->reply['data']['code']=$bookingData['code'];
  }
  
  function removeInvalid(){
    $bookings=$this->db
      ->where_in('status_id',$this->getStatusOption('booking',array('moving','pending')))
      ->where('creation_timestamp <',(MAIN_TIMESTAMP-300))
      ->get('booking',10)->result_array();
    if ($bookings){
      $this->load->model('ItemLock_model');
      $this->load->model('QuoteVariation_model');
      foreach($bookings AS $b){
        if ($b['quote_variation_id']){
          
        }
      }
    }
  }
}

?>