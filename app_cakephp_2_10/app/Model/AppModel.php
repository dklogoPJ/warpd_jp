<?php
/**
 * Application model for CakePHP.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model {
	var $actsAs = array('Containable');
	var $inserted_ids = array();

	function afterSave($created)
	{
		if ($created) {
			$this->inserted_ids[] = $this->getInsertID();
		}
		return true;
	}

	function getInsertedIds($model = NULL)
	{
		return $this->inserted_ids;
	}

	function getNextAutoIncrement()
	{
		$table = Inflector::tableize($this->name);
		$info = $this->query("SHOW TABLE STATUS LIKE '$table'");
		return $info[0]['TABLES']['Auto_increment'];
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

	/** General usage,
	 * this might be useful for some rare occasions.
	 * It returns all fields of the current database table except the ones you blacklisted. */
	function blacklist($blackList = array())
	{
		return array_diff(array_keys($this->schema()), $blackList);
	}

	function debugQuery(){
		$log = $this->getDataSource()->getLog(false, false);
		debug($log);
	}

	function __get_enum_values($field)
	{
		$table = $this->table;

		$types = $this->query( "SHOW COLUMNS FROM {$table} WHERE Field = '{$field}'" );
		$type = $types[0]['COLUMNS']['Type'];
		preg_match('/^enum\((.*)\)$/', $type, $matches);
		foreach( explode(',', $matches[1]) as $value )
		{
			$enum[] = trim( $value, "'" );
		}
		return $enum;
	}
}
