<div class="breadLine">
    <div class="arrow"></div>
    <div class="adminControl active">
        <?php
        echo "Hi, ".$authUser['fname'].' '.$authUser['lname'];
        ?>
    </div>
</div>

<div class="admin">
    <div class="image">
        <?php echo $this->Html->image('user.png', array('width'=>'50', 'height'=>'50','alt' =>'User name','class'=>'img-polaroid')); ?>
        <!--<img src="img/users/aqvatarius.jpg" class="img-polaroid"/>-->
    </div>
    <ul class="control">
        <li><span class="icon-comment"></span> <a href="<?php echo $this->Html->url(array('controller' => 'Messages', 'action' => 'index')); ?>">Messages</a> <a href="javascript: void(0);" id="user_message_counter" class="caption red"><?php echo $user_inbox; ?></a></li>
        <li><span class="icon-cog"></span> <a href="<?php echo $this->Html->url(array('controller' => 'Users', 'action' => 'profile')); ?>">Edit Profile</a></li>
        <li><span class="icon-share-alt"></span> <a href="<?php echo $this->Html->url(array('controller' => 'Users', 'action' => 'logout')); ?>">Logout</a></li>
    </ul>
   <!-- <div class="info">
        <span>Welcom back!</span>
    </div>-->
</div>