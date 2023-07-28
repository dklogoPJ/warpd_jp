<?php
class SalesFormElementAction extends AppModel {

    function getKeyValuePair(){
        $query = $this->find('all',array(
            'conditions'=>array('SalesFormElementAction.deleted'=>'n')
        ));
        if ($query){
            $data = array();
            foreach($query as $row){
                $data[$row['SalesFormElementAction']['action_key']] = $row['SalesFormElementAction']['action_name'];
            }
            return $data;
        }
        else{
            return array();
        }
    }

}
