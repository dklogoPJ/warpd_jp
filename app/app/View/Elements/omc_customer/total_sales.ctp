<div class="head clearfix">
    <div class="isw-archive"></div>
    <h1>Total Sales : <?php echo $format_date;?></h1>
    <ul class="buttons">
        <li>
            <a href="<?php echo $this->Html->url(array('controller' =>  $this->params['controller'], 'action' =>  'dashboard')); ?>" class="isw-refresh"></a>
        </li>
    </ul>
</div>
<div class="block-fluid">
    <script type="text/javascript">
        var pie_data = <?php echo json_encode($pie_daily_sales_product);?>;

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
                        text: 'Total Sales'
                    },
                    tooltip: {
                        //pointFormat: '{series.name}: <b>{point.percentage}%</b>',
                        percentageDecimals: 1,
                        formatter: function(series) {
                            return ''+this.series.name +':   <b> '+ jLib.formatNumber(this.point.percentage,'number',1) +'%';
                        }
                    },
                    plotOptions: {
                        pie: {
                            showInLegend: true,
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: true,
                                color: '#ffffff',
                                distance: -30,
                                formatter: function() {
                                    //return '<b>'+ this.percentage.toFixed(1) +' %</b>: ';
                                    return '<b>'+ jLib.formatNumber(this.point.y,'money',0) +'</b>';
                                }
                            }
                        }
                    },
                    series: [{
                        type: 'pie',
                        name: 'sales share',
                        data: pie_data
                    }],
                    credits: {
                        enabled: false
                    }
                };
                var chart_pie = new Highcharts.Chart(options_pie);
            });
        });

    </script>
    <style type="text/css">
        .highcharts-container{
            width: inherit !important;
        }
    </style>
    <div id="pie-content"></div>
</div>

