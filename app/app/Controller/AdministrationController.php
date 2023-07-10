<?php

/**
 * This is the administration_controller class file.
 * @author David Klogo<klogodavid@gmail.com>
 * @access public
 * @version 1.0
 */
class AdministrationController extends AppController
{
    # Controller Name to be used

    var $name = 'Administration';

    # Models to be used
    var $uses = array(
        'User', 'Package', 'PackagesModule', 'Bdc', 'BdcUser', 'Omc', 'OmcUser', 'Omclist', 'Region', 'District', 'Depot', 'ProductType'
    );

    # set the layout to use
    var $layout = 'admin_layout';


    /**
     * @name index , this determines the user type and redirect to the appropiate action
     * @return Array of data.
     */
    function index()
    {
        $user_type = $this->Auth->user('user_type');
        if ($user_type == 'system') {
            $this->redirect(array('controller' => 'Administration', 'action' => 'system'));
        } elseif ($user_type == 'bdc') {
            $this->redirect(array('controller' => 'Administration', 'action' => 'bdc'));
        } elseif ($user_type == 'omc') {
            $this->redirect(array('controller' => 'Administration', 'action' => 'omc'));
        }
    }

    /********************** SYSTEMS ADMIN BLOCK ********************/

    /**
     * @name bdc index
     * @return Array of data.
     */
    function system()
    {
        /*$ar = array('User'=>array(
            'title'=>'Mr',
            'fname'=>'Kofi',
            'lname'=>'Otoo',
            'mname'=>'B',
            'username'=>'kofi',
            'user_level'=>'normal_user',
            'email'=>'kofi@admin.com'
        ));

        $rs = $this->User->saveAll($ar);

        debug($rs);*/
    }


    function region($type = null)
    {
        $user_type = $this->Auth->user('user_type');

        switch ($type) {
            case 'get' :
                $this->autoRender = false;
                $this->autoLayout = false;

                $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
                $limit = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
                $sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'id';
                $order = isset($_POST['order']) ? strval($_POST['order']) : 'desc';
                $start = ($page - 1) * $limit;
                $result = array();
                $items = array();

                $condition_array = array('Region.deleted' => 'n');
                /* if (!empty($search_query)) {
                    if ($search_query == 'username') {
                        $condition_array = array(
                            'Region.username' => $search_query,
                            'Region.deleted' => 'n'
                        );
                    }
                    else {
                        $condition_array = array(
                            "Region.$qtype LIKE" => $search_query . '%',
                            'Region.deleted' => 'n'
                        );
                    }
                }*/

                $grid_count = $this->Region->find('count', array('conditions' => $condition_array, 'recursive' => -1));
                $result["total"] = $grid_count;
                $grid_data = $this->Region->find('all', array('conditions' => $condition_array, 'order' => "Region.$sort $order", 'limit' => $start . ',' . $limit, 'recursive' => -1));

                foreach ($grid_data as $data) {
                    $items[] = $data['Region'];
                }
                $result["rows"] = $items;

                echo json_encode($result);

                break;

            case 'save' :
                $this->autoRender = false;
                $this->autoLayout = false;

                $_POST['created_by'] = $this->Auth->user('id');
                $data = array('Region' => $_POST);
                if ($this->Region->save($this->sanitize($data))) {
                    echo json_encode(array('success' => true));
                } else {
                    echo json_encode(array('msg' => 'Sorry errors occured while saving data.'));
                }

                break;

            case 'load':
                $this->autoRender = false;
                $this->autoLayout = false;


                break;

            case 'delete':
                $this->autoRender = false;
                $this->autoLayout = false;


                break;
        }

    }


    function district($type = null)
    {
        $user_type = $this->Auth->user('user_type');

        switch ($type) {
            case 'get' :
                $this->autoRender = false;
                $this->autoLayout = false;

                $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
                $limit = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
                $sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'id';
                $order = isset($_POST['order']) ? strval($_POST['order']) : 'desc';
                $start = ($page - 1) * $limit;
                $result = array();
                $items = array();

                $condition_array = array('District.deleted' => 'n');
                /* if (!empty($search_query)) {
                    if ($search_query == 'username') {
                        $condition_array = array(
                            'Region.username' => $search_query,
                            'Region.deleted' => 'n'
                        );
                    }
                    else {
                        $condition_array = array(
                            "Region.$qtype LIKE" => $search_query . '%',
                            'Region.deleted' => 'n'
                        );
                    }
                }*/

                $grid_count = $this->District->find('count', array('conditions' => $condition_array, 'recursive' => -1));
                $result["total"] = $grid_count;
                $grid_data = $this->District->find('all', array('conditions' => $condition_array, 'order' => "District.$sort $order", 'limit' => $start . ',' . $limit, 'recursive' => -1));

                foreach ($grid_data as $data) {
                    $items[] = $data['District'];
                }
                $result["rows"] = $items;

                echo json_encode($result);

                break;

            case 'save' :
                $this->autoRender = false;
                $this->autoLayout = false;

                $_POST['created_by'] = $this->Auth->user('id');
                $data = array('District' => $_POST);
                if ($this->District->save($this->sanitize($data))) {
                    echo json_encode(array('success' => true));
                } else {
                    echo json_encode(array('msg' => 'Sorry errors occured while saving data.'));
                }

                break;

            case 'load':
                $this->autoRender = false;
                $this->autoLayout = false;


                break;

            case 'delete':
                $this->autoRender = false;
                $this->autoLayout = false;


                break;
        }

        $regions_data = $this->Region->find('all', array('conditions' => array('Region.deleted' => 'n'), 'recursive' => -1));
        $regions = array();
        foreach ($regions_data as $reg) {
            $regions[] = array('id' => $reg['Region']['id'], 'name' => $reg['Region']['name']);
        }
        //debug($regions);

        $this->set(compact('title_for_layout', 'regions'));

    }


    function omclist($type = null)
    {
        $user_type = $this->Auth->user('user_type');

        switch ($type) {
            case 'get' :
                $this->autoRender = false;
                $this->autoLayout = false;

                $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
                $limit = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
                $sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'id';
                $order = isset($_POST['order']) ? strval($_POST['order']) : 'desc';
                $start = ($page - 1) * $limit;
                $result = array();
                $items = array();

                $condition_array = array('Omclist.deleted' => 'n');
                /* if (!empty($search_query)) {
                    if ($search_query == 'username') {
                        $condition_array = array(
                            'Region.username' => $search_query,
                            'Region.deleted' => 'n'
                        );
                    }
                    else {
                        $condition_array = array(
                            "Region.$qtype LIKE" => $search_query . '%',
                            'Region.deleted' => 'n'
                        );
                    }
                }*/

                $grid_count = $this->Omclist->find('count', array('conditions' => $condition_array, 'recursive' => -1));
                $result["total"] = $grid_count;
                $grid_data = $this->Omclist->find('all', array('conditions' => $condition_array, 'order' => "Omclist.$sort $order", 'limit' => $start . ',' . $limit, 'recursive' => -1));

                foreach ($grid_data as $data) {
                    $items[] = $data['Omclist'];
                }
                $result["rows"] = $items;

                echo json_encode($result);

                break;

            case 'save' :
                $this->autoRender = false;
                $this->autoLayout = false;

                $_POST['created_by'] = $this->Auth->user('id');
                $data = array('Omclist' => $_POST);
                if ($this->Omclist->save($this->sanitize($data))) {
                    echo json_encode(array('success' => true));
                } else {
                    echo json_encode(array('msg' => 'Sorry errors occured while saving data.'));
                }

                break;

            case 'delete':
                $this->autoRender = false;
                $this->autoLayout = false;


                break;
        }

        //$this->set(compact('title_for_layout','regions'));

    }


    /********************** END OF SYSTEMS ADMIN BLOCK ********************/


    /********************** BDC ADMIN BLOCK ********************/
    /**
     * @name bdc index
     * @return Array of data.
     */
    function bdc()
    {

    }


    function create_bdc_template()
    {

    }

    function create_omc_account($type = null)
    {
        $user_type = $this->Auth->user('user_type');
        $cp = $this->Session->read('CompanyProfile');

        switch ($type) {
            case 'get' :
                $this->autoRender = false;
                $this->autoLayout = false;

                $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
                $limit = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
                $sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'id';
                $order = isset($_POST['order']) ? strval($_POST['order']) : 'desc';
                $start = ($page - 1) * $limit;
                $result = array();
                $items = array();

                $condition_array = array('Omc.bdc_id' => $cp['id'], 'Omc.deleted' => 'n');
                /* if (!empty($search_query)) {
                    if ($search_query == 'username') {
                        $condition_array = array(
                            'Region.username' => $search_query,
                            'Region.deleted' => 'n'
                        );
                    }
                    else {
                        $condition_array = array(
                            "Region.$qtype LIKE" => $search_query . '%',
                            'Region.deleted' => 'n'
                        );
                    }
                }*/

                $grid_count = $this->Omc->find('count', array('conditions' => $condition_array, 'contain' => array('Omclist', 'OmcUser'), 'recursive' => 1));
                $result["total"] = $grid_count;
                $grid_data = $this->Omc->find('all', array('conditions' => $condition_array, 'contain' => array('Omclist', 'OmcUser'), 'order' => "Omc.$sort $order", 'limit' => $start . ',' . $limit, 'recursive' => 1));

                /* $grid_data = $this->Omc->find('all', array(
                     'conditions'=>array('Omc.bdc_id'=>$cp['id']),
                     'contain'=>array('Omclist','OmcUser'),
                     'recursive' => 1
                 ));*/

                //echo debug($grid_data);

                foreach ($grid_data as $data) {

                    $items[] = array('name' => $data['Omclist'], 'number_of_users' => count($data['OmcUser']));
                }
                $result["rows"] = $items;

                echo json_encode($result);

                break;

        }


        $omclist = $this->Omclist->find('all', array(
            'conditions' => array('deleted' => 'n'),
            'order' => array("Omclist.name" => 'asc'),
            'recursive' => -1
        ));

        $omclist = Hash::combine($omclist, '{n}.Omclist.id', '{n}.Omclist.name');

        $this->set(compact('title_for_layout', 'omclist'));
    }


    function depot($type = null)
    {
        $user_type = $this->Auth->user('user_type');
        $cp = $this->Session->read('CompanyProfile');

        switch ($type) {
            case 'get' :
                $this->autoRender = false;
                $this->autoLayout = false;

                $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
                $limit = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
                $sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'id';
                $order = isset($_POST['order']) ? strval($_POST['order']) : 'desc';
                $start = ($page - 1) * $limit;
                $result = array();
                $items = array();

                $condition_array = array('Depot.bdc_id' => $cp['id'], 'Depot.deleted' => 'n');
                /* if (!empty($search_query)) {
                    if ($search_query == 'username') {
                        $condition_array = array(
                            'Region.username' => $search_query,
                            'Region.deleted' => 'n'
                        );
                    }
                    else {
                        $condition_array = array(
                            "Region.$qtype LIKE" => $search_query . '%',
                            'Region.deleted' => 'n'
                        );
                    }
                }*/

                $grid_count = $this->Depot->find('count', array('conditions' => $condition_array, 'recursive' => -1));
                $result["total"] = $grid_count;
                $grid_data = $this->Depot->find('all', array('conditions' => $condition_array, 'order' => "Depot.$sort $order", 'limit' => $start . ',' . $limit, 'recursive' => -1));

                foreach ($grid_data as $data) {
                    $items[] = $data['Depot'];
                }
                $result["rows"] = $items;

                echo json_encode($result);

                break;

            case 'save' :
                $this->autoRender = false;
                $this->autoLayout = false;

                $_POST['created_by'] = $this->Auth->user('id');
                $_POST['bdc_id'] = $cp['id'];
                $data = array('Depot' => $_POST);
                if ($this->Depot->save($this->sanitize($data))) {
                    echo json_encode(array('success' => true));
                } else {
                    echo json_encode(array('msg' => 'Sorry errors occured while saving data.'));
                }

                break;

            case 'delete':
                $this->autoRender = false;
                $this->autoLayout = false;


                break;
        }

        //$this->set(compact('title_for_layout','regions'));
    }


    function product_type($type = null)
    {
        $user_type = $this->Auth->user('user_type');
        $cp = $this->Session->read('CompanyProfile');

        switch ($type) {
            case 'get' :
                $this->autoRender = false;
                $this->autoLayout = false;

                $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
                $limit = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
                $sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'id';
                $order = isset($_POST['order']) ? strval($_POST['order']) : 'desc';
                $start = ($page - 1) * $limit;
                $result = array();
                $items = array();

                $condition_array = array('ProductType.bdc_id' => $cp['id'], 'ProductType.deleted' => 'n');
                /* if (!empty($search_query)) {
                    if ($search_query == 'username') {
                        $condition_array = array(
                            'Region.username' => $search_query,
                            'Region.deleted' => 'n'
                        );
                    }
                    else {
                        $condition_array = array(
                            "Region.$qtype LIKE" => $search_query . '%',
                            'Region.deleted' => 'n'
                        );
                    }
                }*/

                $grid_count = $this->ProductType->find('count', array('conditions' => $condition_array, 'recursive' => -1));
                $result["total"] = $grid_count;
                $grid_data = $this->ProductType->find('all', array('conditions' => $condition_array, 'order' => "ProductType.$sort $order", 'limit' => $start . ',' . $limit, 'recursive' => -1));

                foreach ($grid_data as $data) {
                    $items[] = $data['ProductType'];
                }
                $result["rows"] = $items;

                echo json_encode($result);

                break;

            case 'save' :
                $this->autoRender = false;
                $this->autoLayout = false;

                $_POST['created_by'] = $this->Auth->user('id');
                $_POST['bdc_id'] = $cp['id'];
                $data = array('ProductType' => $_POST);
                if ($this->ProductType->save($this->sanitize($data))) {
                    echo json_encode(array('success' => true));
                } else {
                    echo json_encode(array('msg' => 'Sorry errors occured while saving data.'));
                }

                break;

            case 'delete':
                $this->autoRender = false;
                $this->autoLayout = false;


                break;
        }

        //$this->set(compact('title_for_layout','regions'));
    }


    /********************** END OF BDC ADMIN BLOCK ********************/


    /********************** OMC ADMIN BLOCK ********************/
    /**
     * @name omc index
     * @return Array of data.
     */
    function omc()
    {

    }


    /********************** END OF OMC ADMIN BLOCK ********************/


    //Shared action for creating users
    function create_user($type = null)
    {
        $user_type = $this->Auth->user('user_type');
        $cp = $this->Session->read('CompanyProfile');

        switch ($type) {
            case 'get' :
                $this->autoRender = false;
                $this->autoLayout = false;

                $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
                $limit = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
                $sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'id';
                $order = isset($_POST['order']) ? strval($_POST['order']) : 'desc';
                $start = ($page - 1) * $limit;
                $result = array();
                $items = array();

                $condition_array = array('User.deleted' => 'n');
                if ($user_type == 'system') {
                    //$condition_array[] = array('User.user_level' => 'admin');
                    $condition_array[] = array('User.user_type' => 'system');
                } elseif ($user_type == 'bdc') {
                    $condition_array[] = array('User.user_type' => 'bdc');
                } elseif ($user_type == 'omc') {
                    $condition_array[] = array('User.user_type' => 'omc');
                }
                /* if (!empty($search_query)) {
                    if ($search_query == 'username') {
                        $condition_array = array(
                            'User.username' => $search_query,
                            'User.deleted' => 'n'
                        );
                    }
                    else {
                        $condition_array = array(
                            "BdcDistribution.$qtype LIKE" => $search_query . '%',
                            'BdcDistribution.deleted' => 'n'
                        );
                    }
                }*/
                $fields = array('id', 'title', 'fname', 'lname', 'mname', 'username', 'user_type', 'user_level', 'telephone', 'email');
                $grid_count = $this->User->find('count', array('conditions' => $condition_array, 'recursive' => -1));
                $result["total"] = $grid_count;
                $grid_data = $this->User->find('all', array('fields' => $fields, 'conditions' => $condition_array, 'order' => "User.$sort $order", 'limit' => $start . ',' . $limit, 'recursive' => -1));

                foreach ($grid_data as $data) {
                    $items[] = $data['User'];
                }
                $result["rows"] = $items;

                echo json_encode($result);

                break;

            case 'save' :
                $this->autoRender = false;
                $this->autoLayout = false;

                $level = $_POST['user_level'];


                if ($user_type == 'system') {
                    $_POST['user_type'] = 'system';
                } elseif ($user_type == 'bdc') {
                    $_POST['user_type'] = 'bdc';
                    if ($_POST['user_level'] == 'Admin') {
                        $_POST['user_level'] = 'admin';
                    } else {
                        $_POST['user_level'] = 'normal_user';
                    }
                } elseif ($user_type == 'omc') {
                    $_POST['user_type'] = 'omc';
                    $_POST['user_level'] = 'normal_user';
                }

                /*if(isset($_POST['id'])){
                    unset($_POST['password']);
                    unset($_POST['expires_on']);
                    unset($_POST['created']);
                    unset($_POST['modified']);
                }*/

                $data = array('User' => $_POST);
                if ($user_type == 'bdc') {
                    $data['BdcUser'] = array(
                        'bdc_id' => $cp['id'],
                        'bdc_user_type' => $level
                    );
                }

                //echo json_encode ($data);
                $data = array('User' => $_POST);

                $saveRe = $this->User->save($this->sanitize($data));

                if ($saveRe) {
                    echo json_encode(array('success' => true));
                } else {
                    echo json_encode(array('success' => false, 'msg' => 'Some errors occured.'));
                }
                //echo debug($data);

                break;

            case 'load':
                $this->autoRender = false;
                $this->autoLayout = false;

                break;

            case 'delete':
                $this->autoRender = false;
                $this->autoLayout = false;

                break;
        }


        $this->set(compact('title_for_layout', 'user_type'));
    }


}