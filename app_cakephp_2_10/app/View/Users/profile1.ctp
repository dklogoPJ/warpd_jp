<?php
/**
 * This is the tanks_setup.ctp
 * @access public
 * @version 1.0
 */
?>
<?php
/**
 * This is the tanks_setup.ctp
 * @access public
 * @version 1.0
 */
?>
<?php echo $this->element('navigation'); ?>

<div class="contents">
    <div class="grid_wrapper">

        <div class="g_6 contents_header">
            <h3 class="i_22_settings tab_label"> Profile</h3>

            <div><span class="label">Make changes to your profile</span></div>
        </div>

        <div class="g_12">
            <div class="widget_header">
                <h4 class="widget_header_title wwIcon">

                </h4>
            </div>
            <?php echo $this->Form->create('User', array('url' => array('controller' => 'Users', 'action' => 'signup_administrator'), 'id' => 'form'));?>
            <div class="widget_contents noPadding">
                <div class="line_grid_2">
                    <div class="g_3"><span class="label">Title</span></div>
                    <div class="g_9">
                        <?php echo $this->Form->input('title', array('id' => 'title', 'class' => 'simple_form', 'options' => $this->Select->generateTitles(), 'default' => $authUser['title'], 'div' => false, 'label' => false)); ?>
                    </div>
                </div>
                <div class="line_grid_2">
                    <div class="g_3"><span class="label">First Name</span></div>
                    <div class="g_9">
                        <?php echo $this->Form->input('fname', array('id' => 'fname', 'class' => 'simple_field', 'default' => $authUser['fname'], 'required', 'div' => false, 'label' => false)); ?>
                    </div>
                </div>
                <div class="line_grid_2">
                    <div class="g_3"><span class="label">Middle Name</span></div>
                    <div class="g_9">
                        <?php echo $this->Form->input('mname', array('id' => 'mname', 'class' => 'simple_field', 'default' => $authUser['mname'], 'div' => false, 'label' => false)); ?>
                    </div>
                </div>
                <div class="line_grid_2">
                    <div class="g_3"><span class="label">Last Name</span></div>
                    <div class="g_9">
                        <?php echo $this->Form->input('lname', array('id' => 'lname', 'class' => 'simple_field', 'default' => $authUser['lname'], 'required', 'div' => false, 'label' => false)); ?>
                    </div>
                </div>
                <div class="line_grid_2">
                    <div class="g_3"><span class="label">Username</span></div>
                    <div class="g_9">
                        <?php echo $this->Form->input('username', array('id' => 'username', 'class' => 'simple_field', 'default' => $authUser['username'], 'required', 'div' => false, 'label' => false)); ?>
                    </div>
                </div>
                <div class="line_grid_2">
                    <div class="g_3"><span class="label">Password</span></div>
                    <div class="g_9">
                        <?php echo $this->Form->input('password', array('id' => 'password', 'type' => 'password', 'class' => 'simple_field', 'div' => false, 'label' => false)); ?>
                    </div>
                </div>
                <div class="line_grid_2">
                    <div class="g_3"><span class="label">Email</span></div>
                    <div class="g_9">
                        <?php echo $this->Form->input('email', array('id' => 'email', 'class' => 'simple_field', 'default' => $authUser['email'], 'required', 'div' => false, 'label' => false)); ?>
                    </div>
                </div>
                <div class="line_grid_2">
                    <div class="g_3"><span class="label">Telephone Number</span></div>
                    <div class="g_9">
                        <?php echo $this->Form->input('active_mobile', array('id' => 'active_mobile', 'class' => 'simple_field', 'default' => $authUser['active_mobile'], 'required', 'div' => false, 'label' => false)); ?>
                    </div>
                </div>

                <div class="line_grid_2">
                    <div class="g_12">
                        <button type="submit" id="login-btn" class="simple_buttons"> Save</button>
                    </div>
                </div>

                <?php echo $this->Form->end();?>
            </div>
        </div>

        <!-- Separator -->
        <div class="g_12 separator"><span></span></div>
    </div>

</div>
