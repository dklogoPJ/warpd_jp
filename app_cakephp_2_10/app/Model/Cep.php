<?php
class Cep extends AppModel
{
    /**
     * associations
     */

    var $hasMany = array(
        'Order' => array(
            'className' => 'Order',
            'foreignKey' => 'cep_id',
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
            'foreignKey' => 'cep_id',
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
        'Depot' => array(
            'className' => 'Depot',
            'foreignKey' => 'depot_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        )
    );

    function getCepById($id = null)
    {
        return $this->find('first', array('conditions' => array('id' => $id), 'recursive' => -1));
    }

    function getCepByDepot($id = null)
    {
        return $this->find('first', array('conditions' => array('depot_id' => $id), 'recursive' => -1));
    }
}