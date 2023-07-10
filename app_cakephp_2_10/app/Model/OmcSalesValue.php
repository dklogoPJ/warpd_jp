<?php
class OmcSalesValue extends AppModel
{

    /**
     * associations
     */
    var $belongsTo = array(
        'OmcSalesRecord' => array(
            'className' => 'OmcSalesRecord',
            'foreignKey' => 'omc_sales_record_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        ),
        'OmcSalesField' => array(
            'className' => 'OmcSalesField',
            'foreignKey' => 'omc_sales_field_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        )
    );

}
