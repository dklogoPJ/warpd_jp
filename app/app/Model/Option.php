<?php
class Option extends AppModel {
  var $name = 'Option';
  var $useTable = false;

  public function getDSRPLinkTypes($company_profile_id = null) {

      return array(
          array('id'=>'', 'name'=>'None', 'data'=>array(), 'columns'=>array(
              array('id'=>'', 'name'=>'None')
          )),
          array('id'=>'products', 'name'=>'Products', 'data'=>ClassRegistry::init('ProductType')->getProductList(), //TODO get AND FILTER the product list for the OMC
              'columns'=>array(
                  array('id'=>'products:price', 'name'=>'Products: price')
              )
          ),
          array('id'=>'lpg_settings', 'name'=>'LPG Settings', 'data'=>ClassRegistry::init('LpgSetting')->getProductList($company_profile_id),
              'columns'=>array(
                  array('id'=>'lpg_settings:unit_volume', 'name'=>'LPG Settings: unit_volume'),
                  array('id'=>'lpg_settings:unit_price', 'name'=>'LPG Settings: unit_price'),
                  array('id'=>'lpg_settings:price_per_kg', 'name'=>'LPG Settings:price_per_kg')
              )
          ),
          array('id'=>'lube_settings', 'name'=>'Lube Settings', 'data'=>ClassRegistry::init('LubeSetting')->getProductList($company_profile_id),
              'columns'=>array(
                  array('id'=>'lube_settings:unit_volume', 'name'=>'Lube Settings: unit_volume'),
                  array('id'=>'lube_settings:total_qty_per_pack', 'name'=>'Lube Settings: total_qty_per_pack'),
                  array('id'=>'lube_settings:pack_volume', 'name'=>'Lube Settings: pack_volume'),
                  array('id'=>'lube_settings:unit_cost_price', 'name'=>'Lube Settings: unit_cost_price'),
                  array('id'=>'lube_settings:unit_selling_price', 'name'=>'Lube Settings: unit_selling_price'),
                  array('id'=>'lube_settings:price_per_ltr', 'name'=>'Lube Settings: price_per_ltr')
              )
          )
      );
  }

}
