<?php
class BdcPackage extends AppModel
{
    /**
     * associations
     */
    var $hasMany = array(
        'Bdc' => array(
            'className' => 'Bdc',
            'foreignKey' => 'bdc_package_id',
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
            'BdcPackage.id' => $id
        );
        # fetch the specific data from the server and retrun it.
        return $this->find('first', array('conditions' => $conditions, 'recursive' => -1));
    }
}
