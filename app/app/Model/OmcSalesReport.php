<?php
class OmcSalesReport extends AppModel
{

    var $hasMany = array(
        'OmcSalesReportField' => array(
            'className' => 'OmcSalesReportField',
            'foreignKey' => 'omc_sales_report_id',
            'dependent' => false,
            'conditions' => array('OmcSalesReportField.deleted'=>'n'),
            'fields' => '',
            'order' =>  array('OmcSalesReportField.report_field_order'=>'asc'),
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'OmcSalesReportPrimaryFieldOption' => array(
            'className' => 'OmcSalesReportPrimaryFieldOption',
            'foreignKey' => 'omc_sales_report_id',
            'dependent' => false,
            'conditions' => array('OmcSalesReportPrimaryFieldOption.deleted'=>'n'),
            'fields' => '',
            'order' => array('OmcSalesReportPrimaryFieldOption.report_option_order'=>'asc'),
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'OmcSalesReportCell' => array(
            'className' => 'OmcSalesReportCell',
            'foreignKey' => 'omc_sales_report_id',
            'dependent' => false,
            'conditions' => array(),
            'fields' => '',
            'order' => array(),
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
        ),
        'OmcSalesForm' => array(
            'className' => 'OmcSalesForm',
            'foreignKey' => 'omc_sales_report_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        )
    );

    function getSalesReportOnly($omc_id) {
        return $this->find('all',array(
            'conditions'=>array('OmcSalesReport.omc_id'=>$omc_id,'OmcSalesReport.deleted'=>'n'),
            'recursive'=>-1
        ));
    }

    function getSalesReport($omc_id, $report_key, $render_type = null) {
        $conditions = array('OmcSalesReport.omc_id'=>$omc_id, 'OmcSalesReport.report_key'=>$report_key, 'OmcSalesReport.deleted'=>'n');
        if($render_type != null){
            $conditions['OmcSalesReport.render_type'] = $render_type;
        }
        return $this->find('first',array(
            'conditions'=>$conditions,
            'contain'=>array(
                'OmcSalesReportField',
                'OmcSalesReportPrimaryFieldOption'
            )
        ));
    }

    function getSalesReportByKey($omc_id, $report_key) {
        $conditions = array('OmcSalesReport.omc_id'=>$omc_id, 'OmcSalesReport.report_key'=>$report_key, 'OmcSalesReport.deleted'=>'n');
        return $this->find('first',array(
            'fields'=>array('OmcSalesReport.id','OmcSalesReport.report_name','OmcSalesReport.omc_customer_list'),
            'conditions'=>$conditions,
            'recursive'=> -1
        ));
    }

    function getSalesReportById($id) {
        $conditions = array('OmcSalesReport.id'=>$id, 'OmcSalesReport.deleted'=>'n');
        return $this->find('first',array(
            'conditions'=>$conditions,
            'recursive'=> -1
        ));
    }

    function getSalesReportForSetUp($omc_id, $report_key) {
        $conditions = array('OmcSalesReport.omc_id'=>$omc_id, 'OmcSalesReport.report_key'=>$report_key, 'OmcSalesReport.deleted'=>'n');
        return $this->find('first',array(
            'fields'=>array('OmcSalesReport.id','OmcSalesReport.report_name'),
            'conditions'=>$conditions,
            'contain'=>array(
                'OmcSalesReportField'=>array(
                    'fields' => array(
                        'OmcSalesReportField.id',
                        'OmcSalesReportField.report_field_name'
                    ),
                ),
                'OmcSalesReportPrimaryFieldOption'=>array(
                    'fields' => array(
                        'OmcSalesReportPrimaryFieldOption.id',
                        'OmcSalesReportPrimaryFieldOption.report_option_name'
                    )
                )
            )
        ));
    }


    function deleteReport($report_id,$user_id){
        $save= $this->updateAll(
            array('deleted' => "'y'",'modified_by'=>$user_id),
            array(
                'OmcSalesReport.id' => $report_id,
            )
        );

        return $save;
    }

    function getReportHeaders ($report_id, $filter_ids = array()) {
        $data = $this->getReportForPreview($report_id, $filter_ids);
        if($data) {
            return $data['fields'];
        }
        return array();
    }

    function getReportForPreview($report_id, $filter_ids = array()){
        $fd = $this->find('first',array(
            'conditions'=>array('OmcSalesReport.id'=>$report_id),
            'contain'=>array(
                'OmcSalesReportField'
            )
        ));
        if($fd) {
            $fields = array();
            $fields[]=array(
                'name'=>$fd['OmcSalesReport']['report_primary_field']
            );
            foreach($fd['OmcSalesReportField'] as $field){
                if(count($filter_ids) > 0) {
                    if($field['deleted'] == 'n' && in_array($field['id'], $filter_ids)){
                        $fields[]=array(
                            'name'=>$field['report_field_name']
                        );
                    }
                } else {
                    if($field['deleted'] == 'n'){
                        $fields[]=array(
                            'name'=>$field['report_field_name']
                        );
                    }
                }
            }
            return array(
                'report'=>array(
                    'name'=>$fd['OmcSalesReport']['report_name']
                ),
                'fields'=>$fields
            );
        }
        else{
            return false;
        }

    }

    function getAllSalesReports($omc_id, $render_type = null){
        $conditions = array('OmcSalesReport.omc_id'=>$omc_id,'OmcSalesReport.deleted'=>'n');
        return $this->find('all',array(
            'conditions'=>$conditions,
            'contain'=>array(
                'OmcSalesReportPrimaryFieldOption',
                'OmcSalesReportField'
            ),
            'order' => array("OmcSalesReport.report_order"=>'asc')
        ));
    }

    function getReportName ($report_id = null) {
        $report = $this->getSalesReportById($report_id);
        if($report){
            return $report['OmcSalesReport']['report_name'];
        }
        return false;
    }

    function getReportProperties($report_id) {
        $conditions = array('OmcSalesReport.id'=>$report_id, 'OmcSalesReport.deleted'=>'n');
        return $this->find('first',array(
            'fields'=>array(
                'OmcSalesReport.id',
                'OmcSalesReport.report_name',
                'OmcSalesReport.report_primary_field'
            ),
            'conditions'=>$conditions,
            'contain'=>array(
                'OmcSalesReportField'=>array(
                    'fields' => array(
                        'OmcSalesReportField.id',
                        'OmcSalesReportField.report_field_name',
                        'OmcSalesReportField.report_field_order'
                    ),
                ),
                'OmcSalesReportPrimaryFieldOption'=>array(
                    'fields' => array(
                        'OmcSalesReportPrimaryFieldOption.id',
                        'OmcSalesReportPrimaryFieldOption.report_option_name',
                        'OmcSalesReportPrimaryFieldOption.report_option_order',
                        'OmcSalesReportPrimaryFieldOption.report_is_total',
                        'OmcSalesReportPrimaryFieldOption.report_total_option_list',
                        'OmcSalesReportPrimaryFieldOption.report_total_field_list',
                    )
                ),
                'OmcSalesReportCell'
            )
        ));
    }

    function getReportWithData ($omc_customer_id, $report_id = null, $report_date = '') {
        $report_query = $this->getReportProperties($report_id);
        $report = $report_query['OmcSalesReport'];
        $primary_field_collection = $report_query['OmcSalesReportPrimaryFieldOption'];
        $field_collection = $report_query['OmcSalesReportField'];
        $cell_collection = $report_query['OmcSalesReportCell'];

        //Format collection whiles getting it data from dsrp forms
        $report_collection = array();
        foreach($primary_field_collection as $primary_field) {
            $row_of_fields = array();
            foreach ($field_collection as $field ) {
                $row_of_fields[$field['id']] = $field;
                $cell_data = $this->__get_cell_data($primary_field['id'], $field['id'], $cell_collection);
                $value = '';
                if($cell_data && $cell_data['dsrp_form'] && $cell_data['dsrp_primary_fields'] && $cell_data['dsrp_fields'] && $report_date) {
                    $value = ClassRegistry::init('OmcCustomerDailySale')->calcFormData($omc_customer_id, $cell_data['dsrp_form'], $cell_data['dsrp_primary_fields'], $cell_data['dsrp_fields'], $report_date);
                }
                $row_of_fields[$field['id']]['value'] = $value;
            }
            $report_collection[$primary_field['id']] = $primary_field;
            $report_collection[$primary_field['id']]['fields'] = $row_of_fields;
        }

        //TODO Perform Totals

        //Time to render, add the Primary field name and build the render grid data.
        array_unshift($field_collection , array(
           'id' => '-1',
           'report_field_name' => $report['report_primary_field'],
           'report_field_order' => '0',
           'omc_sales_report_id' => $report['id']
        ));

        $headers = array();
        foreach ($field_collection as $field ) {
            $headers[] = $field['report_field_name'];
        }

        $data_grid = array();
        foreach($primary_field_collection as $primary_field) {
            $row_data = array();
            foreach ($field_collection as $field ) {
                //Primary field cell
                if($field['id'] == '-1') {
                    $row_data[] = $primary_field['report_option_name'];
                }else {
                    $row_data[] = $report_collection[$primary_field['id']]['fields'][$field['id']]['value'];
                }
            }
            $data_grid[] = $row_data;
        }

        return array(
            'headers' =>$headers,
            'fields'=>$data_grid
        );
    }

    function __get_cell_data($primary_field_id, $field_id, $collection){
        $found = false;
        foreach($collection as $item) {
            if($item['omc_sales_report_primary_field_option_id'] == $primary_field_id && $item['omc_sales_report_field_id'] == $field_id){
                $found = $item;
            }
        }
        return $found;
    }

}
