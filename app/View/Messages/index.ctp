<div class="workplace">

    <div class="page-header">
        <h1>Mailbox <small>Messaging</small></h1>
    </div>

    <div class="row-fluid">

        <?php
            echo $this->element('messages/msg_nav');
        ?>

        <div class="span9" id="mails">
           <!-- <div class="headInfo">
                <div class="input-append">
                    <input type="text" name="search" placeholder="search keyword..." id="widgetInputMessage"/><button class="btn btn-success" type="button">Search</button>
                </div>
                <div class="arrow_down"></div>
            </div>-->

            <div class="block-fluid" id="inbox">
                <div class="demo-info" style="margin-bottom:10px">
                    <div class="demo-tip icon-tip"></div>
                    <div>
                        Click on a row to select it, click again to deselect it.
                        Click on View to view a selected row.
                        Click on Delete to delete selected rows.
                    </div>
                </div>
                <table id="flex" style="display:none;"></table>
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
    echo $this->Html->script('scripts/mail_inbox.js');
?>
