<?php
$form = array();
if( $this->Session->check('signup') ) {
    $temp = $this->Session->read('signup');
    $form = $temp['Company'];
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
                        <a href="#step-1" class="selected">
                            <div class="wizard-step-number">1</div>
                            <div class="wizard-step-label">Business Details</div>
                            <div class="wizard-step-bar"></div>
                        </a>
                    </li>
                    <li>
                        <a href="#step-2" class="">
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

                <div id="step-1">

                    <h3 style="font-size: 16px;">Business Details:</h3>


                    <br />


                    <div class="row-fluid">

                        <div class="span6">

                            <form action="<?php echo $this->Html->url(array('controller' => 'Users', 'action' => 'signup_company')); ?>" id="form" class="form-horizontal" novalidate="novalidate" method="post" accept-charset="utf-8">

                                <div class="control-group">
                                    <label class="control-label">* Company Name:</label>
                                    <div class="controls">
                                        <input name="name" id="name" class="input-xlarge" type="text" value="<?php echo isset($form['name']) ? $form['name'] : '';?>">
                                    </div>
                                </div>

                                <!--<div class="control-group">
                                    <label class="control-label">* City:</label>
                                    <div class="controls">
                                        <input name="city" id="city" class="input-xlarge" type="text" value="<?php /*echo isset($form['city']) ? $form['city'] : '';*/?>">
                                    </div>
                                </div>-->

                                <div class="control-group">
                                    <label class="control-label">* Location:</label>
                                    <div class="controls">
                                        <input name="location" id="location" class="input-xlarge" type="text" value="<?php echo isset($form['location']) ? $form['location'] : '';?>">
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label">* Address:</label>
                                    <div class="controls">
                                        <input name="address" id="address" class="input-xlarge"  type="text" value="<?php echo isset($form['address']) ? $form['address'] : '';?>">
                                    </div>
                                </div>

                                <!--<div class="control-group">
                                    <label class="control-label">* Postal Code:</label>
                                    <div class="controls">
                                        <input name="postal_code" id="postal_code" class="input-xlarge" type="text" value="<?php /*echo isset($form['postal_code']) ? $form['postal_code'] : '';*/?>">
                                    </div>
                                </div>-->

                                <div class="control-group">
                                    <label class="control-label">* Telephone:</label>
                                    <div class="controls">
                                        <input name="telephone" id="telephone" class="input-xlarge"  type="text" value="<?php echo isset($form['telephone']) ? $form['telephone'] : '';?>">
                                    </div>
                                </div>

                               <!-- <div class="control-group">
                                    <label class="control-label">* Country:</label>
                                    <div class="controls">
                                        <input name="country" id="country" class="input-xlarge"  type="text" value="<?php /*echo isset($form['country']) ? $form['country'] : '';*/?>">
                                    </div>
                                </div>-->

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

            <!--<a href="#" class="buttonPrevious btn buttonDisabled" style="width:10%;">Previous</a>-->
            <button type='button' id="submit-btn" class="buttonNext btn btn-tertiary btn-inverse" style="width:12%;">Next</button>
            <a href="<?php echo $this->Html->url(array('controller' => 'Users', 'action' => 'signup_cancel')); ?>" class="buttonPrevious btn" style="width:10%;"> Cancel</a>

        </div><!-- /.widget-toolbar -->

    </div> <!-- /widget -->

</div> <!-- /.span12 -->


<!-- URLs -->
<input type="hidden" id="login-url" value="<?php echo $this->Html->url(array('controller' => 'Users', 'action' => 'login')); ?>" />
<input type="hidden" id="after-login-url" value="<?php echo $this->Html->url(array('controller' => 'Dashboard', 'action' => 'index')); ?>" />

<?php
    echo $this->Html->script('Scripts/signup_company.js');
?>