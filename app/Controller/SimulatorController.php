<?php

/**
 * This is the administration_controller class file.
 * @author David Klogo<klogodavid@gmail.com>
 * @access public
 * @version 1.0
 */
class SimulatorController extends AppController
{
    # Controller Name to be used

    var $name = 'Simulator';

    # Models to be used
    var $uses = array(
        'Bdc', 'Omc', 'OmcCustomer','Simulation','BdcStockHistory','BdcOmc','BdcStockUpdate','OmcCustomer',
        'OmcCustomerOrder','Order','BdcDistribution','OmcBdcDistribution','DeliveryLocation','FreightRate',
        'ProductType','Depot','BdcInitialStockStartup'
    );

    # set the layout to use
    var $layout = 'simulator_layout';

    public function beforeFilter($param_array = null)
    {
        parent::beforeFilter($validate_access_control = false);
        $this->Auth->allow('*');
    }


    function initial_stock_startup($today = '2013-07-1'){
        $bdc_omc = $this->getWorkingBdcAndOmc();
        $bdc_ids = $bdc_omc['bdc_ids'];
        $count = 0;
        $quantities = range(10000000, 90000000, 500000);
        $save = array();
        foreach($bdc_ids as $bdc_id){
            $products = $this->get_products($bdc_id);
            $depots = $this->get_depot_list($bdc_id);
            $depots_to_products = $this->Bdc->getDepotToProduct($bdc_id);
            $stock_startup = $grid_data =$this->BdcInitialStockStartup->getStockStartUp($bdc_id);
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

            foreach($depots_products as $depot_id=>$arr){
                //$depot_name = $arr['name'];
                foreach($arr['products'] as $v_arr){
                    $product_id = $v_arr['id'];
                    shuffle($quantities);
                    $rand_key_3 = array_rand($quantities, 1);
                    $quantity = $quantities[$rand_key_3];

                    $save[$count]=array(
                        'depot_id'=>$depot_id,
                        'product_type_id'=>$product_id,
                        'quantity_ltrs'=>$quantity,
                        'initial_quantity'=>$quantity,
                        'stock_date'=>$today,
                        'status'=>'Open',
                        'bdc_id'=>$bdc_id,
                        'created'=>$today,
                        'modified'=>$today
                    );
                    $count++;
                }
            }
        }

        $res = $this->BdcInitialStockStartup->saveAll($save);
        if ($res) {
            $this->BdcStockHistory->saveAll($save);
            echo "Done!" ;
        }
        else {
            echo "Failed Saving!" ;
        }
        $this->autoLayout = false;
        $this->autoRender = false;
    }

    /**
     * @name index , this determines the user type and redirect to the appropiate action
     * @return Array of data.
     */
    function index(){
        $next_date = $this->Simulation->getNextDate();
        if($this->request->is('post')){
            $cancel_order = $this->request->data['Query']['cancel_order'];
            $bdc_stock_update = $this->request->data['Query']['bdc_stock_update'];
            $response = $this->_simulate($next_date,$cancel_order,$bdc_stock_update);
            if ($response) {
                $this->Session->setFlash('Simulation Run Successfully!');
                $this->Session->write('process_error', 'no');
                //save for the next date
                $next_date = date("Y-m-d",strtotime(date("Y-m-d", strtotime($next_date)) . " +1 day"));
                $this->Simulation->saveNextDate($next_date);
            }
            else {
                $this->Session->setFlash('Simulation Failed.');
                $this->Session->write('process_error', 'yes');
            }
            //$this->redirect(array('action' => 'index'));
        }
        $controller  = $this;
        $this->set(compact('next_date','controller'));
    }

    function getWorkingBdcAndOmc(){
        $bdc_ids = $omc_ids =  array();
        $bdc_omc = $this->BdcOmc->getBdcOmc();
        foreach($bdc_omc as $bo){
            $bdc_ids[] = $bo['BdcOmc']['bdc_id'];
            $omc_ids[] = $bo['BdcOmc']['omc_id'];
        }
        $bdc_ids = array_unique($bdc_ids);
        $omc_ids = array_unique($omc_ids);
        return array(
            'bdc_ids'=>$bdc_ids,
            'omc_ids'=>$omc_ids
        );
    }

    function _simulate($next_date,$cancel_order,$bdc_stock_update){

        $bdc_omc = $this->getWorkingBdcAndOmc();
        $bdc_ids = $bdc_omc['bdc_ids'];
        $omc_ids = $bdc_omc['omc_ids'];

        /** Step 1, Close all BDC stocks and Open New Stock */
        $this->closeBDCStocks($next_date,$bdc_ids);
        /** Step 2, Bdc Stock Update */
        $this->stockUpdateBdc($next_date,$bdc_stock_update,$bdc_ids);
        /** Step 3, Omc Customer Order */
        $customer_order_ids = $this->customerOrders($next_date,$omc_ids);
        /** Step 4, Omc Order approval and allocation of orders */
        $bdc_to_omc_order_ids = $this->OmcOrderProcessing($customer_order_ids,$cancel_order,$next_date,$bdc_ids);
        /** Step 5, BDc Order approval and Loading of orders */
        $bdc_loaded_ids = $this->BdcOrderProcessingAndLoading($bdc_to_omc_order_ids,$cancel_order,$bdc_ids);
        /** Step 5, Omc Distribution and UPPF */
        $result = $this->OmcDistribution($bdc_loaded_ids,$cancel_order,$bdc_ids);

        return $result ;
    }

    function closeBDCStocks($date,$bdc_ids){
        $yesterday = date("Y-m-d",strtotime(date("Y-m-d", strtotime($date)) . " -1 day"));
        $tomorrow = date("Y-m-d", strtotime($date));
        foreach($bdc_ids as $bdc_id){
            $this->BdcStockHistory->closeStockOpenNewStock($bdc_id,$yesterday,$tomorrow);
        }
    }

    function stockUpdateBdc($date,$bdc_stock_update,$bdc_ids){
        if($bdc_stock_update == 'no_stock_update'){
            return false;
        }
        $date = date("Y-m-d", strtotime($date));
        $shuffle  = array('update');
        if($bdc_stock_update == 'random_stock_update'){
            $shuffle  = array('update','no_update');
        }
        shuffle($bdc_ids);
        foreach($bdc_ids as $bdc){
            $rand_key = array_rand($shuffle, 1);
            $val = $shuffle[$rand_key];
            if($val == 'no_update'){
                continue;
            }
            else{
                $this->_updateBdcStock($bdc,$date);
            }
        }
    }

    function _updateBdcStock($bdc_id,$date){
        $bdc = $this->Bdc->getBdcById($bdc_id);
        $depots_to_product = $bdc['Bdc']['my_depots_to_products'];
        $arrs = explode('#',$depots_to_product);
        $quantities = range(1000000, 10000000, 500000);
        //debug($quantities);
        $save = array();
        foreach($arrs as $ar ){
            $dp_arr = explode('|',$ar);
            $depot_id = $dp_arr[0];
            $products_str = $dp_arr[1];
            if(!empty($products_str)){
                $product_arr = explode(',',$products_str);
                foreach($product_arr as $product_id){
                    $rand_key = array_rand($quantities, 1);
                    $val = $quantities[$rand_key];
                    $save[]=array(
                        'bdc_id'=>$bdc_id,
                        'depot_id'=>$depot_id,
                        'product_type_id'=>$product_id,
                        'quantity_ltrs'=>$val,
                        'supplier'=>'Wanger',
                        'ship_name'=>'MV Crude Oil',
                        'delivery_date'=>$date,
                    );
                }
            }

        }
        //debug($save);
        $this->BdcStockUpdate->saveAll($save);
    }


    function customerOrders($date,$omc_ids){
        $date .=" ".date('H:i:s');
        $save_orders = array();
        $quantities = range(10000, 40000, 2000);
        foreach($omc_ids as $omc){
            $customers = $this->OmcCustomer->getCustomerByOmcId($omc);
            if($customers){
                foreach($customers as $customer_data){
                    $customer_id = $customer_data['OmcCustomer']['id'];
                    $my_product_str = $customer_data['OmcCustomer']['my_products'];
                    if(!empty($my_product_str)){
                        $products_arr = explode(',',$my_product_str);
                        foreach($products_arr as $product_id){
                            $rand_key = array_rand($quantities, 1);
                            $val = $quantities[$rand_key];
                            $save_orders[]=array(
                                'order_date'=>$date,
                                'omc_customer_id'=>$customer_id,
                                'omc_id'=>$omc,
                                'product_type_id'=>$product_id,
                                'order_quantity'=>$val
                            );
                        }
                    }
                }
            }
        }
        //debug($save_orders);
        $this->OmcCustomerOrder->saveAll($save_orders);
        return $this->OmcCustomerOrder->getInsertedIds();
    }


    function OmcOrderProcessing($customer_order_ids,$cancel_order,$next_date,$bdc_ids){
        $customer_order_data = Hash::combine($this->OmcCustomerOrder->find('all',array(
            'conditions'=>array('id'=>$customer_order_ids),
            'fields'=>array('id','order_quantity','omc_id','omc_customer_id','product_type_id','delivery_priority'),
            'recursive'=>-1
        )),'{n}.OmcCustomerOrder.id', '{n}.OmcCustomerOrder');

        $bdc_depots_products = $this->Bdc->getAllDepotToProduct($bdc_ids);

        //cache BDC depots used
        $bdcs_used_depots = array();
        //debug($bdc_depots_products);

        $update_order = array();
        $order_allocation = array();
        $shuffle  = array('approve');
        if($cancel_order == '1'){
            $shuffle  = array('approve','cancel');
        }

        $current_total_order = 1;
        $bdc_control_order = array();
        $bdc_control_depot = array();
        foreach($bdc_ids as $bdc_id){
            $bdc_control_order[$bdc_id] = array();
            $bdc_control_depot[$bdc_id] = array();
        }
        $delivery_locations_arr = $this->get_delivery_locations();
        $conform_del_loc_ids = $delivery_locations_arr['depots'];

        foreach($customer_order_ids as $orid){
            //$omc_id=$customer_order_data[$orid]['omc_id'];

            //Make sure all BDCs have the same amount of orders and reset bdc control order array
            $cur_count_arr = array();
            foreach($bdc_control_order as $key => $value_arr){
                $cur_count_arr[] = count($value_arr);
            }
            $update_counter = false;
            foreach($cur_count_arr as $vc){
                if($vc < $current_total_order){
                    $update_counter = false;
                    break;
                }
                else{
                    $update_counter = true;
                }
            }
            if($update_counter){
                foreach($bdc_ids as $bdc_id){
                    $bdc_control_order[$bdc_id] = array();
                }
            }

            $product_type_id=$customer_order_data[$orid]['product_type_id'];
            $selected_bdc = null;
            $selected_depot = null;
            $break_bdc_loop = false;

            //Loop through BDCs and foreach of their depots serve the order evenly
            foreach($bdc_ids as $bdc_id){
                if(count($bdc_control_order[$bdc_id]) == $current_total_order){
                    //If the total order for this BDC is equal to the control order count, then skip
                    continue;
                }
                else{//Add this order to the BDC, and get the right depot
                    //Get all depots for this BDC
                    $bdc_depots = $bdc_depots_products[$bdc_id];
                    $serving_depots_1 = array();
                    //Loop through all the depots and see which ones can serve this product.
                    foreach($bdc_depots as $key_depot => $product_array){
                        if(in_array($product_type_id,$product_array)){
                            $serving_depots_1[]= $key_depot;
                        }
                    }
                    $serving_depots = array();
                    //conform with Uppf depots
                    foreach($serving_depots_1 as $sdp){
                        if(in_array($sdp,$conform_del_loc_ids)){
                            $serving_depots[]=$sdp;
                        }
                    }

                    if(empty($serving_depots)){// This BDC can't serve this product so move to the next BDC
                        continue;
                    }
                    else{
                        //Reset $bdc_control_depot if all depots have received an order
                        $depots_ids_orders = array();
                        foreach($bdc_control_depot[$bdc_id] as $depot_x){
                            //if(isset($depot_arr['depot'])){
                                $depots_ids_orders[] = $depot_x;
                            //}
                        }
                        $diff_depots_arr = array_diff($serving_depots,$depots_ids_orders);
                        if(empty($diff_depots_arr)){ //if all serving depots have receive an order then reset.
                            $bdc_control_depot[$bdc_id] = array();
                        }

                        //foreach serving depots give order to depot that has none, if all has orders randomise depots and assign the order that has
                        $depots_with_orders = array();
                        foreach($bdc_control_order[$bdc_id] as $product_depot_arr){
                            if(isset($product_depot_arr['depot'])){
                                $depots_with_orders = $product_depot_arr['depot'];
                            }
                        }
                        $diff_depots_arr = array_diff($serving_depots,$depots_with_orders);
                        //Remove depots that have orders already
                        $depots_ids_orders = array();
                        foreach($bdc_control_depot[$bdc_id] as $depot_x){
                           // if(isset($depot_arr['depot'])){
                                $depots_ids_orders[] = $depot_x;
                            //}
                        }
                        if(!empty($depots_ids_orders)){//remove those depots with orders
                            $diff_depots_arr = array_diff($diff_depots_arr,$depots_ids_orders);
                        }

                        $rand_key = array_rand($diff_depots_arr, 1);
                        $rand_depot = $diff_depots_arr[$rand_key];
                        $selected_bdc = $bdc_id;
                        $selected_depot = $rand_depot;
                        $bdc_control_order[$bdc_id][] = array('depot'=>$selected_depot,'product'=>$product_type_id);
                        $bdc_control_depot[$bdc_id][] = $selected_depot;
                        $break_bdc_loop = true;

                        $test_shuffle[$bdc_id][] = $selected_depot;
                    }
                }
                if($break_bdc_loop){
                    break;
                }
            }

            $rand_key = array_rand($shuffle, 1);
            $val = $shuffle[$rand_key];
            if($val == 'approve'){
                $update_order[]=array(
                    'id'=>$orid,
                    'finance_approval'=>'Approved',
                    'approved_quantity'=>$customer_order_data[$orid]['order_quantity'],
                    'row_bg_color'=>'tr_green',
                    'order_status'=>'Pending Loading',
                    'edit_row'=>'no'
                );

                $order_allocation[]=array(
                    'order_date' => $next_date.' '.date('H:i:s'),
                    'bdc_id'=>$selected_bdc,
                    'omc_id'=>$customer_order_data[$orid]['omc_id'],
                    'omc_customer_id'=>$customer_order_data[$orid]['omc_customer_id'],
                    'depot_id'=>$selected_depot,
                    'product_type_id'=>$customer_order_data[$orid]['product_type_id'],
                    'order_quantity'=>$customer_order_data[$orid]['order_quantity'],
                    'omc_customer_order_id'=>$customer_order_data[$orid]['id'],
                    'omc_order_priority'=>$customer_order_data[$orid]['delivery_priority'],
                    'order_status'=>'New From Dealer',
                    'row_bg_color' => 'tr_mauve',
                    'record_type'=>'bdc',
                    'record_origin'=>'customer_order',
                    //'omc_created_by' => $authUser['id'],
                    'omc_created' => $next_date.' '.date('H:i:s')
                );
            }
            else{
                $update_order[]=array(
                    'id'=>$orid,
                    'finance_approval'=>'Not approved',
                    'row_bg_color'=>'tr_red',
                    'order_status'=>'Cancelled',
                    'edit_row'=>'no'
                );
            }
        }
        $this->Order->saveAll($order_allocation);
        return $this->Order->getInsertedIds();
    }


    function BdcOrderProcessingAndLoading($bdc_to_omc_order_ids,$cancel_order,$bdc_ids){
        $orders_data = $this->Order->find('all', array(
            'conditions' => array('Order.id' => $bdc_to_omc_order_ids),
            'recursive' => -1
        ));
        $update_order = $loading =  array();
        $quantities = range(1000, 100000, 1);
        $car_number_prefix_arr = array('GR','GT','GW','GA','GE','GZ','UV','BA','BR','NR','WR');
        $car_number_postfix_arr = range(1, 9999, 1);
        foreach($orders_data as $order_array){
            $order = $order_array['Order'];
            $update_order[]= array(
                'id'=>$order['id'],
                'bdc_modified' => date('Y-m-d H:i:s'),
                'bdc_modified_by' => '',
                'row_bg_color' => '',
                'order_status' => 'Complete',
                'finance_approval' => 'Ok',
                'approved_quantity' => $order['order_quantity'],
            );
            $time_in = date('H:i:s');
            $loading_date = $this->covertDate($order['order_date'],'mysql').' '.$time_in;
            $waybill_date = $this->covertDate($order['order_date'],'mysql').' '.$time_in;
            shuffle($quantities);
            $rand_key_1 = array_rand($quantities, 1);
            //debug($rand_key_1);
            $waybill_id = $quantities[$rand_key_1];
            //shuffle($quantities);
            $rand_key_2 = array_rand($quantities, 1);
            $collection_order_no = $quantities[$rand_key_2];
            shuffle($car_number_prefix_arr);
            $rand_key_3 = array_rand($car_number_prefix_arr, 1);
            $car_number_prefix = $car_number_prefix_arr[$rand_key_3];
            shuffle($car_number_postfix_arr);
            $rand_key_4 = array_rand($car_number_postfix_arr, 1);
            $car_number_postfix = $car_number_postfix_arr[$rand_key_4];
            $car_number = $car_number_prefix.' '.$car_number_postfix;

            //Flow to loading
            $loading[] = array(
                'bdc_id'=>$order['bdc_id'],
                'omc_id'=>$order['omc_id'],
                'loading_date'=>$loading_date,// this must be the loading date( the day the truck was loaded that is the day this record was created), not the order date
                'waybill_date'=>$waybill_date,
                'waybill_id'=>$waybill_id,
                'collection_order_no'=>$collection_order_no,
                'depot_id'=>$order['depot_id'],
                'product_type_id'=>$order['product_type_id'],
                'approved_quantity'=>$order['order_quantity'],
                'quantity'=>$order['order_quantity'],
                'vehicle_no'=>$car_number,
                'order_id'=>$order['id'],
                'row_bg_color' => '',
                'order_status'=>'Complete',
                'record_type'=>'bdc',
                'record_origin'=>'crm'
                //'created_by' => ''
            );

            //Flow to omc distribution
        }
        //OmcBdcDistribution
       // debug($update_order);
        $this->Order->saveAll($update_order);
        $this->BdcDistribution->saveAll($loading);
        return $this->BdcDistribution->getInsertedIds();
    }


    function OmcDistribution($bdc_loaded_ids,$cancel_order,$bdc_ids){
        $loading_data = $this->BdcDistribution->find('all', array(
            'fields'=>array('BdcDistribution.id','BdcDistribution.quantity','BdcDistribution.depot_id'),
            'conditions' => array('BdcDistribution.id' => $bdc_loaded_ids),
            'contain'=>array(
                'Order'=>array(
                    'fields'=>array('Order.id','Order.omc_customer_id','Order.omc_customer_order_id'),
                )
            )
        ));
        $working_depots = array();
        foreach($loading_data as $loaded_array){
            $working_depots[] = $loaded_array['BdcDistribution']['depot_id'];
        }
        $transporters = array('Ken','Ben','Henry','David','Kwame','John','Mensah','Victor','Prince','Francis','Dennis','Rober','Edwin','Rexford','Borris','Alex','Jimmy','Kevin');
        $invoice_numbers = range(1, 10000, 1);
        $distribute = $update_omc_order =  array();

        $delivery_locations = $this->get_delivery_locations();

        foreach($loading_data as $loaded_array){
            $dist = $loaded_array['BdcDistribution'];
            $ord = $loaded_array['Order'];

            $loading_depot = $dist['depot_id'];
            $depot_regions_data = $delivery_locations['data'][$loading_depot];
            //shuffle and pick a region
            $regions = array_keys($depot_regions_data['regions']);
            //$regions = array()
            shuffle($regions);
            $region_key = array_rand($regions, 1);
            $region_id = $regions[$region_key];
            $region_delivery_locations = $depot_regions_data['data'][$region_id];
            $all_reg_locations = array();
            foreach($region_delivery_locations['data'] as $loc){
                if(!empty($loc['distance']) && $loc['distance'] != null){
                    $all_reg_locations[] = array('loc_id'=>$loc['id'],'distance'=>$loc['distance']);
                }
            }
            shuffle($all_reg_locations);
            $location_key_3 = array_rand($all_reg_locations, 1);
            $del_loc_id = $all_reg_locations[$location_key_3]['loc_id'];
            $distance = $all_reg_locations[$location_key_3]['distance'];

            $uppf_data = $this->pre_process_uppf($dist['id'],$del_loc_id);

            shuffle($invoice_numbers);
            $rand_key_2 = array_rand($invoice_numbers, 1);
            $invoice_number = $invoice_numbers[$rand_key_2];

            shuffle($transporters);
            $rand_key_4 = array_rand($transporters, 1);
            $transporter = $transporters[$rand_key_4];

            $distribute[] = array(
                'invoice_number'=>$invoice_number,
                'bdc_distribution_id'=>$dist['id'],
                'omc_customer_id'=>$ord['id'],
                'quantity'=>$dist['quantity'],
                'region_id'=>$region_id,
                'delivery_location_id'=>$del_loc_id,
                'delivery_distance'=>$uppf_data['distance'],
                'freight_rate'=>$uppf_data['rate'],
                'transporter'=>$transporter
            );

            $update_omc_order[] = array(
                'id'=>$ord['omc_customer_order_id'],
                'delivery_quantity'=>$dist['quantity'],
                'row_bg_color'=>'',
                'order_status'=>'Complete',
                'edit_row'=>'no'
            );
        }

        $this->OmcBdcDistribution->saveAll($distribute);
        $this->OmcCustomerOrder->saveAll($update_omc_order);
        return true;
    }


    function OmcCustomersReduceStock(){

    }



    function get_delivery_locations(){
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
        $all = array();
        foreach ($lists_data as $value) {
            $all[] = $value['Depot']['id'];
            $lists[$value['Depot']['id']]['regions'][$value['Region']['id']]= $value['Region']['name'];
            $lists[$value['Depot']['id']]['data'][$value['Region']['id']]['name']= $value['Region']['name'];
            $lists[$value['Depot']['id']]['data'][$value['Region']['id']]['data'][]= $value['DeliveryLocation'];
        }
        $all = array_unique($all);

        return array(
            'depots'=>$all,
            'data'=>$lists
        );
    }


    function pre_process_uppf($distribution_id=null,$delivery_location_id=null){
        //Get Distribution Data
        $distribution = $this->BdcDistribution->find('first', array(
            'fields' => array('BdcDistribution.id','BdcDistribution.product_type_id','BdcDistribution.depot_id'),
            'conditions' => array('BdcDistribution.id' => $distribution_id),
            'contain' => array(
                'ProductType'=>array('fields' => array('ProductType.id', 'ProductType.freight_rate_category_id')),
            ),
            'recursive' => 1
        ));
        //Get Distance Data
        $distance_data = $this->DeliveryLocation->find('first', array(
            'fields' => array('DeliveryLocation.id','DeliveryLocation.distance'),
            'conditions' => array('DeliveryLocation.id' => $delivery_location_id),
            'recursive' => -1
        ));
        $r_distance = 0;
        $r_rate = 0;
        if($distance_data){
            //Get Rate Data
            $distance = intval($distance_data['DeliveryLocation']['distance']);
            if($distance < 0){
                $distance = $distance * -1;
            }

            if($distance && $distance > 0){
                $freight_rate = $this->FreightRate->find('first', array(
                    'fields' => array('FreightRate.id','FreightRate.rate'),
                    'conditions' => array('FreightRate.freight_rate_category_id' => $distribution['ProductType']['freight_rate_category_id'],'FreightRate.distance' => $distance),
                    'recursive'=>-1
                ));
                $r_distance = $distance;
                $r_rate = $freight_rate['FreightRate']['rate'];
            }
            else{
                $r_distance = 0;
                $r_rate = 0;
            }
        }
        else{
            $r_distance = 0;
            $r_rate = 0;
        }

        return array(
            'distance'=>$r_distance,
            'rate'=>$r_rate,
        );
    }


    function get_products($bdc){
        $depots_products = $this->Bdc->getDepotProduct($bdc);
        $product_ids = $depots_products['my_products'];
        return $this->get_product_list($product_ids);
    }
    function get_depot_list($bdc){
        $depots_products = $this->Bdc->getDepotProduct($bdc);
        $conditions = array('Depot.deleted' => 'n');
        $conditions['Depot.id'] = $depots_products['my_depots'];

        $bdc_depots = $this->Depot->find('all', array(
            'fields' => array('Depot.id', 'Depot.name','Depot.short_name'),
            'conditions' => $conditions,
            'recursive' => -1
        ));

        $bdc_depot_lists = array();
        foreach ($bdc_depots as $value) {
            //$bdc_depot_lists[] = $value['Depot'];
            $bdc_depot_lists[] = array(
                'id'=>$value['Depot']['id'],
                'name'=>$value['Depot']['name'],
            );
        }

        return $bdc_depot_lists;
    }

}