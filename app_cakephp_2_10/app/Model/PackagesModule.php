<?php
class PackagesModule extends AppModel
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

}
