<?php

/**
 * @name user.php
 * @copyright (c) 2011
 */
App::uses('AuthComponent', 'Controller/Component');

class User extends AppModel
{

    var $name = 'User';

    /**
     * associations
     */

    var $hasMany = array(
        'LoginTrail' => array(
            'className' => 'LoginTrail',
            'foreignKey' => 'user_id',
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


    var $belongsTo = array(
        'Group' => array(
            'className' => 'Group',
            'foreignKey' => 'group_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        ), 'Bdc' => array(
            'className' => 'Bdc',
            'foreignKey' => 'bdc_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        ),
        'Omc' => array(
            'className' => 'Omc',
            'foreignKey' => 'omc_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        ),
        'OmcCustomer' => array(
            'className' => 'OmcCustomer',
            'foreignKey' => 'omc_customer_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        ),
        'Org' => array(
            'className' => 'Org',
            'foreignKey' => 'org_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        )
    );


    /*public function beforeSave() {
        if( isset($this->data[$this->alias]['password']) ) {
            $this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password']);
        }
        return true;
    }*/

    /**
     * This function retrieves all the users from the database tables users.
     * @name getAllUsers
     * @param void
     * @return mixed. Array containing the data retrieved.
     * @access public
     */
    function getAllUsers()
    {
        # fetch the data and return it.
        return $this->find('all', array('conditions' => array('User.deleted' => 'n'), 'recursive' => -1));
    }

    /**
     * This function retrieves the user data based upon his/her id
     * @name getUserById
     * @param string $userId string holding the user id.his parameter is defaulted to null.
     * When it is null it means that cake is going to use the ActiveRecord system to retrieve the data.
     * @return mixed. Array containing the user data.
     * @access public
     */
    function getUserById($userId = null, $recursive = 1)
    {
        $conditions = array(
            'User.id' => $userId,
            'User.deleted' => 'n'
        );
        # fetch the specific data from the server and retrun it.
        return $this->find('first', array('conditions' => $conditions, 'recursive' => $recursive));
    }

    # User Data manipulation functions
    /**
     * This function retrieves the user by his login name
     * @name getUserByLoginName
     * @param string $loginName
     * @return array of data containing the login name and the user password
     * @access public
     */

    function getUserByLoginName($loginName = null)
    {
        # search condition
        $conditions = array(
            'User.username' => $loginName,
            'User.deleted' => 'n'
        );
        # fetch the specific data from the server and retrun it.
        return $this->find('first', array('conditions' => $conditions, 'recursive' => 1));
    }

    function getDepotGroup($userId)
    {
        $data = $this->getUserById($userId);
        $your_depot_group = $data['Group']['group_depot'];
        # fetch the specific data from the server and retrun it.
        return $your_depot_group;
    }

    function userTypesEntity(){
        $entities  = array();
        $types =  $this->__get_enum_values('user_type');
        foreach($types as $type){
            $seperator = strstr($type, '_');
            if($seperator){
                $str_arr = explode('_',$type);
                if($str_arr[0] == 'ceps'){//treat ceps special
                    $entities[$type] = 'Cep';
                }
                else{
                    $entities[$type] = ucwords($str_arr[0]).ucwords($str_arr[1]);
                }
            }
            else{
                $entities[$type] = ucwords($type);
            }
        }
       // $entities = array('bdc'=>'Bdc','omc'=>'Omc','omc_customer'=>'OmcCustomer','org'=>'Org','depot'=>'Depot','ceps_central'=>'Cep','ceps_depot'=>'Cep');
        return $entities;
    }

}