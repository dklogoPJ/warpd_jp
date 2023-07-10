<?php
class Group extends AppModel
{
    /**
     * associations
     */
    var $hasMany = array(
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'group_id',
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
        'MenuGroup' => array(
            'className' => 'MenuGroup',
            'foreignKey' => 'group_id',
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
        ),
        'OmcCustomer' => array(
            'className' => 'OmcCustomer',
            'foreignKey' => 'omc_customer_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        ),
        'Org' => array(
            'className' => 'Org',
            'foreignKey' => 'org_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        ),
        'Depot' => array(
            'className' => 'Depot',
            'foreignKey' => 'group_depot',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        )
    );


    function getGroupById($id=null){
        $condition_array = array('Group.id' =>$id, 'Group.deleted' => 'n');
        $data = $this->find('first',array(
            'conditions'=>$condition_array,
            'recursive'=>-1
        ));
        return $data;
    }


    function getGroups($type,$comp_id){
        if ($type == 'bdc') {
            $find_id = 'bdc_id';
        }
        elseif ($type == 'omc') {
            $find_id = 'omc_id';
        }
        elseif ($type == 'omc_customer') {
            $find_id = 'omc_customer_id';
        }
        elseif ($type == 'ceps_depot' || $type == 'ceps_central') {
            $find_id = 'cep_id';
        }
        elseif ($type == 'depot') {
            $find_id = 'depot_id';
        }
        elseif ($type == 'org') {
            $find_id = 'org_id';
        }
        $condition_array = array('Group.type' =>$type,'Group.'. $find_id => $comp_id, 'Group.deleted' => 'n');

        $data = $this->find('all',array(
            'conditions'=>$condition_array,
            'recursive'=>-1
        ));
        $arr = array();
        foreach($data as $d){
            $arr[]= array(
                'id'=>$d['Group']['id'],
                'name'=>$d['Group']['name']
            );
        }

        return $arr;

    }


    function getOmcCustomerGroups($omc_id){
        $condition_array = array('Group.omc_id' =>$omc_id,'Group.type' => 'omc_customer', 'Group.deleted' => 'n');

        $data = $this->find('all',array(
            'conditions'=>$condition_array,
            'recursive'=>-1
        ));
        $arr = array();
        foreach($data as $d){
            $arr[]= array(
                'id'=>$d['Group']['id'],
                'name'=>$d['Group']['name']
            );
        }

        return $arr;

    }

}