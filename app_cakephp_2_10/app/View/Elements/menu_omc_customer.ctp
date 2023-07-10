<div class="menu">
    <?php echo $this->element('menu_user_profile'); ?>


    <ul class="navigation">
        <li class="<?php echo ($this->params['action'] == 'dashboard' && $this->params['controller'] == 'OmcCustomer')? 'active': '' ;?>">
            <a href="<?php echo $this->Html->url(array('controller' =>  'OmcCustomer', 'action' =>  'dashboard')); ?>">
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

</div>