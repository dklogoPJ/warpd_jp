<?php

/**
 * @name OmcController.php
 */
App::import('Controller', 'OmcCustomerApp');

class OmcCustomerStockController extends OmcCustomerAppController
{
    # Controller name

    var $name = 'OmcCustomerStock';
    # set the model to use
    var $uses = array('Order','ProductType','OmcCustomerOrder','OmcCustomerTank','OmcCustomerStock');

    # Set the layout to use
    var $layout = 'omc_customer_layout';

    public function beforeFilter($param_array = null)
    {
        parent::beforeFilter();
    }


    function index()
    {
        $this->redirect('stock_histories');
    }


    function tanks_setup ($type = 'get')
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
                    /** @var $filter  */
                    $filter_status =   isset($_POST['filter_status']) ? $_POST['filter_status'] : 'incomplete_orders' ;
                    /** Search string */
                    $rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
                    $limit = $rp;
                    $start = ($page - 1) * $rp;

                    //get users id for this company only
                    $condition_array = array(
                        'OmcCustomerTank.omc_customer_id' => $company_profile['id'],
                        'OmcCustomerTank.deleted' => 'n'
                    );

                    if (!empty($search_query)) {
                        if ($qtype == 'name') {
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

                   /* $contain = array(
                        'Omc'=>array('fields' => array('Omc.id', 'Omc.name')),
                        'ProductType'=>array('fields' => array('ProductType.id', 'ProductType.name')),
                        'OmcCustomer'=>array('fields' => array('OmcCustomer.id', 'OmcCustomer.name'))
                    );*/
                    // $fields = array('User.id', 'User.username', 'User.first_name', 'User.last_name', 'User.group_id', 'User.active');
                    $data_table = $this->OmcCustomerTank->find('all', array('conditions' => $condition_array,'order' => "OmcCustomerTank.$sortname $sortorder", 'limit' => $start . ',' . $limit, 'recursive' => -1));
                    $data_table_count = $this->OmcCustomerTank->find('count', array('conditions' => $condition_array, 'recursive' => -1));

                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {
                            $return_arr[] = array(
                                'id' => $obj['OmcCustomerTank']['id'],
                                'cell' => array(
                                    //$obj['OmcCustomerTank']['id'],
                                    $obj['OmcCustomerTank']['name'],
                                    $obj['OmcCustomerTank']['type'],
                                    $obj['OmcCustomerTank']['capacity'],
                                    $obj['OmcCustomerTank']['status']
                                ),
                                'property'=>array(
                                    'bg_color'=>$obj['OmcCustomerTank']['row_bg_color'],
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
                   // $_POST['name'] = ucwords(strtolower($_POST['name']));
                    $data = array('OmcCustomerTank' => $_POST);
                    if($_POST['id']== 0){//New Manual Entry
                        $data['OmcCustomerTank']['created_by'] = $authUser['id'];
                        $data['OmcCustomerTank']['modified_by'] = $authUser['id'];
                        $data['OmcCustomerTank']['omc_customer_id'] = $company_profile['id'];
                    }
                    else{// Might be correcting error
                        $data['OmcCustomerTank']['modified_by'] = $authUser['id'];
                    }

                    if($_POST['status'] == 'Operational'){
                        $data['OmcCustomerTank']['row_bg_color'] = '';
                    }
                    elseif($_POST['status'] == 'Maintenance'){
                        $data['OmcCustomerTank']['row_bg_color'] = 'tr_yellow';
                    }
                    elseif($_POST['status'] == 'Out of Service'){
                        $data['OmcCustomerTank']['row_bg_color'] = 'tr_red';
                    }

                    if ($this->OmcCustomerTank->save($this->sanitize($data))) {
                        $tank_id  = $this->OmcCustomerTank->id;

                        if($_POST['id'] > 0){
                            return json_encode(array('code' => 0, 'msg' => 'Data Updated!'));
                        }
                        else{
                            return json_encode(array('code' => 0, 'msg' => 'Data Saved', 'id'=>$tank_id));
                        }
                    } else {
                        echo json_encode(array('code' => 1, 'msg' => 'Some errors occurred.'));
                    }
                    //echo debug($data);
                    break;

                case 'delete':
                    $ids = $_POST['ids'];
                    $modObj = ClassRegistry::init('OmcCustomerTank');
                    $result = $modObj->updateAll(
                        $this->sanitize(array('OmcCustomerTank.deleted' => "'y'")),
                        $this->sanitize(array('OmcCustomerTank.id' => $ids))
                    );
                    if ($result) {
                        echo json_encode(array('code' => 0, 'msg' => 'Data Deleted!'));
                    } else {
                        echo json_encode(array('code' => 1, 'msg' => 'Data cannot be deleted'));
                    }
                    break;
            }
        }

        $tanks_types_pro = $this->getTanksProductTypes();
        $tanks_types_opt = array();
        foreach($tanks_types_pro as $key => $value){
            $tanks_types_opt[] =array(
                'id'=>$key,
                'name'=>$value
            );
        }

        $tank_status = array();
        foreach($this->omc_customer_tank_status as $key => $key){
            $tank_status[] =array(
                'id'=>$key,
                'name'=>$key
            );
        }

        $this->set(compact('tanks_types_opt','tank_status'));
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


    function stock_update(){
        $authUser = $this->Auth->user();
        $company_profile = $this->global_company;
        if($this->request->is('post')){

            $today = date('Y-m-d');
            foreach($this->request->data['OmcCustomerStock'] as $key => $dt){
                $stock_qty = trim($dt['quantity']);
                if(empty($stock_qty)){
                    unset($this->request->data['OmcCustomerStock'][$key]);
                    continue;
                }
                $omc_customer_tank_id = $dt['omc_customer_tank_id'];
                $r = $this->OmcCustomerStock->find('first',array(
                    'fields'=>array('id'),
                    'conditions'=>array('omc_customer_tank_id'=>$omc_customer_tank_id,'created LIKE'=>$today.' %'),
                    'recursive'=>-1
                ));
                if($r){
                    $save_id = $r['OmcCustomerStock']['id'];
                    $this->request->data['OmcCustomerStock'][$key]['modified_by'] = $authUser['id'];
                }
                else{
                    $save_id = 0;
                    $this->request->data['OmcCustomerStock'][$key]['created_by'] = $authUser['id'];
                }
                $this->request->data['OmcCustomerStock'][$key]['id'] = $save_id;
            }

            if(empty($this->request->data['OmcCustomerStock'])){
                $this->Session->setFlash('No Stock Update, You have to enter at list one stock quantity.');
                $this->Session->write('process_error', 'yes');
            }
            else{
                $res = $this->OmcCustomerStock->saveAll($this->sanitize($this->request->data['OmcCustomerStock']));
                if ($res) {
                    $this->Session->setFlash('Stock has been updated !');
                    $this->Session->write('process_error', 'no');
                }
                else {
                    $this->Session->setFlash('Sorry, Stock update failed.');
                    $this->Session->write('process_error', 'yes');
                }
            }

            $this->redirect(array('action' => 'stock_update'));
        }

        $tanks = $this->OmcCustomerTank->getTanks($company_profile['id']);

        $fin_tanks = array();
        $opt_tanks = array();
        foreach($tanks as $value){
            $fin_tanks[$value['OmcCustomerTank']['id']]=$value;
            $opt_tanks[$value['OmcCustomerTank']['id']] = $value['OmcCustomerTank']['name'];
        }

        //debug($fin_tanks);

        $controller = $this;
        $this->set(compact('controller', 'opt_tanks', 'fin_tanks'));
    }


    function stock_histories(){
        $company_profile = $this->global_company;
        $month = date('m');
        $year = date('Y');
        $product_type = 'all';
        $product_type_name = '';

        if($this->request->is('post')){
            $month = $this->request->data['Query']['month'];
            $year = $this->request->data['Query']['year'];
            $product_type = $this->request->data['Query']['type'];
            if($this->request->data['Query']['type'] != 'all'){
                $product_type_name = 'On '.$this->request->data['Query']['type'];
            }
        }

        $g_data = $this->getStockHistory($month,$year,$product_type);
        $t_head = $g_data['t_head'];
        $t_body_data = $g_data['t_body_data'];

        $tanks_types_lists = array_merge(array('all'=>'All'),$this->getTanksProductTypes());
        $month_lists = $this->getMonths();

        $month_name = $this->getMonths($month);
        $table_title = $month_name.'-'.$year.', Historic Station Stock Report '.$product_type_name;

        $controller = $this;
        $this->set(compact('controller','table_title','t_head','t_body_data','tanks_types_lists','year','month','product_type','month_lists'));
    }


    function print_export_stock_histories(){
        $download = false;
        $company_profile = $this->global_company;

        $month = $_POST['data_month'];
        $year = $_POST['data_year'];
        $product_type = $_POST['data_tank_type'];
        $product_type_name = '';
        if($product_type != 'all'){
            $product_type_name = 'On '.$product_type;
        }
        $media_type = $_POST['data_type'];

        $g_data = $this->getStockHistory($month,$year,$product_type);
        $t_head = $g_data['t_head'];
        $t_body_data = $g_data['t_body_data'];

        $month_name = $this->getMonths($month);
        $table_title = $export_title = $month_name.'-'.$year.', Historic Station Stock Report '.$product_type_name;

        $list_data = $t_body_data;

        $this->autoLayout = false;

        if($media_type == 'export'){
            if(empty($list_data)){
                $download = false;
            }
            else{
                $download = true;
                $list_headers = $t_head;
                $filename = $export_title;
                $res = $this->convertToExcel($list_headers,$list_data,$filename);
                $objPHPExcel = $res['excel_obj'];
                $filename = $res['filename'];
            }
        }
        elseif($media_type == 'print'){
            $this->autoLayout = true;
            $this->layout = 'print_layout';
        }
        $print_title = $table_title;
        $controller = $this;

        $this->set(compact('controller','print_title','table_title','t_head','t_body_data','objPHPExcel', 'download', 'filename','media_type'));
    }

}