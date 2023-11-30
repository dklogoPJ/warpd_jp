<?php
/**
 * Application level View Helper
 *
 * This file is application-wide helper file. You can put all
 * application-wide helper-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Helper
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Helper', 'View');

/**
 * Application helper
 *
 * Add your application-wide methods in the class below, your helpers
 * will inherit them.
 *
 * @package       app.View.Helper
 */
class AppHelper extends Helper {

    function covertDate($date = null, $type = 'mysql')
    {
        $res = '';
        if ($type == 'mysql') {
            $res = date("Y-m-d", strtotime($date));
        }
        elseif ($type == 'mysql_flip') {
            $res = date("d-m-Y", strtotime($date));
        }
        elseif ($type == 'ui') {
            $res = date("F d, Y", strtotime($date));
        }
        elseif($type == 'formal'){
            $res = date("m/d/Y", strtotime($date));
        }
        return $res;
    }

    function formatNumber($value = 0, $type = 'number', $decimal_place = 2)
    {
        if ($type == 'money') {
            $num = number_format($value, $decimal_place, '.', ',');
        } else {
            $num = number_format($value, $decimal_place, '.', '');
        }

        return $num;
    }
}
