<?php

/**
 * @name BdcController.php
 */
App::import('Controller', 'BdcApp');

class BdcAdminController extends BdcAppController
{
    # Controller name

    var $name = 'BdcAdmin';
    # set the model to use
    var $uses = array('BdcUser', 'User','Group','Menu','MenuGroup', 'Bdc','BdcPackage', 'Depot', 'Omc', 'BdcOmc', 'Package','ProductType','BdcInitialStockStartup');
    # Set the layout to use
    var $layout = 'bdc_layout';

    public function beforeFilter($param_array = null)
    {
        parent::beforeFilter();
        //If Trading, check if there are new products added to depots, force them to initialise it
        $comp = $this->global_company;
        $StockTrading = ClassRegistry::init('StockTrading');
        $force_new_products_added_to_depot = false;
        if($StockTrading->isTrading($comp['id'])){
            $new_data = $this->get_new_added_depot_products();
            $depots_products = $new_data['depots_products'];
            if(!empty($depots_products)){
                $action = $this->params['action'];
                $controller = $this->params['controller'];
                if(in_array($action,array('access_control','admin_depots','admin_products','admin_depots_to_products'))){
                    //Allow
                    $force_new_products_added_to_depot = false;
                }
                else{
                    //Block all none required pages
                    $force_new_products_added_to_depot = true;
                }
            }
        }
        $this->set(compact('force_new_products_added_to_depot'));
    }

    public function index() {
        $this->redirect('users');
    }


    function users($type = 'get')
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
                    /** Search column */
                    $search_query = isset($_POST['query']) ? $_POST['query'] : '';
                    /** Search string */
                    $rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
                    $limit = $rp;
                    $start = ($page - 1) * $rp;

                    $condition_array = array('User.bdc_id' => $company_profile['id'], 'User.deleted' => 'n');
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

                    $contain = array('Group');
                    // $fields = array('User.id', 'User.username', 'User.first_name', 'User.last_name', 'User.group_id', 'User.active');
                    $data_table = $this->User->find('all', array('conditions' => $condition_array, 'contain'=>$contain,'order' => "User.$sortname $sortorder", 'limit' => $start . ',' . $limit, 'recursive' => 1));
                    $data_table_count = $this->User->find('count', array('conditions' => $condition_array, 'recursive' => -1));

                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {
                            $return_arr[] = array(
                                'id' => $obj['User']['id'],
                                'cell' => array(
                                    $obj['User']['id'],
                                    $obj['User']['fname'],
                                    $obj['User']['lname'],
                                    $obj['User']['username'],
                                    $obj['Group']['name'],
                                    $obj['User']['active']
                                ),
                                'extra_data' => array(//Sometime u need certain data to be stored on the main tr at the client side like the referencing table id for editing
                                    'group_id'=>$obj['Group']['id'],
                                ),
                                'property'=>array(
                                    'edit_row'=> $obj['User']['edit_row'],
                                    'bg_color'=> $obj['User']['bg_color']
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
                    $username = $_POST['username'];
                    if ($_POST['id'] == 0) {
                        $res = $this->_validateUsername($company_profile['id'], $username);
                        if ($res) {
                            return json_encode(array('success' => 1, 'msg' => 'Username already exist.'));
                        }
                    }
                    $data = array('User' => $_POST);
                    $data['User']['user_type'] = 'bdc';
                    $data['User']['bdc_id'] = $company_profile['id'];
                    $data['User']['modified_by'] = $this->Auth->user('id');
                    if ($_POST['id'] == 0) {
                        $data['User']['created_by'] = $this->Auth->user('id');
                    }

                    $pass = '';
                    if ($_POST['id'] == 0) {
                        $pass = $this->randomString(6);
                        $data['User']['password'] = AuthComponent::password($pass);
                        $data['User']['temp_pass'] = $pass;
                    } else {
                        $bk_user = $this->User->getUserById($_POST['id'],-1);
                        if($bk_user['User']['active']=='Disabled' && $_POST['active'] == 'Active'){
                            $data['User']['bg_color'] = '';
                            $modObj = ClassRegistry::init('Login');
                            $re = $modObj->deleteAll(array('Login.username' => $_POST['username']), false);
                        }
                    }

                    if ($this->User->save($this->sanitize($data))) {
                        if($_POST['id'] > 0){
                            $new_user = $_POST['fname'].' '.$_POST['lname'].' ('.$username.')';
                            $log_description = $this->getLogMessage('ModifiedUser')." (User: ".$new_user.")";
                            $this->logActivity('Administration',$log_description);
                            return json_encode(array('code' => 0, 'msg' => 'Data Saved'));
                        }
                        else{ //If new pass back extra data if needed.
                            //Activity Log
                            $new_user = $_POST['fname'].' '.$_POST['lname'].' ('.$username.')';
                            $log_description = $this->getLogMessage('CreateUser')." (User: ".$new_user.")";
                            $this->logActivity('Administration',$log_description);

                            return json_encode(array('code' => 0, 'msg' => 'Data Saved. The default password is '.$pass, 'id'=>$this->User->id));
                        }
                        //echo json_encode(array('success' => 0, 'msg' => 'Data Saved!', 'data' => $dt));
                    } else {
                        echo json_encode(array('code' => 1, 'msg' => 'Some errors occurred.'));
                    }

                break;

                case 'reset_password':
                    if(!in_array('E',$permissions)){
                        return json_encode(array('code' => 1, 'msg' => 'Access Denied.'));
                    }
                    $bk_user = $this->User->getUserById($_POST['id'],-1);
                    $data = array(
                        'id'=>$_POST['id'],
                        'password'=>AuthComponent::password($_POST['password']),
                        'modified_by' => $this->Auth->user('id')
                    );
                    if ($this->User->save($this->sanitize($data))) {
                        $new_user = $bk_user['User']['fname'].' '.$bk_user['User']['lname'].' ('. $bk_user['User']['username'].')';
                        $log_description = $this->getLogMessage('ResetPassword')." (User: ".$new_user.")";
                        $this->logActivity('Administration',$log_description);

                        return json_encode(array('code' => 0, 'msg' => 'Password has been reset.'));
                    } else {
                        echo json_encode(array('code' => 1, 'msg' => 'Password could not be reset.'));
                    }

                break;

                case 'load':

                    break;

                case 'delete':
                    if(!in_array('D',$permissions)){
                        return json_encode(array('code' => 1, 'msg' => 'Access Denied.'));
                    }

                    $ids = $_POST['ids'];
                    $modObj = ClassRegistry::init('User');
                    $result = $modObj->updateAll(
                        $this->sanitize(array('User.deleted' => "'y'")),
                        $this->sanitize(array('User.id' => $ids))
                    );
                    if ($result) {
                        echo json_encode(array('code' => 0, 'msg' => 'Data Deleted!'));
                    } else {
                        echo json_encode(array('code' => 1, 'msg' => 'Data cannot be deleted'));
                    }
                    break;
            }
        }


        $group_data = $this->Group->getGroups('bdc',$company_profile['id']);
        $group_options = $group_data;

        $controller = $this;
        $this->set(compact('controller', 'group_options'));
    }


    function groups($type = 'get')
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
                    /** Search column */
                    $search_query = isset($_POST['query']) ? $_POST['query'] : '';
                    /** Search string */
                    $rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
                    $limit = $rp;
                    $start = ($page - 1) * $rp;


                    $condition_array = array('Group.type' =>'bdc','Group.bdc_id' => $company_profile['id'], 'Group.deleted' => 'n');

                    if (!empty($search_query)) {
                        if ($qtype == 'name') {
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
                        'Depot'=>array('fields' => array('Depot.id', 'Depot.name'))
                    );
                    // $fields = array('User.id', 'User.username', 'User.first_name', 'User.last_name', 'User.group_id', 'User.active');
                    $data_table = $this->Group->find('all', array('conditions' => $condition_array,'contain'=>$contain,'order' => "Group.$sortname $sortorder", 'limit' => $start . ',' . $limit, 'recursive' => 1));
                    $data_table_count = $this->Group->find('count', array('conditions' => $condition_array, 'recursive' => -1));

                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {
                            $group_depot = $obj['Group']['group_depot'];
                            $gd_name = 'All';
                            if($group_depot > 0){
                                $gd_name = $obj['Depot']['name'];
                            }
                            $return_arr[] = array(
                                'id' => $obj['Group']['id'],
                                'cell' => array(
                                    $obj['Group']['id'],
                                    $obj['Group']['name'],
                                    $gd_name
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
                    $data = array('Group' => $_POST);
                    $data['Group']['type'] = 'bdc';
                    $data['Group']['bdc_id'] = $company_profile['id'];
                    $data['Group']['modified_by'] = $this->Auth->user('id');
                    if ($_POST['id'] == 0) {
                        $data['Group']['created_by'] = $this->Auth->user('id');
                    }

                    if ($this->Group->save($this->sanitize($data))) {
                        if($_POST['id'] > 0){
                            $new_user = $_POST['name'];
                            $log_description = $this->getLogMessage('ModifiedGroup')." (Group: ".$new_user.")";
                            $this->logActivity('Administration',$log_description);

                            return json_encode(array('code' => 0, 'msg' => 'Data Saved'));
                        }
                        else{ //If new pass back extra data if needed.
                            $new_user = $_POST['name'];
                            $log_description = $this->getLogMessage('CreateGroup')." (Group: ".$new_user.")";
                            $this->logActivity('Administration',$log_description);
                            return json_encode(array('code' => 0, 'msg' => 'Data Saved.', 'id'=>$this->Group->id));
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
                    $modObj = ClassRegistry::init('Group');
                    $result = $modObj->updateAll(
                        $this->sanitize(array('Group.deleted' => "'y'")),
                        $this->sanitize(array('Group.id' => $ids))
                    );
                    if ($result) {
                        echo json_encode(array('code' => 0, 'msg' => 'Data Deleted!'));
                    } else {
                        echo json_encode(array('code' => 1, 'msg' => 'Data cannot be deleted'));
                    }
                    break;
            }
        }

        $my_depots = $this->get_depot_list();
        array_unshift($my_depots,array('id'=>'0','name'=>'All'));

        $controller = $this;
        $this->set(compact('controller','my_depots'));

    }


    function access_control($group_id=null){
        $authUser = $this->Auth->user();
        $company_profile = $this->global_company;
        if ($this->request->is('post')) {
            $data = $this->request->data['AccessControl'];
            $save_new = array();
            foreach($data['d'] as $acd){
                if($acd['menu_id'] > 0){
                    $perm = array();
                    if(isset($acd['add']) && $acd['add'] == 'A'){
                        $perm[]=$acd['add'];
                    }
                    if(isset($acd['edit']) && $acd['edit'] == 'E'){
                        $perm[]=$acd['edit'];
                    }
                    if(isset($acd['print_export']) && $acd['print_export'] == 'PX'){
                        $perm[]=$acd['print_export'];
                    }
                    if(isset($acd['delete']) && $acd['delete'] == 'D'){
                        $perm[]=$acd['delete'];
                    }
                    $p = implode(',',$perm);
                    $save_new[] = array(
                        'id'=>'',
                        'bdc_id'=>$company_profile['id'],
                        'menu_id'=>$acd['menu_id'],
                        'group_id'=>$data['group_id'],
                        'permission'=>$p,
                        'created_by'=>$authUser['id'],
                        'modified_by'=>$authUser['id']
                    );
                }
            }

            //first delete the existing menu records for this group
            $this->MenuGroup->deleteAll(array('MenuGroup.bdc_id' => $company_profile['id'],'MenuGroup.group_id' => $data['group_id']), false);
            $res = $this->MenuGroup->saveAll($this->sanitize($save_new));
            if ($res) {
                $this->Session->setFlash('Access Control Setup Has been saved !');
                $this->Session->write('process_error', 'no');
            }
            else {
                $this->Session->setFlash("Sorry, can't save Access Control Setup.");
                $this->Session->write('process_error', 'yes');
            }

            $this->redirect(array('action' => 'access_control/'.$data['group_id']));

        }

        $group_data = $this->Group->getGroups('bdc',$company_profile['id']);
        $group_options = array();
        foreach($group_data as $g){
            $group_options[$g['id']] = $g['name'];
        }
        $gp = $group_data;

        $group = $group_id;
        $first_group = array_shift($gp);
        if($group == null){
            $group = $first_group['id'];
        }
        $package = $this->BdcPackage->getByPackageId($company_profile['bdc_package_id']);
        $modules = explode(',',$package['BdcPackage']['modules']);
        $menu_data = $this->Menu->getMenusToAssign('bdc',$modules);
        $group_menu_data = $this->MenuGroup->getGroupMenusIds('bdc',$group,$company_profile['id']);
        $group_menu_ids = array_keys($group_menu_data);

        $controller = $this;
        $this->set(compact('controller','group_options','menu_data','group_menu_ids','group','group_menu_data'));
    }


    function _validateUsername($company_id = null, $username = null)
    {
        $data = $this->User->find('first', array(
            'conditions' => array('User.username' => $username,'User.deleted' =>'n'),
            'recursive' => -1
        ));

        if ($data) {
            return true;
        } else {
            return false;
        }
    }



    function _getCompanyUsers($company_id = null)
    {
        $user_ids = array();
        $data = $this->BdcUser->find('all', array(
            'fields' => array('user_id'),
            'conditions' => array('bdc_id' => $company_id),
            'recursive' => -1
        ));
        foreach ($data as $value) {
            $user_ids[] = $value['BdcUser']['user_id'];
        }
        return $user_ids;
    }


    function admin_depots()
    {
        $authUser = $this->Auth->user();
        $company_profile = $this->global_company;
        if ($this->request->is('post')) {
            $my_depots = $this->request->data['MyDepots'];
            $fin = array();
            foreach($my_depots as $sel){
                if($sel['my_depot_id'] > 0){
                    $fin[]=$sel['my_depot_id'];
                }
            }
            $fin_str = implode(',',$fin);
            if(empty($fin_str)){
                $fin_str = null;
            }
            // If a depot has been removed we have to remove it from the depot to product matrix
            $depots_to_products = $this->Bdc->getDepotToProduct($company_profile['id']);
            $str_arr = array();
            foreach($fin as $val){
                $products_str = implode(',',$depots_to_products[$val]);
                $inner_str = $val.'|'.$products_str;
                $str_arr[] = $inner_str;
            }
            $str = implode('#',$str_arr);

            $save = array('Bdc');
            $save['Bdc']['id'] = $company_profile['id'];
            $save['Bdc']['my_depots_to_products'] = $str;
            $save['Bdc']['modified_by'] = $authUser['id'];
            $save['Bdc']['my_depots'] = $fin_str;

            $res = $this->Bdc->save($this->sanitize($save['Bdc']));
            if ($res) {
                $log_description = $this->getLogMessage('ModifyDepotList');
                $this->logActivity('Administration',$log_description);

                $this->Session->setFlash('Depots has been updated !');
                $this->Session->write('process_error', 'no');
            }
            else {
                $this->Session->setFlash('Sorry, Depots update failed.');
                $this->Session->write('process_error', 'yes');
            }


            $this->redirect(array('action' => 'admin_depots'));
        }

        $all_depots = $this->get_depot_list(false);
        $depots_products = $this->Bdc->getDepotProduct($company_profile['id']);
        $my_depots = $depots_products['my_depots'];
        if($my_depots == null){
            $my_depots = array();
        }
        $controller = $this;
        $this->set(compact('controller', 'all_depots','my_depots'));
    }

    function admin_products()
    {
        $authUser = $this->Auth->user();
        $company_profile = $this->global_company;
        $depots_products = $this->Bdc->getDepotProduct($company_profile['id']);
        $my_products = $depots_products['my_products'];

        if ($this->request->is('post')) {
            $my_depots = $this->request->data['MyProducts'];
            $fin = array();
            foreach($my_depots as $sel){
                if($sel['my_product_id'] > 0){
                    $fin[]=$sel['my_product_id'];
                }
            }
            $fin_str = implode(',',$fin);
            if(empty($fin_str)){
                $fin_str = null;
            }

            // If a product has been removed we have to remove it from the depot to product matrix
            $depots_to_products = $this->Bdc->getDepotToProduct($company_profile['id']);
            $product_diff = array_diff($my_products,$fin);
            foreach($fin as $prefer_product_id){
                foreach($depots_to_products as $depot_key => $products_arr){
                    foreach($products_arr as $key => $prod){
                        if(in_array($prod,$product_diff)){
                           unset($depots_to_products[$depot_key][$key]);
                        }
                    }
                }
            }
            $str_arr = array();
            foreach($depots_to_products as $k_dept => $pr_arr){
                $products_str = implode(',',$pr_arr);
                $inner_str = $k_dept.'|'.$products_str;
                $str_arr[] = $inner_str;
            }
            $str = implode('#',$str_arr);

            $save = array('Bdc');
            $save['Bdc']['id'] = $company_profile['id'];
            $save['Bdc']['my_depots_to_products'] = $str;
            $save['Bdc']['modified_by'] = $authUser['id'];
            $save['Bdc']['my_products'] = $fin_str;

            $res = $this->Bdc->save($this->sanitize($save['Bdc']));
            if ($res) {
                $log_description = $this->getLogMessage('ModifyProductList');
                $this->logActivity('Administration',$log_description);

                $this->Session->setFlash('Products has been updated !');
                $this->Session->write('process_error', 'no');
            }
            else {
                $this->Session->setFlash('Sorry, Products update failed.');
                $this->Session->write('process_error', 'yes');
            }

            $this->redirect(array('action' => 'admin_products'));
        }

        $all_products = $this->get_products(false);
        if($my_products == null){
            $my_products = array();
        }
        $controller = $this;
        $this->set(compact('controller', 'all_products','my_products'));
    }


    function admin_depots_to_products()
    {
        $authUser = $this->Auth->user();
        $company_profile = $this->global_company;
        if ($this->request->is('post')) {
            $my_depots_to_products = $this->request->data;
            $fin = array();
            foreach($my_depots_to_products as $depot_id => $products_arr){
                $cache_pro = array();
                foreach($products_arr as $product_d){
                    if($product_d['my_pro_id'] > 0){
                        $cache_pro[]=$product_d['my_pro_id'];
                    }
                }
                $fin[]=$depot_id.'|'.implode(',',$cache_pro);
            }
            //debug($fin);
            $fin_str = implode('#',$fin);
            if(empty($fin_str)){
                $fin_str = null;
            }
            //debug($fin_str);
            $save = array('Bdc');
            $save['Bdc']['id'] = $company_profile['id'];
            $save['Bdc']['modified_by'] = $authUser['id'];
            $save['Bdc']['my_depots_to_products'] = $fin_str;

            $res = $this->Bdc->save($this->sanitize($save['Bdc']));
            if ($res) {
                $log_description = $this->getLogMessage('ModifyDepotProductRelation');
                $this->logActivity('Administration',$log_description);


                $this->Session->setFlash('Data has been updated !');
                $this->Session->write('process_error', 'no');
            }
            else {
                $this->Session->setFlash('Sorry, Data update failed.');
                $this->Session->write('process_error', 'yes');
            }

            //exit;
            $this->redirect(array('action' => 'admin_depots_to_products'));
        }

        $my_depots = $this->get_depot_list();
        $my_products = $this->get_products();
        $depots_to_products = $this->Bdc->getDepotToProduct($company_profile['id']);

        $controller = $this;
        $this->set(compact('controller','my_depots','my_products','depots_to_products'));
    }



   /* function admin_omcs($type = 'get')
    {
        //We are taking the reponsibility of adding Omcs to the Bdcs and removing them too
        $this->redirect('index');

        if ($this->request->is('ajax')) {
            $this->autoRender = false;
            $this->autoLayout = false;
            $authUser = $this->Auth->user();
            $company_profile = $this->global_company;
            switch ($type) {
                case 'get' :
                    $page = isset($_POST['page']) ? $_POST['page'] : 1;

                    $sortname = isset($_POST['sortname']) ? $_POST['sortname'] : 'id';

                    $sortorder = isset($_POST['sortorder']) ? $_POST['sortorder'] : 'desc';

                    $qtype = isset($_POST['qtype']) ? $_POST['qtype'] : '';

                    $search_query = isset($_POST['query']) ? $_POST['query'] : '';

                    $rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
                    $limit = $rp;
                    $start = ($page - 1) * $rp;

                    //get users id for this company only
                    $condition_array = array(
                        'BdcOmc.bdc_id' => $company_profile['id'],
                        'BdcOmc.deleted' => 'n'
                    );

                    if (!empty($search_query)) {
                        if ($qtype == 'username') {

                        }
                        else {

                        }
                    }

                    $contain = array('Omc');
                    // $fields = array('User.id', 'User.username', 'User.first_name', 'User.last_name', 'User.group_id', 'User.active');
                    $data_table = $this->BdcOmc->find('all', array('conditions' => $condition_array, 'contain'=>$contain,'order' => "BdcOmc.$sortname $sortorder", 'limit' => $start . ',' . $limit, 'recursive' => 1));
                    $data_table_count = $this->BdcOmc->find('count', array('conditions' => $condition_array, 'recursive' => -1));

                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {
                            $return_arr[] = array(
                                'id' => $obj['BdcOmc']['id'],
                                'cell' => array(
                                    $obj['BdcOmc']['id'],
                                    $obj['Omc']['name']
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
                    $omc = $this->Omc->getOmcById($_POST['omc_id']);
                    $id = $_POST['id'];
                    $bdc_id = $company_profile['id'];
                    $omc_id = $_POST['omc_id'];
                    //Check if this BDC has exceeded it's number of omcs
                    $package = $this->Package->find('first', array(
                        'conditions' => array('Package.id' => $company_profile['package_id']),
                        'contain' => array('PackageType'),
                        'recursive' => 1
                    ));
                    if($id == 0){//adding new omc
                        $number_of_omcs = $package['PackageType']['number_of_omc'];
                        $res = $this->BdcOmc->find('count', array(
                            'conditions' => array('bdc_id' => $bdc_id,'deleted'=>'n'),
                            'recursive' => -1
                        ));
                        $current_omcs_count = $res + 1; //Plus one , we have to consider this new record too
                        if($current_omcs_count <= $number_of_omcs){
                            //Allow Add the new one
                        }
                        else{
                            return json_encode(array('code' => 1, 'msg' =>"The allowed number of OMCs to add has reached it's limit."));
                        }
                    }

                    //check if Omc exist for this bdc
                    if($id > 0){//if existing
                        $res1 = $this->BdcOmc->find('first', array(
                            'conditions' => array('id' => $id),
                            'recursive' => -1
                        ));
                        if($res1['BdcOmc']['omc_id'] != $omc_id ){
                            $res = $this->BdcOmc->find('first', array(
                                'conditions' => array('bdc_id' => $bdc_id, 'omc_id' => $omc_id,'deleted'=>'n'),
                                'recursive' => -1
                            ));
                            if ($res) {
                                return json_encode(array('code' => 1, 'msg' => $omc['Omc']['name'] . ' Already exist.'));
                            }
                        }
                    }
                    else{//New
                        $res = $this->BdcOmc->find('first', array(
                            'conditions' => array('bdc_id' => $bdc_id, 'omc_id' => $omc_id,'deleted'=>'n'),
                            'recursive' => -1
                        ));
                        if ($res) {
                            return json_encode(array('code' => 1, 'msg' => $omc['Omc']['name'] . ' Already exist.'));
                        }
                    }
                    $save = array(
                        'id'=> $id,
                        'bdc_id'=>$bdc_id,
                        'omc_id'=>$omc_id,
                        'bdc_omc_sub_package'=> $package['PackageType']['omc_users'],//This will use the default omc pack config until an OMC request changes to it package for one BDC
                        'created_by'=>$authUser['id']
                    );
                    if($id > 0){ //Existing Record
                        $save = array(
                            'id'=> $id,
                            'bdc_id'=>$bdc_id,
                            'omc_id'=>$omc_id,
                            'modified_by'=>$authUser['id']
                        );
                    }

                    if ($this->BdcOmc->save($this->sanitize($save))) {
                        if($id > 0){
                            return json_encode(array('code' => 0, 'msg' => 'Data Updated!'));
                        }
                        else{ //If new pass back extra data if needed.
                            return json_encode(array('code' => 0, 'msg' => 'Data Saved!', 'id'=>$this->BdcOmc->id,'data' => array('default_pass' => $omc['Omc']['default_admin_pass'], 'name' => $omc['Omc']['name'], 'username' => $omc['Omc']['default_admin_username'])));
                        }
                    } else {
                        return json_encode(array('code' => 1, 'msg' => 'Some errors occured.'));
                    }

                    break;

                case 'load':

                    break;

                case 'delete':
                    $ids = $_POST['ids'];
                    $modObj = ClassRegistry::init('BdcOmc');
                    $result = $modObj->updateAll(
                        $this->sanitize(array('BdcOmc.deleted' => "'y'")),
                        $this->sanitize(array('BdcOmc.id' => $ids))
                    );
                    if ($result) {
                        echo json_encode(array('code' => 0, 'msg' => 'Data Deleted!'));
                    } else {
                        echo json_encode(array('code' => 1, 'msg' => 'Data cannot be deleted'));
                    }
                    break;
            }
        }

        $omclist_arr = $this->admin_get_omc_list();

        $this->set(compact('omclist_arr'));
    }*/


    function company()
    {
        $company_profile = $this->global_company;
        $company_key = $company_profile['locator_number'];
        if ($this->request->is('post')) {
            //$this->autoRender = false;
            //$this->autoLayout = false;

            //debug($_POST);
            $res = $this->Bdc->save($this->sanitize($this->request->data));

            if ($res) {
                /*$bdc = $this->Bdc->find('first', array(
                    'conditions' => array('Bdc.id' => $company_profile['id']),
                    'recursive' => -1
                ));*/

                $log_description = $this->getLogMessage('CompanyProfile');
                $this->logActivity('Administration',$log_description);

                //$this->Session->write('CompanyProfile', $bdc['Bdc']);
                $this->Session->setFlash('Company profile info has been updated !');
                $this->Session->write('process_error', 'no');
                //return json_encode(array('success'=>0,'msg'=>'Data Saved!','data'=>array('default_pass'=>$omc['Omc']['default_admin_pass'],'name'=>$omc['Omc']['name'],'username'=>$omc['Omc']['default_admin_username'])));
            } else {
                //return json_encode(array('success'=>1,'msg'=>'Some errors occured.'));
                $this->Session->setFlash('Sorry, Company profile info could not be updated');
                $this->Session->write('process_error', 'yes');
            }

            $this->redirect(array('action' => 'company'));
        }

        $bdc = $this->Bdc->find('first', array(
            'conditions' => array('Bdc.id' => $company_profile['id']),
            'contain' => array('Package' => array('PackageType')),
            'recursive' => 2
        ));

        //debug($bdc);
        $controller = $this;
        $this->set(compact('bdc', 'controller','company_key'));
    }


    function activity_logs($type = 'get')
    {
        $permissions = $this->action_permission;
        $company_profile = $this->global_company;
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
                    /** Search column */
                    $search_query = isset($_POST['query']) ? $_POST['query'] : '';
                    /** @var $filter  */
                    $filter =   isset($_POST['filter']) ? $_POST['filter'] : 0 ;
                    /** Search string */
                    $rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
                    $limit = $rp;
                    $start = ($page - 1) * $rp;

                    $condition_array = array(
                        'ActivityLog.type' => $authUser['user_type'],
                        'ActivityLog.entity_id' => $company_profile['id']
                    );

                    if($filter != 0){
                        $condition_array['ActivityLog.user_id'] = $filter;
                    }

                    $data_table = $this->ActivityLog->find('all', array('conditions' => $condition_array,'order' => "ActivityLog.$sortname $sortorder", 'limit' => $start . ',' . $limit, 'recursive' => -1));
                    $data_table_count = $this->ActivityLog->find('count', array('conditions' => $condition_array, 'recursive' => -1));

                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {
                            $return_arr[] = array(
                                'id' => $obj['ActivityLog']['id'],
                                'cell' => array(
                                    $this->covertDate($obj['ActivityLog']['created'],'mysql_flip'),
                                    $obj['ActivityLog']['user_full_name'],
                                    $obj['ActivityLog']['activity'],
                                    $obj['ActivityLog']['description']
                                )
                            );
                        }
                        return json_encode(array('success' => true, 'total' => $total_records, 'page' => $page, 'rows' => $return_arr));
                    }
                    else {
                        return json_encode(array('success' => false, 'total' => $total_records, 'page' => $page, 'rows' => array()));
                    }

                    break;
            }
        }

        $entity_users = $this->getEntityUsers();
        $entity_users_filter = array( array('name'=>'All','value'=>0));
        foreach($entity_users as $key => $value){
            $entity_users_filter[] = array(
                'name'=>$value['User']['fname'].' '.$value['User']['mname'].' '.$value['User']['lname'],
                'value'=>$value['User']['id']
            );
        }

        $this->set(compact('entity_users_filter'));
    }

}