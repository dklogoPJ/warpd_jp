<?php

?>
<script type="text/javascript">
    var permissions = <?php echo json_encode($permissions); ?>;
    var products = <?php echo json_encode($products_lists);?>;
    var additives = <?php echo json_encode($additives_lists);?>;
    var numbers = <?php echo json_encode($numbers);?>;
    var depotLists = <?php echo json_encode($depot_lists);?>;
    var customerLists = <?php echo json_encode($omc_customers_lists);?>;
    var customers = <?php echo json_encode($omc_customers_lists);?>;
    var teritory_name = <?php echo json_encode($teritory);?>;
</script>

<div class="workplace">

    <div class="page-header">
        <h1 style="font-size: 30px;">RM Performance Monitoring Setting  <small> Dashboard</small></h1>
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
                <h1>Performance Monitoring Setting Table</h1>
            </div>
            <table id="flex" style="display:none;"></table>

        </div>
    </div>



    <div class="row-fluid" id="export-form">
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
                    <?php echo $this->Form->input('export_url', array('type'=>'hidden','id'=>'export_url', 'value'=> $this->Html->url(array('controller' => 'OmcAdditive', 'action' => 'export_additive_cost')))); ?>
                    <?php echo $this->Form->input('action', array('type'=>'hidden','id'=>'action', 'value'=> 'export_me')); ?>
                </div>
                <?php echo $this->Form->end();?>
            </div>
        </div>
    </div>

    <div class="dr"><span></span></div>

</div>

<!-- URLs -->
<input type="hidden" id="table-url" value="<?php echo $this->Html->url(array('controller' => 'OmcPerformance', 'action' => 'perf_monitoring_setting/get')); ?>" />
<input type="hidden" id="table-editable-url" value="<?php echo $this->Html->url(array('controller' => 'OmcPerformance', 'action' => 'perf_monitoring_setting/save')); ?>" />
<input type="hidden" id="table-details-url" value="<?php echo $this->Html->url(array('controller' => 'OmcPerformance', 'action' => 'perf_monitoring_setting/load_details')); ?>" />
<input type="hidden" id="grid_load_url" value="<?php echo $this->Html->url(array('controller' => 'OmcPerformance', 'action' => 'perf_monitoring_setting/load')); ?>" />
<input type="hidden" id="grid_delete_url" value="<?php echo $this->Html->url(array('controller' => 'OmcPerformance', 'action' => 'perf_monitoring_setting/delete')); ?>" />
<input type="hidden" id="export_url" value="<?php echo $this->Html->url(array('controller' => 'OmcPerformance', 'action' => 'export_performance')); ?>" />



<!-- Le Script -->
<?php
echo $this->Html->script('scripts/omc_performance/performance_settings.js');
?>
