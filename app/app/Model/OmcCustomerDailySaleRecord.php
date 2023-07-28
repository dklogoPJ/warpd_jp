<?php
class OmcCustomerDailySaleRecord extends AppModel
{
    /**
     * associations
     */

    var $hasMany = array(
        'OmcCustomerDailySaleRecordPrimaryField' => array(
            'className' => 'OmcCustomerDailySaleRecordPrimaryField',
            'foreignKey' => 'omc_customer_daily_sale_record_id',
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


    var $belongsTo = array(
        'OmcCustomer' => array(
            'className' => 'OmcCustomer',
            'foreignKey' => 'omc_customer_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        ),
        'OmcSalesForm' => array(
            'className' => 'OmcSalesForm',
            'foreignKey' => 'omc_sale_form_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        )
    );

    function getRecord ($condition) {
        return $this->find('first',array(
            'conditions'=> $condition,
            'contain'=>array(
                'OmcSalesForm'=>array('fields' => array(
                    'OmcSalesForm.id',
                    'OmcSalesForm.form_name',
                    'OmcSalesForm.primary_field'
                )
                ),
                'OmcCustomerDailySaleRecordPrimaryField'=> array(
                    'fields' => array(
                        'OmcCustomerDailySaleRecordPrimaryField.id',
                        'OmcCustomerDailySaleRecordPrimaryField.omc_sales_form_primary_field_option_id'
                    ),
                    'OmcSalesFormPrimaryFieldOption'=> array(
                        'fields' => array(
                            'OmcSalesFormPrimaryFieldOption.id',
                            'OmcSalesFormPrimaryFieldOption.option_name'
                        )
                    ),
                    'OmcCustomerDailySaleRecordField'=> array(
                        'fields' => array(
                            'OmcCustomerDailySaleRecordField.id',
                            'OmcCustomerDailySaleRecordField.omc_sales_form_field_id',
                            'OmcCustomerDailySaleRecordField.value'
                        ),
                        'OmcSalesFormField'=> array(
                            'fields' => array(
                                'OmcSalesFormField.id',
                                'OmcSalesFormField.field_name'
                            )
                        )
                    )
                )
            ),
        ));
    }

    function setupFormSaleSheet($omc_id, $omc_customer_id, $form_key) {
        $record_date = date('Y-m-d');
        $OmcSalesForm = ClassRegistry::init('OmcSalesForm');
        $form = $OmcSalesForm->getSalesFormForSetUp($omc_id, $form_key);

        //TODO what happens if $form_key does not exist;

        $form_id = $form['OmcSalesForm']['id'];

        $condition = array(
            'OmcCustomerDailySaleRecord.omc_customer_id'=>$omc_customer_id,
            'OmcCustomerDailySaleRecord.omc_sale_form_id'=> $form_id,
            'OmcCustomerDailySaleRecord.record_dt LIKE'=> "".$record_date."%"
        );
        $sale_sheet_record_raw = $this->getRecord($condition);

        if ($sale_sheet_record_raw) {
            return $this->processSaleSheet($sale_sheet_record_raw);
        } else {
            return $this->processSaleSheet($this->createSaleSheet($omc_customer_id, $form_id, $form, $record_date));
        }
    }

    function createSaleSheet($omc_customer_id, $form_id, $form, $record_date) {
        $save = array(
            'OmcCustomerDailySaleRecord'=> array(
                'omc_customer_id'=> $omc_customer_id,
                'omc_sale_form_id'=> $form_id,
                'record_dt'=> $record_date
            ),
            'OmcCustomerDailySaleRecordPrimaryField' => array()
        );

        foreach($form['OmcSalesFormPrimaryFieldOption'] as $option) {
            $form_fields = array();
            foreach($form['OmcSalesFormField'] as $field) {
                $form_fields[] = array(
                    'omc_sales_form_field_id'=> $field['id'],
                    'value'=> ''
                );
            }
            $save['OmcCustomerDailySaleRecordPrimaryField'][] = array(
                'omc_sales_form_primary_field_option_id'=> $option['id'],
                'OmcCustomerDailySaleRecordField'=> $form_fields
            );
        }

        $this->saveAll($save, array('deep' => true));
        $condition = array(
            'OmcCustomerDailySaleRecord.id'=> $this->id
        );

        return $this->getRecord($condition);
    }

    function processSaleSheet($params) {

        $OmcSalesForm = ClassRegistry::init('OmcSalesForm');

        $formatted_data = array(
            'form'=> array(
                'id'=> $params['OmcSalesForm']['id'],
                'name'=> $params['OmcSalesForm']['form_name'],
                'omc_customer_daily_sales_record_id'=> $params['OmcCustomerDailySaleRecord']['id'],
                'record_dt'=> $params['OmcCustomerDailySaleRecord']['record_dt']
            ),
            'headers' => $OmcSalesForm->getFormHeaders($params['OmcSalesForm']['id'])
        );

        $fields_arr = array();

        foreach ($params['OmcCustomerDailySaleRecordPrimaryField'] as $row) {
            $field_row = array();
            //Adding the primary field first
            $pf_name = str_replace(" ","_",strtolower(trim($row['OmcSalesFormPrimaryFieldOption']['option_name'])));
            $field_row[] = array(
                'id'=> $pf_name."_".$row['id'],
                'name' => $pf_name,
                'value' => $row['OmcSalesFormPrimaryFieldOption']['option_name'],
                'is_primary_field' => true,
                'is_editable' => false,
                'options' => array()
            );

            foreach ($row['OmcCustomerDailySaleRecordField'] as $inner_row) {
                $pf_inner_name = str_replace(" ","_",strtolower(trim($inner_row['OmcSalesFormField']['field_name'])));
                $field_row[] = array(
                    'id'=> $inner_row['id'],
                    'name' => $pf_inner_name,
                    'value' => $inner_row['value'],
                    'is_primary_field' => false,
                    'is_editable' => true,
                    'options' => array(

                    )
                );
            }

            $fields_arr[] = $field_row;
        }

        $formatted_data['fields'] = $fields_arr;

        return $formatted_data;
    }

}