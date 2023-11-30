<?php
class DeliveryLocation extends AppModel
{
    /**
     * associations
     */
    var $hasMany = array(
        'OmcBdcDistribution' => array(
            'className' => 'OmcBdcDistribution',
            'foreignKey' => 'delivery_location_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ));

    var $belongsTo = array(
        'Depot' => array(
            'className' => 'Depot',
            'foreignKey' => 'depot_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        ),
        'Region' => array(
            'className' => 'Region',
            'foreignKey' => 'region_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        )
    );


}