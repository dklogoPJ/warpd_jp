<?php
class Waybill extends AppModel
{
    var $belongsTo = array(
        'Order' => array(
            'className' => 'Order',
            'foreignKey' => 'order_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        ),
        'Depot' => array(
            'className' => 'Depot',
            'foreignKey' => 'depot_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        ),
        'Cep' => array(
            'className' => 'Cep',
            'foreignKey' => 'cep_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        ),
        'Bdc' => array(
            'className' => 'Bdc',
            'foreignKey' => 'bdc_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        ),
        'Omc' => array(
            'className' => 'Omc',
            'foreignKey' => 'omc_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        )
    );


    function getWaybillById($id){
        $condition_array = array(
            'Waybill.id' => $id
        );

        $contain = array(
            'Bdc'=>array('fields' => array('Bdc.id', 'Bdc.name')),
            'Omc'=>array('fields' => array('Omc.id', 'Omc.name')),
            'Cep'=>array('fields' => array('Cep.id', 'Cep.name')),
            'Depot'=>array('fields' => array('Depot.id', 'Depot.name')),
            'Order'=>array(
                'fields' => array('Order.id', 'Order.order_date','Order.loaded_quantity','Order.loaded_date','Order.truck_no'),
                'ProductType'=>array('fields' => array('ProductType.id', 'ProductType.name'))
            )
        );

        $data_table = $this->find('first', array(
                'conditions' => $condition_array,
                'contain'=>$contain,
                'recursive' => 2
            )
        );

        $Endorsement = ClassRegistry::init('Endorsement');
        $signatories = $Endorsement->getSignatories('Way Bill',$id);
        if(!empty($signatories)){
            $data_table['Endorsement'] = $signatories;
        }
        return $data_table;
    }
}