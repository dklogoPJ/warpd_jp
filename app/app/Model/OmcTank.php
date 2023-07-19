<?php
class OmcTank extends AppModel
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


    function getTanks($omc_id = 0) {
        return $this->find('all',array(
            'conditions'=>array(
                'omc_id' => $omc_id,
                'deleted'=>'n'
            ),
            'recursive'=>-1
        ));
    }



    function _getTank($col){
        $tk = array();
        $r = $this->find('all',array(
            'fields'=>array($col),
            'conditions'=>array('NOT'=>array($col=>NULL),'deleted'=>'n'),
            'order'=>array($col=>'Asc'),
            'recursive'=>-1
        ));
        /*debug($r);
        exit;*/
        foreach($r as $k=>$data){
            $tk[$data['OmcTank'][$col]] = $data['OmcTank'][$col];
        }
        asort($tk);
        return $tk;
    }

    function getTankList(){
        $tks =  $this->_getTank('name');
        $tanks  = array();
        foreach($tks as $tk){
            $tanks[] = array(
                'id'=>$tk,
                'name'=>$tk
            );
        }
        return $tanks;
    }

}