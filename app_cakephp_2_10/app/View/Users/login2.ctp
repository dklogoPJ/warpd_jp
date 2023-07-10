<div class="login-logo"></div>

<div class="account-container login">

    <div class="content clearfix">

        <?php echo $this->Form->create('User', array('default' => false, 'id' => 'form-login'));?>

        <h1>Sign In</h1>

        <div class="login-fields">

            <p>Sign in using your registered account:</p>

            <div id="loginmsg" style="display: none;">
                <?php echo $this->Message->msg('Required','Please enter username/password','error'); ?>
            </div>

            <div class="field">
                <label>Username:</label>
                <?php echo $this->Form->input('username', array('id'=>'username' ,'class' => 'login username-field', 'placeholder'=>'Username', 'div' => false, 'label' => false)); ?>
            </div> <!-- /field -->

            <div class="field">
                <label>Password:</label>
                <?php echo $this->Form->input('password', array('type'=>'password', 'id'=>'password' ,'class' => 'login password-field', 'placeholder'=>'Password', 'div' => false, 'label' => false)); ?>
            </div> <!-- /password -->

        </div> <!-- /login-fields -->

        <div class="login-actions">
            <!--<span class="login-checkbox">
                    <input id="Field" name="Field" type="checkbox" class="field login-checkbox" value="First Choice" tabindex="4" />
                    <label class="choice" for="Field">Keep me signed in</label>
            </span>-->
            <button type="submit" id="submit_btn" class="button btn btn-large btn-secondary btn-custom-huge">Sign In</button>

        </div> <!-- .actions -->

        <!-- <div class="login-social">
             <p>Sign in using social network:</p>

             <div class="twitter">
                 <a href="#" class="btn_1">Login with Twitter</a>
             </div>

             <div class="fb">
                 <a href="#" class="btn_2">Login with Facebook</a>
             </div>
         </div>-->

        <?php echo $this->Form->end();?>

    </div> <!-- /content -->

</div> <!-- /account-container -->


<!-- Text Under Box -->
<!--<div class="login-extra">
    &copy; 2012 <?php /* if ($this->Session->check('Bdc')) {
    $bdc = $this->Session->read('Bdc');
        echo $bdc['name'];
    }*/?> - All Rights Reserved<br/>

</div>--> <!-- /login-extra -->

<!-- URLs -->
<input type="hidden" id="login-url" value="<?php echo $this->Html->url(array('controller' => 'Users', 'action' => 'login')); ?>" />
<input type="hidden" id="after-login-url" value="<?php echo $this->Html->url(array('controller' => 'Administration', 'action' => 'index')); ?>" />
<input type="hidden" id="dashboard-url" value="<?php echo $this->Html->url(array('controller' => 'Dashboard', 'action' => 'index')); ?>" />


<?php
echo $this->Html->script('Scripts/login.js');
?>

