<?php

/**
 * @name OmcController.php
 */
App::import('Controller', 'OmcApp');
class OmcController extends   OmcAppController
{
    # Controller name

    var $name = 'Omc';
    # set the model to use
    var $uses = array('BdcDistribution','OmcBdcDistribution', 'OmcCustomer','BdcUser','OmcUser', 'BdcOmc', 'User', 'Depot', 'District', 'ProductType', 'Region','Bdc','Order','FreightRate','DeliveryLocation','OmcCustomerOrder','Omc','Volume');

    # Set the layout to use
    var $layout = 'omc_layout';

    public function beforeFilter($param_array = null)
    {
        parent::beforeFilter();
    }

    function index()
    {

    }

    function dashboard(){
        $authUser = $this->Auth->user();

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

        $group_depot = $this->User->getDepotGroup($authUser['id']);
        $loading_board = $this->get_loading_board($group_depot);
        $loaded_board = $this->get_loaded_board($group_depot);

        $this->set(compact('loading_board','loaded_board','company_profile','grid_data', 'liters_per_products', 'omc_customers_lists','bdc_depot_lists', 'bdc_lists','omclists', 'products_lists', 'regions_lists', 'district_lists', 'bar_graph_data', 'pie_data','glbl_region_district','delivery_locations'));
    }

}