<div class="menu">
    <?php echo $this->element('menu_user_profile'); ?>

    <?php
    if($authUser['OmcUser']['omc_user_type'] == 'Admin'){
        ?>
        <ul class="navigation">
            <li class="<?php echo ($this->params['action'] == 'index')? 'active': '' ;?>">
                <a href="<?php echo $this->Html->url(array('controller' => 'Dashboard', 'action' => 'index')); ?>">
                    <span class="isw-grid"></span><span class="text">Users</span>
                </a>
            </li>
            <li class="<?php echo ($this->params['action'] == 'customer_accounts')? 'active': '' ;?>">
                <a href="<?php echo $this->Html->url(array('controller' => 'OmcAdmin', 'action' => 'customer_accounts')); ?>">
                    <span class="isw-list"></span><span class="text">Customer Accounts</span>
                </a>
            </li>
            <li class="<?php echo ($this->params['action'] == 'admin_products')? 'active': '' ;?>">
                <a href="<?php echo $this->Html->url(array('controller' => 'OmcAdmin', 'action' => 'admin_products'));?>">
                    <span class="isw-list"></span><span class="text">Manage Products</span>
                </a>
            </li>
            <!-- <li class="<?php /*echo ($this->params['action'] == 'admin_depots')? 'active': '' ;*/?>">
                <a href="<?php /*echo $this->Html->url(array('controller' => 'OmcAdmin', 'action' => 'admin_depots')); */?>">
                    <span class="isw-list"></span><span class="text">Depots</span>
                </a>
            </li>-->
            <li class="<?php echo ($this->params['action'] == 'company')? 'active': '' ;?>">
                <a href="<?php echo $this->Html->url(array('controller' => 'OmcAdmin', 'action' => 'company')); ?>">
                    <span class="isw-list"></span><span class="text">Company Info</span>
                </a>
            </li>
        </ul>
        <?php
    }
    else{
        if($authUser['OmcUser']['omc_user_type'] == 'Operations'){
            ?>
            <ul class="navigation">
                <li class="<?php echo ($this->params['controller'] == 'OmcOperations' && $this->params['action'] == 'index')? 'active': '' ;?>">
                    <a href="<?php echo $this->Html->url(array('controller' => 'Dashboard', 'action' => 'index')); ?>">
                        <span class="isw-grid"></span><span class="text">Daily Distribution Dashboard</span>
                    </a>
                </li>

                <li class="openable <?php echo ($this->params['controller'] == 'OmcOrders')? 'active': '' ;?>">
                    <a href="javascript: void(0);">
                        <span class="isw-grid"></span><span class="text">Order Management</span>
                    </a>
                    <ul>
                        <li class="<?php echo ($this->params['action'] == 'orders')? 'active': '' ;?>">
                            <a href="<?php echo $this->Html->url(array('controller' => 'OmcOrders', 'action' => 'orders')); ?>">
                                <span class="icon-file"></span><span class="text">Order Allocations</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <?php
                $return = in_array('uppf',$company_modules);
                if($return){
                    ?>
                    <li class="<?php echo ($this->params['controller'] == 'OmcUppf' && $this->params['action'] == 'uppf')? 'active': '' ;?>">
                        <a href="<?php echo $this->Html->url(array('controller' => 'OmcUppf', 'action' => 'uppf')); ?>">
                            <span class="isw-grid"></span><span class="text">UPPF</span>
                        </a>
                    </li>
                <?php
                }
                ?>

                <li class="openable <?php echo ($this->params['controller'] == 'OmcReporting')? 'active': '' ;?>">
                    <a href="javascript: void(0);">
                        <span class="isw-grid"></span><span class="text">Report Center</span>
                    </a>
                    <ul>
                        <li class="<?php echo ($this->params['action'] == 'index' && $this->params['controller'] == 'OmcReporting')? 'active': '' ;?>">
                            <a href="<?php echo $this->Html->url(array('controller' => 'OmcReporting', 'action' => 'index')); ?>">
                                <span class="icon-file"></span><span class="text">Monthly Product Loading</span>
                            </a>
                        </li>
                        <li class="<?php echo ($this->params['action'] == 'report_bdc_variant')? 'active': '' ;?>">
                            <a href="<?php echo $this->Html->url(array('controller' => 'OmcReporting', 'action' => 'report_bdc_variant')); ?>">
                                <span class="icon-file"></span><span class="text">Monthly Loading by BDCs</span>
                            </a>
                        </li>
                        <li class="<?php echo ($this->params['action'] == 'report_depot_variant')? 'active': '' ;?>">
                            <a href="<?php echo $this->Html->url(array('controller' => 'OmcReporting', 'action' => 'report_depot_variant')); ?>">
                                <span class="icon-file"></span><span class="text">Monthly Loading by Depot</span>
                            </a>
                        </li>

                        <li class="<?php echo ($this->params['action'] == 'report_orders')? 'active': '' ;?>">
                            <a href="<?php echo $this->Html->url(array('controller' => 'OmcReporting', 'action' => 'report_orders')); ?>">
                                <span class="icon-file"></span><span class="text">Orders Data</span>
                            </a>
                        </li>

                    </ul>
                </li>

                <?php
                $allowed_controllers = array('OmcOperations','OmcOrders');
                $allowed_action = array('index','orders');
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
        elseif($authUser['OmcUser']['omc_user_type'] == 'Marketing'){
            ?>
            <ul class="navigation">

                <li class="openable <?php echo ($this->params['controller'] == 'OmcOrders')? 'active': '' ;?>">
                    <a href="javascript: void(0);">
                        <span class="isw-grid"></span><span class="text">Order Management</span>
                    </a>
                    <ul>
                        <li class="<?php echo ($this->params['action'] == 'customer_orders' && $this->params['controller'] == 'OmcOrders')? 'active': '' ;?>">
                            <a href="<?php echo $this->Html->url(array('controller' => 'OmcOrders', 'action' => 'customer_orders')); ?>">
                                <span class="icon-file"></span><span class="text">New Customer Orders</span>
                            </a>
                        </li>
                        <li class="<?php echo ($this->params['action'] == 'orders')? 'active': '' ;?>">
                            <a href="<?php echo $this->Html->url(array('controller' => 'OmcOrders', 'action' => 'orders')); ?>">
                                <span class="icon-file"></span><span class="text">Order Allocations</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <?php
                $return = in_array('stock_management',$company_modules);
                if($return){
                    ?>
                    <li class="openable <?php echo ($this->params['controller'] == 'OmcStock')? 'active': '' ;?>">
                        <a href="javascript: void(0);">
                            <span class="isw-grid"></span><span class="text">Stock Management</span>
                        </a>
                        <ul>
                            <!--<li class="<?php /*echo ($this->params['action'] == 'daily_stock' && $this->params['controller'] == 'OmcStock')? 'active': '' ;*/?>">
                                <a href="<?php /*echo $this->Html->url(array('controller' => 'OmcStock', 'action' => 'daily_stock')); */?>">
                                    <span class="icon-file"></span><span class="text">Daily Stock Report</span>
                                </a>
                            </li>-->
                            <li class="<?php echo ($this->params['action'] == 'daily_stock_variance')? 'active': '' ;?>">
                                <a href="<?php echo $this->Html->url(array('controller' => 'OmcStock', 'action' => 'daily_stock_variance')); ?>">
                                    <span class="icon-file"></span><span class="text">Daily Stock Report</span>
                                </a>
                            </li>
                            <li class="<?php echo ($this->params['action'] == 'stock_administration')? 'active': '' ;?>">
                                <a href="<?php echo $this->Html->url(array('controller' => 'OmcStock', 'action' => 'stock_administration')); ?>">
                                    <span class="icon-file"></span><span class="text">Stock Administration</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php
                }
                ?>

                <li class="openable <?php echo ($this->params['controller'] == 'OmcReporting')? 'active': '' ;?>">
                    <a href="javascript: void(0);">
                        <span class="isw-grid"></span><span class="text">Report Center</span>
                    </a>
                    <ul>
                        <li class="<?php echo ($this->params['action'] == 'index' && $this->params['controller'] == 'OmcReporting')? 'active': '' ;?>">
                            <a href="<?php echo $this->Html->url(array('controller' => 'OmcReporting', 'action' => 'index')); ?>">
                                <span class="icon-file"></span><span class="text">Monthly Product Loading</span>
                            </a>
                        </li>
                        <li class="<?php echo ($this->params['action'] == 'report_bdc_variant')? 'active': '' ;?>">
                            <a href="<?php echo $this->Html->url(array('controller' => 'OmcReporting', 'action' => 'report_bdc_variant')); ?>">
                                <span class="icon-file"></span><span class="text">Monthly Loading by BDCs</span>
                            </a>
                        </li>
                        <li class="<?php echo ($this->params['action'] == 'report_depot_variant')? 'active': '' ;?>">
                            <a href="<?php echo $this->Html->url(array('controller' => 'OmcReporting', 'action' => 'report_depot_variant')); ?>">
                                <span class="icon-file"></span><span class="text">Monthly Loading by Depot</span>
                            </a>
                        </li>

                        <li class="<?php echo ($this->params['action'] == 'report_orders')? 'active': '' ;?>">
                            <a href="<?php echo $this->Html->url(array('controller' => 'OmcReporting', 'action' => 'report_orders')); ?>">
                                <span class="icon-file"></span><span class="text">Orders Data</span>
                            </a>
                        </li>

                        <li class="<?php echo ($this->params['action'] == 'stock_histories')? 'active': '' ;?>">
                            <a href="<?php echo $this->Html->url(array('controller' => 'OmcReporting', 'action' => 'stock_histories')); ?>">
                                <span class="icon-file"></span><span class="text">Stock Histories</span>
                            </a>
                        </li>

                    </ul>
                </li>

                <?php
                $allowed_controllers = array('OmcOperations','OmcOrders');
                $allowed_action = array('index','orders');
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
        elseif($authUser['OmcUser']['omc_user_type'] == 'Finance'){
            ?>
            <ul class="navigation">

                <li class="openable <?php echo ($this->params['controller'] == 'OmcOrders')? 'active': '' ;?>">
                    <a href="javascript: void(0);">
                        <span class="isw-grid"></span><span class="text">Order Management</span>
                    </a>
                    <ul>
                        <li class="<?php echo ($this->params['action'] == 'customer_orders' && $this->params['controller'] == 'OmcOrders')? 'active': '' ;?>">
                            <a href="<?php echo $this->Html->url(array('controller' => 'OmcOrders', 'action' => 'customer_orders')); ?>">
                                <span class="icon-file"></span><span class="text">New Customer Orders</span>
                            </a>
                        </li>
                        <li class="<?php echo ($this->params['action'] == 'orders')? 'active': '' ;?>">
                            <a href="<?php echo $this->Html->url(array('controller' => 'OmcOrders', 'action' => 'orders')); ?>">
                                <span class="icon-file"></span><span class="text">Order Allocations</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="<?php echo ($this->params['controller'] == 'OmcCustomers' && $this->params['action'] == 'index')? 'active': '' ;?>">
                    <a href="<?php echo $this->Html->url(array('controller' => 'OmcCustomers', 'action' => 'index')); ?>">
                        <span class="isw-grid"></span><span class="text">Customer Bio Data</span>
                    </a>
                </li>

                <li class="openable <?php echo ($this->params['controller'] == 'OmcReporting')? 'active': '' ;?>">
                    <a href="javascript: void(0);">
                        <span class="isw-grid"></span><span class="text">Report Center</span>
                    </a>
                    <ul>
                        <li class="<?php echo ($this->params['action'] == 'index' && $this->params['controller'] == 'OmcReporting')? 'active': '' ;?>">
                            <a href="<?php echo $this->Html->url(array('controller' => 'OmcReporting', 'action' => 'index')); ?>">
                                <span class="icon-file"></span><span class="text">Monthly Product Loading</span>
                            </a>
                        </li>
                        <li class="<?php echo ($this->params['action'] == 'report_bdc_variant')? 'active': '' ;?>">
                            <a href="<?php echo $this->Html->url(array('controller' => 'OmcReporting', 'action' => 'report_bdc_variant')); ?>">
                                <span class="icon-file"></span><span class="text">Monthly Loading by BDCs</span>
                            </a>
                        </li>
                        <li class="<?php echo ($this->params['action'] == 'report_depot_variant')? 'active': '' ;?>">
                            <a href="<?php echo $this->Html->url(array('controller' => 'OmcReporting', 'action' => 'report_depot_variant')); ?>">
                                <span class="icon-file"></span><span class="text">Monthly Loading by Depot</span>
                            </a>
                        </li>

                        <li class="<?php echo ($this->params['action'] == 'report_orders')? 'active': '' ;?>">
                            <a href="<?php echo $this->Html->url(array('controller' => 'OmcReporting', 'action' => 'report_orders')); ?>">
                                <span class="icon-file"></span><span class="text">Orders Data</span>
                            </a>
                        </li>

                    </ul>
                </li>

                <?php
                $allowed_controllers = array('OmcOperations','OmcOrders');
                $allowed_action = array('index','orders');
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
    if($authUser['OmcUser']['omc_user_type'] == 'Admin'){
        ?>
        <?php
    }
    else{
        ?>
        <div class="dr"><span></span></div>

        <div class="widget-fluid">

            <div class="wBlock clearfix">
                <div class="dSpace">
                    <h3>Today</h3>
                    <span class="number" style="font-size: 15px;">
                        <?php echo $today_yesterday_totals['today']; ?>
                    </span>
                    <span><b>LTRS</b></span>
                    <!--<span>5,774 <b>Offloading</b></span>
                    <span>3,512 <b>Uploading</b></span>-->
                </div>
                <div class="rSpace">
                    <h3>Yesterday</h3>
                    <span class="number" style="font-size: 15px; color: #FFF; font-weight: bold; line-height: 32px;">
                        <?php echo $today_yesterday_totals['yesterday']; ?>
                    </span>
                    <span><b>LTRS</b></span>
                    <!--<span>6500 <b>Offloading</b></span>
                    <span>3500 <b>Uploading</b></span>-->
                </div>
            </div>

        </div>
        <?php
    }
    ?>



    <div class="dr"><span></span></div>

</div>