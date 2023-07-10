<?php

/**
 * @name BdcController.php
 */
App::import('Controller', 'DepotApp');

class DepotAdminController extends DepotAppController
{
    # Controller name

    var $name = 'DepotAdmin';
    # set the model to use
    var $uses = array('User','Group','Menu','MenuGroup', 'Depot');
    # Set the layout to use
    var $layout = 'depot_layout';

    public function beforeFilter($param_array = null)
    {
        parent::beforeFilter();
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

                    $condition_array = array('User.depot_id' => $company_profile['id'], 'User.deleted' => 'n');
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
                    $data['User']['user_type'] = $this->Auth->user('user_type');
                    $data['User']['depot_id'] = $company_profile['id'];
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


        $group_data = $this->Group->getGroups($this->Auth->user('user_type'),$company_profile['id']);
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


                    $condition_array = array('Group.type' =>$this->Auth->user('user_type'),'Group.depot_id' => $company_profile['id'], 'Group.deleted' => 'n');

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
                    // $fields = array('User.id', 'User.username', 'User.first_name', 'User.last_name', 'User.group_id', 'User.active');
                    $data_table = $this->Group->find('all', array('conditions' => $condition_array,'order' => "Group.$sortname $sortorder", 'limit' => $start . ',' . $limit, 'recursive' => -1));
                    $data_table_count = $this->Group->find('count', array('conditions' => $condition_array, 'recursive' => -1));

                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {
                            $return_arr[] = array(
                                'id' => $obj['Group']['id'],
                                'cell' => array(
                                    $obj['Group']['id'],
                                    $obj['Group']['name']
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
                    $data['Group']['type'] = $this->Auth->user('user_type');
                    $data['Group']['depot_id'] = $company_profile['id'];
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

        $controller = $this;
        $this->set(compact('controller'));

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
                        'depot_id'=>$company_profile['id'],
                        'menu_id'=>$acd['menu_id'],
                        'group_id'=>$data['group_id'],
                        'permission'=>$p,
                        'created_by'=>$authUser['id'],
                        'modified_by'=>$authUser['id']
                    );
                }
            }

            //first delete the existing menu records for this group
            $this->MenuGroup->deleteAll(array('MenuGroup.depot_id' => $company_profile['id'],'MenuGroup.group_id' => $data['group_id']), false);
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

        $group_data = $this->Group->getGroups($this->Auth->user('user_type'),$company_profile['id']);
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
        $menu_data = $this->Menu->getMenusToAssign($this->Auth->user('user_type'));
        $group_menu_data = $this->MenuGroup->getGroupMenusIds($this->Auth->user('user_type'),$group,$company_profile['id']);
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


    function company()
    {
        $company_profile = $this->global_company;
        $company_key = $company_profile['locator_number'];
        if ($this->request->is('post')) {
            $res = $this->Depot->save($this->sanitize($this->request->data));

            if ($res) {
                //$this->Session->write('CompanyProfile', $bdc['Bdc']);
                $log_description = $this->getLogMessage('CompanyProfile');
                $this->logActivity('Administration',$log_description);

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

        $org = $this->Depot->find('first', array(
            'conditions' => array('Depot.id' => $company_profile['id']),
            'recursive' => -1
        ));

        //debug($bdc);
        $controller = $this;
        $this->set(compact('org', 'controller','company_key'));
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