<?php
class TemperatureCompesation extends AppModel
{
    

    var $belongsTo = array(
        'ProductType' => array(
            'className' => 'ProductType',
            'foreignKey' => 'product_type_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        ),
        'OmcCustomer' => array(
            'className' => 'OmcCustomer',
            'foreignKey' => 'omc_customer_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        ));

    function getTempCompId($Id = null, $recursive = -1)
    {
        $conditions = array(
            'TemperatureCompesation.id' => $Id,
        );
        # fetch the specific data from the server and retrun it.
        return $this->find('first', array('conditions' => $conditions, 'recursive' => $recursive));
    }

    function getAllTempComp()
    {
        return $this->find('all');
    }

}