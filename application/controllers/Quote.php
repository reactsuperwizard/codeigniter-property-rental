<?php

class Quote extends NS_Rental_Controller {
  function __construct($config = array()) {
    parent::__construct($config);
    /** /
    $this->continueIfAllowed(array(
      'allowed'=>array('admin'),'ignore'=>array('quote/filtered','quote/load')
    ));/**/
    if (!empty($_GET['debug']) && $_GET['debug']=='profiler'){
      $this->output->enable_profiler(TRUE);
    }
    $this->loadTranslationCodes();
    //
    $this->load->library('EmailHandler',false,'EmailHandler');
  }
  
  function index(){
    /** /$this->db->query("UPDATE `quote` "
      ."SET `delivery_timestamp`=(`delivery_timestamp`+86400*30)"
        .",`collection_timestamp`=(`collection_timestamp`+86400*30) "
      ."WHERE `delivery_timestamp`<'".time()."'");/**/
    
    if ($this->userInfo['role']=='admin'){
      $this->load->model('Venue_model');
      $this->reply['config']['venues']=$this->Venue_model->getAll();
      
      $this->reply['config']['statuses']=$this->getStatusList('quote');
      $this->setTemplate('backend','quote/management');
      $this->output->enable_profiler(TRUE);
    }
    else {
      $this->setTemplate('frontend/index');
    }
  }
  
  function filtered($config=array()){
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
    $config['searchTerm']=$searchTerm;
    
    if ($this->userInfo['role']!='admin'){
      $config['customerID']=$this->userInfo['user_id'];
    }
    
    if (isset($_POST['mode'])){
      $config['mode']=$_POST['mode'];
    }

    $this->load->model('Quote_model');
    $this->Quote_model->getFiltered($config);
    
    foreach($this->reply['data']['entries'] AS &$e){
      $e['rent_start']=date('jS M Y',$e['delivery_timestamp']);
      $e['delivery_datetime']=date('jS M Y H:i',$e['delivery_timestamp']);
      //$e['delivery_timestamp']=date('Y-m-d H:i',$e['delivery_timestamp']);
      
      $e['collection_datetime']=date('jS M Y H:i',$e['collection_timestamp']);
      //$e['collection_timestamp']=date('Y-m-d H:i',$e['collection_timestamp']);
      
      $e['expiration_datetime']=date('jS M Y H:i',$e['expiration_timestamp']);
    }
  }
  
  function isLocked($quoteID){
    if ($quoteID>0){
      $this->getStatusList('quote');
      $data=$this->db
        ->select('IF((pq.quote_id>0),\'parent\',\'usual\') AS quote_mode'
          .',q.status_id AS usual_status_id,q.quote_id AS usual_quote_id'
          .',pq.status_id AS parent_status_id,pq.quote_id AS parent_quote_id')
        ->join('quote AS pq','pq.quote_id=q.parent_id','left')
        ->get_where('quote AS q',array('q.quote_id'=>$quoteID),1)->row_array();
      switch($data[$data['quote_mode'].'_status_id']){
        case QUOTE_STATUS_MOVING:
          $this->error('Quote is moving',false,true);
        break;
        case QUOTE_STATUS_BOOKED:
          $this->error('Quote is already booked',false,true);
        break;
      }
    }
  }
  
  function preparePaymentOptions(&$source,&$destination){
    if (empty($source['purchase_order']) || trim($source['purchase_order'])!='1'){
      $destination['purchase_order']=0;
    }
    else {
      $destination['purchase_order']=1;
    }
    
    if (!empty($source['deposit_type'])){
      $destination['deposit_type']=$source['deposit_type'];
    }
    
    $destination['deposit_value']=$source['deposit_value'];
    if (!isset($source['due_direction'])){
      $destination['due_days']=NULL;
    }
    else {
      switch($source['due_direction']){
        case '-':
          $destination['due_days']=-$source['due_days'];
        break;
        case '+':
          $destination['due_days']=$source['due_days'];
        break;
      }
    }
  }

  
  
  function checkCustomerCompany(){
    if ($this->quoteID>0){
      $this->customerID=$this->db
        ->get_where('quote',array('quote_id'=>$this->quoteID),1)->row_array()['customer_id'];
    }
    $this->customerCompany='';
    if ($this->customerID>0){
      $this->customerCompany=trim($this->db->get_where('user',array('user_id'=>$this->customerID),1)->row_array()['company_name']);
    }
  }


  function save(){
    $dataSet=$this->input->post(array('status_id','customer_id'));
    foreach($dataSet AS $k=>$v){
      $dataSet[$k]=intval($v);
    }
    if ($dataSet['status_id']==0){
      $this->error('status_empty',false,true);
    }
    $dataSet['name']=$this->input->post('name');

    $this->preparePaymentOptions($_POST,$dataSet);

    $this->load->model('ItemLock_model');
    $this->quoteID=intval($_POST['quote_id']);
    
    $this->isLocked($this->quoteID);
    
    //$this->log('quote #'.$this->quoteID.' is not locked');
    if ($_POST['status_id']==$this->getStatusOption('quote','active')){
      //$this->log('quote is active');
      $dataSet['chargeable_days']=floatval($_POST['chargeable_days']);
      if ($dataSet['chargeable_days']==0){
        $this->error('Chargeable days should be set');
      }
      $this->validateExpiration();
      $dataSet['expiration_timestamp']=$this->expirationTimestamp;

      $dataSet['customer_id']=$this->validateCustomerDetails();
      
      $this->checkCustomerCompany();
      
      if ($this->hasErrors()){
        $this->returnJSON();
      }
      
      $dataSet=array_merge($dataSet,$this->validateAddresses());
      
      $this->variationIDs=array();

      $this->validateRentTimestamps($_POST);
      $dataSet['delivery_timestamp']=$this->rentTimestamps['start'];
      $dataSet['collection_timestamp']=$this->rentTimestamps['end'];
      

      if (!$this->hasErrors()){
        $this->prepareVariations();

        if (!$this->hasErrors()){
          if ($this->quoteID==0){
            $dataSet['creator_id']=$this->userInfo['user_id'];
            $dataSet['customer_id']=$this->customerID;
            
            $this->db->insert('quote',$dataSet);
            $this->quoteID=$this->db->insert_id();

            $emailSubject='Quote created';
          }
          else {
            //HTEST CODE quote updating part TEST
            $dataSet['creator_id']=$this->userInfo['user_id'];
            $dataSet['customer_id']=$this->customerID;
            //
            $this->db->where('quote_id',$this->quoteID)->update('quote',$dataSet);
            
            $emailSubject='Quote '.$this->quoteID.' updated';
          }
          $this->EmailHandler->basicReport($emailSubject);
          
          if (!empty($this->newAddresses)){
            $this->Address_model->updateOwnership($dataSet['customer_id'],$this->newAddresses);
          }
          $this->db->where('quote_id',$this->quoteID)->where('code',NULL)->update('quote',array('code'=>$this->makeCode()));
          
          $this->db->where_in('quote_variation_id',$this->variationIDs)->update('quote_variation',array('quote_id'=>$this->quoteID));

          $this->ItemLock_model->replaceLockers('quote',$this->quoteID,$this->newItemLocks);
          if (!empty($this->newItemLocks)){
            $this->ItemLock_model->validateTimestamps('quote',$this->quoteID,$this->rentTimestamps['final']);
          }
        }
      }
    }
    else {
      if ($_POST['status_id']==$this->getStatusOption('quote','inactive')){
      //$this->log('quote is not active');
        if ($this->quoteID>0){
          $this->ItemLock_model->removeLockers('quote',$this->quoteID);
        }
      }
      else {
        $this->error($this->lang->phrase('WRONG_STATUS_TO_SAVE_QUOTE'),false,true);
      }
    }
    //$this->error('test stop');
  }
  
  function loadDetails($quoteID=0,$sections=array()){
    if ($quoteID==0){
      return $this->error($this->lang->phrase('quote_is_not_chosen'));
    }
    
    $this->reply['data']=$this->db->select('q.*')
      ->get_where('quote AS q',array('q.quote_id'=>$quoteID),1)->row_array();
    if (empty($this->reply['data'])){
      return $this->error($this->lang->phrase('quote_is_not_found'));
    }
    $this->reply['data']['expiration_date']=date('Y-m-d',$this->reply['data']['expiration_timestamp']);
    $this->reply['data']['expiration_date_string']=date('jS M Y',$this->reply['data']['expiration_timestamp']);
    $this->reply['data']['expiration_time']=date('H:i',$this->reply['data']['expiration_timestamp']);
    $this->reply['data']['expiration_flag']=(($this->reply['data']['expiration_timestamp']<=MAIN_TIMESTAMP)?1:0);
    
    $this->reply['data']['delivery_date']=date('Y-m-d',$this->reply['data']['delivery_timestamp']);
    $this->reply['data']['delivery_date_string']=date('jS M Y',$this->reply['data']['delivery_timestamp']);
    $this->reply['data']['delivery_time']=date('H:i',$this->reply['data']['delivery_timestamp']);
    $this->reply['data']['collection_date']=date('Y-m-d',$this->reply['data']['collection_timestamp']);
    $this->reply['data']['collection_date_string']=date('jS M Y',$this->reply['data']['collection_timestamp']);
    $this->reply['data']['collection_time']=date('H:i',$this->reply['data']['collection_timestamp']);
    if ($this->reply['data']['customer_id']>0){
      $this->reply['data']['customer']=$this->db
        ->select('user_id,IFNULL(first_name,\'\') AS first_name,IFNULL(last_name,\'\') AS last_name,email,company_name AS `company`,IFNULL(phone,\'\') AS phone')
        ->get_where('user',array('user_id'=>$this->reply['data']['customer_id']),1)->row_array();
    }
    $this->reply['data']['delivery_contact']=json_decode($this->reply['data']['delivery_contact_json']);
    $this->reply['data']['delivery_address'] = $this->db->select('a.*')
    ->get_where('address AS a',array('a.address_id'=>$this->reply['data']['delivery_address_id'], 'address_type_id'=>'2'),1)->row_array();

    // $this->reply['data']['users_delivery_address'] = $this->db->select('a.*')
    // ->get_where('address AS a',array('a.user_id'=>'351'),1)->row_array();

    
    $this->reply['data']['variations']=$this->db->select('*,IFNULL(notes,\'\') AS notes')->get_where('quote_variation',array('quote_id'=>$quoteID))->result_array();
    $vx=count($this->reply['data']['variations']);
    for ($vi=0;$vi<$vx;$vi++){
      $this->reply['data']['variations'][$vi]['items']=$this->db->get_where('quote_variation_item',array(
        'quote_variation_id'=>$this->reply['data']['variations'][$vi]['quote_variation_id']
      ))->result_array();
    }
  }

  function edit($quoteID=0){
    if ($quoteID==0){
      $quoteID=intval($this->input->post('quote_id'));
    }
    
    $this->loadDetails($quoteID);
    if ($this->reply['data']['status_id']!=$this->getStatusOption('quote','active')){
      $this->error($this->lang->phrase('WRONG_STATUS_TO_EDIT_QUOTE'),false,true);
    }
    
    $this->load->model('Item_model');
    $this->load->model('ItemLock_model');
    
    $items=array();
    $config=array('rentTimestamps'=>array(
      'start'=>($this->reply['data']['delivery_timestamp']-DELIVERY_TIME_PREFIX)
      ,'end'=>($this->reply['data']['collection_timestamp']+COLLECTION_TIME_SUFFIX)
    ),'replyData'=>& $items,'getPackages'=>1,'thumbnails'=>1);
    
    $lockedItems=$this->ItemLock_model->getLockedItemIDs('quote',$quoteID);
    if (!empty($lockedItems)){
      $config['onlyChosen']=array();
      foreach ($lockedItems AS $lockedItem){
        $config['onlyChosen'][]=$lockedItem['item_id'];
        $config['onlyChosen'][]=$lockedItem['item_package_id'];
      }
    }
    $config['lockHash']=$this->ItemLock_model->unlock('quote',$quoteID,$config['rentTimestamps']['start'],$config['rentTimestamps']['end']);

    $this->Item_model->getFiltered($config);
    $this->reply['itemCache']=array();
    foreach($items['entries'] AS &$i){
      $this->reply['itemCache'][$i['item_id']]=$i;
    }
    
  }
  
  function validateExpiration(){
    $datePattern='/^20(1[8-9]|[2-9][0-9])-(0[1-9]|1[0-2])-([0-2][0-9]|3[0-1])$/';
    $timePattern='/^([0-1]{1}[0-9]|2[0-3]):([0-5][0-9])$/';
    
    $expirationDate=preg_replace('[^0-9\-]','',$_POST['expiration_date']);
    $expirationDateParts=explode('-',$expirationDate);
    if (!empty($_POST['delivery_time'])){
      $expirationTime=preg_replace('[^0-9:]','',$_POST['expiration_time']);
    }
    else {
      $expirationTime='14:00';
    }

    if (!preg_match($datePattern,$expirationDate)){
      $this->error($this->lang->phrase('expiration_date_should_be_valid'));
    }
    if (!preg_match($timePattern,$expirationTime)){
      $this->error($this->lang->phrase('expiration_time_should_be_valid'));
    }

    if ($this->hasErrors()){
      $this->returnJSON();
    }

    $expirationTimeParts=explode(':',$expirationTime);
    $this->expirationTimestamp=mktime($expirationTimeParts[0],$expirationTimeParts[1],0,intval($expirationDateParts[1]),intval($expirationDateParts[2]),intval($expirationDateParts[0]));

    
    if ($this->expirationTimestamp<(MAIN_TIMESTAMP+MINIMAL_QUOTE_TIME_PREFIX) && 2===1){
      $this->error($this->lang->phrase('expiration_should_be_after').' '.date('Y-m-d H:i',(MAIN_TIMESTAMP+MINIMAL_QUOTE_TIME_PREFIX)),false,true);
    }
    
  }
  
  function acceptCustomer(){
    $this->validateCustomerDetails();
    $this->validateResidentialAddress();
  }
  
  function acceptDelivery(){
    $this->validateDeliveryAddress();
  }

  function validateCustomerDetails(){
    if (empty($_POST['customer'])){
      $this->error('Customer should be set',false,true);
    }
    $_POST['customer']['user_id']=intval($_POST['customer_id']);
    $this->load->model('User_model');
    $validationRules=array('partial_name','phone_or_email','skip_password','update_company_name');
    $this->customerID=$this->reply['data']['customer_id']=$this->User_model->save($_POST['customer'],$validationRules,array('target'=>'customer'));
    if ($this->hasErrors()){
      $this->returnReply();
    }
  }
  
  function validateCustomerDetails______(){
    $customerID=intval($_POST['customer_id']);
    $result=array();
    


    if (!preg_match("/^[a-zA-Z`']{2,}$/", $_POST['customer']['first_name'])){
      $this->error('Customer first name should be set');//,'customer[first_name]');
    }
    if (!empty($_POST['customer']['last_name'])){
      if (!preg_match('/^[a-zA-Z`\']{2,}$/', $_POST['customer']['last_name'])){
        $this->error('Customer last name should be set sgsdgsgsg');//,'customer[last_name]');
      }
      $result['last_name']=trim($_POST['customer']['last_name']);
    }
    $result['first_name']=$_POST['customer']['first_name'];


    if (empty($_POST['customer']['phone']) && empty($_POST['customer']['email'])){
      $this->error('Customer phone or email should be set');//,'customer[email]');
    }
    else {
      $validFields=array();
      $email=trim($_POST['customer']['email']);
      if (preg_match('/^[a-zA-Z0-9\._]+(\+[a-zA-Z0-9\._]+)?@[a-zA-Z0-9_]+(\.[a-zA-Z]{2,8}){1,2}$/', $email)){
        $validFields['email']=$email;

        $this->load->model('User_model');
        if (!$this->User_model->emailCanBeAdded($email,$customerID)){
          $this->error('This email can not be added',false,true);
        }
      }
      $phone=preg_replace('/[^0-9]/','',trim($_POST['customer']['phone']));
      if (preg_match('/^(0[0-9])?[0-9]{8}$/',$phone)){
        $validFields['phone']=$phone;
      }

      if (empty($validFields)){
        $this->error('Customer phone or email should be set',false,true);
      }
    }
    $result['company_name']=(!empty($_POST['customer']['company']))?$_POST['customer']['company']:'';
    if (!empty($result['company_name'])){
      $this->customerCompany=$result['company_name'];
    }
    
    $result=array_merge($result,$validFields);
    if (empty($result['email'])){
      //$result['email']=md5(json_encode($result).'|'.microtime(true));
      $result['email']=$this->makeCode().'@rentevent.com.au';
    }

    $this->customerDataSet=$result;

    return $customerID;
  }

  function validateResidentialAddress(){
    $this->newAddresses=array();
    $this->load->model('Address_model');

    $requiredFields=array('line_1','city','state','postcode','phone');

    $initialResidentialAddressID=intval($_POST['residential_address_id']);
    
    $residentialAddressID=$this->Address_model->errorContainer('residential_address')
      ->validate($initialResidentialAddressID,'residential',$requiredFields,(!empty($_POST['residential_address'])?array_merge(
        $_POST['residential_address']
        ,array('type'=>'residential')
      ):false));

    if ($residentialAddressID>0){
      $this->reply['data']['residential_address_id']=$residentialAddressID;
      if ($initialResidentialAddressID!=$residentialAddressID){
        $this->newAddresses[]=$residentialAddressID;
      }
    }
    else {
      $this->reply['data']['residential_address']=$this->Address_model->dataToValidate;
      //$this->reply['data']['failed_section']='residential_address';
    }
  }

  function validateDeliveryAddress(){
    $this->newAddresses=array();
    $this->load->model('Address_model');

    $requiredFields=array('line_1','line_2','city','state','postcode');//,'phone');

    $initialDeliveryAddressID=intval($_POST['delivery_address_id']);
    
    $deliveryAddressID=$this->Address_model->errorContainer('delivery_address')
      ->validate($initialDeliveryAddressID,'delivery',$requiredFields,(!empty($_POST['delivery_address'])?array_merge(
        $_POST['delivery_address']
        ,array('type'=>'delivery')
      ):false));

    if ($deliveryAddressID>0){
      $this->reply['data']['delivery_address_id']=$deliveryAddressID;
      if ($initialDeliveryAddressID!=$deliveryAddressID){
        $this->newAddresses[]=$deliveryAddressID;
      }
    }
    else {
      $this->reply['data']['delivery_address']=$this->Address_model->dataToValidate;
      //$this->reply['data']['failed_section']='delivery_address';
    }
  }/**/

  function validateAddresses(){
    $this->load->model('Address_model');
    
    $this->newAddresses=array();

    $residentialAddressID=intval($_POST['residential_address_id']);
    if ($_POST['residential_address_id']!='skip'){
      if ($residentialAddressID==0){
        $residentialAddressID=$this->Address_model->save(array_merge(
          $_POST['residential_address']
          ,array('type'=>'residential')
        ),true,array('line_1','city'));
        $this->newAddresses[]=$residentialAddressID;
      }
    }
    
    $deliveryAddressID=intval($_POST['delivery_address_id']);
    if ($_POST['delivery_address_id']=='residential'){
      $deliveryAddressID=$residentialAddressID;
    }
    elseif ($_POST['delivery_address_id']!='skip'){ 
      if ($deliveryAddressID==0){
        $deliveryAddressID = $this->Address_model->save(array_merge($_POST['delivery_address'],array('type'=>'delivery')),true,array('line_1','city'));
        $this->newAddresses[]=$deliveryAddressID;
      }
    }
    
    if (!empty($this->newAddresses)){
      
    }
    if ($this->hasErrors()){
      $this->returnJSON();
    }

    $deliveryContact=array();
    if (isset($_POST['delivery_contact'])){
      $fields=array('venue','name','phone','email');
      foreach($fields AS $f){
        $dataPortion=trim($_POST['delivery_contact'][$f]);
        if ($dataPortion!=''){
          $deliveryContact[$f]=$dataPortion;
        }
      }
    }
    
    return array('residential_address_id'=>$residentialAddressID,
    'delivery_address_id'=>$deliveryAddressID,'delivery_contact_json'=>json_encode($deliveryContact));
  }

  function prepareVariations(){
    $this->newSimpleQuantities=array();
    $this->newItemLocks=array();
    
    $simpleQuantities=array();
    $checkedItems=array();
    $packages=array();
    if (empty($_POST['variation'])){
      $this->error('Variations should be set',false,true);
    }
    $this->log('variation found');
    foreach($_POST['variation'] AS $variationIndex=>$variationData){
      $simpleQuantities[$variationIndex]=array();
      $additionalData[$variationIndex]=array();
      if (empty($variationData['entries'])){
        continue;
      }
      if (($variationData['purchase_order']==1 || $variationData['deposit_value']==0) && !$this->customerCompany){
        $this->error('missing_customer_company',null,true);
      }
      foreach($variationData['entries'] AS $entryIndex){
        if ($variationData[$entryIndex]['quantity']==0 && $variationData[$entryIndex]['type']!='regularItem'){
          continue;
        }
        switch ($variationData[$entryIndex]['type']){
          case 'regularItem':
            $itemID=$variationData[$entryIndex]['item_id'];
            if (empty($checkedItems[$itemID])){
              $packages[$itemID]=$this->db
                ->select('ip.*')
                ->join('item_package AS ip','ip.item_package_id=i.item_id')
                ->get_where('item AS i',array('i.item_id'=>$itemID))->result_array();
              $checkedItems[$itemID]=1;
            }
            if (!empty($packages[$itemID])){
              foreach($packages[$itemID] AS $packedItem){
                if (!isset($simpleQuantities[$variationIndex][$packedItem['item_id']])){
                  $simpleQuantities[$variationIndex][$packedItem['item_id']]=0;
                }
                $simpleQuantities[$variationIndex][$packedItem['item_id']]+=$packedItem['quantity']*$variationData[$entryIndex]['quantity'];
                if (
                  !isset($this->newSimpleQuantities[$packedItem['item_id']]) 
                  || $simpleQuantities[$variationIndex][$packedItem['item_id']]>$this->newSimpleQuantities[$packedItem['item_id']]
                ){
                  $this->newSimpleQuantities[$packedItem['item_id']]=$simpleQuantities[$variationIndex][$packedItem['item_id']];
                }
              }
            }
            else {
              if (!isset($simpleQuantities[$variationIndex][$itemID])){
                $simpleQuantities[$variationIndex][$itemID]=0;
              }
              $simpleQuantities[$variationIndex][$itemID]+=$variationData[$entryIndex]['quantity'];
              if (
                !isset($this->newSimpleQuantities[$itemID]) 
                || $simpleQuantities[$variationIndex][$itemID]>$this->newSimpleQuantities[$itemID]
              ){
                $this->newSimpleQuantities[$itemID]=$simpleQuantities[$variationIndex][$itemID];
              }
            }
          break;
          case 'additionalItem':
            $additionalData[$variationIndex][]=$variationData[$entryIndex];
          break;
          case 'service':
            $additionalData[$variationIndex][]=$variationData[$entryIndex];
          break;
        }
      }
    }
    
    
    $this->reply['data']['simple_quantities']=$simpleQuantities;
    $this->reply['data']['max_simple_quantities']=$this->newSimpleQuantities;
    $this->reply['data']['new_item_locks']=&$this->newItemLocks;
    
    if (!empty($this->newSimpleQuantities)){

      $this->load->model('Item_model');

      $this->reply['data']['items']=array();
      $config=array('rentTimestamps'=>$this->rentTimestamps['final'],'replyData'=>& $this->reply['data']['items'],'onlyChosen'=>array());
      if ($this->quoteID>0){
        $config['lockHash']=$this->ItemLock_model->unlock('quote',$this->quoteID,$this->rentTimestamps['final']['start'],$this->rentTimestamps['final']['end'],true);
      }

      foreach($this->newSimpleQuantities AS $simpleItemID=>$quantity){
        $config['onlyChosen'][]=intval($simpleItemID);
      }

      if (!$this->hasErrors()){
        $this->Item_model->getFiltered($config);
        $availableQuantity=0;
        foreach($this->reply['data']['items']['entries'] AS $item){
          $availableQuantity=(($item['fixed_quantity']!==null)?$item['fixed_quantity']:$item['quantity'])-$item['booked'];
          if ($availableQuantity<$this->newSimpleQuantities[$item['item_id']]){
            $this->error('Overlap for '.$item['title']);
          }
        }
        if (!$this->hasErrors()){
          foreach($this->newSimpleQuantities AS $simpleItemID=>$quantity){
            $this->ItemLock_model->add($simpleItemID,$quantity,$config['rentTimestamps']['start'],$config['rentTimestamps']['end'],$this->newItemLocks);
          }
        }
        
        if ($this->quoteID>0){
          $config['lockHash']=$this->ItemLock_model->unlock('quote',$this->quoteID,$this->rentTimestamps['final']['start'],$this->rentTimestamps['final']['end'],true);
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
    if (!$this->hasErrors()){
      foreach($_POST['variation'] AS $variationIndex=>$variationData){
        if (!empty($variationData['entries'])){
          if ($variationData['quote_variation_id']==0){
            $newQuoteVariationDataSet=array('quote_id'=>$this->quoteID,'code'=>$this->makeCode());
            $this->preparePaymentOptions($variationData,$newQuoteVariationDataSet);
            $this->db->insert('quote_variation',$newQuoteVariationDataSet);
            $variationData['quote_variation_id']=$this->db->insert_id();
          }
          $this->db->delete('quote_variation_item',array('quote_variation_id'=>$variationData['quote_variation_id']));
          $this->variationIDs[]=$variationData['quote_variation_id'];
          $entries=array();
          foreach($variationData['entries'] AS $entryID){
            if ($variationData[$entryID]['quantity']>0){
              if ($variationData[$entryID]['type']=='regularItem'){
                $this->db->insert('quote_variation_item',array(
                  'quote_variation_id'=>$variationData['quote_variation_id']
                  ,'item_id'=>$variationData[$entryID]['item_id']
                  ,'quantity'=>$variationData[$entryID]['quantity']
                  ,'unit_price'=>$variationData[$entryID]['price']
                  ,'discount_type'=>$variationData[$entryID]['discount_type']
                  ,'discount_value'=>$variationData[$entryID]['discount_value']
                ));
              }
              else {
                $entries[]=$variationData[$entryID];
              }
            }
          }
          $variationNotes=trim($variationData['notes']);
          $existingQuoteVariationDataSet=array(
            'name'=>$this->security->xss_clean($variationData['name'])
            ,'discount_type'=>$variationData['discount_type']
            ,'discount_value'=>$variationData['discount_value']
            ,'entries_json'=>json_encode($entries)
            ,'notes'=>(($variationNotes!='')?$variationNotes:NULL)
          );
          $this->preparePaymentOptions($variationData, $existingQuoteVariationDataSet);
          $this->db->where('quote_variation_id',$variationData['quote_variation_id'])
            ->update('quote_variation',$existingQuoteVariationDataSet);
        }
      }
    }
    if (!$this->hasErrors()){
      if (empty($this->variationIDs)){
        $this->error('Variation entries should be set');
      }
    }
  }
  

  
  function prepareTemplate(){
    
  }
  
  function loadView($code=false){
    $quoteID=($code)?($this->db->get_where('quote',array('code'=>$code),1)->row_array()['quote_id']):$_REQUEST['quote_id'];
    $this->quoteID=$quoteID;
    
    $this->load->model('Item_model');
    $this->load->model('Address_model');
    
    $this->loadDetails($quoteID);
    if (empty($this->reply['data']['code'])){
      $this->db->where('quote_id',$this->quoteID)->update('quote',array('code'=>$this->makeCode()));
    }
    
    if ($this->reply['data']['residential_address_id']>0){
      $this->reply['data']['residential_address']=$this->Address_model->get($this->reply['data']['residential_address_id'],true);
    }
    else {
      $this->reply['data']['residential_address']='To be advised';
    }
    if ($this->reply['data']['delivery_address_id']>0){
      $this->reply['data']['delivery_address']=$this->Address_model->get($this->reply['data']['delivery_address_id'],true);
    }
    else {
      $this->reply['data']['delivery_address']='To be advised';
    }

    foreach($this->reply['data']['customer'] AS $field=>$value){
      $this->reply['data']['customer_'.$field]=$value;
    }
    
    $this->reply['data']['variation_count']=count($this->reply['data']['variations']);
    
    $templateContainer='basic';
    $templateTypes=array('quote','variation','item','service');
    $templates=array();
    foreach($templateTypes AS $tt){
      //$templates[$tt]=file_get_contents(APPPATH.'/views/quote/output/'.$templateContainer.'/'.$tt.'.php');
      $templates[$tt]=file_get_contents(APPPATH.'../quote_template/'.$tt.'.php');
    }
    
    $variations=array();
    
    $usedPatterns=array();
    
    $this->load->model('QuoteVariation_model');
    foreach($this->reply['data']['variations'] AS &$v){
      $this->QuoteVariation_model->parseDetails($v);
      
      
      $items=array();
      if (!empty($v['entries']['items'])){
        foreach($v['entries']['items'] AS $itemEntry){
          $patterns=array();
          $replacements=array();
          foreach($itemEntry AS $field=>$value){
            $patterns[]='{'.strtoupper($field).'}';
            $replacements[]=$value;
          }
          $items[]=str_replace($patterns,$replacements,$templates['item']);
        }
        $usedPatterns['item']=$patterns;
      }
      
      $services=array();
      if (!empty($v['entries']['services'])){
        foreach($v['entries']['services'] AS $serviceEntry){
          $patterns=array('{HOST}');
          $replacements=array(NS_BASE_URL);
          foreach($serviceEntry AS $field=>$value){
            $patterns[]='{'.strtoupper($field).'}';
            $replacements[]=$value;
          }
          $services[]=str_replace($patterns,$replacements,$templates['service']);
        }
        $usedPatterns['service']=$patterns;
      }
      
      $v['items']=join('',$items);
      $v['item_data_flag']=(!empty($items))?'Some':'No';
      $v['services']=join('',$services);
      $v['service_data_flag']=(!empty($services))?'Some':'No';
      
      $v['due_datetime']=date('jS M Y H:i',($this->reply['data']['collection_timestamp']+$v['due_days']*86400));
      $v['approval_link']=NS_BASE_URL.'booking/'.$v['code'];
      $v['discussion_link']='mailto:'.ADMIN_EMAIL.'?subject=Revise%20Quote%20'.$v['code'].'&body=Hello,I%20would%20like%20to%20revise%20quote%20'.$v['code'];
      $patterns=array('{HOST}');
      $replacements=array(NS_BASE_URL);
      foreach($v AS $field=>$value){
        //echo $field.'=>'.$value."<br/>\n";
        if (!is_array($value)){
          $patterns[]='{'.strtoupper($field).'}';
          $replacements[]=$value;
        }
      }
      $usedPatterns['variation']=$patterns;
      
      $variations[]=str_replace($patterns,$replacements,$templates['variation']);
    }
    
    $this->reply['data']['date_today']=date('jS M Y');
    
    $this->reply['data']['variations']=join('',$variations);
    $patterns=array('{HOST}');
    $replacements=array(NS_BASE_URL);
    foreach($this->reply['data'] AS $field=>$value){
      if (!is_array($value) && !is_object($value)){
        $patterns[]='{'.strtoupper($field).'}';
        $replacements[]=$value;
      }
    }
    $usedPatterns['quote']=$patterns;
    $final=str_replace($patterns,$replacements,$templates['quote']);
    //$final.='<h3>Available patterns:</h3><pre>'.print_r($usedPatterns,true).'</pre>';
    echo $final;
    //echo '<div id="quoteJSON">'.json_encode($this->reply['data']).'</div>';
    //require_once(APPPATH.'views/quote/view.php');
    //
    //$this->setTemplate('backend','quote/view');
    //$this->makePDF($final);
  }
  
  function load($code=false){
    if (!$code && isset($_POST['code'])){
      $code=$_POST['code'];
    }
    if (REQUEST_TYPE!='JSON'){
      return $this->loadView($code);
    }
    
    $quoteID=($code)?($this->db->get_where('quote',array('code'=>$code),1)->row_array()['quote_id']):$_REQUEST['quote_id'];
    $this->quoteID=$quoteID;
    
    $this->load->model('Item_model');
    $this->load->model('Address_model');
    
    $this->loadDetails($quoteID);
    if (empty($this->reply['data']['code'])){
      $this->db->where('quote_id',$this->quoteID)->update('quote',array('code'=>$this->makeCode()));
    }
    
    if ($this->reply['data']['residential_address_id']>0){
      $this->reply['data']['residential_address']=$this->Address_model->get($this->reply['data']['residential_address_id'],false);
    }
    else {
      $this->reply['data']['residential_address']='To be advised';
    }
    if ($this->reply['data']['delivery_address_id']>0){
      $this->reply['data']['delivery_address']=$this->Address_model->get($this->reply['data']['delivery_address_id'],false);
    }
    else {
      $this->reply['data']['delivery_address']='To be advised';
    }

    foreach($this->reply['data']['customer'] AS $field=>$value){
      $this->reply['data']['customer_'.$field]=$value;
    }
    
    $this->reply['data']['variation_count']=count($this->reply['data']['variations']);
    
    $templateContainer='basic';
    $templateTypes=array('quote','variation','item','service');
    $templates=array();
    foreach($templateTypes AS $tt){
      //$templates[$tt]=file_get_contents(APPPATH.'/views/quote/output/'.$templateContainer.'/'.$tt.'.php');
      $templates[$tt]=file_get_contents(APPPATH.'../quote_template/'.$tt.'.php');
    }
    
    $variations=array();
    
    $usedPatterns=array();
    
    $this->load->model('QuoteVariation_model');
    foreach($this->reply['data']['variations'] AS &$v){
      $this->QuoteVariation_model->parseDetails($v);
 
      $items=array();
      if (!empty($v['entries']['items'])){
        foreach($v['entries']['items'] AS $itemEntry){
          $patterns=array();
          $replacements=array();
          foreach($itemEntry AS $field=>$value){
            $patterns[]='{'.strtoupper($field).'}';
            $replacements[]=$value;
          }
          $items[]=str_replace($patterns,$replacements,$templates['item']);
        }
        $usedPatterns['item']=$patterns;
      }
      
      $services=array();
      if (!empty($v['entries']['services'])){
        foreach($v['entries']['services'] AS $serviceEntry){
          $patterns=array('{HOST}');
          $replacements=array(NS_BASE_URL);
          foreach($serviceEntry AS $field=>$value){
            $patterns[]='{'.strtoupper($field).'}';
            $replacements[]=$value;
          }
          $services[]=str_replace($patterns,$replacements,$templates['service']);
        }
        $usedPatterns['service']=$patterns;
      }
      
      $v['items']=join('',$items);
      $v['item_data_flag']=(!empty($items))?'Some':'No';
      $v['services']=join('',$services);
      $v['service_data_flag']=(!empty($services))?'Some':'No';

      $v['item_data_flag']=(!empty($v['entries']['items']))?'Some':'No';
      $v['service_data_flag']=(!empty($v['entries']['services']))?'Some':'No';
      $v['due_datetime']=date('jS M Y H:i',($this->reply['data']['collection_timestamp']+$v['due_days']*86400));
      $v['approval_link']=NS_BASE_URL.'booking/'.$v['code'];
      $v['discussion_link']='mailto:'.ADMIN_EMAIL.'?subject=Revise%20Quote%20'.$v['code'].'&body=Hello,I%20would%20like%20to%20revise%20quote%20'.$v['code'];
  
      $patterns=array('{HOST}');
      $replacements=array(NS_BASE_URL);
      foreach($v AS $field=>$value){
        //echo $field.'=>'.$value."<br/>\n";
        if (!is_array($value)){
          $patterns[]='{'.strtoupper($field).'}';
          $replacements[]=$value;
        }
      }
      $usedPatterns['variation']=$patterns;
      
      $variations[]=str_replace($patterns,$replacements,$templates['variation']);

    }
    
    $this->reply['data']['date_today']=date('jS M Y');
    
    return $this->returnJSON();
    
    $this->reply['data']['variations']=join('',$variations);
    $patterns=array('{HOST}');
    $replacements=array(NS_BASE_URL);
    foreach($this->reply['data'] AS $field=>$value){
      if (!is_array($value) && !is_object($value)){
        $patterns[]='{'.strtoupper($field).'}';
        $replacements[]=$value;
      }
    }
    $usedPatterns['quote']=$patterns;
    $final=str_replace($patterns,$replacements,$templates['quote']);
    //$final.='<h3>Available patterns:</h3><pre>'.print_r($usedPatterns,true).'</pre>';
    echo $final;
    //echo '<div id="quoteJSON">'.json_encode($this->reply['data']).'</div>';
    //require_once(APPPATH.'views/quote/view.php');
    //
    //$this->setTemplate('backend','quote/view');
    //$this->makePDF($final);
  }
  
  function makePDF($content){
    $this->load->library('tcpdf');
    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    $pdf->SetTitle('QUOTE');
    $pdf->SetMargins(0, 0, 0);

    $pdf->SetHeaderMargin(0);
    $pdf->SetTopMargin(0);
    $pdf->SetFooterMargin(0);
    $pdf->SetAutoPageBreak(true);
    
    $pdf->addPage();
    
  	$bMargin = $pdf->getBreakMargin();

		//$pdf->SetAutoPageBreak(true, 0);
		//$pdf->setPageMark();
    $pdf->writeHTML($content, true, false, true, false, '');
    
    
    $quoteFile='uploads/quote_'.$this->quoteID.'.pdf';
    $pdf->Output();
    //$pdf->Output(APPPATH.'../'.$quoteFile, 'F');
  }
  
}

?>