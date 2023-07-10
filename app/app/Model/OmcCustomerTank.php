<?php
class OmcCustomerTank extends AppModel
{
    /**
     * associations
     *
     * @var array
     */

    var $hasMany = array(
        'OmcCustomerStock' => array(
            'className' => 'OmcCustomerStock',
            'foreignKey' => 'omc_customer_tank_id',
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
        'OmcCustomer' => array(
            'className' => 'OmcCustomer',
            'foreignKey' => 'omc_customer_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        )
    );

    function getCustomerTankById($id = null, $recursive = -1)
    {
        $conditions = array(
            'OmcCustomerTank.id' => $id,
        );
        # fetch the specific data from the server and retrun it.
        return $this->find('first', array('conditions' => $conditions, 'recursive' => $recursive));
    }

    function getCustomerTanks($omc_customer_id = null, $recursive = -1)
    {
        $conditions = array(
            'OmcCustomerTank.omc_customer_id' => $omc_customer_id,
        );
        # fetch the specific data from the server and retrun it.
        return $this->find('all', array('conditions' => $conditions, 'recursive' => $recursive));
    }

    function getTanks($cmp_id = null)
    {
        $conditions = array(
            'OmcCustomerTank.omc_customer_id'=>$cmp_id,
            'OmcCustomerTank.deleted'=>'n',
            'OmcCustomerTank.status'=>'Operational'
            //'NOT'=>array('OmcCustomerTank.status'=>'Maintenance','OmcCustomerTank.status'=>'Out of Service')
        );
        $tanks = $this->find('all',array(
            'fields'=>array('OmcCustomerTank.id','OmcCustomerTank.name','OmcCustomerTank.capacity'),
            'conditions'=>$conditions,
            'contain'=>array(
                'OmcCustomerStock'=>array(
                    'fields'=>array('OmcCustomerStock.id','OmcCustomerStock.created','OmcCustomerStock.quantity'),
                    'conditions'=>array('OmcCustomerStock.created LIKE'=> date('Y-m-d').'%')
                )
            )
        ));

        return $tanks;
    }


    function getLastStockUpdate($cmp_id = null){
        $conditions = array(
            'OmcCustomerTank.omc_customer_id'=>$cmp_id,
            'OmcCustomerTank.deleted'=>'n',
            'OmcCustomerTank.status'=>'Operational'
            //'NOT'=>array('OmcCustomerTank.status'=>'Maintenance','OmcCustomerTank.status'=>'Out of Service')
        );
        $tanks = $this->find('all',array(
            'fields'=>array('OmcCustomerTank.id','OmcCustomerTank.name','OmcCustomerTank.capacity'),
            'conditions'=>$conditions,
            'contain'=>array(
                'OmcCustomerStock'=>array(
                    'fields'=>array('OmcCustomerStock.id','OmcCustomerStock.created','OmcCustomerStock.quantity'),
                    'conditions'=>array('OmcCustomerStock.created LIKE'=> date('Y-m-d').'%')
                )
            )
        ));

        return $tanks;
    }

}