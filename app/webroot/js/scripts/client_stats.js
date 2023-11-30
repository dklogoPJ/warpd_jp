$(function () {
    var chart;
    $(document).ready(function () {
        chart = new Highcharts.Chart({
            chart:{
                renderTo:'container',
                type:'column'
            },
            title:{
                text:'Monthly Test Taken Per Year'
            },
            subtitle:{
                text:'Source: pacem@rtheconsult.com'
            },
            xAxis:{
                categories:[
                    'Jan',
                    'Feb',
                    'Mar',
                    'Apr',
                    'May',
                    'Jun',
                    'Jul',
                    'Aug',
                    'Sep',
                    'Oct',
                    'Nov',
                    'Dec'
                ]
            },
            yAxis:{
                min:0,
                title:{
                    text:'Number of People'
                }
            },
            legend:{
                layout:'vertical',
                backgroundColor:'#FFFFFF',
                align:'left',
                verticalAlign:'top',
                x:100,
                y:70,
                floating:true,
                shadow:true
            },
            tooltip:{
                formatter:function () {
                    return '' +
                        this.x + ': ' + this.y + ' People';
                }
            },
            plotOptions:{
                column:{
                    pointPadding:0.2,
                    borderWidth:0
                }
            },
            series:[
                {
                    name:'CEO/COO/GM',
                    data:[49, 71, 106, 129, 144, 176, 135, 148, 216, 194, 95, 54]

                },
                {
                    name:'Senior Manager',
                    data:[83, 78, 98, 93, 106, 84, 105, 104, 91, 83, 106, 92]

                },
                {
                    name:'Junior Manager',
                    data:[48, 38, 39, 41, 47, 48, 59, 59, 52, 65, 59, 51]

                },
                {
                    name:'Supervisor',
                    data:[42, 33, 34, 39, 52, 75, 57, 60, 47, 39, 46, 51]

                },
                {
                    name:'Senior Staff',
                    data:[32, 33, 34, 39, 52, 75, 57, 60, 47, 39, 46, 51]

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