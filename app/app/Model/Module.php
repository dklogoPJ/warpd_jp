<?php
class Module extends AppModel
{
    /**
     * associations
     */
    var $hasAndBelongsToMany = array(
        'Bdc' => array(
            'className' => 'Bdc',
            'joinTable' => 'bdc_modules',
            'foreignKey' => 'module_id',
            'associationForeignKey' => 'bdc_id',
            'unique' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'finderQuery' => '',
            'deleteQuery' => '',
            'insertQuery' => ''
        )
    );

}