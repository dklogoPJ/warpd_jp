<?php
/**
 * MessageAddressBook Model
 */
class MessageAddressBook extends AppModel
{
    /**
     * associations
     *
     * @var array
     */
    /* var $belongsTo = array(
         'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
         )
      );*/


    function getContacts($user_id){
        $condition_array = array('MessageAddressBook.user_id'=>$user_id,'MessageAddressBook.deleted' => 'n');
        $data_table = $this->find('all', array('conditions' => $condition_array, 'recursive' => -1));
        $contacts = array();
        foreach($data_table as $contact){
            $contacts[] = $contact['MessageAddressBook'];
        }
        return $contacts;
    }
}