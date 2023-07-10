<div class="workplace">

    <div class="page-header">
        <h1>Mailbox <small>Messaging</small></h1>
    </div>

    <div class="row-fluid">

        <?php
            echo $this->element('messages/msg_nav');
        ?>

        <div class="span9" id="mails">
            <div class="block-fluid" id="compose">
                <div class="head clearfix">
                    <div class="isw-mail" style="padding: 11px 0; margin-left: 10px;"></div>
                    <h1>New Message</h1>
                </div>
                <div class="block">
                    <?php echo $this->Form->create('Message', array('default' => false, 'id' => 'form-new-message', 'action' => 'sendMessage','inputDefaults' => array('label' => false,'div' => false)));?>
                    <div class="row-fluid">
                        <div class="block-fluid">
                            <div class="row-form clearfix">
                                <div class="span1">From:</div>
                                <div class="span9"><?php echo $authUser['fname'].' '.$authUser['lname'].' < '.$authUser['username'].' >'; ?></div>
                            </div>
                            <div class="row-form clearfix">
                                <div class="span1">To:</div>
                                <div class="span9">
                                    <?php echo $this->Form->input('to', array('id'=>'to', 'class' => 'validate[required] ', 'placeholder'=>'Username' ,'div' => false, 'label' => false,)); ?>
                                    <a href="#" class="caption_addrbk address_book link_navPopMessages"> Address Book</a>
                                    <span>To send message to multiple people, separate the usernames by a comma(,) and no spaces. Example: use1,user2,user3 .</span>

                                    <div id="navPopMessages" class="popup" style="display: block; top:270px;">
                                        <div class="head clearfix">
                                            <!--<div class="arrow"></div>-->
                                            <span class="isw-bo"></span>
                                            <span class="name">Address Book (Click to add to the send list.)</span>
                                        </div>
                                        <div class="body messages scrollBox">

                                            <div class="scroll" style="height: 200px;">

                                                <?php
                                                    foreach($address_list as $user){
                                                        ?>
                                                        <div class="item address_book_contact" style="cursor: pointer" data-user="<?php echo $user['contact_username']; ?>">
                                                           <!-- <div class="image">
                                                                <a href="javascript:void(0);" style="text-decoration: none">
                                                                    <?php /*echo $this->Html->image('ic_user.png', array('width'=>'50', 'height'=>'50','alt' =>'User name','class'=>'img-polaroid')); */?>
                                                                </a>
                                                            </div>-->
                                                            <div class="info" style="padding: 0px;">
                                                                <a href="javascript:void(0);" style="text-decoration: none" class="name"><?php echo $user['contact_name']; ?></a>

                                                                <span><?php echo ' < '.$user['contact_username'].' >'; ?> </span>
                                                            </div>
                                                        </div>
                                                    <?php
                                                    }
                                                ?>
                                            </div>

                                        </div>
                                        <div class="footer">
                                            <button class="btn link_navPopMessages" type="button">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row-form clearfix">
                                <div class="span1">Title:</div>
                                <div class="span9">
                                    <?php echo $this->Form->input('title', array('id'=>'title', 'class' => 'validate[required]', 'div' => false, 'label' => false,)); ?>
                                </div>
                            </div>
                        </div>
                        <div class="block-fluid">
                            <?php echo $this->Form->input('content', array('type'=>'textarea','id'=>'mail_wysiwyg', 'class' => 'validate[required]', 'div' => false, 'label' => false,)); ?>
                            <?php echo $this->Form->input('user_id', array('type'=>'hidden', 'id'=>'user_id', 'class' => '','value'=>$authUser['id'])); ?>
                            <!--<textarea id="mail_wysiwyg" name="text" style="height: 300px; width: 100%;"></textarea>-->
                        </div>
                        <div class="footer" style="text-align: left">
                            <button type="button" class="btn btn-success" id="send-btn">Send Message</button>
                        </div>
                        <div class="dr"><span></span></div>
                    </div>
                    <?php echo $this->Form->end();?>
                </div>
            </div>

        </div>

    </div>


<div class="dr"><span></span></div>

</div>

<?php
    echo $this->element('messages/msg_incl');
?>

<!-- Users URL -->
<input type="hidden" id="table-url" value="<?php echo $this->Html->url(array('controller' => 'Messages', 'action' => 'index')); ?>" />

<?php
    echo $this->Html->script('scripts/mail_compose.js');
?>
