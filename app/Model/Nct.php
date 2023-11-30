<?php
class Nct extends AppModel
{
    /**
     * associations
     *
     * @var array
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

    function getNcts($omc_id = 0) {
        return $this->find('all',array(
            'conditions'=>array(
                'omc_id' => $omc_id,
                'deleted'=>'n'
            ),
            'recursive'=>-1
        ));
    }



    function _getNct($col){
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
            $nt[$data['Nct'][$col]] = $data['Nct'][$col];
        }
        asort($nt);
        return $nt;
    }

    function getNctList(){
        $nts =  $this->_getNct('name');
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