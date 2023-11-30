<script type="text/javascript">
    var pie_data = <?php echo json_encode($pie_data);?>;
    var graph_title = <?php echo json_encode($graph_title);?>;
</script>
<div class="workplace">

    <div class="page-header">
        <h1 style="font-size: 30px;">Monthly Loading By Depots <small>Report</small></h1>
    </div>

    <div class="row-fluid">
        <div class="span12">
            <div class="wBlock clearfix">
                <div class="wSpace">
                    <div id="pie-content" style="height:470px;"></div>
                </div>
                <script type="text/javascript">
                    $(function () {
                        $(document).ready(function() {
                            var options_pie = {
                                chart: {
                                    renderTo: 'pie-content',
                                    type: 'pie',
                                    plotBackgroundColor: null,
                                    plotBorderWidth: null,
                                    plotShadow: false
                                },
                                title: {
                                    text: graph_title
                                },
                                tooltip: {
                                    //pointFormat: '{series.name}: <b>{point.y} ltrs {point.percentage}%</b>',
                                    percentageDecimals: 1,
                                    formatter: function(series) {
                                        return ''+this.series.name +':   <b>'+ jLib.formatNumber(this.point.y,'money',1) +' ltrs '+ jLib.formatNumber(this.point.percentage,'money',1) +'%';
                                    }
                                },
                                plotOptions: {
                                    pie: {
                                        showInLegend: true,
                                        allowPointSelect: true,
                                        cursor: 'pointer',
                                        dataLabels: {
                                            enabled: true,
                                            color: '#000000',
                                            //distance: -30,
                                            formatter: function() {
                                                return '<b>'+ this.point.name  +'  <br /> '+jLib.formatNumber(this.point.y,'money',1)+' ltrs,  ' + this.percentage.toFixed(1) +' %</b>: ';
                                            }
                                        }
                                    }
                                },
                                series: [{
                                    type: 'pie',
                                    name: 'share',
                                    data: pie_data
                                }]
                            };
                            var chart_pie = new Highcharts.Chart(options_pie);
                        });
                    });
                </script>
            </div>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span8">

            <div class="head clearfix">
                <div class="isw-grid"></div>
                <h1><?php echo $table_title ?> (ltrs)</h1>
                <ul class="buttons">
                    <li>
                        <?php
                        if(in_array('PX',$permissions)){
                            ?>
                            <button class="btn btn-success" type="button" id="print-btn">Print </button>
                            <button class="btn btn-success" type="button" id="export-btn">Export </button>
                        <?php
                        }
                        ?>
                        <!--<a href="#" class="isw-text_document"> Export</a>-->
                    </li>
                </ul>
            </div>
            <div class="block-fluid">
                <table cellpadding="0" cellspacing="0" width="100%" class="table">
                    <thead>
                    <tr>
                        <th>Depot</th>
                        <th>Total Quantity</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                        foreach($raw_data as $data){
                        ?>
                            <tr>
                                <td><?php echo $data[0] ;?></td>
                                <td><?php echo $controller->formatNumber($data[1],'money',0).'' ;?></td>
                            </tr>
                           <?php
                        }
                    ?>
                    </tbody>
                </table>
            </div>

        </div>
        <div class="span4">
            <div class="head clearfix">
                <div class="isw-brush"></div>
                <h1>Data Filter Options</h1>
            </div>
            <?php echo $this->Form->create('Query', array('id' => 'form-query', 'inputDefaults' => array('label' => false,'div' => false)));?>
            <div class="block-fluid">
                <div class="row-form clearfix" style="border-top-width: 0px; padding: 5px 16px;">
                    <div class="span5">Month:</div>
                    <div class="span5">
                        <?php echo $this->Form->input('month', array('id'=>'month', 'class' => '','default'=>$default_month,'options'=>$month_list, 'div' => false, 'label' => false,)); ?>
                        <!---->
                    </div>
                    <!--<span>Example: 2012-12-01 (yyyy-mm-dd)</span>-->
                </div>

                <div class="row-form clearfix" style="border-top-width: 0px; padding: 3px 16px;">
                    <div class="span5">Year:</div>
                    <div class="span5">
                        <?php echo $this->Form->input('year', array('id'=>'year', 'class' => 'span2 date-masking validate[required]','default'=>$year,'placeholder'=>'yyyy','maxlength'=>'4' , 'div' => false, 'label' => false,)); ?>
                        <!---->
                    </div>
                    <!--<span>Example: 2012-12-01 (yyyy-mm-dd)</span>-->
                </div>

                <div class="row-form clearfix" style="border-top-width: 0px; padding: 3px 16px;">
                    <div class="span5">Product:</div>
                    <div class="span5">
                        <?php echo $this->Form->input('product_type', array('id'=>'product_type', 'class' => '','default'=>$default_product_type,'options'=>$products_list, 'div' => false, 'label' => false,)); ?>
                        <!---->
                    </div>
                    <!--<span>Example: 2012-12-01 (yyyy-mm-dd)</span>-->
                </div>

                <div class="footer tal">
                    <button class="btn" type="submit" id="query-btn">Re-Query </button>
                    <?php echo $this->Form->input('product_type_name', array('type'=>'hidden','id'=>'product_type_name', 'value'=>'')); ?>
                </div>
                <?php echo $this->Form->end();?>
            </div>
        </div>
    </div>

    <form id="print-export-form" method="post" action="<?php echo $this->Html->url(array('controller' => 'BdcReporting', 'action' => 'print_export_depot_variant')); ?>" target="PrintExportWindow">
        <input type="hidden" name="data_type" id="data_type" value="" />
        <input type="hidden" name="data_year" id="data_year"  value="" />
        <input type="hidden" name="data_month" id="data_month"  value="" />
        <input type="hidden" name="data_product_type" id="data_product_type"  value="" />
        <input type="hidden" name="data_product_type_name" id="data_product_type_name"  value="" />
    </form>


    <div class="dr"><span></span></div>

</div>
<!-- Le Script -->
<?php
    echo $this->Html->script('scripts/report_monthly_depot_variant.js');
    if(in_array('PX',$permissions)){
        echo $this->Html->script('highcharts/exporting.js');
    }
?>