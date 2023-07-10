<?php
class OmcSalesRecord extends AppModel
{

    var $hasMany = array(
        'OmcSalesValue' => array(
            'className' => 'OmcSalesValue',
            'foreignKey' => 'omc_sales_record_id',
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
    );


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
        ),
        'OmcSalesForm' => array(
            'className' => 'OmcSalesForm',
            'foreignKey' => 'omc_sales_form_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        )
    );


    function createRecord($sheet_id,$form_id){
        $this->create();
        $this->save(array('omc_sales_sheet_id'=>$sheet_id,'omc_sales_form_id'=>$form_id));
        return $this->id;
    }

    function getRecordById($id){
        return $this->find('first',array(
            'fields'=>array('OmcSalesRecord.id','OmcSalesRecord.omc_sales_sheet_id','OmcSalesRecord.omc_sales_form_id'),
            'conditions'=>array(
                'OmcSalesRecord.id'=>$id
            ),
            'contain'=>array(
                'OmcSalesSheet' =>array('fields'=>array('OmcSalesSheet.id','OmcSalesSheet.omc_id','OmcSalesSheet.omc_customer_id','OmcSalesSheet.record_date')),
                'OmcSalesValue'
            )
        ));
    }

    function recordExist($sheet_id,$form_id){
        $res = $this->find('first',array(
            'fields'=>array('OmcSalesRecord.id','OmcSalesRecord.omc_sales_sheet_id','OmcSalesRecord.omc_sales_form_id'),
            'conditions'=>array(
                'OmcSalesRecord.omc_sales_sheet_id'=>$sheet_id,
                'OmcSalesRecord.omc_sales_form_id'=>$form_id
            ),
            'recursive'=>-1
        ));

        if($res){
            return true;
        }
        else{
            return false;
        }
    }

}
