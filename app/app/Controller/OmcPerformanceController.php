<?php

/**
 * @name OmcController.php
 */
App::import('Controller', 'OmcApp');

class OmcPerformanceController extends OmcAppController
{
    # Controller name

    var $name = 'OmcPerformance';
    # set the model to use
    var $uses = array('OmcCustomer','OmcCustomerTankMinstocklevel','OmcCustomerTank','OmcDsrpDataOption','OmcTank','OmcTankStatus','OmcTankType','AdditiveSetup','AdditiveStock','Omc','AdditiveDopingRatio','AdditiveCostGeneration','ProductType','Truck','Depot','AdditiveAverageCost');

    # Set the layout to use
    var $layout = 'omc_layout';

    public function beforeFilter($param_array = null)
    {
        parent::beforeFilter(array('omc_user_types'=>array('Allow All')));
    }


    function index()
    {
        //$this->redirect('daily_stock');
    }

    public function perf_monitoring_setting() {

	}

    public function perf_monitoring_analytics() {

	}

    public function montly_perf_monitoring_analytics() {

	}

}

?>