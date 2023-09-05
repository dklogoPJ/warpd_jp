<?php

/**
 * @name OmcController.php
 */
App::import('Controller', 'OmcApp');

class OmcAdditiveController extends OmcAppController
{
    # Controller name

    var $name = 'OmcAdditive';
    # set the model to use
    var $uses = array('OmcCustomer','OmcCustomerTankMinstocklevel','OmcCustomerTank','OmcDsrpDataOption','OmcTank','OmcTankStatus','OmcTankType','AdditiveSetup','AdditiveStock','Omc','AdditiveDopingRatio','AdditiveCostGeneration','ProductType','Truck','Depot');

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



    function additive_stock_wac($type = 'get') {
        
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
                    /** Search string */
                    $rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
                    $limit = $rp;
                    $start = ($page - 1) * $rp;

                    $condition_array = array(
                        'AdditiveStock.omc_id' => $company_profile['id'],
                        'AdditiveStock.deleted' => 'n'
                    );

                    $contain = array(
                        'AdditiveSetup'=>array('fields' => array('AdditiveSetup.id', 'AdditiveSetup.name'))
                    );
                    
                    $data_table = $this->AdditiveStock->find('all', array('conditions' => $condition_array, 'contain'=>$contain,'order' => "AdditiveStock.$sortname $sortorder", 'limit' => $start . ',' . $limit, 'recursive' => 1));
                    $data_table_count = $this->AdditiveStock->find('count', array('recursive' => -1));

                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {
                            $return_arr[] = array(
                                'id' => $obj['AdditiveStock']['id'],
                                'cell' => array(
                                    $obj['AdditiveStock']['id'],
                                    $obj['AdditiveSetup']['name'],
                                    $obj['AdditiveStock']['drum_size'],
                                    $obj['AdditiveStock']['drum_cost'],
                                    $obj['AdditiveStock']['cost_per_ltr'],
                                    $obj['AdditiveStock']['total_no_dum'],
                                    $obj['AdditiveStock']['total_no_ltr'],
                                    $obj['AdditiveStock']['total_stock_cost']
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
                    
                    $data = array('AdditiveStock' => $_POST);
        
                    if($_POST['id'] == 0){
                        $data['AdditiveStock']['created_by'] = $authUser['id'];
                    }
                    else{
                        $data['AdditiveStock']['modified_by'] = $authUser['id'];
                    }

                    $data['AdditiveStock']['omc_id'] = $company_profile['id'];
                    if ($this->AdditiveStock->save($this->sanitize($data))) {
                        if($_POST['id'] > 0){
                            return json_encode(array('code' => 0, 'msg' => 'Data Updated!'));
                        }
                        else{
                            return json_encode(array('code' => 0, 'msg' => 'Data Saved!', 'id'=>$this->AdditiveStock->id));
                        }
                    } else {
                        return json_encode(array('code' => 1, 'msg' => 'Some errors occurred.'));
                    }
                    break;

                case 'delete':
                    $ids = $_POST['ids'];
                    $modObj = ClassRegistry::init('AdditiveStock');
                    $result = $modObj->updateAll(
                        array('AdditiveStock.deleted' => "'y'"),
                        array('AdditiveStock.id' => $ids)
                    );
                    if ($result) {
                        $modObj = ClassRegistry::init('AdditiveStock');
                        $modObj->updateAll(
                            array('AdditiveStock.deleted' => "'y'"),
                            array('AdditiveStock.id' => $ids)
                        );

                     echo json_encode(array('code' => 0, 'msg' => 'Data Deleted!'));
                    } else {
                        echo json_encode(array('code' => 1, 'msg' => 'Data cannot be deleted'));
                    }
                    break;
            }
        }

        $additives_lists = $this->get_additives();
        
        $this->set(compact('additives_lists'));
	}


    function additives_doping_ratios($type = 'get') {
        
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
                    /** Search string */
                    $rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
                    $limit = $rp;
                    $start = ($page - 1) * $rp;

                    $condition_array = array(
                        'AdditiveDopingRatio.omc_id' => $company_profile['id'],
                        'AdditiveDopingRatio.deleted' => 'n'
                    );

                    $contain = array(
                        'AdditiveSetup'=>array('fields' => array('AdditiveSetup.id', 'AdditiveSetup.name'))
                    );
                    
                    $data_table = $this->AdditiveDopingRatio->find('all', array('conditions' => $condition_array, 'contain'=>$contain,'order' => "AdditiveDopingRatio.$sortname $sortorder", 'limit' => $start . ',' . $limit, 'recursive' => 1));
                    $data_table_count = $this->AdditiveDopingRatio->find('count', array('recursive' => -1));

                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {
                            $return_arr[] = array(
                                'id' => $obj['AdditiveDopingRatio']['id'],
                                'cell' => array(
                                    $obj['AdditiveDopingRatio']['id'],
                                    $obj['AdditiveSetup']['name'],
                                    $obj['AdditiveDopingRatio']['drum_name'],
                                    $obj['AdditiveDopingRatio']['ltr'],
                                    $obj['AdditiveDopingRatio']['product_qty'],
                                    $obj['AdditiveDopingRatio']['doping_ratio']
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
                    
                    $data = array('AdditiveDopingRatio' => $_POST);
        
                    if($_POST['id'] == 0){
                        $data['AdditiveDopingRatio']['created_by'] = $authUser['id'];
                    }
                    else{
                        $data['AdditiveDopingRatio']['modified_by'] = $authUser['id'];
                    }

                    $data['AdditiveDopingRatio']['doping_ratio'] = round($_POST['doping_ratio'], 5);
                    $data['AdditiveDopingRatio']['omc_id'] = $company_profile['id'];
                    if ($this->AdditiveDopingRatio->save($this->sanitize($data))) {
                        if($_POST['id'] > 0){
                            return json_encode(array('code' => 0, 'msg' => 'Data Updated!'));
                        }
                        else{
                            return json_encode(array('code' => 0, 'msg' => 'Data Saved!', 'id'=>$this->AdditiveDopingRatio->id));
                        }
                    } else {
                        return json_encode(array('code' => 1, 'msg' => 'Some errors occurred.'));
                    }
                    break;

                case 'delete':
                    $ids = $_POST['ids'];
                    $modObj = ClassRegistry::init('AdditiveDopingRatio');
                    $result = $modObj->updateAll(
                        array('AdditiveDopingRatio.deleted' => "'y'"),
                        array('AdditiveDopingRatio.id' => $ids)
                    );
                    if ($result) {
                        $modObj = ClassRegistry::init('AdditiveDopingRatio');
                        $modObj->updateAll(
                            array('AdditiveDopingRatio.deleted' => "'y'"),
                            array('AdditiveDopingRatio.id' => $ids)
                        );

                     echo json_encode(array('code' => 0, 'msg' => 'Data Deleted!'));
                    } else {
                        echo json_encode(array('code' => 1, 'msg' => 'Data cannot be deleted'));
                    }
                    break;
            }
        }

        $additives_lists = $this->get_additives();
        
        $this->set(compact('additives_lists'));
	}


    function additive_cost_generation($type = 'get') {

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
                    /** Search string */
                    $rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
                    $limit = $rp;
                    $start = ($page - 1) * $rp;

                    $condition_array = array(
                        'AdditiveCostGeneration.omc_id' => $company_profile['id'],
                        'AdditiveCostGeneration.deleted' => 'n'
                    );

                    $contain = array(
                        'AdditiveSetup'=>array('fields' => array('AdditiveSetup.id', 'AdditiveSetup.name')),
                        'ProductType'=>array('fields' => array('ProductType.id', 'ProductType.name')),
                        'OmcCustomer'=>array('fields' => array('OmcCustomer.id', 'OmcCustomer.name')),
                        'Depot'=>array('fields' => array('Depot.id', 'Depot.name'))
                    );
                    
                    $data_table = $this->AdditiveCostGeneration->find('all', array('conditions' => $condition_array, 'contain'=>$contain,'order' => "AdditiveCostGeneration.$sortname $sortorder", 'limit' => $start . ',' . $limit, 'recursive' => 1));
                    $data_table_count = $this->AdditiveCostGeneration->find('count', array('recursive' => -1));
                    $total_records = $data_table_count;
                    


                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {
                            $return_arr[] = array(
                                'id' => $obj['AdditiveCostGeneration']['id'],
                                'cell' => array(
                                    $obj['AdditiveCostGeneration']['id'],
                                    $obj['AdditiveCostGeneration']['order_id'],
                                    $this->covertDate( $obj['AdditiveCostGeneration']['order_date'], 'mysql_flip'),
                                    $obj['OmcCustomer']['name'],
                                    $obj['Depot']['name'],
                                    $obj['ProductType']['name'],
                                    $obj['AdditiveCostGeneration']['truck_no'],
                                    $obj['AdditiveCostGeneration']['loading_quantity'],
                                    $this->covertDate( $obj['AdditiveCostGeneration']['loading_date'], 'mysql_flip'),
                                    $obj['AdditiveSetup']['name'],
                                    $obj['AdditiveCostGeneration']['doping_ratio'],
                                    $obj['AdditiveCostGeneration']['additive_quantity'],
                                    $obj['AdditiveCostGeneration']['additive_cost_ltr'],
                                    $obj['AdditiveCostGeneration']['invoice_additive_cost']
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
                    
                    $data = array('AdditiveCostGeneration' => $_POST);
        
                    if($_POST['id'] == 0){
                        $data['AdditiveCostGeneration']['created_by'] = $authUser['id'];
                    }
                    else{
                        $data['AdditiveCostGeneration']['modified_by'] = $authUser['id'];
                    }

                   //$data['AdditiveCostGeneration']['order_date'] = $this->covertDate($_POST['order_date'], 'mysql') . ' ' . date('H:i:s');
                   //$data['AdditiveCostGeneration']['doping_ratio'] = $this->covertDate($_POST['loading_date'], 'mysql') . ' ' . date('H:i:s');
                     $data['AdditiveCostGeneration']['doping_ratio'] =  round($_POST['doping_ratio'], 5);
                     $data['AdditiveCostGeneration']['additive_quantity'] =  number_format(round($_POST['additive_quantity'], 0, PHP_ROUND_HALF_UP),2);
             
                    $data['AdditiveCostGeneration']['omc_id'] = $company_profile['id'];
                    if ($this->AdditiveCostGeneration->save($this->sanitize($data))) {
                       
                        if($_POST['id'] > 0){
                            return json_encode(array('code' => 0, 'msg' => 'Data Updated!'));
                        }
                        else{
                            return json_encode(array('code' => 0, 'msg' => 'Data Saved!', 'id'=>$this->AdditiveCostGeneration->id));
                        }
                    } else {
                        return json_encode(array('code' => 1, 'msg' => 'Some errors occurred.'));
                    }
                    break;

                case 'delete':
                    $ids = $_POST['ids'];
                    $modObj = ClassRegistry::init('AdditiveCostGeneration');
                    $result = $modObj->updateAll(
                        array('AdditiveCostGeneration.deleted' => "'y'"),
                        array('AdditiveCostGeneration.id' => $ids)
                    );
                    if ($result) {
                        $modObj = ClassRegistry::init('AdditiveCostGeneration');
                        $modObj->updateAll(
                            array('AdditiveCostGeneration.deleted' => "'y'"),
                            array('AdditiveCostGeneration.id' => $ids)
                        );

                     echo json_encode(array('code' => 0, 'msg' => 'Data Deleted!'));
                    } else {
                        echo json_encode(array('code' => 1, 'msg' => 'Data cannot be deleted'));
                    }
                    break;
            }
        }
        $products_lists = $this->get_products();
        $additives_lists = $this->get_additives();
        $numbers = $this->Truck->getTruckNo();
        $depot_lists = $this->get_depot_list();
        $omc_customers_lists = $this->get_customer_list();
        
        $this->set(compact('additives_lists','products_lists','numbers','depot_lists','omc_customers_lists'));
	}


    function additive_stock_inventory1($type = 'get') {
        
        $this->setPermission('additive_stock_inventory1');
        $this->autoLayout = false;
        $this->autoRender = false;

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
                    /** Search string */
                    $rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
                    $limit = $rp;
                    $start = ($page - 1) * $rp;

                    $condition_array = array(
                        'AdditiveStock.omc_id' => $company_profile['id'],
                        'AdditiveStock.deleted' => 'n'
                    );

                    $contain = array(
                        'AdditiveSetup'=>array('fields' => array('AdditiveSetup.id', 'AdditiveSetup.name'))
                    );
                    
                    $data_table = $this->AdditiveStock->find('all', array('conditions' => $condition_array, 'contain'=>$contain,'order' => "AdditiveStock.$sortname $sortorder", 'limit' => $start . ',' . $limit, 'recursive' => 1));
                    $data_table_count = $this->AdditiveStock->find('count', array('recursive' => -1));

                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {
                            $return_arr[] = array(
                                'id' => $obj['AdditiveStock']['id'],
                                'cell' => array(
                                    $obj['AdditiveStock']['id'],
                                    $obj['AdditiveSetup']['name'],
                                    $obj['AdditiveStock']['drum_size'],
                                    $obj['AdditiveStock']['drum_cost'],
                                    $obj['AdditiveStock']['cost_per_ltr'],
                                    $obj['AdditiveStock']['total_no_dum'],
                                    $obj['AdditiveStock']['total_no_ltr'],
                                    $obj['AdditiveStock']['total_stock_cost']
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
                    
                    $data = array('AdditiveStock' => $_POST);
        
                    if($_POST['id'] == 0){
                        $data['AdditiveStock']['created_by'] = $authUser['id'];
                    }
                    else{
                        $data['AdditiveStock']['modified_by'] = $authUser['id'];
                    }

                    $data['AdditiveStock']['omc_id'] = $company_profile['id'];
                    if ($this->AdditiveStock->save($this->sanitize($data))) {
                        if($_POST['id'] > 0){
                            return json_encode(array('code' => 0, 'msg' => 'Data Updated!'));
                        }
                        else{
                            return json_encode(array('code' => 0, 'msg' => 'Data Saved!', 'id'=>$this->AdditiveStock->id));
                        }
                    } else {
                        return json_encode(array('code' => 1, 'msg' => 'Some errors occurred.'));
                    }
                    break;

                case 'delete':
                    $ids = $_POST['ids'];
                    $modObj = ClassRegistry::init('AdditiveStock');
                    $result = $modObj->updateAll(
                        array('AdditiveStock.deleted' => "'y'"),
                        array('AdditiveStock.id' => $ids)
                    );
                    if ($result) {
                        $modObj = ClassRegistry::init('AdditiveStock');
                        $modObj->updateAll(
                            array('AdditiveStock.deleted' => "'y'"),
                            array('AdditiveStock.id' => $ids)
                        );

                     echo json_encode(array('code' => 0, 'msg' => 'Data Deleted!'));
                    } else {
                        echo json_encode(array('code' => 1, 'msg' => 'Data cannot be deleted'));
                    }
                    break;
            }
        }

        $additives_lists = $this->get_additives();
        
        $this->set(compact('additives_lists'));
	}


    function additive_stock_received2($type = 'get') {

        $this->setPermission('additive_stock_received2');
        $this->autoLayout = false;
        $this->autoRender = false;
        
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
                    /** Search string */
                    $rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
                    $limit = $rp;
                    $start = ($page - 1) * $rp;

                    $condition_array = array(
                        'AdditiveStock.omc_id' => $company_profile['id'],
                        'AdditiveStock.deleted' => 'n'
                    );

                    $contain = array(
                        'AdditiveSetup'=>array('fields' => array('AdditiveSetup.id', 'AdditiveSetup.name'))
                    );
                    
                    $data_table = $this->AdditiveStock->find('all', array('conditions' => $condition_array, 'contain'=>$contain,'order' => "AdditiveStock.$sortname $sortorder", 'limit' => $start . ',' . $limit, 'recursive' => 1));
                    $data_table_count = $this->AdditiveStock->find('count', array('recursive' => -1));

                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {
                            $return_arr[] = array(
                                'id' => $obj['AdditiveStock']['id'],
                                'cell' => array(
                                    $obj['AdditiveStock']['id'],
                                    $obj['AdditiveSetup']['name'],
                                    $obj['AdditiveStock']['drum_size'],
                                    $obj['AdditiveStock']['drum_cost'],
                                    $obj['AdditiveStock']['cost_per_ltr'],
                                    $obj['AdditiveStock']['total_no_dum'],
                                    $obj['AdditiveStock']['total_no_ltr'],
                                    $obj['AdditiveStock']['total_stock_cost']
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
                    
                    $data = array('AdditiveStock' => $_POST);
        
                    if($_POST['id'] == 0){
                        $data['AdditiveStock']['created_by'] = $authUser['id'];
                    }
                    else{
                        $data['AdditiveStock']['modified_by'] = $authUser['id'];
                    }

                    $data['AdditiveStock']['omc_id'] = $company_profile['id'];
                    if ($this->AdditiveStock->save($this->sanitize($data))) {
                        if($_POST['id'] > 0){
                            return json_encode(array('code' => 0, 'msg' => 'Data Updated!'));
                        }
                        else{
                            return json_encode(array('code' => 0, 'msg' => 'Data Saved!', 'id'=>$this->AdditiveStock->id));
                        }
                    } else {
                        return json_encode(array('code' => 1, 'msg' => 'Some errors occurred.'));
                    }
                    break;

                case 'delete':
                    $ids = $_POST['ids'];
                    $modObj = ClassRegistry::init('AdditiveStock');
                    $result = $modObj->updateAll(
                        array('AdditiveStock.deleted' => "'y'"),
                        array('AdditiveStock.id' => $ids)
                    );
                    if ($result) {
                        $modObj = ClassRegistry::init('AdditiveStock');
                        $modObj->updateAll(
                            array('AdditiveStock.deleted' => "'y'"),
                            array('AdditiveStock.id' => $ids)
                        );

                     echo json_encode(array('code' => 0, 'msg' => 'Data Deleted!'));
                    } else {
                        echo json_encode(array('code' => 1, 'msg' => 'Data cannot be deleted'));
                    }
                    break;
            }
        }

        $additives_lists = $this->get_additives();
        
        $this->set(compact('additives_lists'));
	}



    function additive_stock_stock2($type = 'get') {

        $this->setPermission('additive_stock_stock2');
        $this->autoLayout = false;
        $this->autoRender = false;
        
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
                    /** Search string */
                    $rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
                    $limit = $rp;
                    $start = ($page - 1) * $rp;

                    $condition_array = array(
                        'AdditiveStock.omc_id' => $company_profile['id'],
                        'AdditiveStock.deleted' => 'n'
                    );

                    $contain = array(
                        'AdditiveSetup'=>array('fields' => array('AdditiveSetup.id', 'AdditiveSetup.name'))
                    );
                    
                    $data_table = $this->AdditiveStock->find('all', array('conditions' => $condition_array, 'contain'=>$contain,'order' => "AdditiveStock.$sortname $sortorder", 'limit' => $start . ',' . $limit, 'recursive' => 1));
                    $data_table_count = $this->AdditiveStock->find('count', array('recursive' => -1));

                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {
                            $return_arr[] = array(
                                'id' => $obj['AdditiveStock']['id'],
                                'cell' => array(
                                    $obj['AdditiveStock']['id'],
                                    $obj['AdditiveSetup']['name'],
                                    $obj['AdditiveStock']['drum_size'],
                                    $obj['AdditiveStock']['drum_cost'],
                                    $obj['AdditiveStock']['cost_per_ltr'],
                                    $obj['AdditiveStock']['total_no_dum'],
                                    $obj['AdditiveStock']['total_no_ltr'],
                                    $obj['AdditiveStock']['total_stock_cost']
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
                    
                    $data = array('AdditiveStock' => $_POST);
        
                    if($_POST['id'] == 0){
                        $data['AdditiveStock']['created_by'] = $authUser['id'];
                    }
                    else{
                        $data['AdditiveStock']['modified_by'] = $authUser['id'];
                    }

                    $data['AdditiveStock']['omc_id'] = $company_profile['id'];
                    if ($this->AdditiveStock->save($this->sanitize($data))) {
                        if($_POST['id'] > 0){
                            return json_encode(array('code' => 0, 'msg' => 'Data Updated!'));
                        }
                        else{
                            return json_encode(array('code' => 0, 'msg' => 'Data Saved!', 'id'=>$this->AdditiveStock->id));
                        }
                    } else {
                        return json_encode(array('code' => 1, 'msg' => 'Some errors occurred.'));
                    }
                    break;

                case 'delete':
                    $ids = $_POST['ids'];
                    $modObj = ClassRegistry::init('AdditiveStock');
                    $result = $modObj->updateAll(
                        array('AdditiveStock.deleted' => "'y'"),
                        array('AdditiveStock.id' => $ids)
                    );
                    if ($result) {
                        $modObj = ClassRegistry::init('AdditiveStock');
                        $modObj->updateAll(
                            array('AdditiveStock.deleted' => "'y'"),
                            array('AdditiveStock.id' => $ids)
                        );

                     echo json_encode(array('code' => 0, 'msg' => 'Data Deleted!'));
                    } else {
                        echo json_encode(array('code' => 1, 'msg' => 'Data cannot be deleted'));
                    }
                    break;
            }
        }

        $additives_lists = $this->get_additives();
        
        $this->set(compact('additives_lists'));
	}



    function additive_cost_wac($type = 'get') {

        $this->setPermission('additive_cost_wac');
        $this->autoLayout = false;
        $this->autoRender = false;
        
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
                    /** Search string */
                    $rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
                    $limit = $rp;
                    $start = ($page - 1) * $rp;

                    $condition_array = array(
                        'AdditiveStock.omc_id' => $company_profile['id'],
                        'AdditiveStock.deleted' => 'n'
                    );

                    $contain = array(
                        'AdditiveSetup'=>array('fields' => array('AdditiveSetup.id', 'AdditiveSetup.name'))
                    );
                    
                    $data_table = $this->AdditiveStock->find('all', array('conditions' => $condition_array, 'contain'=>$contain,'order' => "AdditiveStock.$sortname $sortorder", 'limit' => $start . ',' . $limit, 'recursive' => 1));
                    $data_table_count = $this->AdditiveStock->find('count', array('recursive' => -1));

                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {
                            $return_arr[] = array(
                                'id' => $obj['AdditiveStock']['id'],
                                'cell' => array(
                                    $obj['AdditiveStock']['id'],
                                    $obj['AdditiveSetup']['name'],
                                    $obj['AdditiveStock']['drum_size'],
                                    $obj['AdditiveStock']['drum_cost'],
                                    $obj['AdditiveStock']['cost_per_ltr'],
                                    $obj['AdditiveStock']['total_no_dum'],
                                    $obj['AdditiveStock']['total_no_ltr'],
                                    $obj['AdditiveStock']['total_stock_cost']
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
                    
                    $data = array('AdditiveStock' => $_POST);
        
                    if($_POST['id'] == 0){
                        $data['AdditiveStock']['created_by'] = $authUser['id'];
                    }
                    else{
                        $data['AdditiveStock']['modified_by'] = $authUser['id'];
                    }

                    $data['AdditiveStock']['omc_id'] = $company_profile['id'];
                    if ($this->AdditiveStock->save($this->sanitize($data))) {
                        if($_POST['id'] > 0){
                            return json_encode(array('code' => 0, 'msg' => 'Data Updated!'));
                        }
                        else{
                            return json_encode(array('code' => 0, 'msg' => 'Data Saved!', 'id'=>$this->AdditiveStock->id));
                        }
                    } else {
                        return json_encode(array('code' => 1, 'msg' => 'Some errors occurred.'));
                    }
                    break;

                case 'delete':
                    $ids = $_POST['ids'];
                    $modObj = ClassRegistry::init('AdditiveStock');
                    $result = $modObj->updateAll(
                        array('AdditiveStock.deleted' => "'y'"),
                        array('AdditiveStock.id' => $ids)
                    );
                    if ($result) {
                        $modObj = ClassRegistry::init('AdditiveStock');
                        $modObj->updateAll(
                            array('AdditiveStock.deleted' => "'y'"),
                            array('AdditiveStock.id' => $ids)
                        );

                     echo json_encode(array('code' => 0, 'msg' => 'Data Deleted!'));
                    } else {
                        echo json_encode(array('code' => 1, 'msg' => 'Data cannot be deleted'));
                    }
                    break;
            }
        }

        $additives_lists = $this->get_additives();
        
        $this->set(compact('additives_lists'));
	}
}