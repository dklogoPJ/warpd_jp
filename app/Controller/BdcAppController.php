<?php
/**
 * Bdc Application level Controller
 *
 */
class BdcAppController extends AppController
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

        $condition_array = array(
            'Omc.available' => 'Available',
            'BdcOmc.bdc_id' => $company_profile['id'],
            'BdcOmc.deleted' => 'n'
        );
        $contain = array('Omc' => array('fields' => array('Omc.id', 'Omc.name')));
        $bdc_omcs = $this->BdcOmc->find('all', array(
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

    function get_depot_list($filter = true){
        $company_profile = $this->global_company;
        $depot_ids = null;
        if($filter){
            $depots_products = $this->Bdc->getDepotProduct($company_profile['id']);
            $depot_ids = $depots_products['my_depots'];
        }
        $depot_lists = $this->Depot->get_depot_list($depot_ids);
        return $depot_lists;
    }


    function get_products($filter = true){
        $company_profile = $this->global_company;
        if($filter){
            $depots_products = $this->Bdc->getDepotProduct($company_profile['id']);
            $product_ids = $depots_products['my_products'];

            return $this->get_product_list($product_ids);
        }
        else{
            return $this->get_product_list();
        }
    }


    function get_new_added_depot_products(){
        $company_profile = $this->global_company;
        $products = $this->get_products();
        $depots = $this->get_depot_list();
        $depots_to_products = $this->Bdc->getDepotToProduct($company_profile['id']);
        $stock_startup = $grid_data =$this->BdcInitialStockStartup->getStockStartUp($company_profile['id']);
        $stock_startup_check = array();
        foreach($stock_startup as $data){
            $n = $data['BdcInitialStockStartup'];
            $stock_startup_check[$n['depot_id']][]=$n['product_type_id'];
        }
        $depots_products = array();
        foreach($depots as $depot){
            $filter_pro = isset($depots_to_products[$depot['id']])?$depots_to_products[$depot['id']]:array();
            $stock_startup_filter = isset($stock_startup_check[$depot['id']])?$stock_startup_check[$depot['id']]:array();
            foreach($products as $pro){
                if(in_array($pro['id'],$filter_pro)){
                    if(in_array($pro['id'],$stock_startup_filter)){

                    }
                    else{
                        $depots_products[$depot['id']]['name'] = $depot['name'];
                        $depots_products[$depot['id']]['products'][]=$pro;
                    }
                }
            }
        }
        return array('depots_products'=>$depots_products,'grid_data'=>$grid_data);
    }

}
