<div class="span3 clearfix" id="mails_navigation">
    <a href="<?php echo $this->Html->url(array('controller' => 'Messages', 'action' => 'compose')); ?>" role="button" class="btn btn-success btn-block">New Message</a>
    <div class="block-fluid sNavigation">
        <ul>
            <li class="<?php echo ($this->params['action'] == 'index')? 'active': '' ;?>"><a href="<?php echo $this->Html->url(array('controller' => 'Messages', 'action' => 'index')); ?>"><span class="icon-inbox"></span> Inbox</a><span class="arrow"></span></li>
            <li class="<?php echo ($this->params['action'] == 'outbox')? 'active': '' ;?>"><a href="<?php echo $this->Html->url(array('controller' => 'Messages', 'action' => 'outbox')); ?>"><span class="icon-envelope"></span> Sent Messages</a><span class="arrow"></span></li>
            <li class="<?php echo ($this->params['action'] == 'address_book')? 'active': '' ;?>"><a href="<?php echo $this->Html->url(array('controller' => 'Messages', 'action' => 'address_book')); ?>"><span class="icon-book"></span> Address Book</a><span class="arrow"></span></li>
            <!--<li class="<?php /*echo ($this->params['action'] == 'trash')? 'active': '' ;*/?>"><a href="<?php /*echo $this->Html->url(array('controller' => 'Messages', 'action' => 'trash')); */?>"><span class="icon-remove"></span> Deleted items</a><span class="arrow"></span></li>-->
        </ul>
    </div>
</div>