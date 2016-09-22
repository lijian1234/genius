var plygons = [];
var selected = "";
var bmap = new BMap.Map("scale-map");

// 初始化地图,设置中心点坐标和地图级别
bmap.centerAndZoom(new BMap.Point(120.602701, 31.807101), 8);

bmap.disableDoubleClickZoom();

//bmap.addControl(new BMap.NavigationControl());

bmap.addControl(new BMap.MapTypeControl());

//去除路网
bmap.setMapStyle({
    styleJson:[
        {
            "featureType": "poi",
            "elementType": "all",
            "stylers": {
                "color": "#ffffff",
                "visibility": "off"
            }
        },
        {
            "featureType": "road",
            "elementType": "all",
            "stylers": {
                "color": "#ffffff",
                "visibility": "off"
            }
        },
        {
            "featureType": "background",
            "elementType": "all",
            "stylers": {
                "color": "#ffffff"
            }
        }
    ]
});

// 创建地理编码实例
var myGeo = new BMap.Geocoder();

bmap.addEventListener("click", function(e){
    myGeo.getLocation(new BMap.Point(e.point.lng, e.point.lat), function(result){
        if (result){
            var addComp = result.addressComponents;
                for (var city in cityNames) {
                if (city == addComp.city) {
                    var params = {};
                    if (selected == addComp.city) {
                        plygons[addComp.city].setFillColor("");
                        params = {'city': 'province'};
                        selected = "";
                        smallBox('scale/meContextNum','#meContextNum');
                        smallBox('scale/cellNum','#cellNum');
                        smallBox('scale/slaveNum','#slaveNum');
                        smallBoxOnAutoKPI('scale/numOnAutoKPI');
                    } else {
                        if (selected != "") {
                            plygons[selected].setFillColor("");
                        }
                        setCityFillColor(addComp.city, cityNames[addComp.city]);
                        selected = addComp.city;
                        params = {'city': addComp.city};
                        smallBox('scale/meContextNumByCity','#meContextNum',params);
                        smallBox('scale/cellNumByCity','#cellNum',params);
                        smallBox('scale/slaveNumByCity','#slaveNum',params);
                        smallBoxOnAutoKPI('scale/numOnAutoKPIByCity',params);
                    }
                }
            }
        }
    });
});

var cityNames = {
    "常州市": '#ff0000',
    "无锡市": '#00ff00',
    "苏州市": '#0000ff',
    "镇江市": '#f00000',
    "南通市": '#0f0000',
};

function getBoundary(cityname){
    var bdary = new BMap.Boundary();
    bdary.get(cityname, function(rs){ // 异步加载
        var count = rs.boundaries.length; //行政区域的点有多少个
        var ply = {};
        for(var i = 0; i < count; i++){
            ply = new BMap.Polygon(rs.boundaries[i], {strokeWeight: 2, strokeColor: "#4169e1"}); //建立多边形覆盖物
        }
        bmap.addOverlay(ply);  //添加覆盖物
        plygons[cityname]=ply;
    });
}
    
function setCityFillColor(city,color) {
    plygons[city].setFillColor(color);
}

for (var key in cityNames) {
    getBoundary(key);
}

var bsc_version = function (route,block) {
    createChart(route,block);
};

var scaleExport = function(){
    var lscale = Ladda.create( document.getElementById( 'scaleExport' ) );
    $.ajax({
        type : "GET",
        url : "scaleExport",
        beforeSend : function () {
            lscale.start();
        },
        success : function(data) {
            lscale.stop();
            download(data);
        }
    });

};
var bsc_type = function (route,block) {
    $.ajax({
        type : "GET",
        url : route,
        //data : {range : "day"},
        dataType : "json",
        beforeSend : function () {
            $(block).html('<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">')
        },
        success: function(data) {
            $(block).highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: null
        },
        subtitle: {
            text: null
        },
        tooltip: {
            pointFormat: ': <b>{point.y}({point.percentage:.2f} %)</b>'
        },
        plotOptions: {
            pie: {
                size:'130px',
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.2f} %',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                },
                showInLegend: true
            }
        },
        credits: {
            enabled: false,
        },
        series: [{
            name: 'Brands',
            colorByPoint: true,
            data: data['series']
        }]
    });
        }
    })
    
};

var carrier = function () {
    // Create the chart
    $('#carrier').highcharts({
        chart: {
            type: 'pie'
        },
        title: {
            text: null
        },
        subtitle: {
            text: null
        },
        plotOptions: {
            series: {
                dataLabels: {
                    enabled: true,
                    format: '{point.name}: {point.y:.1f}%'
                }
            }
        },

        tooltip: {
            headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
            pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}%</b> of total<br/>'
        },
        credits: {
            enabled: false,
        },
        series: [{
            name: 'Brands',
            colorByPoint: true,
            data: [{
                name: 'D',
                y: 56,
                drilldown: 'D'
            }, {
                name: 'E',
                y: 24,
                drilldown: 'E'
            }, {
                name: 'F',
                y: 20,
                drilldown: 'F'
            }]
        }],
        drilldown: {
            series: [{
                name: 'D',
                id: 'D',
                data: [
                    ['D1', 24],
                    ['D2', 17],
                    ['D3', 59]
                ]
            }, {
                name: 'E',
                id: 'E',
                data: [
                    ['E1', 24],
                    ['E2', 17],
                    ['E3', 59]
                ]
            },{
                name: 'F',
                id: 'F',
                data: [
                    ['F1', 24],
                    ['F2', 17],
                    ['F3', 59]
                ]
            }]
        }
    });
};
function smallBox(route,block,params){
    $.get(route,params,function(data){
        if(params){
            setCityFillColor(params['city'],cityNames[params['city']])
        }
        $(block).html(data);
    });
}
function smallBoxOnAutoKPI(route,params){
    $.get(route,params,function(data){
        if(params != null){
            setCityFillColor(params['city'],cityNames[params['city']])
        }
        var data = JSON.parse(data);
        for(var key in data){
            if (data[key]) {
                 $("#"+key).html(data[key]);
            }else{
                $("#"+key).html(0);
            }
           
        }
    });
}
function rru_slave(route,block){
    $.ajax({
        type : "GET",
        url : route,
        //data : {range : "day"},
        dataType : "json",
        beforeSend : function () {
            $(block).html('<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">')
        },
        success: function(data) {
            $(block).html('');
            $(block).highcharts({
                chart: {
                    type: 'column'
                },
                title: {
                    text: null
                },
                xAxis: {
                    categories: data['category']
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: null
                    }
                },
                legend:{
                    enabled:false
                },
                credits: {
                    enabled: false,
                },
                series: data['series']
            });
        }
    })
}
function createChart(route,block){
    $.ajax({
        type : "GET",
        url : route,
        //data : {range : "day"},
        dataType : "json",
        beforeSend : function () {
            $(block).html('<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">')
        },
        success: function(data) {
            $(block).html('');
            $(block).highcharts({
                chart: {
                    type: 'column'
                },
                title: {
                    text: null
                },
                xAxis: {
                    categories: data['category']
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: null
                    }
                },
                credits: {
                    enabled: false,
                },
                series: data['series']
            });
        }
    });
}
function rru_slave_city(route,block){
    createChart(route,block);
}
jQuery(document).ready(function () {
   toogle('scale');
   bsc_type('scale/bscSiteType','#bscSiteType');
   bsc_type('scale/bscSlave',"#bscSlave");
   bsc_type('scale/bscCA',"#bscCA");
   bsc_version('scale/bscversion_type','#bscversion_type');
   bsc_version('scale/bscversion_city','#bscversion_city');
   carrier();
   smallBox('scale/meContextNum','#meContextNum');
   smallBox('scale/cellNum','#cellNum');
   smallBox('scale/slaveNum','#slaveNum');
   smallBoxOnAutoKPI('scale/numOnAutoKPI');
   rru_slave_city('scale/rruandSlave_city','#rruandSlave_city');
   rru_slave('scale/rruandSlave_slave','#rruandSlave_slave');

});