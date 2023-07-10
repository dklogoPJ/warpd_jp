<?php
class OmcMarginSetting extends AppModel{

    function getSetting($key){
        $conditions = array('OmcMarginSetting.key' => $key,'OmcMarginSetting.deleted' => 'n');
        $pcd = $this->find('first', array(
            'conditions' => $conditions,
            'recursive' => -1
        ));
        if($pcd){
            return $pcd['OmcMarginSetting']['value'];
        }
        else{
            return '';
        }
    }

}
