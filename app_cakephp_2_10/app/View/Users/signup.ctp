
<div class="container">

    <div id="page-title" class="clearfix">

        <h1>Registration</h1>

    </div> <!-- /.page-title -->

    <div class="row">

        <div class="span12">

            <div class="widget">

                <div class="widget-header">
                    <h3>
                        <i class="icon-magic"></i>
                        Steps
                    </h3>
                </div> <!-- /widget-header -->

                <div class="widget-content">

                    <form action="#" method="POST" class="form-horizontal">

                        <div id="wizard" class="swMain">

                            <ul class="wizard-steps">
                                <li>
                                    <a href="#step-1" class="">
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

                                <h3>Business Details:</h3>


                                <br />


                                <div class="row-fluid">

                                    <div class="span6">
                                        <form action="" id="business-form" class="form-horizontal" novalidate="novalidate">

                                            <div class="control-group">
                                                <label class="control-label" for="name">* Business Name:</label>
                                                <div class="controls">
                                                    <input type="text" id="name" name="name" class="input-xlarge" />
                                                </div>
                                            </div>

                                            <div class="control-group">
                                                <label class="control-label" for="city">* City:</label>
                                                <div class="controls">
                                                    <input type="text" id="city" name="city" class="input-xlarge" />
                                                </div>
                                            </div>

                                            <div class="control-group">
                                                <label class="control-label" for="location">* Location:</label>
                                                <div class="controls">
                                                    <input type="text" id="location" name="location" class="input-xlarge" />
                                                </div>
                                            </div>

                                            <div class="control-group">
                                                <label class="control-label" for="postal_code">* Address:</label>
                                                <div class="controls">
                                                    <input type="text" id="postal_code" name="postal_code" class="input-xlarge" />
                                                </div>
                                            </div>

                                            <div class="control-group">
                                                <label class="control-label" for="postal_address">* Postal Code:</label>
                                                <div class="controls">
                                                    <input type="text" id="postal_address" name="postal_address" class="input-xlarge" />
                                                </div>
                                            </div>

                                            <div class="control-group">
                                                <label class="control-label" for="tel_num1">* Telephone 1:</label>
                                                <div class="controls">
                                                    <input type="text" id="tel_num1" name="tel_num1" class="input-xlarge" />
                                                </div>
                                            </div>

                                            <div class="control-group">
                                                <label class="control-label" for="tel_num2">Telephone 2:</label>
                                                <div class="controls">
                                                    <input type="text" id="tel_num2" name="tel_num2" class="input-xlarge" />
                                                </div>
                                            </div>

                                            <div class="control-group">
                                                <label class="control-label" for="country">* Country:</label>
                                                <div class="controls">
                                                    <input type="text" id="country" name="country" class="input-xlarge" />
                                                </div>
                                            </div>

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


                            <div id="step-2">

                                <h3>Admin Account:</h3>

                                <br />


                                <div class="row-fluid">

                                    <div class="span6">

                                        <form action="" id="admin-user-form" class="form-horizontal" novalidate="novalidate">

                                            <div class="control-group">
                                                <label class="control-label" for="title">Tilte:</label>
                                                <div class="controls">
                                                    <select id="title" name="title">
                                                       <?php $this->Select->generateTitles(true); ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="control-group">
                                                <label class="control-label" for="fname">* First Name:</label>
                                                <div class="controls">
                                                    <input type="text" id="fname" name="fname" class="input-xlarge" />
                                                </div>
                                            </div>

                                            <div class="control-group">
                                                <label class="control-label" for="mname">Middle Name:</label>
                                                <div class="controls">
                                                    <input type="text" id="mname" name="mname" class="input-xlarge" />
                                                </div>
                                            </div>

                                            <div class="control-group">
                                                <label class="control-label" for="lname">* Last Name:</label>
                                                <div class="controls">
                                                    <input type="text" id="lname" name="lname" class="input-xlarge" />
                                                </div>
                                            </div>

                                            <div class="control-group">
                                                <label class="control-label" for="username">* Username:</label>
                                                <div class="controls">
                                                    <input type="text" id="username" name="username" class="input-xlarge" />
                                                </div>
                                            </div>

                                            <div class="control-group">
                                                <label class="control-label" for="password">* Password:</label>
                                                <div class="controls">
                                                    <input type="password" id="password" name="password" class="input-xlarge" />
                                                </div>
                                            </div>

                                            <div class="control-group">
                                                <label class="control-label" for="email">* Email:</label>
                                                <div class="controls">
                                                    <input type="email" id="email" name="email" class="input-xlarge" />
                                                </div>
                                            </div>

                                            <div class="control-group">
                                                <label class="control-label" for="active_mobile">* Active Telephone Number:</label>
                                                <div class="controls">
                                                    <input type="text" id="active_mobile" name="active_mobile" class="input-xlarge" />
                                                </div>
                                            </div>

                                        </form>


                                    </div> <!-- /span6 -->

                                    <div class="span5 offset1">

                                        <div class="well">
                                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>


                                        </div>

                                    </div> <!-- /span6 -->


                                </div> <!-- /row-fluid -->


                            </div> <!-- /step -->


                            <div id="step-3">

                                <h3>Pricing Plans:</h3>

                                <br />

                                <div class="row-fluid">

                                    <div class="span12">
                                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation</p>
                                        <!--<div id="pricing-header">
                                            <h1>30-day Free Trial on All Accounts</h1>
                                            <h2>No hidden fees. Cancel at anytime. No risk.</h2>
                                        </div> <!-- /pricing-header -->

                                        <div class="pricing-plans plans-4" style="overflow:hidden;">

                                            <div class="plan-container">
                                                <div class="plan">
                                                    <div class="plan-header">

                                                        <div class="plan-title">
                                                            Starter
                                                        </div> <!-- /plan-title -->

                                                        <div class="plan-price">
                                                            <span class="note">GHC</span>35<span class="term">Per Month</span>
                                                        </div> <!-- /plan-price -->

                                                    </div> <!-- /plan-header -->

                                                    <div class="plan-features">
                                                        <ul>
                                                            <li><strong>Free</strong> Setup</li>
                                                            <li><strong>Recieve</strong> SMS</li>
                                                            <li><strong>No</strong> Broadcast</li>
                                                            <li><strong>No</strong> Loyalty</li>
                                                            <li><strong>Get</strong>Limited Support</li>
                                                        </ul>
                                                    </div> <!-- /plan-features -->

                                                    <div class="plan-actions">
                                                        <a href="javascript:;" class="btn">
                                                            Select
                                                            <input id="Field" name="Field" type="checkbox" class="" value='1'  style="margin: 0 0 0 15px;"/>
                                                        </a>
                                                    </div> <!-- /plan-actions -->

                                                </div> <!-- /plan -->
                                            </div> <!-- /plan-container -->

                                            <div class="plan-container best-value">
                                                <div class="plan">
                                                    <div class="plan-header">

                                                        <div class="plan-title">
                                                            Business
                                                        </div> <!-- /plan-title -->

                                                        <div class="plan-price">
                                                            <span class="note">GHC</span>75<span class="term">Per Month</span>
                                                        </div> <!-- /plan-price -->

                                                    </div> <!-- /plan-header -->

                                                    <div class="plan-features">
                                                        <ul>
                                                            <li><strong>Free</strong> Setup</li>
                                                            <li><strong>Recieve/Send</strong> SMS</li>
                                                            <li><strong>Get</strong> Broadcast</li>
                                                            <li><strong>No</strong> Loyalty</li>
                                                            <li><strong>Get</strong> Support</li>
                                                        </ul>
                                                    </div> <!-- /plan-features -->

                                                    <div class="plan-actions">
                                                        <a href="javascript:;" class="btn">
                                                            Select
                                                            <input id="Field" name="Field" type="checkbox" class="" value='1'  style="margin: 0 0 0 15px;"/>
                                                        </a>
                                                    </div> <!-- /plan-actions -->

                                                </div> <!-- /plan -->
                                            </div> <!-- /plan-container -->

                                            <div class="plan-container">
                                                <div class="plan">
                                                    <div class="plan-header">

                                                        <div class="plan-title">
                                                            Enterprise
                                                        </div> <!-- /plan-title -->

                                                        <div class="plan-price">
                                                            <span class="note">GHC</span>125<span class="term">Per Month</span>
                                                        </div> <!-- /plan-price -->

                                                    </div> <!-- /plan-header -->

                                                    <div class="plan-features">
                                                        <ul>
                                                            <li><strong>Free</strong> Setup</li>
                                                            <li><strong>Recieve/Send</strong> SMS</li>
                                                            <li><strong>Get</strong> Broadcast</li>
                                                            <li><strong>Get</strong> Loyalty</li>
                                                            <li><strong>Get Unlimited</strong> Support</li>
                                                        </ul>
                                                    </div> <!-- /plan-features -->

                                                    <div class="plan-actions">
                                                        <a href="javascript:;" class="btn">
                                                            Select
                                                            <input id="Field" name="Field" type="checkbox" class="" value='1'  style="margin: 0 0 0 15px;"/>
                                                        </a>
                                                    </div> <!-- /plan-actions -->

                                                </div> <!-- /plan -->

                                            </div> <!-- /plan-container -->

                                        </div>


                                        <br><br>


                                    </div> <!-- /span12 -->

                                    <!--<div class="span5 offset1">

                                        <div class="well">
                                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>

                                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>

                                        </div>

                                    </div>--> <!-- /span6 -->


                                </div> <!-- /row-fluid -->


                            </div> <!-- /step -->


                            <div id="step-4">

                                <h3>Review Info:</h3>

                                <br />


                                <div class="row-fluid">

                                    <div class="span6">

                                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>

                                        <br />

                                        <button class="btn btn-primary btn-large">Finish</button>



                                    </div> <!-- /span6 -->


                                </div> <!-- /row-fluid -->

                            </div> <!-- /step -->



                        </div> <!-- /wizard -->

                    </form>


                </div> <!-- /widget-content -->

            </div> <!-- /widget -->

        </div> <!-- /.span12 -->



    </div> <!-- /row -->

</div> <!-- /.container -->