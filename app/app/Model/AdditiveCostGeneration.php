<?php
class AdditiveCostGeneration extends AppModel
{
    /**
     * associations
     *
     * @var array
     */

    var $belongsTo = array(
        'AdditiveSetup' => array(
            'className' => 'AdditiveSetup',
            'foreignKey' => 'additive_setup_id',
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
        'ProductType' => array(
            'className' => 'ProductType',
            'foreignKey' => 'product_type_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        )     
    );

    

}