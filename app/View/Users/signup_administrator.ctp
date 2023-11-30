<?php
$form = array();
if( $this->Session->check('signup') ) {
    $temp = $this->Session->read('signup');
    $form = isset($temp['User'])?$temp['User']:array();
}
?>
<div class="span12">

    <div class="widget">

        <div class="widget-header">
            <h3>
                <i class="icon-magic"></i>
                Registration
            </h3>
        </div> <!-- /widget-header -->

        <div class="widget-content">

            <div id="wizard" class="swMain">

                <ul class="wizard-steps">
                    <li>
                        <a href="#step-1" class="done">
                            <div class="wizard-step-number">1</div>
                            <div class="wizard-step-label">Business Details</div>
                            <div class="wizard-step-bar"></div>
                        </a>
                    </li>
                    <li>
                        <a href="#step-2" class="selected">
                            <div class="wizard-step-number">2</div>
                            <div class="wizard-step-label">Admin Account</div>
                            <div class="wizard-step-bar"></div>
                        </a>
                    </li>
                    <li>
                        <a href="#step-3" class="">
                            <div class="wizard-step-number">3</div>
                            <div class="wizard-step-label">Pricing Plans</div>
                            <div class="wizard-step-bar"></div>
                        </a>
                    </li>
                    <li>
                        <a href="#step-4" class="">
                            <div class="wizard-step-number">4</div>
                            <div class="wizard-step-label">Review Info</div>
                            <div class="wizard-step-bar"></div>
                        </a>
                    </li>
                </ul>

                <div id="step-2">

                    <h3 style="font-size: 16px;">Admin Account:</h3>


                    <br />


                    <div class="row-fluid">

                        <div class="span6">

                            <?php
                            if($this->Session->check('flash_msg')){
                                $flash_msg = $this->Session->read('flash_msg');
                                $msg = $flash_msg['msg'];
                                ?>
                                <div class="alert alert-error">
                                    <!--<a class="close" data-dismiss="alert" href="#">×</a>-->
                                    <h4 class="alert-heading">Error!</h4>
                                    <?php echo $msg; ?>
                                </div>
                                <?php
                                $controller->Session->delete('flash_msg');
                            }
                            ?>

                            <form action="<?php echo $this->Html->url(array('controller' => 'Users', 'action' => 'signup_administrator')); ?>" id="form" class="form-horizontal" novalidate="novalidate" method="post" accept-charset="utf-8">

                                <div class="control-group">
                                    <label class="control-label">Title:</label>
                                    <div class="controls">
                                        <select name="title" id="title" class="input-xlarge">
                                            <?php $this->Select->generateTitles(true, isset($form['title']) ? $form['title'] : ''); ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label">* First Name:</label>
                                    <div class="controls">
                                        <input name="fname" id="fname" class="input-xlarge" type="text" value="<?php echo isset($form['fname']) ? $form['fname'] : '';?>">
                                    </div>
                                </div>

                                <!--<div class="control-group">
                                    <label class="control-label">Middle Name:</label>
                                    <div class="controls">
                                        <input name="mname" id="mname" class="input-xlarge" type="text" value="<?php /*echo isset($form['mname']) ? $form['mname'] : '';*/?>">
                                    </div>
                                </div>-->

                                <div class="control-group">
                                    <label class="control-label">* Last Name:</label>
                                    <div class="controls">
                                        <input name="lname" id="lname" class="input-xlarge"  type="text" value="<?php echo isset($form['lname']) ? $form['lname'] : '';?>">
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label">* Username:</label>
                                    <div class="controls">
                                        <input name="username" id="username" class="input-xlarge" type="text" value="<?php echo isset($form['username']) ? $form['username'] : '';?>">
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label">* Password:</label>
                                    <div class="controls">
                                        <input name="password" id="password" class="input-xlarge"  type="password" value="<?php echo isset($form['password']) ? $form['password'] : '';?>">
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label">* Email:</label>
                                    <div class="controls">
                                        <input name="email" id="email" class="input-xlarge"  type="text" value="<?php echo isset($form['email']) ? $form['email'] : '';?>">
                                    </div>
                                </div>
                                <!--
                                <div class="control-group">
                                    <label class="control-label">* Telephone Number:</label>
                                    <div class="controls">
                                        <input name="active_mobile" id="active_mobile" class="input-xlarge"  type="text" value="<?php /*echo isset($form['active_mobile']) ? $form['active_mobile'] : '';*/?>">
                                    </div>
                                </div>-->

                                <input name="user_type" id="user_type" type="hidden" value="bdc">
                                <input name="user_level" id="user_level" type="hidden" value="admin">


                            </form>

                        </div> <!-- /span6 -->

                        <div class="span5 offset1">

                            <div class="well">
                                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>

                                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>

                            </div>

                        </div> <!-- /span6 -->


                    </div> <!-- /row-fluid -->

                </div> <!-- /step -->

            </div> <!-- /wizard -->

        </div> <!-- /widget-content -->

        <div class="widget-toolbar">

            <a href="<?php echo $this->Html->url(array('controller' => 'Users', 'action' => 'signup_company')); ?>" class="buttonPrevious btn" style="width:10%;">Previous</a>
            <button type='button' id="submit-btn" class="buttonNext btn btn-tertiary btn-inverse" style="width:12%;">Next</button>
            <a href="<?php echo $this->Html->url(array('controller' => 'Users', 'action' => 'signup_cancel')); ?>" class="buttonPrevious btn" style="width:10%;"> Cancel</a>

        </div><!-- /.widget-toolbar -->

    </div> <!-- /widget -->

</div> <!-- /.span12 -->

<!-- URLs -->
<input type="hidden" id="login-url" value="<?php echo $this->Html->url(array('controller' => 'Users', 'action' => 'login')); ?>" />
<input type="hidden" id="after-login-url" value="<?php echo $this->Html->url(array('controller' => 'Dashboard', 'action' => 'index')); ?>" />

<?php
echo $this->Html->script('Scripts/signup_admin.js');
?>