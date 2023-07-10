<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container-fluid">
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <?php
            if ($this->Session->check('Bdc')) {
                $bdc = $this->Session->read('Bdc');
                ?>
                <a class="brand" style="color:#000000;" href="<?php echo $this->Html->url(array('controller' => 'Dashboard', 'action' => 'demo')); ?>"><i class="icon-black icon-align-center"></i> <?php echo $bdc['name']; ?></a>
            <?php
            }
            else{
            ?>
                <a class="brand" style="color:#000000;" href="<?php echo $this->Html->url(array('controller' => 'Dashboard', 'action' => 'demo')); ?>"><i class="icon-black icon-align-center"></i> RtHE WARP-D</a>
            <?php
            }
            ?>

            <?php
            if($authUser){
            ?>
                <div class="btn-group pull-right">
                    <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="icon-user"></i> David Klogo
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="#"><i class="icon-user"></i> Profile</a></li>
                        <li class="divider"></li>
                        <li><a href="<?php echo $this->Html->url(array('controller' => 'Users', 'action' => 'Logout')); ?>"><i class="icon-off"></i> Sign Out</a></li>
                    </ul>
                </div>
                <div class="nav-collapse">
                    <ul class="nav">
                        <li class="active"><a href="<?php echo $this->Html->url(array('controller' => 'Dashboard', 'action' => 'index')); ?>"><i class="icon-white icon-home"></i> Home</a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">About Us <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="#"><i class="icon-align-left"></i> RtHE Consult</a></li>
                                <li class="divider"></li>
                                <li><a href="#"><i class="icon-align-center"></i> WARP-D Software</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">BDCs <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo $this->Html->url(array('controller' => 'Dashboard', 'action' => 'index')); ?>"><i class="icon-align-left"></i> BDC Dashboard</a></li>
                                <li class="divider"></li>
                                <li><a href="#"><i class="icon-align-center"></i> Link Two</a></li>
                                <li class="divider"></li>
                                <li><a href="#"><i class="icon-align-right"></i> Link Three</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">OMCs <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="#"><i class="icon-align-left"></i> Link One</a></li>
                                <li class="divider"></li>
                                <li><a href="#"><i class="icon-align-center"></i> Link Two</a></li>
                                <li class="divider"></li>
                                <li><a href="#"><i class="icon-align-right"></i> Link Three</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Regulators <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="#"><i class="icon-align-left"></i> NPA</a></li>
                                <li class="divider"></li>
                                <li><a href="#"><i class="icon-align-center"></i> Standard Authority</a></li>
                                <li class="divider"></li>
                                <li><a href="#"><i class="icon-align-right"></i> BOST</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Help <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="#"><i class="icon-align-left"></i> User Manual</a></li>
                                <li class="divider"></li>
                                <li><a href="#"><i class="icon-align-center"></i> Documentation</a></li>
                            </ul>
                        </li>
                    </ul>
                </div><!--/.nav-collapse -->
            <?php
            }
            ?>
        </div>
    </div>
</div>
