<?php
$pie_data = $g_data['pie'];
?>
<script type="text/javascript">
    var pie_data = <?php echo json_encode($pie_data);?>;
    var graph_title = <?php echo json_encode($graph_title);?>;
</script>
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
                                // pointFormat: '{series.name}: <b>{point.y} ltrs {jLib.formatNumber(point.y,'money',1)}%</b>',
                                percentageDecimals: 1,
                                formatter: function(series) {
                                    return ''+this.series.name +':   <b>'+ jLib.formatNumber(this.point.y,'number',2) +' '+ jLib.formatNumber(this.point.percentage,'money',2) +'%';
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
                                            return '<b>'+ this.point.name  +'  <br /> '+jLib.formatNumber(this.point.y,'money',2)+'  ' + this.percentage.toFixed(2) +' %</b>: ';
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