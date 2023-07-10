<?php
//debug($tanks_types_opt);
?>
<div class="workplace">

    <div class="page-header">
        <h1 style="font-size: 30px;">Omc Customers Stock  <small> Administration</small></h1>
    </div>

    <div class="row-fluid">

        <div class="span12">

            <div class="demo-info" style="margin-bottom:10px">
                <div class="demo-tip icon-tip"></div>
                <div>
                    Click on a row to select it, click again to deselect it.
                    Click the left Plus icon to expand the row, click again to reduce the row.
                    Click the New to add new row.
                    Click on Edit to begin editing on a selected row.
                    Click Save to save the row.
                    Click on Cancel to quit changes to a row.
                </div>
            </div>

            <div class="head clearfix">
                <div class="isw-text_document"></div>
                <h1>Customers Stock Table</h1>
            </div>
            <table id="flex" style="display:none;"></table>

        </div>
    </div>

    <div class="dr"><span></span></div>

  <!--  <div class="row-fluid" id="export-form">
        <div class="span3">
            <div class="head clearfix">
                <div class="isw-text_document"></div>
                <h1>Export Data</h1>
            </div>
            <?php /*echo $this->Form->create('Export', array('id' => 'form-export', 'target'=>'ExportWindow' ,'inputDefaults' => array('label' => false,'div' => false)));*/?>
            <div class="block-fluid">
                <div class="row-form clearfix" style="border-top-width: 0px; padding: 5px 16px;">
                    <div class="span5">Start Date:</div>
                    <div class="span5">
                        <?php /*echo $this->Form->input('export_startdt', array('id'=>'export_startdt', 'class' => 'span2 date-masking validate[required]','default'=>date('Y-m-d'),'placeholder'=>'yyyy-mm-dd', 'div' => false, 'label' => false,)); */?>

                    </div>
                    <span>Example: 2012-12-01 (yyyy-mm-dd)</span>
                </div>

                <div class="row-form clearfix" style="border-top-width: 0px; padding: 3px 16px;">
                    <div class="span5">End Date:</div>
                    <div class="span5">
                        <?php /*echo $this->Form->input('export_enddt', array('id'=>'export_enddt', 'class' => 'span2 date-masking validate[required]','default'=>date('Y-m-d'),'placeholder'=>'yyyy-mm-dd', 'div' => false, 'label' => false,)); */?>

                    </div>
                    <span>Example: 2012-12-01 (yyyy-mm-dd)</span>
                </div>

                <div class="footer tal">
                    <button class="btn" type="button" id="export-btn">Export</button>
                    <?php /*echo $this->Form->input('export_type', array('type'=>'hidden','id'=>'export_type', 'value'=>$authUser['user_type'])); */?>
                    <?php /*echo $this->Form->input('export_url', array('type'=>'hidden','id'=>'export_url', 'value'=> $this->Html->url(array('controller' => 'OmcOperations', 'action' => 'export_loading_data')))); */?>
                    <?php /*echo $this->Form->input('action', array('type'=>'hidden','id'=>'action', 'value'=> 'export_me')); */?>
                </div>
                <?php /*echo $this->Form->end();*/?>
            </div>
        </div>
    </div>-->

    <div class="dr"><span></span></div>

</div>

<!-- URLs -->
<input type="hidden" id="table-url" value="<?php echo $this->Html->url(array('controller' => 'OmcStock', 'action' => 'stock_administration/get')); ?>" />
<input type="hidden" id="table-editable-sub-url" value="<?php echo $this->Html->url(array('controller' => 'OmcStock', 'action' => 'stock_administration/sub_save')); ?>" />
<input type="hidden" id="table-details-url" value="<?php echo $this->Html->url(array('controller' => 'OmcStock', 'action' => 'stock_administration/load_details')); ?>" />

<input type="hidden" id="grid_load_url" value="<?php echo $this->Html->url(array('controller' => 'OmcStock', 'action' => 'stock_administration/load')); ?>" />
<input type="hidden" id="grid_delete_url" value="<?php echo $this->Html->url(array('controller' => 'OmcStock', 'action' => 'stock_administration/delete')); ?>" />
<input type="hidden" id="export_url" value="<?php echo $this->Html->url(array('controller' => 'OmcStock', 'action' => 'stock_administration')); ?>" />



<!-- Le Script -->
<script type="text/javascript">
    var tanks_types_opt = <?php echo json_encode($tanks_types_opt);?>;
   // var customer_tanks = <?php //echo json_encode($customer_tanks);?>;
    var tank_status = <?php echo json_encode($tank_status);?>;
    var tank_names = <?php echo json_encode($tank_names);?>;
    var permissions = <?php echo json_encode($permissions); ?>;
</script>
<?php
echo $this->Html->script('scripts/omc_customers_stock_administration.js');
?>
