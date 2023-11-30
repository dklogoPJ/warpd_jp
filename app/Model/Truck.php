<?php
class Truck extends AppModel
{
    

    function getTruckById($Id = null, $recursive = -1)
    {
        $conditions = array(
            'Truck.id' => $Id,
        );
        # fetch the specific data from the server and retrun it.
        return $this->find('first', array('conditions' => $conditions, 'recursive' => $recursive));
    }

    function getAllTruck()
    {
        return $this->find('all');
    }

    

    function getTruckList1()
    {
        $listdata = $this->find('list');
        $list = array();
       
        foreach($listdata as $row) {
            $id = $row['id'];
            $name = $row['name'];
            $list[$id] = $name;
            
        }
      
        return $listdata;
    }


    function _gettruck($col){
        $vl = array();
        $r = $this->find('all',array(
            'fields'=>array($col),
            'conditions'=>array('NOT'=>array($col=>NULL)),
            'order'=>array($col=>'Asc'),
            'recursive'=>-1
        ));
        /*debug($r);
        exit;*/
        foreach($r as $k=>$data){
            $vl[$data['Truck'][$col]] = $data['Truck'][$col];
        }
        asort($vl);
        return $vl;
    }

    function getTruckList(){
        $vol =  $this->_gettruck('name');
        $volumes  = array();
        foreach($vol as $vl){
            $volumes[] = array(
                'id'=>$vl,
                'name'=>$vl
            );
        }

      
        return $volumes;
    }

    function getTruckNo(){
        $vol =  $this->_gettruck('truck_no');
        $numbers  = array();
        foreach($vol as $vl){
            $numbers[] = array(
                'id'=>$vl,
                'name'=>$vl
            );
        }
        return $numbers;
    }

}