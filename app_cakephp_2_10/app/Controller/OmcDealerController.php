<?php

/**
 * @name OmcController.php
 */
App::import('Controller', 'OmcCustomerApp');

class OmcDealerController extends OmcCustomerAppController
{
    # Controller name

    var $name = 'OmcDealer';
    # set the model to use
    var $uses = array('OmcBdcDistribution', 'OmcCustomerDistribution','OmcCustomer', 'User', 'District', 'ProductType', 'Region');

    # Set the layout to use
    var $layout = 'omc_customer_layout';

    # Bdc ids this user will work with only
    var $user_bdc_ids = array();

    public function beforeFilter($param_array = null)
    {
        parent::beforeFilter(array('omc_customer_user_types'=>array('Allow All')));
        //Cache the users bdcs to work with
        $company_profile = $this->global_company;
         /*$bdcs_data = $this->OmcUser->find('first', array(
             'conditions' => array('OmcUser.user_id' => $this->Auth->user('id'),'OmcUser.omc_id' => $company_profile['id']),
             'contain'=>array('OmcUserBdc'),
             'recursive' => 1
         ));
        foreach($bdcs_data['OmcUserBdc'] as $o){
            $this->user_bdc_ids[] = $o['bdc_id'];
        }*/
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
                    $data_table = $this->OmcBdcDistribution->find('all', array('conditions' => $condition_array, 'contain'=>$contain,'order' => "OmcBdcDistribution.$sortname $sortorder", 'limit' => $start . ',' . $limit, 'recursive' => 2));
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

}