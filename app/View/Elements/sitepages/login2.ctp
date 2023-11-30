<?php //debug($company_profile);
    $comp_id = isset($company_profile)? $company_profile['id'] : '';
    $comp_type = isset($company_profile)? $company_profile['comp_type'] : '';
?>
<!--<div class="bLogo"></div>-->
<header id="top">
    <div class="wrapper">

        <a href="#" id="logo">
            <!--<img src="img/logo.png" alt="Shell Ghana" width="200" height="75" />-->
            <?php /*echo $this->Html->image("sitepages/$company_key/logo.png", array('width' => '200', 'height' => '75', 'alt' => $company_profile['name'])); */?>
        </a>

        <!--<div id="social">
            <a href="tel:+233 548254389" class="tel"><?php /*echo $company_profile['telephone'] */?></a>
        </div>--><!-- .social -->

        <div class="clear"></div>

        <!--<nav id="nav-main">
            <div id="nav-main-container">
                <div class="left-curves">
                    <a href="#" class="home"></a>
                </div>
                <div class="menu-wrapper">
                    <ul id="main-menu">
                        <!--<li class="current-menu-item"><a href="#">Home</a></li>
                        <li><a href="#">About us</a></li>
                        <li><a href="#">Industry News</a></li>
                        <li><a href="#">Support</a></li>
                    </ul>
                </div>
            </div>
        </nav>-->


    </div>
</header><!-- .top -->

<div class="preview-boxes">
    <div class="wrapper clear columns">

        <section class="item find-app fleft column column_25">
            <div class="column_content">
                <h6 class="txt-sensumi" style="color:#52759A !important">WARP-D Login Form</h6>
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
                <ul>
                    <li>
                        <?php echo $this->Form->input('username', array('id' => 'username', 'class' => 'login username-field', 'placeholder' => 'Username', 'div' => false, 'label' => false)); ?>
                    </li>
                    <li>
                        <?php echo $this->Form->input('password', array('type' => 'password', 'id' => 'password', 'class' => 'login password-field', 'placeholder' => 'Password', 'div' => false, 'label' => false)); ?>
                    </li>
                    <li class="submit">
                        <input type="submit" value="LOGIN" class="txt-sensumi"/>

                        <input type="button" id="sign_up" value="SIGN UP" style="float: right" class="txt-sensumi"/>
                    </li>
                </ul>
                <?php echo $this->Form->end();?>
            </div>

        </section>

        <section class="item latest fleft column column_25">
            <div class="column_content">
                <h6 class="txt-sensumi" style="color:#52759A !important">About WARP-D</h6>
                <aside class="vignette vignette2 radius10">
                    <a href="http://rtheconsult.com/support/team_profile.php" target="_blank">
                        <?php echo $this->Html->image("sitepages/$company_key/aboutus.png", array('width' => '72', 'height' => '72', 'alt' => '')); ?>
                    </a>
                </aside>
                <article>
                    <p class="title">
                        <a href="http://rtheconsult.com/support/team_profile.php" target="_blank">Want to know more
                            ?</a>
                    </p>

                    <div class="content">
                        <p>Get to know a little bit more about WARP-D</p>
                        <a href="#" class="more-red-arrow"></a>
                    </div>
                </article>
            </div>
        </section>
        <!-- .latest-news -->

        <section class="item latest fleft column column_25">
            <div class="column_content">
                <h6 class="txt-sensumi" style="color:#52759A !important">Support</h6>
                <aside class="vignette vignette2 radius10">
                    <!-- <img src="img/examples/icon-photo.png" alt="" width="72" height="72" />-->
                    <a href="http://rtheconsult.com/support/support.php" target="_blank">
                        <?php echo $this->Html->image("sitepages/$company_key/training.png", array('width' => '72', 'height' => '72', 'alt' => '')); ?>
                    </a>
                </aside>
                <article>
                    <p class="title">
                        <a href="http://rtheconsult.com/support/support.php" target="_blank">Need help ?</a>
                    </p>

                    <div class="content">
                        <p>Access support to make your experience easier.</p>
                        <a href="#" class="more-red-arrow"></a>
                    </div>
                </article>
            </div>
        </section>
        <!-- .latest-news -->

        <section class="item latest fleft column column_25">
            <div class="column_content">
                <h6 class="txt-sensumi" style="color:#52759A !important">Contact Us</h6>
                <aside class="vignette vignette2 radius10">
                    <a href="http://rtheconsult.com/support/contact_us.php" target="_blank">
                        <?php echo $this->Html->image("sitepages/$company_key/contact.png", array('width' => '72', 'height' => '72', 'alt' => '')); ?>
                    </a>
                </aside>
                <article>
                    <p class="title">
                        <a href="http://rtheconsult.com/support/contact_us.php" target="_blank">Want to reach us ?</a>
                    </p>

                    <div class="content">
                        <p>Send your concerns and feedback,we are here for you.</p>
                        <a href="#" class="more-red-arrow"></a>
                    </div>
                </article>
            </div>
        </section>
        <!-- .latest-news -->

        <!-- Sign up Form -->
        <div style="display: none;">
            <div id="signup-form-window" style="width: 600px;">
                <div class="content" style="padding: 20px;">
                    <fieldset style="border: 1px solid #ccc; padding: 15px; margin-bottom: 20px;">
                        <legend style="font-size: 14px; font-weight: bolder;">Create <?php echo $comp_type; ?> account for the demo.</legend>
                        <section class="item find-app fleft column column_25">
                            <div class="column_content">
                                <?php echo $this->Form->create('User', array('id' => 'signup-form','class'=>'form-horizontal'));?>
                                <div id="signup_msg" style="">
                                    <?php echo $this->Message->msg('Login', $this->Session->flash(), 'error'); ?>
                                </div>
                                <table>
                                    <tr>
                                        <td>First Name:</td>
                                        <td><?php echo $this->Form->input('fname', array('id' => 'fname', 'class' => '', 'value' =>'', 'div' => false, 'label' => false)); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Middle Name:</td>
                                        <td><?php echo $this->Form->input('mname', array('id' => 'mname', 'class' => '', 'value' => '', 'div' => false, 'label' => false)); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Last Name:</td>
                                        <td><?php echo $this->Form->input('lname', array('id' => 'lname', 'class' => '', 'value' => '', 'div' => false, 'label' => false)); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Username:</td>
                                        <td><?php echo $this->Form->input('username', array('id' => 'username', 'class' => '', 'placeholder' => 'Username', 'div' => false, 'label' => false));?></td>
                                    </tr>
                                    <tr>
                                        <td>Password:</td>
                                        <td> <?php echo $this->Form->input('password', array('id' => 'password', 'type' => 'password', 'class' => '', 'div' => false, 'label' => false)); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Email:</td>
                                        <td><?php echo $this->Form->input('email', array('id' => 'email', 'class' => '', 'div' => false, 'label' => false)); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Telephone:</td>
                                        <td><?php echo $this->Form->input('telephone', array('id'=>'telephone', 'value'=>'', 'div' => false, 'label' => false)); ?></td>
                                    </tr>
                                </table>
                                
                                <div class="footer tal">
                                    <button type="submit" class="btn">Create Account</button>
                                    <?php echo $this->Form->input('comp_id', array('type'=>'hidden','id'=>'comp_id', 'value'=>$comp_id,)); ?>
                                    <?php echo $this->Form->input('user_type', array('type'=>'hidden','id'=>'user_type', 'value'=>$comp_type,)); ?>
                                </div>
                                <?php echo $this->Form->end();?>
                            </div>

                        </section>
                    </fieldset>
                </div>
            </div>
        </div>


        <!-- Terms And Conditions -->
        <div style="display: none;">
            <div id="terms-conds-window" style="width: 500px;">
                <div class="content" style="padding: 20px;">
                    <fieldset style="border: 1px solid #ccc; padding: 15px; margin-bottom: 20px;">
                        <legend style="font-size: 14px; font-weight: bolder;">Master Subscription Terms and Conditions</legend>
                        <p>
                            All users of the WARP-D application are bound by the terms and conditions of the WARP-D master subscription agreement which has been accepted and endorsed by your company managements for you and all member of staff who will be using WARP-D.
                        </p>
                        <p>
                            By logging in and using the tool, you accept the terms and conditions.
                        </p>
                    </fieldset>
                    <fieldset style="border: 1px solid #ccc; padding: 15px; margin-bottom: 20px;">
                        <legend style="font-size: 14px; font-weight: bolder;">Other Terms and Conditions</legend>
                        <p>
                            <a href="http://www.rtheconsult.com/term_of_use.php" target="_blank"> <strong>Click here</strong> </a>
                            to view the terms and conditions.
                        </p>
                    </fieldset>
                </div>
            </div>
        </div>


    </div>
</div>

<!-- .previews -->
<footer id="bottom">
    <div class="wrapper">
        <div class="redline"></div>
        <p class="copyrights"></p>

        <p class="copyrights"><a href="javascript:void(0);" id="terms_conds" style="color: #7e7f80">Terms and
                Conditions</a> <br/> WARP-D Powered by: Rtheconsult</p>
    </div>
</footer>

<script type="text/javascript">
    $(document).ready(function () {

        $("#sign_up").click(function(){
            $.colorbox({
                inline:true,
                scrolling:false,
                overlayClose:false,
                escKey:false,
                top:'5%',
                title:'Sign Up',
                href:"#signup-form-window"
            });
        });

        $("#terms_conds").click(function(){
            $.colorbox({
                inline:true,
                scrolling:false,
                overlayClose:false,
                escKey:false,
                top:'5%',
                title:'Term and Conditions',
                href:"#terms-conds-window"
            });
        });

    });
</script>

<!-- URLs -->
<input type="hidden" id="login-url" value="<?php echo $this->Html->url(array('controller' => 'Users', 'action' => 'login')); ?>"/>
<input type="hidden" id="after-login-url" value="<?php echo $this->Html->url(array('controller' => 'Administration', 'action' => 'index')); ?>"/>
<input type="hidden" id="dashboard-url" value="<?php echo $this->Html->url(array('controller' => 'Dashboard', 'action' => 'index')); ?>"/>

