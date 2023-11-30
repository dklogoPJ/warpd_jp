<?php
class Org extends AppModel
{
    /**
     * associations
     */
    var $hasMany = array(
        'Group' => array(
            'className' => 'Group',
            'foreignKey' => 'org_id',
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
            'foreignKey' => 'org_id',
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


    function getOrgById($id = null)
    {
        return $this->find('first', array('conditions' => array('id' => $id), 'recursive' => -1));
    }


}