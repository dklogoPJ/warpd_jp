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


    function getMenusToAssign($type, $modules = array(), $org = null){
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
            $url_type = $d['Menu']['url_type'];
            if(!empty($required)){//Validate for module access if its required
                if(!in_array($required,$modules)){
                    continue;
                }
            }
            //Validate if omc customer has access to proxy url type
            if($type == 'omc_customer' && $url_type == 'proxy') {
                if(!$this->omcCustomerHasProxyPermission($org['omc_id'], $menu_action, $org['id'])) {
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

    function omcCustomerHasProxyPermission ($omc_id, $menu_action, $org_id) {
        $has_perm = $this->hasSalesFormPermission($omc_id, $menu_action, $org_id); //Check for sales form first
        if(!$has_perm) {
            $has_perm = $this->hasSalesReportPermission($omc_id, $menu_action, $org_id); //Then check on sales report
        }
        //For the next proxy check
        /*if(!$has_perm) {
            $has_perm = $this->hasSomeProxyPermission($omc_id, $menu_action, $org_id); //Then check on newly added proxy permission module
        }*/
        return $has_perm;
    }

    /*function hasSomeProxyPermission ($omc_id, $some_key, $org_id) {
        //TODO implement codes like hasSalesFormPermission or hasSalesReportPermission
        return false;
    }*/

    function hasSalesFormPermission ($omc_id, $form_key, $org_id) {
        $OmcSalesForm = ClassRegistry::init('OmcSalesForm');
        $form = $OmcSalesForm->getSalesFormByKey($omc_id, $form_key);
        if($form) {
            $omc_customer_list = $form['OmcSalesForm']['omc_customer_list'];
            $form_url_access_customer_ids = explode(',', $omc_customer_list);
            if($omc_customer_list == null || $omc_customer_list == '' || in_array('all', $form_url_access_customer_ids)) {
                return true;
            } else {
                if(in_array($org_id, $form_url_access_customer_ids)) {
                    return true;
                }
            }
        }
        return false;
    }

    function hasSalesReportPermission ($omc_id, $report_key, $org_id) {
        $OmcSalesReport = ClassRegistry::init('OmcSalesReport');
        $report = $OmcSalesReport->getSalesReportByKey($omc_id, $report_key);
        if($report) {
            $omc_customer_list = $report['OmcSalesReport']['omc_customer_list'];
            $report_url_access_customer_ids = explode(',', $omc_customer_list);
            if($omc_customer_list == null || $omc_customer_list == '' || in_array('all', $report_url_access_customer_ids)) {
                return true;
            } else {
                if(in_array($org_id, $report_url_access_customer_ids)) {
                    return true;
                }
            }
        }
        return false;
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
                'Menu.action'=> $action,
                'Menu.deleted'=> 'n'
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