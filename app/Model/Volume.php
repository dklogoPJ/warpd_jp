<?php
class Volume extends AppModel
{
    var $next_vol = 2250,$vol_end=50000;

    function initVolumes(){//Run this function once
        $quit = true;
        $save_arr = array();
        do{
            $vl = $this->_getNextVol();
            if(is_numeric($vl)){
                $save_arr[]= array(
                    'id'=>0,
                    'vol'=>$vl
                );
            }
            else{
                $quit = false;
            }
        }while($quit);

        $len = count($save_arr);
        if($len > 0){
            $res = $this->saveAll($save_arr);
        }
    }

    function _getNextVol(){
        $multiples = 150;
        if(!($this->next_vol > $this->vol_end)){
            if($this->nex_vol == 2250){
                $this->next_vol += $multiples;
                return 2250;
            }
            else{
                $current =  $this->next_vol;
                $this->next_vol += $multiples;
                return $current;
            }
        }
        else{
            return false;
        }
    }

    function getVols(){
        return $this->_getVol('vol');
    }

    function _getVol($col){
        $vl = array();
        $r = $this->find('all',array(
            'fields'=>array($col),
            'conditions'=>array('NOT'=>array($col=>NULL),'deleted'=>'n'),
            'order'=>array($col=>'Asc'),
            'recursive'=>-1
        ));
        /*debug($r);
        exit;*/
        foreach($r as $k=>$data){
            $vl[$data['Volume'][$col]] = $data['Volume'][$col];
        }
        asort($vl);
        return $vl;
    }

    function getVolsList(){
        $vol =  $this->_getVol('vol');
        $volumes  = array();
        foreach($vol as $vl){
            $volumes[] = array(
                'id'=>$vl,
                'name'=>$this->formatNumber($vl,'money',0)
            );
        }
        return $volumes;
    }

}