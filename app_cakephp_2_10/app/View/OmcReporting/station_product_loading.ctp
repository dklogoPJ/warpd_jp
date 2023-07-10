<script type="text/javascript">
    var bardata = <?php echo json_encode($bar_graph_data);?>;
    var x_axis = <?php echo json_encode($x_axis);?>;
    var graph_title = <?php echo json_encode($graph_title);?>;
</script>
<div class="workplace">

    <div class="page-header">
        <h1 style="font-size: 30px;">Station Monthly Product Loading <small>Report</small></h1>
    </div>

    <div class="row-fluid">
        <div class="span12">
            <div class="wBlock clearfix">
                <div class="wSpace">
                    <div id="bar-content" style="height:470px;"></div>
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
                                        text: 'Litres'
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
                                        return ''+this.series.name +': '+ jLib.formatNumber(this.y,'number',0) +' ltr';
                                    }
                                },
                                plotOptions: {
                                    column: {
                                        stacking: 'normal',
                                        dataLabels: {
                                            enabled: true,
                                            color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',
                                            formatter:function(){
                                                return jLib.formatNumber(this.y,'number',0)
                                            }
                                        },
                                        pointPadding: 0.2,
                                        borderWidth: 0
                                    }
                                },
                                series: bardata
                            };
                            var chart_bar = new Highcharts.Chart(options_bar);
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
                        <?php
                            foreach($t_head as $h){
                        ?>
                            <th><?php echo $h ;?></th>
                       <?php
                             }
                       ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                        foreach($t_body_data as $tbd_arr){
                        ?>
                            <tr>
                        <?php
                            foreach($tbd_arr as $key => $v){
                                if($key == 0){
                                    ?>
                                    <td><?php echo $v ;?></td>
                                    <?php
                                }
                                else{
                                    ?>
                                    <td><?php echo $controller->formatNumber($v,'money',0).'' ;?></td>
                                    <?php
                                }
                            }
                        ?>
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
                <h1>Data Filter Options </h1>
            </div>
            <?php echo $this->Form->create('Query', array('id' => 'form-query', 'inputDefaults' => array('label' => false,'div' => false)));?>
            <div class="block-fluid">
                <div class="row-form clearfix" style="border-top-width: 0px; padding: 5px 16px;">
                    <div class="span5">Start Date:</div>
                    <div class="span5">
                        <?php echo $this->Form->input('start_dt', array('id'=>'start_dt', 'class' => 'span2 date-masking validate[required]','default'=>$start_dt,'placeholder'=>'dd-mm-yyyy', 'div' => false, 'label' => false,)); ?>
                        <!---->
                    </div>
                    <!--<span>Example: 2012-12-01 (yyyy-mm-dd)</span>-->
                </div>

                <div class="row-form clearfix" style="border-top-width: 0px; padding: 3px 16px;">
                    <div class="span5">End Date:</div>
                    <div class="span5">
                        <?php echo $this->Form->input('end_dt', array('id'=>'end_dt', 'class' => 'span2 date-masking validate[required]','default'=>$end_dt,'placeholder'=>'dd-mm-yyyy', 'div' => false, 'label' => false,)); ?>
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

                <div class="row-form clearfix" style="border-top-width: 0px; padding: 3px 16px;">
                    <div class="span5">Station:</div>
                    <div class="span5">
                        <?php echo $this->Form->input('omc_customer', array('id'=>'omc_customer', 'class' => '','default'=>$default_omc_customer,'options'=>$omc_customerlists, 'div' => false, 'label' => false,)); ?>
                        <!---->
                    </div>
                    <!--<span>Example: 2012-12-01 (yyyy-mm-dd)</span>-->
                </div>

                <div class="footer tal">
                    <button class="btn" type="submit" id="query-btn">Re-Query </button>
                    <?php echo $this->Form->input('product_type_name', array('type'=>'hidden','id'=>'product_type_name', 'value'=>'')); ?>
                    <?php echo $this->Form->input('omc_customer_name', array('type'=>'hidden','id'=>'omc_customer_name', 'value'=>'' )); ?>
                </div>
                <?php echo $this->Form->end();?>
            </div>
        </div>
    </div>

    <form id="print-export-form" method="post" action="<?php echo $this->Html->url(array('controller' => 'OmcReporting', 'action' => 'print_export_station_product')); ?>" target="PrintExportWindow">
        <input type="hidden" name="data_type" id="data_type" value="" />
        <input type="hidden" name="data_start_dt" id="data_start_dt"  value="" />
        <input type="hidden" name="data_end_dt" id="data_end_dt"  value="" />
        <input type="hidden" name="data_product_type" id="data_product_type"  value="" />
        <input type="hidden" name="data_product_type_name" id="data_product_type_name"  value="" />
        <input type="hidden" name="data_omc_customer" id="data_omc_customer"  value="" />
        <input type="hidden" name="data_omc_customer_name" id="data_omc_customer_name"  value="" />
    </form>


    <div class="dr"><span></span></div>

</div>
<!-- Le Script -->
<?php
    echo $this->Html->script('scripts/omc/station_products.js');
    if(in_array('PX',$permissions)){
        echo $this->Html->script('highcharts/exporting.js');
    }
?>