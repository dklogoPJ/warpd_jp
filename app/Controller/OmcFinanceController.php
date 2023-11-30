<?php

/**
 * @name 
 */
App::import('Controller', 'OmcApp');
class OmcFinanceController extends OmcAppController
{
    # Controller name

    var $name = 'OmcFinance';
    # set the model to use
    var $uses = array('BdcDistribution','OmcBdcDistribution', 'OmcCustomer','BdcUser','OmcUser', 'BdcOmc', 'User', 'Depot', 'District', 'ProductType', 'Region','Bdc','Order','FreightRate','DeliveryLocation','OmcCustomerOrder');
    # Set the layout to use
    var $layout = 'omc_layout';

    public function beforeFilter($param_array = null)
    {
        parent::beforeFilter(array('omc_user_types'=>array('Finance')));
    }


    function index()
    {
        $this->redirect(array('controller'=>'OmcOrders','action'=>'customer_orders'));
    }

}