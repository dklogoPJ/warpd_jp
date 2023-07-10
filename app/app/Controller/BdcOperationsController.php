<?php
/**
 * @name BdcController.php
 */
App::import('Controller', 'BdcApp');

class BdcOperationsController extends BdcAppController
{
    # Controller name

    var $name = 'BdcOperations';
    # set the model to use
    var $uses = array('Bdc','BdcDistribution', 'BdcUser','OmcUser', 'BdcOmc', 'User', 'Depot', 'OmcBdcDistribution', 'ProductType', 'Order', 'Omc','StockTrading');

    # Set the layout to use
    var $layout = 'bdc_layout';

    public function beforeFilter($param_array = null)
    {
        parent::beforeFilter(array('bdc_user_types'=>array('Operations')));
    }


    function index()
    {
        $company_profile = $this->global_company;
        $data = $this->getTodayConsolidated($company_profile['id'], 'bdc');
        $liters_per_products = $data['liters_per_products'];
        $grid_data = $data['grid_data'];
        $bar_graph_data = $this->getBarGraphData($company_profile['id'], 'bdc');
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
        $controller = $this;
        $this->set(compact('controller','grid_data', 'liters_per_products', 'bar_graph_data', 'pie_data'));
    }


    function export_loading_data(){
        $download = false;
        $company_profile = $this->global_company;
        if($this->request->is('post')){
            if($this->request->data['Export']['action'] == 'export_me'){
                $start_dt = $this->covertDate($this->request->data['Export']['export_startdt'],'mysql').' 00:00:00';
                $end_dt = $this->covertDate($this->request->data['Export']['export_enddt'],'mysql').' 23:59:59';
                $type = $this->request->data['Export']['export_type'];

                $export_data = $this->BdcDistribution->find('all', array(
                    'fields'=>array('BdcDistribution.id','BdcDistribution.order_status','BdcDistribution.loading_date','BdcDistribution.waybill_date','BdcDistribution.collection_order_no','BdcDistribution.quantity','BdcDistribution.waybill_id','BdcDistribution.vehicle_no'),
                    'conditions' => array('BdcDistribution.bdc_id' => $company_profile['id'], 'BdcDistribution.record_type' => 'bdc','BdcDistribution.deleted' => 'n','BdcDistribution.loading_date >=' => $start_dt, 'BdcDistribution.loading_date <=' => $end_dt),
                    'contain'=>array(
                        'Omc'=>array('fields'=>array('Omc.id','Omc.name')),
                        'Depot'=>array('fields'=>array('Depot.id','Depot.name')),
                        'ProductType'=>array('fields'=>array('ProductType.id','ProductType.name')),
                        'Region'=>array('fields'=>array('Region.id','Region.name')),
                        'District'=>array('fields'=>array('District.id','District.name'))
                    ),
                    'order' => array("BdcDistribution.id"=>'desc'),
                    'recursive' => 1
                ));

                if ($export_data) {
                    $download = true;
                    $list_data = array();
                    foreach ($export_data as $value) {
                        $list_data[] = array(
                            $this->covertDate($value['BdcDistribution']['loading_date'],'mysql_flip'),
                            $this->covertDate($value['BdcDistribution']['waybill_date'],'mysql_flip'),
                            $value['BdcDistribution']['waybill_id'],
                            $value['BdcDistribution']['collection_order_no'],
                            $value['Omc']['name'],
                            $value['Depot']['name'],
                            $value['ProductType']['name'],
                            preg_replace('/,/','',$value['BdcDistribution']['quantity']),
                            /*$value['Region']['name'],
                            $value['District']['name'],*/
                            $value['BdcDistribution']['vehicle_no'],
                            $value['BdcDistribution']['order_status']
                        );
                    }
                    $list_headers = array('Date','Waybill Date','Waybill No.','Collection Order No.','OMC','Depot','Product Type','Quantity','Truck No.','Status');
                    //$list_headers = array('Date','Waybill No.','OMC','Loading Depot','Product Type','Quantity','Truck No.');
                    $filename = $company_profile['name']." Daily Upload ".date('Ymdhis');
                    $res = $this->convertToExcel($list_headers,$list_data,$filename);
                    $objPHPExcel = $res['excel_obj'];
                    $filename = $res['filename'];
                }
            }
        }

        $this->autoLayout = false;

        $this->set(compact('objPHPExcel', 'download', 'filename'));
    }


   /* function enter_loading_data(){
        $company_profile = $this->global_company;

        $omclists = $this->get_omc_list();
        $bdc_depot_lists = $this->get_depot_list();
        $products_lists = $this->get_products();
        $depots_to_products = $this->Bdc->getDepotToProduct($company_profile['id']);

        $this->set(compact('bdc_depot_lists', 'omclists', 'products_lists', 'regions_lists', 'district_lists','depots_to_products'));
    }*/

    function enter_loading_data($type = 'get')
    {
        $authUser = $this->Auth->user();
        $company_profile = $this->global_company;
        if ($this->request->is('ajax')) {
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

                    $condition_array = array('BdcDistribution.bdc_id' => $company_profile['id'], 'BdcDistribution.record_type' => 'bdc','BdcDistribution.deleted' => 'n');
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
                        'Omc'=>array('fields' => array('Omc.id', 'Omc.name')),
                        'Depot'=>array('fields' => array('Depot.id', 'Depot.name')),
                        'ProductType'=>array('fields' => array('ProductType.id', 'ProductType.name')),
                        'District'=>array('fields' => array('District.id', 'District.name')),
                        'Region'=>array('fields' => array('Region.id', 'Region.name')),
                    );
                    // $fields = array('User.id', 'User.username', 'User.first_name', 'User.last_name', 'User.group_id', 'User.active');
                    $data_table = $this->BdcDistribution->find('all', array('conditions' => $condition_array, 'contain'=>$contain,'order' => "BdcDistribution.$sortname $sortorder", 'limit' => $start . ',' . $limit, 'recursive' => 1));
                    $data_table_count = $this->BdcDistribution->find('count', array('conditions' => $condition_array, 'recursive' => -1));
                    //debug($data_table);
                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {
                            $waybill_dt = $obj['BdcDistribution']['waybill_date'];
                            if($obj['BdcDistribution']['waybill_date']){
                                $waybill_dt = $this->covertDate($obj['BdcDistribution']['waybill_date'],'mysql_flip');
                            }
                            $return_arr[] = array(
                                'id' => $obj['BdcDistribution']['id'],
                                'cell' => array(
                                    $obj['BdcDistribution']['id'],
                                    $this->covertDate($obj['BdcDistribution']['loading_date'],'mysql_flip'),
                                    $waybill_dt,
                                    $obj['BdcDistribution']['waybill_id'],
                                    $obj['BdcDistribution']['collection_order_no'],
                                    $obj['Omc']['name'],
                                    $obj['Depot']['name'],
                                    $obj['ProductType']['name'],
                                    $obj['BdcDistribution']['approved_quantity'],
                                    $obj['BdcDistribution']['quantity'],
                                    /* $obj['Region']['name'],
                                     $obj['District']['name'],*/
                                    $obj['BdcDistribution']['vehicle_no']
                                ),
                                'extra_data' => array(//Sometime u need certain data to be stored on the main tr at the client side like the referencing table id for editing
                                    'omc_name'=>$obj['Omc']['name'],
                                    'record_origin'=>$obj['BdcDistribution']['record_origin'],
                                    'order_status'=>$obj['BdcDistribution']['order_status'],
                                    'order_id'=>$obj['BdcDistribution']['order_id'],
                                    'product_type_id'=>$obj['ProductType']['id'],
                                    'product_type_name'=>$obj['ProductType']['name']
                                ),
                                'property'=>array(
                                    'bg_color'=>$obj['BdcDistribution']['row_bg_color'],
                                    'edit_row'=> $obj['BdcDistribution']['edit_row']
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
                    $data = array('BdcDistribution' => $_POST);
                    $data['BdcDistribution']['order_status'] = 'Complete'; //whether a new manual record or editing from crm, order is always complete once data is posted
                    $data['BdcDistribution']['row_bg_color'] = ''; //So is row bg_color, what ever it was remove it and make it empty
                    $time_in = date('H:i:s');
                    $data['BdcDistribution']['loading_date'] = $this->covertDate($_POST['loading_date'],'mysql').' '.$time_in;
                    $data['BdcDistribution']['waybill_date'] = $this->covertDate($_POST['waybill_date'],'mysql').' '.$time_in;

                    $record_origin = isset($_POST['extra']['record_origin'])? $_POST['extra']['record_origin'] : 'manual';
                    $order_status = isset($_POST['extra']['order_status'])? $_POST['extra']['order_status'] : 'Pending';
                    $order_id = isset($_POST['extra']['order_id'])? $_POST['extra']['order_id'] : 0;
                    $omc_name = isset($_POST['extra']['omc_name'])? $_POST['extra']['omc_name'] : '';

                    if($_POST['id']== 0){//New Manual Entry
                        $data['BdcDistribution']['record_origin'] = 'manual';
                        $data['BdcDistribution']['bdc_id'] = $company_profile['id'];
                        $data['BdcDistribution']['created_by'] = $authUser['id'];
                        $data['BdcDistribution']['record_type'] = 'bdc';

                        $omc_details = $this->Omc->find('first', array(
                            'fields' => array('Omc.id', 'Omc.name'),
                            'conditions' => array('Omc.id' => $_POST['omc_id']),
                            'recursive' => -1
                        ));
                        $omc_name = $omc_details['Omc']['name'];
                    }
                    else{// Might be correcting error or a record from CRM.
                        $data['BdcDistribution']['modified_by'] = $authUser['id'];
                    }

                    //$data['BdcDistribution']['edit_row'] = 'no'; //If we want to lock the record after truck load is complete, then enable this part

                    if ($this->BdcDistribution->save($this->sanitize($data))) {
                        $send_notification = false;
                        if($_POST['id']== 0){//if it is a new manual record, then send notification
                            $send_notification = true;
                        }
                        else{
                            if($record_origin == 'crm'){
                                if($order_status == 'Pending'){//It means after saving the record the order status has now changed to Complete, so send the message
                                    $send_notification = true;
                                }
                            }
                        }

                        //This is where we update the Order record
                        if($record_origin == 'crm'){
                            $order_save = array(
                                'Order'=>array(
                                    'id'=>$order_id,
                                    'row_bg_color'=>'',
                                    'bdc_modified'=>date('Y-m-d H:i:s'),
                                    'order_status'=>'Complete'
                                )
                            );
                            $this->Order->save($this->sanitize($order_save));
                            //Flow to omc distribution
                            $omc_distribution = $this->OmcBdcDistribution->find('first', array(
                                'fields' => array('OmcBdcDistribution.id'),
                                'conditions' => array('OmcBdcDistribution.bdc_distribution_id' => $_POST['id']),
                                'recursive' => -1
                            ));

                            if(!$omc_distribution){
                                $bdc_distribution = $this->BdcDistribution->find('first', array(
                                    'fields' => array('BdcDistribution.id','BdcDistribution.quantity'),
                                    'conditions' => array('BdcDistribution.id' => $_POST['id']),
                                    'contain'=>array(
                                        'Order'=>array('fields' => array('Order.id','Order.omc_customer_id'))
                                    ),
                                    'recursive' => 1
                                ));

                                $omc_save = array('OmcBdcDistribution'=>array(
                                    'bdc_distribution_id'=>$_POST['id'],
                                    'omc_customer_id'=>$bdc_distribution['Order']['omc_customer_id'],
                                    'quantity'=>$bdc_distribution['BdcDistribution']['quantity'],
                                    'created_by' => $authUser['id']
                                ));

                                $this->OmcBdcDistribution->save($this->sanitize($omc_save));
                            }
                        }

                        //THIS will be where we create uppf records

                        if($send_notification){
                            /** Send Message to all parties both Omc and Bdc Users **/
                            $send_params = array(
                                'title'=>$omc_name.', Truck Loaded',
                                'content'=>'Your truck '.$_POST['vehicle_no'].' has been loaded by '.$company_profile['name'],
                                'sender'=>$authUser['id'],
                                'excluded_users'=>array($authUser['id']),
                                'msg_type'=>'system',
                                'omc'=>$_POST['omc_id'],
                                'omc_user_types' => array('Operations'),
                                'bdc'=>$company_profile['id'],
                                'bdc_user_types' => array('Operations'),
                                'message_origin'=>'bdc'
                            );
                            $this->sendMessage($send_params);
                            /*** End of Send message **/
                        }

                        if($_POST['id'] > 0){
                            return json_encode(array('code' => 0, 'msg' => 'Data Updated!'));
                        }
                        else{
                            return json_encode(array('code' => 0, 'msg' => 'Data Saved', 'id'=>$this->BdcDistribution->id));
                        }
                    } else {
                        echo json_encode(array('code' => 1, 'msg' => 'Some errors occured.'));
                    }
                    //echo debug($data);

                    break;

                case 'load':

                    break;

                case 'delete':

                    break;
            }
        }

        $omclists = $this->get_omc_list();
        $bdc_depot_lists = $this->get_depot_list();
        $products_lists = $this->get_products();
        $depots_to_products = $this->Bdc->getDepotToProduct($company_profile['id']);

        $this->set(compact('bdc_depot_lists', 'omclists', 'products_lists', 'regions_lists', 'district_lists','depots_to_products'));
    }

}