<?php
class OmcPriceChange extends AppModel
{
    /**
     * associations
     */

    var $belongsTo = array(
        'ProductType' => array(
            'className' => 'ProductType',
            'foreignKey' => 'product_type_id',
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
        )
    );


    function getPriceQuotes($omc_id = ''){
        $conditions = array('OmcPriceChange.omc_id' => $omc_id,'OmcPriceChange.deleted' => 'n');
        $pcd = $this->find('all', array(
            'conditions' => $conditions,
            'contain'=>array(
                'ProductType'=>array('fields'=>array('ProductType.id','ProductType.name'))
            ),
            'recursive' => 1
        ));
        $price_q = array();
        foreach ($pcd as $value) {
            $price_q[$value['ProductType']['name']] = $value['OmcPriceChange'];
        }
        return $price_q;
    }


    function getPriceQuotesData($omc_id = ''){
        $conditions = array('OmcPriceChange.omc_id' => $omc_id,'OmcPriceChange.deleted' => 'n');
        $pcd = $this->find('all', array(
            'conditions' => $conditions,
            'contain'=>array(
                'ProductType'=>array('fields'=>array('ProductType.id','ProductType.name'))
            ),
            'recursive' => 1
        ));
        $price_q = array();
        foreach ($pcd as $value) {
            $price_q[$value['ProductType']['id']] = array(
                'price' =>$value['OmcPriceChange']['price'],
                'name' =>$value['ProductType']['name']
            );
        }
        return $price_q;
    }

    function getProductPumpPrice($omc_id = '',$product_type_id){
        $conditions = array('OmcPriceChange.omc_id' => $omc_id,'OmcPriceChange.product_type_id' => $product_type_id,'OmcPriceChange.deleted' => 'n');
        $pcd = $this->find('first', array(
            'conditions' => $conditions,
            'recursive' => -1
        ));
        if($pcd){
            return $pcd['OmcPriceChange']['price'];
        }
        else{
            return 0.00;
        }
    }
}