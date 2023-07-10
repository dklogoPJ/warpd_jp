<?php
$form = array();
if( $this->Session->check('signup') ) {
    $temp = $this->Session->read('signup');
    $form = isset($temp['Payment'])?$temp['Payment']:array();
}
?>
<div class="g_6 contents_header">
    <h3 class="i_22_forms tab_label" style="font-size: 25px;">&nbsp;&nbsp; <?php echo $title_for_layout; ?></h3>
</div>
<div class="contents" style="width: 100%; margin-top: 10px;">
    <div class="grid_wrapper">

        <div class="g_12">
            <div class="progress_wrap">
                <div class="line"></div>
                <ul>
                    <li><span>1</span></li>
                    <li><span>2</span></li>
                    <li><span>3</span></li>
                    <li class="active"><span>4</span></li>
                    <li><span>5</span></li>
                </ul>
            </div>
        </div>

        <div class="g_12">
            <div class="widget_header">
                <h4 class="widget_header_title wwIcon">
                    Payment Information
                </h4>
            </div>
            <?php echo $this->Form->create('Payment', array('url'=>array('controller' => 'Users','action'=> 'signup_payment'),'id' => 'form'));?>
            <div class="widget_contents noPadding">
                <div class="line_grid">
                    <div class="g_3"><span class="label">Name On Card</span></div>
                    <div class="g_9">
                        <?php echo $this->Form->input('cc_name', array('id'=>'cc_name', 'class' => 'simple_form','default'=>isset($form['cc_name']) ? $form['cc_name'] : '','div' => false, 'label' => false)); ?>
                    </div>
                </div>
                <div class="line_grid">
                    <div class="g_3"><span class="label">Address</span></div>
                    <div class="g_9">
                        <?php echo $this->Form->input('cc_address', array('id'=>'cc_address', 'class' => 'simple_field', 'default'=>isset($form['cc_address']) ? $form['cc_address'] : '','div' => false, 'label' => false)); ?>
                    </div>
                </div>
                <div class="line_grid">
                    <div class="g_3"><span class="label">City</span></div>
                    <div class="g_9">
                        <?php echo $this->Form->input('cc_city', array('id'=>'cc_city', 'class' => 'simple_field', 'default'=>isset($form['cc_city']) ? $form['cc_city'] : '','div' => false, 'label' => false)); ?>
                    </div>
                </div>
                <div class="line_grid">
                    <div class="g_3"><span class="label">Last Name</span></div>
                    <div class="g_9">
                        <?php echo $this->Form->input('lname', array('id'=>'lname', 'class' => 'simple_field', 'default'=>isset($form['lname']) ? $form['lname'] : '','div' => false, 'label' => false)); ?>
                    </div>
                </div>
                <div class="line_grid">
                    <div class="g_3"><span class="label">Username</span></div>
                    <div class="g_9">
                        <?php echo $this->Form->input('username', array('id'=>'username', 'class' => 'simple_field', 'default'=>isset($form['username']) ? $form['username'] : '','div' => false, 'label' => false)); ?>
                    </div>
                </div>
                <div class="line_grid">
                    <div class="g_3"><span class="label">Password</span></div>
                    <div class="g_9">
                        <?php echo $this->Form->input('password', array('id'=>'password','type'=>'password' ,'class' => 'simple_field', 'default'=>isset($form['password']) ? $form['password'] : '','div' => false, 'label' => false)); ?>
                    </div>
                </div>
                <div class="line_grid">
                    <div class="g_3"><span class="label">Email</span></div>
                    <div class="g_9">
                        <?php echo $this->Form->input('email', array('id'=>'email', 'class' => 'simple_field', 'default'=>isset($form['email']) ? $form['email'] : '','div' => false, 'label' => false)); ?>
                    </div>
                </div>
                <div class="line_grid">
                    <div class="g_3"><span class="label">Telephone Number</span></div>
                    <div class="g_9">
                        <?php echo $this->Form->input('active_mobile', array('id'=>'active_mobile', 'class' => 'simple_field', 'default'=>isset($form['active_mobile']) ? $form['active_mobile'] : '','div' => false, 'label' => false)); ?>
                    </div>
                </div>
                <div class="line_grid">
                    <div class="g_3"><span class="label">Telephone Number</span></div>
                    <div class="g_9">
                        <?php echo $this->Form->input('active_mobile', array('id'=>'active_mobile', 'class' => 'simple_field', 'default'=>isset($form['active_mobile']) ? $form['active_mobile'] : '','div' => false, 'label' => false)); ?>
                    </div>
                </div>
                <div class="line_grid">
                    <div class="g_3"><span class="label">Telephone Number</span></div>
                    <div class="g_9">
                        <?php echo $this->Form->input('active_mobile', array('id'=>'active_mobile', 'class' => 'simple_field', 'default'=>isset($form['active_mobile']) ? $form['active_mobile'] : '','div' => false, 'label' => false)); ?>
                    </div>
                </div>

                <div class="line_grid">
                    <div class="g_12">
                        <a href="<?php echo $this->Html->url(array('controller' => 'Users', 'action' => 'signup_choose_modules')); ?>" class="simple_buttons"> Back</a>
                        <button type="submit" id="login-btn" class="simple_buttons"> Save & Continue </button>
                    </div>
                </div>

                <?php echo $this->Form->end();?>
            </div>
        </div>

        <div class="g_12 separator"><span></span></div>

    </div>
</div>

<!-- URLs -->
<input type="hidden" id="login-url" value="<?php echo $this->Html->url(array('controller' => 'Users', 'action' => 'login')); ?>" />
<input type="hidden" id="after-login-url" value="<?php echo $this->Html->url(array('controller' => 'Dashboard', 'action' => 'index')); ?>" />

<?php
//echo $this->Html->script('Scripts/signup.js');
?>