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
}
