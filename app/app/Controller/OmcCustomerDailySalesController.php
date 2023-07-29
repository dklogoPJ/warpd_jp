<?php

/**
 * @name OmcCustomerDailySalesController.php
 */
App::import('Controller', 'OmcCustomerApp');

class OmcCustomerDailySalesController extends OmcCustomerAppController
{
    # Controller name

    var $name = 'OmcCustomerDailySales';
    # set the model to use
    var $uses = array(
        'Menu','OmcSalesForm','OmcSalesRecord','OmcSalesSheet','OmcBulkStockPosition',
        'OmcBulkStockCalculation','OmcDailySalesProduct','OmcCashCreditSummary',
        'OmcOperatorsCredit','OmcCustomersCredit','OmcLube','OmcDsrpDataOption',
        'OmcCustomerOrder','OmcCustomer','ProductType','Volume','NctRecord','Nct',
        'OmcCustomerDailySale','OmcCustomerDailySaleField'
    );
    # Set the layout to use
    var $layout = 'omc_customer_layout';

    public function beforeFilter($param_array = null)
    {
        parent::beforeFilter();
    }


    function index($form_key = '') {
        $this->setPermission($form_key);
        $permissions = $this->action_permission;
        $company_profile = $this->global_company;
        $sheet_date = date('Y-m-d');
        $today_sales_sheet = date('Y-m-d');
        $previous_day_sales_sheet = date('Y-m-d',strtotime("-1 days"));

        if ($this->request->is('ajax')) {
            $this->autoRender = false;
            $this->autoLayout = false;
            //$authUser = $this->Auth->user();
            $post = $this->sanitize($_POST);
            $action_type = $post['form_action_type'];

            if($action_type == 'create_sales_sheet'){
                $new = $this->OmcCustomerDailySale->setupFormSaleSheet($company_profile['omc_id'], $company_profile['id'], $post['form_key'], $today_sales_sheet);
                if($new){
                    return json_encode(array('code' => 0, 'msg' => 'Sales sheet created!'));
                }
                else{
                    return json_encode(array('code' => 1, 'msg' => 'Could not create sales sheet!'));
                }
            } elseif ($action_type == 'delete_sales_sheet') {
                $new = $this->OmcCustomerDailySale->deleteFormSaleSheet($post['form_sales_sheet_id']);
                if($new){
                    return json_encode(array('code' => 0, 'msg' => 'Sales sheet deleted!'));
                }
                else{
                    return json_encode(array('code' => 1, 'msg' => 'Could not delete sales sheet!'));
                }
            } elseif ($action_type == 'form_save_sales_record') {
                if($this->OmcCustomerDailySaleField->saveAll($post['field_values'])) {
                    return json_encode(array('code' => 0, 'msg' => 'Sales sheet record saved!'));
                }
                else{
                    return json_encode(array('code' => 1, 'msg' => 'Could not save sales sheet record.'));
                }
            }
        }

        $current_day_records = $this->OmcCustomerDailySale->getFormSaleSheet($company_profile['omc_id'], $company_profile['id'], $form_key, $today_sales_sheet);
        //Get Previous Days Records
        $previous_day_records = $this->OmcCustomerDailySale->getFormSaleSheet($company_profile['omc_id'],$company_profile['id'], $form_key, $previous_day_sales_sheet);

       // debug($current_day_records);

        $price_change_data = array();
        foreach($this->price_change as $pn => $pr){
            $price_change_data[$pr['product_type_id']] = array(
                'name'=>$pn,
                'value'=>$pr['price']
            );
        }

        $menu = $this->Menu->getMenuByUrl('OmcCustomerDailySales', $form_key);
        $menu_title = $menu['Menu']['menu_name'];

        $sales_sheet_id = 0;
        if($current_day_records) {
            $sales_sheet_id = $current_day_records['form']['omc_customer_daily_sales_id'];
        }

        $this->set(compact('permissions','company_profile','price_change_data','previous_day_records','current_day_records','menu_title', 'form_key', 'sales_sheet_id'));
    }

    function indexOriginal(){
        $permissions = $this->action_permission;
        $company_profile = $this->global_company;
        $sheet_date = date('Y-m-d');

        if ($this->request->is('ajax')) {
            $this->autoRender = false;
            $this->autoLayout = false;
            $authUser = $this->Auth->user();
            $post = $this->sanitize($_POST);
            $sheet = $this->OmcSalesSheet->getSheet($company_profile['id'],$company_profile['omc_id'],$sheet_date);
            $sheet_id = 0;
            if($sheet){
                $sheet_id = $sheet['OmcSalesSheet']['id'];
            }
            else{
                $this->OmcSalesSheet->setUpSheet($company_profile['id'],$company_profile['omc_id']);
                $sheet = $this->OmcSalesSheet->getSheet($company_profile['id'],$company_profile['omc_id'],$sheet_date);
                $sheet_id = $sheet['OmcSalesSheet']['id'];
            }
            $post['sheet'] = $sheet_id;
            $record_id = intval($post['record_id']);
            $new = false;
            if($record_id == 0){//New Row or Record
                $new = true;
                $record_id = $this->OmcSalesRecord->createRecord($sheet_id,$post['form_id']);
            }
            foreach($post['field_values'] as $key => $d){
                $post['field_values'][$key]['omc_sales_record_id'] = $record_id;
                $post['field_values'][$key]['modified_by'] = $authUser['id'];
                if($new){
                    $post['field_values'][$key]['created_by'] = $authUser['id'];
                }
            }

            $action_type = $post['form_action_type'];
            //Form Save
            if($action_type == 'form_save'){
                if ($this->OmcSalesValue->saveAll($post['field_values'])) {
                    $rec = $this->OmcSalesRecord->getRecordById($record_id);
                    if($new){
                        return json_encode(array('code' => 0, 'msg' => 'Record Saved!', 'data'=>$rec));
                    }
                    else{
                        return json_encode(array('code' => 0, 'msg' => 'Record Updated!', 'data'=>$rec));
                    }
                }
                else {
                    echo json_encode(array('code' => 1, 'msg' => 'Some errors occurred whiles saving the record.'));
                }
            }
        }

       // $this->OmcSalesSheet->setUpSheet($company_profile['id'],$company_profile['omc_id']);

       // $this->OmcSalesForm->initPrePopulateForms($company_profile['omc_id'],$company_profile['id'],$sheet_date);

        $sale_forms_data = $this->OmcSalesForm->getAllSalesForms($company_profile['omc_id']);

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
                            'control_field'=>$field['control_field'],
                            'field_type_values'=>$field['field_type_values'],
                            'field_required'=>$field['field_required'],
                            'rule_type'=>$field['rule_type'],
                            'on_focus'=>$field['on_focus'],
                            'on_blur'=>$field['on_blur'],
                            'on_change'=>$field['on_change'],
                            'before_render'=>$field['before_render'],
                            'after_render'=>$field['after_render']
                        );
                    }
                }
                //Get form Values
                $form_data_records = array();
                $form_data_record_raw = $this->OmcSalesSheet->getFormData($form['id'],$company_profile['id'],$company_profile['omc_id'],$sheet_date);
                $form_data_records = $form_data_record_raw['data'];
                $forms_n_fields[$form['id']] = array(
                    'id' => $form['id'],
                    'name' => $form['form_name'],
                    'render_type' => $form['render_type'],
                    'fields'=>$fields_arr,
                    'values'=>$form_data_records
                );
            }
        }

        //Get Previous Days Records
        $previous_day_records = $this->OmcSalesForm->getPreviousDayData($company_profile['omc_id'],$company_profile['id']);
        $current_day_records = $this->OmcSalesForm->getCurrentDayData($company_profile['omc_id'],$company_profile['id']);
        //debug($current_day_records);

        $price_change_data = array();
        foreach($this->price_change as $pn => $pr){
            $price_change_data[$pr['product_type_id']] = array(
                'name'=>$pn,
                'value'=>$pr['price']
            );
        }

        //debug($forms_n_fields);
        $this->set(compact('permissions','company_profile','forms_n_fields','price_change_data','previous_day_records','current_day_records'));
    }


    public function bulk_stock_position(){
        $permissions = $this->action_permission;
        $company_profile = $this->global_company;
        if ($this->request->is('ajax')) {
            $this->autoRender = false;
            $this->autoLayout = false;
            $authUser = $this->Auth->user();
            $post = $this->sanitize($_POST);
            $post['modified_by'] = $authUser['id'];

            if ($this->OmcBulkStockPosition->save($post)) {
                //Update Customer Stock module
                $this->OmcBulkStockPosition->update_stock_level($post,$company_profile['omc_id'],$company_profile['id'],$authUser['id']);
                return json_encode(array('code' => 0, 'msg' => 'Record Saved!'));
            }
            else {
                return json_encode(array('code' => 1, 'msg' => 'Some errors occurred whiles saving the record.'));
            }
        }

        $sheet_id = $this->OmcSalesSheet->setUpSheet($company_profile['id'],$company_profile['omc_id']);
        $form_data = $this->OmcBulkStockPosition->setUp($sheet_id,$company_profile['omc_id']);
        $table_setup = $this->OmcBulkStockPosition->getTableSetup();

        $this->set(compact('permissions','form_data','table_setup'));
    }


    public function bulk_stock_calculation(){
        $permissions = $this->action_permission;
        $company_profile = $this->global_company;
        if ($this->request->is('ajax')) {
            $this->autoRender = false;
            $this->autoLayout = false;
            $authUser = $this->Auth->user();
            $post = $this->sanitize($_POST);
            $post['modified_by'] = $authUser['id'];

            if ($this->OmcBulkStockCalculation->save($post)) {
                return json_encode(array('code' => 0, 'msg' => 'Record Saved!'));
            }
            else {
                return json_encode(array('code' => 1, 'msg' => 'Some errors occurred whiles saving the record.'));
            }
        }

        $sheet_id = $this->OmcSalesSheet->setUpSheet($company_profile['id'],$company_profile['omc_id']);
        $form_data = $this->OmcBulkStockCalculation->setUp($sheet_id,$company_profile['omc_id']);
        $table_setup = $this->OmcBulkStockCalculation->getTableSetup();
        $stock_position_data_raw = $this->OmcBulkStockPosition->getData($sheet_id);
        $stock_position_data = array();
        foreach($stock_position_data_raw as $spd){
            $stock_position_data[] = $spd['OmcBulkStockPosition'];
        }
        $this->set(compact('permissions','form_data','table_setup','stock_position_data'));
    }


    public function daily_sales_products(){
        $permissions = $this->action_permission;
        $company_profile = $this->global_company;
        $sheet_id = $this->OmcSalesSheet->setUpSheet($company_profile['id'],$company_profile['omc_id']);
        if ($this->request->is('ajax')) {
            $this->autoRender = false;
            $this->autoLayout = false;
            $authUser = $this->Auth->user();
            $post = $this->sanitize($_POST);
            $post['modified_by'] = $authUser['id'];
            $post = $this->total_daily_sales_product($post);
            if ($this->OmcDailySalesProduct->save($post)) {
                //Update Operators Credit
                $this->OmcOperatorsCredit->setUp($sheet_id,$company_profile['id'],$company_profile['omc_id']);
                return json_encode(array('code' => 0, 'msg' => 'Record Saved!', 'data'=>$post));
            }
            else {
                return json_encode(array('code' => 1, 'msg' => 'Some errors occurred whiles saving the record.'));
            }
        }
        $form_data = $this->OmcDailySalesProduct->setUp($sheet_id,$company_profile['omc_id']);
        $table_setup = $this->OmcDailySalesProduct->getTableSetup();
        $table_total_setup = $this->OmcDailySalesProduct->getTotalTableSetup();
        $previous_data_raw = $this->OmcDailySalesProduct->getPreviousDayData($company_profile['id'],$company_profile['omc_id']);
        $previous_data = array();
        foreach($previous_data_raw as $spd){
            $previous_data[] = $spd['OmcDailySalesProduct'];
        }
        $data = $this->OmcDsrpDataOption->find('first',array(
            'conditions'=>array('omc_id'=>$company_profile['omc_id']),
            'recursive'=>-1
        ));
        $control_data = array();
        $control_data['bulk_stock_position_products'] = unserialize($data['OmcDsrpDataOption']['bulk_stock_position_products']);
        $control_data['daily_sales_products'] = unserialize($data['OmcDsrpDataOption']['daily_sales_products']);
        $control_data['lubricants_products'] = unserialize($data['OmcDsrpDataOption']['lubricants_products']);

        $price_change_data = array();
        foreach($this->price_change as $pn => $pr){
            $price_change_data[$pr['product_type_id']] = array(
                'name'=>$pn,
                'value'=>$pr['price']
            );
        }


        $this->set(compact('permissions','form_data','table_setup','previous_data','control_data','price_change_data','table_total_setup'));
    }


    private function total_daily_sales_product ($param){
        //Total Quantity
        $total_day_sales_qty = floatval($param['cash_day_sales_qty']) + floatval($param['dealer_credit_day_sales_qty']) + floatval($param['customers_day_sales_qty']) ;
        //Total Value
        $total_day_sales_value = floatval($param['cash_day_sales_value']) + floatval($param['dealer_credit_day_sales_value']) + floatval($param['customers_day_sales_value']);
        //Total Prev Qty
        $total_previous_day_sales_qty = floatval($param['cash_previous_day_sales_qty']) + floatval($param['dealer_credit_previous_day_sales_qty']) + floatval($param['customers_previous_day_sales_qty']);
        //Total Prev Value
        $total_previous_day_sales_value = floatval($param['cash_previous_day_sales_value']) + floatval($param['dealer_credit_previous_day_sales_value']) + floatval($param['customers_previous_day_sales_value']);
        //Total Month Qty
        $total_month_to_date_qty = floatval($param['cash_month_to_date_qty']) + floatval($param['dealer_credit_month_to_date_qty']) + floatval($param['customers_month_to_date_qty']);
        //Total Month Value
        $total_month_to_date_value = floatval($param['cash_month_to_date_value']) + floatval($param['dealer_credit_month_to_date_value']) + floatval($param['customers_month_to_date_value']);

        $param['total_day_sales_qty'] = $total_day_sales_qty;
        $param['total_day_sales_value'] = $total_day_sales_value;
        $param['total_previous_day_sales_qty'] = $total_previous_day_sales_qty;
        $param['total_previous_day_sales_value'] = $total_previous_day_sales_value;
        $param['total_month_to_date_qty'] = $total_month_to_date_qty;
        $param['total_month_to_date_value'] = $total_month_to_date_value;

        return $param;
    }


    public function cash_credit_summary(){
        $permissions = $this->action_permission;
        $company_profile = $this->global_company;
        $sheet_id = $this->OmcSalesSheet->setUpSheet($company_profile['id'],$company_profile['omc_id']);
        if ($this->request->is('ajax')) {
            $this->autoRender = false;
            $this->autoLayout = false;
            $authUser = $this->Auth->user();
            $post = $this->sanitize($_POST);
            $post['modified_by'] = $authUser['id'];
            if ($this->OmcCashCreditSummary->save($post)) {
                $updated_data = $this->OmcCashCreditSummary->setUp($sheet_id);
                //Update Operators Credit
                $this->OmcOperatorsCredit->setUp($sheet_id,$company_profile['id'],$company_profile['omc_id']);
                return json_encode(array('code' => 0, 'msg' => 'Record Saved!','data'=>$updated_data));
            }
            else {
                return json_encode(array('code' => 1, 'msg' => 'Some errors occurred whiles saving the record.'));
            }
        }
        $form_data = $this->OmcCashCreditSummary->setUp($sheet_id);
        $table_setup = $this->OmcCashCreditSummary->getTableSetup();

        $this->set(compact('permissions','form_data','table_setup'));
    }


    public function operators_credit(){
        $permissions = $this->action_permission;
        $company_profile = $this->global_company;
        if ($this->request->is('ajax')) {
            $this->autoRender = false;
            $this->autoLayout = false;
            $authUser = $this->Auth->user();
            $post = $this->sanitize($_POST);
            $post['modified_by'] = $authUser['id'];

            if ($this->OmcOperatorsCredit->save($post)) {
                $sheet_id = $this->OmcSalesSheet->setUpSheet($company_profile['id'],$company_profile['omc_id']);
                $updated_data = $this->OmcOperatorsCredit->setUp($sheet_id,$company_profile['id'],$company_profile['omc_id']);
                return json_encode(array('code' => 0, 'msg' => 'Record Saved!','data'=>$updated_data));
            }
            else {
                return json_encode(array('code' => 1, 'msg' => 'Some errors occurred whiles saving the record.'));
            }
        }
        $sheet_id = $this->OmcSalesSheet->setUpSheet($company_profile['id'],$company_profile['omc_id']);
        $form_data = $this->OmcOperatorsCredit->setUp($sheet_id,$company_profile['id'],$company_profile['omc_id']);
        $table_setup = $this->OmcOperatorsCredit->getTableSetup();

        $this->set(compact('permissions','form_data','table_setup'));
    }


    public function customers_credit(){
        $permissions = $this->action_permission;
        $company_profile = $this->global_company;
        if ($this->request->is('ajax')) {
            $this->autoRender = false;
            $this->autoLayout = false;
            $authUser = $this->Auth->user();
            $post = $this->sanitize($_POST);
            $post['modified_by'] = $authUser['id'];

            if ($this->OmcCustomersCredit->save($post)) {
                $sheet_id = $this->OmcSalesSheet->setUpSheet($company_profile['id'],$company_profile['omc_id']);
                $updated_data = $this->OmcCustomersCredit->setUp($sheet_id,$company_profile['id'],$company_profile['omc_id']);
                return json_encode(array('code' => 0, 'msg' => 'Record Saved!','data'=>$updated_data));
            }
            else {
                return json_encode(array('code' => 1, 'msg' => 'Some errors occurred whiles saving the record.'));
            }
        }
        $sheet_id = $this->OmcSalesSheet->setUpSheet($company_profile['id'],$company_profile['omc_id']);
        $form_data = $this->OmcCustomersCredit->setUp($sheet_id,$company_profile['id'],$company_profile['omc_id']);
        $table_setup = $this->OmcCustomersCredit->getTableSetup();

        $this->set(compact('permissions','form_data','table_setup'));
    }


    public function lubricants(){
        $permissions = $this->action_permission;
        $company_profile = $this->global_company;
        if ($this->request->is('ajax')) {
            $this->autoRender = false;
            $this->autoLayout = false;
            $authUser = $this->Auth->user();
            $post = $this->sanitize($_POST);
            $post['modified_by'] = $authUser['id'];

            if ($this->OmcLube->save($post)) {
                return json_encode(array('code' => 0, 'msg' => 'Record Saved!'));
            }
            else {
                return json_encode(array('code' => 1, 'msg' => 'Some errors occurred whiles saving the record.'));
            }
        }
        $sheet_id = $this->OmcSalesSheet->setUpSheet($company_profile['id'],$company_profile['omc_id']);
        $form_data = $this->OmcLube->setUp($sheet_id,$company_profile['omc_id']);
        $table_setup = $this->OmcLube->getTableSetup();
        $previous_data_raw = $this->OmcLube->getPreviousDayData($company_profile['id'],$company_profile['omc_id']);
        $previous_data = array();
        foreach($previous_data_raw as $spd){
            $previous_data[] = $spd['OmcLube'];
        }
        $data = $this->OmcDsrpDataOption->find('first',array(
            'conditions'=>array('omc_id'=>$company_profile['omc_id']),
            'recursive'=>-1
        ));
        $control_data = array();
        $control_data['lubricants_products'] = unserialize($data['OmcDsrpDataOption']['lubricants_products']);

        $price_change_data = array();
        foreach($this->price_change as $pn => $pr){
            $price_change_data[$pr['product_type_id']] = array(
                'name'=>$pn,
                'value'=>$pr['price']
            );
        }

        $liter_setup = $this->OmcLube->getLubesLiterData();

        $this->set(compact('permissions','form_data','table_setup','previous_data','control_data','price_change_data','liter_setup'));
    }


    function dsrp_report (){
        $company_profile = $this->global_company;
        $default_month = date('m');
        $default_year = date('Y');
        $default_day = date('d');
        $default_customer = $company_profile['id'];
        $omc_id = $company_profile['omc_id'];
        $default_dsrp = 'bsp';

        if($this->request->is('post')){
            $default_month = $this->request->data['Query']['month'];
            $default_year = $this->request->data['Query']['year'];
            $default_day = $this->request->data['Query']['day'];
            $default_dsrp = $this->request->data['Query']['dsrp_opt'];
        }
        $full_date = $default_year.'-'.$default_month.'-'.$default_day;
        $g_data = $this->get_dsrp_data($full_date,$omc_id,$default_customer,$default_dsrp);

        $start_year = $this->OmcSalesSheet->getStartYear('omc_customer',$company_profile['id']);
        $month_list = $this->getMonths();
        $year_list = $this->getYears($start_year);
        $day_list = $this->getDays();
        $dsrp_list = $this->getDSRPoptions();

        $month_name = $this->getMonths($default_month);
        $get_dsrp_name = $this->getDSRPoptions($default_dsrp);
        $dsrp_name = ($get_dsrp_name) && !empty($get_dsrp_name) ? $get_dsrp_name : 'DSRP';
        $table_title = $this->__add_ordinal_suffix($default_day).'-'.$month_name.'-'.$default_year.', '.$dsrp_name.' Report ';

        $controller = $this;
        $this->set(compact('controller','table_title','default_customer','default_year','default_month','default_day','default_dsrp','month_list','year_list','day_list','dsrp_list','g_data'));
    }


    function get_dsrp_data($sheet_date,$omc_id,$customer_id,$default_dsrp){
        $sheet = $this->OmcSalesSheet->getSheet($customer_id,$omc_id,$sheet_date);
        if($sheet){
            $model = '';
            if($default_dsrp == 'bsp'){
                $model = 'OmcBulkStockPosition';
            }
            elseif($default_dsrp == 'bsc'){
                $model = 'OmcBulkStockCalculation';
            }
            elseif($default_dsrp == 'dsp'){
                $model = 'OmcDailySalesProduct';
            }
            elseif($default_dsrp == 'ccs'){
                $model = 'OmcCashCreditSummary';
            }
            elseif($default_dsrp == 'opc'){
                $model = 'OmcOperatorsCredit';
            }
            elseif($default_dsrp == 'cmc'){
                $model = 'OmcCustomersCredit';
            }
            elseif($default_dsrp == 'lbp'){
                $model = 'OmcLube';
            }
            if(empty($model)){
                return false;
            }
            else{
                $sheet_id = $sheet['OmcSalesSheet']['id'];
                $gdata = $this->$model->getData($sheet_id);
                $header = $this->$model->getFullHeader();
               /* if( $model == 'OmcDailySalesProduct'){
                    $header = $this->$model->getFullHeader();
                }*/
                if($gdata){
                    return array(
                        'header'=>$header,
                        'data'=>$gdata
                    );
                }
                else{
                    return false;
                }
            }
        }
        else{
            return false;
        }
    }


    function export_dsrp(){
        $this->autoLayout = false;
        $download = false;
        $company_profile = $this->global_company;
        $customer = $company_profile['id'];
        if($this->request->is('post')){
            $dsrp_opt = $_POST['data_dsrp_type'];
            $month = $_POST['data_month'];
            $year = $_POST['data_year'];
            $day = $_POST['data_day'];
            $media_type = $_POST['data_doc_type'];
        }
        $full_date = $year.'-'.$month.'-'.$day;
        $omc_id = $company_profile['omc_id'];
        $g_data_raw = $this->get_dsrp_data($full_date,$omc_id,$customer,$dsrp_opt);
        $g_data = $this->process_export_dsrp_data($g_data_raw,$dsrp_opt);
        $month_name = $this->getMonths($month);
        $get_dsrp_name = $this->getDSRPoptions($dsrp_opt);
        $dsrp_name = ($get_dsrp_name) && !empty($get_dsrp_name) ? $get_dsrp_name : 'DSRP';
        $table_title = $this->__add_ordinal_suffix($day).'-'.$month_name.'-'.$year.', '.$dsrp_name.' Report ';

        $list_headers = $g_data['header'];
        $list_data = $g_data['data'];

        if($g_data_raw){
            $download = true;
        }
        $filename = $table_title;
        $res = array('excel_obj'=>'','filename'=>'');
        if($download){
            $res = $this->convertToExcel($list_headers,$list_data,$filename);
        }
        $objPHPExcel = $res['excel_obj'];
        $filename = $res['filename'];

        $controller = $this;

        $this->set(compact('controller','company_profile','table_title','objPHPExcel', 'download', 'filename','g_data'));

    }



    function process_export_dsrp_data($data,$dsrp_opt){
        $return_arr = array('header'=>array(),'data'=>array());
        if(!$data){
            return $return_arr;
        }
        $table_setup = $data['header'];
        $form_data = $data['data'];
        $with_header = in_array($dsrp_opt,array('ccs','opc','cmc')) ? false:true;
        $model = '';
        if($dsrp_opt == 'bsp'){
            $model = 'OmcBulkStockPosition';
        }
        elseif($dsrp_opt == 'bsc'){
            $model = 'OmcBulkStockCalculation';
        }
        elseif($dsrp_opt == 'dsp'){
            $model = 'OmcDailySalesProduct';
        }
        elseif($dsrp_opt == 'ccs'){
            $model = 'OmcCashCreditSummary';
        }
        elseif($dsrp_opt == 'opc'){
            $model = 'OmcOperatorsCredit';
        }
        elseif($dsrp_opt == 'cmc'){
            $model = 'OmcCustomersCredit';
        }
        elseif($dsrp_opt == 'lbp'){
            $model = 'OmcLube';
        }

        if($with_header){
            foreach($table_setup as $row){
                $return_arr['header'][] = $row['header'].' '.$row['unit'];
            }
            foreach($form_data as $row){
                $new_row = array();
                foreach($table_setup as $tr_row){
                    $field = $tr_row['field'];
                    $format = $tr_row['format'];
                    $cell_value = $field_value = $row[$model][$field];
                    if(is_numeric($field_value)){
                        $decimal_places = 0;
                        if($format == 'float'){
                            $decimal_places = 2;
                        }
                        $cell_value = $this->formatNumber($cell_value,'money',$decimal_places);
                    }
                    $new_row[] = $cell_value;
                }
                $return_arr['data'][] = $new_row;
            }
        }
        else{
            $return_arr['header'][] = '';
            $return_arr['header'][] = '';

            foreach($table_setup as $row){
                $new_row = array();
                $header = $row['header'];
                $field = $row['field'];
                $format = $row['format'];
                $cell_value = $field_value = $form_data[0][$model][$field];
                if(is_numeric($field_value)){
                    $decimal_places = 0;
                    if($format == 'float'){
                        $decimal_places = 2;
                    }
                    $cell_value = $this->formatNumber($cell_value,'money',$decimal_places);
                }
                $new_row[] = $header;
                $new_row[] = $cell_value;
                $return_arr['data'][] = $new_row;
            }
        }

        return $return_arr;
    }



    public function add_dsrp_options(){
        $this->autoRender = false;
        $this->autoLayout = false;
        $company_profile = $this->global_company;

        $products = array(
            array('key' => 'exdrum_xl_super_20w/50','value' => 'EX-DRUM XL Super 20W/50'),
            array('key' => 'exdrum_oleum_hd_30','value' => 'EX-DRUM Oleum HD 30'),
            array('key' => 'exdrum_oleum_hd_40','value' => 'EX-DRUM Oleum HD 40'),
            array('key' => 'exdrum_gear_oil_ep_90','value' => 'EX-DRUM Gear Oil Ep 90'),
            array('key' => 'exdrum_gear_oil_ep_140','value' => 'EX-DRUM Gear Oil Ep 140'),
            array('key' => 'exdrum_regent_xs_40','value' => 'EX-DRUM Regent XS 40'),
            array('key' => 'exkeg_oleum_1','value' => 'EX-KEG Oleum 1'),
            array('key' => 'exkeg_oleum_2','value' => 'EX-KEG Oleum 2'),
            array('key' => 'exkeg_oleum_3','value' => 'EX-KEG Oleum 3'),
            array('key' => 'exkeg_multi_purpose_grease','value' => 'EX-KEG Multi-Purpose Grease'),
            array('key' => '4lttins_xl_super_20w/50','value' => '4Lt. TINS XL Super 20W/50'),
            array('key' => '4lttins_oleum_hd_30','value' => '4Lt. TINS Oleum HD 30'),
            array('key' => '4lttins_oleum_hd_40','value' => '4Lt. TINS Oleum HD 40'),
            array('key' => '4lttins_gear_oil_ep_90','value' => '4Lt. TINS Gear Oil Ep 90'),
            array('key' => '4lttins_gear_oil_ep_140','value' => '4Lt. TINS Gear Oil Ep 140'),
            array('key' => '4lttins_maintain','value' => '4Lt. TINS Maintain'),
            array('key' => '4lttins_oleum_sae_40','value' => '4Lt. TINS Oleum SAE 40'),
            array('key' => '1lttins_atf_dexron_2','value' => '1Lt. TINS ATF Dexron II'),
            array('key' => 'lpg50kg','value' => 'LPG 50kg'),
            array('key' => 'lpg25kg','value' => 'LPG 25kg'),
            array('key' => 'lpg12.5kg','value' => 'LPG 12.5kg'),
            array('key' => 'lpg5kg','value' => 'LPG 5kg'),
            array('key' => 'cooker','value' => 'Cooker'),
            array('key' => 'uniflitt','value' => 'UNIFLITT'),
            array('key' => 'tba','value' => 'T.B.A')
        );

        $array_string = serialize($products);
        $modObj = ClassRegistry::init('OmcDsrpDataOption');
        $modObj->updateAll(
            $this->sanitize(array('OmcDsrpDataOption.lubricants_products' => "'".$array_string."'")),
            $this->sanitize(array('OmcDsrpDataOption.omc_id' => $company_profile['omc_id']))
        );

        $data = $modObj->find('first',array(
            'conditions'=>array('omc_id'=>$company_profile['omc_id']),
            'recursive'=>-1
        ));
        $arr = unserialize($data['OmcDsrpDataOption']['lubricants_products']);
        debug($arr);
    }




    function pump_tank_sales($type = 'get')
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
                    $sortorder = isset($_POST['sortorder']) ? $_POST['sortorder'] : 'desc';
                    /** Sort order */
                    $qtype = isset($_POST['qtype']) ? $_POST['qtype'] : '';
                    /** Search column */
                    $search_query = isset($_POST['query']) ? $_POST['query'] : '';
                    /** @var $filter  */
                    $filter_status =   isset($_POST['filter_status']) ? $_POST['filter_status'] : 'complete_orders' ;
                    /** Search string */
                    $rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
                    $limit = $rp;
                    $start = ($page - 1) * $rp;

                    //get users id for this company only
                    $condition_array = array(
                        'OmcCustomerOrder.omc_customer_id' => $company_profile['id'],
                        'OmcCustomerOrder.deleted' => 'n'
                    );

                    $contain = array(
                        'Omc'=>array('fields' => array('Omc.id', 'Omc.name')),
                        'ProductType'=>array('fields' => array('ProductType.id', 'ProductType.name')),
                        'OmcCustomer'=>array('fields' => array('OmcCustomer.id', 'OmcCustomer.name'))
                    );

                    $data_table = $this->OmcCustomerOrder->find('all', array('conditions' => $condition_array, 'contain'=>$contain,'order' => "OmcCustomerOrder.$sortname $sortorder", 'limit' => $start . ',' . $limit, 'recursive' => 1));
                    $data_table_count = $this->OmcCustomerOrder->find('count', array('conditions' => $condition_array, 'recursive' => -1));
                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {
                            $bigger_time = date('Y-m-d H:i:s');
                            if($obj['OmcCustomerOrder']['order_status'] == 'Complete'){
                                $bigger_time = $obj['OmcCustomerOrder']['omc_modified'];
                                $time_hr = $this->count_time_between_dates($obj['OmcCustomerOrder']['dealer_created'],$bigger_time,'hours');
                                // $time_days = $this->count_time_between_dates($obj['Order']['omc_created'],$bigger_time,'days');
                                $order_time_elapsed = $time_hr.' hr(s)';
                            }
                            else{
                                $time_hr = $this->count_time_between_dates($obj['OmcCustomerOrder']['dealer_created'],$bigger_time,'hours');
                                // $time_days = $this->count_time_between_dates($obj['Order']['omc_created'],$bigger_time,'days');
                                $order_time_elapsed =  $time_hr.' hr(s)';
                            }

                            $delivery_quantity =  isset($obj['OmcCustomerOrder']['delivery_quantity']) ? $this->formatNumber($obj['OmcCustomerOrder']['delivery_quantity'],'number',0) : '';
                            $received_quantity =  isset($obj['OmcCustomerOrder']['received_quantity']) ? $this->formatNumber($obj['OmcCustomerOrder']['received_quantity'],'number',0) : '';
                            $delivery_date =  isset($obj['OmcCustomerOrder']['delivery_date']) ? $this->covertDate($obj['OmcCustomerOrder']['delivery_date'],'mysql_flip') : '';
                            
                        
                            $return_arr[] = array(
                                'id' => $obj['OmcCustomerOrder']['id'],
                                'cell' => array(
                                    $id='',
                                    $tank = '',
                                    $open_stock = '',
                                    $received_quantity = '',
                                    $volume_depot = '',
                                    $pump_day_sales = '',
                                    $closing_stock = '',
                                    $tank_day_sales = '',
                                    $variance = '',
                                    $variance_cedis = '',
                                    $comments = '',
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
                    $data = array('OmcCustomerOrder' => $_POST);

                    if ($this->OmcCustomerOrder->save($this->sanitize($data))) {
                        $order_id  = $this->OmcCustomerOrder->id;
                        //Array Data here 
                        //Activity Log
                        $log_description = $this->getLogMessage('UpdateDeliveryQuantity')." (Order #".$order_id.")";
                        $this->logActivity('Order',$log_description);

                        if($_POST['id'] > 0){
                            return json_encode(array('code' => 0, 'msg' => 'Data Updated!'));
                        }
                        else{
                            return json_encode(array('code' => 0, 'msg' => 'Data Saved', 'id'=>$order_id));
                        }
                    } else {
                        echo json_encode(array('code' => 1, 'msg' => 'Some errors occurred.'));
                    }
                    //echo debug($data);
                    break;

                case 'load':

                    break;
            }
        }

        $products_lists = $this->get_products();
        $start_dt = date('01-m-Y');
        $end_dt = date('t-m-Y');
        $group_by = 'monthly';
        $group_by_title = date('F');

        
        /* $bdclists =array(array('name'=>'All','value'=>0));
         foreach($bdclists_data as $arr){
             $bdclists[] = array('name'=>$arr['name'],'value'=>$arr['id']);
         }*/

        $order_filter = $this->order_filter;

        $g_data =  $this->get_orders($start_dt,$end_dt,$group_by,null);

        $volumes = $this->Volume->getVolsList();
     

        $graph_title = $group_by_title.", Orders-Consolidated";

        $this->set(compact('grid_data','omc_customers_lists','volumes','permissions','depot_lists', 'products_lists','bdc_list','graph_title','g_data','bdclists','order_filter','list_tm'));
    }


    function nct_sales_record($type = 'get')
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
                    $sortorder = isset($_POST['sortorder']) ? $_POST['sortorder'] : 'desc';
                    /** Sort order */
                    $qtype = isset($_POST['qtype']) ? $_POST['qtype'] : '';
                    /** Search column */
                    $search_query = isset($_POST['query']) ? $_POST['query'] : '';
                    /** @var $filter  */
                    $filter_status =   isset($_POST['filter_status']) ? $_POST['filter_status'] : 'complete_orders' ;
                    /** Search string */
                    $rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
                    $limit = $rp;
                    $start = ($page - 1) * $rp;

                    //get users id for this company only
                    $condition_array = array(
                        'NctRecord.omc_customer_id' => $company_profile['id'],
                        'NctRecord.deleted' => 'n'
                    );

                    $contain = array(
                        'OmcCustomer'=>array('fields' => array('OmcCustomer.id', 'OmcCustomer.name'))
                    );

                    $data_table = $this->NctRecord->find('all', array('conditions' => $condition_array, 'contain'=>$contain,'order' => "NctRecord.$sortname $sortorder", 'limit' => $start . ',' . $limit, 'recursive' => 1));
                    $data_table_count = $this->NctRecord->find('count', array('conditions' => $condition_array, 'recursive' => -1));
                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {
                            $return_arr[] = array(
                                'id' => $obj['NctRecord']['id'],
                                'cell' => array(
                                    $obj['NctRecord']['id'],
                                    $this->covertDate($obj['NctRecord']['record_date'], 'mysql_flip'),
                                    $obj['NctRecord']['nct_channel'],
                                    $this->formatNumber($obj['NctRecord']['amount'], 'number', 0)
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
                        if (!in_array('A', $permissions)) {
                            return json_encode(array('code' => 1, 'msg' => 'Access Denied.'));
                        }
                    } else {
                        if (!in_array('E', $permissions)) {
                            return json_encode(array('code' => 1, 'msg' => 'Access Denied.'));
                        }
                    }

                    $data = array('NctRecord' => $_POST);
                    if ($_POST['id'] == 0) {
                        $data['NctRecord']['created_by'] = $authUser['id'];
                    } else {
                        $data['NctRecord']['modified_by'] = $authUser['id'];
                    }
                    $data['NctRecord']['omc_customer_id'] = $company_profile['id'];
                    $data['NctRecord']['amount'] = str_replace(',', '', $_POST['amount']);
                    $data['NctRecord']['record_date'] = $this->covertDate($_POST['record_date'], 'mysql') . ' ' . date('H:i:s');

                    if ($this->NctRecord->save($this->sanitize($data))) {
                        $nct_record_id  = $this->NctRecord->id;
                        //Array Data here
                        //Activity Log
                        $log_description = $this->getLogMessage('UpdateNctRecord')." (Nct Record #".$nct_record_id.")";
                        $this->logActivity('Order',$log_description);

                        if($_POST['id'] > 0){
                            return json_encode(array('code' => 0, 'msg' => 'Data Updated!'));
                        }
                        else{
                            return json_encode(array('code' => 0, 'msg' => 'Data Saved', 'id'=>$nct_record_id));
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
                        $modObj = ClassRegistry::init('NctRecord');
                        $result = $modObj->updateAll(
                            array('NctRecord.deleted' => "'y'"),
                            array('NctRecord.id' => $ids)
                        );
                        if ($result) {
                            $modObj = ClassRegistry::init('NctRecord');
                            $modObj->updateAll(
                                array('NctRecord.deleted' => "'y'"),
                                array('NctRecord.id' => $ids)
                            );

                         echo json_encode(array('code' => 0, 'msg' => 'Data Deleted!'));
                        } else {
                            echo json_encode(array('code' => 1, 'msg' => 'Data cannot be deleted'));
                        }
                        break;
            }
        }

        $products_lists = $this->get_products();
        $start_dt = date('01-m-Y');
        $end_dt = date('t-m-Y');
        $group_by = 'monthly';
        $group_by_title = date('F');

        $order_filter = $this->order_filter;

        $g_data =  $this->get_orders($start_dt,$end_dt,$group_by,null);

        $volumes = $this->Volume->getVolsList();
        $nct = $this->Nct->getNctList();
        $omc_customer = $this->OmcCustomer->getOmcCustomerList();

        $graph_title = $group_by_title.", Orders-Consolidated";

        $this->set(compact('grid_data','omc_customers_lists','volumes','permissions','depot_lists', 'products_lists','bdc_list','graph_title','g_data','bdclists','order_filter','list_tm','nct','omc_customer'));
    }

}