<?php
class OmcCustomerDailySaleRecordField extends AppModel
{
    /**
     * associations
     */

    var $belongsTo = array(
        'OmcCustomerDailySaleRecordPrimaryField' => array(
            'className' => 'OmcCustomerDailySaleRecordPrimaryField',
            'foreignKey' => 'omc_customer_daily_sale_record_primary_field_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        ),
        'OmcSalesFormField' => array(
            'className' => 'OmcSalesFormField',
            'foreignKey' => 'omc_sales_form_field_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        )
    );


}