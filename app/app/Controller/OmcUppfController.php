<?php

/**
 * @name OmcController.php
 */
App::import('Controller', 'OmcApp');

class OmcUppfController extends OmcAppController
{
    # Controller name

    var $name = 'OmcUppf';
    # set the model to use
    var $uses = array('BdcDistribution','OmcBdcDistribution', 'OmcCustomer','BdcUser','OmcUser', 'BdcOmc', 'User', 'Depot', 'District', 'ProductType', 'Region','Bdc','Order','FreightRate','DeliveryLocation');

    # Set the layout to use
    var $layout = 'omc_layout';

    public function beforeFilter($param_array = null)
    {
        parent::beforeFilter(array('omc_user_types'=>array('Allow All')));
    }


    function index()
    {
        $this->redirect('uppf');
    }


    function uppf() {
        $company_profile = $this->global_company;
        $start_dt = date('01-m-Y');
        $end_dt = date('t-m-Y');
        $product_group = 'Premix';
        $default_product_group = 'Premix';
        $product_group_name = 'On Premix';

        if($this->request->is('post')){
            //$start_dt = $this->covertDate($this->request->data['Query']['start_dt'],'mysql');
            $start_dt = $this->request->data['Query']['start_dt'];
            //$end_dt = $this->covertDate($this->request->data['Query']['end_dt'],'mysql');
            $end_dt = $this->request->data['Query']['end_dt'];
            if($this->request->data['Query']['product_group'] != 'all'){
                $product_group = $this->request->data['Query']['product_group'];
                $default_product_group = $this->request->data['Query']['product_group'];
                $product_group_name = 'On '.$this->request->data['Query']['product_group_name'];
            }
        }

        $g_data = $this->getUppf($this->covertDate($start_dt,'mysql'),$this->covertDate($end_dt,'mysql'),$product_group);
        $t_head = $g_data['t_head'];
        $t_body_data = $g_data['t_body_data'];

        $table_title = 'UPPF Returns '.$product_group_name;

        //Get all product group
        $product_group_data = $this->get_product_group();
        //$product_group_list = array('all'=>'All Product Type');
        $product_group_list = array();
        foreach($product_group_data as $arr){
            $product_group_list[$arr] = $arr;
        }

        $controller = $this;
        $this->set(compact('controller','table_title','t_head','t_body_data','product_group_list','end_dt','start_dt','default_product_group'));
    }


    function print_export_uppf(){
        $download = false;
        $company_profile = $this->global_company;

        $media_type = $_POST['data_type'];
        $start_dt = $this->covertDate($_POST['data_start_dt'],'mysql');
        $end_dt = $this->covertDate($_POST['data_end_dt'],'mysql');
        $product_group = $_POST['data_product_group'];
        $product_group_name = 'On '.$_POST['data_product_group_name'];
        if($product_group == 'all'){
            $product_group = null;
            $product_group_name = '';
        }
        $g_data = $this->getUppf($start_dt,$end_dt,$product_group);
        $t_head = $g_data['t_head'];
        $t_body_data = $g_data['t_body_data'];

        $table_title = $export_title = 'UPPF Returns '.$product_group_name;

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

        $this->set(compact('controller','company_profile','print_title','table_title','t_head','t_body_data','graph_title','objPHPExcel', 'download', 'filename','media_type','grid_data'));
    }

}