<?php
/**
 * Depot Application level Controller
 *
 */
class DepotAppController extends AppController
{
    var $uses = array('Bdc');

    function beforeFilter($param_array = null)
    {
        parent::beforeFilter();
    }

    function get_all_bdc_list(){
        $bdc_list = $this->Bdc->getBDCsList();
        return $bdc_list;
    }
}
