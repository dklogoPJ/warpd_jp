<?php

/**
 * @name DashboardController.php
 */
class RouterController extends AppController
{
    # Controller name

    var $name = 'Router';
    # set the model to use
    var $uses = array('User','MenuGroup');

    public function beforeFilter($param_array = null)
    {
        parent::beforeFilter($validate_access_control = false);
    }

    function index()
    {
        $user_type = $this->Auth->user('user_type');
        $company_profile = $this->global_company;
        //Send them to their appropiate dashboards
        if($user_type == 'org'){
            $user_type = $company_profile['comp_type'];
        }
        $con_arr = array('bdc'=>'Bdc','omc'=>'Omc','omc_customer'=>'OmcCustomer','depot'=>'Depot','ceps_central'=>'Ceps','ceps_depot'=>'Ceps','npa'=>'Npa');
        if(isset($con_arr[$user_type])){
            $controller = $con_arr[$user_type];
            $this->redirect(array('controller' => $controller, 'action' => 'dashboard'));
        }
        else{//We don't know you
            $this->redirect(array('controller' => 'Users', 'action' => 'logout'));
        }


        // $group_id = $this->Auth->user('group_id');
        // $data = $this->MenuGroup->getGroupMenus($user_type,$group_id,$company_profile['id']);
        /*if (!empty($data)) {
            foreach($data as $um){
                if(isset($um['sub'])){
                    foreach($um['sub'] as $inner_um){
                        $this->redirect(array('controller' => $inner_um['controller'], 'action' => $inner_um['action']));
                    }
                }
                else{
                    $this->redirect(array('controller' => $um['controller'], 'action' => $um['action']));
                }
            }
        } else { //This will prevent users without purpose
            //$this->redirect(array('controller' => 'Users', 'action' => 'logout'));
        }*/
    }
}