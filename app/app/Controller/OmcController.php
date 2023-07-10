<?php

/**
 * @name OmcController.php
 */
App::import('Controller', 'OmcApp');
class OmcController extends   OmcAppController
{
    # Controller name

    var $name = 'Omc';
    # set the model to use
    var $uses = array('BdcDistribution','OmcBdcDistribution', 'OmcCustomer','BdcUser','OmcUser', 'BdcOmc', 'User', 'Depot', 'District', 'ProductType', 'Region','Bdc','Order','FreightRate','DeliveryLocation','OmcCustomerOrder','Omc','Volume','OmcPriceChange');

    # Set the layout to use
    var $layout = 'omc_layout';

    public function beforeFilter($param_array = null)
    {
        parent::beforeFilter();
    }

    function index()
    {

    }

    function dashboard(){
        $authUser = $this->Auth->user();

        $company_profile = $this->global_company;
        $products_lists = $this->get_products();
        $bdc_depot_lists = $this->get_depot_list();
        $omc_customers_lists = $this->get_customer_list();
        $places_data = $this->get_region_district();
        $bdc_lists = $this->get_bdc_list();
        $glbl_region_district = $places_data['region_district'];
        $regions_lists = $places_data['region'];
        $district_lists = $places_data['district'];
        $delivery_locations = $this->get_delivery_locations();

        $data = $this->getTodayConsolidated($company_profile['id'], 'omc');
        $liters_per_products = $data['liters_per_products'];
        $grid_data = $data['grid_data'];
        $bar_graph_data = $this->getBarGraphData($company_profile['id'], 'omc');
        $pie_data = array();
        foreach ($bar_graph_data['data'] as $pie) {
            $pie_data[] = array($pie['name'], array_sum($pie['data']));
        }
        if ($pie_data) {
            $first_pie_data = $pie_data[0];
            $pie_data[0] = array(
                'name' => $first_pie_data[0],
                'y' => $first_pie_data[1],
                'sliced' => true,
                'selected' => true
            );
        }

        $group_depot = $this->User->getDepotGroup($authUser['id']);
        $loading_board = $this->get_loading_board($group_depot);
        $loaded_board = $this->get_loaded_board($group_depot);

        $this->set(compact('loading_board','loaded_board','company_profile','grid_data', 'liters_per_products', 'omc_customers_lists','bdc_depot_lists', 'bdc_lists','omclists', 'products_lists', 'regions_lists', 'district_lists', 'bar_graph_data', 'pie_data','glbl_region_district','delivery_locations','OmcPriceChange'));
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


                    $condition_array = array('OmcPriceChange.omc_id' => $company_profile['id'],'OmcPriceChange.deleted' => 'n');

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

                    $data_table = $this->OmcPriceChange->find('all', array('conditions' => $condition_array, 'contain'=>$contain,'order' => "OmcPriceChange.$sortname $sortorder", 'offset'=>$start,'limit' => $limit, 'recursive' => 1));
                    $data_table_count = $this->OmcPriceChange->find('count', array('conditions' => $condition_array, 'recursive' => -1));

                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {
                            $return_arr[] = array(
                                'id' => $obj['OmcPriceChange']['id'],
                                'cell' => array(
                                    $obj['OmcPriceChange']['id'],
                                    $obj['ProductType']['name'],
                                    $obj['OmcPriceChange']['description'],
                                    $obj['OmcPriceChange']['price'],
                                    $obj['OmcPriceChange']['unit']
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
                    $data = array('OmcPriceChange' => $_POST);
                    $data['OmcPriceChange']['modified_by'] = $this->Auth->user('id');
                    if ($_POST['id'] == 0) {
                        $data['OmcPriceChange']['created_by'] = $this->Auth->user('id');
                        $data['OmcPriceChange']['omc_id'] =  $company_profile['id'];
                    }


                    if ($this->OmcPriceChange->save($this->sanitize($data))) {
                        if($_POST['id'] > 0){
                            $new_user = $_POST['id'];
                            $log_description = $this->getLogMessage('ModifyOmcPriceChange')." (Price Change ID: ".$new_user.")";
                            $this->logActivity('Administration',$log_description);

                            return json_encode(array('code' => 0, 'msg' => 'Data Saved'));
                        }
                        else{ //If new pass back extra data if needed.
                            $new_user = $this->OmcPriceChange->id;
                            $log_description = $this->getLogMessage('CreateOmcPriceChange')." (Price Change ID: ".$new_user.")";
                            $this->logActivity('Administration',$log_description);

                            return json_encode(array('code' => 0, 'msg' => 'Data Saved.', 'id'=>$this->OmcPriceChange->id));
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
                    $modObj = ClassRegistry::init('OmcPriceChange');
                    $now = "'".date('Y-m-d H:i:s')."'";
                    $result = $modObj->updateAll(
                        $this->sanitize(array('OmcPriceChange.deleted' => "'y'",'OmcPriceChange.modified' => "$now",'OmcPriceChange.modified_by' => $this->Auth->user('id'))),
                        $this->sanitize(array('OmcPriceChange.id' => $ids))
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

        $export_data = $this->OmcPriceChange->find('all', array(
            //'fields'=>array('OmcCustomer.id','OmcCustomer.order_status','BdcDistribution.loading_date','BdcDistribution.waybill_date','BdcDistribution.collection_order_no','BdcDistribution.quantity','BdcDistribution.waybill_id','BdcDistribution.vehicle_no'),
            'conditions' => array('OmcPriceChange.omc_id' => $company_profile['id'],'OmcPriceChange.deleted' => 'n'),
            'contain'=>array(
                'ProductType'=>array('fields'=>array('ProductType.id','ProductType.name'))
            ),
            'order'=>array('OmcPriceChange.id'=>'desc'),
            'recursive' => 1
        ));

        //debug($export_data);
        if ($export_data) {
            $download = true;
            $list_data = array();
            foreach ($export_data as $obj) {
                $list_data[] = array(
                    $obj['ProductType']['name'],
                    $obj['OmcPriceChange']['price']
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

}