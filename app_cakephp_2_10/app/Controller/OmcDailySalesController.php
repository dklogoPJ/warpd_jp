<?php

/**
 * @name OmcDailySalesController.php
 */
App::import('Controller', 'OmcApp');

class OmcDailySalesController extends OmcAppController
{
    # Controller name

    var $name = 'OmcDailySales';
    # set the model to use
    var $uses = array('OmcSalesSheet','OmcSalesRecord','OmcSalesValue','OmcSalesFormField','OmcSalesForm','OmcCustomer');

    # Set the layout to use
    var $layout = 'omc_layout';

    public function beforeFilter($param_array = null)
    {
        parent::beforeFilter();
    }


    function index()
    {

    }


    function product_setup($type = 'get')
    {   $permissions = $this->action_permission;
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
                    $filter_status =   isset($_POST['filter_status']) ? $_POST['filter_status'] : 'incomplete_orders' ;
                    /** Search string */
                    $rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
                    $limit = $rp;
                    $start = ($page - 1) * $rp;

                    //get users id for this company only
                    $condition_array = array(
                        'OmcSalesProduct.omc_id' => $company_profile['id'],
                        'OmcSalesProduct.deleted' => 'n'
                    );

                    if (!empty($search_query)) {
                        if ($qtype == 'id') {
                            $condition_array['OmcSalesProduct.id'] = $search_query;
                        }
                        else {
                             $condition_array = array(
                                 "OmcSalesProduct.$qtype LIKE" => $search_query . '%',
                                 'OmcSalesProduct.deleted' => 'n'
                             );
                        }
                    }

                   /* $contain = array(
                        'Omc'=>array('fields' => array('Omc.id', 'Omc.name')),
                        'ProductType'=>array('fields' => array('ProductType.id', 'ProductType.name')),
                        'OmcCustomer'=>array('fields' => array('OmcCustomer.id', 'OmcCustomer.name'))
                    );*/

                    $data_table = $this->OmcSalesProduct->find('all', array('conditions' => $condition_array,'order' => "OmcSalesProduct.$sortname $sortorder", 'limit' => $start . ',' . $limit, 'recursive' => 1));
                    $data_table_count = $this->OmcSalesProduct->find('count', array('conditions' => $condition_array, 'recursive' => -1));

                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {
                            $return_arr[] = array(
                                'id' => $obj['OmcSalesProduct']['id'],
                                'cell' => array(
                                    $obj['OmcSalesProduct']['id'],
                                    $obj['OmcSalesProduct']['name']
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
                    $data = array('OmcSalesProduct' => $_POST);

                    $data['OmcSalesProduct']['modified_by'] = $authUser['id'];
                    if($_POST['id']== 0){//New Manual Entry
                        $data['OmcSalesProduct']['created_by'] = $authUser['id'];
                        $data['OmcSalesProduct']['omc_id'] =  $company_profile['id'];
                    }

                    if ($this->OmcSalesProduct->save($this->sanitize($data))) {
                        if($_POST['id'] > 0){
                            return json_encode(array('code' => 0, 'msg' => 'Data Updated!'));
                        }
                        else{
                            return json_encode(array('code' => 0, 'msg' => 'Data Saved', 'id'=>$this->OmcSalesProduct->id));
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
                    $modObj = ClassRegistry::init('OmcSalesProduct');
                    $result = $modObj->updateAll(
                        $this->sanitize(array('OmcSalesProduct.deleted' => "'y'")),
                        $this->sanitize(array('OmcSalesProduct.id' => $ids))
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



    function sales_form_templates(){
        $permissions = $this->action_permission;
        $company_profile = $this->global_company;

        if ($this->request->is('ajax')) {
            $this->autoRender = false;
            $this->autoLayout = false;
            $authUser = $this->Auth->user();
            $post = $this->sanitize($_POST);
            $action_type = isset($post['form_action_type'])? $post['form_action_type'] : $post['field_action_type'];
            //Form Save
            if($action_type == 'form_save'){
                $data = array('OmcSalesForm'=>array(
                    'id'=>$post['form_id'],
                    'form_name'=>$post['form_name'],
                    'description'=>$post['form_description'],
                    'omc_id'=>$post['omc_id'],
                    'modified_by'=>$authUser['id']
                )) ;
                if($post['form_id'] == 0){//New Manual Entry
                    $data['OmcSalesForm']['created_by'] = $authUser['id'];
                }
                if ($this->OmcSalesForm->save($data['OmcSalesForm'])) {
                    if($post['form_id'] > 0){
                        return json_encode(array('code' => 0, 'msg' => 'Form Updated!', 'id'=>$post['form_id']));
                    }
                    else{
                        return json_encode(array('code' => 0, 'msg' => 'Form Saved', 'id'=>$this->OmcSalesForm->id));
                    }
                }
                else {
                    echo json_encode(array('code' => 1, 'msg' => 'Some errors occurred.'));
                }
            }
            elseif($action_type == 'form_delete'){
                $form_id= $post['form_id'];
                $res = $this->OmcSalesForm->deleteForm($form_id,$authUser['id']);
                if ($res) {
                    return json_encode(array('code' => 0, 'msg' => 'Form Deleted!', 'id'=>$post['form_id']));
                }
                else {
                    return json_encode(array('code' => 1, 'msg' => 'Form Deletion Failed.'));
                }
            }
            elseif($action_type == 'form_preview'){
                $form_id= $post['form_id'];
                $from_data = $this->OmcSalesForm->getFormForPreview($form_id);
                $view = new View($this, false);
                $view->set(compact('from_data')); // set variables
                $view->viewPath = 'Elements/omc/'; // render an element
                $html = $view->render('preview_table_form'); // get the rendered markup

                if ($from_data) {
                    return json_encode(array('code' => 0, 'msg' => 'Form Found!', 'form_name'=>$from_data['form']['name'],'html'=>$html));
                }
                else {
                    return json_encode(array('code' => 1, 'msg' => 'Form Not Found.'));
                }
            }
            //Field Save
            if($action_type == 'field_save'){
                $data = array('OmcSalesFormField'=>array(
                    'id'=>$post['field_id'],
                    'omc_sales_form_id'=>$post['omc_sales_form_id'],
                    //'groups'=>$post['groups'],
                    'field_name'=>$post['field_name'],
                    'field_type'=>$post['field_type'],
                    'field_type_values'=>$post['field_type_values'],
                    'field_required'=>$post['field_required'],
                    'modified_by'=>$authUser['id']
                )) ;

                if($post['field_id'] == 0){//New Manual Entry
                    $data['OmcSalesFormField']['created_by'] = $authUser['id'];
                }
                if ($this->OmcSalesFormField->save($data['OmcSalesFormField'])) {
                    if($post['field_id'] > 0){
                        return json_encode(array('code' => 0, 'msg' => 'Field Updated!', 'id'=>$post['field_id']));
                    }
                    else{
                        return json_encode(array('code' => 0, 'msg' => 'Field Saved', 'id'=>$this->OmcSalesFormField->id));
                    }
                }
                else {
                    echo json_encode(array('code' => 1, 'msg' => 'Some errors occurred.'));
                }
            }
            elseif($action_type == 'field_delete'){
                $field_id= $post['field_id'];
                $res = $this->OmcSalesFormField->deleteField($field_id,$authUser['id']);
                if ($res) {
                    return json_encode(array('code' => 0, 'msg' => 'Field Deleted!', 'id'=>$field_id));
                }
                else {
                    return json_encode(array('code' => 1, 'msg' => 'Field Deletion Failed.'));
                }
            }
        }


        $sale_forms = $this->OmcSalesForm->getAllSalesForms($company_profile['id']);

        $sale_form_options = $forms_fields = array();
        foreach($sale_forms as $form_arr){
            $form = $form_arr['OmcSalesForm'];
            //Forms for Options
            $sale_form_options[$form['id']] = $form['form_name'];
            //group forms and fields
            $fields_arr = array();
            foreach($form_arr['OmcSalesFormField'] as $field){
                if($field['deleted'] == 'n'){
                    $fields_arr[$field['id']]=array(
                        'id'=>$field['id'],
                        'form_id'=>$field['omc_sales_form_id'],
                        'groups'=>$field['groups'],
                        'field_name'=>$field['field_name'],
                        'field_type'=>$field['field_type'],
                        'field_type_values'=>$field['field_type_values'],
                        'field_required'=>$field['field_required']
                    );
                }
            }

            $forms_fields[$form['id']] = array(
                'id' => $form['id'],
                'name' => $form['form_name'],
                'fields'=>$fields_arr
            );
        }

        //debug($forms_fields);

        $this->set(compact('permissions','products_services','sale_forms','company_profile','sale_form_options','forms_fields'));
    }



    function station_sales(){
        $permissions = $this->action_permission;
        $company_profile = $this->global_company;
        $omc_id = $company_profile['id'];

        if ($this->request->is('ajax')) {
            $this->autoRender = false;
            $this->autoLayout = false;
            $authUser = $this->Auth->user();
            $data = $this->request->data;
            $post = $this->sanitize($data);
            $station = $post['Query']['station'];
            $sales_form_id = $post['Query']['sales_form_id'];
            $sheet_date =  $this->covertDate($post['Query']['record_dt'],'mysql');
            $sale_forms_data = $this->OmcSalesForm->getAllSalesForms($omc_id,$sales_form_id);
            $forms_n_fields = array();
            foreach($sale_forms_data as $form_arr){
                $form = $form_arr['OmcSalesForm'];
                $fields = $form_arr['OmcSalesFormField'];
                if(!empty($fields)){
                    //group forms and fields
                    $fields_arr = array();
                    foreach($form_arr['OmcSalesFormField'] as $field){
                        if($field['deleted'] == 'n'){
                            $fields_arr[$field['id']]=array(
                                'id'=>$field['id'],
                                'form_id'=>$field['omc_sales_form_id'],
                                'groups'=>$field['groups'],
                                'field_name'=>$field['field_name'],
                                'field_type'=>$field['field_type'],
                                'field_type_values'=>$field['field_type_values'],
                                'field_required'=>$field['field_required']
                            );
                        }
                    }
                    //Get form Values
                    $form_data_records = array();
                    $form_data_record_raw = $this->OmcSalesSheet->getFormData($form['id'],$station,$omc_id,$sheet_date);
                    $form_data_records = $form_data_record_raw['data'];
                    $forms_n_fields[$form['id']] = array(
                        'id' => $form['id'],
                        'name' => $form['form_name'],
                        'fields'=>$fields_arr,
                        'values'=>$form_data_records
                    );
                }
            }


            $view = new View($this, false);
            $view->set(compact('forms_n_fields')); // set variables
            $view->viewPath = 'Elements/omc/'; // render an element
            $html = $view->render('preview_station'); // get the rendered markup

            return json_encode(array('code' => 0, 'msg' => 'Records Found!', 'html'=>$html));

          /*  if ($from_data) {

            }
            else {
                return json_encode(array('code' => 1, 'msg' => 'No Records Found!'));
            }*/
        }

        $omc_customers_lists = $this->get_customer_list();
        $station_opt = array();
        foreach($omc_customers_lists as $data){
            $station_opt[$data['id']] = $data['name'];
        }

        $sales_forms = $this->OmcSalesForm->getSalesFormOnly($company_profile['id']);
        $form_sales_opt = array('0'=>'All Sales Form');
        foreach($sales_forms as $data){
            $form_sales_opt[$data['OmcSalesForm']['id']] = $data['OmcSalesForm']['form_name'];
        }

        $this->set(compact('permissions','company_profile','station_opt','form_sales_opt'));
    }


    function export_sale_data (){
        $download = false;
        $company_profile = $this->global_company;
        $filename = '';
        $objPHPExcel = '';

        if($this->request->is('post')){
            $data = $this->request->data;
            $post = $this->sanitize($data);
            $station = $post['data_station'];
            $sales_form_id = $post['data_sales_form_id'];
            $sheet_date =  $this->covertDate($post['data_record_dt'],'mysql');

            $export_data = $this->OmcSalesSheet->getExportData($sales_form_id,$station,$company_profile['id'],$sheet_date);

            if ($export_data) {
                $omc_customers_lists = $this->get_customer_list();
                $station_name = '';
                foreach($omc_customers_lists as $data){
                    if($data['id'] == $station){
                        $station_name = $data['name'];
                        break;
                    }
                }

                $download = true;
                $list_data = $export_data;
                $filename = $station_name." Daily Sale ".$sheet_date;
                $res = $this->convertToExcelBook($list_data,$filename);
                $objPHPExcel = $res['excel_obj'];
                $filename = $res['filename'];
            }

        }

        $this->autoLayout = false;

        $this->set(compact('objPHPExcel', 'download', 'filename'));
    }

}