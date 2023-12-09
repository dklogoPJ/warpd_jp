<?php
class OmcSalesReportCell extends AppModel {
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
        'OmcSalesReportField' => array(
            'className' => 'OmcSalesReportField',
            'foreignKey' => 'omc_sales_report_field_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        ),
        'OmcSalesReportPrimaryFieldOption' => array(
            'className' => 'OmcSalesReportPrimaryFieldOption',
            'foreignKey' => 'omc_sales_report_primary_field_option_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        )
    );

    function getAllReportCells() {
        $data = array();
        $query =  $this->find('all');
        foreach($query as $row){
            $data[$row['OmcSalesReportCell']['omc_sales_report_id']][$row['OmcSalesReportCell']['id']] = $row['OmcSalesReportCell'];
        }
        return $data;
    }

    function getAllReportCellByParams($report_id, $primary_field_id, $field_id) {
        return $this->find('first', array(
            'conditions'=>array(
                'omc_sales_report_id'=> $report_id,
                'omc_sales_report_primary_field_option_id'=> $primary_field_id,
                'omc_sales_report_field_id'=> $field_id,
            ),
            'recursive'=> -1
        ));
    }

    function deleteCells ($report_id, $id, $delete_type = '') {
        $deleted = false;
        if($delete_type == 'primary_field') {
            $deleted = $this->deleteByPrimaryFieldId ($report_id, $id);
        } elseif ($delete_type == 'field') {
            $deleted = $this->deleteByFieldId ($report_id, $id);
        }
        return $deleted;
    }

    function deleteByPrimaryFieldId ($report_id, $id) {
        return $this->deleteAll(array('OmcSalesReportCell.omc_sales_report_id' => $report_id, 'OmcSalesReportCell.omc_sales_report_primary_field_option_id' => $id), false);
    }

    function deleteByFieldId ($report_id, $id) {
        return $this->deleteAll(array('OmcSalesReportCell.omc_sales_report_id' => $report_id, 'OmcSalesReportCell.omc_sales_report_field_id' => $id), false);
    }

}
