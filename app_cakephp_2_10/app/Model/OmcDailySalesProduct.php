<?php
class OmcDailySalesProduct extends AppModel
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

    function setUp($sheet_id,$omc_id){
        $res = $this->find('first',array(
            'conditions'=>array('omc_sales_sheet_id'=>$sheet_id),
            'recursive'=>-1
        ));
        if($res){ // Setup Done already for today

        }
        else{
            $modObj = ClassRegistry::init('OmcDsrpDataOption');
            $data = $modObj->find('first',array(
                'conditions'=>array('omc_id'=>$omc_id),
                'recursive'=>-1
            ));
            $arr = unserialize($data['OmcDsrpDataOption']['daily_sales_products']);
            $save_arr = array();
            foreach($arr as $opt){
                $save_arr[]= array(
                    'omc_sales_sheet_id'=>$sheet_id,
                    'products'=>$opt['value']
                );
            }
            $this->saveAll($save_arr);
        }
        $res = $this->find('all',array(
            'conditions'=>array('omc_sales_sheet_id'=>$sheet_id),
            'contain'=>array('OmcSalesSheet')
        ));

        return $res;
    }


    function getPreviousDayData($comp_id =null,$omc_id=null){
        $OmcSalesSheet = ClassRegistry::init('OmcSalesSheet');
        $sheet_date = date('Y-m-d',strtotime("-1 days"));
        $sheet_data = $OmcSalesSheet->getSheet($comp_id,$omc_id,$sheet_date);
        $sheet_id = isset($sheet_data['OmcSalesSheet'])?$sheet_data['OmcSalesSheet']['id']:0;
        $res = $this->getData($sheet_id);
        if($res){
            return $res;
        }
        else{
            return array();
        }
    }


    function getData($sheet_id){
        $res = $this->find('all',array(
            'conditions'=>array('omc_sales_sheet_id'=>$sheet_id),
            'recursive'=>-1
        ));

        return $res;
    }


    function sumField($sheet_id,$field){
        $res = $this->find('all',array(
            'fields'=>array('SUM('.$field.') AS total'),
            'conditions'=>array('omc_sales_sheet_id'=>$sheet_id),
            'group'=>array('omc_sales_sheet_id'),
            'recursive'=>-1
        ));
        if($res){
            $total = $res[0][0]['total'];
            return $total;
        }
        else{
            return 0;
        }
    }


    function getTableSetup(){
        $table_setup = array(
            'products'=>array('field'=>'products','header'=>'Products','unit'=>'','editable'=>'no','field_type'=>'','validate'=>'','format'=>''),
            'unit_price'=>array('field'=>'unit_price','header'=>'Unit Price','unit'=>'','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'float'),
            'cash_day_sales_qty'=>array('field'=>'cash_day_sales_qty','header'=>'Cash Day Sales Qty.','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'cash_day_sales_value'=>array('field'=>'cash_day_sales_value','header'=>'Cash Day Sales Value','unit'=>'','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'cash_previous_day_sales_qty'=>array('field'=>'cash_previous_day_sales_qty','header'=>'Cash Previous Day Sales Qty.','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'cash_previous_day_sales_value'=>array('field'=>'cash_previous_day_sales_value','header'=>'Cash Previous Day Sales Value','unit'=>'','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'cash_month_to_date_qty'=>array('field'=>'cash_month_to_date_qty','header'=>'Cash Month To Date Qty.','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'cash_month_to_date_value'=>array('field'=>'cash_month_to_date_value','header'=>'Cash Month To Date Value','unit'=>'','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'dealer_credit_day_sales_qty'=>array('field'=>'dealer_credit_day_sales_qty','header'=>'Dealer Credit Day Sales Qty.','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'dealer_credit_day_sales_value'=>array('field'=>'dealer_credit_day_sales_value','header'=>'Dealer Credit Day Sales Value','unit'=>'','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'dealer_credit_previous_day_sales_qty'=>array('field'=>'dealer_credit_previous_day_sales_qty','header'=>'Dealer Credit Previous Day Sales Qty.','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'dealer_credit_previous_day_sales_value'=>array('field'=>'dealer_credit_previous_day_sales_value','header'=>'Dealer Credit Previous Day Sales Value','unit'=>'','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'dealer_credit_month_to_date_qty'=>array('field'=>'dealer_credit_month_to_date_qty','header'=>'Dealer Credit Month To Date Qty','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'dealer_credit_month_to_date_value'=>array('field'=>'dealer_credit_month_to_date_value','header'=>'Dealer Credit Month To Date Value','unit'=>'','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'customers_day_sales_qty'=>array('field'=>'customers_day_sales_qty','header'=>'Company Customers Day Sales Qty.','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'customers_day_sales_value'=>array('field'=>'customers_day_sales_value','header'=>'Company Customers Day Sales Value','unit'=>'','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'customers_previous_day_sales_qty'=>array('field'=>'customers_previous_day_sales_qty','header'=>'Company Customers Previous Day Sales Qty.','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'customers_previous_day_sales_value'=>array('field'=>'customers_previous_day_sales_value','header'=>'Company Customers Previous Day Sales Value','unit'=>'','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'customers_month_to_date_qty'=>array('field'=>'customers_month_to_date_qty','header'=>'Company Customers Month To Date Qty.','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'customers_month_to_date_value'=>array('field'=>'customers_month_to_date_value','header'=>'Company Customers Month To Date Value','unit'=>'','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'money')
        );
        return $table_setup;
    }


    function getTotalTableSetup(){
        $table_setup = array(
            'products'=>array('field'=>'products','header'=>'Products','unit'=>'','editable'=>'no','field_type'=>'','validate'=>'','format'=>''),
           // 'unit_price'=>array('field'=>'unit_price','header'=>'Unit Price','unit'=>'','editable'=>'no','field_type'=>'text','validate'=>'required,numeric','format'=>'float'),
            'total_day_sales_qty'=>array('field'=>'total_day_sales_qty','header'=>'Total Day Sales Qty','unit'=>'ltr','editable'=>'no','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'total_day_sales_value'=>array('field'=>'total_day_sales_value','header'=>'Total Day Sales Value','unit'=>'','editable'=>'no','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'total_previous_day_sales_qty'=>array('field'=>'total_previous_day_sales_qty','header'=>'Total Previous Day Sales Qty.','unit'=>'ltr','editable'=>'no','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'total_previous_day_sales_value'=>array('field'=>'total_previous_day_sales_value','header'=>'Total Previous Day Sales Value','unit'=>'','editable'=>'no','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'total_month_to_date_qty'=>array('field'=>'total_month_to_date_qty','header'=>'Total Month To Date Qty.','unit'=>'ltr','editable'=>'no','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'total_month_to_date_value'=>array('field'=>'total_month_to_date_value','header'=>'Total Month To Date Value','unit'=>'','editable'=>'no','field_type'=>'text','validate'=>'required,numeric','format'=>'money')
        );
        return $table_setup;
    }


    function getFullHeader(){
        $table_setup = array(
            'products'=>array('field'=>'products','header'=>'Products','unit'=>'','editable'=>'no','field_type'=>'','validate'=>'','format'=>''),
            //'unit_price'=>array('field'=>'unit_price','header'=>'Unit Price','unit'=>'','editable'=>'no','field_type'=>'text','validate'=>'required,numeric','format'=>'float'),
            'total_day_sales_qty'=>array('field'=>'total_day_sales_qty','header'=>'Total Day Sales Qty','unit'=>'ltr','editable'=>'no','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'total_day_sales_value'=>array('field'=>'total_day_sales_value','header'=>'Total Day Sales Value','unit'=>'','editable'=>'no','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'total_previous_day_sales_qty'=>array('field'=>'total_previous_day_sales_qty','header'=>'Total Previous Day Sales Qty.','unit'=>'ltr','editable'=>'no','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'total_previous_day_sales_value'=>array('field'=>'total_previous_day_sales_value','header'=>'Total Previous Day Sales Value','unit'=>'','editable'=>'no','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'total_month_to_date_qty'=>array('field'=>'total_month_to_date_qty','header'=>'Total Month To Date Qty.','unit'=>'ltr','editable'=>'no','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'total_month_to_date_value'=>array('field'=>'total_month_to_date_value','header'=>'Total Month To Date Value','unit'=>'','editable'=>'no','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'cash_day_sales_qty'=>array('field'=>'cash_day_sales_qty','header'=>'Cash Day Sales Qty.','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'cash_day_sales_value'=>array('field'=>'cash_day_sales_value','header'=>'Cash Day Sales Value','unit'=>'','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'cash_previous_day_sales_qty'=>array('field'=>'cash_previous_day_sales_qty','header'=>'Cash Previous Day Sales Qty.','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'cash_previous_day_sales_value'=>array('field'=>'cash_previous_day_sales_value','header'=>'Cash Previous Day Sales Value','unit'=>'','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'cash_month_to_date_qty'=>array('field'=>'cash_month_to_date_qty','header'=>'Cash Month To Date Qty.','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'cash_month_to_date_value'=>array('field'=>'cash_month_to_date_value','header'=>'Cash Month To Date Value','unit'=>'','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'dealer_credit_day_sales_qty'=>array('field'=>'dealer_credit_day_sales_qty','header'=>'Dealer Credit Day Sales Qty.','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'dealer_credit_day_sales_value'=>array('field'=>'dealer_credit_day_sales_value','header'=>'Dealer Credit Day Sales Value','unit'=>'','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'dealer_credit_previous_day_sales_qty'=>array('field'=>'dealer_credit_previous_day_sales_qty','header'=>'Dealer Credit Previous Day Sales Qty.','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'dealer_credit_previous_day_sales_value'=>array('field'=>'dealer_credit_previous_day_sales_value','header'=>'Dealer Credit Previous Day Sales Value','unit'=>'','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'dealer_credit_month_to_date_qty'=>array('field'=>'dealer_credit_month_to_date_qty','header'=>'Dealer Credit Month To Date Qty','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'dealer_credit_month_to_date_value'=>array('field'=>'dealer_credit_month_to_date_value','header'=>'Dealer Credit Month To Date Value','unit'=>'','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'customers_day_sales_qty'=>array('field'=>'customers_day_sales_qty','header'=>'Company Customers Day Sales Qty.','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'customers_day_sales_value'=>array('field'=>'customers_day_sales_value','header'=>'Company Customers Day Sales Value','unit'=>'','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'customers_previous_day_sales_qty'=>array('field'=>'customers_previous_day_sales_qty','header'=>'Company Customers Previous Day Sales Qty.','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'customers_previous_day_sales_value'=>array('field'=>'customers_previous_day_sales_value','header'=>'Company Customers Previous Day Sales Value','unit'=>'','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'customers_month_to_date_qty'=>array('field'=>'customers_month_to_date_qty','header'=>'Company Customers Month To Date Qty.','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'customers_month_to_date_value'=>array('field'=>'customers_month_to_date_value','header'=>'Company Customers Month To Date Value','unit'=>'','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'money')
        );
        return $table_setup;
    }


    function widget_daily_sale_product($comp_id,$omc_id,$date){
        $return_arr = array();
        $OmcSalesSheet = ClassRegistry::init('OmcSalesSheet');
        $res = $OmcSalesSheet->getSheet($comp_id,$omc_id,$date);
        $sheet_id  = 0;
        if($res){
            $sheet_id = $res['OmcSalesSheet']['id'];
        }
        $form_data = $this->find('all',array(
            'conditions'=>array('omc_sales_sheet_id'=>$sheet_id),
            'contain'=>array('OmcSalesSheet')
        ));
        foreach($form_data as $row){
            $product = $row['OmcDailySalesProduct']['products'];
            $value = $row['OmcDailySalesProduct']['total_month_to_date_value'];
            $return_arr[]=array(
                'header'=>$product,
                'value'=>$value
            );
        }
        return $return_arr;
    }

}
