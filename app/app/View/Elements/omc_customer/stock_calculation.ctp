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
        var sc_bar_data = <?php echo json_encode($sc_bar_data);?>;

        $(function () {
            $(document).ready(function() {
				var sc_options = {
					chart: {
						renderTo: 'sc_content_bar',
						type: 'column'
					},
					title: {
						text: ''
					},
					xAxis: {
						categories: sc_bar_data['x-axis'],
						/*title: {
							text: 'Products <br/> Category Tile'
						},*/
						/*labels: {
							enabled: false
						}*/
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
					plotOptions: {
						column: {
							pointPadding: 0.2,
							borderWidth: 0
						}
					},
					series: sc_bar_data['y-axis']
				};
				new Highcharts.Chart(sc_options);
            });
        });

    </script>
    <style type="text/css">
        .highcharts-container{
            width: inherit !important;
        }
    </style>
    <div id="sc_content_bar"></div>
</div>

