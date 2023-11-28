<?php
class OmcReport extends AppModel {
	var $useTable = false;

	public function getDailySalesLiters ($omc_id, $report_date = '') {
		$form_key = 'daily_sales_record';
		$field = '55'; //id for Cash Day Sales Qty. ltr (B) (can be comma separated ids)
		return $this->getFormData($omc_id, $form_key, $field, $report_date);
	}

	public function getDailySalesCedis ($omc_id, $report_date = '') {
		$form_key = 'daily_sales_record';
		$field = '56'; //id for Cash Day Sales Value (A x B) (can be comma separated ids)
		return $this->getFormData($omc_id, $form_key, $field, $report_date);
	}


	function getFormData ($omc_id, $form_key, $fields, $report_date = '') {
		$bar_data = array(
			'x-axis'=>array(),
			'y-axis'=>array()
		);
		$form_query = ClassRegistry::init('OmcSalesForm')->getSalesFormForReport($omc_id, $form_key);
		$omc_customers_ids = ClassRegistry::init('OmcCustomer')->getOmcCustomerIds($omc_id);

		if($form_query) {
			$form_id = $form_query['OmcSalesForm']['id'];
			$primary_fields_array = array();
			foreach ($form_query['OmcSalesFormPrimaryFieldOption'] as $row) {
				$primary_fields_array[] = $row['id'];
			}
			$fields_column_array = array();
			foreach ($form_query['OmcSalesFormField'] as $row) {
				$fields_column_array[$row['id']] = $row['field_name'];
			}
			$primary_fields = implode(',', $primary_fields_array);

			$query_collection = array();
			//Loop through each customer and use it to fetch the data whiles accumulating it
			foreach ($omc_customers_ids as $customers_id) {
				$customer_query_data = $this->queryFormData($customers_id, $form_id, $primary_fields, $fields, $report_date);
				foreach($customer_query_data as $customer_row_data){
					$product_name = $customer_row_data['osfpfo']['option_name'];
					$product_columns = $customer_row_data[0];
					if(isset($query_collection[$product_name])) {
						foreach($product_columns as $column_key => $column_data){
							if(isset($query_collection[$product_name]['columns'][$column_key])) {
								$query_collection[$product_name]['columns'][$column_key] = floatval($query_collection[$product_name]['columns'][$column_key]) + floatval($column_data);
							} else {
								$query_collection[$product_name]['columns'][$column_key] = floatval($column_data);
							}
						}
					} else {
						$query_collection[$product_name] = array('columns'=>array());
						foreach($product_columns as $column_key => $column_data){
							$query_collection[$product_name]['columns'][$column_key] = floatval($column_data);
						}
					}
				}
			}

			if ($query_collection) {
				$fields_array = explode(',',$fields);
				$has_multiple_fields = count($fields_array) > 1;
				foreach($query_collection as $product_name_key => $product_columns_values){
					$bar_data['x-axis'][]= $product_name_key;
					if($has_multiple_fields) {
						foreach($fields_array as $fa){
							if(isset($bar_data['y-axis'][$fa])) {
								$bar_data['y-axis'][$fa]['data'][]= $product_columns_values['columns'][$fa] ? floatval($product_columns_values['columns'][$fa]) : 0;
							}else {
								$bar_data['y-axis'][$fa] = array('name'=> $fields_column_array[$fa], 'data'=>array($product_columns_values['columns'][$fa] ? floatval($product_columns_values['columns'][$fa]) : 0));
							}
						}
					} else {
						$bar_data['y-axis'][] = array('name'=> $product_name_key, 'data'=>array($product_columns_values['columns'][$fields_array[0]] ? floatval($product_columns_values['columns'][$fields_array[0]]) : 0));
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
