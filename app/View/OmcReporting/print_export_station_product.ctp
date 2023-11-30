<?php
/**
 * @author Amissah Gideon<kuulmek@yahoo.com>
 * @access public
 * @version 1.0
 */

if($media_type == 'print'){
    echo $this->element('reports/omc/print_station_product');
}
elseif($media_type == 'export'){
    echo $this->element('reports/omc/export_station_product');
}