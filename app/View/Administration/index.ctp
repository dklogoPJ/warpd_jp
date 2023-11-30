<div class="span3">
    <div class="well sidebar-nav">
        <ul class="nav nav-list">
            <li class="nav-header">System Administration Menus</li>
            <li class="active"><a href="<?php echo $this->Html->url(array('controller' => 'Administration', 'action' => 'index')); ?>"><i class="icon-white icon-home"></i> Administrator's Dashboard</a></li>
            <li><a href="<?php echo $this->Html->url(array('controller' => 'Administration', 'action' => 'create_user')); ?>"><i class="icon-user"></i> Create System Users</a></li>
            <li><a href="<?php echo $this->Html->url(array('controller' => 'Administration', 'action' => 'create_omc_account')); ?>"><i class="icon-list-alt"></i> Create OMC Account</a></li>
            <li><a href="<?php echo $this->Html->url(array('controller' => 'Administration', 'action' => 'create_bdc_template')); ?>"><i class="icon-book"></i> Create Corporate Template</a></li>

            <li class="nav-header">System Setup Menus</li>
            <li><a href="#"><i class="icon-random"></i> Septup Product Type</a></li>
            <li><a href="#"><i class="icon-file"></i> Create Waybill Number</a></li>
            <li><a href="#"><i class="icon-home"></i> Create Depot</a></li>
            <li class="divider"></li>
            <li><a href="#"><i class="icon-tasks"></i> Setup Region</a></li>
            <li><a href="#"><i class="icon-list"></i> Setup District</a></li>

        </ul>
    </div>
</div>
<div class="span9">
    <div class="form-actions" style="margin-top: 0px; font-weight: bold">
        SYSTEM ADMINISTRATOR'S DASHBOARD
    </div>
</div>