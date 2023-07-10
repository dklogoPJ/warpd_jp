<?php
class PackageType extends AppModel
{
    /**
     * associations
     *
     * @var array
     */
    var $belongsTo = array(
        'Package' => array(
            'className' => 'Package',
            'foreignKey' => 'package_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        )
    );

    function getByPackageId($id)
    {
        $conditions = array(
            'PackageType.package_id' => $id
        );
        # fetch the specific data from the server and retrun it.
        return $this->find('first', array('conditions' => $conditions, 'recursive' => -1));
    }


}