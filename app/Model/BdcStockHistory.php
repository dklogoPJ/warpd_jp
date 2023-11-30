<?php
class BdcStockHistory extends AppModel
{
    /**
     * associations
     */
    var $belongsTo = array(
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

        'ProductType' => array(
            'className' => 'ProductType',
            'foreignKey' => 'product_type_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        )
    );


    function closeStockOpenNewStock($bdc_id,$closing_date){
        $opened_sock = $this->find('all',array(
            'fields'=>array('id','bdc_id','depot_id','product_type_id','initial_quantity','stock_date','status'),
            'conditions'=>array('bdc_id'=>$bdc_id,'stock_date'=>$closing_date,'status'=>'Open'),
            'recursive'=>-1
        ));
        if($opened_sock){
            $save = array();
            $open_stock_today = date("Y-m-d");

            foreach($opened_sock as $arr){
                $s = $arr['BdcStockHistory'];
                $tt = $this->__getTradingTotals($s['bdc_id'],$s['depot_id'],$s['product_type_id']);
                $stck_qty = 0;
                $lift_qty = 0;
                if($tt){
                    $stck_qty = $tt['StockTrading']['restocking_total'];
                    $lift_qty = $tt['StockTrading']['liftings_total'];
                }

                $closing_qty =  ($s['initial_quantity'] + $stck_qty) - $lift_qty;

                $s['stock_update_quantity'] = $stck_qty;
                $s['lifting_quantity'] = $lift_qty;
                $s['closing_quantity'] = $closing_qty;
                $s['status'] = 'Closed';
                //Close the stock
                $save[] = $s;
                //Open new stock for this record
                $save[] = array(
                    'id'=>'',
                    'bdc_id'=>$s['bdc_id'],
                    'depot_id'=>$s['depot_id'],
                    'product_type_id'=>$s['product_type_id'],
                    'initial_quantity'=>$closing_qty,
                    'stock_date'=>$open_stock_today,
                    'status'=>'Open'
                );
            }

            return $this->saveAll($save);
        }
        else{
            return true;
        }
    }


    function __getTradingTotals($bdc_id,$depot_id,$product_type_id){
        $StockTrading = ClassRegistry::init('StockTrading');
        return $StockTrading->getTradingProduct($bdc_id,$depot_id,$product_type_id);
    }

    function __getStockUpdate($bdc_id,$date,$depot,$product){
        $BdcStockUpdate = ClassRegistry::init('BdcStockUpdate');
        return $BdcStockUpdate->getStockUpdateQuantity($bdc_id,$date,$depot,$product);
    }

    function __getLifting($bdc_id,$date,$depot,$product){
        $BdcDistribution = ClassRegistry::init('BdcDistribution');
        return $BdcDistribution->getLiftingQuantity($bdc_id,$date,$depot,$product);
    }


    function __getOrdersQuantity($bdc_id,$date,$depot,$product){
        $Order = ClassRegistry::init('Order');
        return $Order->getOrderByDepotProduct($bdc_id,$date,$depot,$product);
    }

    function getStockHistories($bdc_id,$start_dt,$end_dt,$depot,$status = null){
        $conditions = array('BdcStockHistory.bdc_id'=>$bdc_id,'stock_date  >= '=>$start_dt,'stock_date  <= '=>$end_dt);
        if($status != null){
            $conditions['status']=$status;
        }
        if($depot != '0'){
            $conditions['depot_id']=$depot;
        }
        $socks = $this->find('all',array(
            'fields'=>array('BdcStockHistory.id','BdcStockHistory.bdc_id','depot_id','product_type_id','initial_quantity','stock_update_quantity','lifting_quantity','closing_quantity','stock_date','status'),
            'conditions'=>$conditions,
            'contain'=>array(
                'Depot'=>array('fields'=>array('Depot.id','Depot.name')),
                'ProductType'=>array('fields'=>array('ProductType.id','ProductType.name'))
            ),
            'recursive'=>1
        ));
        if($socks){
            $date_heads = array();
            $data = array();
            foreach($socks as $st){
                $sh = $st['BdcStockHistory'];
                $bdp = $st['Depot'];
                $pd = $st['ProductType'];
                $data[$bdp['id']]['name']=$bdp['name'];
                $data[$bdp['id']]['products'][$pd['id']]['name']=$pd['name'];
                $data[$bdp['id']]['products'][$pd['id']]['data'][$sh['stock_date']]=$sh;

                $date_heads[] = $sh['stock_date'];
            }
            $date_heads = array_unique($date_heads);
            return array(
                'headers'=>$date_heads,
                'data'=>$data
            );
        }
        else{
            return false;
        }
    }


    function getDailyStockVariance($bdc_id,$today_dt,$depot){
        $conditions = array('BdcStockHistory.bdc_id'=>$bdc_id,'stock_date'=>$today_dt,'status'=>'Open');
        if($depot != '0'){
            $conditions['depot_id']=$depot;
        }
        $socks = $this->find('all',array(
            'fields'=>array('BdcStockHistory.id','BdcStockHistory.bdc_id','depot_id','product_type_id','initial_quantity','stock_update_quantity','stock_date','status'),
            'conditions'=>$conditions,
            'contain'=>array(
                'Depot'=>array('fields'=>array('Depot.id','Depot.name')),
                'ProductType'=>array('fields'=>array('ProductType.id','ProductType.name'))
            ),
            'recursive'=>1
        ));

        if($socks){
            $date_heads = array();
            $data = array();
            foreach($socks as $st){
                $sh = $st['BdcStockHistory'];
                $bdp = $st['Depot'];
                $pd = $st['ProductType'];
                $stck_qty = $this->__getStockUpdate($sh['bdc_id'],$today_dt,$sh['depot_id'],$sh['product_type_id']);
                $orders_qty = $this->__getOrdersQuantity($sh['bdc_id'],$today_dt,$sh['depot_id'],$sh['product_type_id']);
                $lift_qty = $this->__getLifting($sh['bdc_id'],$today_dt,$sh['depot_id'],$sh['product_type_id']);
                $total_stock =  ($sh['initial_quantity'] + $stck_qty) - $lift_qty;
                $variance = $total_stock - $orders_qty;
                $color = 'red';
                $status = 'Inadequate stock to service depot orders';
                if($variance >= 0){
                    $color = 'success';
                    $status = 'Adequate stock to service depot orders';
                }

                $s = array();
                $s['total_stock'] = $total_stock;
                $s['orders_qty'] = $orders_qty;
                $s['variance'] = $variance;
                $s['color'] = $color;
                $s['status'] = $status;

                $data[$bdp['id']]['name']=$bdp['name'];
                $data[$bdp['id']]['products'][$pd['id']]['name']=$pd['name'];
                $data[$bdp['id']]['products'][$pd['id']]['data']=$s;
            }

            return array(
                'data'=>$data
            );
        }
        else{
            return false;
        }
    }


    function getBDCStocks($start_dt,$product,$stock_type){
        $conditions = array('BdcStockHistory.stock_date'=>$start_dt,'BdcStockHistory.product_type_id'=>$product);

        $socks = $this->find('all',array(
            'fields'=>array('BdcStockHistory.id','BdcStockHistory.bdc_id','depot_id','product_type_id','initial_quantity','stock_date','closing_quantity'),
            'conditions'=>$conditions,
            'contain'=>array(
                'Depot'=>array('fields'=>array('Depot.id','Depot.name')),
                'ProductType'=>array('fields'=>array('ProductType.id','ProductType.name')),
                'Bdc'=>array('fields'=>array('Bdc.id','Bdc.name'))
            ),
            'recursive'=>1
        ));

        //$this->debugQuery();
        $grouped_data = array();
        if ($socks) {//If stocks then group by bdc
            foreach ($socks as $data) {
                $group_index = $data['Bdc']['name'];
                $dt_id = $data['Depot']['id'];
                $grouped_data[$group_index][$dt_id] = $data;
            }
        }
        //debug($grouped_data);
        $x_axis = array();
        $y_axis = array();
        foreach ($grouped_data as $x_ax => $arr) {
            ksort($arr);
            $x_axis[] = $tb_row[] = $x_ax;
            foreach ($arr as $d) {
                $depot_id = $d['Depot']['id'];
                $depot_name = $d['Depot']['name'];
                $val = ($stock_type == 'Opening') ? $d['BdcStockHistory']['initial_quantity']:$d['BdcStockHistory']['closing_quantity'];
                $y_axis[$depot_id]['name'] = $depot_name;
                $y_axis[$depot_id]['data'][] = preg_replace('/,/','',$val);
            }
        }
        debug($x_axis);
        debug($y_axis);
    }
}