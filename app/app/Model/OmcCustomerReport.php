<?php
class OmcCustomerReport extends AppModel {
	var $useTable = false;

	public function getDailySalesLiters ($omc_customer_id, $omc_id, $report_date = '') {
		$form_key = 'daily_sales_record';
		$field = '55'; //id for Cash Day Sales Qty. ltr (B) (can be comma separated ids)
		return $this->getFormData($omc_customer_id, $omc_id, $form_key, $field, $report_date);
	}

	public function getDailySalesCedis ($omc_customer_id, $omc_id, $report_date = '') {
		$form_key = 'daily_sales_record';
		$field = '56'; //id for Cash Day Sales Value (A x B) (can be comma separated ids)
		return $this->getFormData($omc_customer_id, $omc_id, $form_key, $field, $report_date);
	}

	public function getStockCalculation ($omc_customer_id, $omc_id, $report_date = '') {
		$form_key = 'bulk_stock_position';
		$fields = '115,117'; //ids for Pump Day Sales ltr,Tank day sales (can be comma separated ids)
		return $this->getFormData($omc_customer_id, $omc_id, $form_key, $fields, $report_date);
	}

	function getFormData ($omc_customer_id, $omc_id, $form_key, $fields, $report_date = '') {
		$bar_data = array(
			'x-axis'=>array(),
			'y-axis'=>array()
		);
		$query = ClassRegistry::init('OmcSalesForm')->getSalesFormForReport($omc_customer_id, $omc_id, $form_key);
		if($query) {
			$form_id = $query['OmcSalesForm']['id'];
			$primary_fields_array = array();
			foreach ($query['OmcSalesFormPrimaryFieldOption'] as $row) {
				$primary_fields_array[] = $row['id'];
			}
			$fields_column_array = array();
			foreach ($query['OmcSalesFormField'] as $row) {
				$fields_column_array[$row['id']] = $row['field_name'];
			}

			$primary_fields = implode(',', $primary_fields_array);
			$query_data = $this->queryFormData($omc_customer_id, $form_id, $primary_fields, $fields, $report_date);
			if ($query_data) {
				$fields_array = explode(',',$fields);
				$has_multiple_fields = count($fields_array) > 1;
				foreach($query_data as $query_row){
					$bar_data['x-axis'][]= $query_row['osfpfo']['option_name'];
					if($has_multiple_fields) {
						foreach($fields_array as $fa){
							if(isset($bar_data['y-axis'][$fa])) {
								$bar_data['y-axis'][$fa]['data'][]= $query_row[0][$fa] ? floatval($query_row[0][$fa]) : 0;
							}else {
								$bar_data['y-axis'][$fa] = array('name'=> $fields_column_array[$fa], 'data'=>array($query_row[0][$fa] ? floatval($query_row[0][$fa]) : 0));
							}
						}
					} else {
						$bar_data['y-axis'][] = array('name'=> $query_row['osfpfo']['option_name'], 'data'=>array($query_row[0][$fields_array[0]] ? floatval($query_row[0][$fields_array[0]]) : 0));
					}
				}
				//Clean up the y-axis data so there are no indexes
				$y_axis = $bar_data['y-axis'];
				$bar_data['y-axis'] = array();
				foreach($y_axis as $yx){
					$bar_data['y-axis'][] = $yx;
				}
			}
			return $bar_data;
		}
		return $bar_data;
	}

	function queryFormData($omc_customer_id, $form_id, $primary_fields, $fields, $record_date) {
		$result = $this->query("CALL dsrp_get_query({$form_id}, {$omc_customer_id}, '{$record_date}', '{$fields}', '{$primary_fields}');");
		$query_str = $result[0][0]['result_string'];
		return $this->query($query_str);
	}

}
