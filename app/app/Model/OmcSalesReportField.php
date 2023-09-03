<?php
class OmcSalesReportField extends AppModel
{

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
        )
    );

    function deleteField($report_id, $user_id){
        return $this->updateAll(
            array('deleted' => "'y'", 'modified_by'=>$user_id),
            array(
                'OmcSalesReportField.id' => $report_id,
            )
        );
    }

}
