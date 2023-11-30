<?php

/**
 * @name BdcController.php
 */
App::import('Controller', 'BdcApp');
class BdcFinanceController extends BdcAppController
{
    # Controller name

    var $name = 'BdcFinance';
    # set the model to use
    var $uses = array('BdcDistribution', 'BdcUser', 'User', 'Depot', 'ProductType', 'Order','StockTrading');
    # Set the layout to use
    var $layout = 'bdc_layout';

    public function beforeFilter($param_array = null)
    {
        parent::beforeFilter(array('bdc_user_types'=>array('Finance')));
    }


    function index()
    {
        $this->redirect(array('controller'=>'BdcOrders','action'=>'orders'));
    }

}