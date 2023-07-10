<?php
class OmcBulkStockCalculation extends AppModel
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
            $arr = unserialize($data['OmcDsrpDataOption']['bulk_stock_calculation_products']);
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


    function getData($sheet_id){
        $res = $this->find('all',array(
            'conditions'=>array('omc_sales_sheet_id'=>$sheet_id),
            'recursive'=>-1
        ));

        return $res;
    }


    function getTableSetup(){
        $table_setup = array(
            'products'=>array('field'=>'products','header'=>'Products','unit'=>'','editable'=>'no','field_type'=>'','validate'=>'','format'=>''),
            'open_stock'=>array('field'=>'open_stock','header'=>'Open Stock','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'quantity_received'=>array('field'=>'quantity_received','header'=>'Quantity Received','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'stock_in_hand'=>array('field'=>'stock_in_hand','header'=>'Stock In Hand','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'day_sales'=>array('field'=>'day_sales','header'=>'Day Sales','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'closing_stock'=>array('field'=>'closing_stock','header'=>'Closing Stock','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'dipping'=>array('field'=>'dipping','header'=>'Dipping','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'variance'=>array('field'=>'variance','header'=>'Variance','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'comments'=>array('field'=>'comments','header'=>'Comments','unit'=>'','editable'=>'yes','field_type'=>'text','validate'=>'required','format'=>'')
        );
        return $table_setup;
    }

    function getFullHeader(){
        $table_setup = array(
            'products'=>array('field'=>'products','header'=>'Products','unit'=>'','editable'=>'no','field_type'=>'','validate'=>'','format'=>''),
            'open_stock'=>array('field'=>'open_stock','header'=>'Open Stock','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'quantity_received'=>array('field'=>'quantity_received','header'=>'Quantity Received','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'stock_in_hand'=>array('field'=>'stock_in_hand','header'=>'Stock In Hand','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'day_sales'=>array('field'=>'day_sales','header'=>'Day Sales','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'money'),
            'closing_stock'=>array('field'=>'closing_stock','header'=>'Closing Stock','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'dipping'=>array('field'=>'dipping','header'=>'Dipping','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'variance'=>array('field'=>'variance','header'=>'Variance','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'comments'=>array('field'=>'comments','header'=>'Comments','unit'=>'','editable'=>'yes','field_type'=>'text','validate'=>'required','format'=>'')
        );
        return $table_setup;
    }


    function widget_bulk_stock_calc($comp_id,$omc_id,$date){
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
            $product = $row['OmcBulkStockCalculation']['products'];
            $closing_stock = $row['OmcBulkStockCalculation']['closing_stock'];
            $dipping = $row['OmcBulkStockCalculation']['dipping'];
            $return_arr[]=array(
                'products'=>$product,
                'closing_stock'=>$closing_stock,
                'dipping'=>$dipping
            );
        }
        return $return_arr;
    }
}
