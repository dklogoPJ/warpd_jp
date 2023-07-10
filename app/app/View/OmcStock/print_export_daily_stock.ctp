<?php
/**
 * @author Amissah Gideon<kuulmek@yahoo.com>
 * @access public
 * @version 1.0
 */

if($media_type == 'print'){
    echo $this->element('omc_customer/print_daily_stock');
}
elseif($media_type == 'export'){
    echo $this->element('omc_customer/export_daily_stock');
}