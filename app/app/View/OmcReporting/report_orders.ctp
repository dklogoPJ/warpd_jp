<script type="text/javascript">
    var bardata = <?php echo json_encode($g_data['data']['y-axis']);?>;
    var x_axis = <?php echo json_encode($g_data['data']['x-axis']);?>;
    var graph_title = <?php echo json_encode($graph_title);?>;
</script>
<div class="workplace">

    <div class="page-header">
        <h1 style="font-size: 30px;">Orders Data<small> Report</small></h1>
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
                                        text: 'Quantity (ltrs)'
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
                                            enabled: true,
                                            color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'black',
                                            formatter:function(){
                                                return jLib.formatNumber(this.y,'number',0);
                                            }
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
    </div>

    <div class="row-fluid">
        <div class="span8">

            <div class="head clearfix">
                <div class="isw-grid"></div>
                <h1><?php echo $table_title ?></h1>
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
                        foreach($g_data['table']['thead'] as $h){
                            ?>
                            <th><?php echo $h ;?></th>
                            <?php
                        }
                        ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach($g_data['table']['tbody'] as $tbd_arr){
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
                                <td><?php echo $this->App->formatNumber($v,'money',0).' ltrs' ;?></td>
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
                <h1>Data Filter Options</h1>
            </div>
            <?php echo $this->Form->create('Query', array('id' => 'form-query', 'inputDefaults' => array('label' => false,'div' => false)));?>
            <div class="block-fluid">
                <div class="row-form clearfix" style="border-top-width: 0px; padding: 5px 16px;">
                    <div class="span5">Start Date:</div>
                    <div class="span5">
                        <?php echo $this->Form->input('start_dt', array('id'=>'start_dt', 'class' => 'datepicker span2 date-masking validate[required]','default'=>$start_dt,'placeholder'=>'dd-mm-yyyy', 'div' => false, 'label' => false,)); ?>
                        <!---->
                    </div>
                </div>

                <div class="row-form clearfix" style="border-top-width: 0px; padding: 3px 16px;">
                    <div class="span5">End Date:</div>
                    <div class="span5">
                        <?php echo $this->Form->input('end_dt', array('id'=>'end_dt', 'class' => 'datepicker span2 date-masking validate[required]','default'=>$end_dt,'placeholder'=>'dd-mm-yyyy', 'div' => false, 'label' => false,)); ?>
                        <!---->
                    </div>
                </div>

                <div class="row-form clearfix" style="border-top-width: 0px; padding: 3px 16px;">
                    <div class="span5">BDCs:</div>
                    <div class="span5">
                        <?php echo $this->Form->input('bdc', array('id'=>'bdc', 'class' => '','default'=>$default_bdc,'options'=>$bdc_lists, 'div' => false, 'label' => false,)); ?>
                    </div>
                </div>

                <div class="row-form clearfix" style="border-top-width: 0px; padding: 3px 16px;">
                    <div class="span5">Group By:</div>
                    <div class="span5">
                        <?php echo $this->Form->input('group_by', array('id'=>'group_by', 'class' => '','default'=>$group_by,'options'=>array('monthly'=>'Monthly','yearly'=>'Yearly'), 'div' => false, 'label' => false,)); ?>
                    </div>
                </div>

                <div class="footer tal">
                    <button class="btn" type="submit" id="query-btn">Re-Query </button>
                    <?php echo $this->Form->input('bdc_name', array('type'=>'hidden','id'=>'bdc_name', 'value'=>'' )); ?>
                </div>
                <?php echo $this->Form->end();?>
            </div>
        </div>
    </div>

    <form id="print-export-form" method="post" action="<?php echo $this->Html->url(array('controller' => 'OmcReporting', 'action' => 'print_export_orders')); ?>" target="PrintExportWindow">
        <input type="hidden" name="data_type" id="data_type" value="" />
        <input type="hidden" name="data_start_dt" id="data_start_dt"  value="" />
        <input type="hidden" name="data_end_dt" id="data_end_dt"  value="" />
        <input type="hidden" name="data_group_by" id="data_group_by"  value="" />
        <input type="hidden" name="data_bdc" id="data_bdc"  value="" />
        <input type="hidden" name="data_bdc_name" id="data_bdc_name"  value="" />
    </form>


    <div class="dr"><span></span></div>

</div>
<!-- Le Script -->
<?php
    echo $this->Html->script('scripts/report_omc_orders.js');
    if(in_array('PX',$permissions)){
        echo $this->Html->script('highcharts/exporting.js');
    }
?>