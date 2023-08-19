<?php
class AdditiveSetup extends AppModel
{
    /**
     * associations
     *
     * @var array
     */

    var $belongsTo = array(
        'ProductType' => array(
            'className' => 'ProductType',
            'foreignKey' => 'product_type_id',
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

    var $hasMany = array(
        'AdditiveStock' => array(
            'className' => 'AdditiveStock',
            'foreignKey' => 'additive_setup_id',
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
        'AdditiveDopingRatio' => array(
            'className' => 'AdditiveDopingRatio',
            'foreignKey' => 'additive_setup_id',
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


    function getAdditiveList($additives_ids = null){
        $conditions = array('deleted' => 'n');
        if($additives_ids != null){
            $conditions['id'] = $additives_ids;
        }
        $additives_type = $this->find('all', array(
            'fields' => array('id', 'name'),
            'conditions' => $conditions,
            'recursive' => -1
        ));
        $additives_lists = array();
        foreach ($additives_type as $value) {
            $additives_lists[] = $value['AdditiveSetup'];
        }
        return $additives_lists;
    }

    

}