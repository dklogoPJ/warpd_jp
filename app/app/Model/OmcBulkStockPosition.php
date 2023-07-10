<?php
class OmcBulkStockPosition extends AppModel
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
            $arr = unserialize($data['OmcDsrpDataOption']['bulk_stock_position_products']);
            $save_arr = array();
            foreach($arr as $opt){
                $save_arr[]= array(
                    'omc_sales_sheet_id'=>$sheet_id,
                    'product_stock'=>$opt['value']
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
            'product_stock'=>array('field'=>'product_stock','header'=>'Product Stock','unit'=>'','editable'=>'no','field_type'=>'','validate'=>'','format'=>''),
            'opening_meter'=>array('field'=>'opening_meter','header'=>'Opening Meter','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'closing_meter'=>array('field'=>'closing_meter','header'=>'Closing Meter','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'day_throughout'=>array('field'=>'day_throughout','header'=>'Day Throughout','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'return_to_stock'=>array('field'=>'return_to_stock','header'=>'Return To Stock','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'day_sales'=>array('field'=>'day_sales','header'=>'Day Sales','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number')
        );
        return $table_setup;
    }

    function getFullHeader(){
        $table_setup = array(
            'product_stock'=>array('field'=>'product_stock','header'=>'Product Stock','unit'=>'','editable'=>'no','field_type'=>'','validate'=>'','format'=>''),
            'opening_meter'=>array('field'=>'opening_meter','header'=>'Opening Meter','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'closing_meter'=>array('field'=>'closing_meter','header'=>'Closing Meter','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'day_throughout'=>array('field'=>'day_throughout','header'=>'Day Throughout','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'return_to_stock'=>array('field'=>'return_to_stock','header'=>'Return To Stock','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number'),
            'day_sales'=>array('field'=>'day_sales','header'=>'Day Sales','unit'=>'ltr','editable'=>'yes','field_type'=>'text','validate'=>'required,numeric','format'=>'number')
        );
        return $table_setup;
    }


    function update_stock_level($post,$omc_id,$omc_customer_id,$authUser){
        $id = $post['id'];
        $stock_level_qty = $post['closing_meter'];
        $data = $this->find('first',array(
            'fields'=>array('product_stock'),
            'conditions'=>array('id'=>$id),
            'recursive'=>-1
        ));
        if($data){
            $name = $data['OmcBulkStockPosition']['product_stock'];
            $OmcCustomerTank = ClassRegistry::init('OmcCustomerTank');
            $r = $OmcCustomerTank->find('first',array(
                'fields'=>array('id'),
                'conditions'=>array('name'=>$name,'omc_id' =>$omc_id,'omc_customer_id' =>$omc_customer_id),
                'recursive'=>-1
            ));
            if($r){
                $today = date('Y-m-d');
                $omc_customer_tank_id = $r['OmcCustomerTank']['id'];
                $OmcCustomerStock = ClassRegistry::init('OmcCustomerStock');
                $xy = $OmcCustomerStock->find('first',array(
                    'fields'=>array('id'),
                    'conditions'=>array('omc_customer_tank_id'=>$omc_customer_tank_id,'created LIKE'=>$today.' %'),
                    'recursive'=>-1
                ));
                if($xy){
                    $save_id = $xy['OmcCustomerStock']['id'];
                    $save_me = array(
                        'id'=>$save_id,
                        'omc_customer_tank_id'=>$omc_customer_tank_id,
                        'quantity'=>$stock_level_qty,
                        'modified_by'=>$authUser
                    );
                }
                else{
                    $save_id = 0;
                    $save_me = array(
                        'id'=>$save_id,
                        'omc_customer_tank_id'=>$omc_customer_tank_id,
                        'quantity'=>$stock_level_qty,
                        'created_by'=>$authUser,
                        'modified_by'=>$authUser
                    );
                }
                $OmcCustomerStock->save($save_me);
            }
        }
    }
}
