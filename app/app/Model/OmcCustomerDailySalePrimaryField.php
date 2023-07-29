<?php
class OmcCustomerDailySalePrimaryField extends AppModel
{
    /**
     * associations
     */

    var $hasMany = array(
        'OmcCustomerDailySaleField' => array(
            'className' => 'OmcCustomerDailySaleField',
            'foreignKey' => 'omc_customer_daily_sale_primary_field_id',
            'dependent' => true,
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
        'OmcCustomerDailySale' => array(
            'className' => 'OmcCustomerDailySale',
            'foreignKey' => 'omc_customer_daily_sale_id',
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


    function getByOmcCustomerDailySaleId ($omc_customer_daily_sale_id){
       return $this->find('all', array(
            'fields' => array(
                'OmcCustomerDailySalePrimaryField.*',
                'OmcSalesFormPrimaryFieldOption.id',
                'OmcSalesFormPrimaryFieldOption.option_name',
                'OmcSalesFormPrimaryFieldOption.order'
            ),
            'conditions'=> array('OmcCustomerDailySalePrimaryField.omc_customer_daily_sale_id'=> $omc_customer_daily_sale_id),
            'joins' =>
                array(
                    array(
                        'table' => 'omc_sales_form_primary_field_options',
                        'alias' => 'OmcSalesFormPrimaryFieldOption',
                        'type' => 'left',
                        'foreignKey' => false,
                        'conditions'=> array('OmcSalesFormPrimaryFieldOption.id = OmcCustomerDailySalePrimaryField.omc_sales_form_primary_field_option_id')
                    )
                ),
            'order' => array('OmcSalesFormPrimaryFieldOption.order' => 'asc'),
            'recursive' => -1
        ));
    }


}