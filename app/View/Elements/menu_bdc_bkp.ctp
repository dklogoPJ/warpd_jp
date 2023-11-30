<div class="menu">

    <?php echo $this->element('menu_user_profile'); ?>

    <?php
        if($authUser['BdcUser']['bdc_user_type'] == 'Admin'){
    ?>
        <ul class="navigation">
            <li class="<?php echo ($this->params['action'] == 'users')? 'active': '' ;?>">
                <a href="<?php echo $this->Html->url(array('controller' => 'BdcAdmin', 'action' => 'users')); ?>">
                    <span class="isw-grid"></span><span class="text">Users</span>
                </a>
            </li>
            <li class="<?php echo ($this->params['action'] == 'admin_depots')? 'active': '' ;?>">
                <a href="<?php echo $this->Html->url(array('controller' => 'BdcAdmin', 'action' => 'admin_depots'));?>">
                    <span class="isw-list"></span><span class="text">Manage Depots</span>
                </a>
            </li>
            <li class="<?php echo ($this->params['action'] == 'admin_products')? 'active': '' ;?>">
                <a href="<?php echo $this->Html->url(array('controller' => 'BdcAdmin', 'action' => 'admin_products'));?>">
                    <span class="isw-list"></span><span class="text">Manage Products</span>
                </a>
            </li>
            <li class="<?php echo ($this->params['action'] == 'admin_depots_to_products')? 'active': '' ;?>">
                <a href="<?php echo $this->Html->url(array('controller' => 'BdcAdmin', 'action' => 'admin_depots_to_products'));?>">
                    <span class="isw-list"></span><span class="text">Depots to Products</span>
                </a>
            </li>
            <li class="<?php echo ($this->params['action'] == 'company')? 'active': '' ;?>">
                <a href="<?php echo $this->Html->url(array('controller' => 'BdcAdmin', 'action' => 'company')); ?>">
                    <span class="isw-list"></span><span class="text">Company Info</span>
                </a>
            </li>
        </ul>
    <?php
        }
        else{
            if($authUser['BdcUser']['bdc_user_type'] == 'Operations'){
    ?>
                <ul class="navigation">
                    <li class="<?php echo ($this->params['action'] == 'index' && $this->params['controller'] != 'BdcReporting')? 'active': '' ;?>">
                        <a href="<?php echo $this->Html->url(array('controller' => 'Dashboard', 'action' => 'index')); ?>">
                            <span class="isw-grid"></span><span class="text">Daily Distribution Dashboard</span>
                        </a>
                    </li>
                    <li class="<?php echo ($this->params['action'] == 'enter_loading_data')? 'active': '' ;?>">
                        <a href="<?php echo $this->Html->url(array('controller' => 'BdcOperations', 'action' => 'enter_loading_data')); ?>">
                            <span class="isw-grid"></span><span class="text">Enter Loading Data</span>
                        </a>
                    </li>
                    <?php
                    $return = in_array('Crm',$company_modules);
                    if($return){
                    ?>
                        <li class="<?php echo ($this->params['controller'] == 'BdcOrders' && $this->params['action'] == 'orders')? 'active': '' ;?>">
                            <a href="<?php echo $this->Html->url(array('controller' => 'BdcOrders', 'action' => 'orders')); ?>">
                                <span class="isw-grid"></span><span class="text">Customer Order Mgt</span>
                            </a>
                        </li>
                    <?php
                    }
                    ?>

                    <li class="openable <?php echo ($this->params['controller'] == 'BdcStock')? 'active': '' ;?>">
                        <a href="javascript: void(0);">
                            <span class="isw-grid"></span><span class="text">Stock Management</span>
                        </a>
                        <ul>
                            <!--<li class="<?php /*echo ($this->params['action'] == 'index' && $this->params['controller'] == 'BdcStock')? 'active': '' ;*/?>">
                                <a href="<?php /*echo $this->Html->url(array('controller' => 'BdcStock', 'action' => 'index')); */?>">
                                    <span class="icon-file"></span><span class="text">Stock Positions</span>
                                </a>
                            </li>-->
                            <li class="<?php echo ($this->params['action'] == 'initial_startup_stocks')? 'active': '' ;?>">
                                <a href="<?php echo $this->Html->url(array('controller' => 'BdcStock', 'action' => 'initial_startup_stocks')); ?>">
                                    <span class="icon-file"></span><span class="text">Initialize Stock Quantity</span>
                                </a>
                            </li>
                            <li class="<?php echo ($this->params['action'] == 'stock_update')? 'active': '' ;?>">
                                <a href="<?php echo $this->Html->url(array('controller' => 'BdcStock', 'action' => 'stock_update')); ?>">
                                    <span class="icon-file"></span><span class="text">Stock Update</span>
                                </a>
                            </li>
                            <li class="<?php echo ($this->params['action'] == 'getDailyStockVariance')? 'active': '' ;?>">
                                <a href="<?php echo $this->Html->url(array('controller' => 'BdcStock', 'action' => 'getDailyStockVariance')); ?>">
                                    <span class="icon-file"></span><span class="text">Daily Stock Variance</span>
                                </a>
                            </li>
                            <li class="<?php echo ($this->params['action'] == 'getStockHistories')? 'active': '' ;?>">
                                <a href="<?php echo $this->Html->url(array('controller' => 'BdcStock', 'action' => 'getStockHistories')); ?>">
                                    <span class="icon-file"></span><span class="text">Stock History</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="openable <?php echo ($this->params['controller'] == 'BdcReporting')? 'active': '' ;?>">
                        <a href="javascript: void(0);">
                            <span class="isw-grid"></span><span class="text">Reports Center</span>
                        </a>
                        <ul>
                            <li class="<?php echo ($this->params['action'] == 'index' && $this->params['controller'] == 'BdcReporting')? 'active': '' ;?>">
                                <a href="<?php echo $this->Html->url(array('controller' => 'BdcReporting', 'action' => 'index')); ?>">
                                    <span class="icon-file"></span><span class="text">Monthly Product Loading</span>
                                </a>
                            </li>
                            <li class="<?php echo ($this->params['action'] == 'report_omc_variant')? 'active': '' ;?>">
                                <a href="<?php echo $this->Html->url(array('controller' => 'BdcReporting', 'action' => 'report_omc_variant')); ?>">
                                    <span class="icon-file"></span><span class="text">Monthly Loading by OMCs</span>
                                </a>
                            </li>
                            <li class="<?php echo ($this->params['action'] == 'report_depot_variant')? 'active': '' ;?>">
                                <a href="<?php echo $this->Html->url(array('controller' => 'BdcReporting', 'action' => 'report_depot_variant')); ?>">
                                    <span class="icon-file"></span><span class="text">Monthly Loading by Depot</span>
                                </a>
                            </li>

                            <?php
                            $return = in_array('Crm',$company_modules);
                            if($return){
                                ?>
                                <li class="<?php echo ($this->params['action'] == 'report_orders')? 'active': '' ;?>">
                                    <a href="<?php echo $this->Html->url(array('controller' => 'BdcReporting', 'action' => 'report_orders')); ?>">
                                        <span class="icon-file"></span><span class="text">Customer Orders</span>
                                    </a>
                                </li>
                            <?php
                            }
                            ?>

                        </ul>
                    </li>

                    <?php
                    $allowed_controllers = array('BdcOperations','BdcOrders');
                    $allowed_action = array('enter_loading_data','orders');
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
            elseif($authUser['BdcUser']['bdc_user_type'] == 'Marketing'){
    ?>
                <!-- menu here-->
                <ul class="navigation">
                    <li class="<?php echo ($this->params['controller'] == 'BdcOrders' && $this->params['action'] == 'orders')? 'active': '' ;?>">
                        <a href="<?php echo $this->Html->url(array('controller' => 'BdcOrders', 'action' => 'orders')); ?>">
                            <span class="isw-grid"></span><span class="text">Customer Orders</span>
                        </a>
                    </li>
                    <li class="openable <?php echo ($this->params['controller'] == 'BdcReporting')? 'active': '' ;?>">
                        <a href="javascript: void(0);">
                            <span class="isw-grid"></span><span class="text">Report</span>
                        </a>
                        <ul>
                            <li class="<?php echo ($this->params['action'] == 'index' && $this->params['controller'] == 'BdcReporting')? 'active': '' ;?>">
                                <a href="<?php echo $this->Html->url(array('controller' => 'BdcReporting', 'action' => 'index')); ?>">
                                    <span class="icon-file"></span><span class="text">Monthly Consolidated by Product</span>
                                </a>
                            </li>
                            <li class="<?php echo ($this->params['action'] == 'report_omc_variant')? 'active': '' ;?>">
                                <a href="<?php echo $this->Html->url(array('controller' => 'BdcReporting', 'action' => 'report_omc_variant')); ?>">
                                    <span class="icon-file"></span><span class="text">Monthly Lifting by OMCs</span>
                                </a>
                            </li>
                            <li class="<?php echo ($this->params['action'] == 'report_depot_variant')? 'active': '' ;?>">
                                <a href="<?php echo $this->Html->url(array('controller' => 'BdcReporting', 'action' => 'report_depot_variant')); ?>">
                                    <span class="icon-file"></span><span class="text">Monthly Lifting by Depot</span>
                                </a>
                            </li>
                            <li class="<?php echo ($this->params['action'] == 'report_orders')? 'active': '' ;?>">
                                <a href="<?php echo $this->Html->url(array('controller' => 'BdcReporting', 'action' => 'report_orders')); ?>">
                                    <span class="icon-file"></span><span class="text">Orders</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>

    <?php
            }
            elseif($authUser['BdcUser']['bdc_user_type'] == 'Finance'){
    ?>
                <!-- menu here-->
                <ul class="navigation">
                    <li class="<?php echo ($this->params['controller'] == 'BdcOrders' && $this->params['action'] == 'orders')? 'active': '' ;?>">
                        <a href="<?php echo $this->Html->url(array('controller' => 'BdcOrders', 'action' => 'orders')); ?>">
                            <span class="isw-grid"></span><span class="text">Customer Orders</span>
                        </a>
                    </li>
                    <li class="openable <?php echo ($this->params['controller'] == 'BdcReporting')? 'active': '' ;?>">
                        <a href="javascript: void(0);">
                            <span class="isw-grid"></span><span class="text">Report</span>
                        </a>
                        <ul>
                            <li class="<?php echo ($this->params['action'] == 'index' && $this->params['controller'] == 'BdcReporting')? 'active': '' ;?>">
                                <a href="<?php echo $this->Html->url(array('controller' => 'BdcReporting', 'action' => 'index')); ?>">
                                    <span class="icon-file"></span><span class="text">Monthly Consolidated by Product</span>
                                </a>
                            </li>
                            <li class="<?php echo ($this->params['action'] == 'report_omc_variant')? 'active': '' ;?>">
                                <a href="<?php echo $this->Html->url(array('controller' => 'BdcReporting', 'action' => 'report_omc_variant')); ?>">
                                    <span class="icon-file"></span><span class="text">Monthly Lifting by OMCs</span>
                                </a>
                            </li>
                            <li class="<?php echo ($this->params['action'] == 'report_depot_variant')? 'active': '' ;?>">
                                <a href="<?php echo $this->Html->url(array('controller' => 'BdcReporting', 'action' => 'report_depot_variant')); ?>">
                                    <span class="icon-file"></span><span class="text">Monthly Lifting by Depot</span>
                                </a>
                            </li>
                            <li class="<?php echo ($this->params['action'] == 'report_orders')? 'active': '' ;?>">
                                <a href="<?php echo $this->Html->url(array('controller' => 'BdcReporting', 'action' => 'report_orders')); ?>">
                                    <span class="icon-file"></span><span class="text">Orders</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
    <?php
            }
            elseif($authUser['BdcUser']['bdc_user_type'] == 'Consolidation'){
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
        if($authUser['BdcUser']['bdc_user_type'] == 'Admin'){
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