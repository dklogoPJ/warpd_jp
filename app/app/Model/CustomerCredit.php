<?php
class CustomerCredit extends AppModel
{
    /**
     * associations
     *
     * @var array
     */

    var $belongsTo = array(
        'OmcCustomer' => array(
            'className' => 'OmcCustomer',
            'foreignKey' => 'omc_customer_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        ) ,
        'ProductType' => array(
            'className' => 'ProductType',
            'foreignKey' => 'product_type_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        ),
        'CustomerCreditSetting' => array(
            'className' => 'CustomerCreditSetting',
            'foreignKey' => 'customer_credit_setting_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        )
    );

    function getCumulativeQtyAndSalesPerProduct($omc_customer_id = null, $date = ''){
        if($date == '') {
            return array();
        }
        $conditions = array('CustomerCredit.invoice_date LIKE'=> $date.'%', 'CustomerCredit.deleted' => 'n');
        if($omc_customer_id != null){
            $conditions['CustomerCredit.omc_customer_id'] = $omc_customer_id;
        }

        $query = $this->find('all', array(
            'fields' => array('CustomerCredit.product_type_id','SUM(CustomerCredit.sales_qty) AS sales_qty','SUM(CustomerCredit.sales_amount) AS sales_amount'),
            'conditions' => $conditions,
            'contain'=>array(
                'ProductType'=>array('fields'=>array('ProductType.id','ProductType.short_name'))
            ),
            'group'=>array('CustomerCredit.product_type_id'),
            'recursive' => 1
        ));

        $lists = array();
        foreach ($query as $value) {
            $lists[] = array(
                'id' => $value['CustomerCredit']['product_type_id'],
                'name' => $value['ProductType']['short_name'],
                'sales_qty' => $value[0]['sales_qty'],
                'sales_amount' => $value[0]['sales_amount']
            );
        }
        return $lists;
    }


	function get_for_export($omc_customer_id, $start_dt, $end_dt, $approved_status = '') {
		$start_dt = $this->covertDate($start_dt, 'mysql') . ' 00:00:00';
		$end_dt = $this->covertDate($end_dt, 'mysql') . ' 23:59:59';
		//get users id for this company only
		$condition = array(
			'CustomerCredit.omc_customer_id' => $omc_customer_id,
			'CustomerCredit.invoice_date >=' => $start_dt, 'CustomerCredit.invoice_date <=' => $end_dt,
			'CustomerCredit.deleted' => 'n'
		);
		if($approved_status) {
			$condition['CustomerCredit.approved_status'] = $approved_status;
		}
		$contain = array(
			'ProductType' => array('fields' => array('ProductType.id', 'ProductType.name')),
			'OmcCustomer' => array('fields' => array('OmcCustomer.id', 'OmcCustomer.name')),
			'CustomerCreditSetting' => array('fields' => array('CustomerCreditSetting.id', 'CustomerCreditSetting.name'))
		);

		return $this->find('all', array('conditions' => $condition, 'contain' => $contain, 'recursive' => 1));
	}

	function export_credit_approval($omc_customer_id, $start_dt, $end_dt) {
		$export_data = array(
			'header'=> array('Customer Name','Request Date','Product Type','Request Quantity (ltr)','Price','Delivery Method','Request Sales Amount (GHs.)'),
			'data'=> array(),
			'filename'=> 'Credit Approval'
		);
		$data_table = $this->get_for_export($omc_customer_id, $start_dt, $end_dt);
		if($data_table) {
			foreach ($data_table as $obj) {
				$req_sales_amount = isset($obj['CustomerCredit']['req_sales_amount']) ? $this->formatNumber($obj['CustomerCredit']['req_sales_amount'], 'money', 0) : '';
				$invoice_date = isset($obj['CustomerCredit']['invoice_date']) ? $this->covertDate($obj['CustomerCredit']['invoice_date'], 'mysql_flip') : '';
				$export_data['data'][] = array(
					$obj['CustomerCreditSetting']['name'],
					$invoice_date,
					$obj['ProductType']['name'],
					$obj['CustomerCredit']['req_sales_qty'],
					$obj['CustomerCredit']['price'],
					$obj['CustomerCredit']['delivery_method'],
					$req_sales_amount
				);
			}
		}
		return $export_data;
	}

	function export_credit_request_approval($omc_customer_id, $start_dt, $end_dt) {
		$export_data = array(
			'header'=> array('Customer Name','Request Date','Product Type','Request Quantity (ltr)','Price','Delivery Method','Request Sales Amount (GHs.)','Approved Quantity (ltr)','Approved Sales Amount (GHs.)','Approved Status','Comments'),
			'data'=> array(),
			'filename'=> 'Credit Request Approval'
		);
		$data_table = $this->get_for_export($omc_customer_id, $start_dt, $end_dt);
		if($data_table) {
			foreach ($data_table as $obj) {
				$req_sales_amount = isset($obj['CustomerCredit']['req_sales_amount']) ? $this->formatNumber($obj['CustomerCredit']['req_sales_amount'], 'money', 0) : '';
				$invoice_date = isset($obj['CustomerCredit']['invoice_date']) ? $this->covertDate($obj['CustomerCredit']['invoice_date'], 'mysql_flip') : '';
				$export_data['data'][] = array(
					$obj['CustomerCreditSetting']['name'],
					$invoice_date,
					$obj['ProductType']['name'],
					$obj['CustomerCredit']['req_sales_qty'],
					$obj['CustomerCredit']['price'],
					$obj['CustomerCredit']['delivery_method'],
					$req_sales_amount,
					$obj['CustomerCredit']['approved_qty'],
					$obj['CustomerCredit']['approved_amount'],
					$obj['CustomerCredit']['approved_status'],
					$obj['CustomerCredit']['comments']
				);
			}
		}
		return $export_data;
	}

	function export_credit_sales($omc_customer_id, $start_dt, $end_dt) {
		$export_data = array(
			'header'=> array('Customer Name','Invoice No','Invoice Date','Product Type','Sales Quantity (ltr)','Price','Delivery Method','Sales Amount (GHs.)','Staff Name','Comments'),
			'data'=> array(),
			'filename'=> 'Credit Sales'
		);
		$data_table = $this->get_for_export($omc_customer_id, $start_dt, $end_dt, 'Approved');
		if($data_table) {
			foreach ($data_table as $obj) {
				$sales_amount = isset($obj['CustomerCredit']['sales_amount']) ? $this->formatNumber($obj['CustomerCredit']['sales_amount'], 'money', 0) : '';
				$invoice_date = isset($obj['CustomerCredit']['invoice_date']) ? $this->covertDate($obj['CustomerCredit']['invoice_date'], 'mysql_flip') : '';
				$export_data['data'][] = array(
					$obj['CustomerCreditSetting']['name'],
					$obj['CustomerCredit']['invoice_no'],
					$invoice_date,
					$obj['ProductType']['name'],
					$obj['CustomerCredit']['sales_qty'],
					$obj['CustomerCredit']['price'],
					$obj['CustomerCredit']['delivery_method'],
					$sales_amount,
					$obj['CustomerCredit']['staff_name'],
					$obj['CustomerCredit']['comments']
				);
			}
		}

		return $export_data;
	}

}
