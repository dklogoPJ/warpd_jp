<?php
    $action = $this->params['action'];
?>
<div class="well sidebar-nav">
    <ul class="nav nav-list">
        <li class="nav-header">Administration Menus</li>
        <li class="<?php echo ($action == 'bdc')? 'active': '';?>"><a href="<?php echo $this->Html->url(array('controller' => 'Administration', 'action' => 'bdc')); ?>"><i class="icon-home"></i>Dashboard</a></li>
        <li class="<?php echo ($action == 'create_user')? 'active': '';?>"><a href="<?php echo $this->Html->url(array('controller' => 'Administration', 'action' => 'create_user')); ?>"><i class="icon-user"></i> Create Users</a></li>
        <li class="<?php echo ($action == 'create_omc_account')? 'active': '';?>"><a href="<?php echo $this->Html->url(array('controller' => 'Administration', 'action' => 'create_omc_account')); ?>"><i class="icon-list-alt"></i> Create OMC Accounts</a></li>
        <li class="<?php echo ($action == 'create_bdc_template')? 'active': '';?>"><a href="<?php echo $this->Html->url(array('controller' => 'Administration', 'action' => 'create_bdc_template')); ?>"><i class="icon-book"></i> Create Corporate Template</a></li>

        <li class="nav-header">Setup Menus</li>
        <li class="<?php echo ($action == 'product_type')? 'active': '';?>"><a href="<?php echo $this->Html->url(array('controller' => 'Administration', 'action' => 'product_type')); ?>"><i class="icon-random"></i> Septup Product Type</a></li>
        <li class="<?php echo ($action == 'index')? 'active': '';?>"><a href="#"><i class="icon-file"></i> Create Waybill Number</a></li>
        <li class="<?php echo ($action == 'depot')? 'active': '';?>"><a href="<?php echo $this->Html->url(array('controller' => 'Administration', 'action' => 'depot')); ?>"><i class="icon-home"></i> Create Depot</a></li>
    </ul>
</div>