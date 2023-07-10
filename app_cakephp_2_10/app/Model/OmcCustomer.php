<?php
class OmcCustomer extends AppModel
{
    /**
     * associations
     *
     * @var array
     */

    var $hasMany = array(
        'OmcBdcDistribution' => array(
            'className' => 'OmcBdcDistribution',
            'foreignKey' => 'omc_customer_id',
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
        'Order' => array(
            'className' => 'Order',
            'foreignKey' => 'omc_customer_id',
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
        'OmcCustomerOrder' => array(
            'className' => 'OmcCustomerOrder',
            'foreignKey' => 'omc_customer_id',
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
        'OmcCustomerTank' => array(
            'className' => 'OmcCustomerTank',
            'foreignKey' => 'omc_customer_id',
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
        'OmcCustomerTankMinstocklevel' => array(
            'className' => 'OmcCustomerTankMinstocklevel',
            'foreignKey' => 'omc_customer_id',
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
        'Group' => array(
            'className' => 'Group',
            'foreignKey' => 'omc_customer_id',
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
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'omc_customer_id',
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
        'Omc' => array(
            'className' => 'Omc',
            'foreignKey' => 'omc_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        ),
        'Region' => array(
            'className' => 'Region',
            'foreignKey' => 'region_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        ),
        'District' => array(
            'className' => 'District',
            'foreignKey' => 'district_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        )
    );


    function getCustomerById($Id = null, $recursive = -1)
    {
        $conditions = array(
            'OmcCustomer.id' => $Id,
        );
        # fetch the specific data from the server and retrun it.
        return $this->find('first', array('conditions' => $conditions, 'recursive' => $recursive));
    }

    function getOmcCustomerProduct($id = null){
        $fields = array('my_products');
        $r =  $this->find('first', array('fields'=>$fields,'conditions' => array('id' => $id), 'recursive' => -1));
        $my_products = explode(',',$r['OmcCustomer']['my_products']);
        return array(
            'my_products'=>$my_products
        );
    }

    function getCustomerByOmcId($omc_id = null, $recursive = -1)
    {
        $conditions = array(
            'OmcCustomer.omc_id' => $omc_id,
        );
        # fetch the specific data from the server and retrun it.
        return $this->find('all', array('conditions' => $conditions, 'recursive' => $recursive));
    }

}