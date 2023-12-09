<div class="head clearfix">
    <div class="isw-archive"></div>
    <h1>Total Day Sales Cedis : <?php echo $format_date;?></h1>
    <ul class="buttons">
        <li>
            <a href="<?php echo $this->Html->url(array('controller' =>  $this->params['controller'], 'action' =>  'dashboard')); ?>" class="isw-refresh"></a>
        </li>
    </ul>
</div>
<div class="block-fluid">
    <script type="text/javascript">
        var dsc_bar_data = <?php echo json_encode($dsc_bar_data);?>;

        $(function () {
            $(document).ready(function() {
				var dsc_options = {
					chart: {
						renderTo: 'dsc_content_bar',
						type: 'column'
					},
					title: {
						text: ''
					},
					xAxis: {
						categories: dsc_bar_data['x-axis'],
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
							text: 'Cedis',
							align: 'middle'
						},
						labels: {
							overflow: 'justify'
						}
					},
					tooltip: {
						valueSuffix: ' Cedis'
					},
					series: dsc_bar_data['y-axis']
				};
				new Highcharts.Chart(dsc_options);
            });
        });

    </script>
    <style type="text/css">
        .highcharts-container{
            width: inherit !important;
        }
    </style>
    <div id="dsc_content_bar"></div>
</div>

