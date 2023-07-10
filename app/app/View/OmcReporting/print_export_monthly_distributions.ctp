<?php
/**
 * @author Amissah Gideon<kuulmek@yahoo.com>
 * @access public
 * @version 1.0
 */

if($media_type == 'print'){
    echo $this->element('reports/omc/print_monthly_distributions');
}
elseif($media_type == 'export'){
    echo $this->element('reports/omc/export_monthly_distributions');
}