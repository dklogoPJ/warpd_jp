<?php
class BdcInitialStockStartup extends AppModel
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

    function getStockStartUp($bdc_id=null){
        return $this->find('all',array(
            'conditions'=>array('BdcInitialStockStartup.bdc_id'=>$bdc_id),
            'contain'=>array(
                'Depot'=>array('fields'=>array('Depot.id','Depot.name')),
                'ProductType'=>array('fields'=>array('ProductType.id','ProductType.name'))
            ),
            'recursive'=>1
        ));
    }

}