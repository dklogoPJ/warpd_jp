<?php
$form = array();
if( $this->Session->check('signup') ) {
    $data = $this->Session->read('signup');
    //echo debug($data);
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
                <a href="#step-3" class="done">
                    <div class="wizard-step-number">3</div>
                    <div class="wizard-step-label">Pricing Plans</div>
                    <div class="wizard-step-bar"></div>
                </a>
            </li>
            <li>
                <a href="#step-4" class="selected">
                    <div class="wizard-step-number">4</div>
                    <div class="wizard-step-label">Review Info</div>
                    <div class="wizard-step-bar"></div>
                </a>
            </li>
        </ul>

        <div id="step-4">

            <h3 style="font-size: 16px;">Review Info:</h3>


            <br />


            <div class="row-fluid">

                <div class="span12">

                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation</p>

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

                    <form action="<?php echo $this->Html->url(array('controller' => 'Users', 'action' => 'do_signup')); ?>" id="form" class="form-horizontal" novalidate="novalidate" method="post" accept-charset="utf-8">

                        <div class="span4">
                            <div class="widget widget-table">
                                <div class="widget-header header-deep-blue">
                                    <h3>Business Details</h3>
                                    <div class="widget-actions">
                                        <a href="<?php echo $this->Html->url(array('controller' => 'Users', 'action' => 'signup_company')); ?>" class="btn">Edit</a>
                                    </div> <!-- /.widget-actions -->
                                </div> <!-- /.widget-header -->
                                <div class="widget-content">
                                    <table class="table table-bordered table-striped">
                                        <tr>
                                            <td>Company Name</td>
                                            <td><?php echo  $data['Company']['name']; ?></td>
                                        </tr>
                                       <!-- <tr>
                                            <td>City</td>
                                            <td><?php /*echo  $data['Company']['city']; */?></td>
                                        </tr>-->
                                        <tr>
                                            <td>Location</td>
                                            <td><?php echo  $data['Company']['location']; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Address</td>
                                            <td><?php echo  $data['Company']['address']; ?></td>
                                        </tr>
                                        <!--<tr>
                                            <td>Postal Code</td>
                                            <td><?php /*echo  $data['Company']['postal_code']; */?></td>
                                        </tr>-->
                                        <tr>
                                            <td>Telephone</td>
                                            <td><?php echo  $data['Company']['telephone']; ?></td>
                                        </tr>
                                        <!--<tr>
                                            <td>Country</td>
                                            <td><?php /*echo  $data['Company']['country']; */?></td>
                                        </tr>-->
                                    </table>
                                </div> <!-- /.widget-content -->
                            </div> <!-- /.widget -->
                        </div><!-- /span4 -->

                        <div class="span4">
                            <div class="widget widget-table">
                                <div class="widget-header header-deep-blue">
                                    <h3>Admin Account </h3>
                                    <div class="widget-actions">
                                        <a href="<?php echo $this->Html->url(array('controller' => 'Users', 'action' => 'signup_administrator')); ?>" class="btn">Edit</a>
                                    </div> <!-- /.widget-actions -->
                                </div> <!-- /.widget-header -->
                                <div class="widget-content">
                                    <table class="table table-bordered table-striped">
                                        <tr>
                                            <td>Title</td>
                                            <td><?php echo  $data['User']['title']; ?></td>
                                        </tr>
                                        <tr>
                                            <td>First Name</td>
                                            <td><?php echo  $data['User']['fname']; ?></td>
                                        </tr>
                                        <!--<tr>
                                            <td>Middle Name</td>
                                            <td><?php /*echo  $data['User']['mname']; */?></td>
                                        </tr>-->
                                        <tr>
                                            <td>Last Name</td>
                                            <td><?php echo  $data['User']['lname']; ?></td>
                                        </tr>
                                        <tr>
                                            <td>User Name</td>
                                            <td><?php echo  $data['User']['username']; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Email</td>
                                            <td><?php echo  $data['User']['email']; ?></td>
                                        </tr>
                                        <!--<tr>
                                            <td>Active Mobile</td>
                                            <td><?php /*echo  $data['User']['active_mobile']; */?></td>
                                        </tr>-->
                                    </table>
                                </div> <!-- /.widget-content -->
                            </div> <!-- /.widget -->
                        </div><!-- /span4 -->

                        <div class="span4">
                            <div class="widget widget-table">
                                <div class="widget-header header-deep-blue">
                                    <h3>Pricing Plan</h3>
                                    <div class="widget-actions">
                                        <a href="<?php echo $this->Html->url(array('controller' => 'Users', 'action' => 'signup_pricing_plans')); ?>" class="btn">Edit</a>
                                    </div> <!-- /.widget-actions -->
                                </div> <!-- /.widget-header -->
                                <div class="widget-content">
                                    <table class="table table-bordered table-striped">
                                        <tr class="even gradeC">
                                            <td>Title</td>
                                            <td><?php echo $packages[0]['Package']['title']; ?></td>
                                        </tr>

                                        <tr class="odd gradeA">
                                            <td>Price</td>
                                            <td><?php echo 'GHc '.$packages[0]['Package']['price']; ?></td>
                                        </tr>
                                        <tr class="even gradeC">
                                            <td>Payment Plan</td>
                                            <td><?php echo $packages[0]['Package']['payment_plan']; ?></td>
                                        </tr>
                                    </table>
                                </div> <!-- /.widget-content -->
                            </div> <!-- /.widget -->
                        </div><!-- /span4 -->
                    </form>

                </div> <!-- /span12 -->

            </div> <!-- /row-fluid -->


        </div> <!-- /step -->

    </div> <!-- /wizard -->

</div> <!-- /widget-content -->

<div class="widget-toolbar">

    <a href="<?php echo $this->Html->url(array('controller' => 'Users', 'action' => 'signup_pricing_plans')); ?>" class="buttonPrevious btn" style="width:10%;">Previous</a>
    <button type='button' id="submit-btn" class="buttonNext btn btn-tertiary btn-inverse" style="width:12%;">Finish</button>
    <a href="<?php echo $this->Html->url(array('controller' => 'Users', 'action' => 'signup_cancel')); ?>" class="buttonPrevious btn" style="width:10%;"> Cancel</a>

</div><!-- /.widget-toolbar -->

</div> <!-- /widget -->

</div> <!-- /.span12 -->

<!-- URLs -->
<input type="hidden" id="login-url" value="<?php echo $this->Html->url(array('controller' => 'Users', 'action' => 'login')); ?>" />
<input type="hidden" id="after-login-url" value="<?php echo $this->Html->url(array('controller' => 'Dashboard', 'action' => 'index')); ?>" />

<?php
    echo $this->Html->script('Scripts/signup_review.js');
?>