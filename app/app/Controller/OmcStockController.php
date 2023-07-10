<?php

/**
 * @name OmcController.php
 */
App::import('Controller', 'OmcApp');

class OmcStockController extends OmcAppController
{
    # Controller name

    var $name = 'OmcStock';
    # set the model to use
    var $uses = array('OmcCustomer','OmcCustomerTankMinstocklevel','OmcCustomerTank','OmcDsrpDataOption');

    # Set the layout to use
    var $layout = 'omc_layout';

    public function beforeFilter($param_array = null)
    {
        parent::beforeFilter(array('omc_user_types'=>array('Allow All')));
    }


    function index()
    {
        $this->redirect('daily_stock');
    }


    /*function daily_stock() {
        $today = date('Y-m-d');
        $g_data = $this->getDailyStockHistory($today,null);
        //debug($g_data);
        $table_title = 'Daily Stations Stock Report. - '.$this->covertDate($today,'ui');
        $controller = $this;
        $this->set(compact('controller','table_title','g_data','today'));
    }

    function print_export_daily_stock(){
        $download = false;
        $company_profile = $this->global_company;

        $media_type = $_POST['data_type'];
        $today = date('Y-m-d');
        $g_data = $this->getDailyStockHistory($today,null);
        $t_head = $g_data['t_head'];
        $t_body_data = $g_data['t_body_data'];

        $table_title = $export_title = 'Daily Stations Stock Report. - '.$this->covertDate($today,'ui');

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

        $this->set(compact('controller','company_profile','print_title','table_title','g_data','graph_title','objPHPExcel', 'download', 'filename','media_type','grid_data'));
    }*/


    function daily_stock_variance() {
        $today = date('Y-m-d');
        $indicator = null;

        if($this->request->is('post')){
            $indicator = $this->request->data['Query']['indicator'];
            if($indicator == 'all'){
                $indicator = null;
            }
        }
        $g_data = $this->getDailyStockVariance($today,null,$indicator);

        $table_title = $export_title = 'Last Updated Stations Stock Variance.';

        $controller = $this;

        $this->set(compact('controller','g_data','table_title','indicator'));
    }

    function print_export_daily_stock_variance(){
        $download = false;
        $company_profile = $this->global_company;
        $indicator = $_POST['data_indicator'];
        $media_type = $_POST['data_type'];
        if($indicator == 'all'){
            $indicator = null;
        }
        $today = date('Y-m-d');
        $g_data = $this->getDailyStockVariance($today,null,$indicator);

        $t_head = array();
       // $t_body_data = array();

        $table_title = $export_title = 'Daily Stations Stock Variance.';

        $list_data = $g_data;
        //debug($g_data);

        $this->autoLayout = false;

        if($media_type == 'export'){
            if(empty($list_data)){
                $download = false;
            }
            else{
                $download = true;
                $list_headers = $t_head;
                $filename = $export_title;
                //$res = $this->convertToExcel($list_headers,$list_data,$filename);
                //$objPHPExcel = $res['excel_obj'];
                //$filename = $res['filename'];
            }
        }
        elseif($media_type == 'print'){
            $this->autoLayout = true;
            $this->layout = 'print_layout';
        }
        $print_title = $table_title;
        $controller = $this;

        $this->set(compact('controller','company_profile','print_title','table_title','g_data','graph_title','objPHPExcel', 'download', 'filename','media_type'));
    }


   /* function stock_histories(){
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
    }*/


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


    function stock_administration($type = 'get')
    {
        $authUser = $this->Auth->user();
        $company_profile = $this->global_company;

        if($this->request->is('ajax')){
            $this->autoRender = false;
            $this->autoLayout = false;
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

                    $condition_array = array('OmcCustomer.omc_id' => $company_profile['id'],'OmcCustomer.deleted' => 'n');
                    if (!empty($search_query)) {
                        if ($qtype == 'name') {
                            $condition_array = array(
                                "OmcCustomer.$qtype LIKE" => $search_query . '%',
                                'OmcCustomer.deleted' => 'n'
                            );
                        }

                    }
                    $contain = array(
                        'OmcCustomerTank'
                    );
                    $fields = array('OmcCustomer.id', 'OmcCustomer.name');
                    $data_table = $this->OmcCustomer->find('all', array('fields' => $fields,'conditions' => $condition_array, 'contain'=>$contain,'order' => "OmcCustomer.$sortname $sortorder", 'limit' => $start . ',' . $limit, 'recursive' => 1));
                    $data_table_count = $this->OmcCustomer->find('count', array('conditions' => $condition_array, 'recursive' => -1));

                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {
                            $type_minstocklevel_str = '';
                            /*$customer_tanks = array();
                            foreach( $obj['OmcCustomerTank'] as $dt){
                                $tnk_capacity = !empty($dt['capacity']) ? preg_replace('/,/','',$dt['capacity']) : 0;
                                $tnk_min_stock_level = !empty($dt['min_stock_level']) ? preg_replace('/,/','',$dt['min_stock_level']) : 0;

                                if(isset($customer_tanks[$dt['type']])){
                                    $temp_capacity = $customer_tanks[$dt['type']]['capacity'];
                                    $temp_min_stock_level = $customer_tanks[$dt['type']]['min_stock_level'];
                                    $customer_tanks[$dt['type']]['capacity'] = $temp_capacity + $tnk_capacity;
                                    $customer_tanks[$dt['type']]['min_stock_level'] = $temp_min_stock_level + $tnk_min_stock_level;
                                    $customer_tanks[$dt['type']][] = array(
                                        'type' => $dt['type'],
                                        'name' => $dt['name'],
                                        'short_name' =>$dt['short_name'],
                                        'capacity' =>  $tnk_capacity,
                                        'min_stock_level' => $tnk_min_stock_level
                                    );
                                }
                                else{
                                    $customer_tanks[$dt['type']]['capacity'] = $tnk_capacity;
                                    $customer_tanks[$dt['type']]['min_stock_level'] = $tnk_min_stock_level;
                                    $customer_tanks[$dt['type']][] = array(
                                        'type' => $dt['type'],
                                        'name' => $dt['name'],
                                        'short_name' =>$dt['short_name'],
                                        'capacity' =>  $tnk_capacity,
                                        'min_stock_level' => $tnk_min_stock_level
                                    );
                                }


                                //$type_minstocklevel_str .= $dt['type'].'=>'.$this->formatNumber(preg_replace('/,/','',$dt['min_stock_level']),'money',0).' | ';
                            }*/

                            $return_arr[] = array(
                                'id' => $obj['OmcCustomer']['id'],
                                'cell' => array(
                                    $obj['OmcCustomer']['id'],
                                    $obj['OmcCustomer']['name']
                                    //$type_minstocklevel_str
                                )
                            );
                        }
                        return json_encode(array('success' => true, 'total' => $total_records, 'page' => $page, 'rows' => $return_arr));
                    }
                    else {
                        return json_encode(array('success' => false, 'total' => $total_records, 'page' => $page, 'rows' => array()));
                    }

                    break;

                case 'sub_save' :
                    $data = array('OmcCustomerTank' => $_POST);
                    $data['OmcCustomerTank']['omc_id'] = $company_profile['id'];
                    $data['OmcCustomerTank']['omc_customer_id'] = $_POST['parent_id'];
                    if($_POST['id'] == 0){
                        $data['OmcCustomerTank']['created_by'] = $authUser['id'];
                        /*$test = $this->OmcCustomerTank->find('first',array(
                            'fields' => array('id'),
                            'conditions'=>array('omc_customer_id'=>$_POST['parent_id'],'omc_id'=>$company_profile['id'],'type'=>$_POST['type']),
                            'recursive'=>-1
                        ));
                        if($test){
                            return json_encode(array('code' => 1, 'msg' => 'Record Already Exist.'));
                        }*/
                    }
                    else{
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
                        if($_POST['id'] > 0){
                            return json_encode(array('code' => 0, 'msg' => 'Data Updated!'));
                        }
                        else{
                            return json_encode(array('code' => 0, 'msg' => 'Data Saved!', 'id'=>$this->OmcCustomerTank->id));
                        }
                        //echo json_encode(array('success' => 0, 'msg' => 'Data Saved'));
                    } else {
                        return json_encode(array('code' => 1, 'msg' => 'Some errors occurred.'));
                    }
                    break;

                case 'load_details':
                    $gdata = $this->OmcCustomerTank->find('all',array(
                        'fields' => array('id','name','type','capacity','min_stock_level','status','row_bg_color'),
                        'conditions'=>array('omc_customer_id'=>$_POST['id']),
                        'recursive'=>-1
                    ));

                    if($gdata){
                        foreach ($gdata as $obj) {
                            $return_arr[] = array(
                                'id' => $obj['OmcCustomerTank']['id'],
                                'cell' => array(
                                    $obj['OmcCustomerTank']['name'],
                                    $obj['OmcCustomerTank']['type'],
                                    $obj['OmcCustomerTank']['capacity'],
                                    $obj['OmcCustomerTank']['min_stock_level'],
                                    $obj['OmcCustomerTank']['status']
                                ),
                                'property'=>array(
                                    'bg_color'=>$obj['OmcCustomerTank']['row_bg_color']
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

        //$customer_tanks = $this->getCustomersTankTypes();

        $tank_status = array();
        foreach($this->omc_customer_tank_status as $key => $key){
            $tank_status[] =array(
                'id'=>$key,
                'name'=>$key
            );
        }

        $tanks_types_pro = $this->getTanksProductTypes();
        $tanks_types_opt = array();
        foreach($tanks_types_pro as $key => $value){
            $tanks_types_opt[] =array(
                'id'=>$key,
                'name'=>$value
            );
        }

        $tank_names = array();
        $data = $this->OmcDsrpDataOption->find('first',array(
            'conditions'=>array('omc_id'=>$company_profile['id']),
            'recursive'=>-1
        ));
        if($data && isset($data['OmcDsrpDataOption']) && !empty($data['OmcDsrpDataOption']['bulk_stock_position_products'])){
            $arr = unserialize($data['OmcDsrpDataOption']['bulk_stock_position_products']);
            foreach($arr as $arr_value){
                $tank_names[] =array(
                    'id'=>$arr_value['value'],
                    'name'=>$arr_value['value']
                );
            }
        }

        $this->set(compact('tanks_types_opt','customer_tanks','tank_status','tank_names'));
    }

}