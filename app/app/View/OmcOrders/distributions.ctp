<script type="text/javascript">
    var delivery_locations = <?php echo json_encode($delivery_locations);?>;
    var volumes = <?php echo json_encode($volumes);?>;
    var customers = <?php echo json_encode($omc_customers_lists);?>;
    var region = <?php echo json_encode($regions_lists);?>;
    var district = <?php echo json_encode($district_lists);?>;
    var glbl_region_district = <?php echo json_encode($glbl_region_district);?>;
    var permissions = <?php echo json_encode($permissions); ?>;
    var truckList = <?php echo json_encode($truckList);?>;
    var numbers = <?php echo json_encode($numbers);?>;
    var invoice_no = <?php echo json_encode($invoice_no);?>;
    var my_bdc_list_ids = <?php echo json_encode($my_bdc_list_ids); ?>;
    var inv_no = <?php echo json_encode($inv_no); ?>;
    var all_customers_products_prices = <?php echo json_encode($all_customers_products_prices); ?>;
    //console.log(delivery_locations)
</script>
<div class="workplace">

    <div class="page-header">
        <h1>Product Distribution <small> Dashboard</small></h1>
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
                <h1>Truck Loading History</h1>
            </div>
            <table id="flex" style="display:none;"></table>

        </div>
    </div>

    <div class="dr"><span></span></div>

   <?php
 if(in_array('PX',$permissions)){
        ?>
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
                            <?php echo $this->Form->input('exp_startdt', array('id'=>'exp_startdt','name'=>'exp_startdt', 'class' => 'span2 date-masking validate[required] datepicker','default'=>date('d-m-Y'),'placeholder'=>'dd-mm-yyyy', 'div' => false, 'label' => false,)); ?>

                        </div>
                        <span>Example: 01-12-2012 (dd-mm-yyyy)</span>
                    </div>

                    <div class="row-form clearfix" style="border-top-width: 0px; padding: 3px 16px;">
                        <div class="span5">End Date:</div>
                        <div class="span5">
                            <?php echo $this->Form->input('exp_enddt', array('id'=>'exp_enddt', 'name'=>'exp_enddt','class' => 'span2 date-masking validate[required] datepicker','default'=>date('d-m-Y'),'placeholder'=>'dd-mm-yyyy', 'div' => false, 'label' => false,)); ?>

                        </div>
                        <span>Example: 01-12-2012 (dd-mm-yyyy)</span>
                    </div>

                    <div class="footer tal">
                        <button class="btn" type="button" id="export-btn">Export</button>
                        <?php echo $this->Form->input('export_type', array('type'=>'hidden','id'=>'export_type', 'value'=>$authUser['user_type'])); ?>
                        <?php echo $this->Form->input('export_url', array('type'=>'hidden','id'=>'export_url', 'value'=> $this->Html->url(array('controller' => 'OmcOperations', 'action' => 'export_loading_data')))); ?>
                        <?php echo $this->Form->input('action', array('type'=>'hidden','id'=>'action', 'value'=> 'export_me')); ?>
                    </div>
                    <?php echo $this->Form->end();?>
                </div>
            </div>
        </div>
    <?php
   }
    ?>

    <div class="dr"><span></span></div>

</div>

<!-- URLs -->
<input type="hidden" id="table-url" value="<?php echo $this->Html->url(array('controller' => 'OmcOrders', 'action' => 'distributions/get')); ?>" />
<input type="hidden" id="table-editable-url" value="<?php echo $this->Html->url(array('controller' => 'OmcOrders', 'action' => 'distributions/save')); ?>" />
<input type="hidden" id="table-details-url" value="<?php echo $this->Html->url(array('controller' => 'OmcOrders', 'action' => 'distributions/load_details')); ?>" />
<input type="hidden" id="table-editable-sub-url" value="<?php echo $this->Html->url(array('controller' => 'OmcOrders', 'action' => 'distributions/save-sub')); ?>" />


<input type="hidden" id="grid_load_url" value="<?php echo $this->Html->url(array('controller' => 'OmcOrders', 'action' => 'distributions/load')); ?>" />
<input type="hidden" id="grid_delete_url" value="<?php echo $this->Html->url(array('controller' => 'OmcOrders', 'action' => 'distributions/delete')); ?>" />
<input type="hidden" id="export_url" value="<?php echo $this->Html->url(array('controller' => 'OmcOrders', 'action' => 'export_orders')); ?>" />


<!-- Le Script -->
<?php
echo $this->Html->script('scripts/omc/omc_loading.js');
?>
