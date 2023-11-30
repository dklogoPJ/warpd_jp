<?php
class StockTrading extends AppModel
{

    function isTrading($bdc_id){
        $trading = $this->find('all',array(
            'conditions'=>array('bdc_id'=>$bdc_id),
            'recursive'=>-1
        ));
        if($trading){
            return true;
        }
        else{//If Not trading, Start trading now.
            return false;
        }
    }


    /** This is executed once per BDC */
    function startTrading($bdc_id){
        $Bdc = ClassRegistry::init('Bdc');
        $BdcInitialStockStartup = ClassRegistry::init('BdcInitialStockStartup');
        $depots_to_products = $Bdc->getDepotToProduct($bdc_id);
        $initial_product_stocks = $BdcInitialStockStartup->getStockStartUp($bdc_id);
        $save = array();
        $close_stock_date = date("Y-m-d", strtotime("+1 day"));
        $current_stock_date = date("Y-m-d");
        foreach($depots_to_products as $depot_id => $products_arr){
            foreach($products_arr as $product_id){
                if(!empty($product_id)){
                    foreach($initial_product_stocks as $value){
                        $ips = $value['BdcInitialStockStartup'];
                        if($bdc_id == $ips['bdc_id'] && $depot_id == $ips['depot_id'] && $product_id == $ips['product_type_id']){
                            $save[]=array(
                                'bdc_id'=> $bdc_id,
                                'depot_id'=>$depot_id,
                                'product_type_id'=>$product_id,
                                'stock_level'=>$ips['quantity_ltrs'],//[quantity_metric_ton]
                                'liftings_total'=>0,
                                'restocking_total'=>0,
                                'current_stock_date'=>$current_stock_date,
                                'close_stock_date'=>$close_stock_date
                            );
                        }
                    }
                }
            }
        }
        if(!empty($save)){
            return $this->saveAll($save);
        }
        return false;
    }


    function isTradingOver($bdc_id){
        $date = date("Y-m-d");
        $trade_over = $this->find('first',array(
            'conditions'=>array('bdc_id'=>$bdc_id,'close_stock_date <='=>$date),
            'recursive'=>-1
        ));
        if($trade_over){
            $this->closeTradingStocks($bdc_id,$trade_over['StockTrading']['current_stock_date']);
            $this->openNewTradingStocks($bdc_id);
        }
    }

    function closeTradingStocks($bdc_id,$date){
        $BdcStockHistory = ClassRegistry::init('BdcStockHistory');
        $BdcStockHistory->closeStockOpenNewStock($bdc_id,$date);
    }

    function openNewTradingStocks($bdc_id){
        $close_stock_date = date("Y-m-d", strtotime("+1 day"));
        $current_stock_date = date("Y-m-d");
        $return = $this->find('all',array(
            'conditions'=>array('bdc_id'=>$bdc_id),
            'recursive'=>-1
        ));
        $update_trade = array();
        foreach($return as $value){
            $current_stock = doubleval($value['StockTrading']['stock_level']);
            $total_liftings = doubleval($value['StockTrading']['liftings_total']);
            $total_restocking = doubleval($value['StockTrading']['restocking_total']);
            $new_stock_level = ($current_stock + $total_restocking) - $total_liftings;

            $update_trade[$value['StockTrading']['id']]=array(
                'id'=>$value['StockTrading']['id'],
                'bdc_id'=> $value['StockTrading']['bdc_id'],
                'depot_id'=>$value['StockTrading']['depot_id'],
                'product_type_id'=>$value['StockTrading']['product_type_id'],
                'stock_level'=>$new_stock_level,
                'liftings_total'=>0,
                'restocking_total'=>0,
                'current_stock_date'=>$current_stock_date,
                'close_stock_date'=>$close_stock_date
            );
        }
        if(!empty($update_trade)){
            return $this->saveAll($update_trade);
        }
        return false;
    }

    function stockRestocking($bdc_id,$depot_id,$product_type_id,$quantity){
        $this->__updateTradingStock('Restock',$bdc_id,$depot_id,$product_type_id,$quantity);
    }

    function stockLiftings($bdc_id,$depot_id,$product_type_id,$quantity){
        $this->__updateTradingStock('Liftings',$bdc_id,$depot_id,$product_type_id,$quantity);
    }

    function __updateTradingStock($type,$bdc_id,$depot_id,$product_type_id,$quantity){
        $trade = $this->find('first',array(
            'conditions'=>array('bdc_id'=>$bdc_id,'depot_id'=>$depot_id,'product_type_id'=>$product_type_id),
            'recursive'=>-1
        ));
        if($trade){
            $restocking = $trade['StockTrading']['restocking_total'];
            $liftings = $trade['StockTrading']['liftings_total'];
            if($type == 'Restock'){
                $restocking = doubleval($restocking) + doubleval($quantity);
            }
            else{//Liftings
                $liftings = doubleval($liftings) + doubleval($quantity);
            }
            $s = array(
                'id'=>$trade['StockTrading']['id'],
                'liftings_total'=>$liftings,
                'restocking_total'=>$restocking
            );
            $this->save($s);
        }
    }

    function getTradingProduct($bdc_id,$depot_id,$product_type_id){
        $trade = $this->find('first',array(
            'conditions'=>array('bdc_id'=>$bdc_id,'depot_id'=>$depot_id,'product_type_id'=>$product_type_id),
            'recursive'=>-1
        ));
        return $trade;
    }

    function _getCurrentTradeDepotProducts($bdc_id){
        $return = $this->find('all',array(
            'conditions'=>array('bdc_id'=>$bdc_id),
            'recursive'=>-1
        ));
        $trading_depot_products = array();
        foreach($return as $value){
            $trading_depot_products[$value['StockTrading']['depot_id']][]=$value['StockTrading']['product_type_id'];
        }
        return $trading_depot_products;
    }


    function isTradingAllProducts($bdc_id,$data){
        $current_trade_depot_product = $this->_getCurrentTradeDepotProducts($bdc_id);
        $close_stock_date = date("Y-m-d", strtotime("+1 day"));
        $current_stock_date = date("Y-m-d");
        $save = array();
        foreach($data as $value_arr){
            $new_bdc_id = $value_arr['bdc_id'];
            $new_depot_id = $value_arr['depot_id'];
            $new_product_type_id = $value_arr['product_type_id'];
            $new_quantity = $value_arr['initial_quantity'];
            if(isset($current_trade_depot_product[$new_depot_id]) && isset($current_trade_depot_product[$new_depot_id][$new_product_type_id])){
                //Do nothing
            }
            else{//Add new one
                $save[]=array(
                    'bdc_id'=> $bdc_id,
                    'depot_id'=>$new_depot_id,
                    'product_type_id'=>$new_product_type_id,
                    'stock_level'=>$new_quantity,//[quantity_metric_ton]
                    'liftings_total'=>0,
                    'restocking_total'=>0,
                    'current_stock_date'=>$current_stock_date,
                    'close_stock_date'=>$close_stock_date
                );
            }
        }
        if(!empty($save)){
            return $this->saveAll($save);
        }
        return false;
    }
}