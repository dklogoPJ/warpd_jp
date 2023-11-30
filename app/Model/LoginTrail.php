<?php

/**
 * @copyright (c) 2011
 */
App::uses('AppModel', 'Model');

class LoginTrail extends AppModel {

    var $name = 'LoginTrail';

    var $belongsTo = array(
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        )
    );
}