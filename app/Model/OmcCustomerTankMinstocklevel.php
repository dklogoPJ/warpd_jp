<?php
class OmcCustomerTankMinstocklevel extends AppModel
{
    /**
     * associations
     *
     * @var array
     */

    var $belongsTo = array(
        'Omc' => array(
            'className' => 'Omc',
            'foreignKey' => 'omc_id',
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
        )
    );

    function getCustomerTankMinStockLevelById($id = null, $recursive = -1)
    {
        $conditions = array(
            'OmcCustomerTankMinstocklevel.id' => $id,
        );
        # fetch the specific data from the server and retrun it.
        return $this->find('first', array('conditions' => $conditions, 'recursive' => $recursive));
    }

    function getCustomerTankMinStockLevelByType($omc_customer_id = null, $type = null,$recursive = -1)
    {
        $conditions = array(
            'OmcCustomerTankMinstocklevel.omc_customer_id' => $omc_customer_id,
            'OmcCustomerTankMinstocklevel.type' => $type
        );
        # fetch the specific data from the server and retrun it.
        return $this->find('all', array('conditions' => $conditions, 'recursive' => $recursive));
    }

    function getCustomerTanksMinStockLevel($omc_customer_id = null,$recursive = -1)
    {
        $conditions = array(
            'OmcCustomerTankMinstocklevel.omc_customer_id' => $omc_customer_id,
        );
        # fetch the specific data from the server and retrun it.
        return $this->find('all', array('conditions' => $conditions, 'recursive' => $recursive));
    }

}