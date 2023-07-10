<?php

/**
 * Helper for generating form select options.
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.cake.libs.view.helpers
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * SelectOptionsHelper helper library.
 *
 * Helps generating form select options..
 *
 * @package       cake
 * @subpackage    cake.cake.libs.view.helpers
 * @link http://book.cakephp.org/view/1358/AJAX
 */
class SelectHelper extends AppHelper {

    function generateTitles($dbval = NULL){
        $arr = array('Mrs'=>'Mrs','Miss'=>'Miss','Mr'=>'Mr','Ms'=>'Ms');
        $return = '';
        foreach ($arr as $key=>$data) {
            $selected = $dbval == $key ? "selected='selected'" : "";
            $return.= "<option value='{$key}' {$selected}>{$data}</option>";
        }
        return $return;
    }

    function generateDays($dbval=NULL){
        $return = '';
        for($d = 1; $d <= 31; $d++) {
            $selected = $dbval == $d ? "selected='selected'" : "";
            if($d >= 1 && $d <= 9){
                $d = '0'.$d;
            }
            $return.= "<option value='{$d}' {$selected}>{$d}</option>";
        }
        return $return;
    }


    function generateMonths($dbval=NULL){
        $return = '';
        $month_arr = array('01'=>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'May','06'=>'Jun',
            '07'=>'Jul','08'=>'Aug','09'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dec');
        foreach ($month_arr as $key=>$value) {
            $selected = $dbval == $key ? "selected='selected'" : "";
            $return.= "<option value='{$key}' {$selected}>{$value}</option>";
        }
        return $return;
    }


    function generateYears($dbval=NULL, $st=NULL, $end=NULL){
        $return = '';
        $year = intval(date('Y'));
        $y = $year-100;
        $end_yr = $year;
        if($st){
            $y = $st;
        }
        if($end){
            $end_yr = $end;
        }

        for($y; $y <= $end_yr; $y++){
            $selected = $dbval == $y ? "selected='selected'" : "";
            $return.= "<option value='{$y}' {$selected}>{$y}</option>";
        }
        return $return;
    }

    function generateCreditCardTypes($dbval = NULL){
        $return = '';
        /*$arr = array('master'=>'Master Card','amex'=>'American Express'
        ,'visa'=>'Visa','discover'=>'Discover','jcb'=>'JCB','diners'=>'Diners Club');*/
        $arr = array('master'=>'Master Card','amex'=>'American Express'
        ,'visa'=>'Visa');
        foreach ($arr as $key => $data) {
            $selected = $dbval == $key ? "selected='selected'" : "";
            $return.= "<option value='{$key}' {$selected}>{$data}</option>";
        }
        return $return;
    }

}
