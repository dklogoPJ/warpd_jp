<?php
class LubeSetting extends AppModel
{
    /**
     * associations
     *
     * @var array
     */

    var $belongsTo = array(
        'OmcCustomer' => array(
            'className' => 'OmcCustomer',
            'foreignKey' => 'omc_customer_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        )   
    );

    function getLubes($omc_id = 0) {
        return $this->find('all',array(
            'conditions'=>array(
                'omc_customer_id' => $omc_id,
                'deleted'=>'n'
            ),
            'recursive'=>-1
        ));
    }



    function _getLube($col){
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
            $nt[$data['LubeSetting'][$col]] = $data['LubeSetting'][$col];
        }
        asort($nt);
        return $nt;
    }

    function getLubeList(){
        $nts =  $this->_getLube('name');
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