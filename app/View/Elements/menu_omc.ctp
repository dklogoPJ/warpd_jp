<div class="menu">
    <?php echo $this->element('menu_user_profile'); ?>


    <ul class="navigation">
        <li class="<?php echo ($this->params['action'] == 'dashboard' && $this->params['controller'] == 'Omc')? 'active': '' ;?>">
            <a href="<?php echo $this->Html->url(array('controller' =>  'Omc', 'action' =>  'dashboard')); ?>">
                <span class="isw-grid"></span><span class="text">Dashboard</span>
            </a>
        </li>
        <?php
        foreach($user_menus as $um){
            if(isset($um['sub'])){
                ?>
                <li class="openable <?php echo ($this->params['controller'] == $um['controller'])? 'active': '' ;?>">
                    <a href="javascript: void(0);">
                        <span class="isw-grid"></span><span class="text"><?php echo $um['name'] ;?></span>
                    </a>
                    <ul>
                        <?php
                        foreach($um['sub'] as $inner_um){
                            ?>
                            <li class="<?php echo ($this->params['action'] == $inner_um['action'] && $this->params['controller'] == $inner_um['controller'])? 'active': '' ;?>">
                                <a href="<?php echo $this->Html->url(array('controller' =>  $inner_um['controller'], 'action' =>  $inner_um['action'])); ?>">
                                    <span class="<?php echo $inner_um['icon'] ;?>"></span><span class="text"><?php echo $inner_um['name'] ;?></span>
                                </a>
                            </li>
                        <?php
                        }
                        ?>
                    </ul>
                </li>
            <?php
            }
            else{
                ?>
                <li class="<?php echo ($this->params['action'] == $um['action'] && $this->params['controller'] == $um['controller'])? 'active': '' ;?>">
                    <a href="<?php echo $this->Html->url(array('controller' =>  $um['controller'], 'action' =>  $um['action'])); ?>">
                        <span class="<?php echo $um['icon'] ;?>"></span><span class="text"><?php echo $um['name'] ;?></span>
                    </a>
                </li>
            <?php
            }
        }
        ?>
    </ul>


    <div class="dr"><span></span></div>

    <div class="widget-fluid">
        <div id="menuDatepicker"></div>
    </div>

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


    <div class="dr"><span></span></div>

</div>