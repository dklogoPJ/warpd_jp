<?php

/**
 * @name BdcController.php
 */
App::import('Controller', 'CepsApp');

class CepsController extends CepsAppController
{
    # Controller name

    var $name = 'Ceps';
    # set the model to use
    var $uses = array('Bdc','Cep','Order','Depot','ProductType','Volume','Waybill');
    # Set the layout to use
    var $layout = 'ceps_layout';

    public function beforeFilter($param_array = null)
    {
        parent::beforeFilter();
    }

    public function index() {
        $this->redirect('approve_orders');
    }

    function dashboard(){
        $comp = $this->global_company;
        $group_depot = $comp['depot_id'];
        $loading_board = $this->get_loading_board($group_depot);
        $loaded_board = $this->get_loaded_board($group_depot);

        $this->set(compact('loading_board','loaded_board'));
    }

    function approve_orders($type = 'get')
    {
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
                    //$filter =   isset($_POST['filter']) ? $_POST['filter'] : 0 ;
                    $filter_status =   isset($_POST['filter_status']) ? $_POST['filter_status'] : 'Not Approved' ;
                    /** Search string */
                    $rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
                    $limit = $rp;
                    $start = ($page - 1) * $rp;

                    $condition_array = array(
                        'Order.cep_id' => $company_profile['id'],
                        'Order.bdc_feedback' => 'Approved',
                        'Order.record_type' => 'bdc',
                        'Order.deleted' => 'n'
                    );

                    if($filter_status == 'Not Approved'){
                        $condition_array['OR'] = array('Order.ceps_approval'=>'Not Approved','Order.ceps_approval'=>NULL);
                    }
                    else{
                        $condition_array['Order.ceps_approval'] = 'Approved';
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
                            if($obj['Order']['ceps_approval'] == 'Approved'){
                                $bigger_time = $obj['Order']['bdc_modified'];
                                //$time_hr = $this->count_time_between_dates($obj['Order']['omc_created'],$bigger_time,'hours');
                                // $time_days = $this->count_time_between_dates($obj['Order']['omc_created'],$bigger_time,'days');
                                //$order_time_elapsed = $time_hr.' hr(s)';
                                $edit_row = 'no';
                                $bg_color = '';
                            }
                            else{
                                //$time_hr = $this->count_time_between_dates($obj['Order']['omc_created'],$bigger_time,'hours');
                                // $time_days = $this->count_time_between_dates($obj['Order']['omc_created'],$bigger_time,'days');
                                //$order_time_elapsed =  $time_hr.' hr(s)';
                                $edit_row = 'yes';
                                $bg_color = $obj['Order']['row_bg_color'];
                            }

                            $cep_feedback = isset($this->ceps_feedback[$obj['Order']['ceps_approval']])? $this->ceps_feedback[$obj['Order']['ceps_approval']] : '';

                            $return_arr[] = array(
                                'id' => $obj['Order']['id'],
                                'cell' => array(
                                    $obj['Order']['id'],
                                    $this->covertDate($obj['Order']['order_date'],'mysql_flip'),
                                    //$order_time_elapsed,
                                    //$obj['OmcCustomer']['name'],
                                    $obj['Bdc']['name'],
                                    $obj['Omc']['name'],
                                    $obj['Depot']['name'],
                                    $obj['ProductType']['name'],
                                    $this->formatNumber( $obj['Order']['approved_quantity'],'money',0),
                                    $cep_feedback
                                ),
                                'extra_data' => array(//Sometime u need certain data to be stored on the main tr at the client side like the referencing table id for editing
                                    'order_quantity'=>preg_replace('/,/','',$obj['Order']['order_quantity']),
                                    'bdc_id'=>$obj['Bdc']['id'],
                                    'omc_id'=>$obj['Omc']['id'],
                                    'depot_id'=>$obj['Depot']['id'],
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
                    $data['Order']['ceps_modified'] = date('Y-m-d H:i:s');
                    $data['Order']['ceps_modified_by'] = $authUser['id'];
                    $omc_id = $_POST['extra']['omc_id'];
                    $bdc_id = $_POST['extra']['bdc_id'];
                    $depot_id = $_POST['extra']['depot_id'];

                    if($_POST['ceps_approval'] == 'Not Approved'){
                        $data['Order']['row_bg_color'] = 'tr_red';
                        $data['Order']['order_status'] = 'Cancelled';
                        $data['Order']['edit_row'] = 'no';
                    }
                    elseif($_POST['ceps_approval'] == 'Approved'){
                        $data['Order']['row_bg_color'] = 'tr_green';
                        $data['Order']['order_status'] = 'Pending Loading';
                    }
                    //Once ceps approves then the depot ppl can see the order

                    if ($this->Order->save($this->sanitize($data))) {

                        //Notify OMC
                        $send_params = array(
                            'title'=>$company_profile['name'].', Order Approved',
                            'content'=>'Order number '. $data['Order']['id'].' has been approved for loading ',
                            'sender'=>$authUser['id'],
                            'sender_type'=>'blast',
                            'entity'=>'omc',
                            'entity_id'=>$omc_id,
                            'include_this_entity'=>false,
                            'msg_type'=>'system'
                        );
                        $this->sendMessage($send_params);

                        //Notify BDC
                        $send_params = array(
                            'title'=>$company_profile['name'].', Order Approved',
                            'content'=>'Order number '. $data['Order']['id'].' has been approved for loading ',
                            'sender'=>$authUser['id'],
                            'sender_type'=>'blast',
                            'entity'=>'bdc',
                            'entity_id'=>$bdc_id,
                            'include_this_entity'=>false,
                            'msg_type'=>'system'
                        );
                        $this->sendMessage($send_params);

                        //Notify Depot
                        $send_params = array(
                            'title'=>$company_profile['name'].', Order Approved',
                            'content'=>'Order number '. $data['Order']['id'].' has been approved for loading ',
                            'sender'=>$authUser['id'],
                            'sender_type'=>'blast',
                            'entity'=>'depot',
                            'entity_id'=>$depot_id,
                            'include_this_entity'=>false,
                            'msg_type'=>'system'
                        );
                        $this->sendMessage($send_params);


                        //Activity Log
                        $log_description = $this->getLogMessage('ApproveOrder')." (Order number ".$data['Order']['id']." For Loading)";
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
            }
        }

        $ceps_feedback = array();
        $ceps_filter = array();
        foreach($this->ceps_feedback as $key => $value){
            $ceps_feedback[] = array(
                'id'=>$key,
                'name'=>$value
            );
            $ceps_filter[] = array(
                'name'=>$key,
                'value'=>$value
            );
        }
        $ceps_filter = array_reverse($ceps_filter);

        $this->set(compact('ceps_feedback','ceps_filter'));
    }


    function export_orders(){
        $download = false;
        $company_profile = $this->global_company;
        if($this->request->is('post')){
            $export_params = $this->request->data;

            $start_dt =  $this->covertDate($export_params['exp_startdt'],'mysql').' 00:00:00';
            $end_dt = $this->covertDate($export_params['exp_enddt'],'mysql').' 23:59:59';
            //$export_filter_omc = $export_params['exp_filter_omc'];
            $export_filter_status = isset($export_params['exp_filter_status'])? $export_params['exp_filter_status'] : 'Not Approved';
            $conditions = array(
                'Order.cep_id' => $company_profile['id'],
                'Order.bdc_feedback' => 'Approved',
                'Order.record_type' => 'bdc',
                'Order.deleted' => 'n',
                'Order.order_date >=' => $start_dt, 'Order.order_date <=' => $end_dt
            );

            if($export_filter_status == 'Not Approved'){
                $conditions['OR'] = array('Order.ceps_approval'=>'Not Approved','Order.ceps_approval'=>NULL);
            }
            else{
                $conditions['Order.ceps_approval'] = 'Approved';
            }

            $contain = array(
                'Bdc'=>array('fields' => array('Bdc.id', 'Bdc.name')),
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
                    /*$delivery_priority = isset($this->mkt_feedback[$obj['Order']['delivery_priority']])? $this->mkt_feedback[$obj['Order']['delivery_priority']] : '';
                    $bdc_feedback = isset($this->ops_feedback[$obj['Order']['bdc_feedback']])? $this->ops_feedback[$obj['Order']['bdc_feedback']] : '';
                    $finance_approval = isset($this->fna_feedback[$obj['Order']['finance_approval']])? $this->fna_feedback[$obj['Order']['finance_approval']] : '';*/
                    $ceps_approval = isset($this->ceps_feedback[$obj['Order']['ceps_approval']])? $this->ceps_feedback[$obj['Order']['ceps_approval']] : '';
                    $list_data[] = array(
                        $obj['Order']['id'],
                        $this->covertDate($obj['Order']['order_date'],'mysql_flip'),
                        $obj['Bdc']['name'],
                        $obj['Omc']['name'],
                        $obj['Depot']['name'],
                        $obj['ProductType']['name'],
                        preg_replace('/,/','',$obj['Order']['approved_quantity']),
                        $ceps_approval,
                    );
                }
                $list_headers = array('Order Id','Order Date','BDC','OMC','Loading Depot','Product Type','Quantity','Status');
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
                        'Waybill.cep_id' => $company_profile['id'],
                        'Waybill.deleted' => 'n'
                    );

                    if($filter != 0){
                        $condition_array['Waybill.bdc_id'] = $filter;
                    }

                    if($filter_status == 'Not Yet Approved'){
                        $condition_array['Waybill.ceps_approval'] = 'Not Yet Approved';
                    }
                    else{
                        $condition_array['Waybill.ceps_approval'] = $filter_status;
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
                            if($obj['Waybill']['ceps_approval'] == 'Approved'){
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
                                    $obj['Bdc']['name']." ($bdc_waybill_feedback)"

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
                    $data['Waybill']['ceps_approval'] = 'Approved';
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

                        //Notify BDC
                        $send_params = array(
                            'title'=>$company_profile['name'].', Way Bill Endorsed',
                            'content'=>'Way Bill number '.$_POST['id'].' has been endorsed',
                            'sender'=>$authUser['id'],
                            'sender_type'=>'blast',
                            'entity'=>'bdc',
                            'entity_id'=>$bdc_id,
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
        $bdc_lists = $this->get_all_bdc_list();
        $bdc_filter = array( array('name'=>'All','value'=>0));
        foreach($bdc_lists as $key => $value){
            $bdc_filter[] = array(
                'name'=>$value['name'],
                'value'=>$value['id']
            );
        }

        $this->set(compact('waybill_feedback','waybill_filter','bdc_filter'));
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