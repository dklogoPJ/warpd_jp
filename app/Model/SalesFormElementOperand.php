<?php
class SalesFormElementOperand extends AppModel {

    function getKeyValuePair(){
        $query = $this->find('all',array(
            'conditions'=>array('SalesFormElementOperand.deleted'=>'n')
        ));
        if ($query){
            $data = array();
            foreach($query as $row){
                $data[$row['SalesFormElementOperand']['operand_key']] = $row['SalesFormElementOperand']['operand_name'];
            }
            return $data;
        }
        else{
            return array();
        }
    }

}
