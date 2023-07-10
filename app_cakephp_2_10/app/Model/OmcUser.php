<?php
class OmcUser extends AppModel
{
    /**
     * associations
     *
     * @var array
     */

    var $hasMany = array(
        'OmcUserBdc' => array(
            'className' => 'OmcUserBdc',
            'foreignKey' => 'omc_user_id',
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
    );

    var $belongsTo = array(
        'Omc' => array(
            'className' => 'Omc',
            'foreignKey' => 'omc_id',
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
            'OmcUser.user_id' => $userId,
        );
        # fetch the specific data from the server and retrun it.
        return $this->find('first', array('conditions' => $conditions, 'recursive' => $recursive));
    }

}