<?php
class OmcCustomerStock extends AppModel
{
    /**
     * associations
     *
     * @var array
     */

    var $belongsTo = array(
        'OmcCustomerTank' => array(
            'className' => 'OmcCustomerTank',
            'foreignKey' => 'omc_customer_tank_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        )
    );

    function getStocksByTankId($id = null, $recursive = -1)
    {
        $conditions = array(
            'OmcCustomerStock.omc_customer_tank_id' => $id,
        );
        # fetch the specific data from the server and retrun it.
        return $this->find('all', array('conditions' => $conditions, 'recursive' => $recursive));
    }


    function __getStockBoard($company_profile)
    {
        $OmcCustomerTank = ClassRegistry::init('OmcCustomerTank');
        $data = $OmcCustomerTank->getCustomerTanks($company_profile['id']);
        $tank_ids = array();
        foreach($data as $datum){
            $tank_ids[] = $datum['OmcCustomerTank']['id'];
        }
        $tank_ids_str = implode(',',$tank_ids);

        /* $conditions =array('OmcCustomerStock.omc_customer_tank_id' => $tank_ids);

         $last_stock_update = $OmcCustomerStock->find('all', array(
             'fields'=>array('MAX(OmcCustomerStock.id) AS id','OmcCustomerStock.quantity','OmcCustomerStock.created','OmcCustomerTank.name','OmcCustomerTank.type'),
             'conditions' => $conditions,
             'group'=>array('OmcCustomerStock.omc_customer_tank_id'),
             'recursive' => 1
         ));*/

        $last_stock_update = $this->query("SELECT *
            FROM omc_customer_stocks
            INNER JOIN
            (SELECT MAX(omc_customer_stocks.id) as id FROM omc_customer_stocks GROUP BY omc_customer_tank_id) last_updates
            ON last_updates.id = omc_customer_stocks.id
            LEFT JOIN omc_customer_tanks ON omc_customer_stocks.omc_customer_tank_id = omc_customer_tanks.id
            WHERE omc_customer_tank_id IN ($tank_ids_str)");


        if($last_stock_update){
            return $last_stock_update;
        }
        else{
            return false;
        }
    }

}