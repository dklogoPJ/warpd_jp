<?php
class OmcCustomerOrder extends AppModel
{
    /**
     * associations
     */
    var $hasOne = array(
        'Order' => array(
            'className' => 'Order',
            'foreignKey' => 'omc_customer_order_id',
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
        'OmcCustomer' => array(
            'className' => 'OmcCustomer',
            'foreignKey' => 'omc_customer_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        ),
        'Bdc' => array(
            'className' => 'Bdc',
            'foreignKey' => 'bdc_id',
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
    );



    function getOrders($type = 'omc',$id, $start_dt,$end_dt,$group_by,$filter_omc_customer){
        if ($type == 'omc') {
            $find_field = 'omc_id';
            $y_axis = array(
                array('name'=>'Complete','data'=>array()),
                array('name'=>'Pending','data'=>array()),
                array('name'=>'Cancelled','data'=>array())
            );
            $tb_head = array('Time','Complete','Pending','Cancelled','Total');
        } elseif ($type == 'omc_customer') {
            $find_field = 'omc_customer_id';
            $y_axis = array(
                array('name'=>'Complete','data'=>array()),
                array('name'=>'Pending','data'=>array()),
                array('name'=>'Cancelled','data'=>array())//,
                //array('name'=>'Not Processed','data'=>array())
            );
            //$tb_head = array('Time','Complete','Pending','Cancelled','Not Processed','Total');
            $tb_head = array('Time','Complete','Pending','Cancelled','Total');
        }
        $condition_array = array('OmcCustomerOrder.' . $find_field => $id, 'OmcCustomerOrder.order_date >=' => $start_dt, 'OmcCustomerOrder.order_date <=' => $end_dt, 'OmcCustomerOrder.deleted' => 'n');
        if ($type == 'omc') {
            $condition_array['OmcCustomerOrder.record_type']='omc';
        }
        if ($type == 'omc' && $filter_omc_customer !=null) {
            $condition_array['OmcCustomerOrder.omc_customer_id']=$filter_omc_customer;
        }

        $grid_data = $this->find('all', array(
            'fields' => array('OmcCustomerOrder.id', 'OmcCustomerOrder.order_status', 'OmcCustomerOrder.order_date','OmcCustomerOrder.order_quantity','OmcCustomerOrder.delivery_quantity','OmcCustomerOrder.received_quantity'),
            'conditions' => $condition_array,
            //'contain' => array('OmcCustomerOrder' => array('fields' => array('BdcDistribution.id', 'BdcDistribution.quantity'))),
            'order' => array('OmcCustomerOrder.id' => 'desc'),
            'recursive' => -1
        ));
        $grouped_data = array();
        if ($grid_data) {//If orders then group by monthly or yearly
            foreach ($grid_data as $data) {
                $order_date = $data['OmcCustomerOrder']['order_date'];
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
                $status = $d['OmcCustomerOrder']['order_status'];
                if($status == 'Complete'){
                    $add = preg_replace('/,/','',$d['OmcCustomerOrder']['received_quantity']);
                    $completed = $completed + $add;
                }
                elseif($status == 'Cancelled'){
                    $add = preg_replace('/,/','',$d['OmcCustomerOrder']['order_quantity']);
                    $cancelled = $cancelled + $add;
                }
                elseif($status == 'Processing'){
                    $add = preg_replace('/,/','',$d['OmcCustomerOrder']['delivery_quantity']);
                    if(!$add){
                        $add =  preg_replace('/,/','',$d['OmcCustomerOrder']['order_quantity']);
                    }
                    $processing = $processing + $add;
                }
                elseif($status == 'Pending Loading'){
                    $add = preg_replace('/,/','',$d['OmcCustomerOrder']['order_quantity']);
                    $pending = $pending + $add;
                }
                elseif($status == 'New') {
                    $add = preg_replace('/,/', '', $d['OmcCustomerOrder']['order_quantity']);
                    $new = $new + $add;
                    $add = 0;
                }
                else{
                    $add = preg_replace('/,/','',$d['OmcCustomerOrder']['delivery_quantity']);
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


    function getStationProduct($omc_id,$start_dt,$end_dt,$product_type_id=null,$omc_customer=null)
    {
        $conditions = array('OmcCustomerOrder.omc_id' => $omc_id, 'OmcCustomerOrder.record_type' => 'omc', 'OmcCustomerOrder.order_status' => 'Complete', 'OmcCustomerOrder.deleted' => 'n','OmcCustomerOrder.order_date >=' => $start_dt, 'OmcCustomerOrder.order_date <=' => $end_dt);
        if($product_type_id){
            $conditions['OmcCustomerOrder.product_type_id'] = $product_type_id;
        }
        if($omc_customer){
            $conditions['OmcCustomerOrder.omc_customer_id'] = $omc_customer;
        }
        $export_data = $this->find('all', array(
            'fields'=>array('OmcCustomerOrder.id','OmcCustomerOrder.order_date','OmcCustomerOrder.order_quantity','OmcCustomerOrder.delivery_quantity','OmcCustomerOrder.received_quantity'),
            'conditions' => $conditions,
            'contain'=>array(
                'Omc'=>array('fields'=>array('Omc.id','Omc.name')),
                'ProductType'=>array('fields'=>array('ProductType.id','ProductType.short_name'))
            ),
            'order' => array("OmcCustomerOrder.order_date"=>'asc'),
            'recursive' => 1
        ));
        $gr = array();
        if($export_data){
            $needed_items = array();
            foreach ($export_data as $data) {
                $load_dt_arr = explode('-',$data['OmcCustomerOrder']['order_date']);
                $load_dt = $load_dt_arr[1].'/'.$load_dt_arr[0];
                $pro_id = $data['ProductType']['id'];
                $pro_name = $data['ProductType']['short_name'];
                $quantity = $data['OmcCustomerOrder']['received_quantity'];
                if(!$quantity){
                    $quantity = $data['OmcCustomerOrder']['delivery_quantity'];
                }
                if(!$quantity){
                    $quantity = $data['OmcCustomerOrder']['order_quantity'];
                }
                $pro_qty = preg_replace('/,/','',$quantity);
                if(isset($needed_items[$pro_name])){}
                else{
                    $needed_items[$pro_name] = $pro_name;
                }
                if (isset($gr[$load_dt]) && isset($gr[$load_dt][$pro_id])) {
                    $gr[$load_dt][$pro_id]['qty'] = $gr[$load_dt][$pro_id]['qty'] + $pro_qty;
                } else { //New
                    $gr[$load_dt][$pro_id] = array(
                        'qty' => $pro_qty,
                        'name' => $pro_name
                    );
                }
            }
        }

        $gr_dt = array();
        foreach ($gr as $key => $value_arr) {
            $dow = array();
            ksort($value_arr);
            foreach ($value_arr as $key2 => $val) {
                $dow[$val['name']] = $val['qty'];
            }
            $gr_dt[$key] = $dow;
        }

        $days_arr = array();
        $products_arr1 = array();
        foreach ($gr_dt as $day => $data_arr) {
            $days_arr[] = $day;
            foreach ($data_arr as $name => $qty) {
                $products_arr1[$name][] = floatval($qty);
            }
        }
        //Arrange each product and it data
        $products_arr1 = array();
        foreach($days_arr as $day){
            foreach ($needed_items as $item) {
                if(isset($gr_dt[$day][$item])){
                    $products_arr1[$item]['data'][]= intval($gr_dt[$day][$item]);
                }
                else{
                    $products_arr1[$item]['data'][]= 0;
                }
            }
        }

        $products_arr = array();
        foreach ($products_arr1 as $pro => $qty_arr) {
            $products_arr[] = array(
                'name' => $pro,
                'data' => $qty_arr['data']
            );
        }

        //Prepare the table data headers
        $data_header = array();
        foreach($gr_dt as $dt=>$arr){
            foreach($arr as $id=>$val){
                $data_header[] = $id;
            }
        }
        $unig_arr = array_unique($data_header);
        sort($unig_arr);
        $t_head = array_merge(array('Date'),$unig_arr);
        //debug($t_head);
        $t_body_data = array();
        foreach($gr_dt as $dt=>$arr){
            $pr = array();
            foreach($t_head as $tkey => $tval){
                $pr[$tkey]='0';
            }
            $pr[0] = $dt;
            foreach($arr as $product_name=>$ltrs){
                $key = array_search($product_name,$t_head);
                $pr[$key]= $ltrs;
            }
            $t_body_data[] = $pr;
        }
        //debug($t_body_data);
        //Total the tbody data
        $tbody_totals = array();
        foreach($t_body_data as $dt){
            foreach($dt as $tbkey=>$tbval){
                if($tbkey == 0){
                    $tbody_totals[0] = '<strong>Total<strong/>';
                }
                else{
                    if(isset($tbody_totals[$tbkey])){
                        $tbody_totals[$tbkey] = intval($tbody_totals[$tbkey]) + intval($tbval);
                    }
                    else{
                        $tbody_totals[$tbkey] =  intval($tbval);
                    }
                }
            }
        }
        $t_body_data[] = $tbody_totals;

        return array(
            'x-axis' => $days_arr,
            'data' => $products_arr,
            'raw_data'=>$gr_dt,
            't_head'=>$t_head,
            't_body_data'=>$t_body_data
        );
    }


}