<?php
/**
 * MessageAddressBookContact Model
 */
class MessageAddressBookContact extends AppModel
{
    /**
     * associations
     *
     * @var array
     */
    var $belongsTo = array(
        'MessageAddressBook' => array(
            'className' => 'MessageAddressBook',
            'foreignKey' => 'message_address_book_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        )/*,
      'User' => array(
          'className' => 'User',
          'foreignKey' => 'user_id',
          'conditions' => '',
          'order' => '',
          'limit' => '',
          'dependent' => false
      )*/
    );

}