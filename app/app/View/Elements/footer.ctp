<div class="span12">
    <footer>
        <!--<p>WARP-D Systems - &copy; 2012</p>-->
    </footer>
    <!-- Users URL -->
    <input type="hidden" id="mail-link" value="<?php echo $this->Html->url(array('controller' => 'Messages', 'action' => 'index')); ?>" />
    <input type="hidden" id="mail-check-url" value="<?php echo $this->Html->url(array('controller' => 'Messages', 'action' => 'checkMail')); ?>" />
    <input type="hidden" id="current_page_controller" value="<?php echo $this->params['controller']; ?>" />
    <input type="hidden" id="current_page_action" value="<?php echo $this->params['action']; ?>" />
    <input type="hidden" id="logout-url" value="<?php echo $this->Html->url(array('controller' => 'Users', 'action' => 'logout')); ?>" />
    <input type="hidden" id="volumes_url" value="<?php echo $this->Html->url(array('controller' => 'Common', 'action' => 'set_volume')); ?>" />
</div>
<?php echo $this->element('footer_ext');?>