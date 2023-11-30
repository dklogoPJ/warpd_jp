<?php
class CustomerCredit extends AppModel
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
        ) ,
        'ProductType' => array(
            'className' => 'ProductType',
            'foreignKey' => 'product_type_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        ),
        'CustomerCreditSetting' => array(
            'className' => 'CustomerCreditSetting',
            'foreignKey' => 'customer_credit_setting_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        )   
    );

    function getCumulativeQtyAndSalesPerProduct($omc_customer_id = null, $date = ''){
        if($date == '') {
            return array();
        }
        $conditions = array('CustomerCredit.invoice_date LIKE'=> $date.'%', 'CustomerCredit.deleted' => 'n');
        if($omc_customer_id != null){
            $conditions['CustomerCredit.omc_customer_id'] = $omc_customer_id;
        }

        $query = $this->find('all', array(
            'fields' => array('CustomerCredit.product_type_id','SUM(CustomerCredit.sales_qty) AS sales_qty','SUM(CustomerCredit.sales_amount) AS sales_amount'),
            'conditions' => $conditions,
            'contain'=>array(
                'ProductType'=>array('fields'=>array('ProductType.id','ProductType.short_name'))
            ),
            'group'=>array('CustomerCredit.product_type_id'),
            'recursive' => 1
        ));

        $lists = array();
        foreach ($query as $value) {
            $lists[] = array(
                'id' => $value['CustomerCredit']['product_type_id'],
                'name' => $value['ProductType']['short_name'],
                'sales_qty' => $value[0]['sales_qty'],
                'sales_amount' => $value[0]['sales_amount']
            );
        }
        return $lists;
    }

}