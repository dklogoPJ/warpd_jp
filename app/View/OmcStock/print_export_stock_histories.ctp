<?php
/**
 * @author Amissah Gideon<kuulmek@yahoo.com>
 * @access public
 * @version 1.0
 */

if($media_type == 'print'){
    echo $this->element('omc_customer/print_stock_histories');
}
elseif($media_type == 'export'){
    echo $this->element('omc_customer/export_stock_histories');
}