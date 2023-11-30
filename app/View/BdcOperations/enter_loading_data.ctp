<?php
    //debug($omclists);
?>
<script type="text/javascript">
    //console.log(<?php echo json_encode($omclists);?>)
    var omc = <?php echo json_encode($omclists);?>
    //console.log(omc);
    var depot = <?php echo json_encode($bdc_depot_lists);?>;
    var product_type =  <?php echo json_encode($products_lists);?>;
    /*var region = <?php //echo json_encode($regions_lists);?>;
    var district = <?php //echo json_encode($district_lists);?>;*/
    var depots_to_products = <?php echo json_encode($depots_to_products);?>;
    var permissions = <?php echo json_encode($permissions); ?>;
</script>
<div class="workplace">

    <div class="page-header">
        <h1>Daily Loading <small> Data Entry</small></h1>
    </div>

    <div class="row-fluid" style="margin-bottom: 50px;">
        <div class="span12">

            <div class="demo-info" style="margin-bottom:10px">
                <div class="demo-tip icon-tip"></div>
                <div>
                    Click on a row to select it, click again to deselect it.
                    Click the New to add new row.
                    Click on Edit to begin editing on a selected row.
                    Click Save to save the row.
                    Click on Cancel to quit changes to a row.
<!--                    Click on Delete to delete selected rows.-->
                </div>
            </div>


            <table id="flex" style="display:none;"></table>

        </div>
    </div>

    <?php
    if(in_array('E',$permissions)){
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
                            <?php echo $this->Form->input('export_startdt', array('id'=>'export_startdt', 'class' => 'span2 date-masking validate[required]','default'=>date('d-m-Y'),'placeholder'=>'dd-mm-yyyy', 'div' => false, 'label' => false,)); ?>
                            <!---->
                        </div>
                        <span>Example: 01-12-2012 (dd-mm-yyyy)</span>
                    </div>

                    <div class="row-form clearfix" style="border-top-width: 0px; padding: 3px 16px;">
                        <div class="span5">End Date:</div>
                        <div class="span5">
                            <?php echo $this->Form->input('export_enddt', array('id'=>'export_enddt', 'class' => 'span2 date-masking validate[required]','default'=>date('d-m-Y'),'placeholder'=>'dd-mm-yyyy', 'div' => false, 'label' => false,)); ?>
                            <!---->
                        </div>
                        <span>Example: 01-12-2012 (dd-mm-yyyy)</span>
                    </div>

                    <div class="footer tal">
                        <button class="btn" type="button" id="export-btn">Export</button>
                        <?php echo $this->Form->input('export_type', array('type'=>'hidden','id'=>'export_type', 'value'=>$authUser['user_type'])); ?>
                        <?php echo $this->Form->input('export_url', array('type'=>'hidden','id'=>'export_url', 'value'=> $this->Html->url(array('controller' => 'BdcOperations', 'action' => 'export_loading_data')))); ?>
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
<input type="hidden" id="table-url" value="<?php echo $this->Html->url(array('controller' => 'BdcOperations', 'action' => 'enter_loading_data/get')); ?>" />
<input type="hidden" id="table-editable-url" value="<?php echo $this->Html->url(array('controller' => 'BdcOperations', 'action' => 'enter_loading_data/save')); ?>" />

<input type="hidden" id="grid_load_url" value="<?php echo $this->Html->url(array('controller' => 'BdcOperations', 'action' => 'enter_loading_data/load')); ?>" />
<input type="hidden" id="grid_delete_url" value="<?php echo $this->Html->url(array('controller' => 'BdcOperations', 'action' => 'enter_loading_data/delete')); ?>" />


<!-- Le Script -->
<?php
    echo $this->Html->script('scripts/enter_loading.js');
?>
