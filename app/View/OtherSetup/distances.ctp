<?php

?>
<script type="text/javascript">
    var route_types =[
        {id:'',name:''},
        {id:'AR','name':'AR'}
    ];
    var depot_lists = <?php echo json_encode($depot_lists);?>;
    var filter_depots = <?php echo json_encode($filter_depots);?>;
   var regions_lists = <?php echo json_encode($regions_lists);?>;
    var filter_region = <?php echo json_encode($filter_region);?>;
    var permissions = <?php echo json_encode($permissions); ?>;
</script>

<div class="workplace">

    <div class="page-header">
        <h1>UPPF Distances <small> Setup</small></h1>
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

            <div class="headInfo">
                <strong>Import from csv file </strong>&nbsp;&nbsp;
                <?php
                if(in_array('E',$permissions)){
                    ?>
                    <a id="import-btn" href="<?php echo $this->Html->url(array('controller'=>'NpaUppf','action' => 'import_distances')); ?>" class="btn">
                        <i class="icon-arrow-up icon-white"></i> Import
                    </a>
                    <!--<button class="btn" type="button" id="import-btn">Select File</button>-->
                <?php
                }
                ?>
                <div class="arrow_down"></div>
            </div>

            <div class="head clearfix">
                <div class="isw-text_document"></div>
                <h1>Distances</h1>
            </div>
            <table id="flex" style="display:none;"></table>

        </div>
    </div>

    <div class="dr"><span></span></div>

</div>

<!-- URLs -->
<input type="hidden" id="table-url" value="<?php echo $this->Html->url(array('controller' => 'NpaUppf', 'action' => 'distances/get')); ?>" />
<input type="hidden" id="table-editable-url" value="<?php echo $this->Html->url(array('controller' => 'NpaUppf', 'action' => 'distances/save')); ?>" />
<input type="hidden" id="grid_delete_url" value="<?php echo $this->Html->url(array('controller' => 'NpaUppf', 'action' => 'distances/delete')); ?>" />
<input type="hidden" id="table-export-url" value="<?php echo $this->Html->url(array('controller' => 'NpaUppf', 'action' => 'export_distances')); ?>" />


<!-- Le Script -->
<?php
    echo $this->Html->script('scripts/npa_uppf_distances.js');
?>
