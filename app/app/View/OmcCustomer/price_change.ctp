<?php

?>
<script type="text/javascript">
    var product_list = <?php echo json_encode($product_list);?>;
    var permissions = <?php echo json_encode($permissions); ?>;
</script>

<div class="workplace">

    <div class="page-header">
        <h1>Price Change <small> Setup</small></h1>
    </div>

    <div class="row-fluid">

        <div class="span12">

            <div class="demo-info" style="margin-bottom:10px">
                <div class="demo-tip icon-tip"></div>
                <div>
                    Click on a row to select it, click again to deselect it.
                    Click the New to add new row.
                    Click on Edit to begin editing on a selected row.
                    Click Save to save the row.
                    Click on Cancel to quit changes to a row.
                </div>
            </div>

            <div class="head clearfix">
                <div class="isw-text_document"></div>
                <h1>Price Change</h1>
            </div>
            <table id="flex" style="display:none;"></table>

        </div>
    </div>

    <div class="dr"><span></span></div>

</div>

<!-- URLs -->
<input type="hidden" id="table-url" value="<?php echo $this->Html->url(array('controller' => 'OmcCustomer', 'action' => 'price_change/get')); ?>" />
<input type="hidden" id="table-editable-url" value="<?php echo $this->Html->url(array('controller' => 'OmcCustomer', 'action' => 'price_change/save')); ?>" />
<input type="hidden" id="grid_delete_url" value="<?php echo $this->Html->url(array('controller' => 'OmcCustomer', 'action' => 'price_change/delete')); ?>" />
<input type="hidden" id="table-export-url" value="<?php echo $this->Html->url(array('controller' => 'OmcCustomer', 'action' => 'export_price_change')); ?>" />


<!-- Le Script -->
<?php
    echo $this->Html->script('scripts/omc_customer/price_change.js');
?>
