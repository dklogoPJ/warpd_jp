<?php
class FreightRate extends AppModel
{
    /**
     * associations
     */
    var $belongsTo = array(
        'FreightRateCategory' => array(
            'className' => 'FreightRateCategory',
            'foreignKey' => 'freight_rate_category_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        )
    );

}