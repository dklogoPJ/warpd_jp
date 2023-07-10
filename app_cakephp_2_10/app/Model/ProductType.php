<?php
class ProductType extends AppModel
{
    /**
     * associations
     */
    var $hasMany = array(
        'BdcDistribution' => array(
            'className' => 'BdcDistribution',
            'foreignKey' => 'product_type_id',
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
        'Order' => array(
            'className' => 'Order',
            'foreignKey' => 'product_type_id',
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
        'OmcCustomerOrder' => array(
            'className' => 'OmcCustomerOrder',
            'foreignKey' => 'product_type_id',
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
        'BdcInitialStockStartup' => array(
            'className' => 'BdcInitialStockStartup',
            'foreignKey' => 'product_type_id',
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
        'BdcStockUpdate' => array(
            'className' => 'BdcStockUpdate',
            'foreignKey' => 'product_type_id',
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
        'BdcStockHistory' => array(
            'className' => 'BdcStockHistory',
            'foreignKey' => 'product_type_id',
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
        'ProductType' => array(
            'className' => 'ProductType',
            'foreignKey' => 'product_type_id',
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
        'FreightRateCategory' => array(
            'className' => 'FreightRateCategory',
            'foreignKey' => 'freight_rate_category_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        )
    );


    function getProductList($product_ids = null){
        $conditions = array('ProductType.deleted' => 'n');
        if($product_ids != null){
            $conditions['ProductType.id'] = $product_ids;
        }
        $products_type = $this->find('all', array(
            'fields' => array('ProductType.id', 'ProductType.name','ProductType.short_name'),
            'conditions' => $conditions,
            'recursive' => -1
        ));
        $products_lists = array();
        foreach ($products_type as $value) {
            $products_lists[] = $value['ProductType'];
        }
        return $products_lists;
    }


}