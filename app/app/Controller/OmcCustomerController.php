<?php

/**
 * @name OmcController.php
 */
App::import('Controller', 'OmcCustomerApp');

class OmcCustomerController extends OmcCustomerAppController
{
    # Controller name

    var $name = 'OmcCustomer';
    # set the model to use
    var $uses = array('OmcBdcDistribution', 'OmcCustomerDistribution','OmcCustomer', 'User', 'District', 'ProductType', 'Region','OmcCashCreditSummary','OmcDailySalesProduct','OmcBulkStockCalculation','Volume','OmcCustomerOrder','PumpTankSale', 'OmcCustomerPriceChange','CustomerCreditSetting','CustomerCredit','CustomerCreditPayment','Nct','OmcCustomerReport');

    # Set the layout to use
    var $layout = 'omc_customer_layout';

    # Bdc ids this user will work with only
    var $user_bdc_ids = array();

    public function beforeFilter($param_array = null)
    {
        parent::beforeFilter();
    }

    function dashboard(){
        $company_profile = $this->global_company;
        $date = date('Y-m-d');
		//Total Daily Sales Liters.
		$dsl_bar_data = $this->OmcCustomerReport->getDailySalesLiters($company_profile['id'], $company_profile['omc_id'], '2023-08-13');
		//Total Daily Sales Cedis.
		$dsc_bar_data = $this->OmcCustomerReport->getDailySalesCedis($company_profile['id'], $company_profile['omc_id'], $date);
		//Stock Calculation.
		$sc_bar_data = $this->OmcCustomerReport->getStockCalculation($company_profile['id'], $company_profile['omc_id'], $date);



        $last_stock_updates = $this->getStockBoard();
        $widget_data_cash_credit_summary = array();
        $widget_daily_sales_product = array();
        $pie_daily_sales_product = array();
        foreach($widget_daily_sales_product as $row){
            if($row['value'] != null){
                $pie_daily_sales_product[]= array(
                    $row['header'],floatval($row['value'])
                );
            }
        }
        $widget_bulk_stock_calc = array();
        $bar_data = array(
            'x-axis'=>array(),
            'series'=>array(
                array('name'=>'Meter Reading','data'=>array()),
                array('name'=>'Dipping','data'=>array())
            )
        );
        foreach($widget_bulk_stock_calc as $row){
            if($row['closing_stock'] != null && $row['dipping'] != null){
                //$bar_data['x-axis'][]= $row['products'];
                $bar_data['series'][0]['data'][]= floatval($row['closing_stock']);//meter_reading
                $bar_data['series'][1]['data'][]= floatval($row['dipping']);//dipping
            }
        }
        $format_date =  date('l jS F Y',strtotime($date));
        $this->set(compact('format_date', 'dsl_bar_data','dsc_bar_data','sc_bar_data','last_stock_updates','widget_data_cash_credit_summary','pie_daily_sales_product','bar_data'));
    }

    function index($type = 'get')
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
                    $data_table = $this->OmcBdcDistribution->find('all', array('conditions' => $condition_array, 'contain'=>$contain,'order' => "OmcBdcDistribution.$sortname $sortorder", 'page' => $page  , 'limit'=> $limit, 'recursive' => 2));
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
                                    $obj['OmcBdcDistribution']['quantity'],
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


    function price_change($type = 'get')
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
                    /** @var $filter  */
                    $filter_depot =   isset($_POST['filter_depot']) ? $_POST['filter_depot'] : 0 ;
                    $filter_region =   isset($_POST['filter_region']) ? $_POST['filter_region'] : 0 ;
                    /** Search column */
                    $search_query = isset($_POST['query']) ? $_POST['query'] : '';
                    /** Search string */
                    $rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
                    $limit = $rp;
                    $start = ($page - 1) * $rp;

                    $condition_array = array('OmcCustomerPriceChange.omc_customer_id' => $company_profile['id'],'OmcCustomerPriceChange.deleted' => 'n');

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
                        'ProductType'=>array('fields' => array('ProductType.id', 'ProductType.name'))
                    );

                    $data_table = $this->OmcCustomerPriceChange->find('all', array('conditions' => $condition_array, 'contain'=>$contain,'order' => "OmcCustomerPriceChange.$sortname $sortorder", 'offset'=>$start,'limit' => $limit, 'recursive' => 1));
                    $data_table_count = $this->OmcCustomerPriceChange->find('count', array('conditions' => $condition_array, 'recursive' => -1));

                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {
                            $return_arr[] = array(
                                'id' => $obj['OmcCustomerPriceChange']['id'],
                                'cell' => array(
                                    $obj['OmcCustomerPriceChange']['id'],
                                    $obj['ProductType']['name'],
                                    $obj['OmcCustomerPriceChange']['description'],
                                    $obj['OmcCustomerPriceChange']['price'],
                                    $obj['OmcCustomerPriceChange']['unit']
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
                    //check if username does not exist for in this company
                    $data = array('OmcCustomerPriceChange' => $_POST);
                    $data['OmcCustomerPriceChange']['modified_by'] = $this->Auth->user('id');
                    if ($_POST['id'] == 0) {
                        $data['OmcCustomerPriceChange']['created_by'] = $this->Auth->user('id');
                        $data['OmcCustomerPriceChange']['omc_customer_id'] =  $company_profile['id'];
                    }

                    if ($this->OmcCustomerPriceChange->save($this->sanitize($data))) {
                        if($_POST['id'] > 0){
                            $new_user = $_POST['id'];
                            $log_description = $this->getLogMessage('ModifyOmcCustomerPriceChange')." (Price Change ID: ".$new_user.")";
                            $this->logActivity('Administration',$log_description);

                            return json_encode(array('code' => 0, 'msg' => 'Data Saved'));
                        }
                        else{ //If new pass back extra data if needed.
                            $new_user = $this->OmcCustomerPriceChange->id;
                            $log_description = $this->getLogMessage('CreateOmcCustomerPriceChange')." (Price Change ID: ".$new_user.")";
                            $this->logActivity('Administration',$log_description);

                            return json_encode(array('code' => 0, 'msg' => 'Data Saved.', 'id'=>$this->OmcCustomerPriceChange->id));
                        }
                        //echo json_encode(array('success' => 0, 'msg' => 'Data Saved!', 'data' => $dt));
                    } else {
                        echo json_encode(array('code' => 1, 'msg' => 'Some errors occurred.'));
                    }

                    break;

                case 'delete':
                    if(!in_array('D',$permissions)){
                        return json_encode(array('code' => 1, 'msg' => 'Access Denied.'));
                    }

                    $ids = $_POST['ids'];
                    $modObj = ClassRegistry::init('OmcCustomerPriceChange');
                    $now = "'".date('Y-m-d H:i:s')."'";
                    $result = $modObj->updateAll(
                        $this->sanitize(array('OmcCustomerPriceChange.deleted' => "'y'",'OmcCustomerPriceChange.modified' => "$now",'OmcCustomerPriceChange.modified_by' => $this->Auth->user('id'))),
                        $this->sanitize(array('OmcCustomerPriceChange.id' => $ids))
                    );
                    if ($result) {
                        echo json_encode(array('code' => 0, 'msg' => 'Data Deleted!'));
                    } else {
                        echo json_encode(array('code' => 1, 'msg' => 'Data cannot be deleted'));
                    }
                    break;
            }
        }


        // $product_list_data = $this->get_product_list();
        $product_list = $this->get_products();
        // debug($product_list_data);
        //$product_group_list = array('all'=>'All Product Type');
        /* $product_list = array();
         foreach($product_list_data as $arr){
             $product_list[] = array('name'=>$arr['name'],'id'=>$arr['id']);
         }*/
        // debug($product_list);
        $controller = $this;
        $this->set(compact('controller', 'product_list'));
    }

    function export_price_change(){
        $download = false;
        $company_profile = $this->global_company;

        $export_data = $this->OmcCustomerPriceChange->find('all', array(
            //'fields'=>array('OmcCustomer.id','OmcCustomer.order_status','BdcDistribution.loading_date','BdcDistribution.waybill_date','BdcDistribution.collection_order_no','BdcDistribution.quantity','BdcDistribution.waybill_id','BdcDistribution.vehicle_no'),
            'conditions' => array('OmcCustomerPriceChange.omc_customer_id' => $company_profile['id'],'OmcCustomerPriceChange.deleted' => 'n'),
            'contain'=>array(
                'ProductType'=>array('fields'=>array('ProductType.id','ProductType.name'))
            ),
            'order'=>array('OmcCustomerPriceChange.id'=>'desc'),
            'recursive' => 1
        ));

        //debug($export_data);
        if ($export_data) {
            $download = true;
            $list_data = array();
            foreach ($export_data as $obj) {
                $list_data[] = array(
                    $obj['ProductType']['name'],
                    $obj['OmcCustomerPriceChange']['price']
                );
            }
            $list_headers = array('Product','Price');
            $filename ="Price Change ".date('Ymdhis');
            $res = $this->convertToExcel($list_headers,$list_data,$filename);
            $objPHPExcel = $res['excel_obj'];
            $filename = $res['filename'];
        }

        $this->autoLayout = false;

        $this->set(compact('objPHPExcel', 'download', 'filename'));
    }

    function daily_truck_view($type = 'get')
    {


    }



    function export_loading_data(){
        $download = false;
        $company_profile = $this->global_company;;
        if($this->request->is('post')){
            //debug($this->request->data);
            if($this->request->data['Export']['action'] == 'export_me'){
                $start_dt = $this->covertDate($this->request->data['Export']['export_startdt'],'mysql').' 00:00:00';
                $end_dt = $this->covertDate($this->request->data['Export']['export_enddt'],'mysql').' 23:59:59';
                $type = $this->request->data['Export']['export_type'];

                /*$export_data = $this->BdcDistribution->find('all', array(
                    'fields'=>array('BdcDistribution.id','BdcDistribution.loading_date','BdcDistribution.waybill_date','BdcDistribution.quantity','BdcDistribution.waybill_id','BdcDistribution.vehicle_no'),
                    'conditions' => array('BdcDistribution.omc_id' => $company_profile['id'], 'BdcDistribution.order_status' => 'Complete', 'BdcDistribution.deleted' => 'n','BdcDistribution.loading_date >=' => $start_dt, 'BdcDistribution.loading_date <=' => $end_dt),
                    'contain'=>array(
                        'OmcBdcDistribution'=>array(
                            'fields'=>array('OmcBdcDistribution.id','OmcBdcDistribution.quantity'),
                            'OmcCustomer'=>array(
                                'fields'=>array('OmcCustomer.id','OmcCustomer.name')
                            ),
                            'DeliveryLocation'=>array('fields'=>array('DeliveryLocation.id','DeliveryLocation.name')),
                            'Region'=>array('fields'=>array('Region.id','Region.name'))
                        ),
                        'Bdc'=>array('fields'=>array('Bdc.id','Bdc.name')),
                        'Depot'=>array('fields'=>array('Depot.id','Depot.name')),
                        'ProductType'=>array('fields'=>array('ProductType.id','ProductType.name')),
                    ),
                    'order' => array("BdcDistribution.id"=>'desc'),
                ));*/

                $export_data = $this->OmcBdcDistribution->find('all', array(
                    'conditions' => array('OmcBdcDistribution.omc_customer_id' => $company_profile['id'], 'OmcBdcDistribution.deleted' => 'n', 'BdcDistribution.loading_date >=' => $start_dt, 'BdcDistribution.loading_date <=' => $end_dt),
                    'contain'=>array(
                        'BdcDistribution'=>array(
                            'fields'=>array('BdcDistribution.id','BdcDistribution.loading_date','BdcDistribution.vehicle_no'),
                            'Depot'=>array('fields'=>array('Depot.id','Depot.name')),
                            'ProductType'=>array('fields'=>array('ProductType.id','ProductType.name')),
                        ),
                        'OmcCustomerDistribution'=>array(
                            'Region'=>array('fields'=>array('Region.id','Region.name'))
                        ),
                        'Region'=>array('fields' => array('Region.id', 'Region.name')),
                        'DeliveryLocation'=>array('fields' => array('DeliveryLocation.id', 'DeliveryLocation.name'))
                    ),
                    'order' => array("OmcBdcDistribution.id"=>'desc'),
                ));

                //debug($export_data);

                if ($export_data) {
                    $download = true;
                    $list_data = array();
                    foreach ($export_data as $value) {
                        $master_row = array(
                            $this->covertDate($value['BdcDistribution']['loading_date'],'mysql_flip'),
                            $value['OmcBdcDistribution']['invoice_number'],
                            $value['BdcDistribution']['ProductType']['name'],
                            preg_replace('/,/','',$value['OmcBdcDistribution']['quantity']),
                            $value['DeliveryLocation']['name'],
                            $value['OmcBdcDistribution']['transporter'],
                            $value['BdcDistribution']['vehicle_no']
                        );
                        //Add the omc record if any
                        if($value['OmcCustomerDistribution']){
                            foreach ($value['OmcCustomerDistribution'] as $omcdb) {
                                $copy_master = $master_row;
                                $copy_master[] = $omcdb['customer'];
                                $copy_master[] = preg_replace('/,/','',$omcdb['quantity']);
                                $copy_master[] = isset($omcdb['Region']['name'])?$omcdb['Region']['name']:'';
                                $copy_master[] = isset($omcdb['location'])? ucwords(strtolower($omcdb['location'])): '';
                                $copy_master[] = $omcdb['transporter'];
                                $list_data[] = $copy_master;
                            }
                        }
                        else{
                            $list_data[] = $master_row;
                        }
                    }
                    $list_headers = array('Loading Date','Invoice','Product Type','Quantity','Delivery Location','Transporter','Vehicle No.','Customer Name','Quantity Delivered','Region','Location','Transporter');
                    //$list_headers = array('Date','Waybill No.','From','Depot','Product Type','Actual Quantity','Vehicle No.','Customer Name','Quantity Delivered','Delivery Location','Region','District');
                    $filename = $company_profile['name']." Daily View ".date('Ymdhis');
                    $res = $this->convertToExcel($list_headers,$list_data,$filename);
                    $objPHPExcel = $res['excel_obj'];
                    $filename = $res['filename'];
                }
            }
        }

        $this->autoLayout = false;

        $this->set(compact('objPHPExcel', 'download', 'filename'));
    }


    function pump_tank_sales($type = 'get')
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
                    $filter_status =   isset($_POST['filter_status']) ? $_POST['filter_status'] : 'complete_orders' ;
                    /** Search string */
                    $rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
                    $limit = $rp;
                    $start = ($page - 1) * $rp;

                    //get users id for this company only
                    $condition_array = array(
                        'PumpTankSale.omc_customer_id' => $company_profile['id'],
                        'PumpTankSale.deleted' => 'n'
                    );

                    if (!empty($search_query)) {
                        if ($qtype == 'id') {
                            $condition_array['PumpTankSale.id'] = $search_query;
                        }
                        else {
                            /* $condition_array = array(
                                 "User.$qtype LIKE" => $search_query . '%',
                                 'User.deleted' => 'n'
                             );*/
                        }
                    }

                    $contain = array(
                        'OmcCustomer'=>array('fields' => array('OmcCustomer.id', 'OmcCustomer.name'))
                    );

                    $data_table = $this->PumpTankSale->find('all', array('conditions' => $condition_array, 'contain'=>$contain,'order' => "PumpTankSale.$sortname $sortorder", 'page' => $page  , 'limit'=> $limit, 'recursive' => 1));
                    $data_table_count = $this->PumpTankSale->find('count', array('conditions' => $condition_array, 'recursive' => -1));
                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {

                            $received_quantity =  isset($obj['PumpTankSale']['received_quantity']) ? $this->formatNumber($obj['PumpTankSale']['received_quantity'],'number',0) : '';

                            $return_arr[] = array(
                                'id' => $obj['PumpTankSale']['id'],
                                'cell' => array(
                                    $obj['PumpTankSale']['id'],
                                    $obj['PumpTankSale']['tank'],
                                    isset($obj['PumpTankSale']['open_stock']) ? $this->formatNumber($obj['PumpTankSale']['open_stock'],'number',0) : '',
                                    $received_quantity,
                                    isset($obj['PumpTankSale']['stock_in_hand']) ? $this->formatNumber($obj['PumpTankSale']['stock_in_hand'],'number',0) : '',
                                    isset($obj['PumpTankSale']['pump_day_sales']) ? $this->formatNumber($obj['PumpTankSale']['pump_day_sales'],'number',0) : '',
                                    isset($obj['PumpTankSale']['closing_stock']) ? $this->formatNumber($obj['PumpTankSale']['closing_stock'],'number',0) : '',
                                    isset($obj['PumpTankSale']['tank_day_sales']) ? $this->formatNumber($obj['PumpTankSale']['tank_day_sales'],'number',0) : '',
                                    isset($obj['PumpTankSale']['variance']) ? $this->formatNumber($obj['PumpTankSale']['variance'],'number',0) : '',
                                    isset($obj['PumpTankSale']['variance_cedis']) ? $this->formatNumber($obj['PumpTankSale']['variance_cedis'],'number',0) : '',
                                    $obj['PumpTankSale']['comments']
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
                    $data = array('PumpTankSale' => $_POST);

                    if($_POST['id'] == 0){
                        $data['PumpTankSale']['created_by'] = $authUser['id'];
                    }
                    else{
                        $data['PumpTankSale']['modified_by'] = $authUser['id'];
                    }

                    $data['PumpTankSale']['omc_customer_id'] = $company_profile['id'];
                    $data['PumpTankSale']['received_quantity'] = str_replace(',', '', $_POST['received_quantity']);
                    $data['PumpTankSale']['open_stock'] = str_replace(',', '', $_POST['open_stock']);
                    $data['PumpTankSale']['stock_in_hand'] = str_replace(',', '', $_POST['stock_in_hand']);
                    $data['PumpTankSale']['pump_day_sales'] = str_replace(',', '', $_POST['pump_day_sales']);
                    $data['PumpTankSale']['closing_stock'] = str_replace(',', '', $_POST['closing_stock']);
                    $data['PumpTankSale']['tank_day_sales'] = str_replace(',', '', $_POST['tank_day_sales']);
                    $data['PumpTankSale']['variance'] = str_replace(',', '', $_POST['variance']);
                    $data['PumpTankSale']['variance_cedis'] = str_replace(',', '', $_POST['variance_cedis']);

                    if ($this->PumpTankSale->save($this->sanitize($data))) {
                        $sale_id  = $this->PumpTankSale->id;
                        //Array Data here
                        //Activity Log
                        $log_description = $this->getLogMessage('UpdatePumpTankSale')." (Order #".$sale_id.")";
                        $this->logActivity('Pump Tank Sales',$log_description);

                        if($_POST['id'] > 0){
                            return json_encode(array('code' => 0, 'msg' => 'Data Updated!'));
                        }
                        else{
                            return json_encode(array('code' => 0, 'msg' => 'Data Saved', 'id'=>$sale_id));
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
        $start_dt = date('Y-m-01');
        $end_dt = date('Y-m-t');
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

        $this->set(compact('grid_data','omc_customers_lists','volumes','permissions','depot_lists', 'products_lists','bdc_list','graph_title','g_data','bdclists','order_filter','list_tm'));
    }


    function get_attachments($order_id = null, $attachment_type =null){
        $this->autoRender = false;
        $result = $this->__get_attachments($attachment_type,$order_id);
        $this->attachment_fire_response($result);
    }

    function delete_attachment($attachment_id = null){
        $this->autoRender = false;
        $result = $this->__delete_attachment($attachment_id);
        $this->attachment_fire_response($result);
    }

    function attach_files(){
        $this->autoRender = false;
        $upload_data = $this->__attach_files();
        $this->attachment_fire_response($upload_data);
    }




    function omc_customer_credit_approval($type = 'get'){

        $permissions = $this->action_permission;
        $company_profile = $this->global_company;
        $omc_customer_id = $company_profile['id'];
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
                        'CustomerCredit.omc_customer_id' => $company_profile['id'],
                        'CustomerCredit.deleted' => 'n'
                    );

                    $contain = array(
                        'ProductType'=>array('fields' => array('ProductType.id', 'ProductType.name')),
                        'OmcCustomer'=>array('fields' => array('OmcCustomer.id', 'OmcCustomer.name')),
                        'CustomerCreditSetting'=>array('fields' => array('CustomerCreditSetting.id', 'CustomerCreditSetting.name'))
                    );

                    $data_table = $this->CustomerCredit->find('all', array('conditions' => $condition_array, 'contain'=>$contain,'order' => "CustomerCredit.$sortname $sortorder", 'page' => $page  , 'limit'=> $limit, 'recursive' => 1));
                    $data_table_count = $this->CustomerCredit->find('count', array('conditions' => $condition_array, 'recursive' => -1));
                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {

                            $req_sales_amount =  isset($obj['CustomerCredit']['req_sales_amount']) ? $this->formatNumber($obj['CustomerCredit']['req_sales_amount'],'money',0) : '';
                            $invoice_date =  isset($obj['CustomerCredit']['invoice_date']) ? $this->covertDate($obj['CustomerCredit']['invoice_date'],'mysql_flip') : '';


                            $return_arr[] = array(
                                'id' => $obj['CustomerCredit']['id'],
                                'cell' => array(
                                    $obj['CustomerCredit']['id'],
                                    $obj['CustomerCreditSetting']['name'],
                                    $invoice_date,
                                    $obj['ProductType']['name'],
                                    $obj['CustomerCredit']['req_sales_qty'],
                                    $obj['CustomerCredit']['price'],
                                    $obj['CustomerCredit']['delivery_method'],
                                    $req_sales_amount,
                                    $obj['CustomerCredit']['approved_qty'],
                                    $obj['CustomerCredit']['approved_amount'],
                                    $obj['CustomerCredit']['approved_status'],
                                    $obj['CustomerCredit']['comments']
                                ),
                                'property' => array(
                                    'edit_row'=> $obj['CustomerCredit']['app_edit_row'],
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
                        if (!in_array('A', $permissions)) {
                            return json_encode(array('code' => 1, 'msg' => 'Access Denied.'));
                        }
                    } else {
                        if (!in_array('E', $permissions)) {
                            return json_encode(array('code' => 1, 'msg' => 'Access Denied.'));
                        }
                    }

                    $data = array('CustomerCredit' => $_POST);

                    if ($_POST['id'] == 0) {
                        $data['CustomerCredit']['created_by'] = $authUser['id'];
                    } else {
                        $data['CustomerCredit']['modified_by'] = $authUser['id'];
                    }

                    $data['CustomerCredit']['omc_customer_id'] = $company_profile['id'];
                    $data['CustomerCredit']['invoice_date'] = $this->covertDate($_POST['invoice_date'], 'mysql') . ' ' . date('H:i:s');
                    $data['CustomerCredit']['req_sales_qty'] = str_replace(',', '', $_POST['req_sales_qty']);
                    $data['CustomerCredit']['req_sales_amount'] = str_replace(',', '', $_POST['req_sales_amount']);

                    if ($this->CustomerCredit->save($this->sanitize($data))) {
                        $credit_id  = $this->CustomerCredit->id;

                        //Activity Log
                        $log_description = $this->getLogMessage('UpdateCustomerCredit')." (Order #".$credit_id.")";
                        $this->logActivity('Order',$log_description);

                        if($_POST['id'] > 0){
                            return json_encode(array('code' => 0, 'msg' => 'Data Updated!'));
                        }
                        else{
                            return json_encode(array('code' => 0, 'msg' => 'Data Saved', 'id'=>$credit_id));
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
        //$cus_lists = $this->get_credit_customers();


        $start_dt = date('Y-m-01');
        $end_dt = date('Y-m-t');
        $group_by = 'monthly';
        $group_by_title = date('F');

       // $customer_name_lists = $this->CustomerCreditSetting->getCustomerNameList();
        $customer_name_lists = $this->get_credit_customers();

       // pr($customer_name_lists);
       //pr($products_lists);
        $delivery_method = array('0'=>array('id'=>'Fleet - Cars','name'=>'Fleet - Cars'),'1'=>array('id'=>'Fleet - Trucks','name'=>'Fleet - Trucks'),'2'=>array('id'=>'Fleet - Site Vehicles','name'=>'Fleet - Site Vehicles'),'3'=>array('id'=>'Fuel Bowser','name'=>'Fuel Bowser'),'4'=>array('id'=>'Fuel - Mobile Tanks','name'=>'Fuel - Mobile Tanks'));
        $approved_status = array('0'=>array('id'=>'Approved','name'=>'Approved'),'1'=>array('id'=>'Reject','name'=>'Reject'));
        $all_customers_products_prices = $this->OmcCustomerPriceChange->getAllProductsPumpPrices($company_profile['id']);

        $order_filter = $this->order_filter;
        $g_data =  $this->get_orders($start_dt,$end_dt,$group_by,null);
        $volumes = $this->Volume->getVolsList();

        $graph_title = $group_by_title.", Orders-Consolidated";

        $this->set(compact('all_customers_products_prices','omc_customer_id','volumes','permissions', 'products_lists','graph_title','g_data','order_filter','customer_name_lists','delivery_method','approved_status'));
    }




    function omc_customer_credit_request_approval($type = 'get'){

        $permissions = $this->action_permission;
        $company_profile = $this->global_company;
        $omc_customer_id = $company_profile['id'];
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
                        'CustomerCredit.omc_customer_id' => $company_profile['id'],
                        'CustomerCredit.deleted' => 'n'
                    );

                    $contain = array(
                        'ProductType'=>array('fields' => array('ProductType.id', 'ProductType.name')),
                        'OmcCustomer'=>array('fields' => array('OmcCustomer.id', 'OmcCustomer.name')),
                        'CustomerCreditSetting'=>array('fields' => array('CustomerCreditSetting.id', 'CustomerCreditSetting.name'))
                    );

                    $data_table = $this->CustomerCredit->find('all', array('conditions' => $condition_array, 'contain'=>$contain,'order' => "CustomerCredit.$sortname $sortorder", 'page' => $page  , 'limit'=> $limit, 'recursive' => 1));
                    $data_table_count = $this->CustomerCredit->find('count', array('conditions' => $condition_array, 'recursive' => -1));
                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {

                            $req_sales_amount =  isset($obj['CustomerCredit']['req_sales_amount']) ? $this->formatNumber($obj['CustomerCredit']['req_sales_amount'],'money',0) : '';
                            $invoice_date =  isset($obj['CustomerCredit']['invoice_date']) ? $this->covertDate($obj['CustomerCredit']['invoice_date'],'mysql_flip') : '';


                            $return_arr[] = array(
                                'id' => $obj['CustomerCredit']['id'],
                                'cell' => array(
                                    $obj['CustomerCredit']['id'],
                                    $obj['CustomerCreditSetting']['name'],
                                    $invoice_date,
                                    $obj['ProductType']['name'],
                                    $obj['CustomerCredit']['req_sales_qty'],
                                    $obj['CustomerCredit']['price'],
                                    $obj['CustomerCredit']['delivery_method'],
                                    $req_sales_amount,
                                    $obj['CustomerCredit']['approved_qty'],
                                    $obj['CustomerCredit']['approved_amount'],
                                    $obj['CustomerCredit']['approved_status'],
                                    $obj['CustomerCredit']['comments']
                                ),
                                'property' => array(
                                    'edit_row'=> $obj['CustomerCredit']['app_edit_row'],
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
                        if (!in_array('A', $permissions)) {
                            return json_encode(array('code' => 1, 'msg' => 'Access Denied.'));
                        }
                    } else {
                        if (!in_array('E', $permissions)) {
                            return json_encode(array('code' => 1, 'msg' => 'Access Denied.'));
                        }
                    }

                    $data = array('CustomerCredit' => $_POST);

                    if ($_POST['id'] == 0) {
                        $data['CustomerCredit']['created_by'] = $authUser['id'];
                    } else {
                        $data['CustomerCredit']['modified_by'] = $authUser['id'];
                    }

                    $data['CustomerCredit']['omc_customer_id'] = $company_profile['id'];
                    $data['CustomerCredit']['app_edit_row'] = 'no';
                    if ($this->CustomerCredit->save($this->sanitize($data))) {
                        $credit_id  = $this->CustomerCredit->id;

                        //Activity Log
                        $log_description = $this->getLogMessage('UpdateCustomerCredit')." (Order #".$credit_id.")";
                        $this->logActivity('Order',$log_description);

                        if($_POST['id'] > 0){
                            return json_encode(array('code' => 0, 'msg' => 'Data Updated!'));
                        }
                        else{
                            return json_encode(array('code' => 0, 'msg' => 'Data Saved', 'id'=>$credit_id));
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
        //$cus_lists = $this->get_credit_customers();


        $start_dt = date('Y-m-01');
        $end_dt = date('Y-m-t');
        $group_by = 'monthly';
        $group_by_title = date('F');

       // $customer_name_lists = $this->CustomerCreditSetting->getCustomerNameList();
        $customer_name_lists = $this->get_credit_customers();

       // pr($customer_name_lists);
       //pr($products_lists);
        $delivery_method = array('0'=>array('id'=>'Fleet - Cars','name'=>'Fleet - Cars'),'1'=>array('id'=>'Fleet - Trucks','name'=>'Fleet - Trucks'),'2'=>array('id'=>'Fleet - Site Vehicles','name'=>'Fleet - Site Vehicles'),'3'=>array('id'=>'Fuel Bowser','name'=>'Fuel Bowser'),'4'=>array('id'=>'Fuel - Mobile Tanks','name'=>'Fuel - Mobile Tanks'));
        $approved_status = array('0'=>array('id'=>'Approved','name'=>'Approved'),'1'=>array('id'=>'Reject','name'=>'Reject'));
        $all_customers_products_prices = $this->OmcCustomerPriceChange->getAllProductsPumpPrices($company_profile['id']);

        $order_filter = $this->order_filter;
        $g_data =  $this->get_orders($start_dt,$end_dt,$group_by,null);
        $volumes = $this->Volume->getVolsList();

        $graph_title = $group_by_title.", Orders-Consolidated";

        $this->set(compact('all_customers_products_prices','omc_customer_id','volumes','permissions', 'products_lists','graph_title','g_data','order_filter','customer_name_lists','delivery_method','approved_status'));
    }




    function omc_customer_credit_sales($type = 'get'){

        $permissions = $this->action_permission;
        $company_profile = $this->global_company;
        $omc_customer_id = $company_profile['id'];
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
                        'CustomerCredit.omc_customer_id' => $company_profile['id'],
                        'CustomerCredit.deleted' => 'n',
                        'CustomerCredit.approved_status' => 'Approved'
                    );

                    $contain = array(
                        'ProductType'=>array('fields' => array('ProductType.id', 'ProductType.name')),
                        'OmcCustomer'=>array('fields' => array('OmcCustomer.id', 'OmcCustomer.name')),
                        'CustomerCreditSetting'=>array('fields' => array('CustomerCreditSetting.id', 'CustomerCreditSetting.name'))
                    );

                    $data_table = $this->CustomerCredit->find('all', array('conditions' => $condition_array, 'contain'=>$contain,'order' => "CustomerCredit.$sortname $sortorder", 'page' => $page  , 'limit'=> $limit, 'recursive' => 1));
                    $data_table_count = $this->CustomerCredit->find('count', array('conditions' => $condition_array, 'recursive' => -1));
                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {

                            $sales_amount =  isset($obj['CustomerCredit']['sales_amount']) ? $this->formatNumber($obj['CustomerCredit']['sales_amount'],'money',0) : '';
                            $invoice_date =  isset($obj['CustomerCredit']['invoice_date']) ? $this->covertDate($obj['CustomerCredit']['invoice_date'],'mysql_flip') : '';


                            $return_arr[] = array(
                                'id' => $obj['CustomerCredit']['id'],
                                'cell' => array(
                                    $obj['CustomerCredit']['id'],
                                    $obj['CustomerCreditSetting']['name'],
                                    $obj['CustomerCredit']['invoice_no'],
                                    $invoice_date,
                                    $obj['ProductType']['name'],
                                    $obj['CustomerCredit']['sales_qty'],
                                    $obj['CustomerCredit']['price'],
                                    $obj['CustomerCredit']['delivery_method'],
                                    $sales_amount,
                                    $obj['CustomerCredit']['staff_name'],
                                    $obj['CustomerCredit']['comments']
                                ),
                                'property' => array(
                                    'edit_row'=> $obj['CustomerCredit']['edit_row'],
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
                        if (!in_array('A', $permissions)) {
                            return json_encode(array('code' => 1, 'msg' => 'Access Denied.'));
                        }
                    } else {
                        if (!in_array('E', $permissions)) {
                            return json_encode(array('code' => 1, 'msg' => 'Access Denied.'));
                        }
                    }

                    $data = array('CustomerCredit' => $_POST);

                    if ($_POST['id'] == 0) {
                        $data['CustomerCredit']['created_by'] = $authUser['id'];
                    } else {
                        $data['CustomerCredit']['modified_by'] = $authUser['id'];
                    }

                    $data['CustomerCredit']['omc_customer_id'] = $company_profile['id'];
                    $data['CustomerCredit']['edit_row'] = 'no';
                    $data['CustomerCredit']['invoice_date'] = $this->covertDate($_POST['invoice_date'], 'mysql') . ' ' . date('H:i:s');
                    $data['CustomerCredit']['sales_amount'] = str_replace(',', '', $_POST['sales_amount']);

                    if ($this->CustomerCredit->save($this->sanitize($data))) {
                        $credit_id  = $this->CustomerCredit->id;

                        //Activity Log
                        $log_description = $this->getLogMessage('UpdateCustomerCredit')." (Order #".$credit_id.")";
                        $this->logActivity('Order',$log_description);

                        if($_POST['id'] > 0){
                            return json_encode(array('code' => 0, 'msg' => 'Data Updated!'));
                        }
                        else{
                            return json_encode(array('code' => 0, 'msg' => 'Data Saved', 'id'=>$credit_id));
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
        //$cus_lists = $this->get_credit_customers();


        $start_dt = date('Y-m-01');
        $end_dt = date('Y-m-t');
        $group_by = 'monthly';
        $group_by_title = date('F');

       // $customer_name_lists = $this->CustomerCreditSetting->getCustomerNameList();
        $customer_name_lists = $this->get_credit_customers();

       // pr($customer_name_lists);
       //pr($products_lists);
        $delivery_method = array('0'=>array('id'=>'Fleet - Cars','name'=>'Fleet - Cars'),'1'=>array('id'=>'Fleet - Trucks','name'=>'Fleet - Trucks'),'2'=>array('id'=>'Fleet - Site Vehicles','name'=>'Fleet - Site Vehicles'),'3'=>array('id'=>'Fuel Bowser','name'=>'Fuel Bowser'),'4'=>array('id'=>'Fuel - Mobile Tanks','name'=>'Fuel - Mobile Tanks'));
        $all_customers_products_prices = $this->OmcCustomerPriceChange->getAllProductsPumpPrices($company_profile['id']);

        $order_filter = $this->order_filter;
        $g_data =  $this->get_orders($start_dt,$end_dt,$group_by,null);
        $volumes = $this->Volume->getVolsList();

        $graph_title = $group_by_title.", Orders-Consolidated";

        $this->set(compact('all_customers_products_prices','omc_customer_id','volumes','permissions', 'products_lists','graph_title','g_data','order_filter','customer_name_lists','delivery_method'));
    }



    function omc_customer_credit_payment($type = 'get'){

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
                    $filter_status =   isset($_POST['filter_status']) ? $_POST['filter_status'] : 'complete_orders' ;
                    /** Search string */
                    $rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
                    $limit = $rp;
                    $start = ($page - 1) * $rp;

                    //get users id for this company only
                    $condition_array = array(
                        'CustomerCreditPayment.omc_customer_id' => $company_profile['id'],
                        'CustomerCreditPayment.deleted' => 'n'
                    );

                    $contain = array(
                        'OmcCustomer'=>array('fields' => array('OmcCustomer.id', 'OmcCustomer.name')),
                        'CustomerCreditSetting'=>array('fields' => array('CustomerCreditSetting.id', 'CustomerCreditSetting.name'))
                    );

                    $data_table = $this->CustomerCreditPayment->find('all', array('conditions' => $condition_array, 'contain'=>$contain,'order' => "CustomerCreditPayment.$sortname $sortorder", 'page' => $page  , 'limit'=> $limit, 'recursive' => 1));
                    $data_table_count = $this->CustomerCreditPayment->find('count', array('conditions' => $condition_array, 'recursive' => -1));
                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {

                            $payment_amount =  isset($obj['CustomerCreditPayment']['payment_amount']) ? $this->formatNumber($obj['CustomerCreditPayment']['payment_amount'],'money',0) : '';
                            $receipt_date =  isset($obj['CustomerCreditPayment']['receipt_date']) ? $this->covertDate($obj['CustomerCreditPayment']['receipt_date'],'mysql_flip') : '';


                            $return_arr[] = array(
                                'id' => $obj['CustomerCreditPayment']['id'],
                                'cell' => array(
                                    $obj['CustomerCreditPayment']['id'],
                                    $obj['CustomerCreditSetting']['name'],
                                    $obj['CustomerCreditPayment']['receipt_no'],
                                    $receipt_date,
                                    $payment_amount,
                                    $obj['CustomerCreditPayment']['payment_method'],
                                    $obj['CustomerCreditPayment']['nct_channel'],
                                    $obj['CustomerCreditPayment']['payment_instrument']
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
                    $data = array('CustomerCreditPayment' => $_POST);

                    if ($_POST['id'] == 0) {//Mew
                        if (!in_array('A', $permissions)) {
                            return json_encode(array('code' => 1, 'msg' => 'Access Denied.'));
                        }
                    } else {
                        if (!in_array('E', $permissions)) {
                            return json_encode(array('code' => 1, 'msg' => 'Access Denied.'));
                        }
                    }

                    $data = array('CustomerCreditPayment' => $_POST);

                    if ($_POST['id'] == 0) {
                        $data['CustomerCreditPayment']['created_by'] = $authUser['id'];
                    } else {
                        $data['CustomerCreditPayment']['modified_by'] = $authUser['id'];
                    }

                    $data['CustomerCreditPayment']['omc_customer_id'] = $company_profile['id'];
                    $data['CustomerCreditPayment']['receipt_date'] = $this->covertDate($_POST['receipt_date'], 'mysql') . ' ' . date('H:i:s');
                    $data['CustomerCreditPayment']['payment_amount'] = str_replace(',', '', $_POST['payment_amount']);

                    if ($this->CustomerCreditPayment->save($this->sanitize($data))) {
                        $payment_id  = $this->CustomerCreditPayment->id;

                        //Activity Log
                        $log_description = $this->getLogMessage('UpdateCustomerCredit')." (Order #".$payment_id.")";
                        $this->logActivity('Order',$log_description);

                        if($_POST['id'] > 0){
                            return json_encode(array('code' => 0, 'msg' => 'Data Updated!'));
                        }
                        else{
                            return json_encode(array('code' => 0, 'msg' => 'Data Saved', 'id'=>$payment_id));
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
        $start_dt = date('Y-m-01');
        $end_dt = date('Y-m-t');
        $group_by = 'monthly';
        $group_by_title = date('F');
         // $customer_name_lists = $this->CustomerCreditSetting->getCustomerNameList();
         $customer_name_lists = $this->get_credit_customers();

        $payment_method = array('0'=>array('id'=>'Cash','name'=>'Cash'),'1'=>array('id'=>'NCT','name'=>'NCT'),'2'=>array('id'=>'Cheque','name'=>'Cheque'),'3'=>array('id'=>'Credit Note','name'=>'Credit Note'));


        /* $bdclists =array(array('name'=>'All','value'=>0));
         foreach($bdclists_data as $arr){
             $bdclists[] = array('name'=>$arr['name'],'value'=>$arr['id']);
         }*/

        $order_filter = $this->order_filter;
        $g_data =  $this->get_orders($start_dt,$end_dt,$group_by,null);
        $volumes = $this->Volume->getVolsList();
        $ncts = $this->Nct->getNctList();
        $graph_title = $group_by_title.", Orders-Consolidated";

        $this->set(compact('grid_data','omc_customers_lists','volumes','permissions','depot_lists', 'products_lists','bdc_list','graph_title','g_data','bdclists','order_filter','list_tm','customer_name_lists','payment_method','ncts'));
    }



    function cm_customer_sales_ledger($type = 'get'){

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
                    $filter_status =   isset($_POST['filter_status']) ? $_POST['filter_status'] : 'complete_orders' ;
                    /** Search string */
                    $rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
                    $limit = $rp;
                    $start = ($page - 1) * $rp;

                    //get users id for this company only
                    $condition_array = array(
                        'CustomerCredit.omc_customer_id' => $company_profile['id'],
                        'CustomerCredit.deleted' => 'n'
                    );

                    $contain = array(
                        'ProductType'=>array('fields' => array('ProductType.id', 'ProductType.name')),
                        'OmcCustomer'=>array('fields' => array('OmcCustomer.id', 'OmcCustomer.name')),
                        'CustomerCreditSetting'=>array('fields' => array('CustomerCreditSetting.id', 'CustomerCreditSetting.name'))
                        /*'CustomerCreditPayment'=>array('fields' => array('CustomerCreditPayment.id', 'CustomerCreditPayment.receipt_no',
                                                                          'CustomerCreditPayment.receipt_date','CustomerCreditPayment.payment_amount',
                                                                          'CustomerCreditPayment.payment_methond','CustomerCreditPayment.nct_channel',
                                                                          'CustomerCreditPayment.payment_instrument'))*/
                    );

                    $data_table = $this->CustomerCredit->find('all', array('conditions' => $condition_array, 'contain'=>$contain,'order' => "CustomerCredit.$sortname $sortorder", 'page' => $page  , 'limit'=> $limit, 'recursive' => 1));
                    $data_table_count = $this->CustomerCredit->find('count', array('conditions' => $condition_array, 'recursive' => -1));
                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {

                            $sales_amount =  isset($obj['CustomerCredit']['sales_amount']) ? $this->formatNumber($obj['CustomerCredit']['sales_amount'],'money',0) : '';
                            $invoice_date =  isset($obj['CustomerCredit']['invoice_date']) ? $this->covertDate($obj['CustomerCredit']['invoice_date'],'mysql_flip') : '';


                            $return_arr[] = array(
                                'id' => $obj['CustomerCredit']['id'],
                                'cell' => array(
                                    $obj['CustomerCredit']['id'],
                                    $obj['CustomerCredit']['invoice_no'],
                                    $invoice_date,
                                    $obj['ProductType']['name'],
                                    $obj['CustomerCredit']['sales_qty'],
                                    $obj['CustomerCredit']['price'],
                                    $sales_amount,
                                    $sales_amount,
                                    $cum_balance =''

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
                    //echo debug($data);
                    break;

                case 'load':

                    break;
            }
        }

        $products_lists = $this->get_products();
        $start_dt = date('Y-m-01');
        $end_dt = date('Y-m-t');
        $group_by = 'monthly';
        $group_by_title = date('F');


       // $customer_name_lists = $this->CustomerCreditSetting->getCustomerNameList();
        $customer_name_lists = $this->get_credit_customers();
        $delivery_method = array('0'=>array('id'=>'Fleet - Cars','name'=>'Fleet - Cars'),'1'=>array('id'=>'Fleet - Trucks','name'=>'Fleet - Trucks'),'2'=>array('id'=>'Fleet - Site Vehicles','name'=>'Fleet - Site Vehicles'),'3'=>array('id'=>'Fuel Bowser','name'=>'Fuel Bowser'),'4'=>array('id'=>'Fuel - Mobile Tanks','name'=>'Fuel - Mobile Tanks'));


        $order_filter = $this->order_filter;
        $g_data =  $this->get_orders($start_dt,$end_dt,$group_by,null);
        $volumes = $this->Volume->getVolsList();
        $graph_title = $group_by_title.", Orders-Consolidated";

        $this->set(compact('grid_data','omc_customers_lists','volumes','permissions','depot_lists', 'products_lists','bdc_list','graph_title','g_data','bdclists','order_filter','list_tm','customer_name_lists','delivery_method'));
    }



    function cm_customer_payment_ledger($type = 'get'){

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
                    $filter_status =   isset($_POST['filter_status']) ? $_POST['filter_status'] : 'complete_orders' ;
                    /** Search string */
                    $rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
                    $limit = $rp;
                    $start = ($page - 1) * $rp;

                    //get users id for this company only
                    $condition_array = array(
                        'CustomerCreditPayment.omc_customer_id' => $company_profile['id'],
                        'CustomerCreditPayment.deleted' => 'n'
                    );

                    $contain = array(
                        'OmcCustomer'=>array('fields' => array('OmcCustomer.id', 'OmcCustomer.name'))
                    );

                    $data_table = $this->CustomerCreditPayment->find('all', array('conditions' => $condition_array, 'contain'=>$contain,'order' => "CustomerCreditPayment.$sortname $sortorder", 'page' => $page  , 'limit'=> $limit, 'recursive' => 1));
                    $data_table_count = $this->CustomerCreditPayment->find('count', array('conditions' => $condition_array, 'recursive' => -1));
                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {

                            $payment_amount =  isset($obj['CustomerCreditPayment']['payment_amount']) ? $this->formatNumber($obj['CustomerCreditPayment']['payment_amount'],'money',0) : '';
                            $receipt_date =  isset($obj['CustomerCreditPayment']['receipt_date']) ? $this->covertDate($obj['CustomerCreditPayment']['receipt_date'],'mysql_flip') : '';


                            $return_arr[] = array(
                                'id' => $obj['CustomerCreditPayment']['id'],
                                'cell' => array(
                                    $obj['CustomerCreditPayment']['id'],
                                    $obj['CustomerCreditPayment']['receipt_no'],
                                    $receipt_date,
                                    $payment_amount,
                                    $cumm_payment_amount = '',
                                    $obj['CustomerCreditPayment']['payment_method'],
                                    $obj['CustomerCreditPayment']['payment_instrument']
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

                    break;

                case 'load':

                    break;
            }
        }

        $products_lists = $this->get_products();
        $start_dt = date('Y-m-01');
        $end_dt = date('Y-m-t');
        $group_by = 'monthly';
        $group_by_title = date('F');
        //$customer_name_lists = $this->CustomerCreditSetting->getCustomerNameList();
        $customer_name_lists = $this->get_credit_customers();
        $payment_method = array('0'=>array('id'=>'Cash','name'=>'Cash'),'1'=>array('id'=>'NCT','name'=>'NCT'),'2'=>array('id'=>'Cheque','name'=>'Cheque'),'3'=>array('id'=>'Credit Note','name'=>'Credit Note'));


        /* $bdclists =array(array('name'=>'All','value'=>0));
         foreach($bdclists_data as $arr){
             $bdclists[] = array('name'=>$arr['name'],'value'=>$arr['id']);
         }*/

        $order_filter = $this->order_filter;
        $g_data =  $this->get_orders($start_dt,$end_dt,$group_by,null);
        $volumes = $this->Volume->getVolsList();
        $graph_title = $group_by_title.", Orders-Consolidated";

        $this->set(compact('grid_data','omc_custo   mers_lists','volumes','permissions','depot_lists', 'products_lists','bdc_list','graph_title','g_data','bdclists','order_filter','list_tm','customer_name_lists','payment_method'));
    }



    function export_orders()
    {
        $download = false;
        $company_profile = $this->global_company;;
        if ($this->request->is('post')) {
            if ($this->request->data['Export']['action'] == 'export_me') {
                $start_dt = $this->covertDate($this->request->data['Export']['export_startdt'], 'mysql') . ' 00:00:00';
                $end_dt = $this->covertDate($this->request->data['Export']['export_enddt'], 'mysql') . ' 23:59:59';
                $type = $this->request->data['Export']['export_type'];

                $contain = array(
                    'ProductType'=>array('fields' => array('ProductType.id', 'ProductType.name')),
                    'OmcCustomer'=>array('fields' => array('OmcCustomer.id', 'OmcCustomer.name')),
                    'CustomerCreditSetting'=>array('fields' => array('CustomerCreditSetting.id', 'CustomerCreditSetting.name'))
                );
                $export_data = $this->CustomerCredit->find('all', array(
                    'conditions' => array('CustomerCredit.omc_customer_id' => $company_profile['id'], 'CustomerCredit.deleted' => 'n', 'CustomerCredit.created >=' => $start_dt, 'CustomerCredit.created <=' => $end_dt),
                    'contain' => $contain,
                    'order' => array("CustomerCredit.id" => 'desc'),
                    'recursive' => 1
                ));

                if ($export_data) {
                    $download = true;
                    $list_data = array();
                    foreach ($export_data as $obj) {
                        $list_data[] = array(
                            $obj['CustomerCredit']['id'],
                            $obj['CustomerCreditSetting']['name'],
                            $obj['CustomerCredit']['invoice_no'],
                            $obj['CustomerCredit']['invoice_date'],
                            $obj['ProductType']['name'],
                            $obj['CustomerCredit']['sales_qty'],
                            $obj['CustomerCredit']['price'],
                            $obj['CustomerCredit']['delivery_method'],
                            $obj['CustomerCredit']['sales_amount'],
                            $obj['CustomerCredit']['staff_name'],
                            $obj['CustomerCredit']['comments'],
                        );
                    }
                    $list_headers = array('Cus. Credit Id', 'Customer Name', 'Invoice No', 'Invoice Date', 'Product Type', 'Sale Quantity', 'Price', 'Delivery Method','Sales Amount','Staff Name','Comment');
                    $filename = $company_profile['name'] . " Credit Date " . date('Ymdhis');
                    $res = $this->convertToExcel($list_headers, $list_data, $filename);
                    $objPHPExcel = $res['excel_obj'];
                    $filename = $res['filename'];
                }
            }
        }

        $this->autoLayout = false;

        $this->set(compact('objPHPExcel', 'download', 'filename'));
    }




}
