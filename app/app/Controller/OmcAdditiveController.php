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
    var $uses = array('OmcCustomer','OmcCustomerTankMinstocklevel','OmcCustomerTank','OmcDsrpDataOption','OmcTank','OmcTankStatus','OmcTankType','AdditiveSetup','AdditiveStock','Omc','AdditiveDopingRatio');

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
                            return json_encode(array('code' => 0, 'msg' => 'Data Saved!', 'id'=>$this->Nct->id));
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

                    $data['AdditiveDopingRatio']['omc_id'] = $company_profile['id'];
                    if ($this->AdditiveDopingRatio->save($this->sanitize($data))) {
                        if($_POST['id'] > 0){
                            return json_encode(array('code' => 0, 'msg' => 'Data Updated!'));
                        }
                        else{
                            return json_encode(array('code' => 0, 'msg' => 'Data Saved!', 'id'=>$this->Nct->id));
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

                    $data['AdditiveDopingRatio']['omc_id'] = $company_profile['id'];
                    if ($this->AdditiveDopingRatio->save($this->sanitize($data))) {
                        if($_POST['id'] > 0){
                            return json_encode(array('code' => 0, 'msg' => 'Data Updated!'));
                        }
                        else{
                            return json_encode(array('code' => 0, 'msg' => 'Data Saved!', 'id'=>$this->Nct->id));
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


}