<?php
class OmcSalesSheet extends AppModel
{

    var $hasMany = array(
        'OmcBulkStockPosition' => array(
            'className' => 'OmcBulkStockPosition',
            'foreignKey' => 'omc_sales_sheet_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'OmcBulkStockCalculation' => array(
            'className' => 'OmcBulkStockCalculation',
            'foreignKey' => 'omc_sales_sheet_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'OmcDailySalesProduct' => array(
            'className' => 'OmcDailySalesProduct',
            'foreignKey' => 'omc_sales_sheet_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'OmcLube' => array(
            'className' => 'OmcLube',
            'foreignKey' => 'omc_sales_sheet_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        )
    );


    var $hasOne = array(
        'OmcCashCreditSummary' => array(
            'className' => 'OmcCashCreditSummary',
            'foreignKey' => 'omc_sales_sheet_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'OmcOperatorsCredit' => array(
            'className' => 'OmcOperatorsCredit',
            'foreignKey' => 'omc_sales_sheet_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'OmcCustomersCredit' => array(
            'className' => 'OmcCustomersCredit',
            'foreignKey' => 'omc_sales_sheet_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        )
    );

    /**
     * associations
     */
    var $belongsTo = array(
        'Omc' => array(
            'className' => 'Omc',
            'foreignKey' => 'omc_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        ),
        'OmcCustomer' => array(
            'className' => 'OmcCustomer',
            'foreignKey' => 'omc_customer_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        )
    );



    function setUpSheet($comp_id =null,$omc_id=null){
        $today = date('Y-m-d');
        $res = $this->find('first',array(
            'conditions'=>array('record_date'=>$today,'omc_id'=>$omc_id,'omc_customer_id'=>$comp_id),
            'recursive'=>-1
        ));
        if($res){
            $sheet_id = $res['OmcSalesSheet']['id'];
        }
        else{
            //Create today sales sheet
            $this->create();
            $this->save(array(
                'record_date'=>$today,
                'omc_id'=>$omc_id,
                'omc_customer_id'=>$comp_id
            ));
            $sheet_id = $this->id;
        }
        return $sheet_id;
    }


    function getSheet($comp_id =null,$omc_id=null,$sheet_date=''){
        return  $this->find('first',array(
            'conditions'=>array(
                'OmcSalesSheet.record_date'=>$sheet_date,
                'OmcSalesSheet.omc_id'=>$omc_id,
                'OmcSalesSheet.omc_customer_id'=>$comp_id
            ),
            'recursive'=>-1
        ));
    }


    function getFormData($form_id,$comp_id =null,$omc_id=null,$sheet_date=''){
        $form_data_record_raw = array();
       /* $form_data_record_raw =  $this->OmcSalesRecord->find('all',array(
            'fields'=>array('OmcSalesRecord.id','OmcSalesRecord.omc_sales_sheet_id','OmcSalesRecord.omc_sales_form_id'),
            'conditions'=>array(
                'OmcSalesRecord.omc_sales_form_id'=>$form_id,
                'OmcSalesSheet.record_date'=>$sheet_date,
                'OmcSalesSheet.omc_id'=>$omc_id,
                'OmcSalesSheet.omc_customer_id'=>$comp_id
            ),
            'contain'=>array(
                'OmcSalesSheet' =>array('fields'=>array('OmcSalesSheet.id','OmcSalesSheet.omc_id','OmcSalesSheet.omc_customer_id','OmcSalesSheet.record_date')),
                'OmcSalesValue'
            )
        ));*/

        $form_data_records = array();
        $sales_sheet_id = 0;
        foreach($form_data_record_raw as $record){
            $sales_sheet_id = $record['OmcSalesSheet']['id'];
            $record_values = array();
            foreach($record['OmcSalesValue'] as $rv){
                $record_values[$rv['omc_sales_form_field_id']] = $rv;
            }
            $form_data_records[$record['OmcSalesRecord']['id']] = array(
                'record_id' =>$record['OmcSalesRecord']['id'],
                'values'=>$record_values
            );
        }

        if($form_data_record_raw){
            return array('sheet'=>$sales_sheet_id,'data'=>$form_data_records);
        }
        else{
            return array('sheet'=>$sales_sheet_id,'data'=>$form_data_records);
        }
    }


    function getExportData($form_id,$comp_id =null,$omc_id=null,$sheet_date=''){
        $conditions = array(
            'OmcSalesSheet.record_date'=>$sheet_date,
            'OmcSalesSheet.omc_id'=>$omc_id,
            'OmcSalesSheet.omc_customer_id'=>$comp_id
        );
        if($form_id > 0){
            $conditions['OmcSalesRecord.omc_sales_form_id']=$form_id;
        }
        $form_data_record_raw =  $this->OmcSalesRecord->find('all',array(
            'fields'=>array('OmcSalesRecord.id','OmcSalesRecord.omc_sales_sheet_id','OmcSalesRecord.omc_sales_form_id'),
            'conditions'=>$conditions,
            'contain'=>array(
                'OmcSalesSheet' =>array('fields'=>array('OmcSalesSheet.id','OmcSalesSheet.record_date')),
                'OmcSalesForm' =>array(
                    'fields'=>array('OmcSalesForm.id','OmcSalesForm.form_name'),
                    'OmcSalesFormField' =>array('fields'=>array('OmcSalesFormField.id','OmcSalesFormField.field_name'))
                ),
                'OmcSalesValue' =>array('fields'=>array('OmcSalesValue.id','OmcSalesValue.value','OmcSalesValue.omc_sales_form_field_id'))
            )
        ));

        $export_data = array();

        if($form_data_record_raw){
            foreach($form_data_record_raw as $f){
                $form_id = $f['OmcSalesForm']['id'];
                if(isset($export_data[$form_id])){
                    //Set Headers and Data
                    $OmcSalesFormField = $f['OmcSalesForm']['OmcSalesFormField'];
                    $row_value = array();
                    foreach($OmcSalesFormField as $field){
                        $val = '';
                        foreach($f['OmcSalesValue'] as $d){
                            if($d['omc_sales_form_field_id'] == $field['id']){
                                $val = $d['value'];
                                break;
                            }
                        }
                        $row_value[] = $val;
                    }
                    $export_data[$form_id]['data'][] = $row_value;
                }
                else{
                    $form_name = $f['OmcSalesForm']['form_name'];
                    //Set Headers and Data
                    $OmcSalesFormField = $f['OmcSalesForm']['OmcSalesFormField'];
                    $header = array();
                    $row_value = array();
                    foreach($OmcSalesFormField as $field){
                        $header[] = $field['field_name'];
                        $val = '';
                        foreach($f['OmcSalesValue'] as $d){
                            if($d['omc_sales_form_field_id'] == $field['id']){
                                $val = $d['value'];
                                break;
                            }
                        }
                        $row_value[] = $val;
                    }

                    $export_data[$form_id] = array(
                        'sheet_name'=>$form_name,
                        'header'=>$header,
                        'data'=>array($row_value)
                    );
                }
            }
        }
        else{
            $export_data = false;
        }

        return $export_data;
    }


    function getStartYear($type='omc',$comp_id = 0){
        if ($type == 'omc') {
            $find_id = 'omc_id';
        }
        elseif ($type == 'omc_customer') {
            $find_id = 'omc_customer_id';
        }
        $conditions = array('OmcSalesSheet.'. $find_id => $comp_id);
        $record_raw =  $this->find('first',array(
            'fields'=>array('MIN(OmcSalesSheet.record_date) as record_date'),
            'conditions'=>$conditions,
            'recursive'=>-1
        ));
        $dt = $record_raw[0]['record_date'];
        if($dt == null){
            //set to default start year preferably 2012
            $return = '2012';
        }
        else{
            $dt_arr = explode('-',$dt);
            $return = $dt_arr[0];
        }
        return $return;
    }

}
