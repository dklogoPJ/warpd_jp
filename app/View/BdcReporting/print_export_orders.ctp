<?php
/**
 * @author Amissah Gideon<kuulmek@yahoo.com>
 * @access public
 * @version 1.0
 */

if($media_type == 'print'){
    echo $this->element('reports/bdc/print_orders');
}
elseif($media_type == 'export'){
    echo $this->element('reports/bdc/export_orders');
}