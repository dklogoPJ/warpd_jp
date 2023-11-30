<?php
class BdcUser extends AppModel
{
    /**
     * associations
     *
     * @var array
     */
    var $belongsTo = array(
        'Bdc' => array(
            'className' => 'Bdc',
            'foreignKey' => 'bdc_id',
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


    function getUserById($userId = null, $recursive = 1)
    {
        $conditions = array(
            'BdcUser.user_id' => $userId,
        );
        # fetch the specific data from the server and retrun it.
        return $this->find('first', array('conditions' => $conditions, 'recursive' => $recursive));
    }

}