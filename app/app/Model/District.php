<?php
class District extends AppModel
{
    /**
     * associations
     */
    var $hasMany = array(
        'BdcDistribution' => array(
            'className' => 'BdcDistribution',
            'foreignKey' => 'district_id',
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
        'OmcBdcDistribution' => array(
            'className' => 'OmcBdcDistribution',
            'foreignKey' => 'district_id',
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
        'OmcCustomer' => array(
            'className' => 'OmcCustomer',
            'foreignKey' => 'region_id',
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