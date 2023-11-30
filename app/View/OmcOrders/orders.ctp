<?php

?>
<script type="text/javascript">
    var bardata = <?php echo json_encode($g_data['data']['y-axis']);?>;
    var x_axis = <?php echo json_encode($g_data['data']['x-axis']);?>;
    var graph_title = <?php echo json_encode($graph_title);?>;

    var customers = <?php echo json_encode($omc_customers_lists);?>;
    var bdc = <?php echo json_encode($bdc_list);?>;
    var depot = <?php echo json_encode($depot_lists);?>;
    var products = <?php echo json_encode($products_lists);?>;
    var bdclists = <?php echo json_encode($bdclists);?>;
    var order_filter = <?php echo json_encode($order_filter);?>;
    var bdc_depots = <?php echo json_encode($bdc_depots);?>;
    var permissions = <?php echo json_encode($permissions); ?>;
    var volumes = <?php echo json_encode($volumes); ?>;
</script>

<div class="workplace">

    <div class="page-header">
        <h1 style="font-size: 30px;">Create New Orders <small> Dashboard</small></h1>
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
                <h1>Order Management Table</h1>
                <ul class="buttons">
                    <li>
                        <?php
                        if(in_array('PX',$permissions)){
                            ?>
                            <button class="btn btn-success export-me-btn" type="button" id="" data-filter-elements="BDC:filter_bdc" data-url="<?php echo $this->Html->url(array('controller' => 'OmcOrders', 'action' => 'export_orders')); ?>">Export </button>
                        <?php
                        }
                        ?>
                    </li>
                </ul>
            </div>
            <table id="flex" style="display:none;"></table><br />

        </div>
    </div>

    <div class="dr"><span></span></div>

    <div class="row-fluid">
        <div class="span6">
            <div class="wBlock clearfix">
                <div class="wSpace">
                    <div id="bar-content" style="height:360px;"></div>
                </div>
                <script type="text/javascript">
                    $(function () {
                        $(document).ready(function() {
                            var options_bar = {
                                /* exporting:{
                                 url:'http://localhost/exporting-server/'
                                 },*/
                                chart: {
                                    renderTo: 'bar-content',
                                    type: 'column'
                                },
                                title: {
                                    text: graph_title
                                },
                                xAxis: {
                                    categories: x_axis
                                },
                                yAxis: {
                                    min: 0,
                                    title: {
                                        text: 'Quantity'
                                    },
                                    stackLabels: {
                                        enabled: true,
                                        style: {
                                            fontWeight: 'bold',
                                            color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                                        },
                                        formatter:function(){
                                            return jLib.formatNumber(this.total,'number',0)
                                        }
                                    }
                                },
                                tooltip: {
                                    formatter: function(series) {
                                        return ''+this.series.name +': '+ jLib.formatNumber(this.y,'number',0);
                                    }
                                },
                                plotOptions: {
                                    column: {
                                        stacking: 'normal',
                                        dataLabels: {
                                            enabled: false,
                                            color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'
                                        },
                                        pointPadding: 0.2,
                                        borderWidth: 0
                                    }
                                },
                                colors: [
                                    '#BFECBB',
                                    '#F1EBC5',
                                    '#EE968F',
                                    '#C8DFF1'
                                ],
                                series: bardata
                            };
                            var chart_bar = new Highcharts.Chart(options_bar);
                        });
                    });
                </script>
            </div>
        </div>
        <div class="span6">
            <div class="head clearfix">
                <div class="isw-grid"></div>
                <h1><?php echo $graph_title ;?></h1>
                <!--<ul class="buttons">
                    <li class="toggle"><a href="#"></a></li>
                </ul>-->
            </div>
            <div class="block users scrollBox">

                <div class="scroll" style="height: 310px;">

                    <?php
                    foreach($g_data['raw_data'] as $time => $arr){
                        foreach($arr as $key => $value){
                        ?>
                        <div class="item clearfix">
                            <div class="image">
                                <div class="ibb-donw_circle"></div>
                                <!-- <a href="#"><img src="img/users/aqvatarius_s.jpg" width="32"/></a>-->
                            </div>
                            <div class="info">
                                <a href="#" class="name"><?php echo $key ;?></a>
                                <span><?php echo $this->App->formatNumber($value,'money',0) ;?></span>
                                <div class="controls">

                                </div>
                                <!--<div class="controls">
                                    <a href="#" class="icon-ok"></a>
                                    <a href="#" class="icon-remove"></a>
                                </div>-->
                            </div>
                        </div>
                    <?php
                        }
                    }
                    ?>
                </div>

            </div>
        </div>
    </div>


</div>

<!-- URLs -->
<input type="hidden" id="table-url" value="<?php echo $this->Html->url(array('controller' => 'OmcOrders', 'action' => 'orders/get')); ?>" />
<input type="hidden" id="table-editable-url" value="<?php echo $this->Html->url(array('controller' => 'OmcOrders', 'action' => 'orders/save')); ?>" />
<input type="hidden" id="table-details-url" value="<?php echo $this->Html->url(array('controller' => 'OmcOrders', 'action' => 'orders/load_details')); ?>" />
<input type="hidden" id="grid_load_url" value="<?php echo $this->Html->url(array('controller' => 'OmcOrders', 'action' => 'orders/load')); ?>" />
<input type="hidden" id="grid_delete_url" value="<?php echo $this->Html->url(array('controller' => 'OmcOrders', 'action' => 'orders/delete')); ?>" />


<!-- This URL will be used by Ajax upload -->
<input type="hidden" id="get_attachments_url" value="<?php echo $this->Html->url(array('controller' => 'OmcOrders', 'action' => 'get_attachments')); ?>" />
<input type="hidden" id="ajax_upload_url" value="<?php echo $this->Html->url(array('controller' => 'OmcOrders', 'action' => 'attach_files')); ?>" />
<?php echo $this->element('ajax_upload');?>

<?php
echo $this->Html->script('scripts/omc/omc_order.js');
?>