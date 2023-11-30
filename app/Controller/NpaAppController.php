<?php
/**
 * Npa Application level Controller
 *
 */
class NpaAppController extends AppController
{

    function beforeFilter($param_array = null)
    {
        parent::beforeFilter();
    }


    function getProductMonthlyConsolidate($bdc_id,$start_dt,$end_dt,$product_type_id=null,$omc=null)
    {
        $BdcDistribution = ClassRegistry::init('BdcDistribution');
        return $BdcDistribution->getProductMonthlyConsolidateBDC($bdc_id,$start_dt,$end_dt,$product_type_id,$omc);
    }


    function getMonthlyVariant($bdc_id,$month,$year,$query_filter,$product_type_id=null)
    {
        $BdcDistribution = ClassRegistry::init('BdcDistribution');
        return $BdcDistribution->getMonthlyVariantBDC($bdc_id,$month,$year,$query_filter,$product_type_id);
    }


    function getMonthlyOmcVariant($bdc_id,$month,$year,$product_type_id=null)
    {
        return $this->getMonthlyVariant($bdc_id,$month,$year,'Omc',$product_type_id);
    }

    function getMonthlyDepotVariant($bdc_id,$month,$year,$product_type_id=null)
    {
        return $this->getMonthlyVariant($bdc_id,$month,$year,'Depot',$product_type_id);
    }


    function get_orders($start_dt,$end_dt,$group_by,$filter_omc){
        $company_profile = $this->global_company;
        return $this->getOrders('bdc',$company_profile['id'],$start_dt,$end_dt,$group_by,null,$filter_omc);
    }

    function admin_get_omc_list(){
        $company_profile = $this->global_company;
        $Omclist = $this->Omc->find('all', array(
            'conditions' => array('available' => 'Available','deleted' => 'n'),
            'recursive' => -1
        ));
        $omclist_arr = array();
        foreach ($Omclist as $value) {
            $omclist_arr[] = array('id'=>$value['Omc']['id'],'name'=>$value['Omc']['name']);
        }
        return $omclist_arr;
    }

    function get_omc_list(){
        $company_profile = $this->global_company;
        $Mod = ClassRegistry::init('BdcOmc');
        $condition_array = array(
            'Omc.available' => 'Available',
            'BdcOmc.bdc_id' => $company_profile['id'],
            'BdcOmc.deleted' => 'n'
        );
        $contain = array('Omc' => array('fields' => array('Omc.id', 'Omc.name')));
        $bdc_omcs = $Mod->find('all', array(
            'fields' => array('BdcOmc.id', 'BdcOmc.omc_id'),
            'conditions' => $condition_array,
            'contain' => $contain,
            'recursive' => 1
        ));


        $omclists = array();
        foreach ($bdc_omcs as $value) {
            $omclists[] = $value['Omc'];
        }

        return $omclists;
    }

    function get_depot_list(){
        $company_profile = $this->global_company;
        $Depot = ClassRegistry::init('Depot');
        $depot_lists = $Depot->get_depot_list();
        return $depot_lists;
    }

    function get_products(){
        $company_profile = $this->global_company;
        $ProductType = ClassRegistry::init('ProductType');
        $p = $ProductType->getProductList();
        $product_lists = array();
        foreach ($p as $value) {
            //$bdc_depot_lists[] = $value['Depot'];
            $product_lists[] = array(
                'id'=>$value['id'],
                'name'=>$value['name'],
            );
        }

        return $product_lists;
    }


    function get_bdc_stocks($start_dt,$product,$stock_type){
        $Mod = ClassRegistry::init('BdcStockHistory');
        return $Mod->getBDCStocks($start_dt,$product,$stock_type);
    }

}
