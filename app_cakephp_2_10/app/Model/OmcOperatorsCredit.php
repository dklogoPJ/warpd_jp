<?php
class OmcOperatorsCredit extends AppModel
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

    function setUp($sheet_id,$comp_id,$omc_id){
        //Setup is different from the other forms with control fields, here we pre-calculate certain fields values
        $res = $this->find('first',array(
            'conditions'=>array('omc_sales_sheet_id'=>$sheet_id),
            'recursive'=>-1
        ));
        if($res){
            $data = $res['OmcOperatorsCredit'];
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
            $data = $res['OmcOperatorsCredit'];
        }
        //Update Credit B/F Previous Day
        $OmcSalesSheet = ClassRegistry::init('OmcSalesSheet');
        $sheet_date = date('Y-m-d',strtotime("-1 days"));
        $sheet_data = $OmcSalesSheet->getSheet($comp_id,$omc_id,$sheet_date);
        $previous_sheet_id = isset($sheet_data['OmcSalesSheet'])?$sheet_data['OmcSalesSheet']['id']:0;
        $credit_bf_prev_day = $this->getFieldValue($previous_sheet_id,'balance_on_credit_sales');
        if(!$res){
            $credit_bf_prev_day = 0;
        }
        //Update Today's Credit Sales
        $OmcDailySalesProduct = ClassRegistry::init('OmcDailySalesProduct');
        $total_todays_credit_sales = $OmcDailySalesProduct->sumField($sheet_id,'dealer_credit_day_sales_value');

        //Update Cumulative Credit Sales
        $total_cumulative_credit_sales = $total_todays_credit_sales + doubleval($credit_bf_prev_day);

        //Get collection on credit sales
        $OmcCashCreditSummary = ClassRegistry::init('OmcCashCreditSummary');
        $collection_on_credit_sales = $OmcCashCreditSummary->getFieldValue($sheet_id,'collection_on_credit_sales');
        if(!$collection_on_credit_sales){
            $collection_on_credit_sales = 0;
        }
        //Update Balance On Credit Sales
        $balance_on_credit_sales  = $total_cumulative_credit_sales - $collection_on_credit_sales;

        $save_arr = array(
            'id'=>$data['id'],
            'credit_bf_prev_day'=>$credit_bf_prev_day,
            'todays_credit_sales'=>$total_todays_credit_sales,
            'cummulative_credit_sales'=>$total_cumulative_credit_sales,
            'balance_on_credit_sales'=>$balance_on_credit_sales
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
            return $res['OmcOperatorsCredit'][$field];
        }
        else{
            return false;
        }
    }


    function getTableSetup(){
        $table_setup = array(
            'credit_bf_prev_day'=>array('field'=>'credit_bf_prev_day','header'=>'Credit B/F Previous Day','unit'=>'','editable'=>'no','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'todays_credit_sales'=>array('field'=>'todays_credit_sales','header'=>"Today's Credit Sales",'unit'=>'','editable'=>'no','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'cummulative_credit_sales'=>array('field'=>'cummulative_credit_sales','header'=>'Cummulative Credit Sales','unit'=>'','editable'=>'no','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'balance_on_credit_sales'=>array('field'=>'balance_on_credit_sales','header'=>'Balance On Credit Sales','unit'=>'','editable'=>'no','field_type'=>'text','validate'=>'required,numeric','format'=>'money')
        );
        return $table_setup;
    }

    function getFullHeader(){
        $table_setup = array(
            'credit_bf_prev_day'=>array('field'=>'credit_bf_prev_day','header'=>'Credit B/F Previous Day','unit'=>'','editable'=>'no','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'todays_credit_sales'=>array('field'=>'todays_credit_sales','header'=>"Today's Credit Sales",'unit'=>'','editable'=>'no','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'cummulative_credit_sales'=>array('field'=>'cummulative_credit_sales','header'=>'Cummulative Credit Sales','unit'=>'','editable'=>'no','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'balance_on_credit_sales'=>array('field'=>'balance_on_credit_sales','header'=>'Balance On Credit Sales','unit'=>'','editable'=>'no','field_type'=>'text','validate'=>'required,numeric','format'=>'money')
        );
        return $table_setup;
    }
}
