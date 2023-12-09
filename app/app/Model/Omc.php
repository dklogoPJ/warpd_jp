<?php
class Omc extends AppModel
{
    /**
     * associations
     */
    var $hasMany = array(
        'BdcOmc' => array(
            'className' => 'BdcOmc',
            'foreignKey' => 'omc_id',
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
        'OmcCustomer' => array(
            'className' => 'OmcCustomer',
            'foreignKey' => 'omc_id',
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
            'foreignKey' => 'omc_id',
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
        'Waybill' => array(
            'className' => 'Waybill',
            'foreignKey' => 'omc_id',
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
            'foreignKey' => 'omc_id',
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
            'foreignKey' => 'omc_id',
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
            'foreignKey' => 'omc_id',
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
            'foreignKey' => 'omc_id',
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
        'OmcSalesProduct' => array(
            'className' => 'OmcSalesProduct',
            'foreignKey' => 'omc_id',
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
        'OmcSalesForm' => array(
            'className' => 'OmcSalesForm',
            'foreignKey' => 'omc_id',
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
        'OmcTank' => array(
            'className' => 'OmcTank',
            'foreignKey' => 'omc_id',
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
        'OmcTankType' => array(
            'className' => 'OmcTankType',
            'foreignKey' => 'omc_id',
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
        'OmcTankStatus' => array(
            'className' => 'OmcTankStatus',
            'foreignKey' => 'omc_id',
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
        'Nct' => array(
            'className' => 'Nct',
            'foreignKey' => 'omc_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
        ),
        'AdditiveSetup' => array(
            'className' => 'AdditiveSetup',
            'foreignKey' => 'omc_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
        ),
        'AdditiveStock' => array(
            'className' => 'AdditiveStock',
            'foreignKey' => 'omc_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
        ),
        'AdditiveDopingRatio' => array(
            'className' => 'AdditiveDopingRatio',
            'foreignKey' => 'omc_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
        ),
        'AdditiveCostGeneration' => array(
            'className' => 'AdditiveCostGeneration',
            'foreignKey' => 'omc_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
        ),
        'AdditiveAverageCost' => array(
            'className' => 'AdditiveAverageCost',
            'foreignKey' => 'omc_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
        )
    );

    var $belongsTo = array(
        'OmcPackage' => array(
            'className' => 'OmcPackage',
            'foreignKey' => 'omc_package_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        )
    );


    function getOmcById($id = null)
    {
        return $this->find('first', array('conditions' => array('id' => $id), 'recursive' => -1));
    }

    function getOmcProduct($id = null){
        $fields = array('my_products');
        $r =  $this->find('first', array('fields'=>$fields,'conditions' => array('id' => $id), 'recursive' => -1));
        $my_products = explode(',',$r['Omc']['my_products']);
        return array(
            'my_products'=>$my_products
        );
    }


    function getOmcAdditive($id = null){
        $fields = array('my_products');
        $r =  $this->find('first', array('fields'=>$fields,'conditions' => array('id' => $id), 'recursive' => -1));
        $my_products = explode(',',$r['Omc']['my_products']);
        return array(
            'my_products'=>$my_products
        );
    }


    function getOmcDepot($id = null){
        $fields = array('my_depots');
        $r =  $this->find('first', array('fields'=>$fields,'conditions' => array('id' => $id), 'recursive' => -1));
        $my_depots = explode(',',$r['Omc']['my_depots']);
        return array(
            'my_depots'=>$my_depots
        );
    }

    function getOMCs(){
        return $this->find('all',array(
            'conditions'=>array('deleted'=>'n'),
            'recursive'=>-1
        ));
    }


}