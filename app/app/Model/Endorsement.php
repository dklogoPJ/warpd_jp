<?php
class Endorsement extends AppModel
{

    function endorse($data){
        return $this->save($data);
    }

    function getEndorsement($org_type,$org_id,$document_type,$document_id)
    {
        $conditions = array(
            'Endorsement.type' => $org_type,
            'Endorsement.from' => $org_id,
            'Endorsement.document_type' => $document_type,
            'Endorsement.document_id' => $document_id
        );
        return $this->find('first', array('conditions' => $conditions, 'recursive' => -1));
    }

    function getSignatories($document_type,$document_id)
    {
        $conditions = array(
            'Endorsement.document_type' => $document_type,
            'Endorsement.document_id' => $document_id
        );
        $data =  $this->find('all', array('conditions' => $conditions, 'recursive' => -1));
        $sign = array();
        foreach($data as $key => $value){
            $sign[$value['Endorsement']['type']] = array(
                'from'=>$value['Endorsement']['from'],
                'endorsed_by'=>$value['Endorsement']['endorsed_by_name'],
                'date'=>$value['Endorsement']['created']
            );
        }
        return $sign;
    }

}