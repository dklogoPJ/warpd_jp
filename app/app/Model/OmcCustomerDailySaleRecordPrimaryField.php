<?php
class OmcCustomerDailySaleRecordPrimaryField extends AppModel
{
    /**
     * associations
     */

    var $hasMany = array(
        'OmcCustomerDailySaleRecordField' => array(
            'className' => 'OmcCustomerDailySaleRecordField',
            'foreignKey' => 'omc_customer_daily_sale_record_primary_field_id',
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
        'OmcCustomerDailySaleRecord' => array(
            'className' => 'OmcCustomerDailySaleRecord',
            'foreignKey' => 'omc_customer_daily_sale_record_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        ),
        'OmcSalesFormPrimaryFieldOption' => array(
            'className' => 'OmcSalesFormPrimaryFieldOption',
            'foreignKey' => 'omc_sales_form_primary_field_option_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        )
    );


}