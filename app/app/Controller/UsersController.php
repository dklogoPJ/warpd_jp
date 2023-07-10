<?php
// app/Controller/UsersController.php
class UsersController extends AppController
{

    var $uses = array(
        'User', 'Package', 'PackagesModule', 'Bdc', 'Omc','Org','Depot','Cep','OmcUserBdc','OmcCustomer','LoginTrail','FailedLogin'
    );

    # Set the layout to use
    var $layout = 'login_layout';

    public function beforeFilter($param_array = null)
    {
        parent::beforeFilter($validate_access_control = false);
        $this->Auth->allow('login', 'createLoginSession','__find_company','logout', 'add', 'signup', 'signup_company', 'signup_administrator',
            'signup_pricing_plans', 'signup_review', 'do_signup', 'signup_complete',
            'signup_cancel'
        );
    }

    public function index()
    {
        //$this->autoRender = false;
        //$this->autoLayout = false;
    }

    public function login($locator_number = null)
    {
        if($this->Auth->user()){
            $this->redirect(array('controller' => 'Dashboard', 'action' => 'index'));
        }

        if ($this->request->is('post')) {
            //$this->autoRender = false;
            //$this->autoLayout = false;
            $success = true;
            $attempt = $this->FailedLogin->validateLoginAttempts($this->request->data['User']['username']);
            if($attempt){
                $success = false;
                $this->Session->setFlash(__('Your account has been disabled, please contact your administrator.'));
            }
            else{
                if ($this->Auth->login()) {
                    /** Check if account is disabled*/
                    $active = $this->Auth->user('active');
                    $user_type = $this->Auth->user('user_type');
                    $user_id = $this->Auth->user('id');
                    $login_session = $this->Auth->user('login_session');
                    $login_update_time = $this->Auth->user('login_update_time');
                    if ($active == 'Disabled') {
                        /** User is disable, Logout */
                        $success = false;
                        $this->Auth->logout();
                        $this->Session->setFlash(__('Your Account is Disabled.'));
                    }
                    /*//Handling Multiple user logging from the same account
                    if(empty($login_session)){//If both are empty, means the user logged out
                        $this->createLoginSession($user_id);
                    }
                    else{
                        //We still have to account for expired cookie  by using the time of last login update time
                        $rt = $this->timeIsUp($login_update_time);
                        if($rt['status']){
                            $this->createLoginSession($user_id);
                        }
                        else{
                            $success = false;
                            $this->Auth->logout();
                            //$this->Session->setFlash(__('Sorry, it seems you did not logout properly on your last login or the account is already being used. Please try again after '.$rt['min'].' mins.'));
                            $this->Session->setFlash(__('Sorry, it seems you did not logout properly on your last login or the account is already being used. Please try again after later'));
                        }
                    }*/

                    if ($success) {
                        $cp = null;
                        $cmp_id = 0;
                        if ($user_type == 'bdc') {
                            $us = $this->Auth->user();
                            $company_profile = $this->Bdc->getBdcById($us['bdc_id']);
                            if(empty($company_profile)){
                                $success = false;
                                $this->Auth->logout();
                                $this->Session->setFlash(__('Invalid Login Credentials Comp Not Found'));
                            }
                            else{
                                $cmp_id = $us['bdc_id'];
                                $cp = $company_profile['Bdc'];
                            }
                        } elseif ($user_type == 'omc') {
                            $us = $this->Auth->user();
                            $company_profile = $this->Omc->getOmcById($us['omc_id']);
                            if(empty($company_profile)){
                                $success = false;
                                $this->Auth->logout();
                                $this->Session->setFlash(__('Invalid Login Credentials Comp Not Found'));
                            }
                            else{
                                $cmp_id = $us['omc_id'];
                                $cp = $company_profile['Omc'];
                            }

                        }
                        elseif ($user_type == 'omc_customer') {
                            $us = $this->Auth->user();
                            $company_profile = $this->OmcCustomer->getCustomerById($us['omc_customer_id']);
                            if(empty($company_profile)){
                                $success = false;
                                $this->Auth->logout();
                                $this->Session->setFlash(__('Invalid Login Credentials Comp Not Found'));
                            }
                            else{
                                $cp = $company_profile['OmcCustomer'];
                                //$cmp_id = $us['omc_customer_id'];
                                $cmp_id = $cp['omc_id'];
                            }

                        }
                        elseif ($user_type == 'org') {
                            $us = $this->Auth->user();
                            $company_profile = $this->Org->getOrgById($us['org_id']);
                            if(empty($company_profile)){
                                $success = false;
                                $this->Auth->logout();
                                $this->Session->setFlash(__('Invalid Login Credentials Comp Not Found'));
                            }
                            else{
                                $cmp_id = $us['org_id'];
                                $cp = $company_profile['Org'];
                            }
                        }
                        elseif ($user_type == 'ceps_depot' || $user_type == 'ceps_central') {
                            $us = $this->Auth->user();
                            $company_profile = $this->Cep->getCepById($us['cep_id']);
                            if(empty($company_profile)){
                                $success = false;
                                $this->Auth->logout();
                                $this->Session->setFlash(__('Invalid Login Credentials Comp Not Found'));
                            }
                            else{
                                $cmp_id = $us['cep_id'];
                                $cp = $company_profile['Cep'];
                            }
                        }
                        elseif ($user_type == 'depot') {
                            $us = $this->Auth->user();
                            $company_profile = $this->Cep->getDepotById($us['depot_id']);
                            if(empty($company_profile)){
                                $success = false;
                                $this->Auth->logout();
                                $this->Session->setFlash(__('Invalid Login Credentials Comp Not Found'));
                            }
                            else{
                                $cmp_id = $us['depot_id'];
                                $cp = $company_profile['Depot'];
                            }
                        }
                        else{
                            $success = false;
                            $this->Auth->logout();
                            $this->Session->setFlash(__('Invalid Login Credentials'));
                        }

                        if($success){
                            //Check if the right user is at the right login page
                            if( $this->Session->check('locator_number')){
                                $comp = $this->__find_company($this->Session->read('locator_number'));
                                if($comp){
                                    if($comp['cmp']['id'] == $cmp_id){

                                    }
                                    else{
                                        $success = false;
                                        $this->Auth->logout();
                                        $this->Session->setFlash(__('Your Account cannot be used to login at this portal'));
                                    }
                                }
                            }
                        }

                    }
                }
                else {
                    //Log login attempt
                    $this->FailedLogin->save($this->sanitize(array(
                        'username'=>$this->request->data['User']['username'],
                        'ip_addr'=> $this->request->clientIp()
                    )));

                    $success = false;
                    $this->Session->setFlash(__('Invalid login Credentials'));
                }
            }



            if ($success) { //Login Successful

                //Activity Log
                $log_description = $this->getLogMessage('Login');
                $this->logActivity('Login',$log_description);
                //Start to store the user id in the cookie for 1 day, let set cookie higher so we have less issue of users
                $save_trail = array(
                    'user_id'=>$this->Auth->user('id'),
                    'ip_addr'=> $this->request->clientIp()
                );
                $this->LoginTrail->save($this->sanitize($save_trail));
                $usr = $this->Auth->user();
                $usr['trail_id']= $this->LoginTrail->id;
                $this->Auth->login($usr);

                $this->Cookie->write('c_user_id',  $this->Auth->user('id'),false,3600 * 24);
                $this->redirect(array('controller' => 'Router', 'action' => 'index'));
            }
        }

        //May want to get the company info here
        $use_default = true;
        $title_for_layout = 'WARP-D';
        if($locator_number){
            $comp = $this->__find_company($locator_number);
            if($comp){
                $company_profile = $comp['cmp'];
                $company_key = $comp['key'];
                $use_default = false;
                $this->layout = "login2_layout";
                $title_for_layout = $company_profile['name'];
            }
            $this->Session->write('locator_number',$locator_number);
        }
        else{
            $this->Session->delete('locator_number');
        }

        //debug($this->dateDiffInMin('2013-04-10 10:30:00',date('Y-m-d H:i:s')));
        $this->set(compact('title_for_layout', 'company_profile','company_key','use_default'));
    }


    public function logout($type = 'manual')
    {
        $user_type = $this->Auth->user('user_type');
        $user_id = $this->Auth->user('id');
        $trail_id = $this->Auth->user('trail_id');
        $locator_number = '';
        if ($user_type == 'bdc') {
            $us = $this->Auth->user();
            $company_profile = $this->Bdc->getBdcById($us['bdc_id']);
            if($company_profile){
                $locator_number = $company_profile['Bdc']['locator_number'];
            }
        } elseif ($user_type == 'omc') {
            $us = $this->Auth->user();
            $company_profile = $this->Omc->getOmcById($us['omc_id']);
            if($company_profile){
                $locator_number = $company_profile['Omc']['locator_number'];
            }
        }
        elseif ($user_type == 'omc_customer') {
            $us = $this->Auth->user();
            //$us = $this->OmcUser->getUserById($user_id, -1);
            $comp = $this->OmcCustomer->getCustomerById($us['omc_customer_id']);
            if($comp){
                $company_profile = $this->Omc->getOmcById($comp['OmcCustomer']['omc_id']);
                $locator_number = $company_profile['Omc']['locator_number'];
            }
        }
        elseif ($user_type == 'org') {
            $us = $this->Auth->user();
            $company_profile = $this->Org->getOrgById($us['org_id']);
            if($company_profile){
                $locator_number = $company_profile['Org']['locator_number'];
            }
        }

        //Activity Log
        if($type == 'manual'){
            $log_description = $this->getLogMessage('Logout');
            $this->logActivity('Logout',$log_description);
        }
        else{
            $log_description = $this->getLogMessage('AutoLogout');
            $this->logActivity('Logout',$log_description);
        }


        $path =  '/'.$locator_number;
        $this->clearLoginSession($user_id,$trail_id);
        $this->Session->destroy();
        $this->redirect($this->Auth->logout().$path);
    }

    private function createLoginSession($id) {
        $login_session = $this->randomString(50);
        $data['login_session'] = $login_session;
        $this->User->save($this->sanitize(array(
            'id'=>$id,
            'login_session' =>$login_session,
            'login_update_time'=>"'".date('Y-m-d H:i:s')."'"
        )));
        $this->Session->write('log_session', $login_session);
        return $data;
    }

    private function clearLoginSession($id,$trail_id) {
        $this->User->save($this->sanitize(array(
            'id'=>$id,
            'login_session' =>'',
            //'login_update_time'=>''
        )));

        $this->LoginTrail->save($this->sanitize(array(
            'id'=>$trail_id,
            'logout' =>date('Y-m-d H:i:s'),
            //'login_update_time'=>''
        )));
    }

    public function __find_company($locator)
    {
        $company = false;
        $loc = trim($locator);
        $entities = array('Bdc','Omc','Org','Cep','Depot');
        foreach($entities as $ent){
            $company_data = $this->$ent->find('first',array(
                'conditions'=>array($ent.'.locator_number'=>$loc),
                'recursive'=>-1
            ));
            if($company_data){
                $company['cmp'] = $company_data[$ent];
                $company['key'] = $loc;
                break;
            }
        }

       /* $company_data = $this->Bdc->find('first',array( //Try BDC
            'conditions'=>array('Bdc.locator_number'=>$loc),
            'recursive'=>1
        ));
        if($company_data){
            $company['cmp'] = $company_data['Bdc'];
            $company['key'] = $loc;
        }
        else{// Try Omc
            $company_data = $this->Omc->find('first',array(
                'conditions'=>array('Omc.locator_number'=>$loc),
                'recursive'=>1
            ));
            if($company_data){
                $company['cmp'] = $company_data['Omc'];
                $company['key'] =  $loc;
            }
            else{// Try Other Orgs
                $company_data = $this->Org->find('first',array(
                    'conditions'=>array('Org.locator_number'=>$loc),
                    'recursive'=>1
                ));
                if($company_data){
                    $company['cmp'] = $company_data['Org'];
                    $company['key'] =  $loc;
                }

            }
        }*/
        /*if($key == 'wpd-m'){//Omc
            $key_id = substr($locator, 0, 6);
            $id = substr($key_id,-1);

        }
        elseif($key == 'wpd-d'){//Bdc
            $key_id = substr($locator, 0, 6);
            $id = substr($key_id,-1);

        }*/

        return $company;
    }


    function timeIsUp($smaller_date){
        $old_datetime_arr = explode(' ',$smaller_date);
        $old_date = $old_datetime_arr[0];
        $old_time = $old_datetime_arr[1];
        $old_time_arr = explode(':',$old_time);
        $old_hour = $old_time_arr[0];
        $old_min = $old_time_arr[1];

        $now_data = date('Y-m-d');
        $now_hour = date('H');
        $now_min = date('i');

        $rt = array('status'=>true,'min'=>0);

        if(strtotime($now_data) > strtotime($old_date)){
            //It's been more than 30 min hesnce allow login
            return $rt;
        }

        if(strtotime($now_data) == strtotime($old_date)){
            if($now_hour > $old_hour){
                //Again more than an hour
                return $rt;
            }
            elseif($now_hour == $old_hour){
                $min = $now_min - $old_min;
                if($min >= 30 ){
                    return $rt;
                }
                else{
                    return array('status'=>false,'min'=> 30 - $min);
                }
            }
        }
    }


    /**
     * This function is for making some changes to the user profile
     * @name profile
     * @param void
     * @return void
     * @access public
     */
    public function profile()
    {
        if ($this->request->is('post')) {

            $this->User->id = $this->Auth->user('id'); // on edit
            $entities = $this->getUserTypesEntities();
            $entity = $entities[$this->Auth->user('user_type')];
            $user_save = array('User' => $this->request->data['User']);

            if (!empty($this->request->data['User']['password'])) {
                $user_save['User']['password'] = AuthComponent::password($this->request->data['User']['password']);
            } else {
                unset($user_save['User']['password']);
            }

            $res = $this->User->save($this->sanitize($user_save['User']));

            if ($res) {

                $user = $this->User->find('first', array(
                    'conditions' => array('User.id' => $this->Auth->user('id')),
                    'contain'=>array('Group',$entity),
                    'recursive' => 1
                ));
                unset($user['User']['password']);
                $user_ses = $user['User'];
                $user_ses['Group'] = $user['Group'];
                $user_ses[$entity] = $user[$entity];
                $this->Auth->login($user_ses);
                $this->Session->setFlash('Profile info has been updated !');
                $this->Session->write('process_error', 'no');

                //Activity Log
                $log_description = $this->getLogMessage('ProfileUpdate');
                $this->logActivity('Profile Update',$log_description);
            }
            else {
                $this->Session->setFlash('Sorry, Profile info could not be updated');
                $this->Session->write('process_error', 'yes');
            }

            $this->redirect(array('action' => 'profile'));
        }

        if ($this->Auth->user('user_type') == 'bdc') {
            $this->layout = 'bdc_layout';
        } elseif ($this->Auth->user('user_type') == 'omc') {
            $this->layout = 'omc_layout';
        } else {
            $this->layout = 'user_layout';
        }

        $company_profile = $this->global_company;
        $company_key = $company_profile['locator_number'];
        if($company_profile['comp_type'] == 'omc_customer'){
            $omc = $this->Omc->find('first',array(
                'fields'=>array('locator_number'),
                'conditions'=>array('id'=>$company_profile['omc_id']),
                'recursive'=>-1
            ));
            $company_key = $omc['Omc']['locator_number'];
        }

        $page_title = 'Profile';
        $controller = $this;
        $this->set(compact('page_title', 'controller','company_key'));
    }

    /**
     * This function redirects the user to his appropriate page after login
     * @name signIn
     * @param void
     * @return void
     * @access public
     */
    function action_after_login()
    {
        $this->redirect(array('controller' => 'Dashboard', 'action' => 'index'));
    }

    public function signup_company()
    {
        if ($this->request->is('post')) {
            if ($this->Session->check('signup')) {
                $signup = $this->Session->read('signup');
                $signup['Company'] = $this->request->data;
                $this->Session->write('signup', $signup);
            } else {
                $signup['Company'] = $this->request->data;
                $this->Session->write('signup', $signup);
            }
            $this->Session->write('step1_done', 'complete');
            $this->redirect('signup_administrator');
        }
        $this->layout = 'signup_layout';
        $title_for_layout = 'Company Info';
        $this->set(compact('title_for_layout'));
    }

    public function signup_administrator()
    {
        if (!$this->Session->check('step1_done')) {
            $this->redirect('signup_company');
        }

        if ($this->request->is('post')) {
            if ($this->Session->check('signup')) {
                $signup = $this->Session->read('signup');
                $signup['User'] = $this->request->data;
                $this->Session->write('signup', $signup);
            } else {
                $signup['User'] = $this->request->data;
                $this->Session->write('signup', $signup);
            }
            $this->Session->write('step2_done', 'complete');
            $this->redirect('signup_pricing_plans');
        }

        $controller = $this;
        $this->layout = 'signup_layout';
        $title_for_layout = 'Create Admin Account';
        $this->set(compact('title_for_layout', 'controller'));
    }

    public function signup_pricing_plans()
    {
        if (!$this->Session->check('step2_done')) {
            $this->redirect('signup_administrator');
        }

        if ($this->request->is('post')) {
            if ($this->Session->check('signup')) {
                $signup = $this->Session->read('signup');
                $signup['Modules'] = $this->request->data;
                $this->Session->write('signup', $signup);
            } else {
                $signup['Modules'] = $this->request->data;
                $this->Session->write('signup', $signup);
            }
            $this->Session->write('step3_done', 'complete');
            $this->redirect('signup_review');
        }

        $packages = $this->Package->find('all', array(
            'conditions' => array('Package.deleted' => 'n'),
            'recursive' => -1
        ));

        $this->layout = 'signup_layout';
        $title_for_layout = 'Select Packages';
        $this->set(compact('title_for_layout', 'packages'));
    }

    public function signup_review()
    {
        if (!$this->Session->check('step3_done')) {
            $this->redirect('signup_pricing_plans');
        }

        $ids = array();
        $signup = $this->Session->read('signup');
        $modules = $signup['Modules'];
        foreach ($modules as $key => $value) {
            if (intval($value) > 0) {
                $ids[] = $value;
            }
        }
        $packages = $this->Package->find('all', array(
            'conditions' => array('Package.id' => $ids, 'Package.deleted' => 'n'),
            'recursive' => -1
        ));
        $controller = $this;
        $this->layout = 'signup_layout';
        $title_for_layout = 'Review';
        $this->set(compact('title_for_layout', 'packages', 'controller'));
    }

    public function do_signup()
    {
        $this->autoRender = false;
        $this->autoLayout = false;
        $signup = $this->Session->read('signup');
        //Save the admin account data
        $user = array();
        $user['User'] = $signup['User'];
        //Check if username exist
        $user_exist = $this->User->find('first', array(
            'conditions' => array('User.username' => $user['User']['username']),
            'recursive' => -1
        ));

        if ($user_exist) {
            $this->Session->write('flash_msg', array('msg' => 'The username already exist.'));
            $this->redirect('signup_administrator');
        }

        //Get the module_id's for the selected package
        $package_id = $signup['Modules']['package_id'];
        $modules = $this->PackagesModule->find('all', array(
            'conditions' => array('PackagesModule.package_id' => $package_id),
            'recursive' => -1
        ));

        if ($this->User->save($this->sanitize($user))) {
            $user_id = $this->User->id;
            //save the company data
            $company = array();
            $company['Bdc'] = $signup['Company'];
            $company['BdcUser'][0] = array(
                'user_id' => $user_id
            );
            $this->Bdc->saveAll($this->sanitize($company));
            $company_id = $this->Bdc->id;
            $company_modules = array();
            $count = 0;
            foreach ($modules as $value) {
                $mod_id = $value['PackagesModule']['module_id'];
                $company_modules['BdcModule'][$count] = array(
                    'bdc_id' => $company_id,
                    'module_id' => $mod_id
                );
                $count++;
            }
            $this->Bdc->BdcModule->saveAll($this->sanitize($company_modules['BdcModule']));

            $this->redirect("signup_complete/" . $this->Bdc->id);
        } else {
            $this->Session->write('flash_msg', array('msg' => 'Data save error, please try again'));
            $this->redirect('signup_review');
        }
    }

    public function signup_complete($id)
    {
        $this->Session->destroy();
        $this->layout = 'signup_layout';
        $title_for_layout = 'Registration Complete';
        $this->set(compact('title_for_layout', 'id'));
    }


    public function signup_cancel()
    {
        $this->Session->destroy();
        $this->redirect(array('controller' => 'pages', 'action' => 'display', 'home'));
    }



    public function welcome() {
        $this->autoLayout = false;
        $this->autoRender = false;
        if ($this->request->is('ajax')) {
            $save = array(
                'User'=>array(
                    'id'=>$this->Auth->user('id'),
                    'shown_welcome'=>'y'
                )
            );
            if ($this->User->save($this->sanitize($save['User']))) {
                /*$user = $this->User->find('first', array(
                    'conditions' => array('User.id' => $this->Auth->user('id')),
                    'recursive' => -1
                ));
                $user = $user['User'];
                $this->Auth->login($user);*/
                return json_encode(array('code' => 0, 'msg' => 'Data Saved!'));
            } else {
                return json_encode(array('code' => 1, 'msg' => 'Some errors occurred.'));
            }
        }
    }

}