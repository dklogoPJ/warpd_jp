<?php
class OmcSalesFormField extends AppModel
{

    /**
     * associations
     */
    var $belongsTo = array(
        'OmcSalesForm' => array(
            'className' => 'OmcSalesForm',
            'foreignKey' => 'omc_sales_form_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        )
    );

    function deleteField($form_id,$user_id){
        return $this->updateAll(
            array('deleted' => "'y'", 'field_required' => "'No'", 'modified_by'=>$user_id),
            array(
                'OmcSalesFormField.id' => $form_id,
            )
        );
    }


}
