<?php
class AdditiveStock extends AppModel
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
        )   
    );

    

}