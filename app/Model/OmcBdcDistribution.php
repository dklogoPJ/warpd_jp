<?php
class OmcBdcDistribution extends AppModel
{
    /**
     * associations
     */

    var $hasMany = array(
        'OmcCustomerDistribution' => array(
            'className' => 'OmcCustomerDistribution',
            'foreignKey' => 'omc_bdc_distribution_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
     ));

    var $belongsTo = array(
        'BdcDistribution' => array(
            'className' => 'BdcDistribution',
            'foreignKey' => 'bdc_distribution_id',
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
        'Region' => array(
            'className' => 'Region',
            'foreignKey' => 'region_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        ),
        'DeliveryLocation' => array(
            'className' => 'DeliveryLocation',
            'foreignKey' => 'delivery_location_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        )
    );


    function addDistribution($order=array()){
        //Flow to omc distribution
        $omc_distribution = $this->find('first', array(
            'fields' => array('OmcBdcDistribution.id'),
            'conditions' => array('OmcBdcDistribution.bdc_distribution_id' => $order['bdc_distribution_id']),
            'recursive' => -1
        ));

        if(!$omc_distribution){

            $omc_save = array(
                'OmcBdcDistribution'=>array(
                    'bdc_distribution_id'=>$order['bdc_distribution_id'],
                    'omc_customer_id'=>$order['omc_customer_id'],
                    'quantity'=>$order['loaded_quantity'],
                    'transporter'=>$order['transporter']
                    //'created_by' => $authUser['id']
                )
            );

            if( $this->save($omc_save)){
                return true;
            }
            else{
                return false;
            }
        }
        else{
            return false;
        }
    }

}