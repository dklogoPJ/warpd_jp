<?php
class OmcCustomerDailySaleField extends AppModel
{
    /**
     * associations
     */

    var $belongsTo = array(
        'OmcCustomerDailySalePrimaryField' => array(
            'className' => 'OmcCustomerDailySalePrimaryField',
            'foreignKey' => 'omc_customer_daily_sale_primary_field_id',
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


    function getByOmcCustomerDailySalePrimaryFieldId ($omc_customer_daily_sale_primary_field_id){
        return $this->find('all', array(
            'fields' => array(
                'OmcCustomerDailySaleField.*',
                'OmcSalesFormField.id',
                'OmcSalesFormField.field_name',
                'OmcSalesFormField.field_order',
                'OmcSalesFormField.field_type',
                'OmcSalesFormField.field_type_values',
                'OmcSalesFormField.field_required',
                'OmcSalesFormField.field_disabled',
                'OmcSalesFormField.field_event',
                'OmcSalesFormField.field_action',
                'OmcSalesFormField.field_action_sources',
                'OmcSalesFormField.field_action_targets'
            ),
            'conditions'=> array('OmcCustomerDailySaleField.omc_customer_daily_sale_primary_field_id'=> $omc_customer_daily_sale_primary_field_id),
            'joins' =>
                array(
                    array(
                        'table' => 'omc_sales_form_fields',
                        'alias' => 'OmcSalesFormField',
                        'type' => 'left',
                        'foreignKey' => false,
                        'conditions'=> array('OmcSalesFormField.id = OmcCustomerDailySaleField.omc_sales_form_field_id')
                    )
                ),
            'order' => array('OmcSalesFormField.field_order' => 'asc'),
            'recursive' => -1
        ));
    }

}