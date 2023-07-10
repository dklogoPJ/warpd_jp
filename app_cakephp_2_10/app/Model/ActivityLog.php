<?php
class ActivityLog extends AppModel
{

    function logActivity($data){
        return $this->save($data);
    }

    function getLog($type,$entity_id,$user_id,$activity = null,$group_id = null)
    {
        $conditions = array(
            'ActivityLog.type' => $type,
            'ActivityLog.entity_id' => $entity_id,
            'ActivityLog.user_id' => $user_id
        );
        if($activity != null){
            $conditions['Log.activity'] = $activity;
        }
        if($group_id != null){
            $conditions['Log.user_group_id'] = $group_id;
        }
        return $this->find('all', array('conditions' => $conditions, 'recursive' => -1));
    }

    function getLogTypes(){
        return $this->__get_enum_values('activity');
    }
}