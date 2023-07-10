<?php
class Order extends AppModel
{
    /**
     * associations
     */
    var $hasOne = array(
        'BdcDistribution' => array(
            'className' => 'BdcDistribution',
            'foreignKey' => 'order_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Waybill' => array(
            'className' => 'Waybill',
            'foreignKey' => 'order_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        )
    );

    var $belongsTo = array(
        'Bdc' => array(
            'className' => 'Bdc',
            'foreignKey' => 'bdc_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        ),
        'Omc' => array(
            'className' => 'Omc',
            'foreignKey' => 'omc_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        ),
        'ProductType' => array(
            'className' => 'ProductType',
            'foreignKey' => 'product_type_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        ),
        'Depot' => array(
            'className' => 'Depot',
            'foreignKey' => 'depot_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        ),
        'Cep' => array(
            'className' => 'Cep',
            'foreignKey' => 'cep_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        ),
        'OmcCustomer' => array(
            'className' => 'OmcCustomer',
            'foreignKey' => 'omc_customer_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        ),
        'OmcCustomerOrder' => array(
            'className' => 'OmcCustomerOrder',
            'foreignKey' => 'omc_customer_order_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        )
    );



    function getOrderById($order_id){
        $order = $this->find('first',array(
            'conditions'=>array('id'=>$order_id),
            'recursive'=>-1
        ));
        if($order){
            return $order['Order'];
        }
        else{
            return false;
        }
    }


    function getOrderByDepotProduct($bdc_id,$date,$depot,$product){
        $lifting = $this->find('all',array(
            'conditions'=>array('bdc_id'=>$bdc_id, 'record_type'=>'bdc' ,'order_date LIKE '=>$date.'%','depot_id'=>$depot,'product_type_id'=>$product, 'NOT'=>array('order_status'=>array('Cancelled','Complete'))),
            'recursive'=>-1
        ));
        $total = 0;
        foreach($lifting as $stck_upt){
            $total = $total + preg_replace('/,/','',$stck_upt['Order']['order_quantity']);
        }
        return $total;
    }


    function getOrders($type = 'bdc',$id, $start_dt,$end_dt,$group_by,$filter_bdc,$filter_omc){
        if ($type == 'bdc') {
            $find_field = 'bdc_id';
            $y_axis = array(
                array('name'=>'Complete','data'=>array()),
                array('name'=>'Pending','data'=>array()),
                array('name'=>'Cancelled','data'=>array())
            );
            $tb_head = array('Time','Complete','Pending','Cancelled','Total');
        } elseif ($type == 'omc') {
            $find_field = 'omc_id';
            $y_axis = array(
                array('name'=>'Complete','data'=>array()),
                array('name'=>'Pending','data'=>array()),
                array('name'=>'Cancelled','data'=>array())//,
                //array('name'=>'Not Processed','data'=>array())
            );
            //$tb_head = array('Time','Complete','Pending','Cancelled','Not Processed','Total');
            $tb_head = array('Time','Complete','Pending','Cancelled','Total');
        }
        $condition_array = array('Order.' . $find_field => $id, 'Order.order_date >=' => $start_dt, 'Order.order_date <=' => $end_dt, 'Order.deleted' => 'n');
        if ($type == 'bdc') {
            $condition_array['Order.record_type']='bdc';
        }
        if ($type == 'bdc' && $filter_omc !=null) {
            $condition_array['Order.omc_id']=$filter_omc;
        }
        elseif($type == 'omc' && $filter_bdc !=null){
            $condition_array['Order.bdc_id']=$filter_bdc;
        }

        $grid_data = $this->find('all', array(
            'fields' => array('Order.id', 'Order.order_status', 'Order.order_date','Order.order_quantity','Order.approved_quantity'),
            'conditions' => $condition_array,
            'contain' => array('BdcDistribution' => array('fields' => array('BdcDistribution.id', 'BdcDistribution.quantity'))),
            'order' => array('Order.id' => 'desc'),
            'recursive' => 1
        ));
        $grouped_data = array();
        if ($grid_data) {//If orders then group by monthly or yearly
            foreach ($grid_data as $data) {
                $order_date = $data['Order']['order_date'];
                $dates_arr = explode('-',$order_date);
                $year = $dates_arr[0];
                $month = $dates_arr[1];
                $group_index = $year;
                if($group_by == 'monthly' ){
                    $group_index = $month.'/'.$year;
                }
                $grouped_data[$group_index][] = $data;
            }
        }

        //Now consolidate based on order status foreach group
        $x_axis = array();
        $tb_body = array();
        $raw_data = array();
        $tl_completed = 0;
        $tl_processing = 0;
        $tl_pending = 0;
        $tl_new = 0;
        $tl_new_from_dealer = 0;
        $tl_cancelled = 0;
        $tl_total = 0;
        foreach ($grouped_data as $x_ax => $arr) {
            $completed = 0;
            $processing = 0;
            $pending = 0;
            $new = 0;
            $new_from_dealer = 0;
            $cancelled = 0;
            $total = 0;
            $tb_row = array();
            foreach ($arr as $d) {
                $status = $d['Order']['order_status'];
                if($status == 'Complete'){
                    $add = preg_replace('/,/','',$d['BdcDistribution']['quantity']);
                    $completed = $completed + $add;
                }
                elseif($status == 'Cancelled'){
                    $add = preg_replace('/,/','',$d['Order']['order_quantity']);
                    $cancelled = $cancelled + $add;
                }
                elseif($status == 'Processing'){
                    $add = preg_replace('/,/','',$d['Order']['approved_quantity']);
                    if(!$add){
                        $add =  preg_replace('/,/','',$d['Order']['order_quantity']);
                    }
                    $processing = $processing + $add;
                }
                elseif($status == 'Pending Loading'){
                    $add = preg_replace('/,/','',$d['Order']['order_quantity']);
                    $pending = $pending + $add;
                }
                elseif($status == 'New'){
                    $add = preg_replace('/,/','',$d['Order']['order_quantity']);
                    $new = $new + $add;
                    $add = 0;
                }
                elseif($status == 'New From Dealer'){
                    $add = preg_replace('/,/','',$d['Order']['order_quantity']);
                    $new_from_dealer = $new_from_dealer + $add;
                    $add = 0;
                }
                else{
                    $add = preg_replace('/,/','',$d['Order']['approved_quantity']);
                    $new_from_dealer = $new_from_dealer + $add;
                    $add = 0;
                }

                $total = $total + $add;
            }

            //Now push consolidated to x and y-axis
            $x_axis[] = $tb_row[] = $x_ax;
            $y_axis[0]['data'][]= $tb_row[] =$completed; //Complete
            $y_axis[1]['data'][]= $tb_row[] =$pending+$processing; //Pending
            $y_axis[2]['data'][]= $tb_row[] =$cancelled; //Cancelled
            if($type == 'omc') {
                //$y_axis[3]['data'][]= $tb_row[] = $new; //Not Processed
                $raw_data[$x_ax] = array(
                    'Complete'=>$completed,
                    'Pending'=>$pending+$processing,
                    'Cancelled'=>$cancelled,
                    //'Not Processed'=>$new,
                    'Total'=>$total
                );
            }
            else{
                $raw_data[$x_ax] = array(
                    'Complete'=>$completed,
                    'Pending'=>$pending+$processing,
                    'Cancelled'=>$cancelled,
                    'Total'=>$total
                );
            }
            $tb_row[] = $total;

            $tb_body[]=$tb_row;

            $tl_completed = $tl_completed + $completed;
            $tl_processing = $tl_processing + $processing;
            $tl_pending = $tl_pending + $pending;
            $tl_new = $tl_new + $new;
            $tl_new_from_dealer = $tl_new_from_dealer + $new_from_dealer;
            $tl_cancelled = $tl_cancelled + $cancelled;
            $tl_total = $tl_total + $total;

        }
        $tr = array('Total',$tl_completed, $tl_processing + $tl_pending, $tl_cancelled );
        if($type == 'omc') {
            //$tr[]= $tl_new;
        }
        $tr[]= $tl_total;
        $tb_body[]=$tr;

        return array(
            'data' => array(
                'x-axis'=>$x_axis,
                'y-axis'=>$y_axis
            ),
            'table'=>array(
                'thead'=>$tb_head,
                'tbody'=>$tb_body
            ),
            'raw_data'=>$raw_data
        );
    }

    function loadingToday($type,$org_id,$depot_id = '0'){
        return $this->loadingBoard ($type,$org_id,$depot_id,'Not Loaded');
    }

    function loadedToday($type,$org_id,$depot_id = '0'){
        return $this->loadingBoard ($type,$org_id,$depot_id,'Loaded');
    }

    function loadingBoard ($type,$org_id,$depot_id,$loading_type){
        $loading_today = date('Y-m-d');
        if($type == 'bdc'){
            $find_column = 'bdc_id';
        }
        elseif($type == 'omc'){
            $find_column = 'omc_id';
        }
        elseif($type == 'depot'){
            $find_column = 'depot_id';
        }
        elseif($type == 'ceps_depot'){
            $find_column = 'cep_id';
        }
        elseif($type == 'omc_customer'){
            $find_column = 'omc_customer_id';
        }

        $conditions = array('Order.'.$find_column =>$org_id,'Order.ceps_approval'=>'Approved','Order.depot_loadding_approval'=>$loading_type);
       /* if($loading_type == 'Not Loaded'){
            $conditions['Order.ceps_modified LIKE']=$loading_today.' %';
        }*/
        if($loading_type == 'Loaded'){
            $conditions['Order.loaded_date LIKE']=$loading_today.' %';
        }
        if($depot_id != '0'){
            $conditions['Order.depot_id']=$depot_id;
        }
        $loading_orders = $this->find('all',array(
            'fields'=>array('Order.id','Order.approved_quantity','Order.depot_id','Order.truck_no'),
            'conditions'=>$conditions,
            'contain'=>array(
                'Depot'=>array('fields'=>array('Depot.id','Depot.name')),
                'ProductType'=>array('fields'=>array('ProductType.id','ProductType.name'))
            ),
            'recursive'=>1
        ));
        $loading_arr = array();
        foreach($loading_orders as $val){
            if(isset($loading_arr[$val['Order']['depot_id']])){
                $loading_arr[$val['Order']['depot_id']]['data'][]=array(
                    'order_id'=>$val['Order']['id'],
                    'loading_product' =>$val['ProductType']['name'],
                    'loading_quantity' =>$this->formatNumber($val['Order']['approved_quantity'],'money',0),
                    'truck_no' => $val['Order']['truck_no']
                );
            }
            else{
                $loading_arr[$val['Order']['depot_id']]['info']=array(
                    'depot'=>$val['Depot']['name']
                );
                $loading_arr[$val['Order']['depot_id']]['data'][]=array(
                    'order_id'=>$val['Order']['id'],
                    'loading_product' =>$val['ProductType']['name'],
                    'loading_quantity' =>$this->formatNumber($val['Order']['approved_quantity'],'money',0),
                    'truck_no' => $val['Order']['truck_no']
                );
            }
        }
        return $loading_arr;
    }



}