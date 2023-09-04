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
    var $uses = array('OmcSalesForm','OmcSalesFormField','OmcSalesFormPrimaryFieldOption',
        'OmcCustomer','Menu','SalesFormElementEvent','SalesFormElementAction','Option',
        'SalesFormElementOperand','ProductType','OmcCustomerDailySale','LpgSetting','LubeSetting',
        'OmcSalesReport','OmcSalesReportField','OmcSalesReportPrimaryFieldOption','OmcSalesReport','OmcSalesReportCell'
    );

    # Set the layout to use
    var $layout = 'omc_layout';

    public function beforeFilter($param_array = null){
        parent::beforeFilter();
    }


    function index(){}

    function product_setup($type = 'get') {
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


    function _createSalesFormMenu($data){
        $menu_action = str_replace(" ","_",strtolower(trim($data['form_name'])));
        return $this->Menu->createMenu(array(
            'id'=>$data['menu_id'],
            'type'=>'omc_customer',
            'title'=>$data['form_name'],
            'description'=>$data['form_name'],
            'permission_controls'=>'A,E,D,PX',
            'parent'=>114,
            'required'=>'',
            'menu_group'=>'Operations',
            'controller'=>'OmcCustomerDailySales',
            'action'=>$menu_action,
            'url_type'=>'omc_customer_form_url_proxy',
            'icon'=>'icon-file',
            'menu_name'=>$data['form_name'],
            'order'=>$data['form_order']
        ));
    }

    function sales_form_templates(){
        $permissions = $this->action_permission;
        $company_profile = $this->global_company;

        if ($this->request->is('ajax')) {
            $this->autoRender = false;
            $this->autoLayout = false;
            $authUser = $this->Auth->user();
            $post = $this->sanitize($_POST);
            $action_type = '';
            if(isset($post['form_action_type'])) {
                $action_type = $post['form_action_type'];
            }
            if(isset($post['field_action_type'])) {
                $action_type = $post['field_action_type'];
            }
            if(isset($post['pf_option_action_type'])) {
                $action_type = $post['pf_option_action_type'];
            }
            //Form
            if($action_type == 'form_save'){
                $menu_action = str_replace(" ","_",strtolower(trim($post['form_name'])));
                //Create form url so it can be accessed via menus
                $post['action'] = $menu_action;
                $menu_id = $this->_createSalesFormMenu($post);

                $data = array('OmcSalesForm'=>array(
                    'id'=>$post['form_id'],
                    'menu_id'=> $menu_id,
                    'form_key'=>$menu_action,
                    'form_name'=>$post['form_name'],
                    'order'=>$post['form_order'],
                    'description'=>$post['form_description'],
                    'primary_field'=>$post['form_primary_field'],
                    'omc_customer_list'=>$post['form_omc_customer_list_str'],
                    'omc_sales_report_id'=> $post['form_omc_sales_report_id'],
                    'omc_id'=>$post['omc_id'],
                    'modified_by'=>$authUser['id']
                )) ;
                if($post['form_id'] == 0){//New Manual Entry
                    $data['OmcSalesForm']['created_by'] = $authUser['id'];
                }
                if ($this->OmcSalesForm->save($data['OmcSalesForm'])) {
                    if($post['form_id'] > 0){
                        return json_encode(array('code' => 0, 'msg' => 'Form Updated!', 'id'=>$post['form_id'], 'menu_id'=>$menu_id));
                    }
                    else{
                        return json_encode(array('code' => 0, 'msg' => 'Form Saved', 'id'=>$this->OmcSalesForm->id, 'menu_id'=>$menu_id));
                    }
                }
                else {
                    $this->Menu->deleteMenu($menu_id, $authUser['id']);
                    echo json_encode(array('code' => 1, 'msg' => 'Some errors occurred.'));
                }
            }
            elseif($action_type == 'form_delete'){
                $form_id= $post['form_id'];
                $menu_id= $post['menu_id'];
                $res = $this->OmcSalesForm->deleteForm($form_id, $authUser['id']);
                if ($res) {
                    $this->Menu->deleteMenu($menu_id, $authUser['id']);
                    return json_encode(array('code' => 0, 'msg' => 'Form Deleted!', 'id'=>$post['form_id']));
                }
                else {
                    return json_encode(array('code' => 1, 'msg' => 'Form Deletion Failed.'));
                }
            }
            //Field
            elseif($action_type == 'field_save'){
                $data = array(
                    'id'=>$post['field_id'],
                    'omc_sales_form_id'=>$post['omc_sales_form_id'],
                    'field_name'=>$post['field_name'],
                    'field_order'=>$post['field_order'],
                    'field_type'=>$post['field_type'],
                    'field_type_values'=>$post['field_type_values'],
                    'field_required'=>$post['field_required'],
                    'field_disabled'=>$post['field_disabled'],
                    'field_event'=>$post['field_event'],
                    'field_action'=>$post['field_action'],
                    'field_action_sources'=> $post['field_action_sources_str'],
                    'field_action_source_column'=> $post['field_action_source_column'],
                    'dsrp_form'=>$post['dsrp_form'],
                    'dsrp_form_fields'=>$post['dsrp_form_fields'],
                    'operands'=>$post['operands'],
                    'field_action_targets'=> $post['field_action_targets_str'],
                    'modified_by'=>$authUser['id']
                ) ;

                if($post['field_id'] == 0){//New Manual Entry
                    $data['created_by'] = $authUser['id'];
                }
                if ($this->OmcSalesFormField->save($data)) {
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
            //Primary Field
            elseif($action_type == 'option_save'){
                $data = array(
                    'id'=>$post['pf_option_id'],
                    'omc_sales_form_id'=>$post['pf_omc_sales_form_id'],
                    'option_name'=>$post['pf_option_name'],
                    'option_link_type'=>$post['pf_option_link_type'],
                    'option_link_id'=> isset($post['pf_option_link_id']) ?: '',
                    'order'=>$post['pf_order'],
                    'is_total'=>$post['pf_option_is_total'],
                    'total_option_list'=>$post['pf_total_option_list'],
                    'total_field_list'=>$post['pf_total_field_list'],
                    'modified_by'=>$authUser['id']
                ) ;

                if($post['pf_option_id'] == 0){//New Manual Entry
                    $data['created_by'] = $authUser['id'];
                }
                if ($this->OmcSalesFormPrimaryFieldOption->save($data)) {
                    if($post['pf_option_id'] > 0){
                        return json_encode(array('code' => 0, 'msg' => 'Option Updated!', 'id'=>$post['pf_option_id']));
                    }
                    else{
                        return json_encode(array('code' => 0, 'msg' => 'Option Saved', 'id'=>$this->OmcSalesFormPrimaryFieldOption->id));
                    }
                }
                else {
                    echo json_encode(array('code' => 1, 'msg' => 'Some errors occurred.'));
                }
            }
            elseif($action_type == 'option_delete'){
                $option_id= $post['pf_option_id'];
                $res = $this->OmcSalesFormPrimaryFieldOption->deleteOption($option_id, $authUser['id']);
                if ($res) {
                    return json_encode(array('code' => 0, 'msg' => 'Option Deleted!', 'id'=>$option_id));
                }
                else {
                    return json_encode(array('code' => 1, 'msg' => 'Option Deletion Failed.'));
                }
            }
            //Preview
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
                        'field_name'=>$field['field_name'],
                        'field_order'=>$field['field_order'],
                        'field_type'=>$field['field_type'],
                        'field_type_values'=>$field['field_type_values'],
                        'field_required'=>$field['field_required'],
                        'field_disabled'=>$field['field_disabled'],
                        'field_event'=>$field['field_event'],
                        'field_action'=>$field['field_action'],
                        'field_action_sources'=>$field['field_action_sources'],
                        'field_action_source_column'=>$field['field_action_source_column'],
                        'dsrp_form'=>$field['dsrp_form'],
                        'dsrp_form_fields'=>$field['dsrp_form_fields'],
                        'operands'=>$field['operands'],
                        'field_action_targets'=>$field['field_action_targets']
                    );
                }
            }
            //group form primary field options
            $primary_field_options_arr = array();
            foreach($form_arr['OmcSalesFormPrimaryFieldOption'] as $option){
                if($option['deleted'] == 'n'){
                    $primary_field_options_arr[$option['id']]=array(
                        'id'=>$option['id'],
                        'form_id'=>$option['omc_sales_form_id'],
                        'option_name'=>$option['option_name'],
                        'option_link_type'=>$option['option_link_type'],
                        'option_link_id'=>$option['option_link_id'],
                        'order'=>$option['order'],
                        'is_total'=>$option['is_total'],
                        'total_option_list'=>$option['total_option_list'],
                        'total_field_list'=>$option['total_field_list']
                    );
                }
            }

            $forms_fields[$form['id']] = array(
                'id' => $form['id'],
                'menu_id' => $form['menu_id'],
                'name' => $form['form_name'],
                'description' => $form['description'],
                'order' => $form['order'],
                'omc_customer_list' => $form['omc_customer_list'],
                'omc_sales_report_id' => $form['omc_sales_report_id'],
                'primary_field_name' => $form['primary_field'],
                'primary_field_options'=>$primary_field_options_arr,
                'fields'=>$fields_arr
            );

        }

        $sale_form_element_events = $this->SalesFormElementEvent->getKeyValuePair();
        $sale_form_element_actions = $this->SalesFormElementAction->getKeyValuePair();
        $sale_form_element_operands = $this->SalesFormElementOperand->getKeyValuePair();
        $all_option_link_types = $this->Option->getDSRPLinkTypes($company_profile['id']);

        $all_reports_query = $this->OmcSalesReport->getSalesReportOnly($company_profile['id']);
        $all_reports = array(array('id'=>'', 'name'=> 'None'));
        foreach($all_reports_query as $data){
            $all_reports[] = array(
                'id' => $data['OmcSalesReport']['id'],
                'name' => $data['OmcSalesReport']['report_name']
            );
        }

        $omc_customers_list = $this->get_customer_list();
        $customers = array(array('id'=>'all', 'name'=> 'All Customers'));
        foreach($omc_customers_list as $data){
            $customers[] = array(
                'id' => $data['id'],
                'name' => $data['name']
            );
        }

        $this->set(compact('permissions','sale_forms','company_profile','sale_form_options','forms_fields','sale_form_element_events','sale_form_element_actions','sale_form_element_operands', 'customers', 'all_option_link_types','all_reports'));
    }


    function sales_report_templates(){
        $permissions = $this->action_permission;
        $company_profile = $this->global_company;

        if ($this->request->is('ajax')) {
            $this->autoRender = false;
            $this->autoLayout = false;
            $authUser = $this->Auth->user();
            $post = $this->sanitize($_POST);
            $action_type = '';
            if(isset($post['report_action_type'])) {
                $action_type = $post['report_action_type'];
            }
            if(isset($post['report_field_action_type'])) {
                $action_type = $post['report_field_action_type'];
            }
            if(isset($post['report_pf_option_action_type'])) {
                $action_type = $post['report_pf_option_action_type'];
            }
            if(isset($post['report_cell_action_type'])) {
                $action_type = $post['report_cell_action_type'];
            }
            //Form
            if($action_type == 'report_save'){
                $menu_action = str_replace(" ","_",strtolower(trim($post['report_name'])));
                //Create report url so it can be accessed via menus
                /*$post['action'] = $menu_action;
                $menu_id = $this->_createSalesFormMenu($post);*/
                $menu_id = null;

                $data = array(
                    'id'=>$post['report_id'],
                    'menu_id'=> $menu_id,
                    'report_key'=>$menu_action,
                    'report_name'=>$post['report_name'],
                    'report_order'=>$post['report_order'],
                    'report_description'=>$post['report_description'],
                    'report_primary_field'=>$post['report_primary_field'],
                    'omc_customer_list'=>$post['report_omc_customer_list_str'],
                    'omc_id'=>$post['omc_id'],
                    'modified_by'=>$authUser['id']
                ) ;
                if($post['report_id'] == 0){//New Manual Entry
                    $data['created_by'] = $authUser['id'];
                }
                if ($this->OmcSalesReport->save($data)) {
                    if($post['report_id'] > 0){
                        return json_encode(array('code' => 0, 'msg' => 'Report Updated!', 'id'=>$post['report_id'], 'menu_id'=>$menu_id));
                    }
                    else{
                        return json_encode(array('code' => 0, 'msg' => 'Report Saved', 'id'=>$this->OmcSalesReport->id, 'menu_id'=>$menu_id));
                    }
                }
                else {
                    //$this->Menu->deleteMenu($menu_id, $authUser['id']);
                    echo json_encode(array('code' => 1, 'msg' => 'Some errors occurred.'));
                }
            }
            elseif($action_type == 'report_delete'){
                $report_id= $post['report_id'];
                $menu_id= $post['menu_id'];
                $res = $this->OmcSalesReport->deleteReport($report_id, $authUser['id']);
                if ($res) {
                   // $this->Menu->deleteMenu($menu_id, $authUser['id']);
                    return json_encode(array('code' => 0, 'msg' => 'Report Deleted!', 'id'=>$post['report_id']));
                }
                else {
                    return json_encode(array('code' => 1, 'msg' => 'Report Deletion Failed.'));
                }
            }
            //Field
            elseif($action_type == 'report_field_save'){
                $data = array(
                    'id'=>$post['report_field_id'],
                    'omc_sales_report_id'=>$post['omc_sales_report_id'],
                    'report_field_name'=>$post['report_field_name'],
                    'report_field_order'=>$post['report_field_order'],
                    'modified_by'=>$authUser['id']
                ) ;

                if($post['report_field_id'] == 0){//New Manual Entry
                    $data['created_by'] = $authUser['id'];
                }
                if ($this->OmcSalesReportField->save($data)) {
                    if($post['report_field_id'] > 0){
                        return json_encode(array('code' => 0, 'msg' => 'Report Field Updated!', 'id'=>$post['report_field_id']));
                    }
                    else{
                        return json_encode(array('code' => 0, 'msg' => 'Report Field Saved', 'id'=>$this->OmcSalesReportField->id));
                    }
                }
                else {
                    echo json_encode(array('code' => 1, 'msg' => 'Some errors occurred.'));
                }
            }
            elseif($action_type == 'report_field_delete'){
                $field_id= $post['report_field_id'];
                $res = $this->OmcSalesReportField->deleteField($field_id, $authUser['id']);
                if ($res) {
                    return json_encode(array('code' => 0, 'msg' => 'Report Field Deleted!', 'id'=>$field_id));
                }
                else {
                    return json_encode(array('code' => 1, 'msg' => 'Report Field Deletion Failed.'));
                }
            }
            //Primary Field
            elseif($action_type == 'report_option_save'){
                $data = array(
                    'id'=>$post['report_pf_option_id'],
                    'omc_sales_report_id'=>$post['pf_omc_sales_report_id'],
                    'report_option_name'=>$post['report_pf_option_name'],
                    'report_option_link_type'=>$post['report_pf_option_link_type'],
                    'report_option_link_id'=> isset($post['report_pf_option_link_id']) ?: '',
                    'report_option_order'=>$post['report_pf_option_order'],
                    'report_is_total'=>$post['report_pf_option_is_total'],
                    'report_total_option_list'=>$post['report_pf_total_option_list'],
                    'report_total_field_list'=>$post['report_pf_total_field_list'],
                    'modified_by'=>$authUser['id']
                ) ;

                if($post['report_pf_option_id'] == 0){//New Manual Entry
                    $data['created_by'] = $authUser['id'];
                }
                if ($this->OmcSalesReportPrimaryFieldOption->save($data)) {
                    if($post['report_pf_option_id'] > 0){
                        return json_encode(array('code' => 0, 'msg' => 'Report Option Updated!', 'id'=>$post['report_pf_option_id']));
                    }
                    else{
                        return json_encode(array('code' => 0, 'msg' => 'Report Option Saved', 'id'=>$this->OmcSalesReportPrimaryFieldOption->id));
                    }
                }
                else {
                    echo json_encode(array('code' => 1, 'msg' => 'Some errors occurred.'));
                }
            }
            elseif($action_type == 'report_option_delete'){
                $option_id= $post['report_pf_option_id'];
                $res = $this->OmcSalesReportPrimaryFieldOption->deleteOption($option_id, $authUser['id']);
                if ($res) {
                    return json_encode(array('code' => 0, 'msg' => 'Report Option Deleted!', 'id'=>$option_id));
                }
                else {
                    return json_encode(array('code' => 1, 'msg' => 'Report Option Deletion Failed.'));
                }
            }//Report Cell
            elseif($action_type == 'report_cell_create'){
                foreach($post['cell_data'] as $row) {
                    //check to see if cell record exist, then skip if not create it
                    $report_cell = $this->OmcSalesReportCell->getAllReportCellByParams($row['omc_sales_report_id'], $row['omc_sales_report_primary_field_option_id'], $row['omc_sales_report_field_id']);
                    if(!$report_cell) {
                        $this->OmcSalesReportCell->create();
                        $this->OmcSalesReportCell->save($row);
                    }
                }
                $data = $this->OmcSalesReportCell->getAllReportCells();
                return json_encode(array('code' => 0, 'msg' => 'Report Cell Created!', 'data'=>$data ));
            }
            //Report Cell
            elseif($action_type == 'report_cell_save') {
                $data = array(
                    'id'=>$post['report_cell_id'],
                    'dsrp_form'=>$post['dsrp_form'],
                    'dsrp_primary_fields'=>$post['dsrp_primary_fields_str'],
                    'dsrp_fields'=>$post['dsrp_fields_str']
                );
                if ($this->OmcSalesReportCell->save($data)) {
                    $data = $this->OmcSalesReportCell->getAllReportCells();
                    return json_encode(array('code' => 0, 'msg' => 'Report Option Updated!', 'data'=>$data));
                } else {
                    return json_encode(array('code' => 1, 'msg' => 'Some errors occurred.'));
                }
            }
            elseif($action_type == 'report_cell_delete') {
                if ($this->OmcSalesReportCell->deleteCells ($post['report_id'], $post['id'], $post['delete_type'])) {
                    $data = $this->OmcSalesReportCell->getAllReportCells();
                    return json_encode(array('code' => 0, 'msg' => 'Report Cells Deleted!', 'data'=> $data));
                } else {
                    return json_encode(array('code' => 1, 'msg' => 'Some errors occurred.'));
                }
            }

        }


        $sale_reports = $this->OmcSalesReport->getAllSalesReports($company_profile['id']);

        $sale_report_options = $reports_fields = array();
        foreach($sale_reports as $report_arr){
            $rpt = $report_arr['OmcSalesReport'];
            //Forms for Options
            $sale_report_options[$rpt['id']] = $rpt['report_name'];
            //group forms and fields
            $fields_arr = array();
            foreach($report_arr['OmcSalesReportField'] as $field){
                if($field['deleted'] == 'n'){
                    $fields_arr[$field['id']]=array(
                        'id'=>$field['id'],
                        'report_id'=>$field['omc_sales_report_id'],
                        'report_field_name'=>$field['report_field_name'],
                        'report_field_order'=>$field['report_field_order']
                    );
                }
            }
            //group form primary field options
            $primary_field_options_arr = array();
            foreach($report_arr['OmcSalesReportPrimaryFieldOption'] as $option){
                if($option['deleted'] == 'n'){
                    $primary_field_options_arr[$option['id']]=array(
                        'id'=>$option['id'],
                        'report_id'=>$option['omc_sales_report_id'],
                        'report_option_name'=>$option['report_option_name'],
                        'report_option_link_type'=>$option['report_option_link_type'],
                        'report_option_link_id'=>$option['report_option_link_id'],
                        'report_option_order'=>$option['report_option_order'],
                        'report_is_total'=>$option['report_is_total'],
                        'report_total_option_list'=>$option['report_total_option_list'],
                        'report_total_field_list'=>$option['report_total_field_list']
                    );
                }
            }

            $reports_fields[$rpt['id']] = array(
                'id' => $rpt['id'],
                'menu_id' => $rpt['menu_id'],
                'name' => $rpt['report_name'],
                'description' => $rpt['report_description'],
                'order' => $rpt['report_order'],
                'omc_customer_list' => $rpt['omc_customer_list'],
                'primary_field_name' => $rpt['report_primary_field'],
                'primary_field_options'=>$primary_field_options_arr,
                'fields'=>$fields_arr
            );

        }

        $sale_forms = $this->OmcSalesForm->getAllSalesForms($company_profile['id']);

        $sale_form_options = $forms_fields = array();
        foreach($sale_forms as $form_arr){
            $frm = $form_arr['OmcSalesForm'];
            $sale_form_options[$frm['id']] = $frm['form_name'];
            $fields_arr = array();
            foreach($form_arr['OmcSalesFormField'] as $field){
                if($field['deleted'] == 'n'){
                    $fields_arr[$field['id']]=array(
                        'id'=>$field['id'],
                        'field_name'=>$field['field_name']
                    );
                }
            }
            //group form primary field options
            $primary_field_options_arr = array();
            foreach($form_arr['OmcSalesFormPrimaryFieldOption'] as $option){
                if($option['deleted'] == 'n'){
                    $primary_field_options_arr[$option['id']]=array(
                        'id'=>$option['id'],
                        'option_name'=>$option['option_name'],
                    );
                }
            }
            $forms_fields[$frm['id']] = array(
                'id' => $frm['id'],
                'name' => $frm['form_name'],
                'primary_field_options'=>$primary_field_options_arr,
                'fields'=>$fields_arr
            );
        }

        $all_reports_cells = $this->OmcSalesReportCell->getAllReportCells();
        $all_option_link_types = $this->Option->getDSRPLinkTypes($company_profile['id']);
        $omc_customers_list = $this->get_customer_list();
        $customers = array(array('id'=>'all', 'name'=> 'All Customers'));
        foreach($omc_customers_list as $data){
            $customers[] = array(
                'id' => $data['id'],
                'name' => $data['name']
            );
        }

        $this->set(compact('permissions','sale_reports','forms_fields','company_profile','sale_report_options','sale_form_options','reports_fields', 'all_reports_cells', 'customers', 'all_option_link_types'));
    }


    function station_sales(){
        $permissions = $this->action_permission;
        $company_profile = $this->global_company;

        if ($this->request->is('ajax')) {
            $this->autoRender = false;
            $this->autoLayout = false;
            $data = $this->request->data;
            $post = $this->sanitize($data);
            $station = $post['Query']['station'];
            $sales_form_id = $post['Query']['sales_form_id'];
            $sheet_date =  $this->covertDate($post['Query']['record_dt'],'mysql');

            $sales_records = $this->OmcCustomerDailySale->getFormSaleSheetForReport($station, $sales_form_id, $sheet_date);

            $view = new View($this, false);
            $view->set(compact('sales_records')); // set variables
            $view->viewPath = 'Elements/omc/'; // render an element
            $html = $view->render('preview_station'); // get the rendered markup

            return json_encode(array('code' => 0, 'msg' => 'Records Found!', 'html'=>$html));

        }

        $omc_customers_lists = $this->get_customer_list();
        $station_opt = array();
        foreach($omc_customers_lists as $data){
            $station_opt[$data['id']] = $data['name'];
        }
        $sales_forms = $this->OmcSalesForm->getSalesFormOnly($company_profile['id']);
        $form_sales_opt = array();
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

            $export_data = $this->OmcCustomerDailySale->getFormSaleSheetForExport($station, $sales_form_id, $sheet_date);

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
                $res = $this->convertToExcelBook($list_data, $filename);
                $objPHPExcel = $res['excel_obj'];
                $filename = $res['filename'];
            }

        }

        $this->autoLayout = false;

        $this->set(compact('objPHPExcel', 'download', 'filename'));
    }

}