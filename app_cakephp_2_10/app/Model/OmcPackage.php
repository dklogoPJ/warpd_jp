<?php
class OmcPackage extends AppModel
{
    /**
     * associations
     */
    var $hasMany = array(
        'Omc' => array(
            'className' => 'Omc',
            'foreignKey' => 'omc_package_id',
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
    );

    function getByPackageId($id)
    {
        $conditions = array(
            'OmcPackage.id' => $id
        );
        # fetch the specific data from the server and retrun it.
        return $this->find('first', array('conditions' => $conditions, 'recursive' => -1));
    }
}
