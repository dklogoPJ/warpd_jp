<?php

/**
 * @name OmcController.php
 */
App::import('Controller', 'OmcApp');

class OmcPerformanceController extends OmcAppController
{
    # Controller name

    var $name = 'OmcPerformance';
    # set the model to use
    var $uses = array('OmcCustomer','OmcCustomerTankMinstocklevel','OmcCustomerTank','OmcDsrpDataOption','OmcTank','OmcTankStatus','OmcTankType','AdditiveSetup','AdditiveStock','Omc','AdditiveDopingRatio','AdditiveCostGeneration','ProductType','Truck','Depot','AdditiveAverageCost','PerformanceSetting');

    # Set the layout to use
    var $layout = 'omc_layout';

    public function beforeFilter($param_array = null)
    {
        parent::beforeFilter(array('omc_user_types'=>array('Allow All')));
    }


    function index()
    {
        //$this->redirect('daily_stock');
    }


    public function perf_monitoring_setting($type = 'get') {

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
                    $sortorder = isset($_POST['sortorder']) ? $_POST['sortorder'] : 'asc';
                    /** Sort order */
                    $qtype = isset($_POST['qtype']) ? $_POST['qtype'] : '';
                    /** Search column */
                    $search_query = isset($_POST['query']) ? $_POST['query'] : '';

                    $filter_customer =   isset($_POST['filter_customer']) ? $_POST['filter_customer'] : 0 ;

                    $filter_product =   isset($_POST['filter_product']) ? $_POST['filter_product'] : 0 ;
                    /** Search string */
                    $rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
                    $limit = $rp;
                    $start = ($page - 1) * $rp;

                    $condition_array = array(
                        'PerformanceSetting.deleted' => 'n'
                    );

                    if($filter_customer != 0){
                        $condition_array['PerformanceSetting.omc_customer_id'] = $filter_customer;
                    }

                    if($filter_product != 0){
                        $condition_array['PerformanceSetting.product_type_id'] = $filter_product;
                    }
                   
                    $contain = array(
                        'OmcCustomer'=>array('fields' => array('OmcCustomer.id', 'OmcCustomer.name')),
                        'ProductType'=>array('fields' => array('ProductType.id', 'ProductType.name'))
                    );

                    $data_table = $this->PerformanceSetting->find('all', array('conditions' => $condition_array, 'contain'=>$contain,'order' => "PerformanceSetting.$sortname $sortorder", 'page' => $page  , 'limit'=> $limit, 'recursive' => 1));
                    $data_table_count = $this->PerformanceSetting->find('count', array('recursive' => -1));

                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {
                            $return_arr[] = array(
                                'id' => $obj['PerformanceSetting']['id'],
                                'cell' => array(
                                    $obj['PerformanceSetting']['id'],
                                    $obj['OmcCustomer']['name'],
                                    $obj['ProductType']['name'],
                                    $obj['PerformanceSetting']['manager_name'],
                                    $obj['PerformanceSetting']['daily_target'],
                                    $obj['PerformanceSetting']['teritory']
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
                    if ($_POST['id'] == 0) {
                        if(!in_array('A',$permissions)){
                            return json_encode(array('code' => 1, 'msg' => 'Access Denied.'));
                        }
                    }
                    else{
                        if(!in_array('E',$permissions)){
                            return json_encode(array('code' => 1, 'msg' => 'Access Denied.'));
                        }
                    }

                    $data = array('PerformanceSetting' => $_POST);

                    if($_POST['id'] == 0){
                        $data['PerformanceSetting']['created_by'] = $authUser['id'];
                    }
                    else{
                        $data['PerformanceSetting']['modified_by'] = $authUser['id'];
                    }

                    $data['PerformanceSetting']['omc_id'] = $company_profile['id'];

                    if ($this->PerformanceSetting->save($this->sanitize($data))) {
                        if($_POST['id'] > 0){
                            return json_encode(array('code' => 0, 'msg' => 'Data Updated!'));
                        }
                        else{
                            return json_encode(array('code' => 0, 'msg' => 'Data Saved!', 'id'=>$this->PerformanceSetting->id));
                        }
                    } else {
                        return json_encode(array('code' => 1, 'msg' => 'Some errors occurred.'));
                    }
                    break;

                case 'delete':
                    $ids = $_POST['ids'];
                    $modObj = ClassRegistry::init('PerformanceSetting');
                    $result = $modObj->updateAll(
                        array('PerformanceSetting.deleted' => "'y'"),
                        array('PerformanceSetting.id' => $ids)
                    );
                    if ($result) {
                        $modObj = ClassRegistry::init('PerformanceSetting');
                        $modObj->updateAll(
                            array('PerformanceSetting.deleted' => "'y'"),
                            array('PerformanceSetting.id' => $ids)
                        );

                     echo json_encode(array('code' => 0, 'msg' => 'Data Deleted!'));
                    } else {
                        echo json_encode(array('code' => 1, 'msg' => 'Data cannot be deleted'));
                    }
                    break;
            }
        }
        $teritory = array('0'=>array('id'=>'Southern','name'=>'Southern'),'1'=>array('id'=>'Northern','name'=>'Northern'),'2'=>array('id'=>'Western','name'=>'Western'),'3'=>array('id'=>'Eastern','name'=>'Eastern'));
        $additives_lists = $this->get_additives();
        $products_data = $this->get_products();
        $products = $this->get_products();
        $products_lists =array(array('name'=>'All','value'=>0));
        foreach($products_data as $arr){
            $products_lists[] = array('name'=>$arr['name'],'value'=>$arr['id']);
        }

        $additives_lists = $this->get_additives();
        $numbers = $this->Truck->getTruckNo();
        $depot_lists = $this->get_depot_list();

        $omc_customers_data = $this->get_customer_list();
        $omc_customers_lists =array(array('name'=>'All','value'=>0));
        foreach($omc_customers_data as $arr){
            $omc_customers_lists[] = array('name'=>$arr['name'],'value'=>$arr['id']);
        }
        $omc_customers = $this->get_customer_list();
        
        $this->set(compact('additives_lists','products_lists','numbers','depot_lists','omc_customers_lists','teritory','omc_customers','products'));
	}


    public function perf_monitoring_analytics($type = 'get') {

        $permissions = $this->action_permission;
        $authUser = $this->Auth->user();
        $company_profile = $this->global_company;

        $today = date('Y-m-d');
        $indicator = null;

        if($this->request->is('post')){
            $indicator = $this->request->data['Query']['indicator'];
            if($indicator == 'all'){
                $indicator = null;
            }
        }
        $g_data = $this->getDailyStockVariance($today,null,$indicator);

        $table_title = $export_title = 'RM Performance Monitoring Analytics Table';

        $controller = $this;

        $this->set(compact('controller','g_data','table_title','indicator'));
        
	}



    public function montly_perf_monitoring_analytics($type = 'get') {
        $permissions = $this->action_permission;
        $authUser = $this->Auth->user();
        $company_profile = $this->global_company;

            switch ($type) {
                case 'get' :
                    /**  Get posted data */
                    $page = isset($_POST['page']) ? $_POST['page'] : 1;
                    /** The current page */
                    $sortname = isset($_POST['sortname']) ? $_POST['sortname'] : 'id';
                    /** Sort column */
                    $sortorder = isset($_POST['sortorder']) ? $_POST['sortorder'] : 'asc';
                    /** Sort order */
                    $qtype = isset($_POST['qtype']) ? $_POST['qtype'] : '';
                    /** Search column */
                    $search_query = isset($_POST['query']) ? $_POST['query'] : '';
                    /** Search string */
                    $rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
                    $limit = $rp;
                    $start = ($page - 1) * $rp;

                    $condition_array = array(
                        'AdditiveAverageCost.omc_id' => $company_profile['id'],
                        'AdditiveAverageCost.deleted' => 'n'
                    );

                    $contain = array(
                        'AdditiveSetup'=>array('fields' => array('AdditiveSetup.id', 'AdditiveSetup.name'))
                    );
                    
                    $data_table = $this->AdditiveAverageCost->find('all', array('conditions' => $condition_array, 'contain'=>$contain,'order' => "AdditiveAverageCost.$sortname $sortorder", 'limit' => $start . ',' . $limit, 'recursive' => 1));
                    $data_table_count = $this->AdditiveAverageCost->find('count', array('recursive' => -1));

                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {
                            $return_arr[] = array(
                                'id' => $obj['AdditiveAverageCost']['id'],
                                'cell' => array(
                                    $obj['AdditiveAverageCost']['id'],
                                    $obj['AdditiveSetup']['name'],
                                    $obj['AdditiveAverageCost']['cost_per_ltr'],
                                    $obj['AdditiveAverageCost']['total_no_dum'],
                                    $obj['AdditiveAverageCost']['total_no_ltr'],
                                    $obj['AdditiveAverageCost']['total_stock_cost']
                                   
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
                   
                    
                    $data = array('AdditiveAverageCost' => $_POST);
        
                    if($_POST['id'] == 0){
                        $data['AdditiveAverageCost']['created_by'] = $authUser['id'];
                    }
                    else{
                        $data['AdditiveAverageCost']['modified_by'] = $authUser['id'];
                    }

                    $data['AdditiveAverageCost']['omc_id'] = $company_profile['id'];
                   
                    if ($this->AdditiveAverageCost->save($this->sanitize($data))) {
                        if($_POST['id'] > 0){
                            return json_encode(array('code' => 0, 'msg' => 'Data Updated!'));
                        }
                        else{
                            return json_encode(array('code' => 0, 'msg' => 'Data Saved!', 'id'=>$this->AdditiveAverageCost->id));
                        }
                    } else {
                        return json_encode(array('code' => 1, 'msg' => 'Some errors occurred.'));
                    }
                    break;

                case 'delete':
                    $ids = $_POST['ids'];
                    $modObj = ClassRegistry::init('AdditiveAverageCost');
                    $result = $modObj->updateAll(
                        array('AdditiveAverageCost.deleted' => "'y'"),
                        array('AdditiveAverageCost.id' => $ids)
                    );
                    if ($result) {
                        $modObj = ClassRegistry::init('AdditiveAverageCost');
                        $modObj->updateAll(
                            array('AdditiveAverageCost.deleted' => "'y'"),
                            array('AdditiveAverageCost.id' => $ids)
                        );

                     echo json_encode(array('code' => 0, 'msg' => 'Data Deleted!'));
                    } else {
                        echo json_encode(array('code' => 1, 'msg' => 'Data cannot be deleted'));
                    }
                    break;
            }
        
        $additives_lists = $this->get_additives();
        
        $this->set(compact('additives_lists'));
	}

}


?>
