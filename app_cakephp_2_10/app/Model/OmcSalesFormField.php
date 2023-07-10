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
        $save= $this->updateAll(
            array('deleted' => "'y'",'modified_by'=>$user_id),
            array(
                'OmcSalesFormField.id' => $form_id,
            )
        );

        return $save;
    }


}
