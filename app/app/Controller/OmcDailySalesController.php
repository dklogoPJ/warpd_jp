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
    var $uses = array('OmcSalesSheet','OmcSalesRecord','OmcSalesValue','OmcSalesFormField','OmcSalesForm',
        'OmcCustomer','Menu','OmcSalesFormPrimaryFieldOption','SalesFormElementEvent','SalesFormElementAction',
        'SalesFormElementOperand','ProductType','OmcCustomerDailySale','LpgSetting','LubeSetting'
    );

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
                    'option_link_id'=>$post['pf_option_link_id'],
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
                'primary_field_name' => $form['primary_field'],
                'primary_field_options'=>$primary_field_options_arr,
                'fields'=>$fields_arr
            );

        }

        $sale_form_element_events = $this->SalesFormElementEvent->getKeyValuePair();
        $sale_form_element_actions = $this->SalesFormElementAction->getKeyValuePair();
        $sale_form_element_operands = $this->SalesFormElementOperand->getKeyValuePair();
        $all_option_link_types = array(
            array('id'=>'', 'name'=>'None', 'data'=>array(), 'columns'=>array(
                array('id'=>'', 'name'=>'None')
            )),
            array('id'=>'products', 'name'=>'Products', 'data'=>$this->ProductType->getProductList(), //TODO get AND FILTER the product list for the OMC
                'columns'=>array(
                    array('id'=>'products:price', 'name'=>'Products: price')
                )
            ),
            array('id'=>'lpg_settings', 'name'=>'LPG Settings', 'data'=>$this->LpgSetting->getProductList($company_profile['id']),
                'columns'=>array(
                    array('id'=>'lpg_settings:unit_volume', 'name'=>'LPG Settings: unit_volume'),
                    array('id'=>'lpg_settings:unit_price', 'name'=>'LPG Settings: unit_price'),
                    array('id'=>'lpg_settings:price_per_kg', 'name'=>'LPG Settings:price_per_kg')
                )
            ),
            array('id'=>'lube_settings', 'name'=>'Lube Settings', 'data'=>$this->LubeSetting->getProductList($company_profile['id']),
                'columns'=>array(
                    array('id'=>'lube_settings:unit_volume', 'name'=>'Lube Settings: unit_volume'),
                    array('id'=>'lube_settings:total_qty_per_pack', 'name'=>'Lube Settings: total_qty_per_pack'),
                    array('id'=>'lube_settings:pack_volume', 'name'=>'Lube Settings: pack_volume'),
                    array('id'=>'lube_settings:unit_cost_price', 'name'=>'Lube Settings: unit_cost_price'),
                    array('id'=>'lube_settings:unit_selling_price', 'name'=>'Lube Settings: unit_selling_price'),
                    array('id'=>'lube_settings:price_per_ltr', 'name'=>'Lube Settings: price_per_ltr')
                )
            )
        );

        $omc_customers_list = $this->get_customer_list();
        $customers = array(array('id'=>'all', 'name'=> 'All Customers'));
        foreach($omc_customers_list as $data){
            $customers[] = array(
                'id' => $data['id'],
                'name' => $data['name']
            );
        }

        $this->set(compact('permissions','sale_forms','company_profile','sale_form_options','forms_fields','sale_form_element_events','sale_form_element_actions','sale_form_element_operands', 'customers', 'all_option_link_types'));
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