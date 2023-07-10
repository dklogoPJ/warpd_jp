<?php
class Attachment extends AppModel
{

    function get_attachments($type_id,$type,$comp){
        return $this->find('all',array(
            'conditions'=>array('type_id'=>$type_id,'type'=>$type,'upload_from_id'=>$comp),
            'recursive'=>-1
        ));
    }

}