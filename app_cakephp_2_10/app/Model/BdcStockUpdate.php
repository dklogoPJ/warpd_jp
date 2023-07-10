<?php
class BdcStockUpdate extends AppModel
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


    function getStockUpdateQuantity($bdc_id,$date,$depot,$product){
        $sock_update = $this->find('all',array(
            'conditions'=>array('bdc_id'=>$bdc_id,'delivery_date'=>$date,'depot_id'=>$depot,'product_type_id'=>$product),
            'recursive'=>-1
        ));
        $total = 0;
        foreach($sock_update as $stck_upt){
            $total = $total + $stck_upt['BdcStockUpdate']['quantity_ltrs'];
        }
        return $total;
    }

}