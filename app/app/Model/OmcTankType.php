<?php
class OmcTankType extends AppModel
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
        )
    );


    function getTankTypes($omc_id = 0) {
        return $this->find('all',array(
            'conditions'=>array(
                'omc_id' => $omc_id,
                'deleted'=>'n'
            ),
            'recursive'=>-1
        ));
    }

}