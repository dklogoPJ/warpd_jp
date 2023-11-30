<?php
class OmcMargin extends AppModel
{
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
        'Bdc' => array(
            'className' => 'Bdc',
            'foreignKey' => 'bdc_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        ),
        'ProductType' => array(
            'className' => 'ProductType',
            'foreignKey' => 'product_type_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        ),
        'OmcCustomer' => array(
            'className' => 'OmcCustomer',
            'foreignKey' => 'omc_customer_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        ),
        'Order' => array(
            'className' => 'Order',
            'foreignKey' => 'order_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        )
    );


    function margin_analysis($omc_id,$year,$month,$report_type){
        if($report_type == 'margin_default'){
            return $this->transaction_margin_analysis($omc_id,$year,$month);
        }
        elseif($report_type == 'margin_customer'){
            return $this->customer_margin_analysis($omc_id,$year,$month);
        }
        elseif($report_type == 'margin_customer_segment'){
            return $this->customer_segment_margin_analysis($omc_id,$year,$month);
        }
        elseif($report_type == 'margin_product'){
            return $this->product_margin_analysis($omc_id,$year,$month);
        }
        elseif($report_type == 'margin_bdc'){
            return $this->bdc_margin_analysis($omc_id,$year,$month);
        }
    }

    function transaction_margin_analysis($omc_id,$year,$month){
        $year_month  = $year.'-'.$month;
        $conditions = array('OmcMargin.omc_id' => $omc_id,'OmcMargin.created  LIKE' => $year_month.'%','OmcMargin.deleted' => 'n');

        $raw_data = $this->find('all', array(
            'fields'=>array('OmcMargin.id','OmcMargin.invoice_number','OmcMargin.customer_segment','OmcMargin.bdc_ex_ref','OmcMargin.pump_price' ,'OmcMargin.margin','OmcMargin.created'),
            'conditions' => $conditions,
            'contain'=>array(
                'Bdc'=>array('fields'=>array('Bdc.id','Bdc.name')),
                //'Omc'=>array('fields'=>array('Omc.id','Omc.name')),
                'Order'=>array('fields'=>array('Order.id')),
                'ProductType'=>array('fields'=>array('ProductType.id','ProductType.short_name')),
                'OmcCustomer'=>array('fields'=>array('OmcCustomer.id','OmcCustomer.name'))
            ),
            'order' => array("OmcMargin.id"=>'asc'),
            'recursive' => 1
        ));

        $format_data = array();
        if($raw_data){
            foreach ($raw_data as $data) {
                //Customer, Customer Segment,Product ,Invoicing Number ,BDC, Ex-Ref,Pump Price,Margin
                $format_data[] = array(
                    $this->covertDate($data['OmcMargin']['created'],'mysql_flip'),
                    $data['Order']['id'],
                    $data['OmcCustomer']['name'],
                    $data['OmcMargin']['customer_segment'],
                    $data['ProductType']['short_name'],
                    $data['Bdc']['name'],
                    $data['OmcMargin']['bdc_ex_ref'],
                    $data['OmcMargin']['pump_price'],
                    $data['OmcMargin']['margin']
                );
            }
        }

        return array(
            'table'=>array(
                't_head'=>array('Date','Order No.','Customer', 'Customer Segment','Product','BDC',' Ex-Ref','Pump Price','Margin'),
                't_body'=>$format_data
            )
        );
    }

    function customer_margin_analysis($omc_id,$year,$month){
        $year_month  = $year.'-'.$month;
        $conditions = array('OmcMargin.omc_id' => $omc_id,'OmcMargin.created  LIKE' => $year_month.'%','OmcMargin.deleted' => 'n');

        $raw_data = $this->find('all', array(
            'fields'=>array('OmcMargin.customer_segment','SUM(OmcMargin.bdc_ex_ref) AS bdc_ex_ref_total','SUM(OmcMargin.pump_price) AS pump_price_total' ,'SUM(OmcMargin.margin) AS margin_total'),
            'conditions' => $conditions,
            'contain'=>array(
                'OmcCustomer'=>array('fields'=>array('OmcCustomer.id','OmcCustomer.name'))
            ),
            'group'=>array('OmcMargin.omc_customer_id'),
            'order' => array("OmcMargin.id"=>'asc'),
            'recursive' => 1
        ));
        $format_data = array();
        $pie_data = array();
        if($raw_data){
            foreach ($raw_data as $data) {
                //Customer, Customer Segment, Ex-Ref,Pump Price,Margin
                $margin = number_format(preg_replace('/,/','',$data[0]['margin_total'],2));
                $margin = floatval($margin);

                $format_data[] = array(
                    $data['OmcCustomer']['name'],
                    //$data['OmcMargin']['customer_segment'],
                    //$data[0]['bdc_ex_ref_total'],
                    //$data[0]['pump_price_total'],
                    $margin
                );

                $pie_data[]=array(
                    $data['OmcCustomer']['name'],
                    $margin
                );
            }
            $first_pie_data = $pie_data[0];
            $pie_data[0] = array(
                'name' => $first_pie_data[0],
                'y' => $first_pie_data[1],
                'sliced' => true,
                'selected' => true
            );
        }

        return array(
            'table'=>array(
                //'t_head'=>array('Customer', 'Customer Segment',' Ex-Ref','Pump Price','Margin'),
                't_head'=>array('Customer','Margin Total'),
                't_body'=>$format_data
            ),
            'pie'=>$pie_data
        );
    }

    function customer_segment_margin_analysis($omc_id,$year,$month){
        $year_month  = $year.'-'.$month;
        $conditions = array('OmcMargin.omc_id' => $omc_id,'OmcMargin.created  LIKE' => $year_month.'%','OmcMargin.deleted' => 'n');

        $raw_data = $this->find('all', array(
            'fields'=>array('OmcMargin.customer_segment','SUM(OmcMargin.bdc_ex_ref) AS bdc_ex_ref_total','SUM(OmcMargin.pump_price) AS pump_price_total' ,'SUM(OmcMargin.margin) AS margin_total'),
            'conditions' => $conditions,
            'contain'=>array(
                'OmcCustomer'=>array('fields'=>array('OmcCustomer.id','OmcCustomer.name'))
            ),
            'group'=>array('OmcMargin.customer_segment'),
            'order' => array("OmcMargin.id"=>'asc'),
            'recursive' => 1
        ));
        $format_data = array();
        $pie_data = array();
        if($raw_data){
            foreach ($raw_data as $data) {
                //Customer, Customer Segment, Ex-Ref,Pump Price,Margin
                $margin = number_format(preg_replace('/,/','',$data[0]['margin_total'],2));
                $margin = floatval($margin);
                $format_data[] = array(
                    $data['OmcMargin']['customer_segment'],
                    //$data['OmcMargin']['customer_segment'],
                    //$data[0]['bdc_ex_ref_total'],
                    //$data[0]['pump_price_total'],
                    $margin
                );

                $pie_data[]=array(
                    $data['OmcMargin']['customer_segment'],
                    $margin
                );
            }
            $first_pie_data = $pie_data[0];
            $pie_data[0] = array(
                'name' => $first_pie_data[0],
                'y' => $first_pie_data[1],
                'sliced' => true,
                'selected' => true
            );
        }

        return array(
            'table'=>array(
                //'t_head'=>array('Customer', 'Customer Segment',' Ex-Ref','Pump Price','Margin'),
                't_head'=>array('Customer Segment','Margin Total'),
                't_body'=>$format_data
            ),
            'pie'=>$pie_data
        );
    }

    function product_margin_analysis($omc_id,$year,$month){
        $year_month  = $year.'-'.$month;
        $conditions = array('OmcMargin.omc_id' => $omc_id,'OmcMargin.created  LIKE' => $year_month.'%','OmcMargin.deleted' => 'n');

        $raw_data = $this->find('all', array(
            'fields'=>array('SUM(OmcMargin.bdc_ex_ref) AS bdc_ex_ref_total','SUM(OmcMargin.pump_price) AS pump_price_total' ,'SUM(OmcMargin.margin) AS margin_total'),
            'conditions' => $conditions,
            'contain'=>array(
                'ProductType'=>array('fields'=>array('ProductType.id','ProductType.short_name'))
            ),
            'group'=>array('OmcMargin.product_type_id'),
            'order' => array("OmcMargin.id"=>'asc'),
            'recursive' => 1
        ));
        $format_data = array();
        $pie_data = array();
        if($raw_data){
            foreach ($raw_data as $data) {
                //Customer, Customer Segment, Ex-Ref,Pump Price,Margin
                $margin = number_format(preg_replace('/,/','',$data[0]['margin_total'],2));
                $margin = floatval($margin);
                $format_data[] = array(
                    $data['ProductType']['short_name'],
                    //$data['OmcMargin']['customer_segment'],
                    //$data[0]['bdc_ex_ref_total'],
                    //$data[0]['pump_price_total'],
                    $margin
                );

                $pie_data[]=array(
                    $data['ProductType']['short_name'],
                    $margin
                );
            }
            $first_pie_data = $pie_data[0];
            $pie_data[0] = array(
                'name' => $first_pie_data[0],
                'y' => $first_pie_data[1],
                'sliced' => true,
                'selected' => true
            );
        }

        return array(
            'table'=>array(
                //'t_head'=>array('Customer', 'Customer Segment',' Ex-Ref','Pump Price','Margin'),
                't_head'=>array('Product','Margin Total'),
                't_body'=>$format_data
            ),
            'pie'=>$pie_data
        );
    }

    function bdc_margin_analysis($omc_id,$year,$month){
        $year_month  = $year.'-'.$month;
        $conditions = array('OmcMargin.omc_id' => $omc_id,'OmcMargin.created  LIKE' => $year_month.'%','OmcMargin.deleted' => 'n');

        $raw_data = $this->find('all', array(
            'fields'=>array('SUM(OmcMargin.bdc_ex_ref) AS bdc_ex_ref_total','SUM(OmcMargin.pump_price) AS pump_price_total' ,'SUM(OmcMargin.margin) AS margin_total'),
            'conditions' => $conditions,
            'contain'=>array(
                'Bdc'=>array('fields'=>array('Bdc.id','Bdc.name'))
            ),
            'group'=>array('OmcMargin.bdc_id'),
            'order' => array("OmcMargin.id"=>'asc'),
            'recursive' => 1
        ));
        $format_data = array();
        $pie_data = array();
        if($raw_data){
            foreach ($raw_data as $data) {
                //Customer, Customer Segment, Ex-Ref,Pump Price,Margin
                $margin = number_format(preg_replace('/,/','',$data[0]['margin_total'],2));
                $margin = floatval($margin);
                $format_data[] = array(
                    $data['Bdc']['name'],
                    //$data['OmcMargin']['customer_segment'],
                    //$data[0]['bdc_ex_ref_total'],
                    //$data[0]['pump_price_total'],
                    $margin
                );

                $pie_data[]=array(
                    $data['Bdc']['name'],
                    $margin
                );
            }
            $first_pie_data = $pie_data[0];
            $pie_data[0] = array(
                'name' => $first_pie_data[0],
                'y' => $first_pie_data[1],
                'sliced' => true,
                'selected' => true
            );
        }

        return array(
            'table'=>array(
                //'t_head'=>array('Customer', 'Customer Segment',' Ex-Ref','Pump Price','Margin'),
                't_head'=>array('BDC','Margin Total'),
                't_body'=>$format_data
            ),
            'pie'=>$pie_data
        );
    }
}
