@extends('layouts.nav')
@section('content-header')
<section class="content-header">
    <h1>
        切换查询
    </h1>
    <ol class="breadcrumb">
        <li><i class="fa fa-dashboard"></i> 日常优化</li>
        <li>邻区分析</li>
        <li class='active'>切换查询</li>
    </ol>
</section>
@endsection 
@section('content')
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">查询条件</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <div class="btn-group">
                            <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-wrench"></i></button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="#">Print Chart</a></li>
                                <li class="divider"></li>
                                <li><a href="#">Download PNG img</a></li>
                                <li><a href="#">Download JPEG img</a></li>
                                <li><a href="#">Download PDF document</a></li>
                                <li><a href="#">Download SVG vector img</a></li>
                            </ul>
                        </div>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <form>
                            <div class="form-group col-sm-6">
                                <label>日期</label>
                                <div class="input-group input-group-md" style="width:100%">
                                    <input id="date" class="form-control" type="text" value=""/>
                                </div>
                            </div>
                            <div class="form-group col-sm-6">
                                <label>小区</label>
                                <div class="input-group input-group-md" style="width:100%">
                                    <input id="cell" class="form-control" type="text" value=""/>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="box-footer">
                    <div style="text-align:right;">
                        <a class="btn btn-primary"  href="#" role="button" onClick="drawMapOut();return false;">查询</a>
                        <a class="btn btn-primary"  href="#" role="button" onClick="paramQueryExport();return false;">导出</a>
                    </div>
                </div>
            </div>
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">查询结果</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <div class="btn-group">
                            <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-wrench"></i></button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="#">Print Chart</a></li>
                                <li class="divider"></li>
                                <li><a href="#">Download PNG img</a></li>
                                <li><a href="#">Download JPEG img</a></li>
                                <li><a href="#">Download PDF document</a></li>
                                <li><a href="#">Download SVG vector img</a></li>
                            </ul>
                        </div>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div id="map" style="position: relative;height: 400px;width:100%" ></div>
                    <!-- ./box-body -->
                </div>
                <!-- /.box -->
            </div>
            <div class="modal fade bs-example-modal-lg" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">Modal title</h4>
                        </div>
                        <div class="modal-body">
                            <table id='bMapTable' class="display" cellspacing="0" border="1">
                                <thead>
                                <tr>
                                    <th>id</th>
                                    <th>day_id</th>
                                    <th>city</th>
                                    <th>subNetwork</th>
                                    <th>cell</th>
                                    <th>EutranCellRelation</th>
                                    <th>切换成功率</th>
                                    <th>同频切换成功率</th>
                                    <th>异频切换成功率</th>
                                    <th>同频准备切换尝试数</th>
                                    <th>同频准备切换成功数</th>
                                    <th>同频执行切换尝试数</th>
                                    <th>同频执行切换成功数</th>
                                    <th>异频准备切换尝试数</th>
                                    <th>异频准备切换成功数</th>
                                    <th>异频执行切换尝试数</th>
                                    <th>准备切换成功率</th>
                                    <th>执行切换成功率</th>
                                    <th>准备切换尝试数</th>
                                    <th>准备切换成功数</th>
                                    <th>准备切换失败数</th>
                                    <th>执行切换尝试数</th>
                                    <th>执行切换成功数</th>
                                    <th>执行切换失败数</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary">Save changes</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
        <!-- /.col -->
@endsection
@section('scripts')
    <link type="text/css" rel="stylesheet" href="plugins/datepicker/css/datepicker.css">
    <script type="text/javascript" src="plugins/datepicker/js/bootstrap-datepicker.js"></script>
    <!--datatables-->
    <script src="plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
    <script type="text/javascript" src="plugins/datatables/grid.js"></script>
    <link type="text/css" rel="stylesheet" href="plugins/datatables/grid.css" >

    <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=XtxLWdHvIBw0FKDLBh835SwO"></script>
    <script src="plugins/mapv/Mapv.js"></script>
    <script>
    	toogle('switch');


        $("#date").datepicker({format: 'yyyy-mm-dd'});
        var nowTemp = new Date();
        $("#date").datepicker('setValue', nowTemp);

        var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
        var checkin = $("#date").datepicker({
            onRender: function(date) {
                return date.valueOf() < now.valueOf() ? '' : '';
            }
        }).on('changeDate', function(ev) {
            if (ev.date.valueOf() > checkout.date.valueOf()) {
                var newDate = new Date(ev.date)
                newDate.setDate(newDate.getDate() + 1);
                checkout.setValue(newDate);
            }
            checkin.hide();
        });

        function initMap(mapId) {

            var bmap = new BMap.Map(mapId);

            bmap.enableScrollWheelZoom(); // 启用滚轮放大缩小

            // 初始化地图,设置中心点坐标和地图级别
            bmap.centerAndZoom(new BMap.Point(120.602701, 32.227101), 10);

            //bmap.addControl(new BMap.NavigationControl());

            //bmap.addControl(new BMap.MapTypeControl());

            //自定义控件
            function staticControl(){
                this.defaultAnchor = BMAP_ANCHOR_TOP_RIGHT;
                this.defaultOffset = new BMap.Size(10,10);
            }
            //继承Control的API
            staticControl.prototype = new BMap.Control();
            //初始化控件
            staticControl.prototype.initialize=function(map){
                var div = document.createElement('div');
                var btn1 = document.createElement('button');
                btn1.setAttribute('class','btn btn-mini btn-primary');
                btn1.setAttribute('type','button');
                btn1.textContent ='切出';
                btn1.onclick = function () {
                    layerin.hide();
                    drawMapOut();
                }
                div.appendChild(btn1);

                var btn2 = document.createElement('button');
                btn2.setAttribute('class','btn btn-mini btn-primary');
                btn2.setAttribute('type','button');
                btn2.textContent ='切入';
                btn2.onclick = function () {
                    layerout.hide();
                    drawMapIn();
                }
                div.appendChild(btn2);
                //添加DOM元素到地图中
                map.getContainer().appendChild(div);
                //返回DOM
                return div;
            }
            //创建控件实例
            var staticsCtrl = new staticControl();
            //添加到地图当中
            bmap.addControl(staticsCtrl);

            //自定义控件
            function LeftControl(){
                this.defaultAnchor = BMAP_ANCHOR_TOP_LEFT;
                this.defaultOffset = new BMap.Size(10,10);
            }
            //继承Control的API
            LeftControl.prototype = new BMap.Control();
            //初始化控件
            LeftControl.prototype.initialize=function(map){
                var ul = document.createElement('ul');
                ul.setAttribute('class','list-group');
                ul.setAttribute('id','leftControl');
                var li = document.createElement('li');
                li.setAttribute('class','list-group-item');
                li.textContent = '请滑动鼠标查看小区名'
                ul.appendChild(li);
                //添加DOM元素到地图中
                map.getContainer().appendChild(ul);
                //返回DOM
                return ul;
            }
            //创建控件实例
            var leftCtrl = new LeftControl();
            //添加到地图当中
            bmap.addControl(leftCtrl);

            var mapv = new Mapv({
                drawTypeControl: false,
                map: bmap // 百度地图的map实例
            });

            $.ajax({
                type: "GET",
                url: "switchSite",
                dataType: "text",
                beforeSend: function () {
                    $("map").html('<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">')
                },
                success: function (data) {
                    var returnData = JSON.parse(data);
                    var vdata = [];
                    for (var i = 0; i < returnData.length; i++) {
                        vdata.push({
                            cell: returnData[i].cellName,
                            lng: returnData[i].longitude,
                            lat: returnData[i].latitude,
                            count: 5,
                            dir: returnData[i].dir,
                            band: returnData[i].band,
                        });
                    }

                    var layer = new Mapv.Layer({
                        mapv: mapv, // 对应的mapv实例
                        zIndex: 1, // 图层层级
                        dataType: 'point', // 数据类型，点类型
                        data: vdata, // 数据
                        drawType: 'choropleth', // 展示形式
                        dataRangeControl: false ,
                        drawOptions: { // 绘制参数
                            size: 20, // 点大小
                            unit: 'px', // 单位
                            strokeStyle: 'gray', // 描边颜色
                            type: 'site',
                            // splitList数值表示按数值区间来展示不同颜色的点
                            splitList: [
                                {
                                    start:0,
                                    end: 10,
                                    color: 'gray'
                                }
                            ],
                            events: {
                                mousemove: function (e, data) {
                                    console.log('click', e, data);
                                    $("#leftControl").children().remove();
                                    var li = '';
                                    for (var i = 0; i < data.length; i++) {
                                        li  += ("<li " + 'class="list-group-item"' + ">" + data[i].cell + "</li>");
                                    }
                                    console.log(li);
                                    $("#leftControl").append(li);
                                }
                            }
                        }
                    });
                }
            });

            return {"bmap":bmap,"mapv":mapv};
        }

        var mapv = initMap("map");

        var layerout = null;
        var layerin = null;

        var drawMapOut = function () {
            if(layerout != null) {
                layerout.hide();
            }

            $.ajax({
                type: "GET",
                url: "switchData",
                data: {date: document.getElementById("date").value,cell: document.getElementById("cell").value},
                dataType: "text",
                beforeSend: function () {
                    $("map").html('<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">')
                },
                success: function (data) {
                    var returnData = JSON.parse(data);
                    var vdata = [];
                    for(var i=0;i<returnData.length;i++) {
                        var count;
                        if(returnData[i].handoverAttemptCount == 0) {
                            count = 80;
                        }else if(returnData[i].handoverSuccessRatio <= 90 && returnData[i].handoverAttemptCount >= 50) {
                            count = 55;
                        }else {
                            count = 30;
                        }
                        vdata.push({
                            lng: returnData[i].slongitude,
                            lat: returnData[i].slatitude,
                            count: count,
                            dir: returnData[i].sdir,
                            band: returnData[i].sband,
                            master: false,
                            scell: returnData[i].scell
                        });
                    }
                    vdata.push({
                        lng:returnData[0].mlongitude,
                        lat: returnData[0].mlatitude,
                        count: -1,
                        dir:returnData[0].mdir,
                        band: returnData[0].mband,
                        master: true
                    });

                    var points = [];

                    for(var i=0;i<vdata.length;i++) {
                        points.push(new BMap.Point(vdata[i].lng,vdata[i].lat));
                    }

                    mapv.bmap.setViewport(points);

                    layerout = new Mapv.Layer({
                        mapv: mapv.mapv, // 对应的mapv实例
                        zIndex: 1, // 图层层级
                        dataType: 'point', // 数据类型，点类型
                        data: vdata, // 数据
                        drawType: 'choropleth', // 展示形式
                        dataRangeControl: false ,
                        drawOptions: { // 绘制参数
                            size: 20, // 点大小
                            unit: 'px', // 单位
                            type: 'switchout',
            				// splitList数值表示按数值区间来展示不同颜色的点
                            splitList: [
                                {
                                    end: 0,
                                    color: 'green'
                                },{
                                    start: 0,
                                    end: 50,
                                    color: 'blue'
                                },{
                                    start: 50,
                                    end: 60,
                                    color: 'red'
                                },{
                                    start: 60,
                                    end: 90,
                                    color: 'gray'
                                }
                            ],
                            events: {
                                click: function(e, data) {
                                    console.log('click',e,data);
                                    var scells = [];
                                    for(var i=0;i<data.length;i++) {
                                        scells.push(data[i].scell);
                                    }
                                    var params = {
                                        date: document.getElementById("date").value,
                                        cell: document.getElementById("cell").value,
                                        scells: scells
                                    }

                                    $("#bMapTable").DataTable( {
                                        "bAutoWidth": false,
                                        "destroy": true,
                                        "scrollX": true,
                                        //"processing": true,
                                        //"serverSide": true,
                                        //"aoColumnDefs":  [{ "sWidth": "500px",  "aTargets": [0] }],
                                        "ajax": {
                                            "url":"switchDetail",
                                            "data":params
                                        },
                                        "columns": [
                                            { "data": "id" },
                                            { "data": "day_id" },
                                            { "data": "city" },
                                            { "data": "subNetwork" },
                                            { "data": "cell" },
                                            { "data": "EutranCellRelation" },
                                            { "data": "切换成功率" },
                                            { "data": "同频切换成功率" },
                                            { "data": "异频切换成功率" },
                                            { "data": "同频准备切换尝试数" },
                                            { "data": "同频准备切换成功数" },
                                            { "data": "同频执行切换尝试数" },
                                            { "data": "同频执行切换成功数" },
                                            { "data": "异频准备切换尝试数" },
                                            { "data": "异频准备切换成功数" },
                                            { "data": "异频执行切换尝试数" },
                                            { "data": "准备切换成功率" },
                                            { "data": "执行切换成功率" },
                                            { "data": "准备切换尝试数" },
                                            { "data": "准备切换成功数" },
                                            { "data": "准备切换失败数" },
                                            { "data": "执行切换尝试数" },
                                            { "data": "执行切换成功数" },
                                            { "data": "执行切换失败数" }
                                        ]
                                    });

                                    $('#myModal').modal({
                                        keyboard: false
                                    })

                                },
//                                 mousemove: function(e, data) {
//                                     console.log('move', data)
//                                 }
                            }
                        }
                    });

                }
            })

        }

        var drawMapIn = function () {
            if(layerin != null) {
                layerin.hide();
            }

            $.ajax({
                type: "GET",
                url: "handoverin",
                data: {date: document.getElementById("date").value,cell: document.getElementById("cell").value},
                dataType: "text",
                beforeSend: function () {
                    $("map").html('<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">')
                },
                success: function (data) {
                    var returnData = JSON.parse(data);
                    var vdata = [];
                    for(var i=0;i<returnData.length;i++) {
                        var count;
                        if(returnData[i].handoverAttemptCount == 0) {
                            count = 80;
                        }else if(returnData[i].handoverSuccessRatio <= 90 && returnData[i].handoverAttemptCount >= 50) {
                            count = 55;
                        }else {
                            count = 30;
                        }
                        vdata.push({
                            lng: returnData[i].mlongitude,
                            lat: returnData[i].mlatitude,
                            count: count,
                            dir: returnData[i].mdir,
                            band: returnData[i].mband,
                            master: false,
                            cell: returnData[i].cell
                        });
                    }
                    vdata.push({
                        lng:returnData[0].slongitude,
                        lat: returnData[0].slatitude,
                        count: -1,
                        dir:returnData[0].sdir,
                        band: returnData[0].sband,
                        master: true
                    });

                    var points = [];

                    for(var i=0;i<vdata.length;i++) {
                        points.push(new BMap.Point(vdata[i].lng,vdata[i].lat));
                    }

                    mapv.bmap.setViewport(points);

                    layerin = new Mapv.Layer({
                        mapv: mapv.mapv, // 对应的mapv实例
                        zIndex: 1, // 图层层级
                        dataType: 'point', // 数据类型，点类型
                        data: vdata, // 数据
                        drawType: 'choropleth', // 展示形式
                        dataRangeControl: false ,
                        drawOptions: { // 绘制参数
                            size: 20, // 点大小
                            unit: 'px', // 单位
                            type: 'switchin',
                            // splitList数值表示按数值区间来展示不同颜色的点
                            splitList: [
                                {
                                    end: 0,
                                    color: 'green'
                                },{
                                    start: 0,
                                    end: 50,
                                    color: 'blue'
                                },{
                                    start: 50,
                                    end: 60,
                                    color: 'red'
                                },{
                                    start: 60,
                                    end: 90,
                                    color: 'gray'
                                }
                            ],
                            events: {
                                click: function(e, data) {
                                    console.log('click',e,data);
                                    var cells = [];
                                    for(var i=0;i<data.length;i++) {
                                        cells.push(data[i].cell);
                                    }
                                    var params = {
                                        date: document.getElementById("date").value,
                                        cell: document.getElementById("cell").value,
                                        cells: cells
                                    }

                                    $("#bMapTable").DataTable( {
                                        "bAutoWidth": false,
                                        "destroy": true,
                                        "scrollX": true,
                                        //"processing": true,
                                        //"serverSide": true,
                                        //"aoColumnDefs":  [{ "sWidth": "500px",  "aTargets": [0] }],
                                        "ajax": {
                                            "url":"handOverInDetail",
                                            "data":params
                                        },
                                        "columns": [
                                            { "data": "id" },
                                            { "data": "day_id" },
                                            { "data": "city" },
                                            { "data": "subNetwork" },
                                            { "data": "cell" },
                                            { "data": "EutranCellRelation" },
                                            { "data": "切换成功率" },
                                            { "data": "同频切换成功率" },
                                            { "data": "异频切换成功率" },
                                            { "data": "同频准备切换尝试数" },
                                            { "data": "同频准备切换成功数" },
                                            { "data": "同频执行切换尝试数" },
                                            { "data": "同频执行切换成功数" },
                                            { "data": "异频准备切换尝试数" },
                                            { "data": "异频准备切换成功数" },
                                            { "data": "异频执行切换尝试数" },
                                            { "data": "准备切换成功率" },
                                            { "data": "执行切换成功率" },
                                            { "data": "准备切换尝试数" },
                                            { "data": "准备切换成功数" },
                                            { "data": "准备切换失败数" },
                                            { "data": "执行切换尝试数" },
                                            { "data": "执行切换成功数" },
                                            { "data": "执行切换失败数" }
                                        ]
                                    });

                                    $('#myModal').modal({
                                        keyboard: false
                                    })

                                },
//                                 mousemove: function(e, data) {
//                                     console.log('move', data)
//                                 }
                            }
                        }
                    });

                }
            })
        }
    </script>
@endsection