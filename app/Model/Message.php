<?php
/**
 * Message Model
 */
class Message extends AppModel
{
    /**
     * associations
     *
     * @var array
     */
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

    var $hasMany = array(
        'MessageReciever' => array(
            'className' => 'MessageReciever',
            'foreignKey' => 'message_id',
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




}