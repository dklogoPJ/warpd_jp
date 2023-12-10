<?php
class CustomerCreditPayment extends AppModel
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
        )  ,
        'CustomerCreditSetting' => array(
            'className' => 'CustomerCreditSetting',
            'foreignKey' => 'customer_credit_setting_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        )
    );

	function get_for_export($omc_customer_id, $start_dt, $end_dt, $approved_status = '') {
		$start_dt = $this->covertDate($start_dt, 'mysql') . ' 00:00:00';
		$end_dt = $this->covertDate($end_dt, 'mysql') . ' 23:59:59';
		//get users id for this company only

		$condition = array(
			'CustomerCreditPayment.omc_customer_id' => $omc_customer_id,
			'CustomerCreditPayment.receipt_date >=' => $start_dt, 'CustomerCreditPayment.receipt_date <=' => $end_dt,
			'CustomerCreditPayment.deleted' => 'n'
		);
		/*if($approved_status) {
			$condition['CustomerCredit.approved_status'] = $approved_status;
		}*/
		$contain = array(
			'OmcCustomer' => array('fields' => array('OmcCustomer.id', 'OmcCustomer.name')),
			'CustomerCreditSetting' => array('fields' => array('CustomerCreditSetting.id', 'CustomerCreditSetting.name'))
		);

		return $this->find('all', array('conditions' => $condition, 'contain' => $contain, 'recursive' => 1));
	}

	function export_credit_payment($omc_customer_id, $start_dt, $end_dt) {
		$export_data = array(
			'header'=> array('Customer Name','Receipt No','Receipt Date','Payment Amount (GHs.)','Payment Method','NCT Channel Type','Payment Instrument No'),
			'data'=> array(),
			'filename'=> 'Credit Payment'
		);
		$data_table = $this->get_for_export($omc_customer_id, $start_dt, $end_dt);
		if($data_table) {
			foreach ($data_table as $obj) {
				$payment_amount = isset($obj['CustomerCreditPayment']['payment_amount']) ? $this->formatNumber($obj['CustomerCreditPayment']['payment_amount'], 'money', 0) : '';
				$receipt_date = isset($obj['CustomerCreditPayment']['receipt_date']) ? $this->covertDate($obj['CustomerCreditPayment']['receipt_date'], 'mysql_flip') : '';

				$export_data['data'][] = array(
					$obj['CustomerCreditSetting']['name'],
					$obj['CustomerCreditPayment']['receipt_no'],
					$receipt_date,
					$payment_amount,
					$obj['CustomerCreditPayment']['payment_method'],
					$obj['CustomerCreditPayment']['nct_channel'],
					$obj['CustomerCreditPayment']['payment_instrument']
				);
			}
		}
		return $export_data;
	}


}
