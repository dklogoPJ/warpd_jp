<div class="head clearfix">
    <div class="isw-archive"></div>
    <h1>Daily Loading Summary : <?php echo $format_date;?></h1>
    <ul class="buttons">
        <li>
            <a href="<?php echo $this->Html->url(array('controller' =>  $this->params['controller'], 'action' =>  'dashboard')); ?>" class="isw-refresh"></a>
        </li>
    </ul>
</div>
<div class="block-fluid">
	<div class="wBlock clearfix">
		<div class="wSpace">
			<div id="today_yesterday_content_bar" style="height:270px;"></div>
		</div>
		<div class="dSpace">
			<h3>Today's Total Loading</h3>
			<span class="number"><?php echo $today_yesterday_totals['today']; ?></span>
			<span><b>LTRS</b></span>
			<!--<span>5,774 <b>Offloading</b></span>
			<span>3,512 <b>Uploading</b></span>-->
		</div>
		<div class="rSpace">
			<h3>Yesterday's Total Loading</h3>
			<span class="number" style="font-size: 20px; color: #FFF; font-weight: bold; line-height: 32px;">
                        <?php echo $today_yesterday_totals['yesterday']; ?>
                    </span>
			<span><b>LTRS</b></span>
			<!--<span>6500 <b>Offloading</b></span>
			<span>3500 <b>Uploading</b></span>-->
		</div>
		<script type="text/javascript">
			var bardata = <?php echo json_encode($bar_graph_data['data']);?>;
			var days = <?php echo json_encode($bar_graph_data['days']);?>;
			$(function () {
				$(document).ready(function() {
					var options_bar = {
						chart: {
							renderTo: 'today_yesterday_content_bar',
							type: 'column'
						},
						title: {
							text: 'Daily Loading'
						},
						xAxis: {
							categories: days
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
								return ''+
									this.series.name +': '+ jLib.formatNumber(this.y,'number',0) +' ltr';
							}
						},
						plotOptions: {
							column: {
								stacking: 'normal',
								/*dataLabels: {
								 enabled: true,
								 color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'
								 },*/
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
    <!--<script type="text/javascript">
        var dsl_bar_data = <?php /*echo json_encode($dsl_bar_data);*/?>;

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

    </script>-->
    <style type="text/css">
        .highcharts-container{
            width: inherit !important;
        }
    </style>
</div>

