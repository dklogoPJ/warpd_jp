<?php
class OmcCustomer extends AppModel
{
    /**
     * associations
     *
     * @var array
     */

    var $hasMany = array(
        'OmcBdcDistribution' => array(
            'className' => 'OmcBdcDistribution',
            'foreignKey' => 'omc_customer_id',
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
            'foreignKey' => 'omc_customer_id',
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
            'foreignKey' => 'omc_customer_id',
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
        'OmcCustomerTank' => array(
            'className' => 'OmcCustomerTank',
            'foreignKey' => 'omc_customer_id',
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
        'OmcCustomerTankMinstocklevel' => array(
            'className' => 'OmcCustomerTankMinstocklevel',
            'foreignKey' => 'omc_customer_id',
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
            'foreignKey' => 'omc_customer_id',
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
            'foreignKey' => 'omc_customer_id',
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
        'TemperatureCompensation' => array(
            'className' => 'TemperatureCompensation',
            'foreignKey' => 'omc_customer_id',
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
        'PumpTankSale' => array(
            'className' => 'PumpTankSale',
            'foreignKey' => 'omc_customer_id',
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
        'NctRecord' => array(
            'className' => 'NctRecord',
            'foreignKey' => 'omc_customer_id',
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
        'CustomerCreditSetting' => array(
            'className' => 'CustomerCreditSetting',
            'foreignKey' => 'omc_customer_id',
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
        'CustomerCredit' => array(
            'className' => 'CustomerCredit',
            'foreignKey' => 'omc_customer_id',
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
        'CustomerCreditPayment' => array(
            'className' => 'CustomerCreditPayment',
            'foreignKey' => 'omc_customer_id',
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
        'LubeSetting' => array(
            'className' => 'LubeSetting',
            'foreignKey' => 'omc_customer_id',
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
        'LpgSetting' => array(
            'className' => 'LpgSetting',
            'foreignKey' => 'omc_customer_id',
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

    var $belongsTo = array(
        'Omc' => array(
            'className' => 'Omc',
            'foreignKey' => 'omc_id',
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
        'District' => array(
            'className' => 'District',
            'foreignKey' => 'district_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        )
    );


    function getCustomerById($Id = null, $recursive = -1)
    {
        $conditions = array(
            'OmcCustomer.id' => $Id,
        );
        # fetch the specific data from the server and return it.
        return $this->find('first', array('conditions' => $conditions, 'recursive' => $recursive));
    }

    function getOmcCustomerProduct($id = null){
        $fields = array('my_products');
        $r =  $this->find('first', array('fields'=>$fields,'conditions' => array('id' => $id), 'recursive' => -1));
        $my_products = explode(',',$r['OmcCustomer']['my_products']);
        return array(
            'my_products'=>$my_products
        );
    }

    function getCustomerByOmcId($omc_id = null, $recursive = -1) {
        $conditions = array(
            'OmcCustomer.omc_id' => $omc_id,
        );
        # fetch the specific data from the server and retrun it.
        return $this->find('all', array('conditions' => $conditions, 'recursive' => $recursive));
    }

    function getOmcCustomerIds($omc_id = null) {
        $conditions = array('OmcCustomer.omc_id' => $omc_id,);
        # fetch the specific data from the server and retrun it.
        $query = $this->find('all', array('conditions' => $conditions, 'recursive' => -1));
        $ids = array();
        foreach ($query as $row) {
            $ids[]= $row['OmcCustomer']['id'];
        }
        return $ids;
    }

    function getCustomerList($recursive = -1){
        $cus_list = $this->find('list', array(
            'fields' => array('id', 'name'),
            $recursive
        ));
        return $cus_list;
    }


    function _getOmcCustomer($col){
        $nt = array();
        $r = $this->find('all',array(
            'fields'=>array($col),
            'conditions'=>array('NOT'=>array($col=>NULL),'deleted'=>'n'),
            'order'=>array($col=>'Asc'),
            'recursive'=>-1
        ));
        /*debug($r);
        exit;*/
        foreach($r as $k=>$data){
            $nt[$data['OmcCustomer'][$col]] = $data['OmcCustomer'][$col];
        }
        asort($nt);
        return $nt;
    }

    function getOmcCustomerList(){
        $nts =  $this->_getOmcCustomer('name');
        $ncts  = array();
        foreach($nts as $nt){
            $ncts[] = array(
                'id'=>$nt,
                'name'=>$nt
            );
        }
        return $ncts;
    }

}