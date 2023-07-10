<?php
/**
 * @name BdcController.php
 */
App::import('Controller', 'BdcApp');

class BdcReportingController extends BdcAppController
{
    # Controller name

    var $name = 'BdcReporting';
    # set the model to use
    var $uses = array('Bdc','BdcDistribution', 'BdcUser','OmcUser', 'BdcOmc', 'User', 'Depot', 'District', 'ProductType', 'Region', 'Waybill','StockTrading');

    # Set the layout to use
    var $layout = 'bdc_layout';

    public function beforeFilter($param_array = null)
    {
        parent::beforeFilter(array('bdc_user_types'=>array('Allow All')));
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
            if($this->request->data['Query']['omc'] != 'all'){
                $omc = $this->request->data['Query']['omc'];
                $default_omc = $this->request->data['Query']['omc'];
                $omc_name = $this->request->data['Query']['omc_name'];
            }
        }
        $g_data = $this->getProductMonthlyConsolidate($company_profile['id'],$start_dt,$end_dt,$product_type,$omc);
        $grid_data_raw = $g_data['raw_data'];
        $x_axis = $g_data['x-axis'];
        $bar_graph_data = $g_data['data'];
        $t_head = $g_data['t_head'];
        $t_body_data = $g_data['t_body_data'];

        //debug($t_body_data);

        $graph_title = $omc_name.' Monthly Product Loading '.$product_name;
        $table_title = $omc_name.' Monthly Product Loading Table '.$product_name;

        //Get all product types
        $products_list_p = $this->get_products();
        $products_list = array('all'=>'All Product Type');
        foreach($products_list_p as $arr){
            $products_list[$arr['id']] = $arr['name'];
        }

        //Get Bdc Omcs
        $omclists_p = $this->get_omc_list();
        $omclists = array('all'=>'All OMCs');
        foreach($omclists_p as $arr){
            $omclists[$arr['id']] = $arr['name'];
        }

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
        $omc = $_POST['data_omc'];
        $product_name = 'For '.$_POST['data_product_type_name'];
        $omc_name = $_POST['data_omc_name'];
        if($product_type == 'all'){
            $product_type = null;
            $product_name = '';
        }
        if($omc == 'all'){
            $omc = null;
            $omc_name = '';
        }

        $g_data = $this->getProductMonthlyConsolidate($company_profile['id'],$start_dt,$end_dt,$product_type,$omc);
        $grid_data = $g_data['raw_data'];
        $t_head = $g_data['t_head'];
        $t_body_data = $g_data['t_body_data'];

        $export_title = $omc_name.' Monthly Product Loading '.$product_name;
        $table_title = $omc_name.' Monthly Product Loading Table '.$product_name;

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


    function report_omc_variant(){
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
        $g_data = $this->getMonthlyOmcVariant($company_profile['id'],$month,$year,$product_type);
        $raw_data = $g_data['raw_data'];
        $pie_data = $g_data['data'];

        $month_name = $this->getMonths($default_month);

        $graph_title = $month_name.', Product Loading By OMCs '.$product_name;
        $table_title = $month_name.', Product Loading By OMCs Table '.$product_name;

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


    function print_export_omc_variant(){
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

        $g_data = $this->getMonthlyOmcVariant($company_profile['id'],$month,$year,$product_type);
        $raw_data = $g_data['raw_data'];
        //$pie_data = $g_data['data'];

        $export_title = $month_name.', Product Loading By OMCs '.$product_name;
        $table_title = $month_name.', Product Loading By OMCs Table '.$product_name;

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
            $list_headers = array('OMC','Total Quantity');
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

        $graph_title = $month_name.', Product Loading By Depot '.$product_name;
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
        $omc = null;
        $default_omc = 'all';
        $omc_name = '';
        $group_by = 'monthly';
        if($this->request->is('post')){
            $start_dt = $this->covertDate($this->request->data['Query']['start_dt'],'mysql');
            $end_dt = $this->covertDate($this->request->data['Query']['end_dt'],'mysql');
            $group_by = $this->request->data['Query']['group_by'];
            if($this->request->data['Query']['omc'] != 'all'){
                $omc = $this->request->data['Query']['omc'];
                $default_omc = $this->request->data['Query']['omc'];
                $omc_name = 'For '.$this->request->data['Query']['omc_name'];
            }
        }
        $group_by_title = 'Yearly';
        if($group_by == 'monthly'){
            $group_by_title = 'Monthly';
        }

        $g_data =  $this->get_orders($start_dt,$end_dt,$group_by,null,$omc);
        $graph_title = $group_by_title." Customer Orders ".$omc_name;
        $table_title = $group_by_title." Customer Orders ".$omc_name;
        $controller = $this;

        //Get Omc for this Bdc
        $omclists_p = $this->get_omc_list();
        $omc_lists = array('all'=>'All OMCs');
        foreach($omclists_p as $arr){
            $omc_lists[$arr['id']] = $arr['name'];
        }

        $this->set(compact('controller','print_title','table_title','graph_title','omc_lists','g_data','group_by','default_omc','start_dt','end_dt'));
    }


    function print_export_orders(){
        $download = false;
        $company_profile = $this->global_company;
        $media_type = $_POST['data_type'];
        $start_dt = $this->covertDate($_POST['data_start_dt'],'mysql');
        $end_dt = $this->covertDate($_POST['data_end_dt'],'mysql');
        $group_by = $_POST['data_group_by'];
        $omc = $_POST['data_omc'];
        $omc_name = 'For '.$_POST['data_omc_name'];
        if($omc == 'all'){
            $omc = null;
            $omc_name = '';
        }
        $g_data =  $this->get_orders($start_dt,$end_dt,$group_by,null,$omc);
        $group_by_title = 'Yearly';
        if($group_by == 'monthly'){
            $group_by_title = 'Monthly';
        }

        $export_title = $group_by_title." Customer Orders ".$omc_name;
        $table_title = $group_by_title." Customer Orders ".$omc_name;

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


}