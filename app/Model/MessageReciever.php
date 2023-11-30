<?php
/**
 * MessageReciever Model
 */
class MessageReciever extends AppModel
{
    /**
     * associations
     *
     * @var array
     */
    var $belongsTo = array(
        'Message' => array(
            'className' => 'Message',
            'foreignKey' => 'message_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        ),
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        )
    );

    function messageCount($user_id){
        return $this->find('count',array(
            'conditions'=>array('MessageReciever.user_id'=>$user_id, 'MessageReciever.msg_status'=>'unread','MessageReciever.trash'=>'n'),
            'recursive'=>-1
        ));
    }

}