<?php
class OmcCustomerPriceChange extends AppModel
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
        ),
        'OmcCustomer' => array(
            'className' => 'OmcCustomer',
            'foreignKey' => 'omc_customer_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        )
    );


    function getPriceQuotes($id = ''){
        $conditions = array('OmcCustomerPriceChange.omc_customer_id' => $id,'OmcCustomerPriceChange.deleted' => 'n');
        $pcd = $this->find('all', array(
            'conditions' => $conditions,
            'contain'=>array(
                'ProductType'=>array('fields'=>array('ProductType.id','ProductType.name'))
            ),
            'recursive' => 1
        ));
        $price_q = array();
        foreach ($pcd as $value) {
            $price_q[$value['ProductType']['name']] = $value['OmcCustomerPriceChange'];
        }
        return $price_q;
    }


    function getPriceQuotesData($id = ''){
        $conditions = array('OmcCustomerPriceChange.omc_customer_id' => $id,'OmcCustomerPriceChange.deleted' => 'n');
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
                'price' =>$value['OmcCustomerPriceChange']['price'],
                'name' =>$value['ProductType']['name']
            );
        }
        return $price_q;
    }

    function getProductPumpPrice($id = '', $product_type_id){
        $conditions = array('OmcCustomerPriceChange.omc_customer_id' => $id,'OmcCustomerPriceChange.product_type_id' => $product_type_id,'OmcCustomerPriceChange.deleted' => 'n');
        $pcd = $this->find('first', array(
            'conditions' => $conditions,
            'recursive' => -1
        ));
        if($pcd){
            return $pcd['OmcCustomerPriceChange']['price'];
        }
        else{
            return 0.00;
        }
    }
}