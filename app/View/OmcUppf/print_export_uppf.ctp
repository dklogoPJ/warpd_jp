<?php
/**
 * @author Amissah Gideon<kuulmek@yahoo.com>
 * @access public
 * @version 1.0
 */

if($media_type == 'print'){
    echo $this->element('omc/print_uppf');
}
elseif($media_type == 'export'){
    echo $this->element('omc/export_uppf');
}