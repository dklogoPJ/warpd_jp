<?php
class OmcSalesReportPrimaryFieldOption extends AppModel {
    /**
     * associations
     */
    var $belongsTo = array(
        'OmcSalesReport' => array(
            'className' => 'OmcSalesReport',
            'foreignKey' => 'omc_sales_report_id',
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
                'OmcSalesReportPrimaryFieldOption.id' => $option_id,
            )
        );
    }

}
