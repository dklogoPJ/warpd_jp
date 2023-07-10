<?php
class Package extends AppModel
{
    /**
     * associations
     */
    var $hasOne = array(
        'PackageType' => array(
            'className' => 'PackageType',
            'foreignKey' => 'package_id',
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
        'Bdc' => array(
            'className' => 'Bdc',
            'foreignKey' => 'package_id',
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

    var $hasMany = array();

}