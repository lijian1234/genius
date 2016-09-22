/**
 * Created by wangyang on 2016/6/23.
 */
jQuery(document).ready( function(){
        toogle('weak');
        //chart_column('badCellOverview','#chart-badCell');
        badCellChart('badCellOverview','#chart-badCell');
})
var chartData;
var badCellData;
function badCellChart(route,block){
        $.ajax({
                type : "GET",
                url : route,
                data : {range : "day"},
                dataType : "json",
                beforeSend : function () {
                        $(block).html('<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">')
                },
                success: function(data) {
                        badCellData = data;
                        createBadCellChart(data,block);
                }
        })
}
function createBadCellChart(data,block){
        $(block).html('');
        $(block).highcharts({
                chart: {
                        type: 'column',
                },
                title: {
                        text: null
                },
                subtitle: {
                        text: 'Click the columns to view detail.'
                },
                xAxis: {
                        categories: data['category']
                },
                yAxis: {
                        min: 0,
                        title: {
                                text: 'the number of badCell'
                        },
                        stackLabels: {
                                enabled: true,
                                style: {
                                        fontWeight: 'bold',
                                        color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                                }
                        }
                },
                /*legend: {
                        align: 'right',
                        x: -30,
                        verticalAlign: 'top',
                        y: 25,
                        floating: true,
                        backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
                        borderColor: '#CCC',
                        borderWidth: 1,
                        shadow: false
                },*/
                tooltip: {
                        headerFormat: '<b>{point.x}</b><br/>',
                        pointFormat: '{series.name}: {point.y}({point.percentage:.0f}%)<br/>Total: {point.stackTotal}<br/>Click to the detail'

                },
                plotOptions: {
                        column: {
                                stacking: 'normal',
                                cursor: 'pointer',
                                point: {
                                        events: {
                                                click: function(event) {
                                                        var city = event.point.category;
                                                        createBadCellDonutPieChart(city,block);
                                                }
                                        }
                                },
                                dataLabels: {
                                        enabled: true,
                                        color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',
                                        style: {
                                                textShadow: '0 0 3px black'
                                        }
                                }
                        }

                },
                series: data['series']
                
        })
}
function createBadCellDonutPieChart(city,block){
         $.ajax({
                type: "GET",
                url: 'badCellOverview/drillDownDonutPie',
                data: {city: city},
                dataType: "text",
                beforeSend: function () {
                        $(block).html('<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">')
                },
                success: function (data) {
                        var colors = Highcharts.getOptions().colors;
                        var data = JSON.parse(data);
                        var badCellType_Data = data['badCellType_Data'];
                        for (var i = 0; i <= badCellType_Data.length - 1; i++) {
                                badCellType_Data[i]['color'] = colors[i];
                        };
                        $(block).highcharts({
                                chart: {
                                        type: 'pie'
                                },
                                title: {
                                        text: 'the detail of '+city
                                },
                                subtitle: {
                                        text: null
                                },
                                yAxis: {
                                        title: {
                                                text: null
                                        }
                                },
                                plotOptions: {
                                        pie: {
                                                shadow: false,
                                                center: ['50%', '50%'],
                                                cursor: 'pointer',
                                                point: {
                                                        events: {
                                                                click: function(event) {
                                                                        createBadCellChart(badCellData,block);
                                                                        $("#cellBackBtn").css('display','none');
                                                                }
                                                        }
                                                }
                                        }
                                },
                                tooltip: {
                                        pointFormat: ': <b>{point.y}({point.percentage:.2f} %)</b><br/>Click to back'
                                },
                                credits: {
                                        enabled: false,
                                },
                                series: [{
                                        name: 'badCellType',
                                        data: badCellType_Data,
                                        dataLabels: {
                                           format: '<b>{point.name}</b>:{point.y}({point.percentage:.2f} %)'
                                        }
                                }]
                        });
                        $("#cellBackBtn").css('display','block');
                        $('#cellBackBtn').click(function(){
                                createBadCellChart(badCellData,block);
                                $("#cellBackBtn").css('display','none');
                        });
                }
        });
}