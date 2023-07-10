<?php

/**
 * @name BdcOrdersController.php
 */
App::import('Controller', 'NpaApp');

class NpaReportController extends NpaAppController
{
    # Controller name

    var $name = 'NpaReport';
    # set the model to use
    var $uses = array('Bdc','BdcDistribution','BdcStockHistory','Omc','OmcBdcDistribution','OmcCustomerStock');

    # Set the layout to use
    var $layout = 'npa_layout';

    public function beforeFilter($param_array = null)
    {
        parent::beforeFilter();
    }


    function index()
    {
        $this->redirect('initial_startup_stocks');
    }

    function bdc_stock(){
        $start_dt = date('Y-m-1');
        $end_dt = date('Y-m-t');
        $bdc = '';
        $status = null;
        if($this->request->is('post')){
            $bdc = $this->request->data['Query']['bdc'];
            $start_dt = $this->request->data['Query']['start_dt'];
            $end_dt = $this->request->data['Query']['end_dt'];
        }
        if(empty($bdc)){
            $bdc = 1;
        }
        $grid = $this->BdcStockHistory->getStockHistories($bdc,$start_dt,$end_dt,$status);

        $bdc_data = $this->Bdc->getBDCs();
        $bdc_list =array();
        foreach($bdc_data as $arr){
            $bdc_list[$arr['Bdc']['id']] = $arr['Bdc']['name'];
        }
        $bdc_name = $bdc_list[$bdc];

        $controller = $this;
        $this->set(compact('controller', 'grid','start_dt','end_dt','bdc_list','bdc','bdc_name'));
    }

    function print_export_bdc_stock(){
        $download = false;
        $company_profile = $this->global_company;
        $media_type = $_POST['data_type'];
        $start_dt = $this->covertDate($_POST['data_start'],'mysql');
        $end_dt = $this->covertDate($_POST['data_end'],'mysql');
        $bdc = $_POST['data_bdc'];
        $bdc_name = $_POST['data_bdc_name'];
        $status = null;

        $grid = $this->BdcStockHistory->getStockHistories($bdc,$start_dt,$end_dt,$status);

        $export_title = $table_title = $bdc_name.' Stock Positions From '.$start_dt.' to '.$end_dt;

        $this->autoLayout = false;

        if($media_type == 'export'){
            if(!empty($grid)){
                $download = true;
            }
            $list_headers = array();
            $filename = $export_title;
            $res = $this->convertToExcel($list_headers,$grid,$filename);
            $objPHPExcel = $res['excel_obj'];
            $filename = $res['filename'];
        }
        elseif($media_type == 'print'){
            $this->autoLayout = true;
            $this->layout = 'print_layout';
        }

        $print_title = $table_title;
        $controller = $this;

        $this->set(compact('controller','company_profile','print_title','table_title','graph_title','objPHPExcel', 'download', 'filename','media_type','grid'));
    }


    function omc_uppf() {
        $company_profile = $this->global_company;
        $start_dt = date('01-m-Y');
        $end_dt = date('t-m-Y');
        $product_group = 'Premix';
        $default_product_group = 'Premix';
        $product_group_name = 'On Premix';
        $omc = '';
        if($this->request->is('post')){
            $start_dt = $this->request->data['Query']['start_dt'];
            $end_dt = $this->request->data['Query']['end_dt'];
            $omc = $this->request->data['Query']['omc'];
            $product_group = $this->request->data['Query']['product_group'];
            $default_product_group = $this->request->data['Query']['product_group'];
            $product_group_name = 'On '.$this->request->data['Query']['product_group_name'];
        }
        if(empty($omc)){
            $omc = 1;
        }
        $g_data = $this->BdcDistribution->getUPPF($this->covertDate($start_dt,'mysql'),$this->covertDate($end_dt,'mysql'),$product_group, $omc);

        $t_head = $g_data['t_head'];
        $t_body_data = $g_data['t_body_data'];

        //Get all product group
        $product_group_data = $this->get_product_group();
        //$product_group_list = array('all'=>'All Product Type');
        $product_group_list = array();
        foreach($product_group_data as $arr){
            $product_group_list[$arr] = $arr;
        }

        $omc_data = $this->Omc->getOMCs();
        $omc_list =array();
        foreach($omc_data as $arr){
            $omc_list[$arr['Omc']['id']] = $arr['Omc']['name'];
        }
        $omc_name = $omc_list[$omc];

        $table_title = $omc_name.' UPPF Returns '.$product_group_name;

        $controller = $this;
        $this->set(compact('controller','table_title','t_head','t_body_data','product_group_list','end_dt','start_dt','default_product_group','omc','omc_list','omc_name'));
    }


    function print_export_omc_uppf(){
        $download = false;
        $company_profile = $this->global_company;

        $media_type = $_POST['data_type'];
        $start_dt = $this->covertDate($_POST['data_start_dt'],'mysql');
        $end_dt = $this->covertDate($_POST['data_end_dt'],'mysql');
        $product_group = $_POST['data_product_group'];
        $product_group_name = 'On '.$_POST['data_product_group_name'];
        $omc = $_POST['data_omc'];
        $omc_name = $_POST['data_omc_name'];
        if($product_group == 'all'){
            $product_group = null;
            $product_group_name = '';
        }
        if(empty($omc)){
            $omc = 1;
        }
        $g_data = $this->BdcDistribution->getUPPF($this->covertDate($start_dt,'mysql'),$this->covertDate($end_dt,'mysql'),$product_group, $omc);

        $t_head = $g_data['t_head'];
        $t_body_data = $g_data['t_body_data'];

        $table_title = $export_title = $omc_name.' UPPF Returns '.$product_group_name;

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

        $this->set(compact('controller','company_profile','print_title','table_title','t_head','t_body_data','graph_title','objPHPExcel', 'download', 'filename','media_type','grid_data','omc','omc_name'));
    }


    function bdc_stock_by_product(){
        $company_profile = $this->global_company;
        $start_dt = date('01-m-Y');
        $product = 1;
        $default_product = '1';
        $stock_type = 'Opening';//Closing
        if($this->request->is('post')){
            $start_dt = $this->covertDate($this->request->data['Query']['start_dt'],'mysql');
            $product = $this->request->data['Query']['product'];
            $stock_type = $this->request->data['Query']['stock_type'];
        }

        $g_data =  $this->get_bdc_stocks($this->covertDate($start_dt,'mysql'),$product,$stock_type);
        debug($g_data);
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


}