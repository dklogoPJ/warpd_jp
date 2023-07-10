<?php

/**
 * @name BdcOrdersController.php
 */
App::import('Controller', 'BdcApp');

class BdcOrdersController extends BdcAppController
{
    # Controller name

    var $name = 'BdcOrders';
    # set the model to use
    var $uses = array('Bdc','BdcDistribution','OmcBdcDistribution', 'OmcCustomer','BdcOmc','Order','Depot','ProductType','Volume','Cep','Waybill','StockTrading');

    # Set the layout to use
    var $layout = 'bdc_layout';

    # Bdc ids this user will work with only
    var $user_bdc_ids = array();

    public function beforeFilter($param_array = null)
    {
        parent::beforeFilter();
    }


    function index()
    {
        $this->redirect('orders');
    }


    function orders($type = 'get')
    {
        $user = $this->Auth->user('BdcUser');
        $user_type = $user['bdc_user_type'];
        $company_profile = $this->global_company;
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
                    $filter_status =   isset($_POST['filter_status']) ? $_POST['filter_status'] : 'incomplete_orders' ;
                    /** Search string */
                    $rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
                    $limit = $rp;
                    $start = ($page - 1) * $rp;

                    $condition_array = array(
                        'Order.bdc_id' => $company_profile['id'],
                        'Order.record_type' => 'bdc',
                        'Order.deleted' => 'n',
                    );

                    $group_depot = ClassRegistry::init('User')->getDepotGroup($authUser['id']);
                    if($group_depot > 0){
                        $condition_array['Order.depot_id'] = $group_depot;
                    }
                    //get users id for this company only

                    if($filter != 0){
                        $condition_array['Order.omc_id'] = $filter;
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
                        'Omc'=>array('fields' => array('Omc.id', 'Omc.name')),
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
                            if($obj['Order']['order_status'] == 'Complete' || $obj['Order']['order_status'] == 'Cancelled'){
                                $bigger_time = $obj['Order']['bdc_modified'];
                                $time_hr = $this->count_time_between_dates($obj['Order']['omc_created'],$bigger_time,'hours');
                                // $time_days = $this->count_time_between_dates($obj['Order']['omc_created'],$bigger_time,'days');
                                $order_time_elapsed = $time_hr.' hr(s)';
                                $edit_row = 'no';
                            }
                            else{
                                $time_hr = $this->count_time_between_dates($obj['Order']['omc_created'],$bigger_time,'hours');
                                // $time_days = $this->count_time_between_dates($obj['Order']['omc_created'],$bigger_time,'days');
                                $order_time_elapsed =  $time_hr.' hr(s)';
                                $edit_row = 'yes';
                            }
                            //has CEPS Approved ?
                            if($obj['Order']['ceps_approval'] == 'Approved'){
                                $edit_row = 'no';
                            }

                            $bg_color = $obj['Order']['row_bg_color'];

                            $delivery_priority = isset($this->mkt_feedback[$obj['Order']['delivery_priority']])? $this->mkt_feedback[$obj['Order']['delivery_priority']] : '';
                            $bdc_feedback = isset($this->ops_feedback[$obj['Order']['bdc_feedback']])? $this->ops_feedback[$obj['Order']['bdc_feedback']] : '';
                            $finance_approval = isset($this->fna_feedback[$obj['Order']['finance_approval']])? $this->fna_feedback[$obj['Order']['finance_approval']] : '';
                            $ceps_approval = isset($this->ceps_feedback[$obj['Order']['ceps_approval']])? $this->ceps_feedback[$obj['Order']['ceps_approval']] : '';

                            $return_arr[] = array(
                                'id' => $obj['Order']['id'],
                                'cell' => array(
                                    $obj['Order']['id'],
                                    $this->covertDate($obj['Order']['order_date'],'mysql_flip'),
                                    $order_time_elapsed,
                                    $obj['OmcCustomer']['name'],
                                    $obj['Omc']['name'],
                                    $obj['Depot']['name'],
                                    $obj['ProductType']['name'],
                                    $this->formatNumber( $obj['Order']['order_quantity'],'money',0),
                                    $delivery_priority,
                                    $bdc_feedback,
                                    $finance_approval,
                                    $this->formatNumber( $obj['Order']['approved_quantity'],'money',0),
                                    $ceps_approval

                                ),
                                'extra_data' => array(//Sometime u need certain data to be stored on the main tr at the client side like the referencing table id for editing
                                    'record_origin'=>$obj['Order']['record_origin'],
                                    'order_status'=>$obj['Order']['order_status'],
                                    'ops_feedback'=>$obj['Order']['bdc_feedback'],
                                    'fna_feedback'=>$obj['Order']['finance_approval'],
                                    'order_quantity'=>preg_replace('/,/','',$obj['Order']['order_quantity']),
                                    'omc_id'=>$obj['Order']['omc_id'],
                                    'omc_name'=>$obj['Omc']['name'],
                                    'depot_id'=>$obj['Depot']['id']
                                ),
                                'property'=>array(
                                    'bg_color'=>$bg_color,
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
                    $data = array('Order' => $_POST);
                    $data['Order']['bdc_modified'] = date('Y-m-d H:i:s');
                    $data['Order']['bdc_modified_by'] = $authUser['id'];
                    $prev_ops_feedback = $_POST['extra']['ops_feedback'];
                    $fna_feedback = $_POST['extra']['fna_feedback'];
                    $omc_id = $_POST['extra']['omc_id'];
                    $omc_name = $_POST['extra']['omc_name'];
                    $depot_id = $_POST['extra']['depot_id'];
                    $prev_order_status = $_POST['extra']['order_status'];

                    $flow_ceps = false;

                    if($_POST['bdc_feedback'] == 'Finance Required'){
                        $data['Order']['row_bg_color'] = 'tr_yellow';
                        $data['Order']['order_status'] = 'Processing';
                    }
                    elseif($_POST['bdc_feedback'] == 'N/A'){
                       // $data['Order']['row_bg_color'] = 'tr_blue';
                       // $data['Order']['order_status'] = 'New';
                    }
                    elseif($_POST['bdc_feedback'] == 'Not Approved'){
                        $data['Order']['row_bg_color'] = 'tr_red';
                        $data['Order']['order_status'] = 'Cancelled';
                        $data['Order']['edit_row'] = 'no';
                    }
                    elseif($_POST['bdc_feedback'] == 'Approved'){
                        $data['Order']['row_bg_color'] = 'tr_green';
                        $data['Order']['order_status'] = 'Pending Loading';
                        if($fna_feedback == 'N/A'){
                            $order = $this->Order->find('first', array(
                                'conditions' => array('Order.id' => $_POST['id']),
                                'recursive' => -1
                            ));
                            $data['Order']['finance_approval'] = 'Ok';
                            $data['Order']['approved_quantity'] = $order['Order']['order_quantity'];
                            $flow_ceps = true;
                        }
                        elseif($fna_feedback == 'Not Approved'){
                           // $data['Order']['bdc_feedback'] = '';
                            $data['Order']['row_bg_color'] = 'tr_red';
                            $data['Order']['order_status'] = 'Cancelled';
                            $data['Order']['edit_row'] = 'no';
                            $flow_ceps = false;
                        }
                        elseif($fna_feedback == 'Approved' || $fna_feedback == 'Ok'){
                            $flow_ceps = true;
                        }
                    }
                    else{
                        $data['Order']['row_bg_color'] = 'tr_yellow';
                        if($fna_feedback == 'Not Approved'){
                            // $data['Order']['bdc_feedback'] = '';
                            $data['Order']['row_bg_color'] = 'tr_red';
                            $flow_ceps = false;
                        }
                    }

                    if($flow_ceps){
                        //Flow to ceps
                        $ceps_data = $this->Cep->getCepByDepot($depot_id);
                        $cep_id = $ceps_data['Cep']['id'];
                        $data['Order']['cep_id'] = $cep_id;
                    }


                    if ($this->Order->save($this->sanitize($data))) {
                        if($flow_ceps ){
                            //Notify OMC
                            $send_params = array(
                                'title'=>$omc_name.', Order Approved',
                                'content'=>'Your Order number '. $data['Order']['id'].' has been approved by '.$company_profile['name'],
                                'sender'=>$authUser['id'],
                                'sender_type'=>'blast',
                                'entity'=>'omc',
                                'entity_id'=>$omc_id,
                                'include_this_entity'=>false,
                                'msg_type'=>'system'
                            );
                            $this->sendMessage($send_params);

                            //Notify Ceps
                            $send_params = array(
                                'title'=>$company_profile['name'].', Order Approval Required',
                                'content'=>'Our Order number '. $data['Order']['id'].' needs your approval to load.',
                                'sender'=>$authUser['id'],
                                'sender_type'=>'blast',
                                'entity'=>'ceps_depot',
                                'entity_id'=> $data['Order']['cep_id'],
                                'include_this_entity'=>false,
                                'msg_type'=>'system'
                            );
                            $this->sendMessage($send_params);
                        }

                        //Activity Log
                        $log_description = $this->getLogMessage('ApproveOrder')." (Order number ".$data['Order']['id']." For ".$omc_name.")";
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

        $ops_feedback = array();
        foreach($this->ops_feedback as $key => $value){
            $ops_feedback[] = array(
                'id'=>$key,
                'name'=>$value
            );
        }

        $fna_feedback = array();
        foreach($this->fna_feedback as $key => $value){
            $fna_feedback[] = array(
                'id'=>$key,
                'name'=>$value
            );
        }

        $mkt_feedback = array();
        foreach($this->mkt_feedback as $key => $value){
            $mkt_feedback[] = array(
                'id'=>$key,
                'name'=>$value
            );
        }

        $omclists_data = $this->get_omc_list();
        $omc_lists =array(array('name'=>'All','value'=>0));
        foreach($omclists_data as $arr){
            $omc_lists[] = array('name'=>$arr['name'],'value'=>$arr['id']);
        }

        $order_filter = $this->order_filter;

        //debug($user_type);

        $start_dt = date('Y-m-1');
        $end_dt = date('Y-m-t');
        $group_by = 'monthly';
        $group_by_title = date('F');

        $g_data =  $this->get_orders($start_dt,$end_dt,$group_by,null,null);
        //debug($g_data);
        $graph_title = $group_by_title." Consolidated Orders";

        $this->set(compact('ops_feedback','fna_feedback','mkt_feedback','user_type','g_data','graph_title','omc_lists','order_filter'));
    }


    function orders_finance($type = 'get')
    {
        $user = $this->Auth->user('BdcUser');
        $user_type = $user['bdc_user_type'];
        $company_profile = $this->global_company;
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
                    $filter_status =   isset($_POST['filter_status']) ? $_POST['filter_status'] : 'incomplete_orders' ;
                    /** Search string */
                    $rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
                    $limit = $rp;
                    $start = ($page - 1) * $rp;

                    //get users id for this company only
                    $condition_array = array(
                        'Order.bdc_id' => $company_profile['id'],
                        'Order.record_type' => 'bdc',
                        'Order.deleted' => 'n',
                    );
                    if($filter != 0){
                        $condition_array['Order.omc_id'] = $filter;
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
                        'Omc'=>array('fields' => array('Omc.id', 'Omc.name')),
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
                            if($obj['Order']['order_status'] == 'Complete' || $obj['Order']['order_status'] == 'Cancelled'){
                                $bigger_time = $obj['Order']['bdc_modified'];
                                $time_hr = $this->count_time_between_dates($obj['Order']['omc_created'],$bigger_time,'hours');
                                // $time_days = $this->count_time_between_dates($obj['Order']['omc_created'],$bigger_time,'days');
                                $order_time_elapsed = $time_hr.' hr(s)';
                                $edit_row = 'no';
                            }
                            else{
                                $time_hr = $this->count_time_between_dates($obj['Order']['omc_created'],$bigger_time,'hours');
                                // $time_days = $this->count_time_between_dates($obj['Order']['omc_created'],$bigger_time,'days');
                                $order_time_elapsed =  $time_hr.' hr(s)';
                                $edit_row = 'yes';
                            }
                            //has CEPS Approved ?
                            if($obj['Order']['ceps_approval'] == 'Approved'){
                                $edit_row = 'no';
                            }


                            $bg_color = $obj['Order']['row_bg_color'];

                            $delivery_priority = isset($this->mkt_feedback[$obj['Order']['delivery_priority']])? $this->mkt_feedback[$obj['Order']['delivery_priority']] : '';
                            $bdc_feedback = isset($this->ops_feedback[$obj['Order']['bdc_feedback']])? $this->ops_feedback[$obj['Order']['bdc_feedback']] : '';
                            $finance_approval = isset($this->fna_feedback[$obj['Order']['finance_approval']])? $this->fna_feedback[$obj['Order']['finance_approval']] : '';

                            $return_arr[] = array(
                                'id' => $obj['Order']['id'],
                                'cell' => array(
                                    $obj['Order']['id'],
                                    $this->covertDate($obj['Order']['order_date'],'mysql_flip'),
                                    $order_time_elapsed,
                                    $obj['OmcCustomer']['name'],
                                    $obj['Omc']['name'],
                                    $obj['Depot']['name'],
                                    $obj['ProductType']['name'],
                                    $this->formatNumber( $obj['Order']['order_quantity'],'money',0),
                                    $delivery_priority,
                                    $bdc_feedback,
                                    $finance_approval,
                                    $this->formatNumber($obj['Order']['approved_quantity'],'money',0)
                                ),
                                'extra_data' => array(//Sometime u need certain data to be stored on the main tr at the client side like the referencing table id for editing
                                    'record_origin'=>$obj['Order']['record_origin'],
                                    'order_status'=>$obj['Order']['order_status'],
                                    'ops_feedback'=>$obj['Order']['bdc_feedback'],
                                    'fna_feedback'=>$obj['Order']['finance_approval'],
                                    'order_quantity'=>preg_replace('/,/','',$obj['Order']['order_quantity']),
                                    'omc_id'=>$obj['Order']['omc_id'],
                                    'omc_name'=>$obj['Omc']['name']
                                ),
                                'property'=>array(
                                    'bg_color'=>$bg_color,
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
                    $data = array('Order' => $_POST);
                    $data['Order']['bdc_modified'] = date('Y-m-d H:i:s');
                    $data['Order']['bdc_modified_by'] = $authUser['id'];
                    $prev_ops_feedback = $_POST['extra']['ops_feedback'];
                    $fna_feedback = $_POST['extra']['fna_feedback'];
                    $omc_id = $_POST['extra']['omc_id'];
                    $omc_name = $_POST['extra']['omc_name'];
                    $prev_order_status = $_POST['extra']['order_status'];

                    if($_POST['finance_approval'] == 'Approved'){
                        $data['Order']['row_bg_color'] = 'tr_yellow';
                        $data['Order']['order_status'] = 'Processing';
                    }
                    elseif($_POST['finance_approval'] == 'Not Approved'){
                        $data['Order']['row_bg_color'] = 'tr_red';
                        $data['Order']['order_status'] = 'Cancelled';
                        $data['Order']['edit_row'] = 'no';
                    }
                    elseif($_POST['finance_approval'] == 'N/A'){
                        // $data['Order']['row_bg_color'] = 'tr_blue';
                        // $data['Order']['order_status'] = 'New';
                    }


                    if ($this->Order->save($this->sanitize($data))) {
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
            }
        }

        $fna_feedback = array();
        foreach($this->fna_feedback as $key => $value){
            $fna_feedback[] = array(
                'id'=>$key,
                'name'=>$value
            );
        }

        $omclists_data = $this->get_omc_list();
        $omc_lists =array(array('name'=>'All','value'=>0));
        foreach($omclists_data as $arr){
            $omc_lists[] = array('name'=>$arr['name'],'value'=>$arr['id']);
        }
        $order_filter = $this->order_filter;

        $start_dt = date('Y-m-1');
        $end_dt = date('Y-m-t');
        $group_by = 'monthly';
        $group_by_title = date('F');

        $g_data =  $this->get_orders($start_dt,$end_dt,$group_by,null,null);

        $graph_title = $group_by_title." Consolidated Orders";
        $volumes = $this->Volume->getVolsList();

        $this->set(compact('fna_feedback','user_type','g_data','graph_title','omc_lists','order_filter','volumes'));
    }


    function orders_marketing($type = 'get')
    {
        $user = $this->Auth->user('BdcUser');
        $user_type = $user['bdc_user_type'];
        $company_profile = $this->global_company;
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
                    $filter_status =   isset($_POST['filter_status']) ? $_POST['filter_status'] : 'incomplete_orders' ;
                    /** Search string */
                    $rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
                    $limit = $rp;
                    $start = ($page - 1) * $rp;

                    //get users id for this company only
                    $condition_array = array(
                        'Order.bdc_id' => $company_profile['id'],
                        'Order.record_type' => 'bdc',
                        'Order.deleted' => 'n',
                    );
                    if($filter != 0){
                        $condition_array['Order.omc_id'] = $filter;
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
                        'Omc'=>array('fields' => array('Omc.id', 'Omc.name')),
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
                            if($obj['Order']['order_status'] == 'Complete' || $obj['Order']['order_status'] == 'Cancelled'){
                                $bigger_time = $obj['Order']['bdc_modified'];
                                $time_hr = $this->count_time_between_dates($obj['Order']['omc_created'],$bigger_time,'hours');
                                // $time_days = $this->count_time_between_dates($obj['Order']['omc_created'],$bigger_time,'days');
                                $order_time_elapsed = $time_hr.' hr(s)';
                                $edit_row = 'no';
                            }
                            else{
                                $time_hr = $this->count_time_between_dates($obj['Order']['omc_created'],$bigger_time,'hours');
                                // $time_days = $this->count_time_between_dates($obj['Order']['omc_created'],$bigger_time,'days');
                                $order_time_elapsed =  $time_hr.' hr(s)';
                                $edit_row = 'yes';
                            }
                            //has CEPS Approved ?
                            if($obj['Order']['ceps_approval'] == 'Approved'){
                                $edit_row = 'no';
                            }


                            $bg_color = $obj['Order']['row_bg_color'];
                            $delivery_priority = isset($this->mkt_feedback[$obj['Order']['delivery_priority']])? $this->mkt_feedback[$obj['Order']['delivery_priority']] : '';
                            $bdc_feedback = isset($this->ops_feedback[$obj['Order']['bdc_feedback']])? $this->ops_feedback[$obj['Order']['bdc_feedback']] : '';
                            $finance_approval = isset($this->fna_feedback[$obj['Order']['finance_approval']])? $this->fna_feedback[$obj['Order']['finance_approval']] : '';

                            $return_arr[] = array(
                                'id' => $obj['Order']['id'],
                                'cell' => array(
                                    $obj['Order']['id'],
                                    $this->covertDate($obj['Order']['order_date'],'mysql_flip'),
                                    $order_time_elapsed,
                                    $obj['OmcCustomer']['name'],
                                    $obj['Omc']['name'],
                                    $obj['Depot']['name'],
                                    $obj['ProductType']['name'],
                                    $this->formatNumber( $obj['Order']['order_quantity'],'money',0),
                                    $delivery_priority,
                                    $bdc_feedback,
                                    $finance_approval,
                                    $this->formatNumber( $obj['Order']['approved_quantity'],'money',0)
                                ),
                                'extra_data' => array(//Sometime u need certain data to be stored on the main tr at the client side like the referencing table id for editing
                                    'record_origin'=>$obj['Order']['record_origin'],
                                    'order_status'=>$obj['Order']['order_status'],
                                    'ops_feedback'=>$obj['Order']['bdc_feedback'],
                                    'fna_feedback'=>$obj['Order']['finance_approval'],
                                    'order_quantity'=>preg_replace('/,/','',$obj['Order']['order_quantity']),
                                    'omc_id'=>$obj['Order']['omc_id'],
                                    'omc_name'=>$obj['Omc']['name']
                                ),
                                'property'=>array(
                                    'bg_color'=>$bg_color,
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
                    $data = array('Order' => $_POST);
                    $data['Order']['bdc_modified'] = date('Y-m-d H:i:s');
                    $data['Order']['bdc_modified_by'] = $authUser['id'];
                    $prev_ops_feedback = $_POST['extra']['ops_feedback'];
                    $fna_feedback = $_POST['extra']['fna_feedback'];
                    $omc_id = $_POST['extra']['omc_id'];
                    $omc_name = $_POST['extra']['omc_name'];

                    if ($this->Order->save($this->sanitize($data))) {

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
            }
        }

        $mkt_feedback = array();
        foreach($this->mkt_feedback as $key => $value){
            $mkt_feedback[] = array(
                'id'=>$key,
                'name'=>$value
            );
        }

        $omclists_data = $this->get_omc_list();
        $omc_lists =array(array('name'=>'All','value'=>0));
        foreach($omclists_data as $arr){
            $omc_lists[] = array('name'=>$arr['name'],'value'=>$arr['id']);
        }
        $order_filter = $this->order_filter;

        $start_dt = date('Y-m-1');
        $end_dt = date('Y-m-t');
        $group_by = 'monthly';
        $group_by_title = date('F');

        $g_data =  $this->get_orders($start_dt,$end_dt,$group_by,null,null);
        //debug($g_data);
        $graph_title = $group_by_title." Consolidated Orders";

        $this->set(compact('mkt_feedback','user_type','g_data','graph_title','omc_lists','order_filter'));
    }


    function export_orders(){
        $download = false;
        $company_profile = $this->global_company;
        if($this->request->is('post')){
            $export_params = $this->request->data;
            $start_dt =  $this->covertDate($export_params['exp_startdt'],'mysql').' 00:00:00';
            $end_dt = $this->covertDate($export_params['exp_enddt'],'mysql').' 23:59:59';
            $export_filter_omc = $export_params['exp_filter_omc'];
            $export_filter_status = isset($export_params['exp_filter_status'])? $export_params['exp_filter_status'] : 'complete_orders';
            $conditions = array(
                'Order.bdc_id' => $company_profile['id'],
                'Order.deleted' => 'n',
                'Order.record_type' => 'bdc',
                'Order.order_date >=' => $start_dt, 'Order.order_date <=' => $end_dt
            );
            if($export_filter_omc != 0){
                $conditions['Order.omc_id'] = $export_filter_omc;
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
                'Omc'=>array('fields' => array('Omc.id', 'Omc.name')),
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
                        $obj['Omc']['name'],
                        $obj['Depot']['name'],
                        $obj['ProductType']['name'],
                        preg_replace('/,/','',$obj['Order']['order_quantity']),
                        $status,
                        $delivery_priority,
                        $bdc_feedback,
                        $finance_approval,
                       /* $obj['Order']['delivery_priority'],
                        $obj['Order']['bdc_feedback'],
                        $obj['Order']['finance_approval'],*/
                         preg_replace('/,/','',$obj['Order']['approved_quantity']),
                    );
                }
                $list_headers = array('Order Id','Order Date','Customer','OMC','Loading Depot','Product Type','Order Quantity','Status','Delivery Priority','BDC Feedback','BDC Finance Approval','Approved Quantity');
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
                        'Waybill.bdc_id' => $company_profile['id'],
                        'Waybill.deleted' => 'n'
                    );

                    if($filter != 0){
                        $condition_array['Waybill.depot_id'] = $filter;
                    }

                    if($filter_status == 'Not Yet Approved'){
                        $condition_array['Waybill.bdc_approval'] = 'Not Yet Approved';
                    }
                    else{
                        $condition_array['Waybill.bdc_approval'] = $filter_status;
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
                            $approve_btn = "Approved";
                            if($obj['Waybill']['bdc_approval'] == 'Approved'){
                                //$bigger_time = $obj['Order']['bdc_modified'];
                                //$time_hr = $this->count_time_between_dates($obj['Order']['omc_created'],$bigger_time,'hours');
                                // $time_days = $this->count_time_between_dates($obj['Order']['omc_created'],$bigger_time,'days');
                                //$order_time_elapsed = $time_hr.' hr(s)';
                                $edit_row = 'no';
                                $approve_btn = "Approved";
                            }
                            else{
                                //$time_hr = $this->count_time_between_dates($obj['Order']['omc_created'],$bigger_time,'hours');
                                // $time_days = $this->count_time_between_dates($obj['Order']['omc_created'],$bigger_time,'days');
                                //$order_time_elapsed =  $time_hr.' hr(s)';
                                $edit_row = 'yes';
                                if(in_array('E',$permissions)){
                                    $approve_btn = "<button class='btn btn-mini approve-waybill' data-id='".$waybill_id."' data-order-id='".$order_id."'>Approve</button>";
                                }
                                else{
                                    $approve_btn = "Not Yet Approved";
                                }

                            }

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
                                    $approve_btn,
                                    $obj['Depot']['name']." ($depot_waybill_feedback)",
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