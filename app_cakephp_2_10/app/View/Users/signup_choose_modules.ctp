<?php
$package_fields = array();
foreach($packages as $package){
    $package_fields[$package['Package']['field']] = 0;
}
$mod = $package_fields;
if( $this->Session->check('signup') ) {
    $temp = $this->Session->read('signup');
    $mod = isset($temp['Modules'])?$temp['Modules']:$package_fields;
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
                    <li class="active"><span>3</span></li>
                    <li><span>4</span></li>
                    <li><span>5</span></li>
                </ul>
            </div>
        </div>

        <div class="g_12" id="package_error" style="display: none;"><div class="error iDialog">You need to select at least one package</div></div>

        <div class="g_12">
            <div class="widget_header">
                <h4 class="widget_header_title wwIcon">
                    Step 3 &nbsp; : &nbsp; Please select the packages to use
                </h4>
            </div>
            <?php echo $this->Form->create('Modules', array('url'=>array('controller' => 'Users','action'=> 'signup_choose_modules'),'id' => 'form'));?>
            <div class="widget_contents noPadding">
                <?php
                    foreach($packages as $package){
                ?>
                    <div class="line_grid">
                        <div class="g_3"><span class="label"><?php echo $package['Package']['name']; ?></span></div>
                        <div class="g_9">
                            <?php echo $this->Form->input($package['Package']['field'], array('id'=>$package['Package']['field'], 'type'=>'checkbox','class' => 'simple_form', 'value'=> $package['Package']['id'],'checked'=>(intval($mod[$package['Package']['field']]) > 0) ? 'checked':'' ,'div' => false, 'label' => false)); ?>
                            <div class="field_notice"><?php echo $package['Package']['description']; ?><strong>Ghc <?php echo $package['Package']['cost']; ?></strong></div>
                        </div>
                    </div>
                <?php
                    }
                ?>

                <div class="line_grid">
                    <div class="g_12">
                        <a href="<?php echo $this->Html->url(array('controller' => 'Users', 'action' => 'signup_administrator')); ?>" class="simple_buttons"> Back</a>
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
<script type="text/javascript">
    var Packages = {
        submit:false,

        init: function() {
            var self = this;

            $("#form").submit(function(e){
                $("#package_error").hide();
                if(!self.validate()){
                    $("#package_error").show();
                    return false
                }
                else{
                    if(!self.submit){
                        $("#package_error").show();
                        return false;
                    }
                }
            });
        },

        validate: function() {
            var self = this;
            var some_checked = false;
            $("#form :checkbox").each(function() {
                var chk = $(this).is(":checked");
                if(chk){
                    some_checked = true;
                }
            });
            if(some_checked){
                self.submit = true;
                return true;
            }
            else{
                self.submit = false;
                return false;
            }
        }
    };

    $(document).ready(function() {
        Packages.init();
    });
</script>