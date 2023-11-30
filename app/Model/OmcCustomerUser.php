<?php
class OmcCustomerUser extends AppModel
{
    /**
     * associations
     *
     * @var array
     */
    var $belongsTo = array(
        'OmcCustomer' => array(
            'className' => 'OmcCustomer',
            'foreignKey' => 'omc_customer_id',
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
            'OmcCustomerUser.user_id' => $userId,
        );
        # fetch the specific data from the server and retrun it.
        return $this->find('first', array('conditions' => $conditions, 'recursive' => $recursive));
    }

}