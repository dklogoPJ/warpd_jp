<?php
class SalesFormElementEvent extends AppModel {

    function getKeyValuePair(){
        $query = $this->find('all',array(
            'conditions'=>array('SalesFormElementEvent.deleted'=>'n')
        ));
        if ($query){
            $data = array();
            foreach($query as $row){
                $data[$row['SalesFormElementEvent']['event_key']] = $row['SalesFormElementEvent']['event_name'];
            }
            return $data;
        }
        else{
            return array();
        }
    }

}
