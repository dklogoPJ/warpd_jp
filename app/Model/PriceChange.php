<?php
class PriceChange extends AppModel
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
        )
    );


    function getPriceQuotes(){
        $conditions = array('PriceChange.deleted' => 'n');
        $pcd = $this->find('all', array(
            'conditions' => $conditions,
            'contain'=>array(
                'ProductType'=>array('fields'=>array('ProductType.id','ProductType.name'))
            ),
            'recursive' => 1
        ));
        $price_q = array();
        foreach ($pcd as $value) {
            $price_q[$value['ProductType']['name']] = $value['PriceChange'];
        }
        return $price_q;
    }


    function getPriceQuotesData(){
        $conditions = array('PriceChange.deleted' => 'n');
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
                'price' =>$value['PriceChange']['price'],
                'name' =>$value['ProductType']['name']
            );
        }
        return $price_q;
    }
}