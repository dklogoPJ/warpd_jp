<?php

/**
 * @name OmcController.php
 */
App::import('Controller', 'OmcApp');

class OmcOperationsController extends OmcAppController
{
    # Controller name

    var $name = 'OmcOperations';
    # set the model to use
    var $uses = array('BdcDistribution','OmcBdcDistribution', 'OmcCustomer','BdcUser','OmcUser', 'BdcOmc', 'User', 'Depot', 'District', 'ProductType', 'Region','Bdc','Order','FreightRate','DeliveryLocation','OmcCustomerOrder','Omc','Volume');

    # Set the layout to use
    var $layout = 'omc_layout';

    # Bdc ids this user will work with only
    var $user_bdc_ids = array();

    public function beforeFilter($param_array = null)
    {
        parent::beforeFilter(array('omc_user_types'=>array('Operations')));
        //Cache the users bdcs to work with
        $company_profile = $this->global_company;
         /*$bdcs_data = $this->OmcUser->find('first', array(
             'conditions' => array('OmcUser.user_id' => $this->Auth->user('id'),'OmcUser.omc_id' => $company_profile['id']),
             'contain'=>array('OmcUserBdc'),
             'recursive' => 1
         ));
        foreach($bdcs_data['OmcUserBdc'] as $o){
            $this->user_bdc_ids[] = $o['bdc_id'];
        }*/
    }


    function index()
    {
        $company_profile = $this->global_company;
        $products_lists = $this->get_products();
        $bdc_depot_lists = $this->get_depot_list();
        $omc_customers_lists = $this->get_customer_list();
        $places_data = $this->get_region_district();
        $bdc_lists = $this->get_bdc_list();
        $glbl_region_district = $places_data['region_district'];
        $regions_lists = $places_data['region'];
        $district_lists = $places_data['district'];
        $delivery_locations = $this->get_delivery_locations();

        $data = $this->getTodayConsolidated($company_profile['id'], 'omc');
        $liters_per_products = $data['liters_per_products'];
        $grid_data = $data['grid_data'];
        $bar_graph_data = $this->getBarGraphData($company_profile['id'], 'omc');
        $pie_data = array();
        foreach ($bar_graph_data['data'] as $pie) {
            $pie_data[] = array($pie['name'], array_sum($pie['data']));
        }
        if ($pie_data) {
            $first_pie_data = $pie_data[0];
            $pie_data[0] = array(
                'name' => $first_pie_data[0],
                'y' => $first_pie_data[1],
                'sliced' => true,
                'selected' => true
            );
        }

        $this->set(compact('company_profile','grid_data', 'liters_per_products', 'omc_customers_lists','bdc_depot_lists', 'bdc_lists','omclists', 'products_lists', 'regions_lists', 'district_lists', 'bar_graph_data', 'pie_data','glbl_region_district','delivery_locations'));
    }


    function export_loading_data(){
        $download = false;
        $company_profile = $this->global_company;;
        if($this->request->is('post')){
            if($this->request->data['Export']['action'] == 'export_me'){
                $start_dt = $this->covertDate($this->request->data['Export']['export_startdt'],'mysql').' 00:00:00';
                $end_dt = $this->covertDate($this->request->data['Export']['export_enddt'],'mysql').' 23:59:59';
                $type = $this->request->data['Export']['export_type'];

                $export_data = $this->BdcDistribution->find('all', array(
                    'fields'=>array('BdcDistribution.id','BdcDistribution.loading_date','BdcDistribution.waybill_date','BdcDistribution.quantity','BdcDistribution.waybill_id','BdcDistribution.vehicle_no'),
                    'conditions' => array('BdcDistribution.omc_id' => $company_profile['id'], 'BdcDistribution.order_status' => 'Complete', 'BdcDistribution.deleted' => 'n','BdcDistribution.loading_date >=' => $start_dt, 'BdcDistribution.loading_date <=' => $end_dt),
                    'contain'=>array(
                        'OmcBdcDistribution'=>array(
                            'fields'=>array('OmcBdcDistribution.id','OmcBdcDistribution.quantity'),
                            'OmcCustomer'=>array(
                                'fields'=>array('OmcCustomer.id','OmcCustomer.name')
                            ),
                            'DeliveryLocation'=>array('fields'=>array('DeliveryLocation.id','DeliveryLocation.name')),
                            'Region'=>array('fields'=>array('Region.id','Region.name'))
                        ),
                        'Bdc'=>array('fields'=>array('Bdc.id','Bdc.name')),
                        'Depot'=>array('fields'=>array('Depot.id','Depot.name')),
                        'ProductType'=>array('fields'=>array('ProductType.id','ProductType.name')),
                        /*'Region'=>array('fields'=>array('Region.id','Region.name')),
                        'District'=>array('fields'=>array('District.id','District.name'))*/
                    ),
                    'order' => array("BdcDistribution.id"=>'desc'),
                ));

                if ($export_data) {
                    $download = true;
                    $list_data = array();
                    foreach ($export_data as $value) {
                        $master_row = array(
                            $this->covertDate($value['BdcDistribution']['loading_date'],'mysql_flip'),
                            $this->covertDate($value['BdcDistribution']['waybill_date'],'mysql_flip'),
                            $value['BdcDistribution']['waybill_id'],
                            $value['Bdc']['name'],
                            $value['Depot']['name'],
                            $value['ProductType']['name'],
                            preg_replace('/,/','',$value['BdcDistribution']['quantity']),
                           /* $value['Region']['name'],
                            $value['District']['name'],*/
                            $value['BdcDistribution']['vehicle_no']
                        );
                        //Add the omc record if any
                        if($value['OmcBdcDistribution']){
                            foreach ($value['OmcBdcDistribution'] as $omcdb) {
                                $copy_master = $master_row;
                                $copy_master[] = $omcdb['OmcCustomer']['name'];
                                $copy_master[] = preg_replace('/,/','',$omcdb['quantity']);
                                $copy_master[] = isset($omcdb['DeliveryLocation']['name'])? ucwords(strtolower($omcdb['DeliveryLocation']['name'])): '';
                                $copy_master[] = isset($omcdb['Region']['name'])?$omcdb['Region']['name']:'';
                                 $list_data[] = $copy_master;
                            }
                        }
                        else{
                            $list_data[] = $master_row;
                        }
                    }
                    $list_headers = array('Date','Waybill Date','Waybill No.','From','Depot','Product Type','Quantity','Vehicle No.','Customer Name','Quantity Delivered','Delivery Location','Region');
                    //$list_headers = array('Date','Waybill No.','From','Depot','Product Type','Actual Quantity','Vehicle No.','Customer Name','Quantity Delivered','Delivery Location','Region','District');
                    $filename = $company_profile['name']." Daily View ".date('Ymdhis');
                    $res = $this->convertToExcel($list_headers,$list_data,$filename);
                    $objPHPExcel = $res['excel_obj'];
                    $filename = $res['filename'];
                }
            }
        }

        $this->autoLayout = false;

        $this->set(compact('objPHPExcel', 'download', 'filename'));
    }



}