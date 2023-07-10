<?php

/**
 * @name BdcController.php
 */
App::import('Controller', 'NpaApp');

class NpaAdminController extends NpaAppController
{
    # Controller name

    var $name = 'NpaAdmin';
    # set the model to use
    var $uses = array('User','Group','Menu','MenuGroup', 'Org','Region','ProductType','Depot','FreightRateCategory','PriceChange');
    # Set the layout to use
    var $layout = 'npa_layout';

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

                    $condition_array = array('User.org_id' => $company_profile['id'], 'User.deleted' => 'n');
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
                    $data['User']['user_type'] = 'org';
                    $data['User']['org_id'] = $company_profile['id'];
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


        $group_data = $this->Group->getGroups('org',$company_profile['id']);
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


                    $condition_array = array('Group.type' =>'org','Group.org_id' => $company_profile['id'], 'Group.deleted' => 'n');

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
                    $data['Group']['type'] = 'org';
                    $data['Group']['org_id'] = $company_profile['id'];
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
                        'org_id'=>$company_profile['id'],
                        'menu_id'=>$acd['menu_id'],
                        'group_id'=>$data['group_id'],
                        'permission'=>$p,
                        'created_by'=>$authUser['id'],
                        'modified_by'=>$authUser['id']
                    );
                }
            }

            //first delete the existing menu records for this group
            $this->MenuGroup->deleteAll(array('MenuGroup.org_id' => $company_profile['id'],'MenuGroup.group_id' => $data['group_id']), false);
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

        $group_data = $this->Group->getGroups('org',$company_profile['id']);
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
        $menu_data = $this->Menu->getMenusToAssign('npa');
        $group_menu_data = $this->MenuGroup->getGroupMenusIds('org',$group,$company_profile['id']);
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



    function company()
    {
        $company_profile = $this->global_company;
        $company_key = $company_profile['locator_number'];
        if ($this->request->is('post')) {
            //$this->autoRender = false;
            //$this->autoLayout = false;

            //debug($_POST);
            $res = $this->Org->save($this->sanitize($this->request->data));

            if ($res) {
               /* $bdc = $this->Org->find('first', array(
                    'conditions' => array('Org.id' => $company_profile['id']),
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

        $org = $this->Org->find('first', array(
            'conditions' => array('Org.id' => $company_profile['id']),
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


    /*** OTHER SETUPS **/


    function products($type = 'get')
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

                    $condition_array = array('ProductType.deleted' => 'n');

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
                        'FreightRateCategory'=>array('fields' => array('FreightRateCategory.id', 'FreightRateCategory.name'))
                    );

                    $data_table = $this->ProductType->find('all', array('conditions' => $condition_array, 'contain'=>$contain,'order' => "ProductType.$sortname $sortorder", 'limit' => $start . ',' . $limit, 'recursive' => 1));
                    $data_table_count = $this->ProductType->find('count', array('conditions' => $condition_array, 'recursive' => -1));

                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {
                            $return_arr[] = array(
                                'id' => $obj['ProductType']['id'],
                                'cell' => array(
                                    $obj['ProductType']['id'],
                                    $obj['ProductType']['name'],
                                    $obj['ProductType']['short_name'],
                                    $obj['ProductType']['group'],
                                    $obj['FreightRateCategory']['name']
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
                    $data = array('ProductType' => $_POST);
                    $data['ProductType']['modified_by'] = $this->Auth->user('id');
                    if ($_POST['id'] == 0) {
                        $data['ProductType']['created_by'] = $this->Auth->user('id');
                    }


                    if ($this->ProductType->save($this->sanitize($data))) {
                        if($_POST['id'] > 0){
                            $new_user = $_POST['name'];
                            $log_description = $this->getLogMessage('ModifyProduct')." (Product: ".$new_user.")";
                            $this->logActivity('Administration',$log_description);

                            return json_encode(array('code' => 0, 'msg' => 'Data Saved'));
                        }
                        else{ //If new pass back extra data if needed.
                            $new_user = $_POST['name'];
                            $log_description = $this->getLogMessage('CreateProduct')." (Product: ".$new_user.")";
                            $this->logActivity('Administration',$log_description);

                            return json_encode(array('code' => 0, 'msg' => 'Data Saved.', 'id'=>$this->ProductType->id));
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
                    $modObj = ClassRegistry::init('ProductType');
                    $now = "'".date('Y-m-d H:i:s')."'";
                    $result = $modObj->updateAll(
                        $this->sanitize(array('ProductType.deleted' => "'y'",'ProductType.modified' => "$now",'ProductType.modified_by' => $this->Auth->user('id'))),
                        $this->sanitize(array('ProductType.id' => $ids))
                    );
                    if ($result) {
                        echo json_encode(array('code' => 0, 'msg' => 'Data Deleted!'));
                    } else {
                        echo json_encode(array('code' => 1, 'msg' => 'Data cannot be deleted'));
                    }
                    break;
            }
        }


        $rate_cats = $this->FreightRateCategory->getCategories();

        $filter_rate_cats =array();
        $rate_cat_options =array();
        foreach($rate_cats as $arr){
            $filter_rate_cats[] = array('name'=>$arr['FreightRateCategory']['name'],'value'=>$arr['FreightRateCategory']['id']);
            $rate_cat_options[]= array('name'=>$arr['FreightRateCategory']['name'],'id'=>$arr['FreightRateCategory']['id']);
        }

        $product_group_data = $this->get_product_group();
        //$product_group_list = array('all'=>'All Product Type');
        $product_group_options = array();
        foreach($product_group_data as $arr){
            $product_group_options[] = array('name'=>$arr,'id'=>$arr);
        }


        $controller = $this;
        $this->set(compact('controller', 'rate_cat_options','filter_rate_cats','product_group_options'));
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

                    $condition_array = array('PriceChange.deleted' => 'n');

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

                    $data_table = $this->PriceChange->find('all', array('conditions' => $condition_array, 'contain'=>$contain,'order' => "PriceChange.$sortname $sortorder", 'limit' => $start . ',' . $limit, 'recursive' => 1));
                    $data_table_count = $this->PriceChange->find('count', array('conditions' => $condition_array, 'recursive' => -1));

                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {
                            $return_arr[] = array(
                                'id' => $obj['PriceChange']['id'],
                                'cell' => array(
                                    $obj['PriceChange']['id'],
                                    $obj['ProductType']['name'],
                                    $obj['PriceChange']['description'],
                                    $obj['PriceChange']['price'],
                                    $obj['PriceChange']['unit']
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
                    $data = array('PriceChange' => $_POST);
                    $data['PriceChange']['modified_by'] = $this->Auth->user('id');
                    if ($_POST['id'] == 0) {
                        $data['PriceChange']['created_by'] = $this->Auth->user('id');
                    }


                    if ($this->PriceChange->save($this->sanitize($data))) {
                        if($_POST['id'] > 0){
                            $new_user = $_POST['id'];
                            $log_description = $this->getLogMessage('ModifyPriceChange')." (Price Change ID: ".$new_user.")";
                            $this->logActivity('Administration',$log_description);

                            return json_encode(array('code' => 0, 'msg' => 'Data Saved'));
                        }
                        else{ //If new pass back extra data if needed.
                            $new_user = $this->PriceChange->id;
                            $log_description = $this->getLogMessage('CreatePriceChange')." (Price Change ID: ".$new_user.")";
                            $this->logActivity('Administration',$log_description);

                            return json_encode(array('code' => 0, 'msg' => 'Data Saved.', 'id'=>$this->PriceChange->id));
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
                    $modObj = ClassRegistry::init('PriceChange');
                    $now = "'".date('Y-m-d H:i:s')."'";
                    $result = $modObj->updateAll(
                        $this->sanitize(array('PriceChange.deleted' => "'y'",'PriceChange.modified' => "$now",'PriceChange.modified_by' => $this->Auth->user('id'))),
                        $this->sanitize(array('PriceChange.id' => $ids))
                    );
                    if ($result) {
                        echo json_encode(array('code' => 0, 'msg' => 'Data Deleted!'));
                    } else {
                        echo json_encode(array('code' => 1, 'msg' => 'Data cannot be deleted'));
                    }
                    break;
            }
        }


        $product_list_data = $this->get_product_list();
        // debug($product_list_data);
        //$product_group_list = array('all'=>'All Product Type');
        $product_list = array();
        foreach($product_list_data as $arr){
            $product_list[] = array('name'=>$arr['name'],'id'=>$arr['id']);
        }
        // debug($product_list);
        $controller = $this;
        $this->set(compact('controller', 'product_list'));
    }


    function export_price_change(){
        $download = false;
        $company_profile = $this->global_company;

        $export_data = $this->PriceChange->find('all', array(
            //'fields'=>array('OmcCustomer.id','OmcCustomer.order_status','BdcDistribution.loading_date','BdcDistribution.waybill_date','BdcDistribution.collection_order_no','BdcDistribution.quantity','BdcDistribution.waybill_id','BdcDistribution.vehicle_no'),
            'conditions' => array('PriceChange.deleted' => 'n'),
            'contain'=>array(
                'ProductType'=>array('fields'=>array('ProductType.id','ProductType.name'))
            ),
            'order'=>array('PriceChange.id'=>'desc'),
            'recursive' => 1
        ));

        //debug($export_data);
        if ($export_data) {
            $download = true;
            $list_data = array();
            foreach ($export_data as $obj) {
                $list_data[] = array(
                    $obj['ProductType']['name'],
                    $obj['PriceChange']['price']
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


    function regions($type = 'get')
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


                    $condition_array = array('Region.deleted' => 'n');

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

                    $data_table = $this->Region->find('all', array('conditions' => $condition_array,'order' => "Region.$sortname $sortorder", 'limit' => $start . ',' . $limit, 'recursive' => -1));
                    $data_table_count = $this->Region->find('count', array('conditions' => $condition_array, 'recursive' => -1));

                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {
                            $return_arr[] = array(
                                'id' => $obj['Region']['id'],
                                'cell' => array(
                                    $obj['Region']['id'],
                                    $obj['Region']['name'],
                                    $obj['Region']['short_name']
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
                    $data = array('Region' => $_POST);
                    $data['Region']['modified_by'] = $this->Auth->user('id');
                    if ($_POST['id'] == 0) {
                        $data['Region']['created_by'] = $this->Auth->user('id');
                    }

                    if ($this->Region->save($this->sanitize($data))) {
                        if($_POST['id'] > 0){
                            $new_user = $_POST['name'];
                            $log_description = $this->getLogMessage('ModifyRegion')." (Region: ".$new_user.")";
                            $this->logActivity('Administration',$log_description);

                            return json_encode(array('code' => 0, 'msg' => 'Data Saved'));
                        }
                        else{ //If new pass back extra data if needed.
                            $new_user = $_POST['name'];
                            $log_description = $this->getLogMessage('CreateRegion')." (Region: ".$new_user.")";
                            $this->logActivity('Administration',$log_description);

                            return json_encode(array('code' => 0, 'msg' => 'Data Saved.', 'id'=>$this->Region->id));
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

                    $now = "'".date('Y-m-d H:i:s')."'";
                    $ids = $_POST['ids'];
                    $modObj = ClassRegistry::init('Region');
                    $result = $modObj->updateAll(
                        $this->sanitize(array('Region.deleted' => "'y'",'Region.modified' => "$now",'Region.modified_by' => $this->Auth->user('id'))),
                        $this->sanitize(array('Region.id' => $ids))
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


}