<div class="head clearfix">
    <div class="isw-archive"></div>
    <h1>Total Day Sales Ltrs - All Customers : <?php echo $format_date;?></h1>
    <ul class="buttons">
        <li>
            <a href="<?php echo $this->Html->url(array('controller' =>  $this->params['controller'], 'action' =>  'dashboard')); ?>" class="isw-refresh"></a>
        </li>
    </ul>
</div>
<div class="block-fluid">
    <script type="text/javascript">
        var dsl_bar_data = <?php echo json_encode($dsl_bar_data);?>;

        $(function () {
            $(document).ready(function() {
				var dsl_options = {
					chart: {
						renderTo: 'dsl_content_bar',
						type: 'column'
					},
					title: {
						text: ''
					},
					xAxis: {
						categories: dsl_bar_data['x-axis'],
						title: {
							text: 'Products <br/> Category Tile'
						},
						labels: {
							enabled: false
						}
					},
					yAxis: {
						min: 0,
						title: {
							text: 'Ltr',
							align: 'middle'
						},
						labels: {
							overflow: 'justify'
						}
					},
					tooltip: {
						valueSuffix: ' ltr'
					},
					series: dsl_bar_data['y-axis']
				};
				new Highcharts.Chart(dsl_options);
            });
        });

    </script>
    <style type="text/css">
        .highcharts-container{
            width: inherit !important;
        }
    </style>
    <div id="dsl_content_bar"></div>
</div>

