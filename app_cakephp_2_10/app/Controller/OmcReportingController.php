<?php
/**
 * @name BdcController.php
 */
App::import('Controller', 'OmcApp');

class OmcReportingController extends OmcAppController
{
    # Controller name

    var $name = 'OmcReporting';
    # set the model to use
    var $uses = array('Omc','BdcDistribution', 'BdcUser','OmcUser', 'BdcOmc', 'User', 'Depot', 'District', 'ProductType', 'Region','Bdc','OmcCustomerTankMinstocklevel','OmcCustomer', 'OmcSalesSheet','OmcBulkStockPosition','OmcBulkStockCalculation','OmcDailySalesProduct','OmcCashCreditSummary','OmcOperatorsCredit','OmcCustomersCredit','OmcLube','OmcDsrpDataOption','OmcCustomerStock');

    # Set the layout to use
    var $layout = 'omc_layout';

    public function beforeFilter($param_array = null)
    {
        parent::beforeFilter(array('omc_user_types'=>array('Allow All')));
    }


    function index()
    {
        $company_profile = $this->global_company;
        $start_dt = date('01-m-Y');
        $end_dt = date('t-m-Y');
        $product_type = null;
        $default_product_type = 'all';
        $omc = null;
        $default_omc = 'all';
        $product_name = '';
        $omc_name = '';
        if($this->request->is('post')){
            $start_dt = $this->covertDate($this->request->data['Query']['start_dt'],'mysql');
            $end_dt = $this->covertDate($this->request->data['Query']['end_dt'],'mysql');
            if($this->request->data['Query']['product_type'] != 'all'){
                $product_type = $this->request->data['Query']['product_type'];
                $default_product_type = $this->request->data['Query']['product_type'];
                $product_name = 'For '.$this->request->data['Query']['product_type_name'];
            }
            /*if($this->request->data['Query']['omc'] != 'all'){
                $omc = $this->request->data['Query']['omc'];
                $default_omc = $this->request->data['Query']['omc'];
                $omc_name = $this->request->data['Query']['omc_name'];
            }*/
        }
        $g_data = $this->getProductMonthlyConsolidate($company_profile['id'],$start_dt,$end_dt,$product_type);
        $grid_data = $g_data['raw_data'];
        $x_axis = $g_data['x-axis'];
        $bar_graph_data = $g_data['data'];
        $t_head = $g_data['t_head'];
        $t_body_data = $g_data['t_body_data'];

        $graph_title = 'Monthly Product Loading '.$product_name;
        $table_title = 'Monthly Product Loading Table '.$product_name;

        //Get all product types
        $products_list_p = $this->get_products();
        $products_list = array('all'=>'All Product Type');
        foreach($products_list_p as $arr){
            $products_list[$arr['id']] = $arr['name'];
        }

        //Get Bdc Omcs
       /* $condition_array = array(
            'BdcOmc.bdc_id' => $company_profile['id'],
            'BdcOmc.deleted' => 'n'
        );
        $contain = array('Omc'=>array('fields' => array('Omc.id', 'Omc.name')));
        $bdc_omcs = $this->BdcOmc->find('all', array('conditions' => $condition_array, 'contain'=>$contain, 'recursive' => 1));
        $omclists = array();
        foreach ($bdc_omcs as $value) {
            $omclists[$value['Omc']['id']] = $value['Omc']['name'];
        }
        $omclists['all'] = 'All OMCs';*/

        $controller = $this;
        $this->set(compact('controller','table_title','t_head','t_body_data','graph_title','grid_data','bar_graph_data', 'x_axis','products_list','omclists','end_dt','start_dt','default_omc','default_product_type'));
    }


    function print_export_monthly_distributions(){
        $download = false;
        $company_profile = $this->global_company;

        $media_type = $_POST['data_type'];
        $start_dt = $this->covertDate($_POST['data_start_dt'],'mysql');
        $end_dt = $this->covertDate($_POST['data_end_dt'],'mysql');
        $product_type = $_POST['data_product_type'];
        //$omc = $_POST['data_omc'];
        $product_name = 'For '.$_POST['data_product_type_name'];
        //$omc_name = $_POST['data_omc_name'];
        if($product_type == 'all'){
            $product_type = null;
            $product_name = '';
        }
        /*if($omc == 'all'){
            $omc = null;
            $omc_name = '';
        }*/

        $g_data = $this->getProductMonthlyConsolidate($company_profile['id'],$start_dt,$end_dt,$product_type);
        $grid_data = $g_data['raw_data'];
        $t_head = $g_data['t_head'];
        $t_body_data = $g_data['t_body_data'];

        $export_title = 'Monthly Product Loading '.$product_name;
        $table_title = 'Monthly Product Loading Table '.$product_name;

        $list_data = $t_body_data;
       /* foreach($grid_data as $key => $data){
            foreach($data as $pname => $qty){
                $list_data[] = array($key,$pname,$qty);
            }
        }*/

        $this->autoLayout = false;

        if($media_type == 'export'){
            if(!empty($list_data)){
                $download = true;
            }
            $list_headers = $t_head;
            $filename = $export_title;
            $res = $this->convertToExcel($list_headers,$list_data,$filename);
            $objPHPExcel = $res['excel_obj'];
            $filename = $res['filename'];
        }
        elseif($media_type == 'print'){
            $this->autoLayout = true;
            $this->layout = 'print_layout';
        }
        $print_title = $table_title;
        $controller = $this;

        $this->set(compact('controller','company_profile','print_title','table_title','t_head','t_body_data','graph_title','objPHPExcel', 'download', 'filename','media_type','grid_data'));
    }


    function report_bdc_variant(){
        $company_profile = $this->global_company;
        $year = date('Y');
        $month = date('m');
        $product_type = null;
        $default_product_type = 'all';
        $default_month = $month;
        $product_name = '';
        if($this->request->is('post')){
            $year = $this->request->data['Query']['year'];
            $month = $this->request->data['Query']['month'];
            if($this->request->data['Query']['product_type'] != 'all'){
                $product_type = $this->request->data['Query']['product_type'];
                $default_product_type = $this->request->data['Query']['product_type'];
                $product_name = 'For '.$this->request->data['Query']['product_type_name'];
            }
            $default_month = $month;
        }
        $g_data = $this->getMonthlyBdcVariant($company_profile['id'],$month,$year,$product_type);
        $raw_data = $g_data['raw_data'];
        $pie_data = $g_data['data'];

        $month_name = $this->getMonths($default_month);

        $graph_title = $month_name.', Product Loading By BDCs '.$product_name;
        $table_title = $month_name.', Product Loading By BDCs Table '.$product_name;

        //Get all product types
        $products_list_p = $this->get_products();
        $products_list = array('all'=>'All Product Type');
        foreach($products_list_p as $arr){
            $products_list[$arr['id']] = $arr['name'];
        }

        $month_list = $this->getMonths();
        $controller = $this;

        $this->set(compact('controller','table_title','graph_title','raw_data','pie_data','products_list','year','month','month_list','default_month','default_product_type'));
    }


    function print_export_bdc_variant(){
        $download = false;
        $company_profile = $this->global_company;
        $media_type = $_POST['data_type'];
        $year = $_POST['data_year'];
        $month = $_POST['data_month'];
        $month_name = $this->getMonths($month);
        $product_type = $_POST['data_product_type'];
        $product_name = 'For '.$_POST['data_product_type_name'];
        if($product_type == 'all'){
            $product_type = null;
            $product_name = '';
        }

        $g_data = $this->getMonthlyBdcVariant($company_profile['id'],$month,$year,$product_type);
        $raw_data = $g_data['raw_data'];
        //$pie_data = $g_data['data'];

        $export_title = $month_name.', Product Loading By BDCs '.$product_name;
        $table_title = $month_name.', Product Loading By BDCs Table '.$product_name;

        $list_data = $raw_data;
        /*foreach($g_data as $key => $data){
            foreach($data as $pname => $qty){
                $list_data[] = array($key,$pname,$qty);
            }
        }*/

        $this->autoLayout = false;

        if($media_type == 'export'){
            if(!empty($list_data)){
                $download = true;
            }
            $list_headers = array('BDC','Total Quantity');
            $filename = $export_title;
            $res = $this->convertToExcel($list_headers,$list_data,$filename);
            $objPHPExcel = $res['excel_obj'];
            $filename = $res['filename'];
        }
        elseif($media_type == 'print'){
            $this->autoLayout = true;
            $this->layout = 'print_layout';
        }
        $print_title = $table_title;
        $controller = $this;

        $this->set(compact('controller','print_title','table_title','graph_title','objPHPExcel', 'download', 'filename','media_type','list_data'));
    }



    function report_depot_variant(){
        $company_profile = $this->global_company;
        $year = date('Y');
        $month = date('m');
        $product_type = null;
        $default_product_type = 'all';
        $default_month = $month;
        $product_name = '';
        if($this->request->is('post')){
            $year = $this->request->data['Query']['year'];
            $month = $this->request->data['Query']['month'];
            if($this->request->data['Query']['product_type'] != 'all'){
                $product_type = $this->request->data['Query']['product_type'];
                $default_product_type = $this->request->data['Query']['product_type'];
                $product_name = 'For '.$this->request->data['Query']['product_type_name'];
            }
            $default_month = $month;
        }
        $g_data = $this->getMonthlyDepotVariant($company_profile['id'],$month,$year,$product_type);
        $raw_data = $g_data['raw_data'];
        $pie_data = $g_data['data'];

        $month_name = $this->getMonths($default_month);

        $graph_title = $month_name.', Product Loading By Depot  '.$product_name;
        $table_title = $month_name.', Product Loading By Depot Table '.$product_name;

        //Get all product types
        $products_list_p = $this->get_products();
        $products_list = array('all'=>'All Product Type');
        foreach($products_list_p as $arr){
            $products_list[$arr['id']] = $arr['name'];
        }

        $month_list = $this->getMonths();
        $controller = $this;

        $this->set(compact('controller','table_title','graph_title','raw_data','pie_data','products_list','year','month','month_list','default_month','default_product_type'));
    }


    function print_export_depot_variant(){
        $download = false;
        $company_profile = $this->global_company;
        $media_type = $_POST['data_type'];
        $year = $_POST['data_year'];
        $month = $_POST['data_month'];
        $month_name = $this->getMonths($month);
        $product_type = $_POST['data_product_type'];
        $product_name = 'For '.$_POST['data_product_type_name'];
        if($product_type == 'all'){
            $product_type = null;
            $product_name = '';
        }

        $g_data = $this->getMonthlyDepotVariant($company_profile['id'],$month,$year,$product_type);
        $raw_data = $g_data['raw_data'];
        //$pie_data = $g_data['data'];

        $export_title = $month_name.', Product Loading By Depot '.$product_name;
        $table_title = $month_name.', Product Loading By Depot Table '.$product_name;

        $list_data = $raw_data;
        /*foreach($g_data as $key => $data){
            foreach($data as $pname => $qty){
                $list_data[] = array($key,$pname,$qty);
            }
        }*/

        $this->autoLayout = false;

        if($media_type == 'export'){
            if(!empty($list_data)){
                $download = true;
            }
            $list_headers = array('Loading Depot','Total Quantity');
            $filename = $export_title;
            $res = $this->convertToExcel($list_headers,$list_data,$filename);
            $objPHPExcel = $res['excel_obj'];
            $filename = $res['filename'];
        }
        elseif($media_type == 'print'){
            $this->autoLayout = true;
            $this->layout = 'print_layout';
        }
        $print_title = $table_title;
        $controller = $this;
        $this->set(compact('controller','print_title','table_title','graph_title','objPHPExcel', 'download', 'filename','media_type','list_data'));
    }


    function report_orders(){
        $company_profile = $this->global_company;
        $start_dt = date('01-m-Y');
        $end_dt = date('t-m-Y');
        $bdc = null;
        $default_bdc = 'all';
        $bdc_name = '';
        $group_by = 'monthly';
        if($this->request->is('post')){
            $start_dt = $this->covertDate($this->request->data['Query']['start_dt'],'mysql');
            $end_dt = $this->covertDate($this->request->data['Query']['end_dt'],'mysql');
            $group_by = $this->request->data['Query']['group_by'];
            if($this->request->data['Query']['bdc'] != 'all'){
                $bdc = $this->request->data['Query']['bdc'];
                $default_bdc = $this->request->data['Query']['bdc'];
                $bdc_name = 'For '.$this->request->data['Query']['bdc_name'];
            }
        }
        $group_by_title = 'Yearly';
        if($group_by == 'monthly'){
            $group_by_title = 'Monthly';
        }

        $g_data =  $this->get_orders($start_dt,$end_dt,$group_by,$bdc);
        $graph_title = $group_by_title." Order-Consolidated ".$bdc_name;
        $table_title = $group_by_title." Order-Consolidated ".$bdc_name;
        $controller = $this;

        //Get Bdcs for this Omc
        $omc_bdcs = $this->get_bdc_list();
        $bdc_lists = array('all'=>'All BDCs');
        foreach($omc_bdcs as $arr){
            $bdc_lists[$arr['id']] = $arr['name'];
        }

        $this->set(compact('controller','print_title','table_title','graph_title','bdc_lists','g_data','group_by','default_bdc','start_dt','end_dt'));
    }


    function print_export_orders(){
        $download = false;
        $company_profile = $this->global_company;
        $media_type = $_POST['data_type'];
        $start_dt = $this->covertDate($_POST['data_start_dt'],'mysql');
        $end_dt = $this->covertDate($_POST['data_end_dt'],'mysql');
        $group_by = $_POST['data_group_by'];
        $bdc = $_POST['data_bdc'];
        $bdc_name = 'For '.$_POST['data_bdc_name'];
        if($bdc == 'all'){
            $bdc = null;
            $bdc_name = '';
        }
        $g_data =  $this->get_orders($start_dt,$end_dt,$group_by,$bdc);
        $group_by_title = 'Yearly';
        if($group_by == 'monthly'){
            $group_by_title = 'Monthly';
        }

        $export_title = $group_by_title." Order-Consolidated ".$bdc_name;
        $table_title = $group_by_title." Order-Consolidated ".$bdc_name;

        $this->autoLayout = false;

        if($media_type == 'export'){
            $list_headers = $g_data['table']['thead'];
            $list_data = $g_data['table']['tbody'];
            if(!empty($list_data)){
                $download = true;
            }
            $filename = $export_title;
            $res = $this->convertToExcel($list_headers,$list_data,$filename);
            $objPHPExcel = $res['excel_obj'];
            $filename = $res['filename'];
        }
        elseif($media_type == 'print'){
            $this->autoLayout = true;
            $this->layout = 'print_layout';
        }
        $print_title = $table_title;
        $controller = $this;

        $this->set(compact('controller','company_profile','print_title','table_title','objPHPExcel', 'download', 'filename','media_type','g_data'));
    }


    function stock_histories(){
        $company_profile = $this->global_company;
        $month = date('m');
        $year = date('Y');
        $customer = 'all';
        $customer_id = null;

        if($this->request->is('post')){
            $month = $this->request->data['Query']['month'];
            $year = $this->request->data['Query']['year'];
            $customer = $this->request->data['Query']['customer'];
            if($customer != 'all'){
                $customer_id = $customer;
            }
        }

        $g_data = $this->getCustomersStockHistory($month,$year,$customer_id);

        $this->getDailyStockHistory(date('Y-m-d'));
        //debug($g_data);

        $customer_lists = array('all'=>'All');
        $ct_data = $this->get_customer_list();
        foreach($ct_data as $ct){
            $customer_lists[$ct['id']]=$ct['name'];
        }

        $month_lists = $this->getMonths();

        $month_name = $this->getMonths($month);
        $table_title = $month_name.'-'.$year.', Historic Stations Stock Report ';

        $controller = $this;
        $this->set(compact('controller','table_title','customer','year','month','month_lists','customer_lists','g_data'));
    }

    /** Station Reporting */

    function dsrp_report (){
        $company_profile = $this->global_company;
        $default_month = date('m');
        $default_year = date('Y');
        $default_day = date('d');
        $default_customer = '';

        $default_dsrp = 'bsp';

        $customer_list = array();
        $ct_data = $this->get_customer_list();
        $first_customer_id = '';
        foreach($ct_data as $ct){
            $customer_list[$ct['id']]=$ct['name'];
            if(empty($first_customer_id)){
                $first_customer_id = $ct['id'];
            }
        }
        $default_customer = $first_customer_id;


        if($this->request->is('post')){
            $default_month = $this->request->data['Query']['month'];
            $default_year = $this->request->data['Query']['year'];
            $default_day = $this->request->data['Query']['day'];
            $default_dsrp = $this->request->data['Query']['dsrp_opt'];
            $default_customer = $this->request->data['Query']['customer'];
        }
        $full_date = $default_year.'-'.$default_month.'-'.$default_day;
        $omc_id = $company_profile['id'];
        $g_data = $this->get_dsrp_data($full_date,$omc_id,$default_customer,$default_dsrp);

        $start_year = $this->OmcSalesSheet->getStartYear('omc',$company_profile['id']);
        $month_list = $this->getMonths();
        $year_list = $this->getYears($start_year);
        $day_list = $this->getDays();
        $dsrp_list = $this->getDSRPoptions();

        $month_name = $this->getMonths($default_month);
        $get_dsrp_name = $this->getDSRPoptions($default_dsrp);
        $dsrp_name = ($get_dsrp_name) && !empty($get_dsrp_name) ? $get_dsrp_name : 'DSRP';
        $table_title = $this->__add_ordinal_suffix($default_day).'-'.$month_name.'-'.$default_year.', '.$dsrp_name.' Report ';

        $controller = $this;
        $this->set(compact('controller','table_title','default_customer','default_year','default_month','default_day','default_dsrp','month_list','year_list','day_list','dsrp_list','customer_list','g_data'));
    }


    function get_dsrp_data($sheet_date,$omc_id,$customer_id,$default_dsrp){
        $sheet = $this->OmcSalesSheet->getSheet($customer_id,$omc_id,$sheet_date);
        if($sheet){
            $model = '';
            if($default_dsrp == 'bsp'){
                $model = 'OmcBulkStockPosition';
            }
            elseif($default_dsrp == 'bsc'){
                $model = 'OmcBulkStockCalculation';
            }
            elseif($default_dsrp == 'dsp'){
                $model = 'OmcDailySalesProduct';
            }
            elseif($default_dsrp == 'ccs'){
                $model = 'OmcCashCreditSummary';
            }
            elseif($default_dsrp == 'opc'){
                $model = 'OmcOperatorsCredit';
            }
            elseif($default_dsrp == 'cmc'){
                $model = 'OmcCustomersCredit';
            }
            elseif($default_dsrp == 'lbp'){
                $model = 'OmcLube';
            }
            if(empty($model)){
                return false;
            }
            else{
                $sheet_id = $sheet['OmcSalesSheet']['id'];
                $gdata = $this->$model->getData($sheet_id);
                $header = $this->$model->getFullHeader();
               /* if( $model == 'OmcDailySalesProduct'){
                    $header = $this->$model->getFullHeader();
                }*/

                if($gdata){
                    return array(
                        'header'=>$header,
                        'data'=>$gdata
                    );
                }
                else{
                    return false;
                }
            }
        }
        else{
            return false;
        }
    }


    function export_dsrp(){
        $this->autoLayout = false;
        $download = false;
        $company_profile = $this->global_company;

        if($this->request->is('post')){
            $dsrp_opt = $_POST['data_dsrp_type'];
            $customer = $_POST['data_customer'];
            $month = $_POST['data_month'];
            $year = $_POST['data_year'];
            $day = $_POST['data_day'];
            $media_type = $_POST['data_doc_type'];
        }
        $full_date = $year.'-'.$month.'-'.$day;
        $omc_id = $company_profile['id'];
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

    }

    function process_export_dsrp_data($data,$dsrp_opt){
        $return_arr = array('header'=>array(),'data'=>array());
        if(!$data){
            return $return_arr;
        }
        $table_setup = $data['header'];
        $form_data = $data['data'];
        $with_header = in_array($dsrp_opt,array('ccs','opc','cmc')) ? false:true;
        $model = '';
        if($dsrp_opt == 'bsp'){
            $model = 'OmcBulkStockPosition';
        }
        elseif($dsrp_opt == 'bsc'){
            $model = 'OmcBulkStockCalculation';
        }
        elseif($dsrp_opt == 'dsp'){
            $model = 'OmcDailySalesProduct';
        }
        elseif($dsrp_opt == 'ccs'){
            $model = 'OmcCashCreditSummary';
        }
        elseif($dsrp_opt == 'opc'){
            $model = 'OmcOperatorsCredit';
        }
        elseif($dsrp_opt == 'cmc'){
            $model = 'OmcCustomersCredit';
        }
        elseif($dsrp_opt == 'lbp'){
            $model = 'OmcLube';
        }

        if($with_header){
            foreach($table_setup as $row){
                $return_arr['header'][] = $row['header'].' '.$row['unit'];
            }
            foreach($form_data as $row){
                $new_row = array();
                foreach($table_setup as $tr_row){
                    $field = $tr_row['field'];
                    $format = $tr_row['format'];
                    $cell_value = $field_value = $row[$model][$field];
                    if(is_numeric($field_value)){
                        $decimal_places = 0;
                        if($format == 'float'){
                            $decimal_places = 2;
                        }
                        $cell_value = $this->formatNumber($cell_value,'money',$decimal_places);
                    }
                    $new_row[] = $cell_value;
                }
                $return_arr['data'][] = $new_row;
            }
        }
        else{
            $return_arr['header'][] = '';
            $return_arr['header'][] = '';

            foreach($table_setup as $row){
                $new_row = array();
                $header = $row['header'];
                $field = $row['field'];
                $format = $row['format'];
                $cell_value = $field_value = $form_data[0][$model][$field];
                if(is_numeric($field_value)){
                    $decimal_places = 0;
                    if($format == 'float'){
                        $decimal_places = 2;
                    }
                    $cell_value = $this->formatNumber($cell_value,'money',$decimal_places);
                }
                $new_row[] = $header;
                $new_row[] = $cell_value;
                $return_arr['data'][] = $new_row;
            }
        }

        return $return_arr;
    }


    function view_station_dashboard($customer_id,$year,$month,$day){
        $this->layout = 'no_menu_layout';
        $company_profile = $this->global_company;
        $full_date = $year.'-'.$month.'-'.$day;
        $date = $full_date;
        $customer_data = $this->OmcCustomer->getCustomerById($customer_id);
        $customer_arr = array('id'=>$customer_id);
        $last_stock_updates = $this->OmcCustomerStock->__getStockBoard($customer_arr);
        $widget_data_cash_credit_summary = $this->OmcCashCreditSummary->widget_cash_credit_summary($customer_id,$company_profile['id'],$date);
        $widget_daily_sales_product = $this->OmcDailySalesProduct->widget_daily_sale_product($customer_id,$company_profile['id'],$date);
        $pie_daily_sales_product = array();
        foreach($widget_daily_sales_product as $row){
            if($row['value'] != null){
                $pie_daily_sales_product[]= array(
                    $row['header'],floatval($row['value'])
                );
            }
        }
        $widget_bulk_stock_calc = $this->OmcBulkStockCalculation->widget_bulk_stock_calc($customer_id,$company_profile['id'],$date);
        $bar_data = array(
            'x-axis'=>array(),
            'series'=>array(
                array('name'=>'Meter Reading','data'=>array()),
                array('name'=>'Dipping','data'=>array())
            )
        );
        foreach($widget_bulk_stock_calc as $row){
            if($row['closing_stock'] != null && $row['dipping'] != null){
                $bar_data['x-axis'][]= $row['products'];
                $bar_data['series'][0]['data'][]= floatval($row['closing_stock']);//meter_reading
                $bar_data['series'][1]['data'][]= floatval($row['dipping']);//dipping
            }
        }
        $format_date =  date('l jS F Y',strtotime($date));
        $customer_name = isset($customer_data['OmcCustomer'])? $customer_data['OmcCustomer']['name'] : 'Station';
        $this->set(compact('format_date','last_stock_updates','widget_data_cash_credit_summary','pie_daily_sales_product','bar_data','customer_name'));
    }



    function station_orders (){
        $company_profile = $this->global_company;
        $start_dt = date('01-m-Y');
        $end_dt = date('t-m-Y');
        $omc_customer = null;
        $default_omc_customer = 'all';
        $omc_customer_name = '';
        $group_by = 'monthly';
        if($this->request->is('post')){
            $start_dt = $this->covertDate($this->request->data['Query']['start_dt'],'mysql');
            $end_dt = $this->covertDate($this->request->data['Query']['end_dt'],'mysql');
            $group_by = $this->request->data['Query']['group_by'];
            if($this->request->data['Query']['omc_customer'] != 'all'){
                $omc_customer = $this->request->data['Query']['omc_customer'];
                $default_omc_customer = $this->request->data['Query']['omc_customer'];
                $omc_customer_name = 'For '.$this->request->data['Query']['omc_customer_name'];
            }
        }
        $group_by_title = 'Yearly';
        if($group_by == 'monthly'){
            $group_by_title = 'Monthly';
        }

        $g_data =  $this->get_station_orders($start_dt,$end_dt,$group_by,$omc_customer);
        $graph_title = $group_by_title." Station Orders ".$omc_customer_name;
        $table_title = $group_by_title." Station Orders ".$omc_customer_name;
        $controller = $this;

        //Get Omc for this Bdc
        $omc_customerlists_p = $this->get_customer_list();
        $omc_customer_customer_lists = array('all'=>'All Stations');
        foreach($omc_customerlists_p as $arr){
            $omc_customer_lists[$arr['id']] = $arr['name'];
        }

        $this->set(compact('controller','print_title','table_title','graph_title','omc_customer_lists','g_data','group_by','default_omc_customer','start_dt','end_dt'));
    }


    function print_export_station_orders(){
        $download = false;
        $company_profile = $this->global_company;
        $media_type = $_POST['data_type'];
        $start_dt = $this->covertDate($_POST['data_start_dt'],'mysql');
        $end_dt = $this->covertDate($_POST['data_end_dt'],'mysql');
        $group_by = $_POST['data_group_by'];
        $omc_customer = $_POST['data_omc_customer'];
        $omc_customer_name = 'For '.$_POST['data_omc_customer_name'];
        if($omc_customer == 'all'){
            $omc_customer = null;
            $omc_customer_name = '';
        }
        $g_data =  $this->get_station_orders($start_dt,$end_dt,$group_by,$omc_customer_customer);
        $group_by_title = 'Yearly';
        if($group_by == 'monthly'){
            $group_by_title = 'Monthly';
        }

        $export_title = $group_by_title." Station Orders ".$omc_customer_name;
        $table_title = $group_by_title." Station Orders ".$omc_customer_name;

        $this->autoLayout = false;

        if($media_type == 'export'){
            $list_headers = $g_data['table']['thead'];
            $list_data = $g_data['table']['tbody'];
            if(!empty($list_data)){
                $download = true;
            }
            $filename = $export_title;
            $res = $this->convertToExcel($list_headers,$list_data,$filename);
            $objPHPExcel = $res['excel_obj'];
            $filename = $res['filename'];
        }
        elseif($media_type == 'print'){
            $this->autoLayout = true;
            $this->layout = 'print_layout';
        }
        $print_title = $table_title;
        $controller = $this;

        $this->set(compact('controller','company_profile','print_title','table_title','objPHPExcel', 'download', 'filename','media_type','g_data'));
    }



    function station_product_loading()
    {
        $company_profile = $this->global_company;
        $start_dt = date('01-m-Y');
        $end_dt = date('t-m-Y');
        $product_type = null;
        $default_product_type = 'all';
        $omc_customer = null;
        $default_omc_customer = 'all';
        $product_name = '';
        $omc_customer_name = '';
        if($this->request->is('post')){
            $start_dt = $this->covertDate($this->request->data['Query']['start_dt'],'mysql');
            $end_dt = $this->covertDate($this->request->data['Query']['end_dt'],'mysql');
            if($this->request->data['Query']['product_type'] != 'all'){
                $product_type = $this->request->data['Query']['product_type'];
                $default_product_type = $this->request->data['Query']['product_type'];
                $product_name = 'For '.$this->request->data['Query']['product_type_name'];
            }
            if($this->request->data['Query']['omc_customer'] != 'all'){
                $omc_customer = $this->request->data['Query']['omc_customer'];
                $default_omc_customer = $this->request->data['Query']['omc_customer'];
                $omc_customer_name = $this->request->data['Query']['omc_customer_name'];
            }
        }
        $g_data = $this->getStationProductReport($company_profile['id'],$start_dt,$end_dt,$product_type,$omc_customer);
        $grid_data_raw = $g_data['raw_data'];
        $x_axis = $g_data['x-axis'];
        $bar_graph_data = $g_data['data'];
        $t_head = $g_data['t_head'];
        $t_body_data = $g_data['t_body_data'];

        //debug($t_body_data);

        $graph_title = $omc_customer_name.' Station Monthly Product Loading '.$product_name;
        $table_title = $omc_customer_name.' Station Monthly Product Loading Table '.$product_name;

        //Get all product types
        $products_list_p = $this->get_products();
        $products_list = array('all'=>'All Product Type');
        foreach($products_list_p as $arr){
            $products_list[$arr['id']] = $arr['name'];
        }

        //Get Bdc Omcs
        $omc_customerlists_p = $this->get_customer_list();
        $omc_customerlists = array('all'=>'All Stations');
        foreach($omc_customerlists_p as $arr){
            $omc_customerlists[$arr['id']] = $arr['name'];
        }

        $controller = $this;
        $this->set(compact('controller','table_title','t_head','t_body_data','graph_title','grid_data','bar_graph_data', 'x_axis','products_list','omc_customerlists','end_dt','start_dt','default_omc_customer','default_product_type'));
    }


    function print_export_station_product(){
        $download = false;
        $company_profile = $this->global_company;

        $media_type = $_POST['data_type'];
        $start_dt = $this->covertDate($_POST['data_start_dt'],'mysql');
        $end_dt = $this->covertDate($_POST['data_end_dt'],'mysql');
        $product_type = $_POST['data_product_type'];
        $omc_customer = $_POST['data_omc_customer'];
        $product_name = 'For '.$_POST['data_product_type_name'];
        $omc_customer_name = $_POST['data_omc_customer_name'];
        if($product_type == 'all'){
            $product_type = null;
            $product_name = '';
        }
        if($omc_customer == 'all'){
            $omc_customer = null;
            $omc_customer_name = '';
        }

        $g_data = $this->getStationProductReport($company_profile['id'],$start_dt,$end_dt,$product_type,$omc_customer);
        $grid_data = $g_data['raw_data'];
        $t_head = $g_data['t_head'];
        $t_body_data = $g_data['t_body_data'];

        $export_title = $omc_customer_name.' Monthly Product Loading '.$product_name;
        $table_title = $omc_customer_name.' Monthly Product Loading Table '.$product_name;

        $list_data = $t_body_data;
        /*foreach($grid_data as $key => $data){
            foreach($data as $pname => $qty){
                $list_data[] = array($key,$pname,$qty);
            }
        }*/

        $this->autoLayout = false;

        if($media_type == 'export'){
            if(!empty($list_data)){
                $download = true;
            }
            $list_headers = $t_head;
            $filename = $export_title;
            $res = $this->convertToExcel($list_headers,$list_data,$filename);
            $objPHPExcel = $res['excel_obj'];
            $filename = $res['filename'];
        }
        elseif($media_type == 'print'){
            $this->autoLayout = true;
            $this->layout = 'print_layout';
        }
        $print_title = $table_title;
        $controller = $this;

        $this->set(compact('controller','company_profile','print_title','table_title','t_head','t_body_data','graph_title','objPHPExcel', 'download', 'filename','media_type','grid_data'));
    }

}