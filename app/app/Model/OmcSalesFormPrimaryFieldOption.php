<?php
class OmcSalesFormPrimaryFieldOption extends AppModel {
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
        ),
    );

    function deleteOption($option_id, $user_id){
        return $this->updateAll(
            array('deleted' => "'y'",'modified_by'=>$user_id),
            array(
                'OmcSalesFormPrimaryFieldOption.id' => $option_id,
            )
        );
    }

}
