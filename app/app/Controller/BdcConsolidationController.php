<?php

/**
 * @name BdcController.php
 */
App::import('Controller', 'BdcApp');
class BdcConsolidationController extends BdcAppController
{
    # Controller name

    var $name = 'BdcConsolidation';
    # set the model to use
    var $uses = array('BdcDistribution', 'BdcUser', 'User', 'Depot', 'District', 'ProductType', 'Region', 'Waybill','StockTrading');
    # Set the layout to use
    var $layout = 'bdc_layout';

    public function beforeFilter($param_array = null)
    {
        parent::beforeFilter(array('bdc_user_types'=>array('Operations')));
    }


    function index()
    {

    }

}