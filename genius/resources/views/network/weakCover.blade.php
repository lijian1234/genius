@extends('layouts.nav')
@section('content-header')
<section class="content-header">
    <h1>
        弱覆盖分析
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">弱覆盖分析</a></li>
        <li class='active'>弱覆盖点图</li>
    </ol>
</section>
@endsection
@section('content')
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">弱覆盖点图</h3>
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
                	<form class="form-inline" role='form'>
                		<div class="form-group">
                			&nbsp;&nbsp;城市:
                			<select id="city" class="form-control input-sm" >
							</select>
                		</div>
                		<div class="form-group">
                			&nbsp;&nbsp;日期:
                			<!-- <select id="date" class="form-control input-sm" >
							</select> -->
							<input id="date" class="form-control" type="text" value=""/>
                		</div>
                		<div class="form-group">
                			&nbsp;&nbsp;忙时:
                			<select id="busyTime" class="form-control input-sm" >
                				<option value="earlyTime">早忙时</option>
                				<option value="laterTime">晚忙时</option>
							</select>
                		</div>
                		<div class="form-group">
                			<a id="search" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onClick="drawMap()"><span class="ladda-label">查询</span></a>
                	    </div>
                	</form>
                    <!-- <table class="table">
                        <tr>
                            <td>日期</td>
							<th>
								<select id="date" class="form-control input-sm" >
								</select>
							</th>
							<td>
                                <div style="text-align:right;">
                                    <a id="search" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onClick="drawMap()"><span class="ladda-label">查询</span></a>
                                </div>
                            </td>
                        </tr>
                    </table> -->
                </div>
                <div class="box-body">
                    <div id="map" style="position: relative;height: 600px;"></div>
                    <!-- ./box-body -->
                </div>

                <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				    <div class="modal-dialog" style="">
				        <div class="modal-content">
				            <div class="modal-header">
				                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
				                  &times;
				                </button>
				                <h4 class="modal-title" id="myModalLabel">
				                    柱状图显示
				                </h4>
				          	</div>
				        <div id="weakCoverChartsContainer" class="modal-body"></div>
				    </div>
				</div>
                
            </div>
        </div>
    </div>
</section>
<!-- <style>
.modal-dialog {
  height: 80% !important;
  padding-top:10%;
}

.modal-content {
  height: 100% !important;
  overflow:visible;
}

.modal-body {
  height: 80%;
  overflow: auto;
}
</style>
        /.col -->

<style>
.modal-dialog{
    position: relative;
    display: table; 
    overflow-y: auto;    
    overflow-x: auto;
    width: auto;
    min-width: 300px;   
}
</style>

@endsection
@section('scripts')
    <script src="dist/js/genius/alarm-chart.js"></script>
    <link type="text/css" rel="stylesheet" href="plugins/datepicker/css/datepicker.css">
    <script type="text/javascript" src="plugins/datepicker/js/bootstrap-datepicker.js"></script>
    <script src="plugins/highcharts/js/highcharts.js"></script>
    <!--datatables-->
    <script src="plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
    <script type="text/javascript" src="plugins/datatables/grid.js"></script>
    <link type="text/css" rel="stylesheet" href="plugins/datatables/grid.css" >

    <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=XtxLWdHvIBw0FKDLBh835SwO"></script>
    <script src="plugins/mapv/Mapv.js"></script>
    <script>

    $('#city').multiselect({
	    dropRight: true,
	    buttonWidth: 160,
	    //enableFiltering: true,
	    nonSelectedText:'请选择城市',
	    //filterPlaceholder:'搜索',
	    nSelectedText:'项被选中',
	    includeSelectAllOption:true,
	    selectAllText:'全选/取消全选',
	    allSelectedText:'已选中所有平台类型',
	    maxHeight:200,
	    width:220
	});
	var url = "NetworkOptimization/getAllCity";
	$.ajax({
			type:"GET",
		  	url:url,
		  	dataType:"json",
		  	success:function(data){
			    var newOptions = new Array();
			    var obj = new Object();
			    $(data).each(function(k,v){
				    var v = eval("("+v+")");
				    obj = {
				        label : v["text"],
				        value : v["value"]
				      };
			    	newOptions.push(obj);
			    });
			    $('#city').multiselect('dataprovider', newOptions);
		  	}  	
	});


	  $("#date").datepicker({format: 'yyyy-mm-dd'});
	  var nowTemp = new Date();
	  $("#date").datepicker('setValue', nowTemp);
	 //  var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
	  var checkin = $('#date').datepicker({
		onRender: function(date) {
		  return date.valueOf() < now.valueOf() ? '' : '';
		}
	  }).on('changeDate', function(ev) {
		checkin.hide();
	}).data('datepicker');

    $('#busyTime').multiselect({
				dropRight: true,
				buttonWidth: 200,
				//enableFiltering: true,
				//nonSelectedText:'请选择日期',
				//filterPlaceholder:'搜索',
				//nSelectedText:'项被选中',
				includeSelectAllOption:false,
				//selectAllText:'全选/取消全选',
				//allSelectedText:'已全选',
				maxHeight:200,
				maxWidth:'100%'
				
			});




    	toogle('weakCover');

    	//getDate();


        var bmap = new BMap.Map("map");

        bmap.enableScrollWheelZoom(); // 启用滚轮放大缩小

        bmap.disableDoubleClickZoom(); //禁止双击放大

        // 初始化地图,设置中心点坐标和地图级别
        bmap.centerAndZoom(new BMap.Point(120.602701, 32.227101), 10);

        //bmap.addControl(new BMap.NavigationControl());

        //bmap.addControl(new BMap.MapTypeControl());

        var mapv = new Mapv({
            drawTypeControl: false,
            map: bmap // 百度地图的map实例
        });





        function getDate()
        {
        	$('#date').multiselect({
				dropRight: true,
				buttonWidth: 200,
				//enableFiltering: true,
				//nonSelectedText:'请选择日期',
				//filterPlaceholder:'搜索',
				//nSelectedText:'项被选中',
				includeSelectAllOption:false,
				//selectAllText:'全选/取消全选',
				//allSelectedText:'已全选',
				maxHeight:200,
				maxWidth:'100%'
				
			});

			var url = "weakCoverDate";

			$.ajax({
				type:"GET",
				url:url,
				dataType:"json",
				success:function(data){
				  var newOptions = new Array();
				  var obj = new Object();
				  $(data).each(function(k,v){
					var v = eval("("+v+")");
					obj = {
							label : v["text"],
							value : v["value"]
						};
					newOptions.push(obj);
				  });
				  $('#date').multiselect('dataprovider', newOptions);
				}
			});

        };

         function drawMap(){

        	var S = Ladda.create( document.getElementById( 'search' ) );
        	S.start();
        	var returnData = []; // 取城市的点来做示例展示的点数据

        	var city = $('#city').val();
			var date      = $('#date'). val();
			var busyTime = $('#busyTime').val();
			// alert(date+city+busyTime);
			// return;

			bmap.clearOverlays();

			if(date.length=="") {
				S.stop();
				alert('请用户选择正确信息');
				return;
			}

			//alert(date);
			//alert(hour);
			//alert(minute);
			//alert(channel);

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

			// var params = {
			// 	date:date			
			//   };

			var params = {
				city:city,
				busyTime:busyTime,
				date:date			
			  };
			

			var url="weakCoverCells";


			$.ajax({
				type:"GET",
				url:url,
				dataType:"json",
				data: {
					//date: date
					city:city,
					busyTime:busyTime,
					date:date
				},
				beforeSend: function () {
                    $("map").html('<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">')
                },
				success:function(data){
					if(data == ''){
						alert('暂无数据！');
						S.stop();
						return;
					}
					for (var i=0;i<data.length;i++) {
						returnData.push({
						lng  : data[i].longitude,
						lat  : data[i].latitude,
						count: data[i].ratio110,
						dir  : data[i].dir,
						cell : data[i].ecgi,
						band : data[i].band,
						master: false
						});
					}


					var layer = new Mapv.Layer({
						mapv: mapv, // 对应的mapv实例
						zIndex: 1, // 图层层级
						dataType: 'point', // 数据类型，点类型
						data: returnData, // 数据
						drawType: 'choropleth', // 展示形式
						dataRangeControl: false ,
						drawOptions: { // 绘制参数
							size: 20, // 网格大小
							unit: 'px', // 单位
							strokeStyle: 'gray', // 描边颜色
							type: 'site',
							splitList: [
								{
									start: 0,
									end: 20,
									color: 'gray'
								},{
									start: 20,
									end: 100,
									color: 'red'
								}
							],
							events: {
								click: function(e, data) {
									//console.log('click',e, data)
								}
								
							}
						},
					});
					//S.stop();

					var returnDataFillColor=[];

					for (var i=0;i<data.length;i++) {
						if (data[i].ratio110<20) {
							continue;
						};
						returnDataFillColor.push({
						lng  : data[i].longitude,
						lat  : data[i].latitude,
						count: data[i].ratio110,
						dir  : data[i].dir,
						cell : data[i].ecgi,
						band : data[i].band,
						master: false
						});
					}


					var layer2 = new Mapv.Layer({
						mapv: mapv, // 对应的mapv实例
						zIndex: 1, // 图层层级
						dataType: 'point', // 数据类型，点类型
						data: returnDataFillColor, // 数据
						drawType: 'choropleth', // 展示形式
						dataRangeControl: false ,
						drawOptions: { // 绘制参数
							size: 20, // 网格大小
							unit: 'px', // 单位
							type: 'siteband',
							splitList: [
								{
									end: 20,
									color: 'gray'
								},{
									start: 20,
									color: 'red'
								}
							],
							events: {
								mousemove: function (e, data) {
                                    //console.log('click', e, data);
                                    $("#leftControl").children().remove();
                                    var li = '';
                                    for (var i = 0; i < data.length; i++) {
                                        li  += ("<li " + 'class="list-group-item"' + ">" + data[i].cell + "</li>");
                                    }
                                    //console.log(li);
                                    $("#leftControl").append(li);
                                },
								click: function(e, data) {
									//console.log('click',e, data);

									var cells = [];
                                    for(var i=0;i<data.length;i++) {
                                        cells.push(data[i].cell);
                                    }

                                    data2=$('#date'). val();

                                    var params = {
                                        cells: cells.join(","),
                                        //date : data2
                                        city:city,
										busyTime:busyTime,
										date:date
                                    }

                                    var url2= "weakCoverCharts";

                                    $.ajax({
										type:"GET",
										url: url2,
										dataType:"json",
										data: params,
										success:function(data){
										      var ser_str = JSON.stringify(data);

										      //var ser_str = JSON.stringify(data['series']);
										      
										      ser_str=ser_str.replace(/"/g,"");
										      ser_str=ser_str.replace(/A/g,"\"");

										      //ser_str=ser_str.replace(cell_str,"'"+cell_str+"'");

										      //alert(ser_str);   
										      var  ser_obj = eval("("+ser_str+")");

										      $('#weakCoverChartsContainer').highcharts({
										        exporting: {   
										            enabled:true,     
										        },
										        chart: {
										            type: 'column'
										        },
										        title: {
										            text: '弱覆盖小区信号强度分布显示柱状图'
										        },
										        subtitle: {
										            text: ""
										        },
										        xAxis: {
										            categories: [
										            	'signal>-80',
										            	'-80>=signal>-90',
										            	'-90>=signal>-100',
										            	'-100>=signal>-110',
										            	'signal<=-110'
										            	],
										            crosshair: true
										        },
										        yAxis: {
										            //max:-80,
										            min:0,
										            //tickPositions: [0, 5, 10, 15, 20, 25],
										            title: {
										                text: '落在各电平区间的数量 (个)'
										            }
										            //minTickInterval: 10
										        },
										        tooltip: {
										            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
										            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
										                '<td style="padding:0"><b>{point.y:.0f} 个</b></td></tr>',
										            footerFormat: '</table>',
										            shared: true,
										            useHTML: true
										        },
										        plotOptions: {
										            column: {
										                pointPadding: 0.2,
										                borderWidth: 0
										            }
										        },
										        credits: {
										            enabled: false
										        },
										        series: ser_obj
										    });

											$('#myModal').modal({
		                                        keyboard: false
		                                    });

										}
									});

									
									
								}
							}
						},
					});
					S.stop();

				}
			});

			
			
        };



    </script>
@endsection