<?php
    $action = $this->params['action'];
?>
<div class="well sidebar-nav">
    <ul class="nav nav-list">
        <li class="nav-header">List Menus</li>
        <li class="<?php echo ($action == 'system')? 'active': '';?>"><a href="<?php echo $this->Html->url(array('controller' => 'Administration', 'action' => 'system')); ?>"><i class="icon-home"></i> Dashboard</a></li>
        <li class="<?php echo ($action == 'create_user')? 'active': '';?>"><a href="<?php echo $this->Html->url(array('controller' => 'Administration', 'action' => 'create_user')); ?>"><i class="icon-book"></i> Manage User Accounts</a></li>
        <li class="<?php echo ($action == 'omclist')? 'active': '';?>"><a href="<?php echo $this->Html->url(array('controller' => 'Administration', 'action' => 'omclist')); ?>"><i class="icon-book"></i> List of Omcs</a></li>
        <li class="<?php echo ($action == 'region')? 'active': '';?>"><a href="<?php echo $this->Html->url(array('controller' => 'Administration', 'action' => 'region')); ?>"><i class="icon-book"></i> Setup Region</a></li>
        <li class="<?php echo ($action == 'district')? 'active': '';?>"><a href="<?php echo $this->Html->url(array('controller' => 'Administration', 'action' => 'district')); ?>"><i class="icon-book"></i> Setup District</a></li>
    </ul>
</div>