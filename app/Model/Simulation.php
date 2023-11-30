<?php
class Simulation extends AppModel
{

    function getNextDate(){
        $return = $this->find('first',array(
            'conditions'=>array('id '=>'1'),
            'recursive'=>-1
        ));

        return $return['Simulation']['next_date'];
    }

    function saveNextDate($next_date){
        $this->id = 1;
        $return = $this->save(array(
            'next_date' =>$next_date
        ));
        return $return;
    }

}