<?php

/**
 * @name OmcController.php
 */
App::import('Controller', 'OmcCustomerApp');
class OmcCustomerAdminController extends OmcCustomerAppController
{
    # Controller name

    var $name = 'OmcCustomerAdmin';
    # set the model to use
    var $uses = array('Group', 'User','OmcCustomerUser','OmcCustomer' , 'Omc', 'Package','ProductType','Menu','MenuGroup');
    # Set the layout to use
    var $layout = 'omc_customer_layout';

    public function beforeFilter($param_array = null)
    {
        parent::beforeFilter(array('omc_customer_user_types'=>array('Allow All')));
    }

    function index()
    {
        $this->redirect('admin_products');
    }


    function access_control($group_id=null){
        $authUser = $this->Auth->user();
        $company_profile = $this->global_company;
        if ($this->request->is('post')) {
            $data = $this->request->data['AccessControl'];
            $group_data = $this->Group->getGroupById($data['group_id']);
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
                        'omc_customer_id'=>$company_profile['id'],
                        'menu_id'=>$acd['menu_id'],
                        'group_id'=>$data['group_id'],
                        'permission'=>$p,
                        'created_by'=>$authUser['id'],
                        'modified_by'=>$authUser['id']
                    );
                }
            }

            //first delete the existing menu records for this group
            $this->MenuGroup->deleteAll(array('MenuGroup.omc_customer_id' => $company_profile['id'],'MenuGroup.group_id' => $data['group_id']), false);
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

        $group_data = $this->Group->getGroups('omc_customer',$company_profile['id']);
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

        $menu_data = $this->Menu->getMenusToAssign('omc_customer',null);
        $group_menu_data = $this->MenuGroup->getGroupMenusIds('omc_customer',$group,$company_profile['id']);
        $group_menu_ids = array_keys($group_menu_data);

        $controller = $this;
        $this->set(compact('controller','group_options','menu_data','group_menu_ids','group','group_menu_data'));
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


                    $condition_array = array('Group.type' =>'omc_customer','Group.omc_customer_id' => $company_profile['id'],'Group.deleted' => 'n');

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
                    //$contian = array('OmcCustomer'=>array('fields'=>array('OmcCustomer.id', 'OmcCustomer.name')));
                    // $fields = array('User.id', 'User.username', 'User.first_name', 'User.last_name', 'User.group_id', 'User.active');
                    $data_table = $this->Group->find('all', array('conditions' => $condition_array,'order' => "Group.$sortname $sortorder", 'limit' => $start . ',' . $limit, 'recursive' => 1));
                    $data_table_count = $this->Group->find('count', array('conditions' => $condition_array, 'recursive' => -1));

                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {
                            $return_arr[] = array(
                                'id' => $obj['Group']['id'],
                                'cell' => array(
                                    $obj['Group']['id'],
                                    $obj['Group']['name'],
                                    //$obj['OmcCustomer']['name']
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
                    $data['Group']['type'] = 'omc_customer';
                    $data['Group']['omc_customer_id'] = $company_profile['id'];
                    $data['Group']['modified_by'] = $this->Auth->user('id');
                    if ($_POST['id'] == 0) {
                        $data['Group']['created_by'] = $this->Auth->user('id');
                    }

                    if ($this->Group->save($this->sanitize($data))) {
                        if($_POST['id'] > 0){
                            return json_encode(array('code' => 0, 'msg' => 'Data Saved'));
                        }
                        else{ //If new pass back extra data if needed.
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

        $controller = $this;
        $this->set(compact('controller','permissions'));

    }


    function staff_account($type = 'get')
    {
        $permissions = $this->action_permission;
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
                    /** @var $filter  */
                    $filter =   isset($_POST['filter']) ? $_POST['filter'] : 0 ;
                    /** Search string */
                    $rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
                    $limit = $rp;
                    $start = ($page - 1) * $rp;

                    $condition_array = array('User.omc_customer_id' => $company_profile['id'],'User.user_type'=>'omc_customer', 'User.deleted' => 'n');
                    if (!empty($search_query)) {
                        if ($qtype == 'username') {
                            $condition_array = array(
                                'User.username' => $search_query,
                                'User.deleted' => 'n'
                            );
                        }
                        else {
                            $condition_array = array(
                                "User.$qtype LIKE" => $search_query . '%',
                                'User.deleted' => 'n'
                            );
                        }
                    }

                    $contain = array(
                        'Group'
                    );
                    // $fields = array('User.id', 'User.username', 'User.first_name', 'User.last_name', 'User.group_id', 'User.active');
                    $data_table = $this->User->find('all', array('conditions' => $condition_array, 'contain'=>$contain,'order' => "User.$sortname $sortorder", 'limit' => $start . ',' . $limit, 'recursive' => 2));
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
                            return json_encode(array('code' => 1, 'msg' => 'Username already exist.'));
                        }
                    }
                    $data = array('User' => $_POST);
                    $data['User']['user_type'] = 'omc_customer';
                    $data['User']['omc_customer_id'] = $company_profile['id'];
                    $data['User']['modified_by'] = $this->Auth->user('id');
                    if ($_POST['id'] == 0) {
                        $data['User']['created_by'] = $this->Auth->user('id');
                    }

                    $pass = '';
                    $is_new = 'n';
                    if ($_POST['id'] == 0) {
                        $pass = $this->randomString(6);
                        $data['User']['password'] = AuthComponent::password($pass);
                        $data['User']['temp_pass'] = $pass;
                        $is_new = 'y';
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
                            return json_encode(array('code' => 0, 'msg' => 'Data Updated!'));
                        }
                        else{ //If new pass back extra data if needed.
                            return json_encode(array('code' => 0, 'msg' => 'Data Saved. The default password is '.$pass, 'id'=>$this->User->id));
                        }
                    } else {
                        echo json_encode(array('code' => 1, 'msg' => 'Some errors occurred.'));
                    }

                    break;

                case 'reset_password':
                    if(!in_array('E',$permissions)){
                        return json_encode(array('code' => 1, 'msg' => 'Access Denied.'));
                    }
                    $data = array(
                        'id'=>$_POST['id'],
                        'password'=>AuthComponent::password($_POST['password']),
                        'modified_by' => $this->Auth->user('id')
                    );
                    if ($this->User->save($this->sanitize($data))) {
                        return json_encode(array('code' => 0, 'msg' => 'Password has been reset.'));
                    } else {
                        echo json_encode(array('code' => 1, 'msg' => 'Password could not be reset.'));
                    }

                    break;

                case 'load':
                    $user_id = $_POST['user_id'];

                    $user_data = $this->User->find('first', array(
                        'conditions' => array('User.id' => $user_id),
                        'contain' => array('OmcCustomerUser' => array('OmcCustomer')),
                        'recursive' => 2
                    ));

                    //debug($user_data);
                    if ($user_data) {
                        echo json_encode(array('success' => 0, 'data' => $user_data));
                    } else {
                        echo json_encode(array('success' => 1));
                    }

                    break;

                case 'delete':
                    if(!in_array('D',$permissions)){
                        return json_encode(array('code' => 1, 'msg' => 'Access Denied.'));
                    }
                    $ids = $_POST['ids'];
                    $modObj = ClassRegistry::init('User');
                    $result = $modObj->updateAll(
                        array('User.deleted' => "'y'"),
                        array('User.id' => $ids)
                    );
                    if ($result) {
                        echo json_encode(array('code' => 0, 'msg' => 'Data Deleted!'));
                    } else {
                        echo json_encode(array('code' => 1, 'msg' => 'Data cannot be deleted'));
                    }
                    break;
            }
        }

        $group_data = $this->Group->getGroups('omc_customer',$company_profile['id']);
        $group_options = $group_data;

        $this->set(compact('group_options'));
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


    function admin_products()
    {
        $authUser = $this->Auth->user();
        $company_profile = $this->global_company;
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

            $save = array('Omc');
            $save['OmcCustomer']['id'] = $company_profile['id'];
            $save['OmcCustomer']['modified_by'] = $authUser['id'];
            $save['OmcCustomer']['my_products'] = $fin_str;

            $res = $this->OmcCustomer->save($this->sanitize($save['OmcCustomer']));
            if ($res) {
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
        $depots_products = $this->OmcCustomer->getOmcCustomerProduct($company_profile['id']);
        $my_products = $depots_products['my_products'];
        if($my_products == null){
            $my_products = array();
        }
        $controller = $this;
        $this->set(compact('controller', 'all_products','my_products'));
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