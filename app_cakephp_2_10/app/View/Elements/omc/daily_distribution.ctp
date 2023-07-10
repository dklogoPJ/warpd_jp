<script type="text/javascript">
    var delivery_locations = <?php echo json_encode($delivery_locations);?>;
    var bardata = <?php echo json_encode($bar_graph_data['data']);?>;
    var days = <?php echo json_encode($bar_graph_data['days']);?>;
    var pie_data = <?php echo json_encode($pie_data);?>;

    var customers = <?php echo json_encode($omc_customers_lists);?>;
    var region = <?php echo json_encode($regions_lists);?>;
    var district = <?php echo json_encode($district_lists);?>;
    var glbl_region_district = <?php echo json_encode($glbl_region_district);?>;
</script>
<div class="row-fluid">
    <div class="span4">
        <div class="wBlock clearfix">
            <div class="wSpace">
                <div id="bar-content" style="height:270px;"></div>
            </div>
            <div class="dSpace">
                <h3>Todays Total Loading</h3>
                <span class="number"><?php echo $today_yesterday_totals['today']; ?></span>
                <span><b>LTRS</b></span>
                <!--<span>5,774 <b>Offloading</b></span>
                <span>3,512 <b>Uploading</b></span>-->
            </div>
            <div class="rSpace">
                <h3>Yesterdays Total Loading</h3>
                    <span class="number" style="font-size: 20px; color: #FFF; font-weight: bold; line-height: 32px;">
                        <?php echo $today_yesterday_totals['yesterday']; ?>
                    </span>
                <span><b>LTRS</b></span>
                <!--<span>6500 <b>Offloading</b></span>
                <span>3500 <b>Uploading</b></span>-->
            </div>
            <script type="text/javascript">
                $(function () {
                    $(document).ready(function() {
                        var options_bar = {
                            chart: {
                                renderTo: 'bar-content',
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
    </div>

    <div class="span4">
        <div class="wBlock clearfix">
            <div class="wSpace">
                <div id="pie-content" style="height:360px;"></div>
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
                                text: 'Weekly Loading Consolidated'
                            },
                            tooltip: {
                                //pointFormat: '{series.name}: <b>{point.percentage}%</b>',
                                percentageDecimals: 1,
                                formatter: function(series) {
                                    return ''+this.series.name +':   <b> '+ jLib.formatNumber(this.point.percentage,'money',1) +'%';
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
                                            return '<b>'+ jLib.formatNumber(this.point.y,'number',0) +'</b>';
                                        }
                                    }
                                }
                            },
                            series: [{
                                type: 'pie',
                                name: 'weekly share',
                                data: pie_data
                            }]
                        };
                        var chart_pie = new Highcharts.Chart(options_pie);
                    });
                });
            </script>
        </div>
    </div>

    <div class="span4">
        <div class="head clearfix">
            <div class="isw-grid"></div>
            <h1>Today's Loading Data</h1>
            <ul class="buttons">
                <li class="toggle"><a href="#"></a></li>
            </ul>
        </div>
        <div class="block users scrollBox">

            <div class="scroll" style="height: 310px;">

                <?php
                foreach($liters_per_products as $pl){
                    ?>
                    <div class="item clearfix">
                        <div class="image">
                            <div class="ibb-donw_circle"></div>
                            <!-- <a href="#"><img src="img/users/aqvatarius_s.jpg" width="32"/></a>-->
                        </div>
                        <div class="info">
                            <a href="#" class="name"><?php echo $pl['name'] ;?></a>
                            <!--<span></span>-->
                            <div class="controls">
                                <?php echo $this->App->formatNumber($pl['qty'],'money',0) ;?> ltrs
                            </div>
                            <!--<div class="controls">
                                <a href="#" class="icon-ok"></a>
                                <a href="#" class="icon-remove"></a>
                            </div>-->
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>

        </div>
    </div>
</div>

<div class="dr"><span></span></div>

<div class="row-fluid">
    <div class="span12">

        <div class="head clearfix">
            <div class="isw-grid"></div>
            <h1>Daily Distribution Data</h1>
            <!--<ul class="buttons">
                <li><a href="#" class="isw-print"></a></li>
            </ul>-->
        </div>
        <div class="block-fluid">
            <table cellpadding="0" cellspacing="0" width="100%" class="table table_responsive">
                <!--<thead>-->
                <tr>
                    <th>Date</th>
                    <th>Order No</th>
                    <th>Waybill No</th>
                    <th>Waybill Date</th>
                    <th>BDC Name</th>
                    <th>Loading Depot</th>
                    <th>Product Type</th>
                    <th>Product Quantity</th>
                    <!--<th>Region</th>-->
                    <th>Transporter</th>
                    <th>Truck No</th>
                </tr>
                <!-- </thead>
                 <tbody>-->
                <?php
                foreach($grid_data as $data){
                    ?>
                    <tr>
                        <td><?php echo $this->App->covertDate($data['BdcDistribution']['created'],'mysql_flip'); ?></td>
                        <td><?php echo $data['BdcDistribution']['order_id'] ;?></td>
                        <td><?php echo $data['BdcDistribution']['waybill_id'] ;?></td>
                        <td><?php echo $this->App->covertDate($data['BdcDistribution']['waybill_date'],'mysql_flip'); ?></td>
                        <td><?php echo $data['Bdc']['name'] ;?></td>
                        <td><?php echo $data['Depot']['name'] ;?></td>
                        <td><?php echo $data['ProductType']['name'] ;?></td>
                        <td><?php echo $this->App->formatNumber(preg_replace('/,/','',$data['BdcDistribution']['quantity']),'money',0) ;?></td>
                        <!--<td><?php /*echo $data['Region']['name'] ;*/?></td>
                                <td><?php /*echo $data['District']['name'] ;*/?></td>-->
                        <td><?php echo $data['BdcDistribution']['transporter'] ;?></td>
                        <td><?php echo $data['BdcDistribution']['vehicle_no'] ;?></td>
                    </tr>
                <?php
                }
                ?>
                <!-- </tbody>-->
            </table>
        </div>

        <!-- <div class="pagination">
             <ul>
                 <li><a href="#">Prev</a></li>
                 <li class="active"><a href="#">1</a></li>
                 <li><a href="#">2</a></li>
                 <li><a href="#">3</a></li>
                 <li><a href="#">4</a></li>
                 <li><a href="#">Next</a></li>
             </ul>

         </div>-->

    </div>
</div>

