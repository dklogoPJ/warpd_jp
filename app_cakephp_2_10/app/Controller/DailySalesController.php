<?php

/**
 * @name OmcController.php
 */
App::import('Controller', 'OmcCustomerApp');

class DailySalesController extends OmcCustomerAppController
{
    # Controller name

    var $name = 'OmcDailySales';
    # set the model to use
    var $uses = array('OmcCustomerSalesProduct');

    # Set the layout to use
    var $layout = 'omc_customer_layout';

    public function beforeFilter($param_array = null)
    {
        parent::beforeFilter();
    }


    function index()
    {

    }


    function product_setup($type = 'get')
    {   $permissions = $this->action_permission;
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
                    $sortorder = isset($_POST['sortorder']) ? $_POST['sortorder'] : 'desc';
                    /** Sort order */
                    $qtype = isset($_POST['qtype']) ? $_POST['qtype'] : '';
                    /** Search column */
                    $search_query = isset($_POST['query']) ? $_POST['query'] : '';
                    /** @var $filter  */
                    $filter_status =   isset($_POST['filter_status']) ? $_POST['filter_status'] : 'incomplete_orders' ;
                    /** Search string */
                    $rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
                    $limit = $rp;
                    $start = ($page - 1) * $rp;

                    //get users id for this company only
                    $condition_array = array(
                        'OmcCustomerSalesProduct.omc_customer_id' => $company_profile['id'],
                        'OmcCustomerSalesProduct.deleted' => 'n'
                    );

                    if (!empty($search_query)) {
                        if ($qtype == 'id') {
                            $condition_array['OmcCustomerSalesProduct.id'] = $search_query;
                        }
                        else {
                             $condition_array = array(
                                 "OmcCustomerSalesProduct.$qtype LIKE" => $search_query . '%',
                                 'OmcCustomerSalesProduct.deleted' => 'n'
                             );
                        }
                    }

                   /* $contain = array(
                        'Omc'=>array('fields' => array('Omc.id', 'Omc.name')),
                        'ProductType'=>array('fields' => array('ProductType.id', 'ProductType.name')),
                        'OmcCustomer'=>array('fields' => array('OmcCustomer.id', 'OmcCustomer.name'))
                    );*/

                    $data_table = $this->OmcCustomerSalesProduct->find('all', array('conditions' => $condition_array,'order' => "OmcCustomerSalesProduct.$sortname $sortorder", 'limit' => $start . ',' . $limit, 'recursive' => 1));
                    $data_table_count = $this->OmcCustomerSalesProduct->find('count', array('conditions' => $condition_array, 'recursive' => -1));

                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {
                            $return_arr[] = array(
                                'id' => $obj['OmcCustomerSalesProduct']['id'],
                                'cell' => array(
                                    $obj['OmcCustomerSalesProduct']['id'],
                                    $obj['OmcCustomerSalesProduct']['name']
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
                    $data = array('OmcCustomerSalesProduct' => $_POST);

                    $data['OmcCustomerSalesProduct']['modified_by'] = $authUser['id'];
                    if($_POST['id']== 0){//New Manual Entry
                        $data['OmcCustomerSalesProduct']['created_by'] = $authUser['id'];
                        $data['OmcCustomerSalesProduct']['omc_customer_id'] =  $company_profile['id'];
                    }

                    if ($this->OmcCustomerSalesProduct->save($this->sanitize($data))) {
                        if($_POST['id'] > 0){
                            return json_encode(array('code' => 0, 'msg' => 'Data Updated!'));
                        }
                        else{
                            return json_encode(array('code' => 0, 'msg' => 'Data Saved', 'id'=>$this->OmcCustomerSalesProduct->id));
                        }
                    } else {
                        echo json_encode(array('code' => 1, 'msg' => 'Some errors occurred.'));
                    }
                    //echo debug($data);
                    break;

                case 'load':

                    break;

                case 'delete':
                    $ids = $_POST['ids'];
                    $modObj = ClassRegistry::init('OmcCustomerSalesProduct');
                    $result = $modObj->updateAll(
                        $this->sanitize(array('OmcCustomerSalesProduct.deleted' => "'y'")),
                        $this->sanitize(array('OmcCustomerSalesProduct.id' => $ids))
                    );
                    if ($result) {
                        echo json_encode(array('code' => 0, 'msg' => 'Data Deleted!'));
                    } else {
                        echo json_encode(array('code' => 1, 'msg' => 'Data cannot be deleted'));
                    }
                    break;
            }
        }


        $this->set(compact('permissions'));
    }


}