<?php
class OmcSalesProduct extends AppModel
{

    var $hasMany = array(
        'OmcSalesField' => array(
            'className' => 'OmcSalesField',
            'foreignKey' => 'omc_sales_product_id',
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
    );

    var $belongsTo = array(
        'Omc' => array(
            'className' => 'Omc',
            'foreignKey' => 'omc_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        )
    );


    function listSalesAndServices($omc_id=''){
        return $this->find('list',array(
            'fields'=>array('id','name'),
            'conditions'=>array('omc_id'=>$omc_id,'deleted'=>'n'),
            'order'=>array('id'=>'desc'),
            'recursive'=>-1
        ));
    }


}