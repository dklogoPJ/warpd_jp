<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container-fluid">
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <?php
            if ($this->Session->check('CompanyProfile')) {
                $company_profile = $this->Session->read('CompanyProfile');
            ?>
            <a class="brand" style="color:#000000;" href="<?php echo $this->Html->url(array('controller' => 'Administration', 'action' => '#')); ?>"><i class="icon-black icon-align-center"></i> <?php echo $company_profile['name']; ?></a>
            <?php
            }
            else{
            ?>
            <a class="brand" style="color:#000000;" href="<?php echo $this->Html->url(array('controller' => 'Administration', 'action' => '#')); ?>"><i class="icon-black icon-align-center"></i> RtHE WARP-D</a>
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
                    <li><a href="<?php echo $this->Html->url(array('controller' => 'Administration', 'action' => '#')); ?>"><i class="icon-user"></i> Profile</a></li>
                    <li class="divider"></li>
                    <li><a href="<?php echo $this->Html->url(array('controller' => 'Users', 'action' => 'Logout')); ?>"><i class="icon-off"></i> Sign Out</a></li>
                </ul>
            </div>

            <div class="nav-collapse">
                <ul class="nav">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Help <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="<?php echo $this->Html->url(array('controller' => 'Administration', 'action' => '#')); ?>"><i class="icon-align-left"></i> User Manual</a></li>
                            <li class="divider"></li>
                            <li><a href="<?php echo $this->Html->url(array('controller' => 'Administration', 'action' => '#')); ?>"><i class="icon-align-center"></i> Documentation</a></li>
                        </ul>
                    </li>
                </ul>
            </div>

            <?php
            }
            ?>
        </div>
    </div>
</div>
