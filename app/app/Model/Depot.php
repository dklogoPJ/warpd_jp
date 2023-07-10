<?php
class Depot extends AppModel
{
    /**
     * associations
     */
    var $hasMany = array(
        'BdcDistribution' => array(
            'className' => 'BdcDistribution',
            'foreignKey' => 'depot_id',
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
            'foreignKey' => 'depot_id',
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
            'foreignKey' => 'depot_id',
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
        'DeliveryLocation' => array(
            'className' => 'DeliveryLocation',
            'foreignKey' => 'depot_id',
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
            'foreignKey' => 'depot_id',
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
        'BdcInitialStockStartup' => array(
            'className' => 'BdcInitialStockStartup',
            'foreignKey' => 'depot_id',
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
        'BdcStockUpdate' => array(
            'className' => 'BdcStockUpdate',
            'foreignKey' => 'depot_id',
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
        'BdcStockHistory' => array(
            'className' => 'BdcStockHistory',
            'foreignKey' => 'depot_id',
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
            'foreignKey' => 'group_depot',
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

    var $hasOne = array(
        'Cep' => array(
            'className' => 'Cep',
            'foreignKey' => 'depot_id',
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

    function getDepotById($id = null){
        return $this->find('first', array('conditions' => array('id' => $id), 'recursive' => -1));
    }

    function getDepots($depot_ids = null){
        $conditions = array('deleted'=>'n');
        if($depot_ids != null){
            $conditions['id']=$depot_ids;
        }
        return $this->find('all',array(
            'conditions'=>$conditions,
            'recursive'=>-1
        ));
    }

    function get_depot_list($depot_ids = null){
        $depots = $this->getDepots($depot_ids);
        $depot_lists = array();
        foreach ($depots as $value) {
            $depot_lists[] = array(
                'id'=>$value['Depot']['id'],
                'name'=>$value['Depot']['name'],
            );
        }
        return $depot_lists;
    }

}