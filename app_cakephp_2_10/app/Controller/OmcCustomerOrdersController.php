<?php

/**
 * @name OmcController.php
 */
App::import('Controller', 'OmcCustomerApp');

class OmcCustomerOrdersController extends OmcCustomerAppController
{
    # Controller name

    var $name = 'OmcCustomerOrders';
    # set the model to use
    var $uses = array('Order','ProductType','OmcCustomerOrder','OmcCustomer','Volume','OmcBdcDistribution', 'OmcCustomerDistribution');

    # Set the layout to use
    var $layout = 'omc_customer_layout';

    public function beforeFilter($param_array = null)
    {
        parent::beforeFilter();
    }


    function index()
    {
        $this->redirect('orders');
    }


    function orders($type = 'get')
    {   $permissions = $this->action_permission;
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
                    $filter_status =   isset($_POST['filter_status']) ? $_POST['filter_status'] : 'incomplete_orders' ;
                    /** Search string */
                    $rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
                    $limit = $rp;
                    $start = ($page - 1) * $rp;

                    //get users id for this company only
                    $condition_array = array(
                        'OmcCustomerOrder.omc_customer_id' => $company_profile['id'],
                        'OmcCustomerOrder.deleted' => 'n'
                    );
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
                        'Omc'=>array('fields' => array('Omc.id', 'Omc.name')),
                        'ProductType'=>array('fields' => array('ProductType.id', 'ProductType.name')),
                        'OmcCustomer'=>array('fields' => array('OmcCustomer.id', 'OmcCustomer.name'))
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
                            if($st != 'New'){
                                $edit_row = 'no';
                            }
                            /*$feedback = 'N/A';
                            if($obj['OmcCustomerOrder']['omc_feedback'] == 'N/A' || $obj['OmcCustomerOrder']['omc_feedback'] == 'Approved'){
                                $feedback = $obj['OmcCustomerOrder']['omc_feedback'];
                            }
                            else{

                            }*/
                            $feedback = isset($this->omc_dealer_feedback[$obj['OmcCustomerOrder']['finance_approval']]) ? $this->omc_dealer_feedback[$obj['OmcCustomerOrder']['finance_approval']] : '';

                            $return_arr[] = array(
                                'id' => $obj['OmcCustomerOrder']['id'],
                                'cell' => array(
                                    $obj['OmcCustomerOrder']['id'],
                                    $this->covertDate($obj['OmcCustomerOrder']['order_date'],'mysql_flip'),
                                    //$order_time_elapsed,
                                    //$obj['OmcCustomer']['name'],
                                    //$obj['Omc']['name'],
                                    $obj['ProductType']['name'],
                                   $this->formatNumber( $obj['OmcCustomerOrder']['order_quantity'],'money',0),
                                    /*$this->mkt_feedback[$obj['Order']['delivery_priority']],*/
                                    $obj['OmcCustomerOrder']['intended_delivery_location'],
                                    $feedback
                                    //$this->fna_feedback[$obj['OmcCustomerOrder']['finance_approval']],
                                   // $obj['OmcCustomerOrder']['delivery_quantity']
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
                        return json_encode(array('success' => false, 'total' => $total_records, 'page' => $page, 'rows' => array()));
                    }

                    break;

                case 'save' :
                    $data = array('OmcCustomerOrder' => $_POST);
                    $data['OmcCustomerOrder']['order_date'] = $this->covertDate($_POST['order_date'],'mysql').' '.date('H:i:s');
                    if($_POST['id']== 0){//New Manual Entry
                        $data['OmcCustomerOrder']['dealer_created_by'] = $authUser['id'];
                        $data['OmcCustomerOrder']['order_status'] = 'New';
                        $data['OmcCustomerOrder']['row_bg_color'] = 'tr_blue';
                        $data['OmcCustomerOrder']['dealer_created'] = date('Y-m-d H:i:s');
                        $data['OmcCustomerOrder']['omc_id'] =  $company_profile['omc_id'];
                        $data['OmcCustomerOrder']['omc_customer_id'] =  $company_profile['id'];
                        $data['OmcCustomerOrder']['record_type'] = 'omc';
                    }
                    else{// Might be correcting error
                        $data['OmcCustomerOrder']['dealer_modified'] = date('Y-m-d H:i:s');
                        $data['OmcCustomerOrder']['dealer_modified_by'] = $authUser['id'];
                        if($_POST['extra']['order_status'] == 'New'){
                            $data['OmcCustomerOrder']['order_status'] = 'New';
                            $data['OmcCustomerOrder']['row_bg_color'] = 'tr_blue';
                        }
                    }

                    if ($this->OmcCustomerOrder->save($this->sanitize($data))) {
                        $order_id  = $this->OmcCustomerOrder->id;
                        $send_notification = false;
                        if($_POST['id']== 0){//if it is a new manual record, then send notification
                            $send_notification = true;
                        }

                        if($send_notification){
                            /** Notify OMC **/
                            $send_params = array(
                                'title'=>$company_profile['name'].', Order',
                                'content'=>'We have placed an order (order number '.$order_id.')',
                                'sender'=>$authUser['id'],
                                'sender_type'=>'blast',
                                'entity'=>'omc',
                                'entity_id'=>$company_profile['omc_id'],
                                'include_this_entity'=>false,
                                'msg_type'=>'system'
                            );
                            $this->sendMessage($send_params);

                            //Activity Log
                            $log_description = $this->getLogMessage('NewOrder')." (Order #".$order_id.")";
                            $this->logActivity('Order',$log_description);
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
        $products_lists = $this->get_products();
       /* $depot_lists = $this->get_depot_list();
        $omc_customers_lists = $this->get_customer_list();
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

        $order_filter = $this->order_filter;

        $g_data =  $this->get_orders($start_dt,$end_dt,$group_by,null);

        $volumes = $this->Volume->getVolsList();

        $graph_title = $group_by_title.", Orders-Consolidated";

        $this->set(compact('grid_data','omc_customers_lists','volumes','permissions','depot_lists', 'products_lists','bdc_list','graph_title','g_data','bdclists','order_filter'));
    }



    function orders_delivery($type = 'get')
    {   $permissions = $this->action_permission;
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
                    $filter_status =   isset($_POST['filter_status']) ? $_POST['filter_status'] : 'complete_orders' ;
                    /** Search string */
                    $rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
                    $limit = $rp;
                    $start = ($page - 1) * $rp;

                    //get users id for this company only
                    $condition_array = array(
                        'OmcCustomerOrder.omc_customer_id' => $company_profile['id'],
                        'OmcCustomerOrder.deleted' => 'n'
                    );
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
                        'Omc'=>array('fields' => array('Omc.id', 'Omc.name')),
                        'ProductType'=>array('fields' => array('ProductType.id', 'ProductType.name')),
                        'OmcCustomer'=>array('fields' => array('OmcCustomer.id', 'OmcCustomer.name'))
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

                            $delivery_quantity =  isset($obj['OmcCustomerOrder']['delivery_quantity']) ? $this->formatNumber($obj['OmcCustomerOrder']['delivery_quantity'],'money',0) : '';
                            $received_quantity =  isset($obj['OmcCustomerOrder']['received_quantity']) ? $this->formatNumber($obj['OmcCustomerOrder']['received_quantity'],'money',0) : '';
                            $delivery_date =  isset($obj['OmcCustomerOrder']['delivery_date']) ? $this->covertDate($obj['OmcCustomerOrder']['delivery_date'],'mysql_flip') : '';

                            $return_arr[] = array(
                                'id' => $obj['OmcCustomerOrder']['id'],
                                'cell' => array(
                                    $obj['OmcCustomerOrder']['id'],
                                    $this->covertDate($obj['OmcCustomerOrder']['order_date'],'mysql_flip'),
                                    //$order_time_elapsed,
                                    $obj['ProductType']['name'],
                                    $this->formatNumber($obj['OmcCustomerOrder']['order_quantity'],'money',0),
                                    $delivery_quantity,
                                    $received_quantity,
                                    $obj['OmcCustomerOrder']['comments'],
                                    $delivery_date
                                ),
                                'extra_data' => array(//Sometime u need certain data to be stored on the main tr at the client side like the referencing table id for editing
                                    'order_status'=>$obj['OmcCustomerOrder']['order_status'],
                                    'delivery_quantity'=>$obj['OmcCustomerOrder']['delivery_quantity'],
                                    'product_id'=>$obj['ProductType']['id']
                                ),
                                'property'=>array(
                                    //'bg_color'=>$obj['OmcCustomerOrder']['row_bg_color'],
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
                    $data['OmcCustomerOrder']['dealer_modified'] = date('Y-m-d H:i:s');
                    $data['OmcCustomerOrder']['dealer_modified_by'] = $authUser['id'];
                    $data['OmcCustomerOrder']['delivery_date'] = $this->covertDate($_POST['delivery_date'],'mysql').' '.date('H:i:s');
                    $product_id = $_POST['extra']['product_id'];
                    $delivery_quantity = $_POST['extra']['delivery_quantity'];
                    $received_quantity = $_POST['received_quantity'];
                    $shortage = $delivery_quantity - $received_quantity;
                    $data['OmcCustomerOrder']['shortage_quantity'] = $shortage;
                    $price_change = $this->getPriceChangeData();
                    $product_price = doubleval($price_change[$product_id]['price']);
                    $shortage_cost = $shortage * $product_price;
                    $data['OmcCustomerOrder']['shortage_cost'] = $shortage_cost;
                    if ($this->OmcCustomerOrder->save($this->sanitize($data))) {
                        $order_id  = $this->OmcCustomerOrder->id;
                        //Activity Log
                        $log_description = $this->getLogMessage('UpdateDeliveryQuantity')." (Order #".$order_id.")";
                        $this->logActivity('Order',$log_description);

                        if($_POST['id'] > 0){
                            return json_encode(array('code' => 0, 'msg' => 'Data Updated!'));
                        }
                        else{
                            return json_encode(array('code' => 0, 'msg' => 'Data Saved', 'id'=>$order_id));
                        }
                    } else {
                        echo json_encode(array('code' => 1, 'msg' => 'Some errors occurred.'));
                    }
                    //echo debug($data);
                    break;

                case 'load':

                    break;
            }
        }

        $products_lists = $this->get_products();
        $start_dt = date('01-m-Y');
        $end_dt = date('t-m-Y');
        $group_by = 'monthly';
        $group_by_title = date('F');

        /* $bdclists =array(array('name'=>'All','value'=>0));
         foreach($bdclists_data as $arr){
             $bdclists[] = array('name'=>$arr['name'],'value'=>$arr['id']);
         }*/

        $order_filter = $this->order_filter;

        $g_data =  $this->get_orders($start_dt,$end_dt,$group_by,null);

        $volumes = $this->Volume->getVolsList();

        $graph_title = $group_by_title.", Orders-Consolidated";

        $this->set(compact('grid_data','omc_customers_lists','volumes','permissions','depot_lists', 'products_lists','bdc_list','graph_title','g_data','bdclists','order_filter'));
    }


    function shortages($type = 'get')
    {   $permissions = $this->action_permission;
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
                    $filter_status =   isset($_POST['filter_status']) ? $_POST['filter_status'] : 'complete_orders' ;
                    /** Search string */
                    $rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
                    $limit = $rp;
                    $start = ($page - 1) * $rp;

                    //get users id for this company only
                    $condition_array = array(
                        'OmcCustomerOrder.omc_customer_id' => $company_profile['id'],
                        'OmcCustomerOrder.deleted' => 'n'
                    );
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
                        'Omc'=>array('fields' => array('Omc.id', 'Omc.name')),
                        'ProductType'=>array('fields' => array('ProductType.id', 'ProductType.name')),
                        'OmcCustomer'=>array('fields' => array('OmcCustomer.id', 'OmcCustomer.name'))
                    );
                    // $fields = array('User.id', 'User.username', 'User.first_name', 'User.last_name', 'User.group_id', 'User.active');
                    $data_table = $this->OmcCustomerOrder->find('all', array('conditions' => $condition_array, 'contain'=>$contain,'order' => "OmcCustomerOrder.$sortname $sortorder", 'limit' => $start . ',' . $limit, 'recursive' => 1));
                    $data_table_count = $this->OmcCustomerOrder->find('count', array('conditions' => $condition_array, 'recursive' => -1));

                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {
                            $delivery_quantity =  isset($obj['OmcCustomerOrder']['delivery_quantity']) ? $this->formatNumber($obj['OmcCustomerOrder']['delivery_quantity'],'money',0) : '';
                            $received_quantity =  isset($obj['OmcCustomerOrder']['received_quantity']) ? $this->formatNumber($obj['OmcCustomerOrder']['received_quantity'],'money',0) : '';
                            $shortage_quantity =  isset($obj['OmcCustomerOrder']['shortage_quantity']) ? $this->formatNumber($obj['OmcCustomerOrder']['shortage_quantity'],'money',0) : '';
                            $shortage_cost =  isset($obj['OmcCustomerOrder']['shortage_cost']) ? $this->formatNumber($obj['OmcCustomerOrder']['shortage_cost'],'money',2) : '';
                            $return_arr[] = array(
                                'id' => $obj['OmcCustomerOrder']['id'],
                                'cell' => array(
                                    $obj['OmcCustomerOrder']['id'],
                                    $this->covertDate($obj['OmcCustomerOrder']['order_date'],'mysql_flip'),
                                    $obj['ProductType']['name'],
                                    $this->formatNumber($obj['OmcCustomerOrder']['order_quantity'],'money',0),
                                    $delivery_quantity,
                                    $received_quantity,
                                    $shortage_quantity,
                                    $shortage_cost,
                                    $obj['OmcCustomerOrder']['truck_number'],
                                    $obj['OmcCustomerOrder']['driver'],
                                    $obj['OmcCustomerOrder']['comments']
                                ),
                                'extra_data' => array(//Sometime u need certain data to be stored on the main tr at the client side like the referencing table id for editing
                                    'order_status'=>$obj['OmcCustomerOrder']['order_status'],
                                    'delivery_quantity'=>$obj['OmcCustomerOrder']['delivery_quantity'],
                                    'product_id'=>$obj['ProductType']['id']
                                ),
                                'property'=>array(
                                    //'bg_color'=>$obj['OmcCustomerOrder']['row_bg_color'],
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
                    $data['OmcCustomerOrder']['dealer_modified'] = date('Y-m-d H:i:s');
                    $data['OmcCustomerOrder']['dealer_modified_by'] = $authUser['id'];
                    $product_id = $_POST['extra']['product_id'];
                    $delivery_quantity = $_POST['extra']['delivery_quantity'];
                    $received_quantity = $_POST['received_quantity'];
                    $shortage = $delivery_quantity - $received_quantity;
                    $data['OmcCustomerOrder']['shortage_quantity'] = $shortage;
                    $price_change = $this->getPriceChangeData();
                    $product_price = doubleval($price_change[$product_id]['price']);
                    $shortage_cost = $shortage * $product_price;
                    $data['OmcCustomerOrder']['shortage_cost'] = $shortage_cost;
                    if ($this->OmcCustomerOrder->save($this->sanitize($data))) {
                        $order_id  = $this->OmcCustomerOrder->id;

                        if($_POST['id'] > 0){
                            return json_encode(array('code' => 0, 'msg' => 'Data Updated!'));
                        }
                        else{
                            return json_encode(array('code' => 0, 'msg' => 'Data Saved', 'id'=>$order_id));
                        }
                    } else {
                        echo json_encode(array('code' => 1, 'msg' => 'Some errors occurred.'));
                    }
                    //echo debug($data);
                    break;

                case 'load':

                    break;
            }
        }

        $products_lists = $this->get_products();
        $start_dt = date('01-m-Y');
        $end_dt = date('t-m-Y');
        $group_by = 'monthly';
        $group_by_title = date('F');

        /* $bdclists =array(array('name'=>'All','value'=>0));
         foreach($bdclists_data as $arr){
             $bdclists[] = array('name'=>$arr['name'],'value'=>$arr['id']);
         }*/

        $order_filter = $this->order_filter;

        $g_data =  $this->get_orders($start_dt,$end_dt,$group_by,null);

        $volumes = $this->Volume->getVolsList();

        $graph_title = $group_by_title.", Orders-Consolidated";

        $this->set(compact('grid_data','omc_customers_lists','volumes','permissions','depot_lists', 'products_lists','bdc_list','graph_title','g_data','bdclists','order_filter'));
    }


    function export_shortages(){
        $download = false;
        $company_profile = $this->global_company;
        if($this->request->is('post')){
            $export_params = $this->request->data;
            $start_dt = $this->covertDate($export_params['exp_startdt'],'mysql').' 00:00:00';
            $end_dt =  $this->covertDate($export_params['exp_enddt'],'mysql').' 23:59:59';

            $conditions = array(
                'OmcCustomerOrder.omc_customer_id' => $company_profile['id'],
                'OmcCustomerOrder.deleted' => 'n',
                'OmcCustomerOrder.order_date >=' => $start_dt, 'OmcCustomerOrder.order_date <=' => $end_dt,
                'OmcCustomerOrder.order_status'=>'Complete'
            );

            $contain = array(
                'ProductType'=>array('fields' => array('ProductType.id', 'ProductType.name'))
            );

            $export_data = $this->OmcCustomerOrder->find('all', array(
                //'fields'=>array('Order.id','Order.loading_date','Order.waybill_date','Order.quantity','BdcDistribution.waybill_id','BdcDistribution.vehicle_no'),
                'conditions' => $conditions,
                'contain'=>$contain,
                'order' => array("OmcCustomerOrder.id"=>'desc'),
                'recursive' => 1
            ));

            if ($export_data) {
                $download = true;
                $list_data = array();
                foreach ($export_data as $obj) {
                    $delivery_quantity =  isset($obj['OmcCustomerOrder']['delivery_quantity']) ? $this->formatNumber($obj['OmcCustomerOrder']['delivery_quantity'],'number',0) : '';
                    $received_quantity =  isset($obj['OmcCustomerOrder']['received_quantity']) ? $this->formatNumber($obj['OmcCustomerOrder']['received_quantity'],'number',0) : '';
                    $shortage_quantity =  isset($obj['OmcCustomerOrder']['shortage_quantity']) ? $this->formatNumber($obj['OmcCustomerOrder']['shortage_quantity'],'number',0) : '';
                    $shortage_cost =  isset($obj['OmcCustomerOrder']['shortage_cost']) ? $this->formatNumber($obj['OmcCustomerOrder']['shortage_cost'],'number',2) : '';
                    $list_data[] = array(
                        $obj['OmcCustomerOrder']['id'],
                        $this->covertDate($obj['OmcCustomerOrder']['order_date'],'mysql_flip'),
                        $obj['ProductType']['name'],
                        $this->formatNumber($obj['OmcCustomerOrder']['order_quantity'],'number',0),
                        $delivery_quantity,
                        $received_quantity,
                        $shortage_quantity,
                        $shortage_cost,
                        $obj['OmcCustomerOrder']['truck_number'],
                        $obj['OmcCustomerOrder']['driver'],
                        $obj['OmcCustomerOrder']['comments']
                    );
                }
                $list_headers = array('Order Id','Order Date','Product Type','Order Quantity','Delivery Quantity','Received Quantity','Quantity Short','Shortage Cost','Delivery Truck','Driver','Comments');
                $filename = $company_profile['name']." Product Shortages ".date('Ymdhis');
                $res = $this->convertToExcel($list_headers,$list_data,$filename);
                $objPHPExcel = $res['excel_obj'];
                $filename = $res['filename'];
            }
        }

        $this->autoLayout = false;

        $this->set(compact('objPHPExcel', 'download', 'filename'));
    }


    function export_orders(){
        $download = false;
        $company_profile = $this->global_company;;
        if($this->request->is('post')){
            if($this->request->data['Export']['action'] == 'export_me'){
                $start_dt = $this->covertDate($this->request->data['Export']['export_startdt'],'mysql').' 00:00:00';
                $end_dt =  $this->covertDate($this->request->data['Export']['export_enddt'],'mysql').' 23:59:59';
                $type = $this->request->data['Export']['export_type'];
                $contain = array(
                    'Bdc'=>array('fields' => array('Bdc.id', 'Bdc.name')),
                    'Depot'=>array('fields' => array('Depot.id', 'Depot.name')),
                    'ProductType'=>array('fields' => array('ProductType.id', 'ProductType.name')),
                    'OmcCustomer'=>array('fields' => array('OmcCustomer.id', 'OmcCustomer.name'))
                );
                $export_data = $this->Order->find('all', array(
                    //'fields'=>array('Order.id','Order.loading_date','Order.waybill_date','Order.quantity','BdcDistribution.waybill_id','BdcDistribution.vehicle_no'),
                    'conditions' => array('Order.omc_id' => $company_profile['id'], 'Order.deleted' => 'n','Order.order_date >=' => $start_dt, 'Order.order_date <=' => $end_dt),
                    'contain'=>$contain,
                    'order' => array("Order.id"=>'desc'),
                    'recursive' => 1
                ));

                if ($export_data) {
                    $download = true;
                    $list_data = array();
                    foreach ($export_data as $obj) {
                        $list_data[] = array(
                            $obj['Order']['id'],
                            $this->covertDate($obj['Order']['order_date'],'mysql_flip'),
                            $obj['OmcCustomer']['name'],
                            $obj['Bdc']['name'],
                            $obj['Depot']['name'],
                            $obj['ProductType']['name'],
                            preg_replace('/,/','',$obj['Order']['order_quantity']),
                            $this->mkt_feedback[$obj['Order']['delivery_priority']],
                            $this->ops_feedback[$obj['Order']['bdc_feedback']],
                            $this->fna_feedback[$obj['Order']['finance_approval']],
                             preg_replace('/,/','',$obj['Order']['approved_quantity']),
                        );
                    }
                    $list_headers = array('Order Id','Order Date','Customer','BDC','Loading Depot','Product Type','Order Quantity','Delivery Priority','BDC Feedback','BDC Finance Approval','Approved Quantity');
                    //$list_headers = array('Date','Waybill No.','From','Depot','Product Type','Actual Quantity','Vehicle No.','Customer Name','Quantity Delivered','Delivery Location','Region','District');
                    $filename = $company_profile['name']." Orders ".date('Ymdhis');
                    $res = $this->convertToExcel($list_headers,$list_data,$filename);
                    $objPHPExcel = $res['excel_obj'];
                    $filename = $res['filename'];
                }
            }
        }

        $this->autoLayout = false;

        $this->set(compact('objPHPExcel', 'download', 'filename'));
    }


    function distribution($type = 'get')
    {
        $company_profile = $this->global_company;
        $permissions = $this->action_permission;
        $authUser = $this->Auth->user();
        if ($this->request->is('ajax')) {
            $this->autoRender = false;
            $this->autoLayout = false;
            $authUser = $this->Auth->user();
            $company_profile = $this->global_company;;
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

                    $condition_array = array('OmcBdcDistribution.omc_customer_id' => $company_profile['id'], 'OmcBdcDistribution.deleted' => 'n');

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
                        'BdcDistribution'=>array(
                            'Depot'=>array('fields' => array('Depot.id', 'Depot.name')),
                            'ProductType'=>array('fields' => array('ProductType.id', 'ProductType.name')),
                        ),
                        'Region'=>array('fields' => array('Region.id', 'Region.name')),
                        'DeliveryLocation'=>array('fields' => array('DeliveryLocation.id', 'DeliveryLocation.name'))
                    );
                    // $fields = array('User.id', 'User.username', 'User.first_name', 'User.last_name', 'User.group_id', 'User.active');
                    $data_table = $this->OmcBdcDistribution->find('all', array('conditions' => $condition_array, 'contain'=>$contain,'order' => "OmcBdcDistribution.$sortname $sortorder", 'limit' => $start . ',' . $limit, 'recursive' => 2));
                    $data_table_count = $this->OmcBdcDistribution->find('count', array('conditions' => $condition_array, 'recursive' => -1));

                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {
                            $to_row = array(
                                'id' => $obj['OmcBdcDistribution']['id'],
                                'cell' => array(
                                    $obj['OmcBdcDistribution']['id'],
                                    $this->covertDate($obj['BdcDistribution']['loading_date'],'mysql_flip'),
                                    //$this->covertDate($obj['BdcDistribution']['waybill_date'],'mysql_flip'),
                                    $obj['OmcBdcDistribution']['invoice_number'],
                                    $obj['BdcDistribution']['ProductType']['name'],
                                    $this->formatNumber($obj['OmcBdcDistribution']['quantity'],'money',0),
                                    // $obj['Region']['name'],
                                    $obj['DeliveryLocation']['name'],
                                    $obj['OmcBdcDistribution']['transporter'],
                                    $obj['BdcDistribution']['vehicle_no']
                                ),
                                'extra_data' => array(//Sometime u need certain data to be stored on the main tr at the client side like the referencing table id for editing
                                    /*'omc_name'=>$obj['Bdc']['name'],
                                    'record_origin'=>$obj['BdcDistribution']['record_origin'],
                                    'order_status'=>$obj['BdcDistribution']['order_status'],
                                    'order_id'=>$obj['BdcDistribution']['order_id'],
                                    'depot_id'=>$obj['Depot']['id']*/
                                )
                            );
                            $return_arr[] = $to_row;
                        }
                        return json_encode(array('success' => true, 'total' => $total_records, 'page' => $page, 'rows' => $return_arr));
                    }
                    else {
                        return json_encode(array('success' => false, 'total' => $total_records, 'page' => $page, 'rows' => array()));
                    }

                    break;

                case 'save' :

                    break;

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
                    $data = array('OmcCustomerDistribution' => $_POST);
                    $data['OmcCustomerDistribution']['omc_bdc_distribution_id'] = $_POST['parent_id'];
                    if($_POST['id'] == 0){
                        $data['OmcCustomerDistribution']['created_by'] = $authUser['id'];
                    }
                    else{
                        $data['OmcCustomerDistribution']['modified_by'] = $authUser['id'];
                    }

                    if ($this->OmcCustomerDistribution->save($this->sanitize($data))) {
                        if($_POST['id'] > 0){
                            return json_encode(array('code' => 0, 'msg' => 'Data Updated!'));
                        }
                        else{
                            return json_encode(array('code' => 0, 'msg' => 'Data Saved!', 'id'=>$this->OmcCustomerDistribution->id));
                        }
                        //echo json_encode(array('success' => 0, 'msg' => 'Data Saved'));
                    } else {
                        echo json_encode(array('success' => 1, 'msg' => 'Some errors occured.'));
                    }
                    break;

                case 'load_details':
                    $gdata = $this->OmcCustomerDistribution->find('all',array(
                        'conditions'=>array('OmcCustomerDistribution.omc_bdc_distribution_id'=>$_POST['id']),
                        'contain' => array(
                            //'OmcCustomer'=>array('fields' => array('OmcCustomer.id', 'OmcCustomer.name')),
                            //'DeliveryLocation'=>array('fields' => array('DeliveryLocation.id', 'DeliveryLocation.name')),
                            'Region'=>array('fields' => array('Region.id', 'Region.name'))
                        ),
                        'recursive'=>1
                    ));

                    if($gdata){
                        foreach ($gdata as $obj) {
                            $return_arr[] = array(
                                'id' => $obj['OmcCustomerDistribution']['id'],
                                'cell' => array(
                                    //$obj['OmcBdcDistribution']['invoice_number'],
                                    $obj['OmcCustomerDistribution']['customer'],
                                    $obj['OmcCustomerDistribution']['quantity'],
                                    $obj['Region']['name'],
                                    $obj['OmcCustomerDistribution']['location'],
                                    $obj['OmcCustomerDistribution']['transporter']
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
        $places_data = $this->get_region_district();
        $glbl_region_district = $places_data['region_district'];
        $regions_lists = $places_data['region'];

        $this->set(compact('company_profile','grid_data', 'liters_per_products', 'omc_customers_lists','bdc_depot_lists', 'bdc_lists','omclists', 'products_lists', 'regions_lists', 'district_lists', 'bar_graph_data', 'pie_data','glbl_region_district','delivery_locations'));
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