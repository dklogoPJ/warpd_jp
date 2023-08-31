<?php

?>
<script type="text/javascript">
    var permissions = <?php echo json_encode($permissions); ?>;
    var additives = <?php echo json_encode($additives_lists);?>;
</script>

<div class="workplace">

    <div class="page-header">
        <h1 style="font-size: 30px;">Additive Stock WAC  <small> Dashboard</small></h1>
    </div>

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

<div class="block-fluid tabs">

<ul>
    <li><a href="#tabs-1"><strong> Stock 1 - Received </strong></a></li>
    <li><a href="#tabs-2" id="finventory1"><strong> Stock 1 - Inventory </strong></a></li>
    <li><a href="#tabs-3" id="freceive2"><strong>Stock 2 - Received </strong></a></li>
    <li><a href="#tabs-4" id="inventory2"><strong>Stock 2 - Inventory </strong></a></li>
    <li><a href="#tabs-5" id="wac"><strong>Stock 2 - Weighted Average Cost (WAC) </strong></a></li>

</ul>

<div id="tabs-1">
    
    <div class="row-fluid">
        <div class="span12">
            <div class="head clearfix">
                <h1> <div class="isw-text_document"></div> Stock 1 - Received Table</h1>
            </div>
            <table id="flex" style="display:none;"></table>
        </div>
    </div>

</div>

<div id="tabs-2">

    <div class="row-fluid">
        <div class="span12">
            <div class="head clearfix">
                <h1> <div class="isw-text_document"></div> Stock 1 - Received Table</h1>
            </div>
            <table id="flex2" style="display:none;"></table>
        </div>
    </div>
    
</div>

<div id="tabs-3">

    <div class="row-fluid">
        <div class="span12">
            <div class="head clearfix">
                <h1> <div class="isw-text_document"></div> Stock 1 - Received Table</h1>
            </div>
            <table id="flex3" style="display:none;"></table>
        </div>
    </div>

</div>

<div id="tabs-4">
    
    <div class="row-fluid">
        <div class="span12">
            <div class="head clearfix">
                <h1> <div class="isw-text_document"></div> Stock 1 - Received Table</h1>
            </div>
            <table id="flex4" style="display:none;"></table>
        </div>
    </div>

</div>

<div id="tabs-5">

    <div class="row-fluid">
        <div class="span12">
            <div class="head clearfix">
                <h1> <div class="isw-text_document"></div> Stock 1 - Received Table</h1>
            </div>
            <table id="flex5" style="display:none;"></table>
        </div>
    </div>

</div>

</div>

<!--<div class="row-fluid" id="export-form">
        <div class="span3">
            <div class="head clearfix">
                <div class="isw-text_document"></div>
                <h1>Export Data</h1>
            </div>
            <?php echo $this->Form->create('Export', array('id' => 'form-export', 'target'=>'ExportWindow' ,'inputDefaults' => array('label' => false,'div' => false)));?>
            <div class="block-fluid">
                <div class="row-form clearfix" style="border-top-width: 0px; padding: 5px 16px;">
                    <div class="span5">Start Date:</div>
                    <div class="span5">
                        <?php echo $this->Form->input('export_startdt', array('id'=>'export_startdt', 'class' => 'span2 date-masking validate[required]','default'=>date('d-m-Y'),'placeholder'=>'dd-mm-yyyy', 'div' => false, 'label' => false,)); ?>

                    </div>
                    <span>Example: 01-12-2012 (dd-mm-yyyy)</span>
                </div>

                <div class="row-form clearfix" style="border-top-width: 0px; padding: 3px 16px;">
                    <div class="span5">End Date:</div>
                    <div class="span5">
                        <?php echo $this->Form->input('export_enddt', array('id'=>'export_enddt', 'class' => 'span2 date-masking validate[required]','default'=>date('d-m-Y'),'placeholder'=>'dd-mm-yyyy', 'div' => false, 'label' => false,)); ?>

                    </div>
                    <span>Example: 01-12-2012 (dd-mm-yyyy)</span>
                </div>

                <div class="footer tal">
                    <button class="btn" type="button" id="export-btn">Export</button>
                    <?php echo $this->Form->input('export_type', array('type'=>'hidden','id'=>'export_type', 'value'=>$authUser['user_type'])); ?>
                    <?php echo $this->Form->input('export_url', array('type'=>'hidden','id'=>'export_url', 'value'=> $this->Html->url(array('controller' => 'OmcAdditive', 'action' => 'export_additive_wac')))); ?>
                    <?php echo $this->Form->input('action', array('type'=>'hidden','id'=>'action', 'value'=> 'export_me')); ?>
                </div>
                <?php echo $this->Form->end();?>
            </div>
        </div>
    </div>-->

<div class="dr"><span></span></div>

</div>

<!-- URLs -->
<input type="hidden" id="table-url" value="<?php echo $this->Html->url(array('controller' => 'OmcAdditive', 'action' => 'additive_stock_wac/get')); ?>" />
<input type="hidden" id="table-editable-url" value="<?php echo $this->Html->url(array('controller' => 'OmcAdditive', 'action' => 'additive_stock_wac/save')); ?>" />
<input type="hidden" id="grid_delete_url" value="<?php echo $this->Html->url(array('controller' => 'OmcAdditive', 'action' => 'additive_stock_wac/delete')); ?>" />
<input type="hidden" id="export_url" value="<?php echo $this->Html->url(array('controller' => 'OmcAdditive', 'action' => 'export_additive')); ?>" />

<input type="hidden" id="table2-url" value="<?php echo $this->Html->url(array('controller' => 'OmcAdditive', 'action' => 'additive_stock_inventory1/get')); ?>" />
<input type="hidden" id="table2-editable-url" value="<?php echo $this->Html->url(array('controller' => 'OmcAdditive', 'action' => 'additive_stock_inventory1/save')); ?>" />
<input type="hidden" id="grid2_delete_url" value="<?php echo $this->Html->url(array('controller' => 'OmcAdditive', 'action' => 'additive_stock_inventory1/delete')); ?>" />
<input type="hidden" id="export2_url" value="<?php echo $this->Html->url(array('controller' => 'OmcAdditive', 'action' => 'additive_stock_inventory1_export')); ?>" />

<input type="hidden" id="table3-url" value="<?php echo $this->Html->url(array('controller' => 'OmcAdditive', 'action' => 'additive_stock_received2/get')); ?>" />
<input type="hidden" id="table3-editable-url" value="<?php echo $this->Html->url(array('controller' => 'OmcAdditive', 'action' => 'additive_stock_received2/save')); ?>" />
<input type="hidden" id="grid3_delete_url" value="<?php echo $this->Html->url(array('controller' => 'OmcAdditive', 'action' => 'additive_stock_received2/delete')); ?>" />
<input type="hidden" id="export3_url" value="<?php echo $this->Html->url(array('controller' => 'OmcAdditive', 'action' => 'additive_stock_received2_export')); ?>" />

<input type="hidden" id="table4-url" value="<?php echo $this->Html->url(array('controller' => 'OmcAdditive', 'action' => 'additive_stock_stock2/get')); ?>" />
<input type="hidden" id="table4-editable-url" value="<?php echo $this->Html->url(array('controller' => 'OmcAdditive', 'action' => 'additive_stock_stock2/save')); ?>" />
<input type="hidden" id="grid4_delete_url" value="<?php echo $this->Html->url(array('controller' => 'OmcAdditive', 'action' => 'additive_stock_stock2/delete')); ?>" />
<input type="hidden" id="export4_url" value="<?php echo $this->Html->url(array('controller' => 'OmcAdditive', 'action' => 'additive_stock_stock2_export')); ?>" />


<input type="hidden" id="table5-url" value="<?php echo $this->Html->url(array('controller' => 'OmcAdditive', 'action' => 'additive_cost_wac/get')); ?>" />
<input type="hidden" id="table5-editable-url" value="<?php echo $this->Html->url(array('controller' => 'OmcAdditive', 'action' => 'additive_cost_wac/save')); ?>" />
<input type="hidden" id="grid5_delete_url" value="<?php echo $this->Html->url(array('controller' => 'OmcAdditive', 'action' => 'additive_cost_wac/delete')); ?>" />
<input type="hidden" id="export5_url" value="<?php echo $this->Html->url(array('controller' => 'OmcAdditive', 'action' => 'additive_cost_wac_export')); ?>" />


<!-- Le Script -->
<?php
echo $this->Html->script('scripts/omc/additive_wac_tab1.js');
echo $this->Html->script('scripts/omc/additive_wac_tab2.js');
echo $this->Html->script('scripts/omc/additive_wac_tab3.js');
echo $this->Html->script('scripts/omc/additive_wac_tab4.js');
echo $this->Html->script('scripts/omc/additive_wac_tab5.js');
?>
