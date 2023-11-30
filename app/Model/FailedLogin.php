<?php

/**
 * @copyright (c) 2011
 */
App::uses('AppModel', 'Model');

class FailedLogin extends AppModel {

    var $name = 'FailedLogin';


    function afterSave($param=null){
        $username = $this->data[$this->alias]['username'];
        $trial_count = $this->getLoginFailedAttempts($username);
        if($trial_count >= 3){
            //Block user account
            $modObj = ClassRegistry::init('Login');
            $modObj->save(array(
                'username' => $username,
                'count'=>'3'
            ));

            //disable account
            $modObj = ClassRegistry::init('User');
            $result = $modObj->updateAll(
                array('User.active' => "'Disabled'",'User.bg_color' => "'tr_red'"),
                array('User.username' => $username)
            );
        }
    }

    function getLoginFailedAttempts($username = null, $ip_addr = null) {
        //$sql = 'SELECT COUNT(1) AS failed FROM failed_logins WHERE attempted > DATE_SUB(NOW(), INTERVAL 15 minute)';
        $result = $this->find('count', array(
            //'conditions' => array('username' => $username, 'created > '=> 'date_sub(now(), interval 10 minute)'),
            'conditions' => array('username' => $username, 'created LIKE'=> date('Y-m-d').'%'),
            'recursive' => -1
        ));
        return $result;
    }

    function validateLoginAttempts($username) {
        $trial_count = $this->getLoginFailedAttempts($username);
        $modObj = ClassRegistry::init('Login');
        $result = $modObj->find('first', array(
            'conditions' => array('username' => $username),
            'recursive' => -1
        ));
        if($result && $trial_count >= 3){
            return true;
        }
        else{
            return false;
        }
    }
}