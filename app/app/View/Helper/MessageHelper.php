<?php

/**
 * Helper for formating messages as error, warning, success.
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
 * MessageHelper helper library.
 *
 * Helps formating messages as error, warning, success.
 *
 * @package       cake
 * @subpackage    cake.cake.libs.view.helpers
 * @link http://book.cakephp.org/view/1358/AJAX
 */
class MessageHelper extends AppHelper {
	
	function msg($title=NULL, $msg=NULL, $type='success' ,$close=false){
		$class_array = array('success'=>'alert-success','error'=>'alert-error','warning'=>'alert-block','info'=>'alert-info');
		$class = $class_array[$type];
        $hd = isset($title)? $title :'';
        $return = "<div class='alert $class'>";
        if($close){
            $return .= "<a class='close' data-dismiss='alert' href='#'>×</a>";
        }
        $return .= "<h4 class='alert-heading'>$hd</h4>";
        $return .= "<span>".$msg."</span>";
        $return .= "</div>";
        return $return;
    }
}