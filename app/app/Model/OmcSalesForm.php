<?php
class OmcSalesForm extends AppModel
{

    var $hasMany = array(
        'OmcSalesFormField' => array(
            'className' => 'OmcSalesFormField',
            'foreignKey' => 'omc_sales_form_id',
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
        'OmcSalesRecord' => array(
            'className' => 'OmcSalesRecord',
            'foreignKey' => 'omc_sales_form_id',
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
        'Omc' => array(
            'className' => 'Omc',
            'foreignKey' => 'omc_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        )
    );

    function getSalesFormOnly($omc_id){
        return $this->find('all',array(
            'conditions'=>array('OmcSalesForm.omc_id'=>$omc_id,'OmcSalesForm.deleted'=>'n'),
            'recursive'=>-1
        ));
    }

    function getAllSalesForms($omc_id,$render_type = null){
        $conditions = array('OmcSalesForm.omc_id'=>$omc_id,'OmcSalesForm.deleted'=>'n');
        if($render_type != null){
            $conditions['OmcSalesForm.render_type'] = $render_type;
        }
        return $this->find('all',array(
            'conditions'=>$conditions,
            'contain'=>array(
                'OmcSalesFormField'
            )
        ));
    }


    function deleteForm($form_id,$user_id){
        $save= $this->updateAll(
            array('deleted' => "'y'",'modified_by'=>$user_id),
            array(
                'OmcSalesForm.id' => $form_id,
            )
        );

        return $save;
    }


    function getFormForPreview($form_id){
        $fd = $this->find('first',array(
            'conditions'=>array('OmcSalesForm.id'=>$form_id),
            'contain'=>array(
                'OmcSalesFormField'
            )
        ));
        if($fd){
            $fields = array();
            foreach($fd['OmcSalesFormField'] as $field){
                if($field['deleted'] == 'n'){
                    $fields[]=array(
                        'name'=>$field['field_name']
                    );
                }
            }
            $arr = array(
                'form'=>array(
                    'name'=>$fd['OmcSalesForm']['form_name']
                ),
                'fields'=>$fields
            );
            return $arr;
        }
        else{
            return false;
        }

    }


    function getPreviousDayData($omc_id,$org_id){
        $sheet_date = date('Y-m-d',strtotime("-1 days"));
        return $this->getAllFormsData($omc_id,$org_id,$sheet_date);
    }

    function getCurrentDayData($omc_id,$org_id){
        $sheet_date = date('Y-m-d');
        return $this->getAllFormsData($omc_id,$org_id,$sheet_date);
    }

    function getAllFormsData($omc_id,$org_id,$sheet_date){
        $sale_forms_data = $this->getAllSalesForms($omc_id);
        $forms_n_fields = array();
        foreach($sale_forms_data as $form_arr){
            $form = $form_arr['OmcSalesForm'];
            $fields = $form_arr['OmcSalesFormField'];
            if(!empty($fields)){
                //group forms and fields
                $fields_arr = array();
                foreach($form_arr['OmcSalesFormField'] as $field){
                    if($field['deleted'] == 'n'){
                        $fields_arr[$field['id']]=array(
                            'id'=>$field['id'],
                            'form_id'=>$field['omc_sales_form_id'],
                            'field_name'=>$field['field_name'],
                        );
                    }
                }
                //Get form Values
                $form_data_records = array();
                $form_data_record_raw = ClassRegistry::init('OmcSalesSheet')->getFormData($form['id'],$org_id,$omc_id,$sheet_date);
                $form_data_records = $form_data_record_raw['data'];
                $forms_n_fields[$form['id']] = array(
                    'id' => $form['id'],
                    'name' => $form['form_name'],
                    'fields'=>$fields_arr,
                    'values'=>$form_data_records
                );
            }
        }

        return $forms_n_fields;
    }



    function initPrePopulateForms($omc_id,$org_id,$populate_date){
        $OmcSalesSheet = ClassRegistry::init('OmcSalesSheet');
        $OmcSalesRecord = ClassRegistry::init('OmcSalesRecord');
        $OmcSalesValue = ClassRegistry::init('OmcSalesValue');

        $sheet = $OmcSalesSheet->getSheet($org_id,$omc_id,$populate_date);
        $sheet_id = 0;
        if($sheet){
            $sheet_id = $sheet['OmcSalesSheet']['id'];
        }
        else{
            $OmcSalesSheet->setUpSheet($org_id,$omc_id);
            $sheet = $OmcSalesSheet->getSheet($org_id,$omc_id,$populate_date);
            $sheet_id = $sheet['OmcSalesSheet']['id'];
        }

        $sale_forms_data = $this->getAllSalesForms($omc_id,'Pre Populate');
        foreach($sale_forms_data as $form_arr){
            $form = $form_arr['OmcSalesForm'];
            $form_id = $form['id'];
            $form_name = $form['form_name'];

            //Do a check and make sure we don't get duplicate
            if($OmcSalesRecord->recordExist($sheet_id,$form_id)){
                //echo "Skip Form $form_name <br />";
                continue;
            }

            $fields = $form_arr['OmcSalesFormField'];
            if(!empty($fields)){
                //Search for the control field
                $control_field = false;
                $other_fields = array();
                foreach($fields as $field){
                    if($field['deleted'] == 'n'){
                        if($field['control_field']){//If this is the control field, save it and break the loop
                            $control_field = $field;
                        }
                        else{
                            $other_fields[] = $field;
                        }
                    }
                }

                if($control_field){
                    $field_type_values = $control_field['field_type_values'];
                    $val_arr = explode(',',$field_type_values);
                    $save_all = array();
                    //For each control field value, create a row record for it with the other fields as empty.
                    foreach($val_arr as $val){
                        $record_id = $OmcSalesRecord->createRecord($sheet_id,$form_id);
                        $save_all[] = array(
                            'omc_sales_record_id'=>$record_id,
                            'omc_sales_form_field_id'=>$control_field['id'],
                            'value'=>$val
                        );
                        foreach($other_fields as $f){
                            $save_all[] = array(
                                'omc_sales_record_id'=>$record_id,
                                'omc_sales_form_field_id'=>$f['id'],
                                'value'=>''
                            );
                        }
                    }

                    $OmcSalesValue->saveAll($save_all);
                }

            }
        }

    }

}
