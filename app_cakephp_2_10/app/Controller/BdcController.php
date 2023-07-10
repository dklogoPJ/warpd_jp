<?php

/**
 * @name BdcController.php
 */
App::import('Controller', 'BdcApp');
class BdcController extends BdcAppController
{
    # Controller name

    var $name = 'Bdc';
    # set the model to use
    var $uses = array('BdcDistribution','User' ,'Order', 'Depot', 'District', 'ProductType', 'Region', 'Waybill','StockTrading');
    # Set the layout to use
    var $layout = 'bdc_layout';

    public function beforeFilter($param_array = null)
    {
        parent::beforeFilter();
    }


    function dashboard(){
        $authUser = $this->Auth->user();
        $group_depot = $this->User->getDepotGroup($authUser['id']);
        $loading_board = $this->get_loading_board($group_depot);
        $loaded_board = $this->get_loaded_board($group_depot);

        $company_profile = $this->global_company;
        $data = $this->getTodayConsolidated($company_profile['id'], 'bdc');
        $liters_per_products = $data['liters_per_products'];
        $grid_data = $data['grid_data'];
        $bar_graph_data = $this->getBarGraphData($company_profile['id'], 'bdc');
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
        $controller = $this;
        $this->set(compact('loading_board','loaded_board','controller','grid_data', 'liters_per_products', 'bar_graph_data', 'pie_data'));
    }

}