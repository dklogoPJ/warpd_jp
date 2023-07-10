<?php

/**
 * Helper for generating table form.
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
class TableFormHelper extends AppHelper {


    function render_preview($name,$columns = array(),$data=array(),$class='table table-bordered'){
        $columns_count = count($columns);
        $table = "<table id='{$name}' width='100%' class='{$class}'>";
            $table .= "<thead>";
                $table .= "<tr>";
                foreach($columns as $column){
                    $table .= "<th>";
                        $table .= $column['name'];
                    $table .= "</th>";
                }
                $table .= "</tr>";
            $table .= "</thead>";
            $table .= "<tbody>";
                for($x = 1; $x <= 3; $x++){
                    $table .= "<tr>";
                        for($d = 1; $d <=$columns_count; $d++){
                            $table .= "<td>";
                            $table .= 'Test Value';
                            $table .= "</td>";
                        }
                    $table .= "</tr>";
                }
            $table .= "</tbody>";
        $table .= "</table>";

        return $table;
    }


    function render($name,$columns = array(),$data=array(),$class='table table-bordered'){
        $table = "<table id='{$name}' width='100%' class='form-tables {$class}'>";
        $table .= "<thead>";
        $table .= "<tr>";
        $fields = array();

        foreach($columns as $column){
            $table .= "<th>";
            $table .= $column['field_name'];
            $table .= "</th>";
            $fields[] = $column;
        }
        $table .= "</tr>";
        $table .= "</thead>";
        $table .= "<tbody>";
       //Render Data here
        foreach($data as $record){
            $record_id = $record['record_id'];
            $values = $record['values'];
            $table .= "<tr data-record_id='{$record_id}' class=''>";
            foreach($fields as $field){
                $field_id = $field['id'];
                $control_field = ($field['control_field']) ? 'yes':'no';
                $rule_type = $field['rule_type'];
                if(isset($values[$field_id])){
                    $cell_value = $values[$field_id]['value'];
                    if(is_numeric($cell_value)){
                        $cell_value = $this->formatNumber($cell_value,'money',2);
                    }
                    $cell_value_id = $values[$field_id]['id'];
                    $table .= "<td data-control_field='{$control_field}' data-rule_type='{$rule_type}' data-field_id='{$field_id}' data-value='{$cell_value}' data-value_id='{$cell_value_id}'>";
                    $table .= $cell_value;
                    $table .= "</td>";
                }
                else{
                    $table .= "<td data-control_field='{$control_field}' data-rule_type='{$rule_type}' data-field_id='{$field_id}' data-value='' data-value_id=''>";
                    $table .= '';
                    $table .= "</td>";
                }
            }
            $table .= "</tr>";
        }
        $table .= "</tbody>";
        $table .= "</table>";

        return array('table'=>$table,"fields"=>$fields);
    }

}
