<?php
class OmcCustomerDailySale extends AppModel
{
    /**
     * associations
     */

    var $hasMany = array(
        'OmcCustomerDailySalePrimaryField' => array(
            'className' => 'OmcCustomerDailySalePrimaryField',
            'foreignKey' => 'omc_customer_daily_sale_id',
            'dependent' => true,
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

    function __get_attachments($attachment_type, $attachment_type_id, $company_profile_id) {
        $Attachment = ClassRegistry::init('Attachment');
        $attachments_data = $Attachment->get_attachments($attachment_type_id, $attachment_type, $company_profile_id);
        $result = array();
        $webroot_url = $this->get_webroot_url();
        foreach($attachments_data as $rec){
            $path = $rec['Attachment']['path'];
            $file_name = $rec['Attachment']['file_name'];
            $file_url = $webroot_url.'/'.$path.'/'.$file_name;
            $result[]=array(
                "name"=> $file_name,
                "url"=> $file_url
            );
        }
        return $result;
    }

    function getRecord ($condition) {
        //First get the OmcCustomerDailySale
        $DailySaleQuery = $this->find('first', array(
            'conditions'=> $condition,
            'contain'=>array(
                'OmcSalesForm'=> array(
                    'fields' => array(
                        'OmcSalesForm.id',
                        'OmcSalesForm.form_name',
                        'OmcSalesForm.primary_field',
                        'OmcSalesForm.omc_sales_report_id'
                    )
                )
            ),
        ));

        if (!$DailySaleQuery) {
            return array();
        }

        //Next get sorted  OmcCustomerDailySalePrimaryField
        $DailySalePrimaryFieldsQuery = $this->OmcCustomerDailySalePrimaryField->getByOmcCustomerDailySaleId($DailySaleQuery['OmcCustomerDailySale']['id']);

        //Next get sorted OmcCustomerDailySaleField for each OmcCustomerDailySalePrimaryField.id
        $daily_sale_primary_fields_collection = array();
        $OmcCustomerDailySaleFieldModel = ClassRegistry::init('OmcCustomerDailySaleField');

        foreach ($DailySalePrimaryFieldsQuery as $row) {

            $DailySaleFieldsQuery = $OmcCustomerDailySaleFieldModel->getByOmcCustomerDailySalePrimaryFieldId($row['OmcCustomerDailySalePrimaryField']['id']);

            $daily_sale_field_collection = array();
            foreach ($DailySaleFieldsQuery as $inner_row) {
                $arr =  $inner_row['OmcCustomerDailySaleField'];
                $arr['OmcSalesFormField'] = $inner_row['OmcSalesFormField'];
                //if the field type is a file upload then you may want to include links to the files
                $arr['has_attachments'] = false;
                $arr['attachments'] = array();
                if($arr['OmcSalesFormField']['field_type'] == 'File Upload') {
                    $attchs = $this->__get_attachments($DailySaleQuery['OmcSalesForm']['form_name'], $arr['id'], $DailySaleQuery['OmcCustomerDailySale']['omc_customer_id']);
                    if($attchs) {
                        $arr['has_attachments'] = true;
                        foreach ($attchs as $file) {
                            $arr['attachments'][] = $file;
                        }
                    }
                }
                $daily_sale_field_collection[] = $arr;
            }

            $arr0 =  $row['OmcCustomerDailySalePrimaryField'];
            $arr0['OmcSalesFormPrimaryFieldOption'] = $row['OmcSalesFormPrimaryFieldOption'];
            $arr0['OmcCustomerDailySaleField'] = $daily_sale_field_collection;

            $daily_sale_primary_fields_collection[] = $arr0;

        }

        $DailySaleQuery['OmcCustomerDailySalePrimaryField'] = $daily_sale_primary_fields_collection;

        return $DailySaleQuery;
    }


    function getFormSaleSheetForExport($omc_customer_id, $form_id, $record_date) {

        $processedData = $this->getFormSaleSheetForReport($omc_customer_id, $form_id, $record_date);
        if($processedData) {
            $export_header = array();
            $export_data = array();
            $sheet_name = $processedData['form']['name'];

            foreach($processedData['headers'] as $item) {
                $export_header[] = $item['name'];
            }

            foreach($processedData['fields'] as $row) {
                $row_data = array();
                foreach($row as $column) {
                    $row_data[] = $column['value'];
                }
                $export_data[] = $row_data;
            }

            return array(
                array(
                    'header'=> $export_header,
                    'data'=> $export_data,
                    'sheet_name'=> $sheet_name
                )
            );
        }
        return false;
    }

    function getFormSaleSheetForReport($omc_customer_id, $form_id, $record_date) {
        $condition = array(
            'OmcCustomerDailySale.omc_customer_id'=>$omc_customer_id,
            'OmcCustomerDailySale.omc_sale_form_id'=> $form_id,
            'OmcCustomerDailySale.record_dt LIKE'=> "".$record_date."%",
            'OmcCustomerDailySale.deleted' => "n"
        );
        $query = $this->getRecord($condition);
        if($query) {
            return $this->processSaleSheet($query);
        }
        return false;
    }

    function getFormSaleSheet($omc_id, $omc_customer_id, $form_key, $record_date) {
        $OmcSalesForm = ClassRegistry::init('OmcSalesForm');
        $form = $OmcSalesForm->getSalesFormByKey($omc_id, $form_key);
        if ($form) {
            $condition = array(
                'OmcCustomerDailySale.omc_customer_id'=>$omc_customer_id,
                'OmcCustomerDailySale.omc_sale_form_id'=> $form['OmcSalesForm']['id'],
                'OmcCustomerDailySale.record_dt LIKE'=> "".$record_date."%",
                'OmcCustomerDailySale.deleted' => "n"
            );
            $query = $this->getRecord($condition);
            if($query) {
                return $this->processSaleSheet($query);
            }
            return false;
        }
        return false;
    }

    function getAllFormSalesSheet($omc_id, $omc_customer_id, $record_date) {
        $OmcSalesForm = ClassRegistry::init('OmcSalesForm');
        $forms = $OmcSalesForm->getSalesFormOnly($omc_id);
        $results = array();
        foreach($forms as $form) {
            $condition = array(
                'OmcCustomerDailySale.omc_customer_id'=>$omc_customer_id,
                'OmcCustomerDailySale.omc_sale_form_id'=> $form['OmcSalesForm']['id'],
                'OmcCustomerDailySale.record_dt LIKE'=> "".$record_date."%",
                'OmcCustomerDailySale.deleted' => "n"
            );
            $query = $this->getRecord($condition);
            if($query) {
                $p = $this->processSaleSheet($query);
                $results[$form['OmcSalesForm']['id']] = $p['fields'];
            }
        }
        return $results;
    }

    function calcFormData($omc_customer_id, $form_id, $primary_fields, $fields, $record_date) {
        $result = $this->query("CALL dsrp_get_query({$form_id}, {$omc_customer_id}, '{$record_date}', '{$fields}', '{$primary_fields}');");
        $query_str = $result[0][0]['result_string'];
        $executed_query_data = $this->query($query_str);
        $total = 0;
        foreach ($executed_query_data as $row) {
            $row_total = 0;
            foreach ($row[0] as $field_value) {
                $row_total = $row_total + intval($field_value);
            }
            $total = $total + $row_total;
        }
        return $total;
    }

    function setupFormSaleSheet($omc_id, $omc_customer_id, $form_key, $record_date) {
        $OmcSalesForm = ClassRegistry::init('OmcSalesForm');
        $form = $OmcSalesForm->getSalesFormForSetUp($omc_id, $form_key);

        if($form) {
            $form_id = $form['OmcSalesForm']['id'];

            $condition = array(
                'OmcCustomerDailySale.omc_customer_id'=>$omc_customer_id,
                'OmcCustomerDailySale.omc_sale_form_id'=> $form_id,
                'OmcCustomerDailySale.record_dt LIKE'=> "".$record_date."%",
                'OmcCustomerDailySale.deleted' => "n"
            );
            $sale_sheet_record_raw = $this->getRecord($condition);

            if ($sale_sheet_record_raw) {
                return $this->processSaleSheet($sale_sheet_record_raw);
            } else {
                return $this->processSaleSheet($this->createSaleSheet($omc_customer_id, $form_id, $form, $record_date));
            }
        }

        return false;
    }

    function createSaleSheet($omc_customer_id, $form_id, $form, $record_date) {
        $save = array(
            'OmcCustomerDailySale'=> array(
                'omc_customer_id'=> $omc_customer_id,
                'omc_sale_form_id'=> $form_id,
                'record_dt'=> $record_date
            ),
            'OmcCustomerDailySalePrimaryField' => array()
        );

        foreach($form['OmcSalesFormPrimaryFieldOption'] as $option) {
            $form_fields = array();
            foreach($form['OmcSalesFormField'] as $field) {
                $form_fields[] = array(
                    'omc_sales_form_field_id'=> $field['id'],
                    'value'=> ''
                );
            }
            $save['OmcCustomerDailySalePrimaryField'][] = array(
                'omc_sales_form_primary_field_option_id'=> $option['id'],
                'OmcCustomerDailySaleField'=> $form_fields
            );
        }

        $this->saveAll($save, array('deep' => true));
        $condition = array(
            'OmcCustomerDailySale.id'=> $this->id
        );

        return $this->getRecord($condition);
    }

    function processSaleSheet($params) {
        $formatted_data = array(
            'form'=> array(
                'id'=> $params['OmcSalesForm']['id'],
                'name'=> $params['OmcSalesForm']['form_name'],
                'omc_customer_daily_sales_id'=> $params['OmcCustomerDailySale']['id'],
                'omc_sales_report_id'=> $params['OmcSalesForm']['omc_sales_report_id'],
                'record_dt'=> $params['OmcCustomerDailySale']['record_dt']
            )
        );

        $fields_arr = array();
        $cached_header_ids = array();

        foreach ($params['OmcCustomerDailySalePrimaryField'] as $row) {
            $field_row = array();
            //Adding the primary field first
            $pf_name = str_replace(" ","_",strtolower(trim($row['OmcSalesFormPrimaryFieldOption']['option_name'])));
            $custom_id = $pf_name."_".$row['id'];
            $field_row[$custom_id] = array(
                'id'=> $custom_id,
                'row_id'=> $row['id'],
                'primary_field_option_row_id'=> $row['OmcSalesFormPrimaryFieldOption']['id'],
                'element_column_id'=> 'pf_'.$row['OmcSalesFormPrimaryFieldOption']['id'],
                'name' => $pf_name,
                'value' => $row['OmcSalesFormPrimaryFieldOption']['option_name'],
                'option_link_type' => $row['OmcSalesFormPrimaryFieldOption']['option_link_type'],
                'option_link_id' => $row['OmcSalesFormPrimaryFieldOption']['option_link_id'],
                'is_primary_field' => true,
                'is_editable' => false,
                'options' => array(),
                'is_total_row' => $row['OmcSalesFormPrimaryFieldOption']['is_total'] == 'yes',
                'is_total_options' => array(
                    'total_option_list' => $row['OmcSalesFormPrimaryFieldOption']['total_option_list'],
                    'total_field_list' => $row['OmcSalesFormPrimaryFieldOption']['total_field_list']
                ),
                'has_attachments' => false,
                'attachments' => array()
            );

            foreach ($row['OmcCustomerDailySaleField'] as $inner_row) {
                $pf_inner_name = str_replace(" ","_",strtolower(trim($inner_row['OmcSalesFormField']['field_name'])));
                $field_row[$inner_row['id']] = array(
                    'id'=> $inner_row['id'],
                    'row_id'=> $row['id'],
                    'primary_field_option_row_id'=> $row['OmcSalesFormPrimaryFieldOption']['id'],
                    'element_column_id'=> $inner_row['OmcSalesFormField']['id'],
                    'name' => $pf_inner_name,
                    'value' => $inner_row['value'],
                    'option_link_type' => $row['OmcSalesFormPrimaryFieldOption']['option_link_type'],
                    'option_link_id' => $row['OmcSalesFormPrimaryFieldOption']['option_link_id'],
                    'is_primary_field' => false,
                    'is_editable' => true,
                    'options' => $inner_row['OmcSalesFormField'],
                    'is_total_row' => $row['OmcSalesFormPrimaryFieldOption']['is_total'] == 'yes',
                    'is_total_options' => array(
                        'total_option_list' => $row['OmcSalesFormPrimaryFieldOption']['total_option_list'],
                        'total_field_list' => $row['OmcSalesFormPrimaryFieldOption']['total_field_list']
                    ),
                    'has_attachments' => $inner_row['has_attachments'],
                    'attachments' => $inner_row['attachments']
                );
                $cached_header_ids[] = $inner_row['OmcSalesFormField']['id'];
            }

            $fields_arr[$row['id']] = $field_row;
        }
        $cached_header_ids = array_unique($cached_header_ids);
        $formatted_data['headers'] = $this->OmcSalesForm->getFormHeaders($params['OmcSalesForm']['id'], $cached_header_ids);
        $formatted_data['fields'] = $fields_arr;

        return $formatted_data;
    }


    function deleteFormSaleSheet ($id) {
        return $this->updateAll(
            array('deleted' => "'y'"),
            array(
                'OmcCustomerDailySale.id' => $id,
            )
        );

       /* $query = $this->delete($id, true);
        $this->resetAutoincrement();
        $this->resetAutoincrement($this->OmcCustomerDailySalePrimaryField->table);
        $this->resetAutoincrement(ClassRegistry::init('OmcCustomerDailySaleField')->table);*/
      //  return $query;
    }

}