<div class="workplace">
<?php //debug($authUser); ?>
    <div class="page-header">
        <h1><?php echo $authUser['fname'].' '.$authUser['lname']; ?> <small> Profile</small></h1>
    </div>

    <?php
    $error = false;
    if($this->Session->check('process_error')){
        if($this->Session->read('process_error') == 'yes'){
            $error = true;
        }
        $controller->Session->delete('process_error');
    }
    ?>
    <?php
    $flash_msg = $this->Session->read('Message');
    if(isset($flash_msg['flash'])){
        ?>
        <div class="row-fluid">
            <div class="span12">
                <?php
                if($error){
                    echo $this->Message->msg('Status',$this->Session->flash(),'error',true);
                }
                else{
                    echo $this->Message->msg('Status',$this->Session->flash(),'success',true);
                }
                ?>
            </div>
        </div>
        <?php
    }
    ?>

    <div class="row-fluid">
        <div class="span6">
            <div class="profile clearfix">
                <div class="image">
                    <?php echo $this->Html->image("sitepages/$company_key/small-bg.jpg", array('alt' =>'Bg','class'=>'img-polaroid')); ?>
                </div>
                <div class="user clearfix">
                    <div class="avatar">
                        <?php echo $this->Html->image('user_big.png', array('alt' =>'User name','class'=>'img-polaroid')); ?>
                    </div>
                    <h2><?php echo $authUser['fname'].' '.$authUser['lname']; ?></h2>
                    <div class="actions">

                        <!--<div class="btn-group">
                            <button class="btn btn-small tip" data-original-title="Upload Company Logo"><span class="icon-envelope icon-white"></span> Messages</button>
                            <!--<button class="btn btn-small tip" data-original-title="Upgrade Company Package"><span class="icon-share-alt icon-white"></span> Upgrade Package</button>
                        </div>-->

                    </div>
                </div>
                <div class="info">
                    <p><span class="icon-globe"></span> <span class="title">Telephone:</span>  <?php echo $authUser['telephone']; ?></p>
                    <p><span class="icon-gift"></span> <span class="title">Date of Registration:</span> <?php echo date("d M Y, g:ia", strtotime($authUser['created'])); ?></p>
                </div>

            </div>

        </div>

        <div class="span6">
            <div class="head clearfix">
                <div class="isw-edit"></div>
                <h1>Edit Profile Information</h1>
            </div>
            <div class="block-fluid">
                <?php echo $this->Form->create('User', array('id' => 'form','class'=>'form-horizontal'));?>
                <div class="row-form clearfix" style="border-top-width: 0px;">
                    <div class="span3">Title:</div>
                    <div class="span9">
                        <?php echo $this->Form->input('title', array('id' => 'title', 'class' => '', 'options' => $this->Select->generateTitles(), 'default' => $authUser['title'], 'div' => false, 'label' => false)); ?>
                    </div>
                </div>

                <div class="row-form clearfix" style="border-top-width: 0px;">
                    <div class="span3">First Name:</div>
                    <div class="span9">
                        <?php echo $this->Form->input('fname', array('id' => 'fname', 'class' => '', 'value' => $authUser['fname'], 'div' => false, 'label' => false)); ?>
                    </div>
                </div>

                <div class="row-form clearfix" style="border-top-width: 0px;">
                    <div class="span3">Middle Name:</div>
                    <div class="span9">
                        <?php echo $this->Form->input('mname', array('id' => 'mname', 'class' => '', 'value' => $authUser['mname'], 'div' => false, 'label' => false)); ?>
                    </div>
                </div>

                <div class="row-form clearfix" style="border-top-width: 0px;">
                    <div class="span3">Last Name:</div>
                    <div class="span9">
                        <?php echo $this->Form->input('lname', array('id' => 'lname', 'class' => '', 'value' => $authUser['lname'], 'div' => false, 'label' => false)); ?>
                    </div>
                </div>

               <!-- <div class="row-form clearfix">
                    <div class="span3">Username:</div>
                    <div class="span9">
                        <?php /*echo $this->Form->input('username', array('id' => 'username', 'class' => '', 'default' => $authUser['username'], 'div' => false, 'label' => false)); */?>
                    </div>
                </div>-->

                <div class="row-form clearfix">
                    <div class="span3">Password:</div>
                    <div class="span9">
                        <?php echo $this->Form->input('password', array('id' => 'password', 'type' => 'password', 'class' => '', 'div' => false, 'label' => false)); ?>
                    </div>
                </div>

                <div class="row-form clearfix">
                    <div class="span3">Email:</div>
                    <div class="span9">
                        <?php echo $this->Form->input('email', array('id' => 'email', 'class' => '', 'default' => $authUser['email'], 'div' => false, 'label' => false)); ?>
                    </div>
                </div>

                <div class="row-form clearfix">
                    <div class="span3">Telephone:</div>
                    <div class="span9">
                        <?php echo $this->Form->input('telephone', array('id'=>'telephone', 'value'=>$authUser['telephone'], 'div' => false, 'label' => false)); ?>
                    </div>
                </div>

                <div class="footer tal">
                    <button type="submit" class="btn">Update Info</button>
                    <?php echo $this->Form->input('id', array('type'=>'hidden','id'=>'id', 'value'=>$authUser['id'], 'div' => false, 'label' => false)); ?>
                </div>
            <?php echo $this->Form->end();?>
            </div>
        </div>
    </div>

    <div class="dr"><span></span></div>

</div>

<!-- URLs -->
<input type="hidden" id="company_save_url" value="<?php echo $this->Html->url(array('controller' => 'User', 'action' => 'update')); ?>" />

<!-- Le Script -->
<?php
    //echo $this->Html->script('scripts/company.js');
?>
