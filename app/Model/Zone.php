<?php
class Zone extends AppModel
{
    /**
     * associations
     */
    var $hasMany = array(
        'Distance' => array(
            'className' => 'Distance',
            'foreignKey' => 'zone_id',
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


}