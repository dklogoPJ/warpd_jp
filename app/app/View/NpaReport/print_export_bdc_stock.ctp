<?php
/**
 * @author Amissah Gideon<kuulmek@yahoo.com>
 * @access public
 * @version 1.0
 */

if($media_type == 'print'){
    echo $this->element('reports/npa/print_bdc_stock');
}
elseif($media_type == 'export'){
    echo $this->element('reports/npa/export_bdc_stock');
}