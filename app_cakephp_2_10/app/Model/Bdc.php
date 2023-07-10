<?php
class Bdc extends AppModel
{
    /**
     * associations
     */
    var $hasMany = array(
        'BdcOmc' => array(
            'className' => 'BdcOmc',
            'foreignKey' => 'bdc_id',
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
        'Depot' => array(
            'className' => 'Depot',
            'foreignKey' => 'bdc_id',
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
        'BdcDistribution' => array(
            'className' => 'BdcDistribution',
            'foreignKey' => 'bdc_id',
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
        'OmcUserBdc' => array(
            'className' => 'OmcUserBdc',
            'foreignKey' => 'bdc_id',
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
        'Order' => array(
            'className' => 'Order',
            'foreignKey' => 'bdc_id',
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
        'Waybill' => array(
            'className' => 'Waybill',
            'foreignKey' => 'bdc_id',
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
        'OmcCustomerOrder' => array(
            'className' => 'OmcCustomerOrder',
            'foreignKey' => 'bdc_id',
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
        'BdcInitialStockStartup' => array(
            'className' => 'BdcInitialStockStartup',
            'foreignKey' => 'bdc_id',
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
        'BdcStockUpdate' => array(
            'className' => 'BdcStockUpdate',
            'foreignKey' => 'bdc_id',
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
        'BdcStockHistory' => array(
            'className' => 'BdcStockHistory',
            'foreignKey' => 'bdc_id',
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
        'Group' => array(
            'className' => 'Group',
            'foreignKey' => 'bdc_id',
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
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'bdc_id',
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

    var $hasAndBelongsToMany = array(
        'Module' => array(
            'className' => 'Module',
            'joinTable' => 'bdc_modules',
            'foreignKey' => 'bdc_id',
            'associationForeignKey' => 'module_id',
            'unique' => true,
            'conditions' => '',
            'fields' => '',
            'order' => array('Module.name' => 'ASC'),
            'limit' => '',
            'offset' => '',
            'finderQuery' => '',
            'deleteQuery' => '',
            'insertQuery' => ''
        )
    );


    var $belongsTo = array(
        'Package' => array(
            'className' => 'Package',
            'foreignKey' => 'package_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        ),
        'BdcPackage' => array(
            'className' => 'BdcPackage',
            'foreignKey' => 'bdc_package_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        )
    );


    function getBdcById($id = null)
    {
        return $this->find('first', array('conditions' => array('id' => $id), 'recursive' => -1));
    }

    function getDepotProduct($id = null){
        $fields = array('my_depots','my_products');
        $r =  $this->find('first', array('fields'=>$fields,'conditions' => array('id' => $id), 'recursive' => -1));
        $my_depots = explode(',',$r['Bdc']['my_depots']);
        $my_products = explode(',',$r['Bdc']['my_products']);

        return array(
            'my_depots'=>$my_depots,
            'my_products'=>$my_products
        );
    }

    function getDepotToProduct($id = null){
        $fields = array('my_depots_to_products');
        $r =  $this->find('first', array('fields'=>$fields,'conditions' => array('id' => $id), 'recursive' => -1));
        $fin = array();
        if($r['Bdc']['my_depots_to_products'] != null){
            $depot_to_product_arr = explode('#',$r['Bdc']['my_depots_to_products']);//4|1,2,3#6|4,5
            foreach($depot_to_product_arr as $value){
                $depot_to_product_pair = explode('|',$value);//4|1,2,3
                $depot_id = $depot_to_product_pair[0];//4
                $product_ids = explode(',',$depot_to_product_pair[1]);//1,2,3
                $fin[$depot_id]=$product_ids;
            }
        }

        return $fin;
    }

    function getAllDepotToProduct($bdc_ids = null){
        $conditions = array('deleted' => 'n');
        if($bdc_ids != null){
            $conditions = array('id'=>$bdc_ids,'deleted' => 'n');
        }
        $fields = array('id','my_depots_to_products');
        $all_bdcs =  $this->find('all', array('fields'=>$fields,'conditions' => $conditions, 'recursive' => -1));
        $fin = array();
        foreach($all_bdcs as $d){
            if($d['Bdc']['my_depots_to_products'] != null){
                $depot_to_product_arr = explode('#',$d['Bdc']['my_depots_to_products']);//4|1,2,3#6|4,5
                foreach($depot_to_product_arr as $value){
                    $depot_to_product_pair = explode('|',$value);//4|1,2,3
                    $depot_id = $depot_to_product_pair[0];//4
                    $product_ids = explode(',',$depot_to_product_pair[1]);//1,2,3
                    $fin[$d['Bdc']['id']][$depot_id]=$product_ids;
                }
            }
        }
        return $fin;
    }


    function getBDCs(){
        return $this->find('all',array(
            'conditions'=>array('deleted'=>'n'),
            'recursive'=>-1
        ));
    }

    function getBDCsList(){
        $bdcs = $this->find('all', array(
            'fields' => array('Bdc.id', 'Bdc.name'),
            'conditions' => array('Bdc.deleted' => 'n'),
            'recursive' => -1
        ));
        $bdc_list = array();
        if($bdcs){
            foreach ($bdcs as $item) {
                $bdc_list[] = $item['Bdc'];
            }
        }
        return $bdc_list;
    }

}