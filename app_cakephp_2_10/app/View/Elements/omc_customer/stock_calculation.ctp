<div class="head clearfix">
    <div class="isw-archive"></div>
    <h1>Stock Calculation : <?php echo $format_date;?></h1>
    <ul class="buttons">
        <li>
            <a href="<?php echo $this->Html->url(array('controller' =>  $this->params['controller'], 'action' =>  'dashboard')); ?>" class="isw-refresh"></a>
        </li>
    </ul>
</div>
<div class="block-fluid">
    <script type="text/javascript">
        var bar_data = <?php echo json_encode($bar_data);?>;

        $(function () {
            $(document).ready(function() {
                var options = {
                    chart: {
                        renderTo: 'content_bar',
                        type: 'bar'
                    },
                    title: {
                        text: 'Stock Calculation'
                    },
                    subtitle: {
                        text: ''
                    },
                    xAxis: {
                        categories: bar_data['x-axis'],
                        title: {
                            text: null
                        }
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: '',
                            align: 'high'
                        },
                        labels: {
                            overflow: 'justify'
                        }
                    },
                    tooltip: {
                        valueSuffix: ' ltr'
                    },
                    plotOptions: {
                        bar: {
                            dataLabels: {
                                enabled: true
                            }
                        }
                    },
                    credits: {
                        enabled: false
                    },
                    series: bar_data['series']
                };

                var chart_pie = new Highcharts.Chart(options);
            });
        });

    </script>
    <style type="text/css">
        .highcharts-container{
            width: inherit !important;
        }
    </style>
    <div id="content_bar"></div>
</div>

