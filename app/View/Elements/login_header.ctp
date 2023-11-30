<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container-fluid">
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <?php
            if ($company_profile) {
            ?>
                <a class="brand" style="color:#000000;" href="<?php echo $this->Html->url(array('controller' => 'Dashboard', 'action' => 'demo')); ?>"><i class="icon-black icon-align-center"></i> <?php echo $company_profile['name']; ?></a>
            <?php
            }
            else{
            ?>
                <a class="brand" style="color:#000000;" href="<?php echo $this->Html->url(array('controller' => 'Dashboard', 'action' => 'demo')); ?>"><i class="icon-black icon-align-center"></i> RtHE WARP-D</a>
            <?php
            }
            ?>
        </div>
    </div>
</div>
