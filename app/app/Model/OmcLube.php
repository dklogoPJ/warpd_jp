<?php
class OmcLube extends AppModel
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
            $arr = unserialize($data['OmcDsrpDataOption']['lubricants_products']);
            $save_arr = array();
            foreach($arr as $opt){
                $save_arr[]= array(
                    'omc_sales_sheet_id'=>$sheet_id,
                    'lubricant_type'=>$opt['value']
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
            'lubricant_type'=>array('field'=>'lubricant_type','header'=>'Lubricant Type','unit'=>'','editable'=>'no','field_type'=>'','validate'=>'','format'=>''),
            'unit_price'=>array('field'=>'unit_price','header'=>'Unit Price','unit'=>'','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'float'),
            'unit_qty'=>array('field'=>'unit_qty','header'=>'Unit Quantity','unit'=>'','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'liter_qty'=>array('field'=>'liter_qty','header'=>'Liter Quantity','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'open_stock'=>array('field'=>'open_stock','header'=>'Open Stock','unit'=>'','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'quantity_rcd'=>array('field'=>'quantity_rcd','header'=>'Quantity Received','unit'=>'','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'total_at_hand'=>array('field'=>'total_at_hand','header'=>'Total At Hand','unit'=>'','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'total_day_sales_qty'=>array('field'=>'total_day_sales_qty','header'=>'Total Day Sales Qty.','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'total_day_sales_value'=>array('field'=>'total_day_sales_value','header'=>'Total Day Sales Value','unit'=>'','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'bf_prev_day_qty'=>array('field'=>'bf_prev_day_qty','header'=>'B/F Previous Day Qty.','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'bf_prev_day_value'=>array('field'=>'bf_prev_day_value','header'=>'B/F Previous Day Qty.','unit'=>'','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'month_to_date_qty'=>array('field'=>'month_to_date_qty','header'=>'Month To Date Qty.','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'month_to_date_value'=>array('field'=>'month_to_date_value','header'=>'Month To Date Value','unit'=>'','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'closing_stock_qty'=>array('field'=>'closing_stock_qty','header'=>'Closing Stock Qty','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'closing_stock_value'=>array('field'=>'closing_stock_value','header'=>'Closing Stock Value','unit'=>'','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'money')
        );
        return $table_setup;
    }


    function getFullHeader(){
        $table_setup = array(
            'lubricant_type'=>array('field'=>'lubricant_type','header'=>'Lubricant Type','unit'=>'','editable'=>'no','field_type'=>'','validate'=>'','format'=>''),
            //'unit_price'=>array('field'=>'unit_price','header'=>'Unit Price','unit'=>'','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'float'),
            'unit_qty'=>array('field'=>'unit_qty','header'=>'Unit Quantity','unit'=>'','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'liter_qty'=>array('field'=>'liter_qty','header'=>'Liter Quantity','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'open_stock'=>array('field'=>'open_stock','header'=>'Open Stock','unit'=>'','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'quantity_rcd'=>array('field'=>'quantity_rcd','header'=>'Quantity Received','unit'=>'','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'total_at_hand'=>array('field'=>'total_at_hand','header'=>'Total At Hand','unit'=>'','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'total_day_sales_qty'=>array('field'=>'total_day_sales_qty','header'=>'Total Day Sales Qty.','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'total_day_sales_value'=>array('field'=>'total_day_sales_value','header'=>'Total Day Sales Value','unit'=>'','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'bf_prev_day_qty'=>array('field'=>'bf_prev_day_qty','header'=>'B/F Previous Day Qty.','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'bf_prev_day_value'=>array('field'=>'bf_prev_day_value','header'=>'B/F Previous Day Qty.','unit'=>'','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'month_to_date_qty'=>array('field'=>'month_to_date_qty','header'=>'Month To Date Qty.','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'month_to_date_value'=>array('field'=>'month_to_date_value','header'=>'Month To Date Value','unit'=>'','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'closing_stock_qty'=>array('field'=>'closing_stock_qty','header'=>'Closing Stock Qty','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'closing_stock_value'=>array('field'=>'closing_stock_value','header'=>'Closing Stock Value','unit'=>'','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'money')
        );
        return $table_setup;
    }



    function getLubesLiterData(){
        $liter_unit_setup = array(
            'exdrum'=>array('key' => 'exdrum','value' => '208','unit'=>'ltrs'),
            'exkeg'=>array('key' => 'exkeg','value' => '25','unit'=>'ltrs'),
            'gallon4'=>array('key' => 'gallon4','value' => '4','unit'=>'ltrs'),
            'gallon5'=>array('key' => 'gallon5','value' => '5','unit'=>'ltrs'),
            '4lttins'=>array('key' => '4lttins','value' => '4','unit'=>'ltrs'),
            '1lttins'=>array('key' => '1lttins','value' => '1','unit'=>'ltrs'),
            '1/2lttins'=>array('key' => '1/2lttins','value' => '0.5','unit'=>'ltrs'),
            'drum_retail'=>array('key' => 'drum_retail','value' => '1','unit'=>'ltrs'),
            'lpg50kg'=>array('key' => 'lpg50kg','value' => '50','unit'=>'kg'),
            'lpg25kg'=>array('key' => 'lpg25kg','value' => '25','unit'=>'kg'),
            'lpg12.5kg'=>array('key' => 'lpg12.5kg','value' => '12.5','unit'=>'kg'),
            'lpg5kg'=>array('key' => 'lpg5kg','value' => '5','unit'=>'kg'),
            'cooker'=>array('key' => 'cooker','value' => '1','unit'=>''),
            'uniflitt'=>array('key' => 'uniflitt','value' => '1','unit'=>''),
            'tba'=>array('key' => 'tba','value' => '1','unit'=>''),
        );

        return $liter_unit_setup;
    }

}
