<?php
class MenuGroup extends AppModel
{
    /**
     * associations
     */

    var $belongsTo = array(
        'Menu' => array(
            'className' => 'Menu',
            'foreignKey' => 'menu_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        ),
        'Group' => array(
            'className' => 'Group',
            'foreignKey' => 'group_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        )
    );

    function getGroupMenusIds($type,$group_id,$comp_id){
        if ($type == 'bdc') {
            $find_id = 'bdc_id';
        }
        elseif ($type == 'omc') {
            $find_id = 'omc_id';
        }
        elseif ($type == 'omc_customer') {
            $find_id = 'omc_customer_id';
        }
        elseif ($type == 'org') {
            $find_id = 'org_id';
        }
        elseif ($type == 'ceps_depot' || $type == 'ceps_central') {
            $find_id = 'cep_id';
        }
        elseif ($type == 'depot') {
            $find_id = 'depot_id';
        }

        $condition_array = array('MenuGroup.'. $find_id => $comp_id,'MenuGroup.group_id' => $group_id, 'MenuGroup.deleted' => 'n');

        $data = $this->find('all',array(
            'conditions'=>$condition_array,
            'recursive'=>-1
        ));
        $arr = array();
        foreach($data as $d){
            $menu_id = $d['MenuGroup']['menu_id'];
            $arr[$menu_id]=  $d['MenuGroup'];
        }
        return $arr;
    }


    function getOmcCustomerGroupMenusIds($type,$group_id,$comp_id){
        if ($type == 'bdc') {
            $find_id = 'bdc_id';
        }
        elseif ($type == 'omc') {
            $find_id = 'omc_id';
        }
        elseif ($type == 'omc_customer') {
            $find_id = 'omc_customer_id';
        }
        elseif ($type == 'org') {
            $find_id = 'org_id';
        }
        elseif ($type == 'ceps_depot' || $type == 'ceps_central') {
            $find_id = 'cep_id';
        }
        elseif ($type == 'depot') {
            $find_id = 'depot_id';
        }

        $condition_array = array('MenuGroup.'. $find_id => $comp_id,'MenuGroup.group_id' => $group_id);

        $data = $this->find('all',array(
            'conditions'=>$condition_array,
            'recursive'=>-1
        ));
        $arr = array();
        foreach($data as $d){
            $menu_id = $d['MenuGroup']['menu_id'];
            $arr[$menu_id]=  $d['MenuGroup'];
        }
        return $arr;
    }

    function getGroupMenus($type,$group_id,$comp_id){
        if ($type == 'bdc') {
            $find_id = 'bdc_id';
        }
        elseif ($type == 'omc') {
            $find_id = 'omc_id';
        }
        elseif ($type == 'omc_customer') {
            $find_id = 'omc_customer_id';
        }
        elseif ($type == 'org') {
            $find_id = 'org_id';
        }
        elseif ($type == 'ceps_depot' || $type == 'ceps_central') {
            $find_id = 'cep_id';
        }
        elseif ($type == 'depot') {
            $find_id = 'depot_id';
        }

        $condition_array = array('MenuGroup.'. $find_id => $comp_id,'MenuGroup.group_id' => $group_id , 'MenuGroup.deleted' => 'n');

        $data = $this->find('all',array(
            'conditions'=>$condition_array,
            'contain'=>array('Menu'),
            'order'=>array('Menu.order'=>'ASC'),
            'recursive'=>1
        ));
        $arr = array();
        foreach($data as $d){
            $parent = $d['Menu']['parent'];
            $menu_id = $d['Menu']['id'];
            if($parent > 0){
                $modObj = ClassRegistry::init('Menu');
                $parent_data = $modObj->find('first',array(
                    'conditions'=>array('id'=>$parent),
                    'recursive'=>-1
                ));
                $parent_id = $parent_data['Menu']['id'];
                $arr[$parent_id]['name'] = $parent_data['Menu']['menu_name'];
                $arr[$parent_id]['controller'] = $parent_data['Menu']['controller'];
                $arr[$parent_id]['sub'][$menu_id]=  array(
                    'name'=>$d['Menu']['menu_name'],
                    'controller'=>$d['Menu']['controller'],
                    'action'=>$d['Menu']['action'],
                    'icon'=>$d['Menu']['icon'],
                    'permission'=>$d['MenuGroup']['permission']
                );
            }
            else{
                $arr[$menu_id]=  array(
                    'name'=>$d['Menu']['menu_name'],
                    'controller'=>$d['Menu']['controller'],
                    'action'=>$d['Menu']['action'],
                    'icon'=>$d['Menu']['icon'],
                    'permission'=>$d['MenuGroup']['permission']
                );
            }

        }

        //debug($arr);

        return $arr;
    }
}