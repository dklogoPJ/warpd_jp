<?php
class OmcUserBdc extends AppModel
{
    /**
     * associations
     *
     * @var array
     */
    var $belongsTo = array(
        'OmcUser' => array(
            'className' => 'OmcUser',
            'foreignKey' => 'omc_user_id',
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
        )
    );

}