<?php
class PumpTankSale extends AppModel
{
    

    var $belongsTo = array(
        
        'OmcCustomer' => array(
            'className' => 'OmcCustomer',
            'foreignKey' => 'omc_customer_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        ));

    function getPumpTankSaleId($Id = null, $recursive = -1)
    {
        $conditions = array(
            'PumpTankSale.id' => $Id,
        );
        # fetch the specific data from the server and retrun it.
        return $this->find('first', array('conditions' => $conditions, 'recursive' => $recursive));
    }

    function getAllPumpTankSales()
    {
        return $this->find('all');
    }

}