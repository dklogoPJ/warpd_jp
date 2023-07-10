<?php
/**
 * Omc Application level Controller
 *
 */
class OmcAppController extends AppController
{

    function beforeFilter($param_array = null)
    {
        parent::beforeFilter();

    }


    function getProductMonthlyConsolidate($omc_id,$start_dt,$end_dt,$product_type_id=null)
    {
        $BdcDistribution = ClassRegistry::init('BdcDistribution');
        return $BdcDistribution->getProductMonthlyConsolidateOMC($omc_id,$start_dt,$end_dt,$product_type_id);
    }


    function getMonthlyVariant($omc_id,$month,$year,$query_filter,$product_type_id=null)
    {
        $BdcDistribution = ClassRegistry::init('BdcDistribution');
        return $BdcDistribution->getMonthlyVariantOMC($omc_id,$month,$year,$query_filter,$product_type_id);
    }


    function getMonthlyBdcVariant($omc_id,$month,$year,$product_type_id=null)
    {
        return $this->getMonthlyVariant($omc_id,$month,$year,'Bdc',$product_type_id);
    }

    function getMonthlyDepotVariant($omc_id,$month,$year,$product_type_id=null)
    {
        return $this->getMonthlyVariant($omc_id,$month,$year,'Depot',$product_type_id);
    }


    function getUppf($start_dt,$end_dt,$product_group){
        $company_profile = $this->global_company;
        $BdcDistribution = ClassRegistry::init('BdcDistribution');
        return $BdcDistribution->getUPPF($start_dt,$end_dt,$product_group, $company_profile['id']);
    }


    function get_orders($start_dt,$end_dt,$group_by,$filter_bdc){
        $company_profile = $this->global_company;
        return $this->getOrders('omc',$company_profile['id'],$start_dt,$end_dt,$group_by,$filter_bdc,null);
    }


    function get_station_orders($start_dt,$end_dt,$group_by,$filter_omc_customer){
        $company_profile = $this->global_company;
        return $this->getStationOrders('omc',$company_profile['id'],$start_dt,$end_dt,$group_by,$filter_omc_customer);
    }

    function getStationOrders($type = 'omc',$id, $start_dt,$end_dt,$group_by,$filter_omc_customer)
    {
        $Order = ClassRegistry::init('OmcCustomerOrder');
        return $Order->getOrders($type,$id, $start_dt,$end_dt,$group_by,$filter_omc_customer);
    }


    function getStationProductReport($omc_id,$start_dt,$end_dt,$product_type_id=null,$omc_customer=null)
    {
        $Order = ClassRegistry::init('OmcCustomerOrder');
        return $Order->getStationProduct($omc_id,$start_dt,$end_dt,$product_type_id,$omc_customer);
    }


    function get_customer_list($id=null){
        $company_profile = $this->global_company;
        $conditions = array('OmcCustomer.omc_id' => $company_profile['id'],'OmcCustomer.deleted' => 'n');
        if($id != null){
            $conditions['OmcCustomer.id'] = $id;
        }
        $omc_customers = $this->OmcCustomer->find('all', array(
            'fields' => array('OmcCustomer.id', 'OmcCustomer.name'),
            'conditions' => $conditions,
            'recursive' => -1
        ));
        $omc_customers_lists = array();
        foreach ($omc_customers as $value) {
            $omc_customers_lists[] = $value['OmcCustomer'];
        }
        return $omc_customers_lists;
    }


    function get_bdc_list(){
        $bdc_list_data = $this->get_all_bdc_list();
        $bdc_list = array();
        foreach($bdc_list_data as $bdc){
            $bdc_list[] = $bdc['Bdc'];
        }
        return $bdc_list;
    }

    function get_bdc_omc_list(){
        $company_profile = $this->global_company;
        $bdc_list = $this->BdcOmc->get_bdc_omc_list($company_profile);
        return $bdc_list;
    }

    function get_all_bdc_list(){
        $bdc_list = $this->Bdc->getBDCs();
        return $bdc_list;
    }

    function get_depot_list($filter =true){
        $company_profile = $this->global_company;
        $depot_ids = null;
        if($filter){
            $depots_products = $this->Omc->getOmcDepot($company_profile['id']);
            $depot_ids = $depots_products['my_depots'];
        }
        $depot_lists = $this->Depot->get_depot_list($depot_ids);
        return $depot_lists;
    }

    function get_delivery_locations(){
        $company_profile = $this->global_company;
        $lists_data = $this->DeliveryLocation->find('all', array(
            'fields' => array('DeliveryLocation.id', 'DeliveryLocation.name','DeliveryLocation.distance','DeliveryLocation.alternate_route'),
            'conditions' => array('DeliveryLocation.deleted' => 'n'),
            'contain'=>array(
                'Depot'=>array('fields' => array('Depot.id', 'Depot.name','Depot.short_name')),
                'Region'=>array('fields' => array('Region.id', 'Region.name'))
            ),
            'recursive' => 1
        ));

        $lists = array();
        foreach ($lists_data as $value) {
            $lists[$value['Depot']['id']]['regions'][$value['Region']['id']]= $value['Region']['name'];
            $lists[$value['Depot']['id']]['data'][$value['Region']['id']]['name']= $value['Region']['name'];
            $lists[$value['Depot']['id']]['data'][$value['Region']['id']]['data'][]= $value['DeliveryLocation'];
        }

        return $lists;
    }

    function get_product_group(){
        //$lists = array('White Products','LPG','Premix','MGO','Kero','RFO','Naphtha');
        $lists = array('White Products','LPG','Premix','MGO','Kero');
        return $lists;
    }


    function getCustomersStockHistory($month,$year,$customer_id=null){
        if($customer_id == null){
            $customer_list = $this->get_customer_list();
            $result = array();
            foreach($customer_list as $data){
                $result[$data['id']]=array(
                    'name'=>$data['name'],
                    'data'=>$this->getStockHistory($month,$year,$data['id']),
                );
            }
        }
        else{
            $customer_list = $this->get_customer_list($customer_id);
            $result[$customer_list[0]['id']]=array(
                'name'=>$customer_list[0]['name'],
                'data'=>$this->getStockHistory($month,$year,$customer_list[0]['id']),
            );
        }

        return $result;
    }

    /** For Stock Management */
    function getStockHistory($month,$year,$customer_ids){
        $company_profile = $this->global_company;
        $ModObject = ClassRegistry::init('OmcCustomerStock');
        $month_year = $year.'-'.$month;
        $conditions = array('OmcCustomerStock.created LIKE '=>$month_year.'%','OmcCustomerTank.omc_customer_id' => $customer_ids, 'OmcCustomerTank.status' => 'Operational', 'OmcCustomerTank.deleted' => 'n');
        $export_data = $ModObject->find('all', array(
            'fields'=>array('OmcCustomerStock.id','OmcCustomerStock.quantity','OmcCustomerStock.created'),
            'conditions' => $conditions,
            'contain'=>array(
                'OmcCustomerTank'=>array(
                    'fields'=>array('OmcCustomerTank.id','OmcCustomerTank.name','OmcCustomerTank.type'),
                    //'OmcCustomer'=>array('fields'=>array('OmcCustomer.id','OmcCustomer.name'))
                )
            ),
            'order' => array("OmcCustomerStock.created"=>'asc'),
        ));

        $tank_types = array();
        $t_head = array();
        $t_body_data = array();
        $t_body = array();
        if($export_data){
            //Get all the product type
            $f_data = array();
            foreach($export_data as $arr){
                $date_arr = explode(' ',$arr['OmcCustomerStock']['created']);
                $t_head[$date_arr[0]] = $date_arr[0];
                $tank_types[$arr['OmcCustomerTank']['type']]=$arr['OmcCustomerTank']['type'];
                $f_data[$date_arr[0]][$arr['OmcCustomerTank']['type']][]=doubleval(preg_replace('/,/','',$arr['OmcCustomerStock']['quantity']));
            }
            foreach($t_head as $dt){
                foreach($tank_types as $tank_type_val){
                    //$new_key = $tank_type_val.':'.$dt;
                    $new_key = $tank_type_val;
                    if(isset($f_data[$dt])){
                        if(isset($f_data[$dt][$tank_type_val])){
                            $sm = array_sum($f_data[$dt][$tank_type_val]);
                            $t_body[$new_key][] = $sm;
                        }
                        else{
                            $t_body[$new_key][] = '-';
                        }
                    }
                }
            }
            //last format
            foreach($t_body as $tank_name => $arr){
                $t_body_data[]=array_merge(array($tank_name => $tank_name),$arr);
            }
        }
        if($export_data){
            $tmp = array();
            foreach($t_head as $dt){
                $cnv = $this->covertDate($dt,'formal');
                $tmp[$cnv]=$cnv;
            };
            $t_head = array_merge(array('Tank S/No.'=> 'Tank S/No.'),$tmp);
        }

        return array(
            't_head'=>$t_head,
            't_body_data'=>$t_body_data
        );
    }


    function getDailyStockHistory($date,$customer_id=null){
        $customer_ids = array();
        $result = array();
        if($customer_id == null){
            $customer_list = $this->get_customer_list();
            foreach($customer_list as $data){
                $customer_ids[]= $data['id'];
            }
            $result = $this->getDailyStock($date,$customer_ids);
        }
        else{
            $customer_list = $this->get_customer_list($customer_id);
            $customer_ids = array();
            $customer_ids[] = $customer_list[0]['id'];
            $result = $this->getDailyStock($date,$customer_ids);
        }

        return $result;
    }

    function getDailyStock($date,$customer_ids){
        $company_profile = $this->global_company;
        $ModObject = ClassRegistry::init('OmcCustomerStock');
        $conditions = array('OmcCustomerStock.created LIKE '=>$date.'%','OmcCustomerTank.omc_customer_id' => $customer_ids, 'OmcCustomerTank.status' => 'Operational', 'OmcCustomerTank.deleted' => 'n');
        $export_data = $ModObject->find('all', array(
            'fields'=>array('OmcCustomerStock.id','OmcCustomerStock.quantity','OmcCustomerStock.created','OmcCustomerStock.omc_customer_tank_id'),
            'conditions' => $conditions,
            'contain'=>array(
                'OmcCustomerTank'=>array(
                    'fields'=>array('OmcCustomerTank.id','OmcCustomerTank.name','OmcCustomerTank.type','OmcCustomerTank.omc_customer_id'),
                    'OmcCustomer'=>array('fields'=>array('OmcCustomer.id','OmcCustomer.name'))
                )
            ),
            'order' => array("OmcCustomerStock.created"=>'asc'),
        ));
        //debug($export_data);
        $customers = array();
        $t_head = array();
        $t_body_data = array();
        $t_body= $t_body_tl = array();
        if($export_data){
            //Get all the product type
            $f_data = array();
            foreach($export_data as $arr){
                $customers[$arr['OmcCustomerTank']['omc_customer_id']]=$arr['OmcCustomerTank']['OmcCustomer'];
                $t_head[$arr['OmcCustomerTank']['type']]=$arr['OmcCustomerTank']['type'];
                $f_data[$arr['OmcCustomerTank']['type']][$arr['OmcCustomerTank']['omc_customer_id']]['name']=$arr['OmcCustomerTank']['OmcCustomer'];
                //$f_data[$arr['OmcCustomerTank']['type']][$arr['OmcCustomerTank']['omc_customer_id']]['data'][]=doubleval(preg_replace('/,/','',$arr['OmcCustomerStock']['quantity']));
                $f_data[$arr['OmcCustomerTank']['type']][$arr['OmcCustomerTank']['omc_customer_id']]['data'][]=preg_replace('/,/','',$arr['OmcCustomerStock']['quantity']);
            }

            foreach($t_head as $dt){
                foreach($customers as $customer_key => $customer_val){
                    $new_key = $customer_val['name'];
                    if(isset($f_data[$dt])){
                        if(isset($f_data[$dt][$customer_key])){
                            $sm = array_sum($f_data[$dt][$customer_key]['data']);
                            $t_body[$new_key][] = $sm;
                            $t_body_tl[$new_key][$dt] = $sm;
                        }
                        else{
                            $t_body[$new_key][] = '-';
                            $t_body_tl[$new_key][$dt] = 0;
                        }
                    }
                }
            }

            //debug($t_body);

            $tl = array();
            foreach($t_body_tl as $key => $arr){
                foreach($arr as $key_inner => $val){
                    if(isset($tl[$key_inner])){
                        $tl[$key_inner]= $tl[$key_inner] + $val;
                    }
                    else{
                        $tl[$key_inner]=$val;
                    }
                }
            }
            //last format
            foreach($t_body as $tank_name => $arr){
                $t_body_data[]=array_merge(array($tank_name => $tank_name),$arr);
            }
            //debug($t_body_data);
            $t_body_data[]=array_merge(array('Total' => 'Total'),$tl);
            if($export_data){
                $t_head = array_merge(array('Customers'=> 'Customers'),$t_head);
            }
        }

        return array(
            't_head'=>$t_head,
            't_body_data'=>$t_body_data
        );
    }

    function getCustomersTankTypes($customer_id = null){
        $company_profile = $this->global_company;

        $OmcCustomer = ClassRegistry::init('OmcCustomer');
        $OmcCustomerTank = ClassRegistry::init('OmcCustomerTank');
        //All Customers
        $conditions = array('OmcCustomer.omc_id' =>  $company_profile['id']);
        if($customer_id != null){
            $conditions = array('OmcCustomer.id' =>  $customer_id);
        }
        $customers_data = $OmcCustomer->find('all', array(
            'fields' => array('OmcCustomer.id'),
            'conditions' => $conditions,
            'recursive' => -1
        ));
        $customer_ids = array();
        foreach($customers_data as $cust_arr){
            $customer_ids[] = $cust_arr['OmcCustomer']['id'];
        }
        //Get Tank details for each customer
        $customer_tanks = $OmcCustomerTank->find('all', array(
            'fields' => array('OmcCustomerTank.type','OmcCustomerTank.omc_customer_id'),
            'conditions' => array('OmcCustomerTank.omc_customer_id' => $customer_ids,'OmcCustomerTank.status' => 'Operational'),
            'group' => array('OmcCustomerTank.type','OmcCustomerTank.omc_customer_id'),
            'recursive' => -1
        ));

        $omc_customer_list = array();
        foreach($customer_tanks as $dt_arr){
            $omc_customer_list[$dt_arr['OmcCustomerTank']['omc_customer_id']][$dt_arr['OmcCustomerTank']['type']] = $dt_arr['OmcCustomerTank']['type'];
        }

        return $omc_customer_list;

    }


    function getDailyStockVariance($date,$customer_id=null,$indicator=null){
        $customer_ids = array();
        $result = array();
        if($customer_id == null){
            $customer_list = $this->get_customer_list();
            foreach($customer_list as $data){
                $customer_ids[]= $data['id'];
            }
            //$customer_ids = array(20);
            $result = $this->__getDailyStockVarianceData($date,$customer_ids,$indicator);
        }
        else{
            $customer_list = $this->get_customer_list($customer_id);
            $customer_ids = array();
            $customer_ids[] = $customer_list[0]['id'];
            $result = $this->__getDailyStockVarianceData($date,$customer_ids,$indicator);
        }

        return $result;
    }


    function __getDailyStockVarianceData($date,$customer_ids,$indicator){
        $company_profile = $this->global_company;
        $ModObject = ClassRegistry::init('OmcCustomerStock');
        //$ModObject2 = ClassRegistry::init('OmcCustomerTank');

       // $conditions = array('OmcCustomerStock.created LIKE '=>$date.'%','OmcCustomerTank.omc_customer_id' => $customer_ids, 'OmcCustomerTank.status' => 'Operational', 'OmcCustomerTank.deleted' => 'n');
       // $conditions = array('OmcCustomerTank.omc_customer_id' => $customer_ids, 'OmcCustomerTank.status' => 'Operational', 'OmcCustomerTank.deleted' => 'n');
       /* $export_data = $ModObject->find('all', array(
            'fields'=>array('OmcCustomerStock.id','OmcCustomerStock.quantity','MAX(DATE(OmcCustomerStock.created)) AS created','OmcCustomerStock.omc_customer_tank_id'),
            'conditions' => $conditions,
            'contain'=>array(
                'OmcCustomerTank'=>array(
                    'fields'=>array('OmcCustomerTank.id','OmcCustomerTank.name','OmcCustomerTank.type','OmcCustomerTank.omc_customer_id','OmcCustomerTank.min_stock_level'),
                    'OmcCustomer'=>array('fields'=>array('OmcCustomer.id','OmcCustomer.name'))
                )
            ),
            //'group'=>array('OmcCustomerTank.omc_customer_id','OmcCustomerTank.id'),
            'order' => array("OmcCustomerStock.created"=>'asc'),
        ));*/

        $q  = "SELECT OmcCustomerStock.id,OmcCustomerStock.created, OmcCustomerStock.quantity,OmcCustomerStock.omc_customer_tank_id,OmcCustomerTank.name,OmcCustomerTank.type,OmcCustomerTank.min_stock_level,OmcCustomerTank.omc_customer_id,OmcCustomer.name
               FROM (
                   SELECT *,@prev <> omc_customer_tank_id AS is_newest, @prev := omc_customer_tank_id
                    FROM omc_customer_stocks, (SELECT @prev := -1) AS vars
                    ORDER BY omc_customer_tank_id, created DESC, id DESC
                ) AS OmcCustomerStock
               LEFT JOIN omc_customer_tanks AS OmcCustomerTank ON OmcCustomerTank.id  = OmcCustomerStock.omc_customer_tank_id
               LEFT JOIN omc_customers AS OmcCustomer ON OmcCustomerTank.omc_customer_id  = OmcCustomer.id
               WHERE is_newest AND OmcCustomerTank.omc_customer_id IN (".implode(',',$customer_ids).")";

        $export_data = $ModObject->query($q);

       // debug($export_data);

        $per_customer = array();
        foreach($export_data as $arr){
            $cust_id = $arr['OmcCustomerTank']['omc_customer_id'];
            $tnk_type = $arr['OmcCustomerTank']['type'];
            $current_stock_level = !empty($arr['OmcCustomerStock']['quantity']) ? preg_replace('/,/','',$arr['OmcCustomerStock']['quantity']) : 0;
            $min_stock_level = !empty($arr['OmcCustomerTank']['min_stock_level']) ? preg_replace('/,/','',$arr['OmcCustomerTank']['min_stock_level']) : 0;
            $variance = $min_stock_level - $current_stock_level;
            if(isset($per_customer[$cust_id])){
                if(isset($per_customer[$cust_id]['stock'][$tnk_type])){
                    $temp_stock = $per_customer[$cust_id]['stock'][$tnk_type]['current_stock_level'];
                    $temp_mini = $per_customer[$cust_id]['stock'][$tnk_type]['min_stock_level'];
                    $tl_temp_stock = $temp_stock + $current_stock_level;
                    $tl_temp_mini = $temp_mini + $min_stock_level;
                    $tl_variance = $tl_temp_mini - $tl_temp_stock;

                    $per_customer[$cust_id]['stock'][$tnk_type]['current_stock_level'] = $tl_temp_stock;
                    $per_customer[$cust_id]['stock'][$tnk_type]['min_stock_level'] = $tl_temp_mini;
                    $per_customer[$cust_id]['stock'][$tnk_type]['variance'] = $tl_variance;
                    $r_arr = $this->_getVarianceStatus($tl_variance,$tl_temp_mini);
                    $per_customer[$cust_id]['stock'][$tnk_type]['color']=$r_arr['color'];
                    $per_customer[$cust_id]['stock'][$tnk_type]['status']=$r_arr['status'];

                }
                else{
                    $per_customer[$cust_id]['stock'][$tnk_type] = array(
                        'current_stock_level' =>$current_stock_level,
                        'min_stock_level' =>$min_stock_level,
                        'variance' =>$variance
                    );
                    $r_arr = $this->_getVarianceStatus($variance,$min_stock_level);
                    $per_customer[$cust_id]['stock'][$tnk_type]['color']=$r_arr['color'];
                    $per_customer[$cust_id]['stock'][$tnk_type]['status']=$r_arr['status'];
                }
            }
            else{
                $per_customer[$cust_id]['info']= $arr['OmcCustomer'];
                $per_customer[$cust_id]['stock'][$tnk_type] = array(
                    'current_stock_level' =>$current_stock_level,
                    'min_stock_level' =>$min_stock_level,
                    'variance' =>$variance
                );
                $r_arr = $this->_getVarianceStatus($variance,$min_stock_level);
                $per_customer[$cust_id]['stock'][$tnk_type]['color']=$r_arr['color'];
                $per_customer[$cust_id]['stock'][$tnk_type]['status']=$r_arr['status'];
            }
        }

        $customers = array();
        $t_head = array();
        $t_body_data = array();
        $t_body= $t_body_tl = array();

        if($indicator != null){
            $hay_stack = array();
            if($indicator == 'red'){
                $hay_stack[]='red';
            }
            else{
                $hay_stack[]='red';
                $hay_stack[]='yellow';
            }
            foreach($per_customer as $c_key => $arr){
                foreach($arr['stock'] as $t_key => $dt_arr){
                    if(!in_array($dt_arr['color'],$hay_stack)){
                        unset($per_customer[$c_key]['stock'][$t_key]);
                    }
                }
            }
        }

        return $per_customer;
    }


    function _getVarianceStatus($variance, $minimum_stock_level){
        $r = array('color'=>'','status'=>'');
        if($variance < 0){//Green Ok
            $r['color']= 'green';
            $r['status']= 'Okay';
        }
        else{
            $fraction = $minimum_stock_level/2;
            if($variance >= $fraction){
                $r['color']= 'red';
                $r['status']= 'Emergency restocking required';
            }
            else{
                $r['color']= 'yellow';
                $r['status']= 'Restocking required';
            }
        }
        return $r;
    }


    function get_products($filter = true){
        $company_profile = $this->global_company;
        if($filter){
            $products = $this->Omc->getOmcProduct($company_profile['id']);
            $product_ids = $products['my_products'];

            return $this->get_product_list($product_ids);
        }
        else{
            return $this->get_product_list();
        }
    }

}