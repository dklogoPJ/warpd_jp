<?php
/**
 * Omc Application level Controller
 *
 */
class OmcCustomerAppController extends AppController
{

    function beforeFilter($param_array = null)
    {
        parent::beforeFilter();
    }


    function getStockHistory($month,$year,$product_group){
        $company_profile = $this->global_company;

        $ModObject = ClassRegistry::init('OmcCustomerStock');
        $month_year = $year.'-'.$month;
        $conditions = array('OmcCustomerStock.created LIKE '=>$month_year.'%','OmcCustomerTank.omc_customer_id' => $company_profile['id'], 'OmcCustomerTank.status' => 'Operational', 'OmcCustomerTank.deleted' => 'n');
        if($product_group != 'all'){
            $conditions['OmcCustomerTank.type']=$product_group;
        }
        $export_data = $ModObject->find('all', array(
            'fields'=>array('OmcCustomerStock.id','OmcCustomerStock.quantity','OmcCustomerStock.created'),
            'conditions' => $conditions,
            'contain'=>array('OmcCustomerTank'=>array('fields'=>array('OmcCustomerTank.id','OmcCustomerTank.name'))),
            'order' => array("OmcCustomerStock.created"=>'asc'),
        ));

        $tank_ids = array();
        $t_head = array();
        $t_body_data = array();
        $t_body = array();
        if($export_data){
            //Get all the product type
            foreach($export_data as $arr){
                $date_arr = explode(' ',$arr['OmcCustomerStock']['created']);
                $t_head[$date_arr[0]] = $date_arr[0];
                $tank_ids[$arr['OmcCustomerTank']['id']]=array(
                    'id' => $arr['OmcCustomerTank']['id'],
                    'name' => $arr['OmcCustomerTank']['name'],
                );
            }
            foreach($t_head as $dt){
                foreach($tank_ids as $key_id => $key_val){
                    $vl = '-';
                    foreach($export_data as $arr){
                        $date_arr = explode(' ',$arr['OmcCustomerStock']['created']);
                        $cmp_dt = $date_arr[0];
                        $cmp_tank_id = $arr['OmcCustomerTank']['id'];
                        if($dt == $cmp_dt && $key_id == $cmp_tank_id){
                            $vl = doubleval(preg_replace('/,/','',$arr['OmcCustomerStock']['quantity']));
                        }
                    }
                    $t_body[$key_val['name']][] = $vl;
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

    function getAddressList($omc_id,$omc_customer_id,$omc_user_type=array(),$omc_customer_user_type=array(),$exclude=array(), $type='omc_customer')
    {
        $OmcUser = ClassRegistry::init('OmcUser');
        $OmcCustomerUser = ClassRegistry::init('OmcCustomerUser');

        $conditions_omc = array('OmcUser.omc_id' => $omc_id,'OmcUser.omc_user_type'=>$omc_user_type);
        if($type == 'omc'){
            $conditions_omc['NOT']=array('OmcUser.user_id' => $exclude);
        }
        $all_omc_users = $OmcUser->find('list', array(
            'fields'=>array('OmcUser.user_id'),
            'conditions' => $conditions_omc,
            'recursive' => -1
        ));

        $conditions_customer =array('OmcCustomerUser.omc_customer_id' => $omc_customer_id,'OmcCustomerUser.omc_customer_user_type'=>$omc_customer_user_type);
        if($type == 'omc_customer'){
            $conditions_bdc['NOT']=array('OmcCustomerUser.user_id' => $exclude);
        }
        $all_customer_users = $OmcCustomerUser->find('list', array(
            'fields'=>array('OmcCustomerUser.user_id'),
            'conditions' => $conditions_customer,
            'recursive' => -1
        ));

        $all_users = array_merge($all_omc_users,$all_customer_users);

        return $all_users;
    }


    function get_orders($start_dt,$end_dt,$group_by,$filter_bdc){
        $company_profile = $this->global_company;
        return $this->getOrders('omc',$company_profile['id'],$start_dt,$end_dt,$group_by,$filter_bdc,null);
    }

    function get_products($filter = true){
        $company_profile = $this->global_company;
        if($filter){
            $products = $this->OmcCustomer->getOmcCustomerProduct($company_profile['id']);
            $product_ids = $products['my_products'];

            return $this->get_product_list($product_ids);
        }
        else{
            return $this->get_product_list();
        }
    }


    function getStockBoard()
    {
        $OmcCustomerStock = ClassRegistry::init('OmcCustomerStock');
        $company_profile = $this->global_company;
        $last_stock_update = $OmcCustomerStock->__getStockBoard($company_profile);
        return $last_stock_update;
    }

}