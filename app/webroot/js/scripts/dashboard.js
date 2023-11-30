$(function () {
    var chart;
    $(document).ready(function () {
        chart = new Highcharts.Chart({
            chart:{
                renderTo:'container',
                plotBackgroundColor:null,
                plotBorderWidth:null,
                plotShadow:false
            },
            title:{
                text:'Presidential General Election Result'
            },
            tooltip:{
                formatter:function () {
                    return '<b>' + this.point.name + '</b>: ' + this.percentage + ' %';
                }
            },
            plotOptions:{
                pie:{
                    allowPointSelect:true,
                    cursor:'pointer',
                    dataLabels:{
                        enabled:false
                    },
                    showInLegend:true
                }
            },
            series:[
                {
                    type:'pie',
                    name:'Candidate share',
                    data:[
                        [$('#p1_name').val(), parseInt($('#p1_score').val())],
                        [$('#p2_name').val(), parseInt($('#p2_score').val())]
                    ]
                }
            ]
        });

        /** Tree **/
        $("#list").treeview({
            animated:"fast",
            collapsed:true,
            unique:true,
            //persist: "cookie",
            toggle:function () {
                window.console && console.log("%o was toggled", this);
            }
        });
    });

});