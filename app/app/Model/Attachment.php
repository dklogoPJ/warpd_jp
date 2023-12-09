<?php
class Attachment extends AppModel {
    function get_attachments($type_id,$type, $comp){
        return $this->find('all',array(
            'conditions'=>array('type_id'=>$type_id,'type'=>$type,'upload_from_id'=>$comp),
            'recursive'=>-1
        ));
    }

    function get_attachment($id){
        return $this->find('first',array(
            'conditions'=>array('id'=>$id),
            'recursive'=>-1
        ));
    }

    function delete_file ($id) {
        return $this->deleteAll(array('id' => $id), false);
    }

}