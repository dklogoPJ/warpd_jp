<?php
class FreightRateCategory extends AppModel
{
    /**
     * associations
     */

    var $hasMany = array(
        'FreightRate' => array(
            'className' => 'FreightRate',
            'foreignKey' => 'freight_rate_category_id',
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

    function getCategories(){
        return $this->find('all',array(
            'conditions'=>array('deleted'=>'n'),
            'recursive'=>-1
        ));
    }

}