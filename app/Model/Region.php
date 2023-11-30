<?php
class Region extends AppModel
{
    /**
     * associations
     */
    var $hasMany = array(
        'BdcDistribution' => array(
            'className' => 'BdcDistribution',
            'foreignKey' => 'region_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'OmcBdcDistribution' => array(
            'className' => 'OmcBdcDistribution',
            'foreignKey' => 'region_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'District' => array(
            'className' => 'District',
            'foreignKey' => 'region_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'OmcCustomer' => array(
            'className' => 'OmcCustomer',
            'foreignKey' => 'region_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'DeliveryLocation' => array(
            'className' => 'DeliveryLocation',
            'foreignKey' => 'region_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'OmcCustomerDistribution' => array(
            'className' => 'OmcCustomerDistribution',
            'foreignKey' => 'region_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        )
    );


    function getRegionDistrict(){
        $regions = $this->find('all', array(
            'fields' => array('Region.id', 'Region.name'),
            'conditions' => array('Region.deleted' => 'n'),
            'contain'=>array('District'=>array('fields' => array('District.id', 'District.name'))),
            'recursive' => 1
        ));
        $glbl_region_district = array();
        $regions_lists = array();
        $district_lists = array();
        foreach ($regions as $value) {
            $regions_lists[] = $value['Region'];
            foreach ($value['District'] as $val) {
                $district_lists[] = $val;
                $glbl_region_district[$value['Region']['id']][$val['id']] = $val['name'];
            }
        }

        return array(
            'region'=>$regions_lists,
            'district'=>$district_lists,
            'region_district'=>$glbl_region_district,
        );
    }
}