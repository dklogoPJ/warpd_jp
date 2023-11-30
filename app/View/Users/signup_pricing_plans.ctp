<?php
$package_fields = array('package_id'=>0);
$mod = $package_fields;
if( $this->Session->check('signup') ) {
    $temp = $this->Session->read('signup');
    $mod = isset($temp['Modules'])?$temp['Modules']:$package_fields;
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
                        <a href="#step-2" class="done">
                            <div class="wizard-step-number">2</div>
                            <div class="wizard-step-label">Admin Account</div>
                            <div class="wizard-step-bar"></div>
                        </a>
                    </li>
                    <li>
                        <a href="#step-3" class="selected">
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

                <div id="step-3">

                    <h3 style="font-size: 16px;">Pricing Plans:</h3>


                    <br />


                    <div class="row-fluid">

                        <div class="span12">

                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation</p>
                            <!--<div id="pricing-header">
                             <h1>30-day Free Trial on All Accounts</h1>
                             <h2>No hidden fees. Cancel at anytime. No risk.</h2>
                         </div> <!-- /pricing-header -->
                            <form action="<?php echo $this->Html->url(array('controller' => 'Users', 'action' => 'signup_pricing_plans')); ?>" id="form" class="form-horizontal" novalidate="novalidate" method="post" accept-charset="utf-8">

                                <div class="pricing-plans plans-4" style="overflow:hidden;">

                                    <?php
                                    foreach($packages as $package){
                                        $pck = $package['Package'];
                                        $price_arr = explode('.',$pck['price']);
                                        $cedi = $price_arr[0];
                                        $pesswas = $price_arr[1];
                                        ?>
                                        <div class="plan-container <?php echo ($pck['best_value'] == 1)? 'best-value':'';?>">
                                            <div class="plan">
                                                <div class="plan-header">

                                                    <div class="plan-title">
                                                        <?php echo $pck['title'] ;?>
                                                    </div> <!-- /plan-title -->

                                                    <div class="plan-price">
                                                        <span class="note">GHS</span><?php echo $cedi ;?><span class="note"><?php echo '.'.$pesswas ;?></span><span class="term"><?php echo $pck['payment_plan'] ;?></span>
                                                    </div> <!-- /plan-price -->

                                                </div> <!-- /plan-header -->

                                                <div class="plan-features">
                                                    <?php echo $pck['features'] ;?>
                                                </div> <!-- /plan-features -->

                                                <div class="plan-actions">
                                                    <a href="javascript:;" class="btn package_type_btn" data-id="<?php echo $pck['id'] ;?>">Select</a>
                                                </div> <!-- /plan-actions -->

                                            </div> <!-- /plan -->
                                        </div> <!-- /plan-container -->
                                        <?php
                                    }
                                    ?>

                                </div>

                                <input type="hidden" name="package_id" id='package_id' value="<?php echo $mod['package_id']?>"  />

                            </form>

                            <div id="package_error" style="display: none;">
                                <?php echo $this->Message->msg('Required','You need to select at least one plan','error'); ?>
                            </div>

                        </div> <!-- /span12 -->

                    </div> <!-- /row-fluid -->


                </div> <!-- /step -->

            </div> <!-- /wizard -->

        </div> <!-- /widget-content -->

        <div class="widget-toolbar">

            <a href="<?php echo $this->Html->url(array('controller' => 'Users', 'action' => 'signup_administrator')); ?>" class="buttonPrevious btn" style="width:10%;">Previous</a>
            <button type='button' id="submit-btn" class="buttonNext btn btn-tertiary btn-inverse" style="width:12%;">Next</button>
            <a href="<?php echo $this->Html->url(array('controller' => 'Users', 'action' => 'signup_cancel')); ?>" class="buttonPrevious btn" style="width:10%;"> Cancel</a>

        </div><!-- /.widget-toolbar -->

    </div> <!-- /widget -->

</div> <!-- /.span12 -->

<!-- URLs -->
<input type="hidden" id="login-url" value="<?php echo $this->Html->url(array('controller' => 'Users', 'action' => 'login')); ?>" />
<input type="hidden" id="after-login-url" value="<?php echo $this->Html->url(array('controller' => 'Dashboard', 'action' => 'index')); ?>" />

<?php
    echo $this->Html->script('Scripts/signup_pricing_plan.js');
?>