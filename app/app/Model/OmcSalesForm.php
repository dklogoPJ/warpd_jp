<?php
class OmcSalesForm extends AppModel
{

    var $hasMany = array(
        'OmcSalesFormField' => array(
            'className' => 'OmcSalesFormField',
            'foreignKey' => 'omc_sales_form_id',
            'dependent' => false,
            'conditions' => array('OmcSalesFormField.deleted'=>'n'),
            'fields' => '',
            'order' =>  array('OmcSalesFormField.field_order'=>'asc'),
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'OmcSalesFormPrimaryFieldOption' => array(
            'className' => 'OmcSalesFormPrimaryFieldOption',
            'foreignKey' => 'omc_sales_form_id',
            'dependent' => false,
            'conditions' => array('OmcSalesFormPrimaryFieldOption.deleted'=>'n'),
            'fields' => '',
            'order' => array('OmcSalesFormPrimaryFieldOption.order'=>'asc'),
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
        )
    );

    function getSalesFormOnly($omc_id) {
        return $this->find('all',array(
            'conditions'=>array('OmcSalesForm.omc_id'=>$omc_id,'OmcSalesForm.deleted'=>'n'),
            'recursive'=>-1
        ));
    }

    function getSalesForm($omc_id, $form_key, $render_type = null) {
        $conditions = array('OmcSalesForm.omc_id'=>$omc_id, 'OmcSalesForm.form_key'=>$form_key, 'OmcSalesForm.deleted'=>'n');
        if($render_type != null){
            $conditions['OmcSalesForm.render_type'] = $render_type;
        }
        return $this->find('first',array(
            'conditions'=>$conditions,
            'contain'=>array(
                'OmcSalesFormField',
                'OmcSalesFormPrimaryFieldOption'
            )
        ));
    }

    function getSalesFormByKey($omc_id, $form_key) {
        $conditions = array('OmcSalesForm.omc_id'=>$omc_id, 'OmcSalesForm.form_key'=>$form_key, 'OmcSalesForm.deleted'=>'n');
        return $this->find('first',array(
            'fields'=>array('OmcSalesForm.id','OmcSalesForm.form_name','OmcSalesForm.omc_customer_list'),
            'conditions'=>$conditions,
            'recursive'=> -1
        ));
    }

    function getSalesFormForSetUp($omc_id, $form_key) {
        $conditions = array('OmcSalesForm.omc_id'=>$omc_id, 'OmcSalesForm.form_key'=>$form_key, 'OmcSalesForm.deleted'=>'n');
        return $this->find('first',array(
            'fields'=>array('OmcSalesForm.id','OmcSalesForm.form_name'),
            'conditions'=>$conditions,
            'contain'=>array(
                'OmcSalesFormField'=>array(
                    'fields' => array(
                        'OmcSalesFormField.id',
                        'OmcSalesFormField.field_name'
                    ),
                ),
                'OmcSalesFormPrimaryFieldOption'=>array(
                    'fields' => array(
                        'OmcSalesFormPrimaryFieldOption.id',
                        'OmcSalesFormPrimaryFieldOption.option_name'
                    )
                )
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

    function getFormHeaders ($form_id, $filter_ids = array()) {
        $data = $this->getFormForPreview($form_id, $filter_ids);
        if($data) {
            return $data['fields'];
        }
        return array();
    }

    function getFormForPreview($form_id, $filter_ids = array()){
        $fd = $this->find('first',array(
            'conditions'=>array('OmcSalesForm.id'=>$form_id),
            'contain'=>array(
                'OmcSalesFormField'
            )
        ));
        if($fd) {
            $fields = array();
            $fields[]=array(
                'name'=>$fd['OmcSalesForm']['primary_field']
            );
            foreach($fd['OmcSalesFormField'] as $field){
                if(count($filter_ids) > 0) {
                    if($field['deleted'] == 'n' && in_array($field['id'], $filter_ids)){
                        $fields[]=array(
                            'name'=>$field['field_name']
                        );
                    }
                } else {
                    if($field['deleted'] == 'n'){
                        $fields[]=array(
                            'name'=>$field['field_name']
                        );
                    }
                }
            }
            return array(
                'form'=>array(
                    'name'=>$fd['OmcSalesForm']['form_name']
                ),
                'fields'=>$fields
            );
        }
        else{
            return false;
        }

    }

    function getAllSalesForms($omc_id,$render_type = null){
        $conditions = array('OmcSalesForm.omc_id'=>$omc_id,'OmcSalesForm.deleted'=>'n');
        if($render_type != null){
            $conditions['OmcSalesForm.render_type'] = $render_type;
        }
        return $this->find('all',array(
            'conditions'=>$conditions,
            'contain'=>array(
                'OmcSalesFormPrimaryFieldOption',
                'OmcSalesFormField'
            ),
            'order' => array("OmcSalesForm.order"=>'asc')
        ));
    }

}
