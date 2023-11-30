<?php

/**
 * @name BdcOrdersController.php
 */
App::import('Controller', 'NpaApp');

class NpaController extends NpaAppController
{
    # Controller name

    var $name = 'Npa';
    # set the model to use
    var $uses = array('Bdc','BdcDistribution','BdcStockHistory','Omc','OmcBdcDistribution','OmcCustomerStock');

    # Set the layout to use
    var $layout = 'npa_layout';

    public function beforeFilter($param_array = null)
    {
        parent::beforeFilter();
    }


    function dashboard(){

    }

}