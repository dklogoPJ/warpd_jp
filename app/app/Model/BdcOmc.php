<?php
class BdcOmc extends AppModel
{
    /**
     * associations
     *
     * @var array
     */
    var $belongsTo = array(
        'Bdc' => array(
            'className' => 'Bdc',
            'foreignKey' => 'bdc_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        ),
        'Omc' => array(
            'className' => 'Omc',
            'foreignKey' => 'omc_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        )
    );

    function getBdcOmc(){
        return $this->find('all',array(
            'conditions'=>array('deleted'=>'n'),
            'recursive'=>-1
        ));
    }


    function get_bdc_omc_list($comp,$for_orders = ''){
        $bdc_list = array();
        $omc_bdcs = $this->find('all', array(
            'fields' => array('BdcOmc.id', 'BdcOmc.bdc_id'),
            'conditions' => array('BdcOmc.omc_id' => $comp['id']),
            'contain' => array(
                'Bdc' => array(
                    'fields' => array('Bdc.id', 'Bdc.name','Bdc.my_depots','Bdc.my_depots_to_products'),
                    'Package'=>array(
                        'fields' => array('Package.id', 'Package.title'),
                        'PackageType'=>array('fields' => array('PackageType.id', 'PackageType.modules'))
                    )
                )
            )
        ));
        foreach ($omc_bdcs as $item) {
            $bdc_list[] = $item['Bdc'];
            /*if($for_orders == 'crm'){
                $bdc_modules =  explode(',',$item['Bdc']['Package']['PackageType']['modules']);
                $return = in_array('Crm',$bdc_modules);
                if($return){
                    $bdc_list[] = $item['Bdc'];
                }
            }
            else{
                $bdc_list[] = $item['Bdc'];
            }*/
        }
        return $bdc_list;
    }

}