<div class="menu">
    <?php echo $this->element('menu_user_profile'); ?>

    <?php
    if($authUser['OmcCustomerUser']['omc_customer_user_type'] == 'Admin'){
        ?>
        <!--<ul class="navigation">
            <li class="<?php /*echo ($this->params['action'] == 'index')? 'active': '' ;*/?>">
                <a href="<?php /*echo $this->Html->url(array('controller' => 'Dashboard', 'action' => 'index')); */?>">
                    <span class="isw-grid"></span><span class="text">Users</span>
                </a>
            </li>
            <li class="<?php /*echo ($this->params['action'] == 'customer_accounts')? 'active': '' ;*/?>">
                <a href="<?php /*echo $this->Html->url(array('controller' => 'OmcAdmin', 'action' => 'customer_accounts')); */?>">
                    <span class="isw-list"></span><span class="text">Customer Accounts</span>
                </a>
            </li>
             <li class="<?php /*echo ($this->params['action'] == 'admin_depots')? 'active': '' ;*/?>">
                <a href="<?php /*echo $this->Html->url(array('controller' => 'OmcAdmin', 'action' => 'admin_depots')); */?>">
                    <span class="isw-list"></span><span class="text">Depots</span>
                </a>
            </li>
            <li class="<?php /*echo ($this->params['action'] == 'company')? 'active': '' ;*/?>">
                <a href="<?php /*echo $this->Html->url(array('controller' => 'OmcAdmin', 'action' => 'company')); */?>">
                    <span class="isw-list"></span><span class="text">Company Info</span>
                </a>
            </li>
        </ul>-->
        <?php
    }
    else{
        if($authUser['OmcCustomerUser']['omc_customer_user_type'] == 'Operations'){
            ?>
            <ul class="navigation">
                <li class="<?php echo ($this->params['controller'] == 'OmcDealer' && $this->params['action'] == 'index')? 'active': '' ;?>">
                    <a href="<?php echo $this->Html->url(array('controller' => 'OmcDealer', 'action' => 'index')); ?>">
                        <span class="isw-grid"></span><span class="text">Daily Distribution Dashboard</span>
                    </a>
                </li>

                <li class="<?php echo ($this->params['controller'] == 'OmcDealerOrders' && $this->params['action'] == 'orders')? 'active': '' ;?>">
                    <a href="<?php echo $this->Html->url(array('controller' => 'OmcDealerOrders', 'action' => 'orders')); ?>">
                        <span class="isw-grid"></span><span class="text">Order Management</span>
                    </a>
                </li>

                <li class="openable <?php echo ($this->params['controller'] == 'OmcDealerStock')? 'active': '' ;?>">
                    <a href="javascript: void(0);">
                        <span class="isw-grid"></span><span class="text">Stock Management</span>
                    </a>
                    <ul>
                        <!--<li class="<?php /*echo ($this->params['action'] == 'tanks_setup' && $this->params['controller'] == 'OmcDealerStock')? 'active': '' ;*/?>">
                            <a href="<?php /*echo $this->Html->url(array('controller' => 'OmcDealerStock', 'action' => 'tanks_setup')); */?>">
                                <span class="icon-file"></span><span class="text">Tanks Setup</span>
                            </a>
                        </li>-->
                        <li class="<?php echo ($this->params['action'] == 'stock_update')? 'active': '' ;?>">
                            <a href="<?php echo $this->Html->url(array('controller' => 'OmcDealerStock', 'action' => 'stock_update')); ?>">
                                <span class="icon-file"></span><span class="text">Stock Update</span>
                            </a>
                        </li>
                        <li class="<?php echo ($this->params['action'] == 'stock_histories')? 'active': '' ;?>">
                            <a href="<?php echo $this->Html->url(array('controller' => 'OmcDealerStock', 'action' => 'stock_histories')); ?>">
                                <span class="icon-file"></span><span class="text">Stock Histories</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="<?php echo ($this->params['action'] == 'admin_products')? 'active': '' ;?>">
                    <a href="<?php echo $this->Html->url(array('controller' => 'OmcCustomerAdmin', 'action' => 'admin_products'));?>">
                        <span class="isw-list"></span><span class="text">Manage Products</span>
                    </a>
                </li>

                <?php
                $allowed_controllers = array('OmcDealer','OmcDealerOrders','OmcDealerStock');
                $allowed_action = array('index','orders','stock_report');
                if(in_array($this->params['controller'],$allowed_controllers) && in_array($this->params['action'],$allowed_action)){
                    ?>
                    <li>
                        <a href="#export-form">
                            <span class="isw-folder"></span><span class="text">Export Data</span>
                        </a>
                    </li>
                <?php
                }
                ?>
            </ul>
            <?php
        }
        elseif($authUser['OmcCustomerUser']['omc_customer_user_type'] == 'Marketing'){
            ?>
            <!-- menu here-->
            <?php
        }
        elseif($authUser['OmcCustomerUser']['omc_customer_user_type'] == 'Finance'){
            ?>
            <!-- menu here-->
            <?php
        }
        else{// For may be guests menus
            ?>
            <!-- menu here-->
            <?php
        }
    }
    ?>

    <div class="dr"><span></span></div>

    <div class="widget-fluid">
        <div id="menuDatepicker"></div>
    </div>
    <?php
    if($authUser['OmcCustomerUser']['omc_customer_user_type'] == 'Admin'){
        ?>
        <?php
    }
    else{
        ?>
        <div class="dr"><span></span></div>

        <?php
    }
    ?>
</div>