<?php
/**
 * Ceps Application level Controller
 *
 */
class CepsAppController extends AppController
{
    function beforeFilter($param_array = null)
    {
        parent::beforeFilter();
    }

    function get_all_bdc_list(){
        $bdc_list = $this->Bdc->getBDCsList();
        return $bdc_list;
    }

}
