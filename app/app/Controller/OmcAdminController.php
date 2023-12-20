<?php

/**
 * @name OmcController.php
 */
App::import('Controller', 'OmcApp');
class OmcAdminController extends OmcAppController
{
    # Controller name

    var $name = 'OmcAdmin';
    # set the model to use
    var $uses = array('User','Group','Menu','MenuGroup','OmcCustomerUser','OmcCustomer' ,'OmcUserBdc', 'Depot', 'Omc', 'Bdc', 'BdcOmc', 'OmcPackage','ProductType','Truck','Nct','CustomerCreditSetting',
                        'LubeSetting','LpgSetting','AdditiveSetup','OmcCustomerPriceChange');
    # Set the layout to use
    var $layout = 'omc_layout';

    public function beforeFilter($param_array = null)
    {
        parent::beforeFilter();
    }

    /*Admin part
    */
    function index($type = 'get')
    {
        // debug($this->Auth->user());
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
                    /** Search string */
                    $rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
                    $limit = $rp;
                    $start = ($page - 1) * $rp;

                    $condition_array = array('User.omc_id' => $company_profile['id'],'User.user_type'=>'omc', 'User.deleted' => 'n');
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
                    $data_table = $this->User->find('all', array('conditions' => $condition_array, 'contain'=>$contain,'order' => "User.$sortname $sortorder", 'page' => $page  , 'limit'=> $limit, 'recursive' => 1));
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
                    $data['User']['user_type'] = 'omc';
                    $data['User']['omc_id'] = $company_profile['id'];
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
                            $new_user = $_POST['fname'].' '.$_POST['lname'].' ('.$username.')';
                            $log_description = $this->getLogMessage('ModifiedUser')." (User: ".$new_user.")";
                            $this->logActivity('Administration',$log_description);

                            return json_encode(array('code' => 0, 'msg' => 'Data Updated!'));
                        }
                        else{ //If new pass back extra data if needed.
                            //Activity Log
                            $new_user = $_POST['fname'].' '.$_POST['lname'].' ('.$username.')';
                            $log_description = $this->getLogMessage('CreateUser')." (User: ".$new_user.")";
                            $this->logActivity('Administration',$log_description);

                            return json_encode(array('code' => 0, 'msg' => 'Data Saved. The default password is '.$pass, 'id'=>$this->User->id));
                        }
                    }
                    else {
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
                    $user_id = $_POST['user_id'];

                    $user_data = $this->User->find('first', array(
                        'conditions' => array('User.id' => $user_id),
                        'contain' => array('OmcUser' => array('OmcUserBdc')),
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
                        $modObj = ClassRegistry::init('OmcUser');
                        $modObj->updateAll(
                            array('OmcUser.deleted' => "'y'"),
                            array('OmcUser.user_id' => $ids)
                        );
                        echo json_encode(array('code' => 0, 'msg' => 'Data Deleted!'));
                    } else {
                        echo json_encode(array('code' => 1, 'msg' => 'Data cannot be deleted'));
                    }
                    break;

                case 'load_details':
                    $user_id = $_POST['id'];
                    $omc_id = $company_profile['id'];

                    $gdata = $this->OmcUser->find('first', array(
                        'conditions' => array('OmcUser.user_id' => $user_id,'OmcUser.omc_id'=>$omc_id),
                        'contain' => array('OmcUserBdc'=>array('Bdc'=>array('fields'=>array('Bdc.id','Bdc.name')))),
                        'recursive' => 2
                    ));
                    //debug($gdata);
                    $return_arr = array();
                    if($gdata){
                        foreach($gdata['OmcUserBdc'] as $sub_obj){
                            $omc_bdcs_user_id = $sub_obj['id'];
                            $return_arr[] = array(
                                'id' => $omc_bdcs_user_id,
                                'cell' => array(
                                    $sub_obj['Bdc']['name']
                                )
                            );
                        }
                        return json_encode(array('code' => 0, 'rows' => $return_arr));
                    }
                    else {
                        return json_encode(array('code' => 1, 'rows' => array(), 'mesg' => __('No Record Found')));
                    }
                    break;

                case 'sub_save' :
                    //validate users against package chosen by bdc
                    $id = $_POST['id']; //id for Omc Bdc User table
                    $user_id = $_POST['parent_id']; //The User ID
                    $bdc_id = $_POST['bdc_id'];

                    $res = $this->_validateUserPackage($_POST);
                    if (!$res['status']) {
                        return json_encode(array('code' => 1, 'msg' => $res['msg']));
                    }

                    $omc_user_data = $this->OmcUser->find('first', array(
                        'conditions' => array('OmcUser.user_id' => $user_id,'OmcUser.omc_id' => $company_profile['id']),
                        'recursive' => -1
                    ));

                    $save['OmcUserBdc']=array(
                        'id'=>$id,
                        'omc_user_id'=>$omc_user_data['OmcUser']['id'],
                        'bdc_id'=>$bdc_id
                    );

                    if ($this->OmcUserBdc->save($this->sanitize($save['OmcUserBdc']))) {
                        if($id > 0){
                            return json_encode(array('code' => 0, 'msg' => 'Data Saved'));
                        }
                        else{ //If new pass back extra data if needed.
                            return json_encode(array('code' => 0, 'msg' => 'Data Saved', 'id'=>$this->OmcUserBdc->id));
                        }
                    } else {
                        return json_encode(array('code' => 1, 'msg' => 'Some errors occured.'));
                    }
                    break;

                case 'sub_delete':
                    $ids = $_POST['ids'];
                    $modObj = ClassRegistry::init('OmcUserBdc');
                    $result = $modObj->deleteAll(array('OmcUserBdc.id' => $ids),false);
                    if ($result) {
                        echo json_encode(array('code' => 0, 'msg' => 'Data Deleted!'));
                    } else {
                        echo json_encode(array('code' => 1, 'msg' => 'Data cannot be deleted'));
                    }
                    break;
            }
        }

        //Get Bdcs for this Omc
        $omc_bdcs_arr = $this->get_bdc_list();

        $group_data = $this->Group->getGroups('omc',$company_profile['id']);
        $group_options = $group_data;

        $this->set(compact('omc_bdcs_arr','group_options'));
    }


    function customers($type = 'get')
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

                    $condition_array = array('OmcCustomer.omc_id' => $company_profile['id'],'OmcCustomer.deleted' => 'n');
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
                        'District'=>array('fields' => array('District.id', 'District.name')),
                        'Region'=>array('fields' => array('Region.id', 'Region.name'))
                    );
                    // $fields = array('User.id', 'User.username', 'User.first_name', 'User.last_name', 'User.group_id', 'User.active');
                    $data_table = $this->OmcCustomer->find('all', array('conditions' => $condition_array, 'contain'=>$contain,'order' => "OmcCustomer.$sortname $sortorder", 'page' => $page  , 'limit'=> $limit, 'recursive' => 1));
                    $data_table_count = $this->OmcCustomer->find('count', array('conditions' => $condition_array, 'recursive' => -1));

                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {
                            $return_arr[] = array(
                                'id' => $obj['OmcCustomer']['id'],
                                'cell' => array(
                                    $obj['OmcCustomer']['id'],
                                    $obj['OmcCustomer']['name'],
                                    $obj['OmcCustomer']['owner'],
                                    $obj['OmcCustomer']['dealer'],
                                    $obj['OmcCustomer']['station_type'],
                                    $obj['OmcCustomer']['address'],
                                    $obj['OmcCustomer']['telephone'],
                                    $obj['OmcCustomer']['admin_username'],
                                    '**********'//Password
                                    /*$obj['OmcCustomer']['credit_limit'],
                                    $obj['OmcCustomer']['credit_days']*/
                                ),
                                'extra_data' => array(//Sometime u need certain data to be stored on the main tr at the client side like the referencing table id for editing
                                    'admin_username'=>$obj['OmcCustomer']['admin_username']
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
                    //$old_admin_username = isset($_POST['extra']['admin_username'])? $_POST['extra']['admin_username']:'';
                    //Check if username is in use
                    if ($_POST['id'] == 0) {
                        $res = $this->_validateUsername($company_profile['id'], $_POST['admin_username']);
                        if ($res) {
                            return json_encode(array('code' => 1, 'msg' => 'Username already exist.'));
                        }
                    }
                    else{
                        /*if($old_admin_username != $_POST['admin_username']){
                            $res = $this->_validateUsername($company_profile['id'], $_POST['admin_username']);
                            if ($res) {
                                return json_encode(array('code' => 1, 'msg' => 'Username already exist.'));
                            }
                        }*/
                    }

                    $data = array('OmcCustomer' => $_POST);
                    $data['OmcCustomer']['omc_id'] = $company_profile['id'];
                    if($_POST['id'] == 0){
                        $data['OmcCustomer']['created_by'] = $authUser['id'];
                    }
                    else{
                        $data['OmcCustomer']['modified_by'] = $authUser['id'];
                    }

                    if ($this->OmcCustomer->save($this->sanitize($data))) {
                        if($_POST['id'] > 0){
                            $new_user = $_POST['name'];
                            $log_description = $this->getLogMessage('ModifyCustomer')." (Customer: ".$new_user.")";
                            $this->logActivity('Administration',$log_description);

                            return json_encode(array('code' => 0, 'msg' => 'Data Updated!'));
                        }
                        else{
                            //Create account for customer user
                            $this->createCustomerAdminAccount($_POST['admin_username'],$_POST['admin_pass'],$this->OmcCustomer->id);

                            $new_user = $_POST['name'];
                            $log_description = $this->getLogMessage('CreateCustomer')." (Customer: ".$new_user.")";
                            $this->logActivity('Administration',$log_description);

                            return json_encode(array('code' => 0, 'msg' => 'Data Saved!', 'id'=>$this->OmcCustomer->id));
                        }
                        //echo json_encode(array('success' => 0, 'msg' => 'Data Saved'));
                    } else {
                        return json_encode(array('code' => 1, 'msg' => 'Some errors occurred.'));
                    }
                    break;

                case 'delete':

                    break;
            }
        }

        $data = $this->get_region_district();
        $regions_lists = $data['region'];
        $district_lists = $data['district'];
        $glbl_region_district = $data['region_district'];
        //$location_list = $this->get_location_list();
        $st_type = array('0'=>array('id'=>'COSS','name'=>'COSS'),'1'=>array('id'=>'DOCO','name'=>'DOCO'),'2'=>array('id'=>'DODO','name'=>'DODO'));

        $this->set(compact('regions_lists', 'district_lists','glbl_region_district','location_list','st_type'));

    }


    function createCustomerAdminAccount($username,$pass,$omc_customer_id){
        //create a group for this account to use
        $user_id = $this->Auth->user('id');
        $group_data = array(
            'name'=>'System Administrators',
            'type'=>'omc_customer',
            'omc_customer_id'=>$omc_customer_id,
            'modified_by'=>$user_id,
            'created_by'=>$user_id
        );
        $this->Group->save($group_data);
        $group_id = $this->Group->id;
        //Assign this group access control so they can take it on from there.
        $group_menu = array(
            'omc_customer_id'=>$omc_customer_id,
            'menu_id'=>53,//Access control
            'group_id'=>$group_id,
            'permission'=>'E',
            'created_by'=>$user_id,
            'modified_by'=>$user_id,
        );
        $this->MenuGroup->save($group_menu);

        $user_data = array(
            'fname'=>'Admin',
            'lname'=>'User',
            'username'=>$username,
            'password'=>AuthComponent::password($pass),
            'temp_pass'=>$pass,
            'user_type'=>'omc_customer',
            'omc_customer_id'=>$omc_customer_id,
            'modified_by'=>$user_id,
            'created_by'=>$user_id,
            'group_id'=>$group_id
        );
        $this->User->save($user_data);
    }


    function export_customers(){
        $download = false;
        $company_profile = $this->global_company;

        $export_data = $this->OmcCustomer->find('all', array(
            //'fields'=>array('OmcCustomer.id','OmcCustomer.order_status','BdcDistribution.loading_date','BdcDistribution.waybill_date','BdcDistribution.collection_order_no','BdcDistribution.quantity','BdcDistribution.waybill_id','BdcDistribution.vehicle_no'),
            'conditions' => array('OmcCustomer.omc_id' => $company_profile['id'],'OmcCustomer.deleted' => 'n'),
            'contain'=>array(
                'Region'=>array('fields'=>array('Region.id','Region.name')),
                'District'=>array('fields'=>array('District.id','District.name'))
            ),
            'order' => array("OmcCustomer.name"=>'asc'),
            'recursive' => 1
        ));

        //debug($export_data);
        if ($export_data) {
            $download = true;
            $list_data = array();
            foreach ($export_data as $obj) {
                $list_data[] = array(
                    $obj['OmcCustomer']['name'],
                    $obj['Region']['name'],
                    $obj['District']['name'],
                    $obj['OmcCustomer']['address'],
                    $obj['OmcCustomer']['telephone']
                );
            }
            $list_headers = array('Name','Region','District','Address','Telephone');
            $filename = $company_profile['name']." Daily Upload ".date('Ymdhis');
            $res = $this->convertToExcel($list_headers,$list_data,$filename);
            $objPHPExcel = $res['excel_obj'];
            $filename = $res['filename'];
        }

        $this->autoLayout = false;

        $this->set(compact('objPHPExcel', 'download', 'filename'));
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


                    $condition_array = array('Group.type' =>'omc','Group.omc_id' => $company_profile['id'], 'Group.deleted' => 'n');

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
                    $data_table = $this->Group->find('all', array('conditions' => $condition_array,'contain'=>$contain,'order' => "Group.$sortname $sortorder", 'page' => $page  , 'limit'=> $limit, 'recursive' => 1));
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
                    $data['Group']['type'] = 'omc';
                    $data['Group']['omc_id'] = $company_profile['id'];
                    $data['Group']['modified_by'] = $this->Auth->user('id');
                    if ($_POST['id'] == 0) {
                        $data['Group']['created_by'] = $this->Auth->user('id');
                    }

                    if ($this->Group->save($this->sanitize($data))) {
                        if($_POST['id'] > 0){
                            $new_user = $_POST['name'];
                            $log_description = $this->getLogMessage('ModifiedGroup')." (Group: ".$new_user.")";
                            $this->logActivity('Administration',$log_description);

                            return json_encode(array('code' => 0, 'msg' => 'Data Updated'));
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
        //debug($my_depots);
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
                        'omc_id'=>$company_profile['id'],
                        'menu_id'=>$acd['menu_id'],
                        'group_id'=>$data['group_id'],
                        'permission'=>$p,
                        'created_by'=>$authUser['id'],
                        'modified_by'=>$authUser['id']
                    );
                }
            }

            //first delete the existing menu records for this group
            $this->MenuGroup->deleteAll(array('MenuGroup.omc_id' => $company_profile['id'],'MenuGroup.group_id' => $data['group_id']), false);
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

        $group_data = $this->Group->getGroups('omc',$company_profile['id']);
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
        $package = $this->OmcPackage->getByPackageId($company_profile['omc_package_id']);
        $modules = explode(',',$package['OmcPackage']['modules']);
        $menu_data = $this->Menu->getMenusToAssign('omc',$modules);
        $group_menu_data = $this->MenuGroup->getGroupMenusIds('omc',$group,$company_profile['id']);
        $group_menu_ids = array_keys($group_menu_data);

        $controller = $this;
        $this->set(compact('controller','group_options','menu_data','group_menu_ids','group','group_menu_data'));
    }


    function _validateUsername($company_id = null, $username = null)
    {
        /*$data = $this->User->find('first', array(
            'conditions' => array('User.username' => $username, 'OmcUser.omc_id' => $company_id),
            'contain' => array('OmcUser'),
            'recursive' => 1
        ));*/

        $data = $this->User->find('first', array(
            'conditions' => array('User.username' => $username,'User.deleted' =>'n'),
            //'contain' => array('BdcUser'),
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
        $data = $this->OmcUser->find('all', array(
            'fields' => array('user_id'),
            'conditions' => array('omc_id' => $company_id),
            'recursive' => -1
        ));
        foreach ($data as $value) {
            $user_ids[] = $value['OmcUser']['user_id'];
        }
        return $user_ids;
    }


    function _getCustomerUsers($company_id = null,$filter)
    {
        $condition_array = array('OmcCustomer.omc_id' => $company_id);
        if($filter != 0){
            $condition_array['OmcCustomer.id'] = $filter;
        }
        $user_ids = array();
        $data = $this->OmcCustomer->find('all', array(
            'fields' => array('OmcCustomer.id'),
            'conditions' => $condition_array,
            'contain'=>array('OmcCustomerUser'=>array('fields' => array('OmcCustomerUser.user_id'),)),
            'recursive' => 2
        ));

        foreach ($data as $value) {
            foreach ($value['OmcCustomerUser'] as $value2) {
                $user_ids[] = $value2['user_id'];
            }
        }

        return $user_ids;
    }




    function _validateUserPackage($post)
    {
        $return = true;
        $msg = '';

        return array(
            'status' => $return,
            'msg' => $msg
        );
    }


    function company()
    {
        $company_profile = $this->global_company;
        $company_key = $company_profile['locator_number'];
        if ($this->request->is('post')) {
            //$this->autoRender = false;
            //$this->autoLayout = false;

            //debug($_POST);
            $res = $this->Omc->save($this->sanitize($this->request->data));

            if ($res) {
               /* $omc = $this->Omc->find('first', array(
                    'conditions' => array('Omc.id' => $company_profile['id']),
                    'recursive' => -1
                ));*/

                $log_description = $this->getLogMessage('CompanyProfile');
                $this->logActivity('Administration',$log_description);

                //$this->Session->write('CompanyProfile', $omc['Omc']);
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

        $company = $this->Omc->find('first', array(
            'conditions' => array('Omc.id' => $company_profile['id']),
            //'contain'=>array('Package'=>array('PackageType')),
            'recursive' => -1
        ));
        //debug($bdc);
        $controller = $this;
        $this->set(compact('company', 'controller','company_key'));
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

            $save = array('Omc');
            $save['Omc']['id'] = $company_profile['id'];
            $save['Omc']['modified_by'] = $authUser['id'];
            $save['Omc']['my_depots'] = $fin_str;

            $res = $this->Omc->save($this->sanitize($save['Omc']));
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
        $depots_products = $this->Omc->getOmcDepot($company_profile['id']);
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
            $save['Omc']['id'] = $company_profile['id'];
            $save['Omc']['modified_by'] = $authUser['id'];
            $save['Omc']['my_products'] = $fin_str;

            $res = $this->Omc->save($this->sanitize($save['Omc']));
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

        $all_products = $this->get_products(false);//filter products
        $depots_products = $this->Omc->getOmcProduct($company_profile['id']);
        $my_products = $depots_products['my_products'];
        if($my_products == null){
            $my_products = array();
        }
        $controller = $this;
        $this->set(compact('controller', 'all_products','my_products'));
    }



    function manage_bdc()
    {
        $authUser = $this->Auth->user();
        $company_profile = $this->global_company;
        if ($this->request->is('post')) {
            $my_depots = $this->request->data['MyBDCs'];
            $save = array('BdcOmc');
            foreach($my_depots as $sel){
                if($sel['my_bdc_id'] > 0){
                    //$fin[]=$sel['my_bdc_id'];
                    $save['BdcOmc'][] = array(
                        'omc_id'=>$company_profile['id'],
                        'bdc_id'=>$sel['my_bdc_id'],
                        'created_by'=> $authUser['id'],
                        'modified_by'=> $authUser['id']
                    );
                }
            }

            //first delete the existing bdc records for this omc
            $this->BdcOmc->deleteAll(array('BdcOmc.omc_id' => $company_profile['id']), false);

            if(isset($save['BdcOmc'])) {
                $res = $this->BdcOmc->saveAll($this->sanitize($save['BdcOmc']));
                if ($res) {

                    $log_description = $this->getLogMessage('ModifyBdcRelation');
                    $this->logActivity('Administration',$log_description);

                    $this->Session->setFlash('BDCs has been connected !');
                    $this->Session->write('process_error', 'no');
                }
                else {
                    $this->Session->setFlash('Sorry, BDCs update failed.');
                    $this->Session->write('process_error', 'yes');
                }
            } else {
                $this->Session->setFlash('BDCs has been disconnected !');
                $this->Session->write('process_error', 'no');
            }

            $this->redirect(array('action' => 'manage_bdc'));
        }

        $my_bdc_list = $this->get_bdc_omc_list();//Bdc this Omc is connected with on this system
        $my_bdc_list_ids = array();
        foreach($my_bdc_list as $arr){
            $my_bdc_list_ids[] = $arr['id'];
        }
        $all_bdcs = $this->get_all_bdc_list();

        $controller = $this;
        $this->set(compact('controller', 'all_bdcs','my_bdc_list_ids'));
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

                    $data_table = $this->ActivityLog->find('all', array('conditions' => $condition_array,'order' => "ActivityLog.$sortname $sortorder", 'page' => $page  , 'limit'=> $limit, 'recursive' => -1));
                    $data_table_count = $this->ActivityLog->find('count', array('conditions' => $condition_array, 'recursive' => -1));

                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {
                            $return_arr[] = array(
                                'id' => $obj['ActivityLog']['id'],
                                'cell' => array(
                                    //$this->covertDate($obj['ActivityLog']['created'],'mysql_flip'),
                                    $obj['ActivityLog']['created'],
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



    function truck($type = 'get')
    {
        //$company_profile = $this->global_company;
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


                    $data_table = $this->Truck->find('all', array('order' => "Truck.$sortname $sortorder", 'page' => $page  , 'limit'=> $limit, 'recursive' => 1));
                    $data_table_count = $this->Truck->find('count', array('recursive' => -1));

                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {
                            $return_arr[] = array(
                                'id' => $obj['Truck']['id'],
                                'cell' => array(
                                    $obj['Truck']['id'],
                                    $obj['Truck']['truck_no'],
                                    $obj['Truck']['capacity'],
                                    $obj['Truck']['name'],
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



                    $data = array('Truck' => $_POST);

                    if($_POST['id'] == 0){
                        $data['Truck']['created_by'] = $authUser['id'];
                    }
                    else{
                        $data['Truck']['modified_by'] = $authUser['id'];
                    }

                    if ($this->Truck->save($this->sanitize($data))) {
                        if($_POST['id'] > 0){
                            return json_encode(array('code' => 0, 'msg' => 'Data Updated!'));
                        }
                        else{
                            return json_encode(array('code' => 0, 'msg' => 'Data Saved!', 'id'=>$this->Truck->id));
                        }
                    } else {
                        return json_encode(array('code' => 1, 'msg' => 'Some errors occurred.'));
                    }
                    break;

                case 'delete':
                    $ids = $_POST['ids'];
                    $modObj = ClassRegistry::init('Truck');
                    $result = $modObj->deleteAll(array('Truck.id' => $ids),false);
                    if ($result) {
                        echo json_encode(array('code' => 0, 'msg' => 'Data Deleted!'));
                    } else {
                        echo json_encode(array('code' => 1, 'msg' => 'Data cannot be deleted'));
                    }
                    break;
            }
        }

    }


    function credit_customer_setting($type = 'get')
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

                    $condition_array = array(
                        'CustomerCreditSetting.deleted' => 'n'
                    );

                    $contain = array(
                        'OmcCustomer'=>array('fields' => array('OmcCustomer.id', 'OmcCustomer.name'))
                    );


                    $data_table = $this->CustomerCreditSetting->find('all', array('conditions' => $condition_array, 'contain'=>$contain,'order' => "CustomerCreditSetting.$sortname $sortorder", 'page' => $page  , 'limit'=> $limit, 'recursive' => 1));
                    $data_table_count = $this->CustomerCreditSetting->find('count', array('conditions' => $condition_array, 'recursive' => -1));
                    $total_records = $data_table_count;


                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {
                            $return_arr[] = array(
                                'id' => $obj['CustomerCreditSetting']['id'],
                                'cell' => array(
                                    $obj['CustomerCreditSetting']['id'],
                                    $obj['CustomerCreditSetting']['name'],
                                    $obj['CustomerCreditSetting']['business_type'],
                                    $obj['OmcCustomer']['name'],
                                    $obj['CustomerCreditSetting']['territory'],
                                    $obj['CustomerCreditSetting']['credit_limit'],
                                    $obj['CustomerCreditSetting']['credit_days'],
                                    $obj['CustomerCreditSetting']['agreement_sign'],
                                    $obj['CustomerCreditSetting']['risk_rating']
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



                    $data = array('CustomerCreditSetting' => $_POST);

                    if($_POST['id'] == 0){
                        $data['CustomerCreditSetting']['created_by'] = $authUser['id'];
                    }
                    else{
                        $data['CustomerCreditSetting']['modified_by'] = $authUser['id'];
                    }

                    if ($this->CustomerCreditSetting->save($this->sanitize($data))) {
                        if($_POST['id'] > 0){
                            return json_encode(array('code' => 0, 'msg' => 'Data Updated!'));
                        }
                        else{
                            return json_encode(array('code' => 0, 'msg' => 'Data Saved!', 'id'=>$this->CustomerCreditSetting->id));
                        }
                    } else {
                        return json_encode(array('code' => 1, 'msg' => 'Some errors occurred.'));
                    }
                    break;

                    case 'delete':
                        $ids = $_POST['ids'];
                        $modObj = ClassRegistry::init('CustomerCreditSetting');
                        $result = $modObj->updateAll(
                            array('CustomerCreditSetting.deleted' => "'y'"),
                            array('CustomerCreditSetting.id' => $ids)
                        );
                        if ($result) {
                            $modObj = ClassRegistry::init('CustomerCreditSetting');
                            $modObj->updateAll(
                                array('CustomerCreditSetting.deleted' => "'y'"),
                                array('CustomerCreditSetting.id' => $ids)
                            );

                         echo json_encode(array('code' => 0, 'msg' => 'Data Deleted!'));
                        } else {
                            echo json_encode(array('code' => 1, 'msg' => 'Data cannot be deleted'));
                        }
                        break;
            }
        }


        $station_list = $this->get_customer_list();
        $yes_no = array('0'=>array('id'=>'Yes','name'=>'Yes'),'1'=>array('id'=>'No','name'=>'No'));
        $risk_rate = array('0'=>array('id'=>'Low','name'=>'Low'),'1'=>array('id'=>'Medium','name'=>'Medium'),'2'=>array('id'=>'High','name'=>'High'));

        $this->set(compact('yes_no','station_list','risk_rate'));

    }


    function nct_channel_setup($type = 'get')
    {
        //$company_profile = $this->global_company;
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
                        'Nct.deleted' => 'n'
                    );


                    $data_table = $this->Nct->find('all', array('conditions' => $condition_array,'order' => "Nct.$sortname $sortorder", 'page' => $page  , 'limit'=> $limit, 'recursive' => 1));
                    $data_table_count = $this->Nct->find('count', array('recursive' => -1));

                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {
                            $return_arr[] = array(
                                'id' => $obj['Nct']['id'],
                                'cell' => array(
                                    $obj['Nct']['id'],
                                    $obj['Nct']['name'],
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

                    $data = array('Nct' => $_POST);

                    if($_POST['id'] == 0){
                        $data['Nct']['created_by'] = $authUser['id'];
                    }
                    else{
                        $data['Nct']['modified_by'] = $authUser['id'];
                    }

                    $data['Nct']['omc_id'] = $company_profile['id'];

                    if ($this->Nct->save($this->sanitize($data))) {
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
                    $modObj = ClassRegistry::init('Nct');
                    $result = $modObj->updateAll(
                        array('Nct.deleted' => "'y'"),
                        array('Nct.id' => $ids)
                    );
                    if ($result) {
                        $modObj = ClassRegistry::init('Nct');
                        $modObj->updateAll(
                            array('Nct.deleted' => "'y'"),
                            array('Nct.id' => $ids)
                        );

                     echo json_encode(array('code' => 0, 'msg' => 'Data Deleted!'));
                    } else {
                        echo json_encode(array('code' => 1, 'msg' => 'Data cannot be deleted'));
                    }
                    break;
            }
        }

    }


    function lube_sales_setting($type = 'get')
    {
        //$company_profile = $this->global_company;
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
                        'LubeSetting.deleted' => 'n'
                    );

                    $contain = array(
                        'OmcCustomer'=>array('fields' => array('OmcCustomer.id', 'OmcCustomer.name'))
                    );

                    $data_table = $this->LubeSetting->find('all', array('conditions' => $condition_array, 'contain'=>$contain,'order' => "LubeSetting.$sortname $sortorder", 'page' => $page  , 'limit'=> $limit, 'recursive' => 1));
                    $data_table_count = $this->LubeSetting->find('count', array('conditions' => $condition_array, 'recursive' => -1));
                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {
                            $return_arr[] = array(
                                'id' => $obj['LubeSetting']['id'],
                                'cell' => array(
                                    $obj['LubeSetting']['id'],
                                    $obj['LubeSetting']['station_category'],
                                    $obj['LubeSetting']['name'],
                                    $obj['LubeSetting']['unit_volume'],
                                    $obj['LubeSetting']['total_qty_per_pack'],
                                    $obj['LubeSetting']['pack_volume'],
                                    $obj['LubeSetting']['unit_cost_price'],
                                    $obj['LubeSetting']['unit_selling_price'],
                                    $obj['LubeSetting']['price_per_ltr']
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

                    $data = array('LubeSetting' => $_POST);

                    if($_POST['id'] == 0){
                        $data['LubeSetting']['created_by'] = $authUser['id'];
                    }
                    else{
                        $data['LubeSetting']['modified_by'] = $authUser['id'];
                    }

                    $data['LubeSetting']['omc_customer_id'] = $company_profile['id'];

                    if ($this->LubeSetting->save($this->sanitize($data))) {
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
                    $modObj = ClassRegistry::init('LubeSetting');
                    $result = $modObj->updateAll(
                        array('LubeSetting.deleted' => "'y'"),
                        array('LubeSetting.id' => $ids)
                    );
                    if ($result) {
                        $modObj = ClassRegistry::init('LubeSetting');
                        $modObj->updateAll(
                            array('LubeSetting.deleted' => "'y'"),
                            array('LubeSetting.id' => $ids)
                        );

                     echo json_encode(array('code' => 0, 'msg' => 'Data Deleted!'));
                    } else {
                        echo json_encode(array('code' => 1, 'msg' => 'Data cannot be deleted'));
                    }
                    break;
            }
        }

    }



    function lpg_sales_setting($type = 'get')
    {
        //$company_profile = $this->global_company;
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
                        'LpgSetting.deleted' => 'n'
                    );


                    $contain = array(
                        'OmcCustomer'=>array('fields' => array('OmcCustomer.id', 'OmcCustomer.name'))
                    );

                    $data_table = $this->LpgSetting->find('all', array('conditions' => $condition_array, 'contain'=>$contain,'order' => "LpgSetting.$sortname $sortorder", 'page' => $page  , 'limit'=> $limit, 'recursive' => 1));
                    $data_table_count = $this->LpgSetting->find('count', array('conditions' => $condition_array, 'recursive' => -1));
                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {
                            $return_arr[] = array(
                                'id' => $obj['LpgSetting']['id'],
                                'cell' => array(
                                    $obj['LpgSetting']['id'],
                                    $obj['LpgSetting']['name'],
                                    $obj['LpgSetting']['unit_volume'],
                                    $obj['LpgSetting']['unit_price'],
                                    $obj['LpgSetting']['price_per_kg']
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

                    $data = array('LpgSetting' => $_POST);

                    if($_POST['id'] == 0){
                        $data['LpgSetting']['created_by'] = $authUser['id'];
                    }
                    else{
                        $data['LpgSetting']['modified_by'] = $authUser['id'];
                    }

                    $data['LpgSetting']['omc_customer_id'] = $company_profile['id'];

                    if ($this->LpgSetting->save($this->sanitize($data))) {
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
                    $modObj = ClassRegistry::init('LpgSetting');
                    $result = $modObj->updateAll(
                        array('LpgSetting.deleted' => "'y'"),
                        array('LpgSetting.id' => $ids)
                    );
                    if ($result) {
                        $modObj = ClassRegistry::init('LpgSetting');
                        $modObj->updateAll(
                            array('LpgSetting.deleted' => "'y'"),
                            array('LpgSetting.id' => $ids)
                        );

                     echo json_encode(array('code' => 0, 'msg' => 'Data Deleted!'));
                    } else {
                        echo json_encode(array('code' => 1, 'msg' => 'Data cannot be deleted'));
                    }
                    break;
            }
        }

    }


    function additive_setup($type = 'get')
    {
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
                        'AdditiveSetup.deleted' => 'n'
                    );

                    $contain = array(
                        'ProductType'=>array('fields' => array('ProductType.id', 'ProductType.name'))
                    );

                    $data_table = $this->AdditiveSetup->find('all', array('conditions' => $condition_array, 'contain'=>$contain,'order' => "AdditiveSetup.$sortname $sortorder", 'page' => $page  , 'limit'=> $limit, 'recursive' => 1));
                    $data_table_count = $this->AdditiveSetup->find('count', array('recursive' => -1));

                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {
                            $return_arr[] = array(
                                'id' => $obj['AdditiveSetup']['id'],
                                'cell' => array(
                                    $obj['AdditiveSetup']['id'],
                                    $obj['AdditiveSetup']['name'],
                                    $obj['ProductType']['name']
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

                    $data = array('AdditiveSetup' => $_POST);

                    if($_POST['id'] == 0){
                        $data['AdditiveSetup']['created_by'] = $authUser['id'];
                    }
                    else{
                        $data['AdditiveSetup']['modified_by'] = $authUser['id'];
                    }

                    $data['AdditiveSetup']['omc_id'] = $company_profile['id'];

                    if ($this->AdditiveSetup->save($this->sanitize($data))) {
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
                    $modObj = ClassRegistry::init('AdditiveSetup');
                    $result = $modObj->updateAll(
                        array('AdditiveSetup.deleted' => "'y'"),
                        array('AdditiveSetup.id' => $ids)
                    );
                    if ($result) {
                        $modObj = ClassRegistry::init('AdditiveSetup');
                        $modObj->updateAll(
                            array('AdditiveSetup.deleted' => "'y'"),
                            array('AdditiveSetup.id' => $ids)
                        );

                     echo json_encode(array('code' => 0, 'msg' => 'Data Deleted!'));
                    } else {
                        echo json_encode(array('code' => 1, 'msg' => 'Data cannot be deleted'));
                    }
                    break;
            }
        }

        $products_lists = $this->get_products();

        $this->set(compact('products_lists'));

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
                    $filter_customer =   isset($_POST['filter_customer']) ? $_POST['filter_customer'] : 0 ;
                    /** Search column */
                    $search_query = isset($_POST['query']) ? $_POST['query'] : '';
                    /** Search string */
                    $rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
                    $limit = $rp;
                    $start = ($page - 1) * $rp;

                    $condition_array = array('OmcCustomerPriceChange.deleted' => 'n');
                    if($filter_customer != 0){
                        $condition_array['OmcCustomerPriceChange.omc_customer_id'] = $filter_customer;
                    }

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
                        'ProductType'=>array('fields' => array('ProductType.id', 'ProductType.name')),
                        'OmcCustomer'=>array('fields' => array('OmcCustomer.id', 'OmcCustomer.name'))
                    );

                    $data_table = $this->OmcCustomerPriceChange->find('all', array('conditions' => $condition_array, 'contain'=>$contain,'order' => "OmcCustomerPriceChange.$sortname $sortorder", 'offset'=>$start,'limit' => $limit, 'recursive' => 1));
                    $data_table_count = $this->OmcCustomerPriceChange->find('count', array('conditions' => $condition_array, 'recursive' => -1));

                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {
                            $return_arr[] = array(
                                'id' => $obj['OmcCustomerPriceChange']['id'],
                                'cell' => array(
                                    $obj['OmcCustomerPriceChange']['id'],
                                    $obj['OmcCustomer']['name'],
                                    $obj['ProductType']['name'],
                                    $obj['OmcCustomerPriceChange']['description'],
                                    $obj['OmcCustomerPriceChange']['price'],
                                    $obj['OmcCustomerPriceChange']['unit']
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
                    $data = array('OmcCustomerPriceChange' => $_POST);
                    $data['OmcCustomerPriceChange']['modified_by'] = $this->Auth->user('id');
                    if ($_POST['id'] == 0) {
                        $data['OmcCustomerPriceChange']['created_by'] = $this->Auth->user('id');
                        $data['OmcCustomerPriceChange']['omc_customer_id'] =  $_POST['omc_customer_id'];
                    }

                    if ($this->OmcCustomerPriceChange->save($this->sanitize($data))) {
                        if($_POST['id'] > 0){
                            $new_user = $_POST['id'];
                            $log_description = $this->getLogMessage('ModifyOmcCustomerPriceChange')." (Price Change ID: ".$new_user.")";
                            $this->logActivity('Administration',$log_description);

                            return json_encode(array('code' => 0, 'msg' => 'Data Saved'));
                        }
                        else{ //If new pass back extra data if needed.
                            $new_user = $this->OmcCustomerPriceChange->id;
                            $log_description = $this->getLogMessage('CreateOmcCustomerPriceChange')." (Price Change ID: ".$new_user.")";
                            $this->logActivity('Administration',$log_description);

                            return json_encode(array('code' => 0, 'msg' => 'Data Saved.', 'id'=>$this->OmcCustomerPriceChange->id));
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
                    $modObj = ClassRegistry::init('OmcCustomerPriceChange');
                    $now = "'".date('Y-m-d H:i:s')."'";
                    $result = $modObj->updateAll(
                        $this->sanitize(array('OmcCustomerPriceChange.deleted' => "'y'",'OmcCustomerPriceChange.modified' => "$now",'OmcCustomerPriceChange.modified_by' => $this->Auth->user('id'))),
                        $this->sanitize(array('OmcCustomerPriceChange.id' => $ids))
                    );
                    if ($result) {
                        echo json_encode(array('code' => 0, 'msg' => 'Data Deleted!'));
                    } else {
                        echo json_encode(array('code' => 1, 'msg' => 'Data cannot be deleted'));
                    }
                    break;
            }
        }

        $omc_customers_data = $this->get_customer_list();
        $omc_customers_data = $this->get_customer_list();
        $omc_customers_lists =array(array('name'=>'All','value'=>0));
        foreach($omc_customers_data as $arr){
            $omc_customers_lists[] = array('name'=>$arr['name'],'value'=>$arr['id']);
        }
        $product_list = $this->get_products();
        // $product_list_data = $this->get_product_list();
        // debug($product_list_data);
        //$product_group_list = array('all'=>'All Product Type');
        /* $product_list = array();
         foreach($product_list_data as $arr){
             $product_list[] = array('name'=>$arr['name'],'id'=>$arr['id']);
         }*/
        // debug($product_list);
        $controller = $this;
        $this->set(compact('controller', 'product_list','omc_customers_lists','omc_customers_data'));
    }

    function export_price_change(){
        $download = false;
        $company_profile = $this->global_company;

        $export_data = $this->OmcCustomerPriceChange->find('all', array(
            'conditions' => array('OmcCustomerPriceChange.deleted' => 'n'),
            'contain'=>array(
                'ProductType'=>array('fields'=>array('ProductType.id','ProductType.name'))
            ),
            'order'=>array('OmcCustomerPriceChange.id'=>'desc'),
            'recursive' => 1
        ));

        //debug($export_data);
        if ($export_data) {
            $download = true;
            $list_data = array();
            foreach ($export_data as $obj) {
                $list_data[] = array(
                    $obj['ProductType']['name'],
                    $obj['OmcCustomerPriceChange']['price']
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
