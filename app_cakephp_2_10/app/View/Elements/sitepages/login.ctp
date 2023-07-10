<!--<div class="bLogo"></div>-->
<div class="loginForm">
    <?php echo $this->Form->create('User', array('id' => 'form-login', 'class' => 'form-horizontal'));?>

    <?php
    $flash_msg = $this->Session->read('Message');
    if (isset($flash_msg['flash'])) {
        ?>
        <div id="loginmsg" style="">
            <?php echo $this->Message->msg('Login', $this->Session->flash(), 'error'); ?>
        </div>
    <?php
    }
    ?>

    <div class="control-group">
        <div class="input-prepend">
            <span class="add-on"><span class="icon-user"></span></span>
            <?php echo $this->Form->input('username', array('id' => 'username', 'class' => 'login username-field', 'placeholder' => 'Username', 'div' => false, 'label' => false)); ?>
        </div>
    </div>
    <div class="control-group">
        <div class="input-prepend">
            <span class="add-on"><span class="icon-lock"></span></span>
            <?php echo $this->Form->input('password', array('type' => 'password', 'id' => 'password', 'class' => 'login password-field', 'placeholder' => 'Password', 'div' => false, 'label' => false)); ?>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span8">
            <div class="control-group" style="margin-top: 5px;">
                <!--<label class="checkbox"><input type="checkbox"> Remember me</label>-->
            </div>
        </div>
        <div class="span4">
            <button type="submit" class="btn btn-block" id="submit_btn">Sign in</button>
        </div>
    </div>
    <?php echo $this->Form->end();?>
    <!--<div class="row-fluid">
        <div class="span8 white">
            <p>Or use on of this services: <a href="#" class="tip" title="facebook"><img src="img/facebook.png"/></a> <a href="#" class="tip" title="twitter"><img src="img/twitter.png"/></a> <a href="#" class="tip" title="google"><img src="img/google.png"/></a></p>
        </div>
        <div class="span1"></div>
        <div class="span3 change">
            <p><a href="#" class="tip" title="Registration">Join us</a></p>
        </div>
    </div>-->
</div>

<!-- Text Under Box -->
<!--<div class="login-extra">
    &copy; 2012 <?php /* if ($this->Session->check('Bdc')) {
    $bdc = $this->Session->read('Bdc');
        echo $bdc['name'];
    }*/
?> - All Rights Reserved<br/>

</div>--> <!-- /login-extra -->

<!-- URLs -->
<input type="hidden" id="login-url"
       value="<?php echo $this->Html->url(array('controller' => 'Users', 'action' => 'login')); ?>"/>
<input type="hidden" id="after-login-url"
       value="<?php echo $this->Html->url(array('controller' => 'Administration', 'action' => 'index')); ?>"/>
<input type="hidden" id="dashboard-url"
       value="<?php echo $this->Html->url(array('controller' => 'Dashboard', 'action' => 'index')); ?>"/>


<?php
echo $this->Html->script('Scripts/login.js');
?>

