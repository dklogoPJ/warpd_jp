<?php
class BdcDistribution extends AppModel
{
    /**
     * associations
     */
    var $hasMany = array(
        'OmcBdcDistribution' => array(
            'className' => 'OmcBdcDistribution',
            'foreignKey' => 'bdc_distribution_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ));

    var $belongsTo = array(
        'Bdc' => array(
            'className' => 'Bdc',
            'foreignKey' => 'bdc_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        ),
        'Order' => array(
            'className' => 'Order',
            'foreignKey' => 'order_id',
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

        'Depot' => array(
            'className' => 'Depot',
            'foreignKey' => 'depot_id',
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

        'Region' => array(
            'className' => 'Region',
            'foreignKey' => 'region_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        ),

        'District' => array(
            'className' => 'District',
            'foreignKey' => 'district_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        )
    );


    function getLiftingQuantity($bdc_id,$date,$depot,$product){
        $lifting = $this->find('all',array(
            'conditions'=>array('bdc_id'=>$bdc_id,'waybill_date LIKE '=>$date.'%','depot_id'=>$depot,'product_type_id'=>$product),
            'recursive'=>-1
        ));
        $total = 0;
        foreach($lifting as $stck_upt){
            $total = $total + $stck_upt['BdcDistribution']['quantity'];
        }
        return $total;
    }


    function getTodayAndYesterday($id, $type = 'bdc'){
        $today = date('Y-m-d');
        $yesterday = date("Y-m-d", strtotime("-1 day"));
        //$totals = array('today','yesterday');
        //$find_id = 'bdc';
        if ($type == 'bdc') {
            $find_id = 'bdc_id';
        } elseif ($type == 'omc') {
            $find_id = 'omc_id';
        }
        $conditon_arr = array('BdcDistribution.' . $find_id => $id, 'BdcDistribution.loading_date LIKE' => $today . "%", 'BdcDistribution.order_status' => 'Complete', 'BdcDistribution.deleted' => 'n');
        if ($type == 'bdc') {
            $conditon_arr['BdcDistribution.record_type']='bdc';
        }

        $today_grid_data = $this->find('all', array(
            'conditions' => $conditon_arr,
            'order' => array('BdcDistribution.id' => 'desc'),
            'recursive' => 1
        ));
        $liters_per_products = array();
        foreach ($today_grid_data as $data) {
            $pro_id = $data['ProductType']['id'];
            $pro_name = $data['ProductType']['name'];
            $pro_qty = preg_replace('/,/','',$data['BdcDistribution']['quantity']);
            if (isset($liters_per_products[$pro_id])) {
                $liters_per_products[$pro_id]['qty'] = $liters_per_products[$pro_id]['qty'] + $pro_qty;
            } else { //New
                $liters_per_products[$pro_id] = array(
                    'qty' => $pro_qty,
                    'name' => $pro_name
                );
            }
        }
        $tl1 = 0.00;
        foreach ($liters_per_products as $data) {
            $pro_qty = $data['qty'];
            $tl1 = $tl1 + floatval($pro_qty);
        }
        $totals['today'] = $tl1;
        /************** Yesterday */
        $conditon_arr = array('BdcDistribution.' . $find_id => $id, 'BdcDistribution.loading_date LIKE' => $yesterday . "%", 'BdcDistribution.order_status' => 'Complete', 'BdcDistribution.deleted' => 'n');
        if ($type == 'bdc') {
            $conditon_arr['BdcDistribution.record_type']='bdc';
        }

        $yesterday_grid_data = $this->find('all', array(
            'conditions' => $conditon_arr,
            'order' => array('BdcDistribution.id' => 'desc'),
            'recursive' => 1
        ));
        $liters_per_products = array();
        foreach ($yesterday_grid_data as $data) {
            $pro_id = $data['ProductType']['id'];
            $pro_name = $data['ProductType']['name'];
            $pro_qty = preg_replace('/,/','',$data['BdcDistribution']['quantity']);
            if (isset($liters_per_products[$pro_id])) {
                $liters_per_products[$pro_id]['qty'] = $liters_per_products[$pro_id]['qty'] + $pro_qty;
            } else { //New
                $liters_per_products[$pro_id] = array(
                    'qty' => $pro_qty,
                    'name' => $pro_name
                );
            }
        }
        $tl1 = 0.00;
        foreach ($liters_per_products as $data) {
            $pro_qty = $data['qty'];
            $tl1 = $tl1 + floatval($pro_qty);
        }
        $totals['yesterday'] = $tl1;

        return $totals;
    }


    function getTodayConsolidated($id, $type = 'bdc'){
        if ($type == 'bdc') {
            $find_id = 'bdc_id';
        } elseif ($type == 'omc') {
            $find_id = 'omc_id';
        }
        $today = date('Y-m-d');
        $condition_array = array('BdcDistribution.' . $find_id => $id, 'BdcDistribution.loading_date LIKE' => $today . "%", 'BdcDistribution.order_status' => 'Complete', 'BdcDistribution.deleted' => 'n');
        if($type == 'bdc') {
            $condition_array['BdcDistribution.record_type']='bdc';
        }
        $grid_data = $this->find('all', array(
            'conditions' => $condition_array,
            'order' => array('BdcDistribution.id' => 'desc'),
            'recursive' => 1
        ));

        // Total liters per products per day
        $liters_per_products = array();
        foreach ($grid_data as $data) {
            $pro_id = $data['ProductType']['id'];
            $pro_name = $data['ProductType']['name'];
            $pro_qty = preg_replace('/,/','',$data['BdcDistribution']['quantity']);
            if (isset($liters_per_products[$pro_id])) {
                $liters_per_products[$pro_id]['qty'] = $liters_per_products[$pro_id]['qty'] + $pro_qty;
            } else { //New
                $liters_per_products[$pro_id] = array(
                    'qty' => $pro_qty,
                    'name' => $pro_name
                );
            }
        }

        $return['liters_per_products'] = $liters_per_products;
        $return['grid_data'] = $grid_data;

        return $return;
    }


    function getBarGraphData($id, $type = 'bdc',$dates_arr){
        $day_of_week = array(
            0 => 'Sun', 1 => 'Mon', 2 => 'Tue', 3 => 'Wed', 4 => 'Thr', 5 => 'Fri', 6 => 'Sat'
        );
        if ($type == 'bdc') {
            $find_id = 'bdc_id';
        } elseif ($type == 'omc') {
            $find_id = 'omc_id';
        }

        ksort($dates_arr);
        $gr = array();
        $needed_items = array();
        foreach ($dates_arr as $key => $value) {
            $condition_array = array('BdcDistribution.' . $find_id => $id, 'BdcDistribution.loading_date LIKE' => $value . '%', 'BdcDistribution.order_status' => 'Complete', 'BdcDistribution.deleted' => 'n');
            if ($type == 'bdc') {
                $condition_array['BdcDistribution.record_type']='bdc';
            }
            $grid_data = $this->find('all', array(
                'fields' => array('BdcDistribution.id', 'BdcDistribution.quantity', 'BdcDistribution.quantity', 'BdcDistribution.product_type_id', 'BdcDistribution.created'),
                'conditions' => $condition_array,
                'contain' => array('ProductType' => array('fields' => array('ProductType.id', 'ProductType.short_name'))),
                'order' => array('BdcDistribution.id' => 'desc'),
                'recursive' => 1
            ));
            $products = array();
            if ($grid_data) {
                $pr_ids = array();
                foreach ($grid_data as $data) {
                    $pro_id = $data['ProductType']['id'];
                    $pro_name = $data['ProductType']['short_name'];
                    if(isset($needed_items[$pro_name])){}
                    else{
                        $needed_items[$pro_name] = $pro_name;
                    }
                    $pro_qty = preg_replace('/,/','',$data['BdcDistribution']['quantity']);

                    if (isset($products[$pro_id])) {
                        $products[$pro_id]['qty'] = $products[$pro_id]['qty'] + $pro_qty;
                    } else { //New
                        $products[$pro_id] = array(
                            'qty' => $pro_qty,
                            'name' => $pro_name
                        );
                    }
                    $pr_ids[$pro_id] = $pro_id;
                }
                //Add the rest of the products and set their qty to zero.
                /*$arr_diff = array_diff_key($products_data, $pr_ids);
                foreach ($arr_diff as $id1 => $name1) {
                    $products[$id1] = array(
                        'qty' => 0,
                        'name' => $name1
                    );
                }*/
                $gr[$day_of_week[$key]] = $products;
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

        return array(
            'days' => $days_arr,
            'data' => $products_arr
        );
    }


    function getProductMonthlyConsolidateBDC($bdc_id,$start_dt,$end_dt,$product_type_id=null,$omc=null)
    {
        $conditions = array('BdcDistribution.bdc_id' => $bdc_id, 'BdcDistribution.record_type' => 'bdc', 'BdcDistribution.order_status' => 'Complete', 'BdcDistribution.deleted' => 'n','BdcDistribution.loading_date >=' => $start_dt, 'BdcDistribution.loading_date <=' => $end_dt);
        if($product_type_id){
            $conditions['BdcDistribution.product_type_id'] = $product_type_id;
        }
        if($omc){
            $conditions['BdcDistribution.omc_id'] = $omc;
        }
        $export_data = $this->find('all', array(
            'fields'=>array('BdcDistribution.id','BdcDistribution.loading_date','BdcDistribution.quantity','BdcDistribution.waybill_id','BdcDistribution.vehicle_no'),
            'conditions' => $conditions,
            'contain'=>array(
                'Omc'=>array('fields'=>array('Omc.id','Omc.name')),
                'Depot'=>array('fields'=>array('Depot.id','Depot.name')),
                'ProductType'=>array('fields'=>array('ProductType.id','ProductType.short_name'))/*,
                'Region'=>array('fields'=>array('Region.id','Region.name')),
                'District'=>array('fields'=>array('District.id','District.name'))*/
            ),
            'order' => array("BdcDistribution.loading_date"=>'asc'),
            'recursive' => 1
        ));
        $gr = array();
        if($export_data){
            $needed_items = array();
            foreach ($export_data as $data) {
                $load_dt_arr = explode('-',$data['BdcDistribution']['loading_date']);
                $load_dt = $load_dt_arr[1].'/'.$load_dt_arr[0];
                $pro_id = $data['ProductType']['id'];
                $pro_name = $data['ProductType']['short_name'];
                $pro_qty = preg_replace('/,/','',$data['BdcDistribution']['quantity']);
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

    function getMonthlyVariantBDC($bdc_id,$month,$year,$query_filter,$product_type_id=null)
    {
        $year_month  = $year.'-'.$month;
        $conditions = array('BdcDistribution.bdc_id' => $bdc_id, 'BdcDistribution.record_type' => 'bdc', 'BdcDistribution.order_status' => 'Complete', 'BdcDistribution.deleted' => 'n','BdcDistribution.loading_date  LIKE' => $year_month.'%');
        if($product_type_id){
            $conditions['BdcDistribution.product_type_id'] = $product_type_id;
        }
        $export_data = $this->find('all', array(
            'fields'=>array('BdcDistribution.id','BdcDistribution.loading_date','BdcDistribution.quantity','BdcDistribution.waybill_id','BdcDistribution.vehicle_no'),
            'conditions' => $conditions,
            'contain'=>array(
                'Omc'=>array('fields'=>array('Omc.id','Omc.name')),
                'Depot'=>array('fields'=>array('Depot.id','Depot.name')),
                'ProductType'=>array('fields'=>array('ProductType.id','ProductType.short_name')),
                //'Region'=>array('fields'=>array('Region.id','Region.name')),
                //'District'=>array('fields'=>array('District.id','District.name'))
            ),
            //'order' => array("BdcDistribution.id"=>'desc'),
            'recursive' => 1
        ));
        $gr = array();
        if($export_data){
            foreach ($export_data as $data) {
                if($query_filter == 'Omc'){
                    $pro_id = $data['Omc']['id'];
                    $pro_name = $data['Omc']['name'];
                }
                elseif($query_filter == 'Depot'){
                    $pro_id = $data['Depot']['id'];
                    $pro_name = $data['Depot']['name'];
                }
                $pro_qty = intval(preg_replace('/,/','',$data['BdcDistribution']['quantity']));
                if (isset($gr[$pro_id])) {
                    $gr[$pro_id]['qty'] = $gr[$pro_id]['qty'] + $pro_qty;
                } else { //New
                    $gr[$pro_id] = array(
                        'qty' => $pro_qty,
                        'name' => $pro_name
                    );
                }
            }
        }

        $products_arr = array();
        foreach ($gr as $pro => $qty_arr) {
            $products_arr[] = array(
                $qty_arr['name'],
                $qty_arr['qty']
            );
        }
        $pie_data = $products_arr;
        $raw_pie = $pie_data;
        if ($pie_data) {
            $first_pie_data = $pie_data[0];
            $pie_data[0] = array(
                'name' => $first_pie_data[0],
                'y' => $first_pie_data[1],
                'sliced' => true,
                'selected' => true
            );
        }

        return array(
            'data' => $pie_data,
            'raw_data'=>$raw_pie
        );
    }


    function getProductMonthlyConsolidateOMC($omc_id,$start_dt,$end_dt,$product_type_id=null)
    {
        //$conditions = array('BdcDistribution.omc_id' => $omc_id, 'BdcDistribution.record_type' => 'bdc', 'BdcDistribution.deleted' => 'n','BdcDistribution.loading_date >=' => $start_dt, 'BdcDistribution.loading_date <=' => $end_dt);
        $conditions = array('BdcDistribution.omc_id' => $omc_id, 'BdcDistribution.order_status' => 'Complete', 'BdcDistribution.deleted' => 'n','BdcDistribution.loading_date >=' => $start_dt, 'BdcDistribution.loading_date <=' => $end_dt);
        if($product_type_id){
            $conditions['BdcDistribution.product_type_id'] = $product_type_id;
        }
        $export_data = $this->find('all', array(
            'fields'=>array('BdcDistribution.id','BdcDistribution.loading_date','BdcDistribution.quantity','BdcDistribution.waybill_id','BdcDistribution.vehicle_no'),
            'conditions' => $conditions,
            'contain'=>array(
                //'Bcd'=>array('fields'=>array('Bcd.id','Bcd.name')),
                'Omc'=>array('fields'=>array('Omc.id','Omc.name')),
                'Depot'=>array('fields'=>array('Depot.id','Depot.name')),
                'ProductType'=>array('fields'=>array('ProductType.id','ProductType.short_name'))/*,
                'Region'=>array('fields'=>array('Region.id','Region.name')),
                'District'=>array('fields'=>array('District.id','District.name'))*/
            ),
            'order' => array("BdcDistribution.loading_date"=>'asc'),
            'recursive' => 1
        ));
        $gr = array();
        if($export_data){
            $needed_items = array();
            foreach ($export_data as $data) {
                $load_dt_arr = explode('-',$data['BdcDistribution']['loading_date']);
                $load_dt = $load_dt_arr[1].'/'.$load_dt_arr[0];
                $pro_id = $data['ProductType']['id'];
                $pro_name = $data['ProductType']['short_name'];
                $pro_qty = preg_replace('/,/','',$data['BdcDistribution']['quantity']);
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


    function getMonthlyVariantOMC($omc_id,$month,$year,$query_filter,$product_type_id=null)
    {
        $year_month  = $year.'-'.$month;
        //$conditions = array('BdcDistribution.omc_id' => $omc_id, 'BdcDistribution.record_type' => 'bdc', 'BdcDistribution.deleted' => 'n','BdcDistribution.loading_date  LIKE' => $year_month.'%');
        $conditions = array('BdcDistribution.omc_id' => $omc_id, 'BdcDistribution.order_status' => 'Complete',  'BdcDistribution.deleted' => 'n','BdcDistribution.loading_date  LIKE' => $year_month.'%');
        if($product_type_id){
            $conditions['BdcDistribution.product_type_id'] = $product_type_id;
        }
        $export_data = $this->find('all', array(
            'fields'=>array('BdcDistribution.id','BdcDistribution.loading_date','BdcDistribution.quantity','BdcDistribution.waybill_id','BdcDistribution.vehicle_no'),
            'conditions' => $conditions,
            'contain'=>array(
                'Bdc'=>array('fields'=>array('Bdc.id','Bdc.name')),
                //'Omc'=>array('fields'=>array('Omc.id','Omc.name')),
                'Depot'=>array('fields'=>array('Depot.id','Depot.name')),
                'ProductType'=>array('fields'=>array('ProductType.id','ProductType.short_name')),
                //'Region'=>array('fields'=>array('Region.id','Region.name')),
                //'District'=>array('fields'=>array('District.id','District.name'))
            ),
            //'order' => array("BdcDistribution.id"=>'desc'),
            'recursive' => 1
        ));
        $gr = array();
        if($export_data){
            foreach ($export_data as $data) {
                if($query_filter == 'Bdc'){
                    $pro_id = $data['Bdc']['id'];
                    $pro_name = $data['Bdc']['name'];
                }
                elseif($query_filter == 'Depot'){
                    $pro_id = $data['Depot']['id'];
                    $pro_name = $data['Depot']['name'];
                }
                $pro_qty = intval(preg_replace('/,/','',$data['BdcDistribution']['quantity']));
                if (isset($gr[$pro_id])) {
                    $gr[$pro_id]['qty'] = $gr[$pro_id]['qty'] + $pro_qty;
                } else { //New
                    $gr[$pro_id] = array(
                        'qty' => $pro_qty,
                        'name' => $pro_name
                    );
                }
            }
        }

        $products_arr = array();
        foreach ($gr as $pro => $qty_arr) {
            $products_arr[] = array(
                $qty_arr['name'],
                $qty_arr['qty']
            );
        }
        $pie_data = $products_arr;
        $raw_pie = $pie_data;
        if ($pie_data) {
            $first_pie_data = $pie_data[0];
            $pie_data[0] = array(
                'name' => $first_pie_data[0],
                'y' => $first_pie_data[1],
                'sliced' => true,
                'selected' => true
            );
        }

        return array(
            'data' => $pie_data,
            'raw_data'=>$raw_pie
        );
    }


    function getUPPF($start_dt,$end_dt,$product_group, $comp){

        $ProductType = ClassRegistry::init('ProductType');
        $all_product = $ProductType->find('all',array(
            'fields'=>array('ProductType.id'),
            'conditions'=>array('ProductType.group'=>$product_group),
            'recursive'=> -1
        ));
        $product_list = array();
        foreach($all_product as $arr){
            $product_list[] = $arr['ProductType']['id'];
        }

        $conditions = array('BdcDistribution.omc_id' => $comp, 'BdcDistribution.order_status' => 'Complete', 'BdcDistribution.deleted' => 'n', 'BdcDistribution.product_type_id' =>$product_list,'BdcDistribution.loading_date >=' => $start_dt, 'BdcDistribution.loading_date <=' => $end_dt);
        $export_data = $this->find('all', array(
            'fields'=>array('BdcDistribution.id','BdcDistribution.loading_date','BdcDistribution.quantity','BdcDistribution.vehicle_no'),
            'conditions' => $conditions,
            'contain'=>array(
                'OmcBdcDistribution'=>array(
                    'fields'=>array('OmcBdcDistribution.id','OmcBdcDistribution.invoice_number','OmcBdcDistribution.omc_customer_id','OmcBdcDistribution.quantity','OmcBdcDistribution.delivery_distance','OmcBdcDistribution.freight_rate','OmcBdcDistribution.transporter','OmcBdcDistribution.driver'),
                    'OmcCustomer'=>array(
                        'fields'=>array('OmcCustomer.id','OmcCustomer.name')
                    ),
                    'Region'=>array('fields'=>array('Region.id','Region.name')),
                    'DeliveryLocation'=>array('fields'=>array('DeliveryLocation.id','DeliveryLocation.name'))
                ),
                'Depot'=>array('fields'=>array('Depot.id','Depot.name','Depot.short_name')),
                'ProductType'=>array('fields'=>array('ProductType.id','ProductType.short_name'))
            ),
            'order' => array("BdcDistribution.loading_date"=>'asc'),
        ));

        // debug($export_data);
        $product_list = array();
        $t_head = array();
        $t_body_data = array();
        if($export_data){
            //Get all the product type
            foreach($export_data as $arr){
                $product_list[$arr['ProductType']['id']] = $arr['ProductType']['short_name'];
            }
            //build the header
            $t_head =array(
                'date'=>'Date',
                'invoice'=>'Invoice',
                'customer'=>'Customer',
                'loading_point'=>'Loading Point'
            );
            //Add product
            $row_inner = array();
            foreach($product_list as $key => $name){
                $t_head[$name] = $name;
                $row_inner[$name] = 0;
            }
            $t_head['total_volume'] = 'Total Volume';
            $t_head['delivery_point'] = 'Delivery Point';
            $t_head['region'] = 'Region';
            // $t_head['zonal_depot'] = 'Zonal Depot';
            $t_head['delivery_distance'] = 'Delivery Distance';
            $t_head['uppf_rate'] = 'UPPF Rate';
            $t_head['total_amount'] = 'Total Amount';
            $t_head['vehicle_no'] = 'Vehicle No.';
            $t_head['driver'] = 'Driver';
            //End of header

            //Now the body
            foreach($export_data as $arr){
                foreach($arr['OmcBdcDistribution'] as $inner){
                    //Create copy of t_head
                    $t_row = $t_head;
                    foreach($row_inner as $key => $value){
                        $t_row[$key] = $value;
                    }
                    //Start Pushing data
                    $t_row['date'] = $this->covertDate($arr['BdcDistribution']['loading_date'],'mysql_flip');
                    $t_row['invoice'] = $inner['invoice_number'];
                    $t_row['customer'] = ucwords(strtolower($inner['OmcCustomer']['name']));
                    $t_row['loading_point'] = $arr['Depot']['short_name'];
                    $product_short_name = $arr['ProductType']['short_name'];
                    $quantity = preg_replace('/,/','',$inner['quantity']);
                    $t_row[$product_short_name] = $this->formatNumber($quantity,'number',0) ;
                    $t_row['total_volume'] = $this->formatNumber($quantity,'number',0);
                    $t_row['delivery_point'] = isset($inner['DeliveryLocation']['name'])? ucwords(strtolower($inner['DeliveryLocation']['name'])): '';
                    $t_row['region'] = isset($inner['Region']['name'])?$inner['Region']['name']:'';
                    //$t_row['zonal_depot'] = 'Zonal Depot';
                    $t_row['delivery_distance'] = $inner['delivery_distance'];
                    $t_row['uppf_rate'] = $inner['freight_rate'];
                    $t_row['total_amount'] = $this->formatNumber($inner['freight_rate'] * $quantity,'number');
                    $t_row['vehicle_no'] = $arr['BdcDistribution']['vehicle_no'];
                    $t_row['driver'] = ucwords(strtolower($inner['driver']));

                    $t_body_data[]= $t_row;
                }
            }
        }


        return array(
            't_head'=>$t_head,
            't_body_data'=>$t_body_data
        );
    }


    function addDistribution($order=array(),$record_type = 'bdc',$created_by = null){
        $distribution = $this->find('first', array(
            'fields' => array('BdcDistribution.id'),
            'conditions' => array('BdcDistribution.order_id' => $order['id']),
            'recursive' => -1
        ));

        if(!$distribution){
            $distribution_data = array(
                'BdcDistribution'=>array(
                    'bdc_id'=>$order['bdc_id'],
                    'omc_id'=>$order['omc_id'],
                    'loading_date'=>$order['loaded_date'],
                    'waybill_date'=>$order['waybill_date'],
                    'waybill_id'=>$order['waybill_id'],
                    'depot_id'=>$order['depot_id'],
                    'product_type_id'=>$order['product_type_id'],
                    'approved_quantity'=>$order['approved_quantity'],
                    'quantity'=>$order['loaded_quantity'],
                    'transporter'=>$order['transporter'],
                    'vehicle_no'=>$order['truck_no'],
                    'order_id'=>$order['id'],
                    'row_bg_color' => 'tr_green',
                    'order_status'=>'Complete',
                    'record_type'=>$record_type,
                    'record_origin'=>'crm',
                    'created_by' => $created_by
                )
            );

            if($this->save($distribution_data)){
                return true;
            }
            else{
                return false;
            }
        }
        else{//It exist already
            return false;
        }
    }

}