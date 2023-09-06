<?php
class CustomerCreditSetting extends AppModel
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

    var $hasMany = array(
        'CustomerCredit' => array(
            'className' => 'CustomerCredit',
            'foreignKey' => 'customercredit_setting_id',
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
        'CustomerCreditPayment' => array(
            'className' => 'CustomerCreditPayment',
            'foreignKey' => 'customercredit_setting_id',
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

    function getCreditCustomerList($cus_ids = null){
        $conditions = array('deleted' => 'n');
        if($cus_ids != null){
            $conditions['id'] = $cus_ids;
        }
        $customers = $this->find('all', array(
            'fields' => array('id', 'name'),
            'conditions' => $conditions,
            'recursive' => -1
        ));
        $credit_customer_lists = array();
        foreach ($customers as $value) {
            $credit_customer_lists[] = $value['CustomerCreditSetting'];
        }
        return $credit_customer_lists;
    }
    

}