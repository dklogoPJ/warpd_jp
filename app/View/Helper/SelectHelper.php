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

    function generateTitles($echo=false,$dbval = NULL){
        $arr = array('Mr'=>'Mr','Mrs'=>'Mrs','Miss'=>'Miss','Ms'=>'Ms');
        if($echo){
            foreach ($arr as $key=>$data) {
                $selected = $dbval == $key ? "selected='selected'" : "";
                echo "<option value='{$key}' {$selected}>{$data}</option>";
            }
        }
        else{
            return $arr;
        }
    }

    function generateDays($echo=false,$dbval=NULL){
        if($echo){
            for($d = 1; $d <= 31; $d++) {
                $selected = $dbval == $d ? "selected='selected'" : "";
                if($d >= 1 && $d <= 9){
                    $d = '0'.$d;
                }
                echo "<option value='{$d}' {$selected}>{$d}</option>";
            }
        }
        else{
            $arr = array();
            for($d = 1; $d <= 31; $d++) {
                $selected = $dbval == $d ? "selected='selected'" : "";
                if($d >= 1 && $d <= 9){
                    $d = '0'.$d;
                }
                $arr[$d]=$d;
            }
            return $arr;
        }
    }


    function generateMonths($echo=false,$dbval=NULL){
        $month_arr = array('01'=>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'May','06'=>'Jun',
            '07'=>'Jul','08'=>'Aug','09'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dec');
        if($echo){
            foreach ($month_arr as $key=>$value) {
                $selected = $dbval == $key ? "selected='selected'" : "";
                echo "<option value='{$key}' {$selected}>{$value}</option>";
            }
        }
        else{
            return $month_arr;
        }
    }


    function generateYears($echo=false,$dbval=NULL, $st=NULL, $end=NULL){
        $year = intval(date('Y'));
        $y = $year-100;
        $end_yr = $year;
        if($st){
            $y = $st;
        }
        if($end){
            $end_yr = $end;
        }

        if($echo){
            for($y; $y <= $end_yr; $y++){
                $selected = $dbval == $y ? "selected='selected'" : "";
                echo "<option value='{$y}' {$selected}>{$y}</option>";
            }
        }
        else{
            $arr = array();
            for($y; $y <= $end_yr; $y++){
                $selected = $dbval == $y ? "selected='selected'" : "";
                $arr[$y]=$y;
            }
            return $arr;
        }
    }

    function generateCreditCardTypes($echo=false,$dbval = NULL){
        /*$arr = array('master'=>'Master Card','amex'=>'American Express'
        ,'visa'=>'Visa','discover'=>'Discover','jcb'=>'JCB','diners'=>'Diners Club');*/
        $arr = array('master'=>'Master Card','amex'=>'American Express'
        ,'visa'=>'Visa');
        if($echo){
            foreach ($arr as $key => $data) {
                $selected = $dbval == $key ? "selected='selected'" : "";
                echo "<option value='{$key}' {$selected}>{$data}</option>";
            }
        }
        else{
            return $arr;
        }
    }

}
