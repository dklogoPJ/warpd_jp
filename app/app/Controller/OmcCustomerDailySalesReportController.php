<?php

/**
 * @name OmcCustomerDailySalesController.php
 */
App::import('Controller', 'OmcCustomerApp');

class OmcCustomerDailySalesReportController extends OmcCustomerAppController
{
    # Controller name

    var $name = 'OmcCustomerDailySalesReport';
    # set the model to use
    var $uses = array(
        'Menu','OmcSalesReport'
    );
    # Set the layout to use
    var $layout = 'omc_customer_layout';

    public function beforeFilter($param_array = null)
    {
        parent::beforeFilter();
    }

    function index($report_key = '', $record_dt = '') {
        $this->setPermission($report_key);
        $permissions = $this->action_permission;
        $company_profile = $this->global_company;
        $sales_report_date = date('Y-m-d');
        $report_id = null;

        if($record_dt) {
            $sales_report_date = $record_dt;
        }

        $last7days = array();
        for ($i =0; $i <= 6; $i++) {
            $day = date('Y-m-d', strtotime("-$i day", strtotime(date('Y-m-d'))));
            $last7days[$day] = $day;
        }

        $query = $this->OmcSalesReport->getSalesReportByKey($company_profile['omc_id'], $report_key);
        if($query) {
            $report_id = $query['OmcSalesReport']['id'];
        }

        $report_records = $this->OmcSalesReport->getReportWithData($company_profile['id'], $report_id, $sales_report_date);

        $menu = $this->Menu->getMenuByUrl('OmcCustomerDailySalesReport', $report_key);
        $menu_title = $menu['Menu']['menu_name'];

        $this->set(compact('permissions','company_profile','report_records','menu_title', 'sales_report_date', 'report_key', 'last7days'));
    }

}