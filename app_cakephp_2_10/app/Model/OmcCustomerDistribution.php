<?php
class OmcCustomerDistribution extends AppModel
{
    /**
     * associations
     */

    var $belongsTo = array(
        'OmcBdcDistribution' => array(
            'className' => 'OmcBdcDistribution',
            'foreignKey' => 'omc_bdc_distribution_id',
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