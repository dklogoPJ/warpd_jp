<?php
class OmcCashCreditSummary extends AppModel
{
    /**
     * associations
     */
    var $belongsTo = array(
        'OmcSalesSheet' => array(
            'className' => 'OmcSalesSheet',
            'foreignKey' => 'omc_sales_sheet_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        )
    );

    function setUp($sheet_id){
        //Setup is different from the other forms with control fields, here we pre-calculate certain fields values
        $res = $this->find('first',array(
            'conditions'=>array('omc_sales_sheet_id'=>$sheet_id),
            'recursive'=>-1
        ));
        if($res){
            $data = $res['OmcCashCreditSummary'];
        }
        else{//Pre create the empty record
            $save_arr = array(
                'omc_sales_sheet_id'=>$sheet_id
            );
            $this->save($save_arr);
            $res = $this->find('first',array(
                'conditions'=>array('omc_sales_sheet_id'=>$sheet_id),
                'recursive'=>-1
            ));
            $data = $res['OmcCashCreditSummary'];
        }
        //Update Day Cash Sales
        $OmcDailySalesProduct = ClassRegistry::init('OmcDailySalesProduct');
        $total_cash_day_sales_value = $OmcDailySalesProduct->sumField($sheet_id,'cash_day_sales_value');

        //Update Total Cash Received
        $total_cash_received = $total_cash_day_sales_value + doubleval($data['collection_on_credit_sales']) + doubleval($data['other_collection']);
        //Update Total Cash In Hand
        $total_cash_in_hand  = $total_cash_received + doubleval($data['cash_bf_prev_day']);
        //Update Total Cash Withdrawn
        $total_cash_withdrawn =  doubleval($data['station_expenses']) + doubleval($data['approved_salaries_withdrawals']) + doubleval($data['others']);
        //Update Cash C/F Next Day
        $cash_cf_to_next_day = $total_cash_in_hand - $total_cash_withdrawn - doubleval($data['payment_to_omc']);

        $save_arr = array(
            'id'=>$data['id'],
            'day_cash_sales'=>$total_cash_day_sales_value,
            'total_cash_received'=>$total_cash_received,
            'total_cash_in_hand'=>$total_cash_in_hand,
            'total_cash_withdrawn'=>$total_cash_withdrawn,
            'cash_cf_to_next_day'=>$cash_cf_to_next_day,
        );
        $this->save($save_arr);

        $res = $this->find('first',array(
              'conditions'=>array('omc_sales_sheet_id'=>$sheet_id),
              'contain'=>array('OmcSalesSheet')
        ));

        return $res;
    }


    function getData($sheet_id){
        $res = $this->find('all',array(
            'conditions'=>array('omc_sales_sheet_id'=>$sheet_id),
            'recursive'=>-1
        ));

        return $res;
    }

    function getFieldValue($sheet_id,$field){
        $res = $this->find('first',array(
            'conditions'=>array('omc_sales_sheet_id'=>$sheet_id),
            'recursive'=>-1
        ));
        if($res){
            return $res['OmcCashCreditSummary'][$field];
        }
        else{
            return false;
        }
    }

    function getTableSetup(){
        $table_setup = array(
            'cash_bf_prev_day'=>array('field'=>'cash_bf_prev_day','header'=>'Cash B/F Previous Day','unit'=>'','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'day_cash_sales'=>array('field'=>'day_cash_sales','header'=>'Day Cash Sales','unit'=>'','editable'=>'no','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'collection_on_credit_sales'=>array('field'=>'collection_on_credit_sales','header'=>'Collection On Credit Sales','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'other_collection'=>array('field'=>'other_collection','header'=>'Other Collections','unit'=>'','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'total_cash_received'=>array('field'=>'total_cash_received','header'=>'Total Cash Received','unit'=>'','editable'=>'no','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'total_cash_in_hand'=>array('field'=>'total_cash_in_hand','header'=>'Total Cash In Hand','unit'=>'','editable'=>'no','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'station_expenses'=>array('field'=>'station_expenses','header'=>'Station Expenses','unit'=>'','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'approved_salaries_withdrawals'=>array('field'=>'approved_salaries_withdrawals','header'=>'Approved Salaries & Withdrawals','unit'=>'','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'others'=>array('field'=>'others','header'=>'Others','unit'=>'','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'total_cash_withdrawn'=>array('field'=>'total_cash_withdrawn','header'=>'Total Cash Withdrawn','unit'=>'','editable'=>'no','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'payment_to_omc'=>array('field'=>'payment_to_omc','header'=>'Payment To OMC','unit'=>'','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'cash_cf_to_next_day'=>array('field'=>'cash_cf_to_next_day','header'=>'Cash C/F To Next Day','unit'=>'','editable'=>'no','field_type'=>'text','validate'=>'required,numeric','format'=>'money')
        );
        return $table_setup;
    }


    function getFullHeader(){
        $table_setup = array(
            'cash_bf_prev_day'=>array('field'=>'cash_bf_prev_day','header'=>'Cash B/F Previous Day','unit'=>'','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'day_cash_sales'=>array('field'=>'day_cash_sales','header'=>'Day Cash Sales','unit'=>'','editable'=>'no','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'collection_on_credit_sales'=>array('field'=>'collection_on_credit_sales','header'=>'Collection On Credit Sales','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'other_collection'=>array('field'=>'other_collection','header'=>'Other Collections','unit'=>'','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'total_cash_received'=>array('field'=>'total_cash_received','header'=>'Total Cash Received','unit'=>'','editable'=>'no','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'total_cash_in_hand'=>array('field'=>'total_cash_in_hand','header'=>'Total Cash In Hand','unit'=>'','editable'=>'no','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'station_expenses'=>array('field'=>'station_expenses','header'=>'Station Expenses','unit'=>'','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'approved_salaries_withdrawals'=>array('field'=>'approved_salaries_withdrawals','header'=>'Approved Salaries & Withdrawals','unit'=>'','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'others'=>array('field'=>'others','header'=>'Others','unit'=>'','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'total_cash_withdrawn'=>array('field'=>'total_cash_withdrawn','header'=>'Total Cash Withdrawn','unit'=>'','editable'=>'no','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'payment_to_omc'=>array('field'=>'payment_to_omc','header'=>'Payment To OMC','unit'=>'','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'cash_cf_to_next_day'=>array('field'=>'cash_cf_to_next_day','header'=>'Cash C/F To Next Day','unit'=>'','editable'=>'no','field_type'=>'text','validate'=>'required,numeric','format'=>'money')
        );
        return $table_setup;
    }


    function widget_cash_credit_summary($comp_id,$omc_id,$date){
        $return_arr = array();
        $OmcSalesSheet = ClassRegistry::init('OmcSalesSheet');
        $res = $OmcSalesSheet->getSheet($comp_id,$omc_id,$date);
        $sheet_id  = 0;
        if($res){
            $sheet_id = $res['OmcSalesSheet']['id'];
        }
        $form_data = $this->find('first',array(
            'conditions'=>array('omc_sales_sheet_id'=>$sheet_id),
            'contain'=>array('OmcSalesSheet')
        ));
        $table_setup = $this->getTableSetup();
        //$id = $form_data['OmcCashCreditSummary']['id'];
        foreach($table_setup as $row){
            $header = $row['header'];
            $field = $row['field'];
            $format = $row['format'];
           // $row_id = $id;
            $cell_value = $field_value = isset($form_data['OmcCashCreditSummary'][$field]) ? $form_data['OmcCashCreditSummary'][$field] :'';
            if(is_numeric($field_value)){
                $format_type = $format;
                $decimal_places = 0;
                if($format == 'float'){
                    $decimal_places = 2;
                    $format_type = 'money';
                }
                if($format_type !=''){
                    $cell_value = $this->formatNumber($cell_value,$format_type,$decimal_places);
                }
            }
            $return_arr[]=array(
                'header'=>$header,
                'value'=>$cell_value
            );
        }
        return $return_arr;
    }
}