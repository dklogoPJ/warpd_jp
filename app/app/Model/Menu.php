<?php
class Menu extends AppModel
{
    /**
     * associations
     */

    var $hasMany = array(
        'MenuGroup' => array(
            'className' => 'MenuGroup',
            'foreignKey' => 'menu_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        )
    );


    function getMenusToAssign($type,$modules = array()){
        $condition_array = array('Menu.type' =>$type, 'Menu.deleted' => 'n');

        $data = $this->find('all',array(
            'conditions'=>$condition_array,
            'recursive'=>-1
        ));
        $arr = array();
        foreach($data as $d){
            $menu_group = $d['Menu']['menu_group'];
            $menu_action = $d['Menu']['action'];
            $id = $d['Menu']['id'];
            $required = $d['Menu']['required'];
            $parent = $d['Menu']['parent'];
            if(!empty($required)){//Validate for module access if its required
                if(!in_array($required,$modules)){
                    continue;
                }
            }
            if(!empty($menu_action) || $menu_action != null){
                if(isset($arr[$menu_group][$parent])){
                    $arr[$menu_group][$parent]['sub'][]=$d['Menu'];
                }
                else{
                    $arr[$menu_group][$id]=  $d['Menu'];
                }
            }

        }
        return $arr;
    }

    function createMenu($params) {
        $query = $this->find('first',array(
            'conditions'=> array(
                'Menu.id'=> $params['id']
            ),
            'recursive'=>-1
        ));
        if($query) {
            $this->id = $query['Menu']['id'];
        }
        if ($this->save($params)) {
            return $this->id;
        }
        return null;
    }

    function getMenuByUrl($controller, $action) {
        return $this->find('first',array(
            'conditions'=>array(
                'Menu.controller'=> $controller,
                'Menu.action'=> $action
            ),
            'recursive'=>-1
        ));
    }

    function deleteMenu($menu_id, $user_id) {
        $save = $this->updateAll(
            array('deleted' => "'y'"),
            array(
                'Menu.id' => $menu_id,
            )
        );
        //Unbind menu from all menu user groups
        $MenuGroup = ClassRegistry::init('MenuGroup');
        $MenuGroup->deleteMenuGroupsByMenuId($menu_id, $user_id);
        return $save;
    }
}