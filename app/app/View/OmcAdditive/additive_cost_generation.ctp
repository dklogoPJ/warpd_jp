<?php

?>
<script type="text/javascript">
    var permissions = <?php echo json_encode($permissions); ?>;
    var products = <?php echo json_encode($products_lists);?>;
    var additives = <?php echo json_encode($additives_lists);?>;
    var numbers = <?php echo json_encode($numbers);?>;
    var depotLists = <?php echo json_encode($depot_lists);?>;
    var customerLists = <?php echo json_encode($omc_customers_lists);?>;
</script>

<div class="workplace">

    <div class="page-header">
        <h1 style="font-size: 30px;">Additive Cost Generation  <small> Dashboard</small></h1>
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
                <h1>Additive Cost Generation Table</h1>
            </div>
            <table id="flex" style="display:none;"></table>

        </div>
    </div>

    <div class="dr"><span></span></div>

    <div class="dr"><span></span></div>

</div>

<!-- URLs -->
<input type="hidden" id="table-url" value="<?php echo $this->Html->url(array('controller' => 'OmcAdditive', 'action' => 'additive_cost_generation/get')); ?>" />
<input type="hidden" id="table-editable-url" value="<?php echo $this->Html->url(array('controller' => 'OmcAdditive', 'action' => 'additive_cost_generation/save')); ?>" />
<input type="hidden" id="table-details-url" value="<?php echo $this->Html->url(array('controller' => 'OmcAdditive', 'action' => 'additive_cost_generation/load_details')); ?>" />
<input type="hidden" id="grid_load_url" value="<?php echo $this->Html->url(array('controller' => 'OmcAdditive', 'action' => 'additive_cost_generation/load')); ?>" />
<input type="hidden" id="grid_delete_url" value="<?php echo $this->Html->url(array('controller' => 'OmcAdditive', 'action' => 'additive_cost_generation/delete')); ?>" />
<input type="hidden" id="export_url" value="<?php echo $this->Html->url(array('controller' => 'OmcAdditive', 'action' => 'export_additive')); ?>" />



<!-- Le Script -->
<?php
echo $this->Html->script('scripts/omc/additive_cost.js');
?>
