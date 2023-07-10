<?php

/**
 * @name OmcController.php
 */
App::import('Controller', 'OmcApp');

class OmcOrdersController extends OmcAppController
{
    # Controller name

    var $name = 'OmcOrders';
    # set the model to use
    var $uses = array('Omc','OmcBdcDistribution', 'OmcCustomer','BdcOmc','Bdc','Order','OmcCustomerOrder','Depot','ProductType','BdcDistribution','Volume','Waybill','FreightRate','DeliveryLocation');

    # Set the layout to use
    var $layout = 'omc_layout';

    var $permissions = array(
        'index' => '*',
        'orders' => '*',
        'export_orders' =>'*',
        'customer_orders' =>array('Marketing','Finance')
    );

    # Bdc ids this user will work with only
    var $user_bdc_ids = array();

    public function beforeFilter($param_array = null)
    {
       // $this->Auth->authorize = 'controller';
        parent::beforeFilter();

    }


    function index()
    {
        //$this->redirect('orders');

    }


    function orders($type = 'get')
    {
        $permissions = $this->action_permission;
        if ($this->request->is('ajax')) {
            $this->autoRender = false;
            $this->autoLayout = false;
            $authUser = $this->Auth->user();
            $company_profile = $this->global_company;
            switch ($type) {
                case 'get' :
                    /**  Get posted data */
                    $page = isset($_POST['page']) ? $_POST['page'] : 1;
                    /** The current page */
                    $sortname = isset($_POST['sortname']) ? $_POST['sortname'] : 'id';
                    /** Sort column */
                    $sortorder = isset($_POST['sortorder']) ? $_POST['sortorder'] : 'desc';
                    /** Sort order */
                    $qtype = isset($_POST['qtype']) ? $_POST['qtype'] : '';
                    /** Search column */
                    $search_query = isset($_POST['query']) ? $_POST['query'] : '';
                    /** @var $filter  */
                    $filter =   isset($_POST['filter']) ? $_POST['filter'] : 0 ;
                    /** @var $filter  */
                    $filter_status =   isset($_POST['filter_status']) ? $_POST['filter_status'] : 'incomplete_orders' ;
                    /** Search string */
                    $rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
                    $limit = $rp;
                    $start = ($page - 1) * $rp;

                    $condition_array = array(
                        'Order.omc_id' => $company_profile['id'],
                        'Order.record_origin'=>'manual',//Orders created manually
                        'Order.deleted' => 'n'
                    );
                    if($filter != 0){
                        $condition_array['Order.bdc_id'] = $filter;
                    }
                    if($filter_status == 'incomplete_orders'){
                        $condition_array['NOT'] = array('Order.order_status'=>'Complete');
                    }
                    else{
                        $condition_array['Order.order_status'] = 'Complete';
                    }

                    if (!empty($search_query)) {
                        if ($qtype == 'id') {
                            $condition_array['Order.id'] = $search_query;
                        }
                        else {
                            /* $condition_array = array(
                                 "User.$qtype LIKE" => $search_query . '%',
                                 'User.deleted' => 'n'
                             );*/
                        }
                    }

                    $contain = array(
                        'Bdc'=>array('fields' => array('Bdc.id', 'Bdc.name')),
                        'Depot'=>array('fields' => array('Depot.id', 'Depot.name')),
                        'ProductType'=>array('fields' => array('ProductType.id', 'ProductType.name')),
                        'OmcCustomer'=>array('fields' => array('OmcCustomer.id', 'OmcCustomer.name'))
                    );
                    // $fields = array('User.id', 'User.username', 'User.first_name', 'User.last_name', 'User.group_id', 'User.active');
                    $data_table = $this->Order->find('all', array('conditions' => $condition_array, 'contain'=>$contain,'order' => "Order.$sortname $sortorder", 'limit' => $start . ',' . $limit, 'recursive' => 1));
                    $data_table_count = $this->Order->find('count', array('conditions' => $condition_array, 'recursive' => -1));

                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {
                            $bigger_time = date('Y-m-d H:i:s');
                            if($obj['Order']['order_status'] == 'Complete'){
                                $bigger_time = $obj['Order']['bdc_modified'];
                                if(!$bigger_time){
                                    if($company_profile['available'] != 'Available'){
                                        $bigger_time = $obj['Order']['omc_created'];
                                        if($obj['Order']['omc_modified']){
                                            $bigger_time = $obj['Order']['omc_modified'];
                                        }
                                    }
                                    else{
                                        $bigger_time = $obj['Order']['omc_modified'];
                                    }
                                }

                                $time_hr = $this->count_time_between_dates($obj['Order']['omc_created'],$bigger_time,'hours');
                               // $time_days = $this->count_time_between_dates($obj['Order']['omc_created'],$bigger_time,'days');
                                $order_time_elapsed = $time_hr.' hr(s)';
                            }
                            else{
                                $time_hr = $this->count_time_between_dates($obj['Order']['omc_created'],$bigger_time,'hours');
                               // $time_days = $this->count_time_between_dates($obj['Order']['omc_created'],$bigger_time,'days');
                                $order_time_elapsed =  $time_hr.' hr(s)';
                            }

                            //If Orders are being processed by BDC, Omc can't edit that order, unless that order is still blue (New which must be edited with higher clearance right)
                            $st = $obj['Order']['order_status'];
                            $edit_row = $obj['Order']['edit_row'];
                            //debug($st);
                            if($st == 'New' || $st == 'New From Dealer'){

                            }
                            else{
                                $edit_row = 'no';
                            }
                            //debug($edit_row);

                            $return_arr[] = array(
                                'id' => $obj['Order']['id'],
                                'cell' => array(
                                    $obj['Order']['id'],
                                    $this->covertDate($obj['Order']['order_date'],'mysql_flip'),
                                    //$obj['Order']['omc_order_priority'],
                                    $order_time_elapsed,
                                    $obj['OmcCustomer']['name'],

                                    $obj['Depot']['name'],
                                    $obj['ProductType']['name'],
                                    $this->formatNumber( $obj['Order']['order_quantity'],'money',0),
                                    $obj['Bdc']['name'],
                                    /*$this->mkt_feedback[$obj['Order']['delivery_priority']],*/
                                   // $this->ops_feedback[$obj['Order']['bdc_feedback']],
                                   // $this->fna_feedback[$obj['Order']['finance_approval']],
                                  //  $obj['Order']['approved_quantity']
                                ),
                                'extra_data' => array(//Sometime u need certain data to be stored on the main tr at the client side like the referencing table id for editing
                                    'record_origin'=>$obj['Order']['record_origin'],
                                    'order_status'=>$obj['Order']['order_status'],
                                    'product_type_id'=>$obj['ProductType']['id'],
                                    'product_type_name'=>$obj['ProductType']['name']
                                ),
                                'property'=>array(
                                    'bg_color'=>$obj['Order']['row_bg_color'],
                                    'edit_row'=> $edit_row
                                )
                            );
                        }
                        return json_encode(array('success' => true, 'total' => $total_records, 'page' => $page, 'rows' => $return_arr));
                    }
                    else {
                        return json_encode(array('success' => false, 'total' => $total_records, 'page' => $page, 'rows' => array()));
                    }

                    break;

                case 'save' :
                    if ($_POST['id'] == 0) {//Mew
                        if(!in_array('A',$permissions)){
                            return json_encode(array('code' => 1, 'msg' => 'Access Denied.'));
                        }
                    }
                    else{
                        if(!in_array('E',$permissions)){
                            return json_encode(array('code' => 1, 'msg' => 'Access Denied.'));
                        }
                    }
                    $data = array('Order' => $_POST);
                    $data['Order']['order_date'] = $this->covertDate($_POST['order_date'],'mysql').' '.date('H:i:s');
                    if($_POST['id']== 0){//New Manual Entry
                        $data['Order']['omc_created_by'] = $authUser['id'];
                        $data['Order']['order_status'] = 'New';
                        $data['Order']['row_bg_color'] = 'tr_blue';
                        $data['Order']['record_origin'] = 'manual';
                        $data['Order']['omc_created'] = date('Y-m-d H:i:s');
                        $data['Order']['omc_id'] =  $company_profile['id'];
                        $data['Order']['record_type'] = 'bdc';
                        if($company_profile['available'] != 'Available'){
                            $data['Order']['record_type'] = 'omc';
                            $data['Order']['order_status'] = 'Pending Loading';
                            $data['Order']['row_bg_color'] = 'tr_green';
                        }
                    }
                    else{// Might be correcting error or a record from customers order.
                        $data['Order']['omc_modified'] = date('Y-m-d H:i:s');
                        $data['Order']['omc_modified_by'] = $authUser['id'];
                       /* if($_POST['extra']['record_origin'] == 'customer_order' && $_POST['extra']['order_status'] == 'New From Dealer'){
                            $data['Order']['order_status'] = 'New';
                            $data['Order']['row_bg_color'] = 'tr_blue';
                            if($company_profile['available'] != 'Available'){
                                $data['Order']['record_type'] = 'omc';
                                $data['Order']['order_status'] = 'Pending Loading';
                                $data['Order']['row_bg_color'] = 'tr_green';
                            }
                        }
                        elseif($_POST['extra']['record_origin'] == 'manual' && $_POST['extra']['order_status'] == 'New'){
                            //May be of need
                        }*/
                    }

                    if ($this->Order->save($this->sanitize($data))) {
                        $order_id  = $this->Order->id;

                        /** After Creating or Editing a new order, send the order is automatically sent for allocation   ** */

                        $send_notification = false;
                        if($_POST['id']== 0){//if it is a new manual record, then send notification
                            $send_notification = true;
                        }
                        else{
                            $record_origin = isset($_POST['record_origin'])? $_POST['record_origin'] : '';
                            if($record_origin == 'customer_order'){
                                $order_status = isset($_POST['order_status'])? $_POST['order_status'] : '';
                                if($order_status == 'New'){//It means after saving the record the order status has now changed to Complete, so send the message
                                    $send_notification = true;
                                }
                            }
                        }

                        //Activity Log
                        $log_description = $this->getLogMessage('NewOrder')." Order id = $order_id";
                        $this->logActivity('Login',$log_description);

                        if($_POST['id'] > 0){
                            return json_encode(array('code' => 0, 'msg' => 'Data Updated!'));
                        }
                        else{
                            return json_encode(array('code' => 0, 'msg' => 'Data Saved', 'id'=>$this->Order->id));
                        }
                    } else {
                        echo json_encode(array('code' => 1, 'msg' => 'Some errors occurred.'));
                    }
                    //echo debug($data);
                    break;

                case 'load':

                    break;

                case 'delete':
                    if(!in_array('D',$permissions)){
                        return json_encode(array('code' => 1, 'msg' => 'Access Denied.'));
                    }
                    $ids = $_POST['ids'];
                    $modObj = ClassRegistry::init('Order');
                    $result = $modObj->updateAll(
                        $this->sanitize(array('Order.deleted' => "'y'")),
                        $this->sanitize(array('Order.id' => $ids))
                    );
                    if ($result) {
                        echo json_encode(array('code' => 0, 'msg' => 'Data Deleted!'));
                    } else {
                        echo json_encode(array('code' => 1, 'msg' => 'Data cannot be deleted'));
                    }
                    break;
            }
        }

        $company_profile = $this->global_company;
        $products_lists = $this->get_products();
        $depot_lists = $this->get_depot_list();
        $omc_customers_lists = $this->get_customer_list();
        //Get Bdcs for this Omc
        $bdc_list = $bdclists_data = $this->get_bdc_list();
        $start_dt = date('01-m-Y');
        $end_dt = date('t-m-Y');
        $group_by = 'monthly';
        $group_by_title = date('F');

        $bdclists =array(array('name'=>'All','value'=>0));
        $bdc_depots  =array();
        foreach($bdclists_data as $arr){
            $bdclists[] = array('name'=>$arr['name'],'value'=>$arr['id']);
            $bdc_depots[$arr['id']] = $arr;
        }

        $order_filter = $this->order_filter;

        $g_data =  $this->get_orders($start_dt,$end_dt,$group_by,null);

        $graph_title = $group_by_title.", Orders-Consolidated";

        $volumes = $this->Volume->getVolsList();

        $this->set(compact('grid_data','omc_customers_lists','depot_lists', 'products_lists','bdc_list','graph_title','g_data','bdclists','order_filter','bdc_depots','volumes'));
    }



    function export_orders(){
        $download = false;
        $company_profile = $this->global_company;
        if($this->request->is('post')){
            $export_params = $this->request->data;
            $start_dt = $this->covertDate($export_params['exp_startdt'],'mysql').' 00:00:00';
            $end_dt =  $this->covertDate($export_params['exp_enddt'],'mysql').' 23:59:59';
            $export_filter_bdc = $export_params['exp_filter_bdc'];
            $export_filter_status = isset($export_params['exp_filter_status'])? $export_params['exp_filter_status'] : 'complete_orders';
            $conditions = array(
                'Order.omc_id' => $company_profile['id'],
                'Order.deleted' => 'n',
                'Order.order_date >=' => $start_dt, 'Order.order_date <=' => $end_dt
            );
            if($export_filter_bdc != 0){
                $conditions['Order.bdc_id'] = $export_filter_bdc;
            }
            if(isset($export_params['exp_filter_status'])){
                if($export_filter_status == 'incomplete_orders'){
                    $conditions['NOT'] = array('Order.order_status'=>'Complete');
                }
                else{
                    $conditions['Order.order_status'] = 'Complete';
                }
            }

            $contain = array(
                'Bdc'=>array('fields' => array('Bdc.id', 'Bdc.name')),
                'Depot'=>array('fields' => array('Depot.id', 'Depot.name')),
                'ProductType'=>array('fields' => array('ProductType.id', 'ProductType.name')),
                'OmcCustomer'=>array('fields' => array('OmcCustomer.id', 'OmcCustomer.name'))
            );

            $export_data = $this->Order->find('all', array(
                //'fields'=>array('Order.id','Order.loading_date','Order.waybill_date','Order.quantity','BdcDistribution.waybill_id','BdcDistribution.vehicle_no'),
                'conditions' => $conditions,
                'contain'=>$contain,
                'order' => array("Order.id"=>'desc'),
                'recursive' => 1
            ));

            if ($export_data) {
                $download = true;
                $list_data = array();
                foreach ($export_data as $obj) {
                    $delivery_priority = isset($this->mkt_feedback[$obj['Order']['delivery_priority']])? $this->mkt_feedback[$obj['Order']['delivery_priority']] : '';
                    $bdc_feedback = isset($this->ops_feedback[$obj['Order']['bdc_feedback']])? $this->ops_feedback[$obj['Order']['bdc_feedback']] : '';
                    $finance_approval = isset($this->fna_feedback[$obj['Order']['finance_approval']])? $this->fna_feedback[$obj['Order']['finance_approval']] : '';
                    $status = $obj['Order']['order_status'];
                    if($status != 'Complete'){
                        $status = 'In Complete';
                    }
                    $list_data[] = array(
                        $obj['Order']['id'],
                        $this->covertDate($obj['Order']['order_date'],'mysql_flip'),
                        $obj['OmcCustomer']['name'],
                        $obj['Bdc']['name'],
                        $obj['Depot']['name'],
                        $obj['ProductType']['name'],
                        preg_replace('/,/','',$obj['Order']['order_quantity']),
                        $obj['Order']['truck_no'],
                        $status,
                        $delivery_priority,
                        $bdc_feedback,
                        $finance_approval,
                         preg_replace('/,/','',$obj['Order']['approved_quantity']),
                    );
                }
                $list_headers = array('Order Id','Order Date','Customer','BDC','Loading Depot','Product Type','Order Quantity','Truck No.','Status','Delivery Priority','BDC Feedback','BDC Finance Approval','Approved Quantity');
                //$list_headers = array('Date','Waybill No.','From','Depot','Product Type','Actual Quantity','Vehicle No.','Customer Name','Quantity Delivered','Delivery Location','Region','District');
                $filename = $company_profile['name']." Orders ".date('Ymdhis');
                $res = $this->convertToExcel($list_headers,$list_data,$filename);
                $objPHPExcel = $res['excel_obj'];
                $filename = $res['filename'];
            }

        }

        $this->autoLayout = false;

        $this->set(compact('objPHPExcel', 'download', 'filename'));
    }


    function order_allocation($type = 'get')
    {
        $permissions = $this->action_permission;
        $my_bdc_list = $this->get_bdc_omc_list();//Bdc this Omc is connected with on this system
        $my_bdc_list_ids = array();
        foreach($my_bdc_list as $arr){
            $my_bdc_list_ids[] = $arr['id'];
        }

        if ($this->request->is('ajax')) {
            $this->autoRender = false;
            $this->autoLayout = false;
            $authUser = $this->Auth->user();

            $company_profile = $this->global_company;
            switch ($type) {
                case 'get' :
                    /**  Get posted data */
                    $page = isset($_POST['page']) ? $_POST['page'] : 1;
                    /** The current page */
                    $sortname = isset($_POST['sortname']) ? $_POST['sortname'] : 'id';
                    /** Sort column */
                    $sortorder = isset($_POST['sortorder']) ? $_POST['sortorder'] : 'desc';
                    /** Sort order */
                    $qtype = isset($_POST['qtype']) ? $_POST['qtype'] : '';
                    /** Search column */
                    $search_query = isset($_POST['query']) ? $_POST['query'] : '';
                    /** @var $filter  */
                    $filter =   isset($_POST['filter']) ? $_POST['filter'] : 0 ;
                    /** @var $filter  */
                    $filter_status =   isset($_POST['filter_status']) ? $_POST['filter_status'] : 'incomplete_orders' ;
                    /** Search string */
                    $rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
                    $limit = $rp;
                    $start = ($page - 1) * $rp;

                    $group_depot = ClassRegistry::init('User')->getDepotGroup($authUser['id']);
                    $condition_array = array(
                        'Order.omc_id' => $company_profile['id'],
                        'Order.deleted' => 'n'
                    );
                    if($group_depot > 0){
                        $condition_array['Order.depot_id'] = $group_depot;
                    }
                    if($filter != 0){
                        $condition_array['Order.bdc_id'] = $filter;
                    }
                    if($filter_status == 'incomplete_orders'){
                        $condition_array['NOT'] = array('Order.order_status'=>'Complete');
                    }
                    else{
                        $condition_array['Order.order_status'] = 'Complete';
                    }

                    if (!empty($search_query)) {
                        if ($qtype == 'id') {
                            $condition_array['Order.id'] = $search_query;
                        }
                        else {
                            /* $condition_array = array(
                                 "User.$qtype LIKE" => $search_query . '%',
                                 'User.deleted' => 'n'
                             );*/
                        }
                    }

                    $contain = array(
                        'Bdc'=>array('fields' => array('Bdc.id', 'Bdc.name')),
                        'Depot'=>array('fields' => array('Depot.id', 'Depot.name')),
                        'ProductType'=>array('fields' => array('ProductType.id', 'ProductType.name')),
                        'OmcCustomer'=>array('fields' => array('OmcCustomer.id', 'OmcCustomer.name'))
                    );
                    // $fields = array('User.id', 'User.username', 'User.first_name', 'User.last_name', 'User.group_id', 'User.active');
                    $data_table = $this->Order->find('all', array('conditions' => $condition_array, 'contain'=>$contain,'order' => "Order.$sortname $sortorder", 'limit' => $start . ',' . $limit, 'recursive' => 1));
                    $data_table_count = $this->Order->find('count', array('conditions' => $condition_array, 'recursive' => -1));

                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {
                            $bigger_time = date('Y-m-d H:i:s');
                            if($obj['Order']['order_status'] == 'Complete'){
                                $bigger_time = $obj['Order']['bdc_modified'];
                                if(!$bigger_time){
                                    if($company_profile['available'] != 'Available'){
                                        $bigger_time = $obj['Order']['omc_created'];
                                        if($obj['Order']['omc_modified']){
                                            $bigger_time = $obj['Order']['omc_modified'];
                                        }
                                    }
                                    else{
                                        $bigger_time = $obj['Order']['omc_modified'];
                                    }
                                }

                                $time_hr = $this->count_time_between_dates($obj['Order']['omc_created'],$bigger_time,'hours');
                                // $time_days = $this->count_time_between_dates($obj['Order']['omc_created'],$bigger_time,'days');
                                $order_time_elapsed = $time_hr.' hr(s)';
                            }
                            else{
                                $time_hr = $this->count_time_between_dates($obj['Order']['omc_created'],$bigger_time,'hours');
                                // $time_days = $this->count_time_between_dates($obj['Order']['omc_created'],$bigger_time,'days');
                                $order_time_elapsed =  $time_hr.' hr(s)';
                            }

                            //If Orders are being processed by BDC, Omc can't edit that order, unless that order is still blue (New which must be edited with higher clearance right)
                            $st = $obj['Order']['order_status'];
                            $edit_row = $obj['Order']['edit_row'];
                            //debug($st);
                            if($st == 'New' || $st == 'New From Dealer'){

                            }
                            else{
                                $edit_row = 'no';
                            }
                            //debug($edit_row);
                            $ops_feed = isset($this->ops_feedback[$obj['Order']['bdc_feedback']]) ? $this->ops_feedback[$obj['Order']['bdc_feedback']] : '';
                            //$fna_feed = isset($this->fna_feedback[$obj['Order']['finance_approval']]) ? $this->fna_feedback[$obj['Order']['finance_approval']] : '';
                            $mkt_feed = isset($this->mkt_feedback[$obj['Order']['delivery_priority']]) ? $this->mkt_feedback[$obj['Order']['delivery_priority']] : '';
                            $loaded_date = '';
                            if($obj['Order']['loaded_date']){
                                $loaded_date = $this->covertDate($obj['Order']['loaded_date'],'mysql_flip');
                            }
                            $approved_quantity = '';
                            if($obj['Order']['approved_quantity']){
                                $approved_quantity = $this->formatNumber($obj['Order']['approved_quantity'],'money',0);
                            }
                            $loaded_quantity = '';
                            if($obj['Order']['loaded_quantity']){
                                $loaded_quantity = $this->formatNumber($obj['Order']['loaded_quantity'],'money',0);
                            }
                            $return_arr[] = array(
                                'id' => $obj['Order']['id'],
                                'cell' => array(
                                    $obj['Order']['id'],
                                    $this->covertDate($obj['Order']['order_date'],'mysql_flip'),
                                    //$obj['Order']['omc_order_priority'],
                                    $order_time_elapsed,
                                    $obj['OmcCustomer']['name'],
                                    $obj['Depot']['name'],
                                    $obj['ProductType']['name'],
                                    $this->formatNumber($obj['Order']['order_quantity'],'money',0),
                                    /*,$mkt_feed*/
                                    $obj['Order']['transporter'],
                                    $obj['Order']['truck_no'],
                                    $obj['Bdc']['name'],
                                    $approved_quantity,
                                    $loaded_quantity,
                                    $loaded_date,
                                    $ops_feed
                                   // $fna_feed,

                                ),
                                'extra_data' => array(//Sometime u need certain data to be stored on the main tr at the client side like the referencing table id for editing
                                    'record_origin'=>$obj['Order']['record_origin'],
                                    'order_status'=>$obj['Order']['order_status'],
                                    'product_type_id'=>$obj['ProductType']['id'],
                                    'product_type_name'=>$obj['ProductType']['name'],
                                    'depot_id'=>$obj['Depot']['id'],
                                    'depot_name'=>$obj['Depot']['name']
                                ),
                                'property'=>array(
                                    'bg_color'=>$obj['Order']['row_bg_color'],
                                    'edit_row'=> $edit_row
                                )
                            );
                        }
                        return json_encode(array('success' => true, 'total' => $total_records, 'page' => $page, 'rows' => $return_arr));
                    }
                    else {
                        return json_encode(array('success' => false, 'total' => $total_records, 'page' => $page, 'rows' => array()));
                    }

                    break;

                case 'save' :
                    if(!in_array('E',$permissions)){
                        return json_encode(array('code' => 1, 'msg' => 'Access Denied.'));
                    }
                    $auto_flow = false;
                    $bdc_id = $_POST['bdc_id'];
                    $data = array('Order' => $_POST);
                    /*$data['Order']['order_date'] = $this->covertDate($_POST['order_date'],'mysql').' '.date('H:i:s');*/
                    $data['Order']['omc_modified'] = date('Y-m-d H:i:s');
                    $data['Order']['omc_modified_by'] = $authUser['id'];
                    $data['Order']['edit_row'] = 'no'; //
                    unset( $data['Order']['extra']);
                    if(in_array($bdc_id,$my_bdc_list_ids)){
                        unset( $data['Order']['approved_quantity']);
                        unset( $data['Order']['loaded_quantity']);
                        unset( $data['Order']['loaded_date']);
                        $data['Order']['record_type'] = 'bdc'; //BDC must respond to this order
                        $auto_flow = false;
                    }
                    else{
                        $data['Order']['row_bg_color'] = 'tr_green';
                        $data['Order']['order_status'] = 'Complete';
                        $data['Order']['record_type'] = 'omc'; // omc is not connected with this bdc
                        $data['Order']['depot_loadding_approval'] = 'Loaded';
                        $data['Order']['loaded_date'] = $this->covertDate($_POST['loaded_date'],'mysql').' '.date('H:i:s');
                        $auto_flow = true;
                    }

                    if ($this->Order->save($this->sanitize($data))) {
                        $order_id  = $this->Order->id;
                        if($auto_flow){
                            $order_data = $this->Order->find('first', array(
                                'conditions' => array('Order.id' => $order_id),
                                'recursive' => -1
                            ));
                            $order  = $order_data['Order'];
                            //This Order needs no BDC input so quickly approve his orders and push to distribution
                            $order['waybill_date'] = '';
                            $order['waybill_id'] = '';
                            //Push to BDC distribution
                            $this->BdcDistribution->addDistribution($order,'omc',$authUser['id']);
                            $order['bdc_distribution_id'] = $this->BdcDistribution->id;
                            //Push to OMC distribution
                            $this->OmcBdcDistribution->addDistribution($order);
                        }
                        else{
                            /** Notify BDC **/
                            $send_params = array(
                                'title'=>$company_profile['name'].', Order Allocation',
                                'content'=>'New order = '.$data['Order']['id'].' has been allocated to you.',
                                'sender'=>$authUser['id'],
                                'sender_type'=>'blast',
                                'entity'=>'bdc',
                                'entity_id'=>$data['Order']['bdc_id'],
                                'include_this_entity'=>false,
                                'msg_type'=>'system'
                            );
                            $this->sendMessage($send_params);
                        }
                        //Activity Log
                        $log_description = $this->getLogMessage('AllocateOrder');
                        $this->logActivity('Order',$log_description);

                        if($_POST['id'] > 0){
                            return json_encode(array('code' => 0, 'msg' => 'Data Updated!'));
                        }
                        else{
                            return json_encode(array('code' => 0, 'msg' => 'Data Saved', 'id'=>$this->Order->id));
                        }
                    } else {
                        echo json_encode(array('code' => 1, 'msg' => 'Some errors occured.'));
                    }
                    //echo debug($data);
                    break;

                case 'load':

                    break;

                case 'delete':
                    if(!in_array('D',$permissions)){
                        return json_encode(array('code' => 1, 'msg' => 'Access Denied.'));
                    }
                    $ids = $_POST['ids'];
                    $modObj = ClassRegistry::init('Order');
                    $result = $modObj->updateAll(
                        $this->sanitize(array('Order.deleted' => "'y'")),
                        $this->sanitize(array('Order.id' => $ids))
                    );
                    if ($result) {
                        echo json_encode(array('code' => 0, 'msg' => 'Data Deleted!'));
                    } else {
                        echo json_encode(array('code' => 1, 'msg' => 'Data cannot be deleted'));
                    }
                    break;
            }
        }


        $products_lists = $this->get_products();
        $depot_lists = $this->get_depot_list();
        $omc_customers_lists = $this->get_customer_list();

        $bdc_list = $bdclists_data = $this->get_bdc_list();
        $start_dt = date('01-m-Y');
        $end_dt = date('t-m-Y');
        $group_by = 'monthly';
        $group_by_title = date('F');

        $bdclists =array(array('name'=>'All','value'=>0));
        $bdc_depots  =array();
        foreach($bdclists_data as $arr){
            $bdclists[] = array('name'=>$arr['name'],'value'=>$arr['id']);
            $bdc_depots[$arr['id']] = $arr;
        }

        $order_filter = $this->order_filter;

        $g_data =  $this->get_orders($start_dt,$end_dt,$group_by,null);

        $graph_title = $group_by_title.", Orders-Consolidated";

        $volumes = $this->Volume->getVolsList();

        $this->set(compact('grid_data','omc_customers_lists','depot_lists', 'products_lists','bdc_list','graph_title','g_data','bdclists','order_filter','bdc_depots','volumes','my_bdc_list_ids'));
    }


    function customer_orders($type = 'get')
    {
        if ($this->request->is('ajax')) {
            $this->autoRender = false;
            $this->autoLayout = false;
            $authUser = $this->Auth->user();
            $company_profile = $this->global_company;
            switch ($type) {
                case 'get' :
                    /**  Get posted data */
                    $page = isset($_POST['page']) ? $_POST['page'] : 1;
                    /** The current page */
                    $sortname = isset($_POST['sortname']) ? $_POST['sortname'] : 'id';
                    /** Sort column */
                    $sortorder = isset($_POST['sortorder']) ? $_POST['sortorder'] : 'desc';
                    /** Sort order */
                    $qtype = isset($_POST['qtype']) ? $_POST['qtype'] : '';
                    /** Search column */
                    $search_query = isset($_POST['query']) ? $_POST['query'] : '';
                    $filter_customer =   isset($_POST['filter_customer']) ? $_POST['filter_customer'] : 0 ;
                    /** @var $filter  */
                    $filter_status =   isset($_POST['filter_status']) ? $_POST['filter_status'] : 'incomplete_orders' ;
                    /** Search string */
                    $rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
                    $limit = $rp;
                    //$start = ($page - 1) * $rp;

                    //get users id for this company only
                    $condition_array = array(
                        'OmcCustomerOrder.omc_id' => $company_profile['id'],
                        'OmcCustomerOrder.deleted' => 'n'
                    );
                    if($filter_customer != 0){
                        $condition_array['OmcCustomerOrder.omc_customer_id'] = $filter_customer;
                    }
                    if($filter_status == 'incomplete_orders'){
                        $condition_array['NOT'] = array('OmcCustomerOrder.order_status'=>'Complete');
                    }
                    else{
                        $condition_array['OmcCustomerOrder.order_status'] = 'Complete';
                    }

                    if (!empty($search_query)) {
                        if ($qtype == 'id') {
                            $condition_array['OmcCustomerOrder.id'] = $search_query;
                        }
                        else {
                            /* $condition_array = array(
                                 "User.$qtype LIKE" => $search_query . '%',
                                 'User.deleted' => 'n'
                             );*/
                        }
                    }

                    $contain = array(
                        'Bdc'=>array('fields' => array('Bdc.id', 'Bdc.name')),
                        'Depot'=>array('fields' => array('Depot.id', 'Depot.name')),
                        'Omc'=>array('fields' => array('Omc.id', 'Omc.name')),
                        'ProductType'=>array('fields' => array('ProductType.id', 'ProductType.name')),
                        'OmcCustomer'=>array('fields' => array('OmcCustomer.id', 'OmcCustomer.name','OmcCustomer.credit_limit','OmcCustomer.credit_days'))
                    );
                    // $fields = array('User.id', 'User.username', 'User.first_name', 'User.last_name', 'User.group_id', 'User.active');
                    $data_table = $this->OmcCustomerOrder->find('all', array(
						'conditions' => $condition_array,
						'contain'=>$contain,
						'order' => "OmcCustomerOrder.$sortname $sortorder",
						'limit' => $limit,
						'page' => $page,
						'recursive' => 1));
                    $data_table_count = $this->OmcCustomerOrder->find('count', array('conditions' => $condition_array, 'recursive' => -1));

                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {
                            $bigger_time = date('Y-m-d H:i:s');
                            if($obj['OmcCustomerOrder']['order_status'] == 'Complete'){
                                $bigger_time = $obj['OmcCustomerOrder']['omc_modified'];
                                $time_hr = $this->count_time_between_dates($obj['OmcCustomerOrder']['dealer_created'],$bigger_time,'hours');
                                // $time_days = $this->count_time_between_dates($obj['Order']['omc_created'],$bigger_time,'days');
                                $order_time_elapsed = $time_hr.' hr(s)';
                            }
                            else{
                                $time_hr = $this->count_time_between_dates($obj['OmcCustomerOrder']['dealer_created'],$bigger_time,'hours');
                                // $time_days = $this->count_time_between_dates($obj['Order']['omc_created'],$bigger_time,'days');
                                $order_time_elapsed =  $time_hr.' hr(s)';
                            }

                            //If Orders are being processed by BDC, Omc can't edit that order, unless that order is still blue (New which must be edited with higher clearance right)
                            $st = $obj['OmcCustomerOrder']['order_status'];
                            $edit_row = $obj['OmcCustomerOrder']['edit_row'];
                            /*if($st != 'New'){
                                $edit_row = 'no';
                            }*/

                            $depot_id =  $obj['Depot']['id'];
                            $depot_name = '';
                            if($depot_id > 0){
                                $depot_name = $obj['Depot']['name'];
                            }
                            $approve = isset($this->omc_dealer_feedback[$obj['OmcCustomerOrder']['finance_approval']])? $this->omc_dealer_feedback[$obj['OmcCustomerOrder']['finance_approval']] :'';
                            $marketing_feed = isset($this->omc_dealer_marketing_feedback[$obj['OmcCustomerOrder']['delivery_priority']])? $this->omc_dealer_marketing_feedback[$obj['OmcCustomerOrder']['delivery_priority']]:'';
                            $return_arr[] = array(
                                'id' => $obj['OmcCustomerOrder']['id'],
                                'cell' => array(
                                    $obj['OmcCustomerOrder']['id'],
                                    $this->covertDate($obj['OmcCustomerOrder']['order_date'],'mysql_flip'),
                                    //$order_time_elapsed,
                                    $obj['OmcCustomer']['name'],
                                   // $obj['OmcCustomer']['credit_limit'],
                                    $obj['ProductType']['name'],
                                    $this->formatNumber($obj['OmcCustomerOrder']['order_quantity'],'money',0),
                                    //$obj['Bdc']['name'],
                                    $marketing_feed,
                                    $approve,
                                    $depot_name,
                                    $obj['OmcCustomerOrder']['intended_delivery_location']
                                ),
                                'extra_data' => array(//Sometime u need certain data to be stored on the main tr at the client side like the referencing table id for editing
                                    'order_status'=>$obj['OmcCustomerOrder']['order_status']
                                ),
                                'property'=>array(
                                    'bg_color'=>$obj['OmcCustomerOrder']['row_bg_color'],
                                    'edit_row'=> $edit_row
                                )
                            );
                        }
                        return json_encode(array('success' => true, 'total' => $total_records, 'page' => $page, 'rows' => $return_arr));
                    }
                    else {
                        return json_encode(array('success' => false, 'total' => $total_records, 'page' => $page, 'rows' => array(), 'query_result'=>$data_table));
                    }

                    break;

                case 'save' :
                    $data = array('OmcCustomerOrder' => $_POST);

                    $data['OmcCustomerOrder']['omc_modified'] = date('Y-m-d H:i:s');
                    $data['OmcCustomerOrder']['omc_modified_by'] = $authUser['id'];

                    $send_to_main_order = false;

                    if($_POST['finance_approval'] == 'Approved'){
                        $data['OmcCustomerOrder']['row_bg_color'] = 'tr_green';
                        $data['OmcCustomerOrder']['order_status'] = 'Pending Loading';
                        $data['OmcCustomerOrder']['edit_row'] = 'no';
                        $send_to_main_order = true;
                    }
                    else{
                        $data['OmcCustomerOrder']['row_bg_color'] = 'tr_red';
                        $data['OmcCustomerOrder']['order_status'] = 'Cancelled';
                        $data['OmcCustomerOrder']['edit_row'] = 'no';
                        $send_to_main_order = false;
                    }

                    if ($this->OmcCustomerOrder->save($this->sanitize($data))) {
                        //$order_id  = $this->OmcCustomerOrder->id;
                        $order_id = $_POST['id'];

                        if($send_to_main_order){
                            //Flow to bdc omc orders
                            $order = $this->Order->find('first', array(
                                'fields' => array('Order.id'),
                                'conditions' => array('Order.omc_customer_order_id' => $order_id),
                                'recursive' => -1
                            ));

                            if(!$order){
                                $order_customer = $this->OmcCustomerOrder->find('first', array(
                                    'conditions' => array('OmcCustomerOrder.id' => $order_id),
                                    'recursive' => -1
                                ));
                                $order_data = array('Order'=>array(
                                    'order_date' => date('Y-m-d H:i:s'),
                                    //'bdc_id'=>$order_customer['OmcCustomerOrder']['bdc_id'],
                                    'omc_id'=>$order_customer['OmcCustomerOrder']['omc_id'],
                                    'omc_customer_id'=>$order_customer['OmcCustomerOrder']['omc_customer_id'],
                                    'depot_id'=>$order_customer['OmcCustomerOrder']['depot_id'],
                                    'product_type_id'=>$order_customer['OmcCustomerOrder']['product_type_id'],
                                    'order_quantity'=>$order_customer['OmcCustomerOrder']['order_quantity'],
                                    'omc_customer_order_id'=>$order_customer['OmcCustomerOrder']['id'],
                                    'omc_order_priority'=>$order_customer['OmcCustomerOrder']['delivery_priority'],
                                    'order_status'=>'New From Dealer',
                                    'row_bg_color' => 'tr_blue',
                                    'record_type'=>'bdc',
                                    'record_origin'=>'customer_order',
                                    'omc_created_by' => $authUser['id'],
                                    'omc_created' => date('Y-m-d H:i:s')
                                ));
                                if($company_profile['available'] != 'Available'){
                                    $order_data['Order']['record_type'] = 'omc';
                                }

                                $this->Order->save($this->sanitize($order_data));

                                //Activity Log
                                $log_description = $this->getLogMessage('ApproveCustomerOrder')." (Customer Order #".$order_id.")";
                                $this->logActivity('Order',$log_description);

                              //Send message
                            }
                        }

                        if($_POST['id'] > 0){
                            return json_encode(array('code' => 0, 'msg' => 'Data Updated!'));
                        }
                        else{
                            return json_encode(array('code' => 0, 'msg' => 'Data Saved', 'id'=>$this->OmcCustomerOrder->id));
                        }
                    } else {
                        echo json_encode(array('code' => 1, 'msg' => 'Some errors occurred.'));
                    }
                    //echo debug($data);
                    break;

                case 'load':

                    break;

                case 'delete':
                    $ids = $_POST['ids'];
                    $modObj = ClassRegistry::init('OmcCustomerOrder');
                    $result = $modObj->updateAll(
                        $this->sanitize(array('OmcCustomerOrder.deleted' => "'y'")),
                        $this->sanitize(array('OmcCustomerOrder.id' => $ids))
                    );
                    if ($result) {
                        echo json_encode(array('code' => 0, 'msg' => 'Data Deleted!'));
                    } else {
                        echo json_encode(array('code' => 1, 'msg' => 'Data cannot be deleted'));
                    }
                    break;
            }
        }

        $company_profile = $this->global_company;
        $products_lists = $this->get_product_list();
        $depot_lists = $this->get_depot_list();
        array_unshift($depot_lists,array('id'=>0,'name'=>''));
        $omc_customers_data = $this->get_customer_list();
        $omc_customers_lists =array(array('name'=>'All','value'=>0));
        foreach($omc_customers_data as $arr){
            $omc_customers_lists[] = array('name'=>$arr['name'],'value'=>$arr['id']);
        }
        /* $depot_lists = $this->get_depot_list();

         //Get Bdcs for this Omc
         $bdc_list = $this->get_bdc_list('crm');
         $bdclists_data = $this->get_bdc_list();*/
        $start_dt = date('01-m-Y');
        $end_dt = date('t-m-Y');
        $group_by = 'monthly';
        $group_by_title = date('F');

        /* $bdclists =array(array('name'=>'All','value'=>0));
         foreach($bdclists_data as $arr){
             $bdclists[] = array('name'=>$arr['name'],'value'=>$arr['id']);
         }*/
        $omc_dealer_feedback = array();
        foreach($this->omc_dealer_feedback as $key => $value){
            $omc_dealer_feedback[] = array(
                'id'=>$key,
                'name'=>$value
            );
        }

        $omc_dealer_marketing_feedback  = array();
        foreach($this->omc_dealer_marketing_feedback as $key => $value){
            $omc_dealer_marketing_feedback[] = array(
                'id'=>$key,
                'name'=>$value
            );
        }

        $order_filter = $this->order_filter;

        $g_data =  $this->get_orders($start_dt,$end_dt,$group_by,null);

        $graph_title = $group_by_title.", Orders-Consolidated";

        $this->set(compact('grid_data','omc_customers_lists', 'products_lists','depot_lists','bdc_list','graph_title','g_data','bdclists','order_filter','omc_dealer_feedback','omc_dealer_marketing_feedback'));
    }



    function customer_orders_marketing($type = 'get')
    {
        if ($this->request->is('ajax')) {
            $this->autoRender = false;
            $this->autoLayout = false;
            $authUser = $this->Auth->user();
            $company_profile = $this->global_company;
            switch ($type) {
                case 'get' :
                    /**  Get posted data */
                    $page = isset($_POST['page']) ? $_POST['page'] : 1;
                    /** The current page */
                    $sortname = isset($_POST['sortname']) ? $_POST['sortname'] : 'id';
                    /** Sort column */
                    $sortorder = isset($_POST['sortorder']) ? $_POST['sortorder'] : 'desc';
                    /** Sort order */
                    $qtype = isset($_POST['qtype']) ? $_POST['qtype'] : '';
                    /** Search column */
                    $search_query = isset($_POST['query']) ? $_POST['query'] : '';
                    $filter_customer =   isset($_POST['filter_customer']) ? $_POST['filter_customer'] : 0 ;
                    /** @var $filter  */
                    $filter_status =   isset($_POST['filter_status']) ? $_POST['filter_status'] : 'incomplete_orders' ;
                    /** Search string */
                    $rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
                    $limit = $rp;
                    $start = ($page - 1) * $rp;

                    //get users id for this company only
                    $condition_array = array(
                        'OmcCustomerOrder.omc_id' => $company_profile['id'],
                        'OmcCustomerOrder.deleted' => 'n'
                    );
                    if($filter_customer != 0){
                        $condition_array['OmcCustomerOrder.omc_customer_id'] = $filter_customer;
                    }
                    if($filter_status == 'incomplete_orders'){
                        $condition_array['NOT'] = array('OmcCustomerOrder.order_status'=>'Complete');
                    }
                    else{
                        $condition_array['OmcCustomerOrder.order_status'] = 'Complete';
                    }

                    if (!empty($search_query)) {
                        if ($qtype == 'id') {
                            $condition_array['OmcCustomerOrder.id'] = $search_query;
                        }
                        else {
                            /* $condition_array = array(
                                 "User.$qtype LIKE" => $search_query . '%',
                                 'User.deleted' => 'n'
                             );*/
                        }
                    }

                    $contain = array(
                        'Bdc'=>array('fields' => array('Bdc.id', 'Bdc.name')),
                        'Depot'=>array('fields' => array('Depot.id', 'Depot.name')),
                        'Omc'=>array('fields' => array('Omc.id', 'Omc.name')),
                        'ProductType'=>array('fields' => array('ProductType.id', 'ProductType.name')),
                        'OmcCustomer'=>array('fields' => array('OmcCustomer.id', 'OmcCustomer.name','OmcCustomer.credit_limit','OmcCustomer.credit_days'))
                    );
                    // $fields = array('User.id', 'User.username', 'User.first_name', 'User.last_name', 'User.group_id', 'User.active');
                    $data_table = $this->OmcCustomerOrder->find('all', array('conditions' => $condition_array, 'contain'=>$contain,'order' => "OmcCustomerOrder.$sortname $sortorder", 'limit' => $start . ',' . $limit, 'recursive' => 1));
                    $data_table_count = $this->OmcCustomerOrder->find('count', array('conditions' => $condition_array, 'recursive' => -1));

                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {
                            $bigger_time = date('Y-m-d H:i:s');
                            if($obj['OmcCustomerOrder']['order_status'] == 'Complete'){
                                $bigger_time = $obj['OmcCustomerOrder']['omc_modified'];
                                $time_hr = $this->count_time_between_dates($obj['OmcCustomerOrder']['dealer_created'],$bigger_time,'hours');
                                // $time_days = $this->count_time_between_dates($obj['Order']['omc_created'],$bigger_time,'days');
                                $order_time_elapsed = $time_hr.' hr(s)';
                            }
                            else{
                                $time_hr = $this->count_time_between_dates($obj['OmcCustomerOrder']['dealer_created'],$bigger_time,'hours');
                                // $time_days = $this->count_time_between_dates($obj['Order']['omc_created'],$bigger_time,'days');
                                $order_time_elapsed =  $time_hr.' hr(s)';
                            }

                            //If Orders are being processed by BDC, Omc can't edit that order, unless that order is still blue (New which must be edited with higher clearance right)
                            $st = $obj['OmcCustomerOrder']['order_status'];
                            $edit_row = $obj['OmcCustomerOrder']['edit_row'];
                            /*if($st != 'New'){
                                $edit_row = 'no';
                            }*/
                            $priority = isset($this->omc_dealer_marketing_feedback[$obj['OmcCustomerOrder']['delivery_priority']]) ? $this->omc_dealer_marketing_feedback[$obj['OmcCustomerOrder']['delivery_priority']] : '';
                            $fina_feed = isset($this->omc_dealer_feedback[$obj['OmcCustomerOrder']['finance_approval']]) ? $this->omc_dealer_feedback[$obj['OmcCustomerOrder']['finance_approval']] : '';
                            $return_arr[] = array(
                                'id' => $obj['OmcCustomerOrder']['id'],
                                'cell' => array(
                                    $obj['OmcCustomerOrder']['id'],
                                    $this->covertDate($obj['OmcCustomerOrder']['order_date'],'mysql_flip'),
                                    //$order_time_elapsed,
                                    $obj['OmcCustomer']['name'],
                                    $this->formatNumber($obj['OmcCustomer']['credit_limit'],'money',2),
                                    $obj['ProductType']['name'],
                                    $this->formatNumber($obj['OmcCustomerOrder']['order_quantity'],'money',0),
                                    //$obj['Bdc']['name'],
                                    //$obj['Depot']['name'],
                                    $priority,
                                    $fina_feed,
                                    $obj['OmcCustomerOrder']['intended_delivery_location']
                                ),
                                'extra_data' => array(//Sometime u need certain data to be stored on the main tr at the client side like the referencing table id for editing
                                    'order_status'=>$obj['OmcCustomerOrder']['order_status']
                                ),
                                'property'=>array(
                                    'bg_color'=>$obj['OmcCustomerOrder']['row_bg_color']
                                    //'edit_row'=> $edit_row
                                )
                            );
                        }
                        return json_encode(array('success' => true, 'total' => $total_records, 'page' => $page, 'rows' => $return_arr));
                    }
                    else {
                        return json_encode(array('success' => false, 'total' => $total_records, 'page' => $page, 'rows' => array()));
                    }

                    break;

                case 'save' :
                    $data = array('OmcCustomerOrder' => $_POST);
                    $delivery_priority = $_POST['delivery_priority'];
                    $data['OmcCustomerOrder']['omc_modified'] = date('Y-m-d H:i:s');
                    $data['OmcCustomerOrder']['omc_modified_by'] = $authUser['id'];


                    if ($this->OmcCustomerOrder->save($this->sanitize($data))) {
                        //$order_id  = $this->OmcCustomerOrder->id;
                        $order_id = $_POST['id'];

                        //Activity Log
                        $log_description = $this->getLogMessage('PrioritizeCustomerOrder')." (Customer Order #".$order_id." to $delivery_priority)";
                        $this->logActivity('Order',$log_description);

                        if($_POST['id'] > 0){
                            return json_encode(array('code' => 0, 'msg' => 'Data Updated!'));
                        }
                        else{
                            return json_encode(array('code' => 0, 'msg' => 'Data Saved', 'id'=>$this->OmcCustomerOrder->id));
                        }
                    } else {
                        echo json_encode(array('code' => 1, 'msg' => 'Some errors occurred.'));
                    }
                    //echo debug($data);
                    break;
            }
        }

        $company_profile = $this->global_company;
        $products_lists = $this->get_product_list();
        $omc_customers_data = $this->get_customer_list();
        $omc_customers_lists =array(array('name'=>'All','value'=>0));
        foreach($omc_customers_data as $arr){
            $omc_customers_lists[] = array('name'=>$arr['name'],'value'=>$arr['id']);
        }
        /* $depot_lists = $this->get_depot_list();

         //Get Bdcs for this Omc
         $bdc_list = $this->get_bdc_list('crm');
         $bdclists_data = $this->get_bdc_list();*/
        $start_dt = date('01-m-Y');
        $end_dt = date('t-m-Y');
        $group_by = 'monthly';
        $group_by_title = date('F');

        /* $bdclists =array(array('name'=>'All','value'=>0));
         foreach($bdclists_data as $arr){
             $bdclists[] = array('name'=>$arr['name'],'value'=>$arr['id']);
         }*/
        $omc_dealer_feedback = array();
        foreach($this->omc_dealer_feedback as $key => $value){
            $omc_dealer_feedback[] = array(
                'id'=>$key,
                'name'=>$value
            );
        }

        $omc_dealer_marketing_feedback  = array();
        foreach($this->omc_dealer_marketing_feedback as $key => $value){
            $omc_dealer_marketing_feedback[] = array(
                'id'=>$key,
                'name'=>$value
            );
        }

        $order_filter = $this->order_filter;

        $g_data =  $this->get_orders($start_dt,$end_dt,$group_by,null);

        $graph_title = $group_by_title.", Orders-Consolidated";

        $this->set(compact('grid_data','omc_customers_lists','depot_lists', 'products_lists','bdc_list','graph_title','g_data','bdclists','order_filter','omc_dealer_feedback','omc_dealer_marketing_feedback'));
    }


    function distributions($type = 'get')
    {
        $company_profile = $this->global_company;
        $permissions = $this->action_permission;
        if ($this->request->is('ajax')) {
            $this->autoRender = false;
            $this->autoLayout = false;
            $authUser = $this->Auth->user();
            switch ($type) {
                case 'get' :
                    /**  Get posted data */
                    $page = isset($_POST['page']) ? $_POST['page'] : 1;
                    /** The current page */
                    $sortname = isset($_POST['sortname']) ? $_POST['sortname'] : 'id';
                    /** Sort column */
                    $sortorder = isset($_POST['sortorder']) ? $_POST['sortorder'] : 'desc';
                    /** Sort order */
                    $qtype = isset($_POST['qtype']) ? $_POST['qtype'] : '';
                    /** Search column */
                    $search_query = isset($_POST['query']) ? $_POST['query'] : '';
                    /** Search string */
                    $rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
                    $limit = $rp;
                    $start = ($page - 1) * $rp;


                    $condition_array = array('BdcDistribution.omc_id' => $company_profile['id'], 'BdcDistribution.order_status' => 'Complete', 'BdcDistribution.deleted' => 'n');
                    if($company_profile['available'] != 'Available'){
                        $condition_array = array('BdcDistribution.omc_id' => $company_profile['id'],  'BdcDistribution.deleted' => 'n');
                    }

                    if (!empty($search_query)) {
                        if ($qtype == 'username') {
                            /*$condition_array = array(
                                'User.username' => $search_query,
                                'User.deleted' => 'n'
                            );*/
                        }
                        else {
                            /* $condition_array = array(
                                 "User.$qtype LIKE" => $search_query . '%',
                                 'User.deleted' => 'n'
                             );*/
                        }
                    }
                    $contain = array(
                        'Bdc'=>array('fields' => array('Bdc.id', 'Bdc.name')),
                        'Depot'=>array('fields' => array('Depot.id', 'Depot.name')),
                        'ProductType'=>array('fields' => array('ProductType.id', 'ProductType.name')),
                        'District'=>array('fields' => array('District.id', 'District.name')),
                        'Region'=>array('fields' => array('Region.id', 'Region.name')),
                    );
                    // $fields = array('User.id', 'User.username', 'User.first_name', 'User.last_name', 'User.group_id', 'User.active');
                    $data_table = $this->BdcDistribution->find('all', array('conditions' => $condition_array, 'contain'=>$contain,'order' => "BdcDistribution.$sortname $sortorder", 'limit' => $start . ',' . $limit, 'recursive' => 1));
                    $data_table_count = $this->BdcDistribution->find('count', array('conditions' => $condition_array, 'recursive' => -1));

                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {
                            $waybill_date = '';
                            if($obj['BdcDistribution']['waybill_date']){
                                $waybill_date = $this->covertDate($obj['BdcDistribution']['waybill_date'],'mysql_flip');
                            }
                            $to_row = array(
                                'id' => $obj['BdcDistribution']['id'],
                                'cell' => array(
                                    $obj['BdcDistribution']['id'],
                                    $this->covertDate($obj['BdcDistribution']['loading_date'],'mysql_flip'),
                                    $obj['BdcDistribution']['order_id'],
                                    $waybill_date,
                                    $obj['BdcDistribution']['waybill_id'],
                                    $obj['Bdc']['name'],
                                    $obj['Depot']['name'],
                                    $obj['ProductType']['name'],
                                    $this->formatNumber( $obj['BdcDistribution']['quantity'],'money',0),
                                    /*$obj['Region']['name'],
                                    $obj['District']['name'],*/
                                    $obj['BdcDistribution']['vehicle_no']
                                ),
                                'extra_data' => array(//Sometime u need certain data to be stored on the main tr at the client side like the referencing table id for editing
                                    'omc_name'=>$obj['Bdc']['name'],
                                    'record_origin'=>$obj['BdcDistribution']['record_origin'],
                                    'order_status'=>$obj['BdcDistribution']['order_status'],
                                    'order_id'=>$obj['BdcDistribution']['order_id'],
                                    'depot_id'=>$obj['Depot']['id']
                                )
                            );
                            if($company_profile['available'] != 'Available'){
                                $to_row['property']=array(
                                    'bg_color'=>$obj['BdcDistribution']['row_bg_color'],
                                    'edit_row'=> $obj['BdcDistribution']['edit_row']
                                );
                            }
                            $return_arr[] = $to_row;
                        }
                        return json_encode(array('success' => true, 'total' => $total_records, 'page' => $page, 'rows' => $return_arr));
                    }
                    else {
                        return json_encode(array('success' => false, 'total' => $total_records, 'page' => $page, 'rows' => array()));
                    }

                    break;

               /* case 'save' :
                    if ($_POST['id'] == 0) {//Mew
                        if(!in_array('A',$permissions)){
                            return json_encode(array('code' => 1, 'msg' => 'Access Denied.'));
                        }
                    }
                    else{
                        if(!in_array('E',$permissions)){
                            return json_encode(array('code' => 1, 'msg' => 'Access Denied.'));
                        }
                    }
                    $data = array('BdcDistribution' => $_POST);
                    $data['BdcDistribution']['order_status'] = 'Complete'; //whether a new manual record or editing from crm, order is always complete once data is posted
                    $data['BdcDistribution']['row_bg_color'] = ''; //So is row bg_color, what ever it was remove it and make it empty
                    $time_in = date('H:i:s');
                    $data['BdcDistribution']['loading_date'] = $this->covertDate($_POST['loading_date'],'mysql').' '.$time_in;
                    $data['BdcDistribution']['waybill_date'] = $this->covertDate($_POST['waybill_date'],'mysql').' '.$time_in;

                    $record_origin = isset($_POST['extra']['record_origin'])? $_POST['extra']['record_origin'] : 'manual';
                    $order_status = isset($_POST['extra']['order_status'])? $_POST['extra']['order_status'] : 'Pending';
                    $order_id = isset($_POST['extra']['order_id'])? $_POST['extra']['order_id'] : 0;
                    $omc_name = isset($_POST['extra']['omc_name'])? $_POST['extra']['omc_name'] : '';

                    if($_POST['id']== 0){//New Manual Entry
                        $data['BdcDistribution']['record_origin'] = 'manual';
                        $data['BdcDistribution']['omc_id'] = $company_profile['id'];
                        $data['BdcDistribution']['created_by'] = $authUser['id'];
                        $data['BdcDistribution']['record_type'] = 'omc';

                        // $omc_details = $this->Omc->find('first', array(
                            // 'fields' => array('Omc.id', 'Omc.name'),
                            // 'conditions' => array('Omc.id' => $_POST['omc_id']),
                            // 'recursive' => -1
                        // ));
                        // $omc_name = $omc_details['Omc']['name'];
                    }
                    else{// Might be correcting error or a record from CRM.
                        $data['BdcDistribution']['modified_by'] = $authUser['id'];
                    }
                    //$data['BdcDistribution']['edit_row'] = 'no'; //If we want to lock the record after truck load is complete, then enable this part

                    if ($this->BdcDistribution->save($this->sanitize($data))) {
                        //This is where we update the Order record
                        if($record_origin == 'crm'){
                            $order_save = array(
                                'Order'=>array(
                                    'id'=>$order_id,
                                    'row_bg_color'=>'',
                                    'order_status'=>'Complete'
                                )
                            );
                            if($company_profile['available'] != 'Available'){
                                $order_save['Order']['omc_modified'] = date('Y-m-d H:i:s');
                            }
                            $this->Order->save($this->sanitize($order_save));
                            //Flow to omc distribution
                            $omc_distribution = $this->OmcBdcDistribution->find('first', array(
                                'fields' => array('OmcBdcDistribution.id'),
                                'conditions' => array('OmcBdcDistribution.bdc_distribution_id' => $_POST['id']),
                                'recursive' => -1
                            ));

                            if(!$omc_distribution){
                                $bdc_distribution = $this->BdcDistribution->find('first', array(
                                    'fields' => array('BdcDistribution.id','BdcDistribution.quantity'),
                                    'conditions' => array('BdcDistribution.id' => $_POST['id']),
                                    'contain'=>array(
                                        'Order'=>array('fields' => array('Order.id','Order.omc_customer_id'))
                                    ),
                                    'recursive' => 1
                                ));

                                $omc_save = array('OmcBdcDistribution'=>array(
                                    'bdc_distribution_id'=>$_POST['id'],
                                    'omc_customer_id'=>$bdc_distribution['Order']['omc_customer_id'],
                                    'quantity'=>$bdc_distribution['BdcDistribution']['quantity'],
                                    'created_by' => $authUser['id']
                                ));

                                $this->OmcBdcDistribution->save($this->sanitize($omc_save));
                            }
                        }

                        if($_POST['id'] > 0){
                            return json_encode(array('code' => 0, 'msg' => 'Data Updated!'));
                        }
                        else{
                            return json_encode(array('code' => 0, 'msg' => 'Data Saved', 'id'=>$this->BdcDistribution->id));
                        }
                    } else {
                        echo json_encode(array('code' => 1, 'msg' => 'Some errors occurred.'));
                    }
                    //echo debug($data);

                    break;*/

                case 'save-sub' :
                    if ($_POST['id'] == 0) {//Mew
                        if(!in_array('A',$permissions)){
                            return json_encode(array('code' => 1, 'msg' => 'Access Denied.'));
                        }
                    }
                    else{
                        if(!in_array('E',$permissions)){
                            return json_encode(array('code' => 1, 'msg' => 'Access Denied.'));
                        }
                    }

                    $validate_sum = $this->distributionsValidate($_POST['parent_id'],$_POST['quantity'],$_POST['id']);
                    if(!$validate_sum['status']){
                        return json_encode(array('success' => 1, 'msg' => $validate_sum['msg']));
                    }

                    $data = array('OmcBdcDistribution' => $_POST);
                    $data['OmcBdcDistribution']['bdc_distribution_id'] = $_POST['parent_id'];
                    if($_POST['id'] == 0){
                        $data['OmcBdcDistribution']['created_by'] = $authUser['id'];
                    }
                    else{
                        $data['OmcBdcDistribution']['modified_by'] = $authUser['id'];
                    }

                    //Pre Process UPPF
                    $uppf_pre_data = $this->pre_process_uppf($_POST['parent_id'],$_POST['delivery_location_id']);
                    $data['OmcBdcDistribution']['delivery_distance'] = $uppf_pre_data['distance'];
                    $data['OmcBdcDistribution']['freight_rate'] = $uppf_pre_data['rate'];

                    if ($this->OmcBdcDistribution->save($this->sanitize($data))) {
                        //Update Omc_customer order delivery quantity
                        $distribution = $this->BdcDistribution->find('first', array(
                            'fields' => array('BdcDistribution.id','BdcDistribution.order_id','BdcDistribution.record_origin'),
                            'conditions' => array('BdcDistribution.id' => $_POST['parent_id']),
                            'contain' => array(
                                'ProductType'=>array(
                                    'fields' => array('ProductType.id', 'ProductType.name')
                                ),
                                'Order'=>array(
                                    'fields' => array('Order.id', 'Order.omc_customer_order_id','Order.record_origin','Order.truck_no')
                                    // 'OmcCustomerOrder'=>array('fields' => array('Order.id', 'Order.omc_customer_order_id'))
                                ),
                                'OmcBdcDistribution'=>array(
                                    'fields' => array('OmcBdcDistribution.id', 'OmcBdcDistribution.transporter'),
                                    'OmcCustomer'=>array('fields' => array('OmcCustomer.id', 'OmcCustomer.name'))
                                )
                            ),
                            'recursive' => 2
                        ));

                        if($distribution['BdcDistribution']['record_origin']=='crm'){
                            if($distribution['Order']['record_origin']=='customer_order'){
                                //Update
                                $this->OmcCustomerOrder->save($this->sanitize(array(
                                    'id'=>$distribution['Order']['omc_customer_order_id'],
                                    'delivery_quantity'=>$_POST['quantity'],
                                    'truck_number'=>$distribution['Order']['truck_no'],
                                    'driver'=>$distribution['OmcBdcDistribution'][0]['transporter'],
                                    'row_bg_color'=>'',
                                    'order_status'=>'Complete',
                                    'edit_row'=>'no'
                                )));
                            }
                        }
                        $order_id = $distribution['Order']['id'];
                        $product_name = $distribution['ProductType']['name'];
                        $quantity = $this->formatNumber($_POST['quantity'],'money',0);
                        $to_whom  = $distribution['OmcBdcDistribution'][0]['OmcCustomer']['name'];
                        //Activity Log
                        $log_description = $this->getLogMessage('DistributeOrder')." (Order: #".$order_id.", Product: ".$product_name." , Quantity: ".$quantity." To: ".$to_whom.")";
                        $this->logActivity('Order',$log_description);

                        //Send message to dealer if any

                        if($_POST['id'] > 0){
                            return json_encode(array('code' => 0, 'msg' => 'Data Updated!'));
                        }
                        else{
                            return json_encode(array('code' => 0, 'msg' => 'Data Saved!', 'id'=>$this->OmcBdcDistribution->id));
                        }
                        //echo json_encode(array('success' => 0, 'msg' => 'Data Saved'));
                    } else {
                        return json_encode(array('success' => 1, 'msg' => 'Some errors occurred.'));
                    }
                    break;

                case 'load_details':
                    $gdata = $this->OmcBdcDistribution->find('all',array(
                        'conditions'=>array('OmcBdcDistribution.bdc_distribution_id'=>$_POST['id']),
                        'contain' => array(
                            'OmcCustomer'=>array('fields' => array('OmcCustomer.id', 'OmcCustomer.name')),
                            'DeliveryLocation'=>array('fields' => array('DeliveryLocation.id', 'DeliveryLocation.name')),
                            'Region'=>array('fields' => array('Region.id', 'Region.name'))
                        ),
                        'recursive'=>1
                    ));

                    if($gdata){
                        foreach ($gdata as $obj) {
                            $return_arr[] = array(
                                'id' => $obj['OmcBdcDistribution']['id'],
                                'cell' => array(
                                    $obj['OmcBdcDistribution']['invoice_number'],
                                    $obj['OmcCustomer']['name'],
                                    $this->formatNumber($obj['OmcBdcDistribution']['quantity'],'money',0),
                                    $obj['Region']['name'],
                                    $obj['DeliveryLocation']['name'],
                                    $obj['OmcBdcDistribution']['transporter'],
                                    $obj['OmcBdcDistribution']['driver']
                                )
                            );
                        }
                        return json_encode(array('code' => 0, 'rows' => $return_arr));
                    }
                    else {
                        return json_encode(array('code' => 1, 'rows' => array(), 'mesg' => __('No Record Found')));
                    }

                    break;

                case 'delete':

                    break;
            }
        }

        $company_profile = $this->global_company;
        $products_lists = $this->get_products();
        $bdc_depot_lists = $this->get_depot_list();
        $omc_customers_lists = $this->get_customer_list();
        $places_data = $this->get_region_district();
        $bdc_lists = $this->get_bdc_list();
        $glbl_region_district = $places_data['region_district'];
        $regions_lists = $places_data['region'];
        $district_lists = $places_data['district'];
        $delivery_locations = $this->get_delivery_locations();
        $volumes = $this->Volume->getVolsList();

        $this->set(compact('volumes','company_profile', 'omc_customers_lists','bdc_depot_lists', 'bdc_lists','omclists', 'products_lists', 'regions_lists', 'district_lists','glbl_region_district','delivery_locations'));

    }


    function distributionsValidate($distribution_id,$amount,$id){

        $distribution = $this->BdcDistribution->find('first', array(
            'fields' => array('BdcDistribution.id','BdcDistribution.quantity'),
            'conditions' => array('BdcDistribution.id' => $distribution_id),
            'contain' => array(
                'OmcBdcDistribution'=>array('fields' => array('OmcBdcDistribution.id', 'OmcBdcDistribution.quantity')),
            ),
            'recursive' => 1
        ));

        $amount =  doubleval(preg_replace('/,/','',$amount));
        $total = doubleval(preg_replace('/,/','',$distribution['BdcDistribution']['quantity']));
        $q_total = 0;

        if($id == 0){
            foreach($distribution['OmcBdcDistribution'] as $q){
                $q_total = $q_total + doubleval(preg_replace('/,/','',$q['quantity']));
            }
            if($q_total == $total){
                return array(
                    'status'=>false,
                    'msg'=>'Total quantity cannot exceed '.$total
                );
            }
            $bal = $total - $q_total;
            if($amount > $bal){
                return array(
                    'status'=>false,
                    'msg'=>'Total quantity cannot exceed '.$total.' Actual quantity should be '.$bal
                );
            }
        }
        else{
            foreach($distribution['OmcBdcDistribution'] as $q){
                $id_sub = $q['id'];
                $v =  doubleval(preg_replace('/,/','',$q['quantity']));
                if($id == $id_sub){
                    $q_total = $q_total + $amount;
                }
                else{
                    $q_total = $q_total + $v;
                }
            }
            if($q_total > $total){
                return array(
                    'status'=>false,
                    'msg'=>'Total quantity cannot exceed '.$total
                );
            }
        }

        return array(
            'status'=>true,
            'msg'=>'Ok'
        );
    }


    function pre_process_uppf($distribution_id=null,$delivery_location_id=null){
        //Get Distribution Data
        $distribution = $this->BdcDistribution->find('first', array(
            'fields' => array('BdcDistribution.id','BdcDistribution.product_type_id','BdcDistribution.depot_id'),
            'conditions' => array('BdcDistribution.id' => $distribution_id),
            'contain' => array(
                'ProductType'=>array('fields' => array('ProductType.id', 'ProductType.freight_rate_category_id')),
            ),
            'recursive' => 1
        ));
        //Get Distance Data
        $distance_data = $this->DeliveryLocation->find('first', array(
            'fields' => array('DeliveryLocation.id','DeliveryLocation.distance'),
            'conditions' => array('DeliveryLocation.id' => $delivery_location_id),
            'recursive' => -1
        ));
        $r_distance = 0;
        $r_rate = 0;
        if($distance_data){
            //Get Rate Data
            $distance = intval($distance_data['DeliveryLocation']['distance']);
            if($distance < 0){
                $distance = $distance * -1;
            }

            if($distance && $distance > 0){
                $freight_rate = $this->FreightRate->find('first', array(
                    'fields' => array('FreightRate.id','FreightRate.rate'),
                    'conditions' => array('FreightRate.freight_rate_category_id' => $distribution['ProductType']['freight_rate_category_id'],'FreightRate.distance' => $distance),
                    'recursive'=>-1
                ));
                $r_distance = $distance;
                $r_rate = $freight_rate['FreightRate']['rate'];
            }
            else{
                $r_distance = 0;
                $r_rate = 0;
            }
        }
        else{
            $r_distance = 0;
            $r_rate = 0;
        }

        return array(
            'distance'=>$r_distance,
            'rate'=>$r_rate,
        );
    }



    function waybills($type = 'get')
    {
        $permissions = $this->action_permission;
        $company_profile = $this->global_company;
        if ($this->request->is('ajax')) {
            $this->autoRender = false;
            $this->autoLayout = false;
            $authUser = $this->Auth->user();
            switch ($type) {
                case 'get' :
                    /**  Get posted data */
                    $page = isset($_POST['page']) ? $_POST['page'] : 1;
                    /** The current page */
                    $sortname = isset($_POST['sortname']) ? $_POST['sortname'] : 'id';
                    /** Sort column */
                    $sortorder = isset($_POST['sortorder']) ? $_POST['sortorder'] : 'desc';
                    /** Sort order */
                    $qtype = isset($_POST['qtype']) ? $_POST['qtype'] : '';
                    /** Search column */
                    $search_query = isset($_POST['query']) ? $_POST['query'] : '';
                    /** @var $filter  */
                    $filter =   isset($_POST['filter']) ? $_POST['filter'] : 0 ;
                    $filter_status =   isset($_POST['filter_status']) ? $_POST['filter_status'] : 'Not Yet Approved' ;
                    /** Search string */
                    $rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
                    $limit = $rp;
                    $start = ($page - 1) * $rp;

                    $condition_array = array(
                        'Waybill.omc_id' => $company_profile['id'],
                        'Waybill.deleted' => 'n'
                    );

                    if($filter != 0){
                        $condition_array['Waybill.depot_id'] = $filter;
                    }

                    if($filter_status == 'Not Yet Approved'){
                       // $condition_array['Waybill.bdc_approval'] = 'Not Yet Approved';
                    }
                    else{
                       // $condition_array['Waybill.bdc_approval'] = $filter_status;
                    }


                    if (!empty($search_query)) {
                        if ($qtype == 'id') {
                            $condition_array['Waybill.id'] = $search_query;
                        }
                        else {
                            $condition_array['Waybill.'.$qtype] = $search_query;
                        }
                    }

                    $contain = array(
                        'Bdc'=>array('fields' => array('Bdc.id', 'Bdc.name')),
                        'Omc'=>array('fields' => array('Omc.id', 'Omc.name')),
                        'Cep'=>array('fields' => array('Cep.id', 'Cep.name')),
                        'Depot'=>array('fields' => array('Depot.id', 'Depot.name')),
                        'Order'=>array(
                            'fields' => array('Order.id', 'Order.order_date','Order.loaded_quantity','Order.loaded_date','Order.truck_no'),
                            'ProductType'=>array('fields' => array('ProductType.id', 'ProductType.name'))
                        )
                    );

                    $data_table = $this->Waybill->find('all', array('conditions' => $condition_array, 'contain'=>$contain,'order' => "Waybill.$sortname $sortorder", 'limit' => $start . ',' . $limit, 'recursive' => 2));
                    $data_table_count = $this->Waybill->find('count', array('conditions' => $condition_array, 'recursive' => -1));

                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {
                            $waybill_id = $obj['Waybill']['id'];
                            $order_id = $obj['Order']['id'];
                            $bigger_time = date('Y-m-d H:i:s');
                            $edit_row = 'no';

                            // debug($obj);exit;

                            $depot_waybill_feedback = isset($this->waybill_feedback[$obj['Waybill']['depot_approval']])? $this->waybill_feedback[$obj['Waybill']['depot_approval']] : 'Not Yet Approved';
                            $bdc_waybill_feedback = isset($this->waybill_feedback[$obj['Waybill']['bdc_approval']])? $this->waybill_feedback[$obj['Waybill']['bdc_approval']] : 'Not Yet Approved';
                            $cep_waybill_feedback = isset($this->waybill_feedback[$obj['Waybill']['ceps_approval']])? $this->waybill_feedback[$obj['Waybill']['ceps_approval']] : 'Not Yet Approved';

                            $loaded_quantity = empty($obj['Order']['loaded_quantity']) ? '':$this->formatNumber( $obj['Order']['loaded_quantity'],'money',0);
                            $return_arr[] = array(
                                'id' => $obj['Waybill']['id'],
                                'cell' => array(
                                    $obj['Waybill']['id'],
                                    $this->covertDate($obj['Waybill']['created'],'mysql_flip'),
                                    $obj['Order']['id'],
                                    $this->covertDate($obj['Order']['order_date'],'mysql_flip'),
                                    $obj['Order']['ProductType']['name'],
                                    $loaded_quantity,
                                    $obj['Order']['truck_no'],
                                    $obj['Depot']['name']." ($depot_waybill_feedback)",
                                    $obj['Bdc']['name']." ($bdc_waybill_feedback)",
                                    $obj['Cep']['name']." ($cep_waybill_feedback)"
                                ),
                                'extra_data' => array(//Sometime u need certain data to be stored on the main tr at the client side like the referencing table id for editing
                                    'loaded_quantity'=>preg_replace('/,/','',$loaded_quantity),
                                    'order_id'=>$obj['Order']['id']
                                ),
                                'property'=>array(
                                    'edit_row'=> $edit_row
                                )
                            );
                        }
                        return json_encode(array('success' => true, 'total' => $total_records, 'page' => $page, 'rows' => $return_arr));
                    }
                    else {
                        return json_encode(array('success' => false, 'total' => $total_records, 'page' => $page, 'rows' => array()));
                    }

                    break;

                case 'save' :
                    $data = array('Waybill' => $_POST);
                    $data['Waybill']['bdc_approval'] = 'Approved';
                    $order = $this->Order->getOrderById($_POST['order_id']);
                    $omc_id = $order['omc_id'];
                    $bdc_id = $order['bdc_id'];
                    $depot_id = $order['depot_id'];
                    $cep_id = $order['cep_id'];


                    if ($this->Waybill->save($this->sanitize($data))) {
                        $this->endorse('Way Bill',$_POST['id']);

                        //Notify OMC
                        $send_params = array(
                            'title'=>$company_profile['name'].', Way Bill Endorsed',
                            'content'=>'Way Bill number '.$_POST['id'].' has been endorsed',
                            'sender'=>$authUser['id'],
                            'sender_type'=>'blast',
                            'entity'=>'omc',
                            'entity_id'=>$omc_id,
                            'include_this_entity'=>false,
                            'msg_type'=>'system'
                        );
                        $this->sendMessage($send_params);

                        //Notify Depot
                        $send_params = array(
                            'title'=>$company_profile['name'].', Way Bill Endorsed',
                            'content'=>'Way Bill number '.$_POST['id'].' has been endorsed',
                            'sender'=>$authUser['id'],
                            'sender_type'=>'blast',
                            'entity'=>'depot',
                            'entity_id'=>$depot_id,
                            'include_this_entity'=>false,
                            'msg_type'=>'system'
                        );
                        $this->sendMessage($send_params);

                        //Notify Cep
                        $send_params = array(
                            'title'=>$company_profile['name'].', Way Bill Endorsed',
                            'content'=>'Way Bill number '.$_POST['id'].' has been endorsed',
                            'sender'=>$authUser['id'],
                            'sender_type'=>'blast',
                            'entity'=>'ceps_depot',
                            'entity_id'=>$cep_id,
                            'include_this_entity'=>false,
                            'msg_type'=>'system'
                        );
                        $this->sendMessage($send_params);


                        //Activity Log
                        $log_description = $this->getLogMessage('EndorseWaybill')." Way bill number ".$_POST['id'];
                        $this->logActivity('Way Bill',$log_description);

                        return json_encode(array('code' => 0, 'msg' => 'Approved'));
                    } else {
                        echo json_encode(array('code' => 1, 'msg' => 'Some errors occurred.'));
                    }
                    //echo debug($data);
                    break;

                case 'load':

                    break;
            }
        }

        $waybill_feedback = array();
        $waybill_filter = array();
        foreach($this->waybill_feedback as $key => $value){
            $waybill_feedback[] = array(
                'id'=>$key,
                'name'=>$value
            );
            $waybill_filter[] = array(
                'name'=>$key,
                'value'=>$value
            );
        }
        $depot_filter = array( array('name'=>'All','value'=>0));
        $depot_list = $this->get_depot_list(true);
        foreach($depot_list as $key => $value){
            $depot_filter[] = array(
                'name'=>$value['name'],
                'value'=>$value['id']
            );
        }

        $this->set(compact('waybill_feedback','waybill_filter','depot_filter'));
    }


    function print_waybill($id=null){
        $this->layout = 'print_layout';
        $print = false;
        $print_data = $this->Waybill->getWaybillById($id);
        if($print_data){
            $print = true;
        }
        $controller = $this;
        $print_title = $print_data['Depot']['name']." Way Bill";
        $no_print_header ='';

        $this->set(compact('controller','print_title','print','print_data','no_print_header'));
    }


    function get_attachments($order_id = null, $attachment_type =null){
        $this->autoRender = false;
        $result = $this->__get_attachments($attachment_type,$order_id);
        $this->attachment_fire_response($result);
    }

    function attach_files(){
        $this->autoRender = false;
        $upload_data = $this->__attach_files();
        $this->attachment_fire_response($upload_data);
    }

}
