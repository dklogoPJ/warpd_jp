<?php

/**
 * @name OmcCustomerDailySalesController.php
 */
App::import('Controller', 'OmcCustomerApp');

class OmcCustomerDailySalesController extends OmcCustomerAppController
{
    # Controller name

    var $name = 'OmcCustomerDailySales';
    # set the model to use
    var $uses = array(
        'Menu','OmcSalesForm','OmcSalesRecord','OmcSalesSheet','OmcSalesReport',
        'OmcDailySalesProduct', 'OmcOperatorsCredit','OmcDsrpDataOption',
        'OmcCustomerOrder','OmcCustomer','ProductType','Volume','NctRecord','Nct',
        'OmcCustomerDailySale','OmcCustomerDailySaleField','LpgSetting','LubeSetting',
        'CustomerCredit'
    );
    # Set the layout to use
    var $layout = 'omc_customer_layout';

    public function beforeFilter($param_array = null)
    {
        parent::beforeFilter();
    }


    function index($form_key = '', $record_dt = '') {
        $this->setPermission($form_key);
        $permissions = $this->action_permission;
        $company_profile = $this->global_company;
        $sales_sheet_date = date('Y-m-d');
        $previous_sales_sheet_date = date('Y-m-d', strtotime('-1 day', strtotime($sales_sheet_date)));
        if($record_dt) {
            $sales_sheet_date = $record_dt;
            $previous_sales_sheet_date = date('Y-m-d', strtotime('-1 day', strtotime($sales_sheet_date)));
        }

        $sales_sheet_date_range = array();
        for ($i =0; $i <= 1; $i++) {// We need only today and yesterday
            $day = date('Y-m-d', strtotime("-$i day", strtotime(date('Y-m-d'))));
            $sales_sheet_date_range[$day] = $day;
        }

        if ($this->request->is('ajax')) {
            $this->autoRender = false;
            $this->autoLayout = false;
            //$authUser = $this->Auth->user();
           // $post = $this->sanitize($_POST);
            $post = $_POST;
            $action_type = $post['form_action_type'];

            if($action_type == 'create_sales_sheet'){
                $new = $this->OmcCustomerDailySale->setupFormSaleSheet($company_profile['omc_id'], $company_profile['id'], $post['form_key'], $post['sales_sheet_date']);
                if($new){
                    return json_encode(array('code' => 0, 'msg' => 'Sales sheet created!'));
                }
                else{
                    return json_encode(array('code' => 1, 'msg' => 'Could not create sales sheet!'));
                }
            } elseif ($action_type == 'delete_sales_sheet') {
                $new = $this->OmcCustomerDailySale->deleteFormSaleSheet($post['form_sales_sheet_id']);
                if($new){
                    return json_encode(array('code' => 0, 'msg' => 'Sales sheet deleted!'));
                }
                else{
                    return json_encode(array('code' => 1, 'msg' => 'Could not delete sales sheet!'));
                }
            } elseif ($action_type == 'form_save_sales_record') {
                if($this->OmcCustomerDailySaleField->saveAll($post['field_values'])) {
                    return json_encode(array('code' => 0, 'msg' => 'Sales sheet record saved!', 'post'=>$post['field_values']));
                }
                else{
                    return json_encode(array('code' => 1, 'msg' => 'Could not save sales sheet record.'));
                }
            }
        }

        $current_day_records = $this->OmcCustomerDailySale->getFormSaleSheet($company_profile['omc_id'], $company_profile['id'], $form_key, $sales_sheet_date);
        //Get Previous Days Records
        $previous_day_records = $this->OmcCustomerDailySale->getFormSaleSheet($company_profile['omc_id'],$company_profile['id'], $form_key, $previous_sales_sheet_date);

        $price_change_data = array();
        foreach($this->price_change as $pr){
            $price_change_data[] = array(
                'id'=> $pr['product_type_id'],
                'name'=> $pr['description'],
                'price'=> $pr['price']
            );
        }

        $all_external_data_sources = array(
            'products'=> $price_change_data,
            'lpg_settings'=> $this->LpgSetting->getProductList($company_profile['id']),
            'lube_settings'=> $this->LubeSetting->getProductList($company_profile['id']),
            'dsrp'=> $this->OmcCustomerDailySale->getAllFormSalesSheet($company_profile['omc_id'], $company_profile['id'], $sales_sheet_date),
            'credit_sales_record'=> $this->CustomerCredit->getCumulativeQtyAndSalesPerProduct($company_profile['id'], $sales_sheet_date)
        );

        $menu = $this->Menu->getMenuByUrl('OmcCustomerDailySales', $form_key);
        $menu_title = $menu['Menu']['menu_name'];

        $sales_sheet_id = 0;
        $report_title = false;
        if($current_day_records) {
            $sales_sheet_id = $current_day_records['form']['omc_customer_daily_sales_id'];
            $report_title = $this->OmcSalesReport->getReportName($current_day_records['form']['omc_sales_report_id']);
        }

        $this->set(compact('permissions','company_profile','all_external_data_sources','previous_day_records','current_day_records','menu_title', 'form_key', 'report_title', 'sales_sheet_id', 'sales_sheet_date', 'sales_sheet_date_range'));
    }

    function get_attachments($id = null, $attachment_type = null){
        $this->autoRender = false;
        $result = $this->__get_attachments($attachment_type, $id);
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

    function get_dsrp_report () {
        $this->autoRender = false;
        $company_profile = $this->global_company;
        $post = $_POST;
        $sales_records = $this->OmcSalesReport->getReportWithData($company_profile['id'], $post['report_id'], $post['report_date']);
       // debug($sales_records);
        $view = new View($this, false);
        $view->set(compact('sales_records')); // set variables
        $view->viewPath = 'Elements/omc_customer/'; // render an element
        $html = $view->render('preview_report'); // get the rendered markup
        return json_encode(array('code' => 0, 'msg' => 'Report Found!', 'html'=> $html));
    }


 /*   private function total_daily_sales_product ($param){
        //Total Quantity
        $total_day_sales_qty = floatval($param['cash_day_sales_qty']) + floatval($param['dealer_credit_day_sales_qty']) + floatval($param['customers_day_sales_qty']) ;
        //Total Value
        $total_day_sales_value = floatval($param['cash_day_sales_value']) + floatval($param['dealer_credit_day_sales_value']) + floatval($param['customers_day_sales_value']);
        //Total Prev Qty
        $total_previous_day_sales_qty = floatval($param['cash_previous_day_sales_qty']) + floatval($param['dealer_credit_previous_day_sales_qty']) + floatval($param['customers_previous_day_sales_qty']);
        //Total Prev Value
        $total_previous_day_sales_value = floatval($param['cash_previous_day_sales_value']) + floatval($param['dealer_credit_previous_day_sales_value']) + floatval($param['customers_previous_day_sales_value']);
        //Total Month Qty
        $total_month_to_date_qty = floatval($param['cash_month_to_date_qty']) + floatval($param['dealer_credit_month_to_date_qty']) + floatval($param['customers_month_to_date_qty']);
        //Total Month Value
        $total_month_to_date_value = floatval($param['cash_month_to_date_value']) + floatval($param['dealer_credit_month_to_date_value']) + floatval($param['customers_month_to_date_value']);

        $param['total_day_sales_qty'] = $total_day_sales_qty;
        $param['total_day_sales_value'] = $total_day_sales_value;
        $param['total_previous_day_sales_qty'] = $total_previous_day_sales_qty;
        $param['total_previous_day_sales_value'] = $total_previous_day_sales_value;
        $param['total_month_to_date_qty'] = $total_month_to_date_qty;
        $param['total_month_to_date_value'] = $total_month_to_date_value;

        return $param;
    }*/


  /*  function dsrp_report (){
        $company_profile = $this->global_company;
        $default_month = date('m');
        $default_year = date('Y');
        $default_day = date('d');
        $default_customer = $company_profile['id'];
        $omc_id = $company_profile['omc_id'];
        $default_dsrp = 'bsp';

        if($this->request->is('post')){
            $default_month = $this->request->data['Query']['month'];
            $default_year = $this->request->data['Query']['year'];
            $default_day = $this->request->data['Query']['day'];
            $default_dsrp = $this->request->data['Query']['dsrp_opt'];
        }
        $full_date = $default_year.'-'.$default_month.'-'.$default_day;
        $g_data = $this->get_dsrp_data($full_date,$omc_id,$default_customer,$default_dsrp);

        $start_year = $this->OmcSalesSheet->getStartYear('omc_customer',$company_profile['id']);
        $month_list = $this->getMonths();
        $year_list = $this->getYears($start_year);
        $day_list = $this->getDays();
        $dsrp_list = $this->getDSRPoptions();

        $month_name = $this->getMonths($default_month);
        $get_dsrp_name = $this->getDSRPoptions($default_dsrp);
        $dsrp_name = ($get_dsrp_name) && !empty($get_dsrp_name) ? $get_dsrp_name : 'DSRP';
        $table_title = $this->__add_ordinal_suffix($default_day).'-'.$month_name.'-'.$default_year.', '.$dsrp_name.' Report ';

        $controller = $this;
        $this->set(compact('controller','table_title','default_customer','default_year','default_month','default_day','default_dsrp','month_list','year_list','day_list','dsrp_list','g_data'));
    }*/


   /* function export_dsrp(){
        $this->autoLayout = false;
        $download = false;
        $company_profile = $this->global_company;
        $customer = $company_profile['id'];
        if($this->request->is('post')){
            $dsrp_opt = $_POST['data_dsrp_type'];
            $month = $_POST['data_month'];
            $year = $_POST['data_year'];
            $day = $_POST['data_day'];
            $media_type = $_POST['data_doc_type'];
        }
        $full_date = $year.'-'.$month.'-'.$day;
        $omc_id = $company_profile['omc_id'];
        $g_data_raw = $this->get_dsrp_data($full_date,$omc_id,$customer,$dsrp_opt);
        $g_data = $this->process_export_dsrp_data($g_data_raw,$dsrp_opt);
        $month_name = $this->getMonths($month);
        $get_dsrp_name = $this->getDSRPoptions($dsrp_opt);
        $dsrp_name = ($get_dsrp_name) && !empty($get_dsrp_name) ? $get_dsrp_name : 'DSRP';
        $table_title = $this->__add_ordinal_suffix($day).'-'.$month_name.'-'.$year.', '.$dsrp_name.' Report ';

        $list_headers = $g_data['header'];
        $list_data = $g_data['data'];

        if($g_data_raw){
            $download = true;
        }
        $filename = $table_title;
        $res = array('excel_obj'=>'','filename'=>'');
        if($download){
            $res = $this->convertToExcel($list_headers,$list_data,$filename);
        }
        $objPHPExcel = $res['excel_obj'];
        $filename = $res['filename'];

        $controller = $this;

        $this->set(compact('controller','company_profile','table_title','objPHPExcel', 'download', 'filename','g_data'));

    }*/


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
                        'OmcCustomerOrder.omc_customer_id' => $company_profile['id'],
                        'OmcCustomerOrder.deleted' => 'n'
                    );

                    $contain = array(
                        'Omc'=>array('fields' => array('Omc.id', 'Omc.name')),
                        'ProductType'=>array('fields' => array('ProductType.id', 'ProductType.name')),
                        'OmcCustomer'=>array('fields' => array('OmcCustomer.id', 'OmcCustomer.name'))
                    );

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

                            $delivery_quantity =  isset($obj['OmcCustomerOrder']['delivery_quantity']) ? $this->formatNumber($obj['OmcCustomerOrder']['delivery_quantity'],'number',0) : '';
                            $received_quantity =  isset($obj['OmcCustomerOrder']['received_quantity']) ? $this->formatNumber($obj['OmcCustomerOrder']['received_quantity'],'number',0) : '';
                            $delivery_date =  isset($obj['OmcCustomerOrder']['delivery_date']) ? $this->covertDate($obj['OmcCustomerOrder']['delivery_date'],'mysql_flip') : '';
                            
                        
                            $return_arr[] = array(
                                'id' => $obj['OmcCustomerOrder']['id'],
                                'cell' => array(
                                    $id='',
                                    $tank = '',
                                    $open_stock = '',
                                    $received_quantity = '',
                                    $volume_depot = '',
                                    $pump_day_sales = '',
                                    $closing_stock = '',
                                    $tank_day_sales = '',
                                    $variance = '',
                                    $variance_cedis = '',
                                    $comments = '',
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

                    if ($this->OmcCustomerOrder->save($this->sanitize($data))) {
                        $order_id  = $this->OmcCustomerOrder->id;
                        //Array Data here 
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


    function nct_sales_record($type = 'get')
    {   
        $permissions = $this->action_permission;
        $company_profile = $this->global_company;
        $sheet_id = $this->OmcSalesSheet->setUpSheet($company_profile['id'],$company_profile['omc_id']);
        if ($this->request->is('ajax')) {
            $this->autoRender = false;
            $this->autoLayout = false;
            $authUser = $this->Auth->user();
            $post = $this->sanitize($_POST);
            $post['modified_by'] = $authUser['id'];
            $post = $this->total_daily_sales_product($post);
            if ($this->OmcDailySalesProduct->save($post)) {
                //Update Operators Credit
                $this->OmcOperatorsCredit->setUp($sheet_id,$company_profile['id'],$company_profile['omc_id']);
                return json_encode(array('code' => 0, 'msg' => 'Record Saved!', 'data'=>$post));
            }
            else {
                return json_encode(array('code' => 1, 'msg' => 'Some errors occurred whiles saving the record.'));
            }
        }
        $form_data = $this->OmcDailySalesProduct->setUp($sheet_id,$company_profile['omc_id']);
        $table_setup = $this->OmcDailySalesProduct->getTableSetup();
        $table_total_setup = $this->OmcDailySalesProduct->getTotalTableSetup();
        $previous_data_raw = $this->OmcDailySalesProduct->getPreviousDayData($company_profile['id'],$company_profile['omc_id']);
        $previous_data = array();
        foreach($previous_data_raw as $spd){
            $previous_data[] = $spd['OmcDailySalesProduct'];
        }
        $data = $this->OmcDsrpDataOption->find('first',array(
            'conditions'=>array('omc_id'=>$company_profile['omc_id']),
            'recursive'=>-1
        ));
        $control_data = array();
        $control_data['bulk_stock_position_products'] = unserialize($data['OmcDsrpDataOption']['bulk_stock_position_products']);
        $control_data['daily_sales_products'] = unserialize($data['OmcDsrpDataOption']['daily_sales_products']);
        $control_data['lubricants_products'] = unserialize($data['OmcDsrpDataOption']['lubricants_products']);

        $price_change_data = array();
        foreach($this->price_change as $pn => $pr){
            $price_change_data[$pr['product_type_id']] = array(
                'name'=>$pn,
                'value'=>$pr['price']
            );
        }


        $this->set(compact('permissions','form_data','table_setup','previous_data','control_data','price_change_data','table_total_setup'));
    }

}