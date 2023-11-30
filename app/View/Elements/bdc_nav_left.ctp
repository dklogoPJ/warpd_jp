<?php
    $action = $this->params['action'];
?>
<div class="well sidebar-nav">
    <ul class="nav nav-list">
        <li class="nav-header">List Menus</li>
        <li class="<?php echo ($action == 'index')? 'active': '';?>"><a href="<?php echo $this->Html->url(array('controller' => 'Dashboard', 'action' => 'index')); ?>"><i
            class="icon-home"></i> My Dashboard</a></li>
        <li class="<?php echo ($action == 'enter')? 'active': '';?>"><a
            href="<?php echo $this->Html->url(array('controller' => 'Dashboard', 'action' => 'enter')); ?>"><i
            class="icon-book"></i> Enter Loading Data</a></li>

        <li class="nav-header">Save Data</li>
        <li><a href="#"><i class="icon-align-right"></i> Export Data</a></li>
        <li><a href="#"><i class="icon-list"></i> Sort and Export Data</a></li>
        <!--<li><a href="#"><i class="icon-check"></i> Save Loading Data</a></li>
                <li><a href="<?php /*echo $this->Html->url(array('controller' => 'Dashboard', 'action' => 'edit')); */?>"><i
                    class="icon-pencil"></i> Edit Loading Data</a></li>
                <li><a href="<?php /*echo $this->Html->url(array('controller' => 'Dashboard', 'action' => 'view')); */?>"><i
                    class="icon-th-list"></i> View Loading Data</a></li>-->
        <!--


        <li class="divider"></li>
        <li><a href="#"><i class="icon-flag"></i> Help</a></li>-->
    </ul>
</div>