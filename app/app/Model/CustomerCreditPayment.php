<?php
class CustomerCreditPayment extends AppModel
{
    /**
     * associations
     *
     * @var array
     */

    var $belongsTo = array(
        'OmcCustomer' => array(
            'className' => 'OmcCustomer',
            'foreignKey' => 'omc_customer_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        )   
    );

    

}