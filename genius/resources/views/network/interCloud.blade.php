@extends('layouts.nav')
@section('content-header')
<section class="content-header">
    <h1>
        高干扰分析
    </h1>
    <ol class="breadcrumb">
        <li><i class="fa fa-dashboard"></i> 专项研究</li>
        <li>高干扰分析</li>
        <li class='active'>干扰云图</li>
    </ol>
</section>
@endsection
@section('content')
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">干扰云图</h3>
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
                    <table class="table">
                        <tr>
                        	<td>城市</td>
                        	<th>
                        		<select id="allCity" class="form-control input-sm" multiple="multiple">
								</select>
                        	</th>
                            <td>日期</td>
                            <th>
                                <div class="input-group input-group-md" style="width:150px">
                                    <input id="date" class="form-control" type="text" value=""/>
                                </div>
                            </th>

                           	<td>小时</td>
							<th>				
								<select id="hour" class="form-control input-sm" style="width:150px">
									<option value='0'>0</option>
									<option value='1'>1</option>
									<option value='2'>2</option>
									<option value='3'>3</option>
									<option value='4'>4</option>
									<option value='5'>5</option>
									<option value='6'>6</option>
									<option value='7'>7</option>
									<option value='8'>8</option>
									<option value='9'>9</option>
									<option value='10'>10</option>
									<option value='11'>11</option>
									<option value='12'>12</option>
									<option value='13'>13</option>
									<option value='14'>14</option>
									<option value='15'>15</option>
									<option value='16'>16</option>
									<option value='17'>17</option>
									<option value='18'>18</option>
									<option value='19'>19</option>
									<option value='20'>20</option>
									<option value='21'>21</option>
									<option value='22'>22</option>
									<option value='23'>23</option>
								</select>
							</th>
                            <td>分钟</td>
							<th>				
								<select id="minute" class="form-control input-sm" style="width:150px">
									<option value='0'>0</option>
									<option value='15'>15</option>
									<option value='30'>30</option>
									<option value='45'>45</option>
								</select>
							</th>

							<td>频段</td>
								<th>
									<select id="channel" class="form-control input-sm" multiple="multiple">
									</select>
								</th>
                            <td>

                                <div style="text-align:right;">
                                    <a id="search" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onClick="drawMap()"><span class="ladda-label">查询</span></a>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="box-body">
                    <div id="map" style="position: relative;height: 600px;"></div>
                    <!-- ./box-body -->
                </div>
                
            </div>
        </div>
    </div>
</section>
        <!-- /.col -->
@endsection
@section('scripts')
    <script src="plugins/highcharts/js/highstock.js"></script>
    <script src="dist/js/genius/alarm-chart.js"></script>
    <link type="text/css" rel="stylesheet" href="plugins/datepicker/css/datepicker.css">
    <script type="text/javascript" src="plugins/datepicker/js/bootstrap-datepicker.js"></script>
    <!--datatables-->
    <script src="plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
    <script type="text/javascript" src="plugins/datatables/grid.js"></script>
    <link type="text/css" rel="stylesheet" href="plugins/datatables/grid.css" >
    <!--input select-->
	<script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
	<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" />

    <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=XtxLWdHvIBw0FKDLBh835SwO"></script>
    <script src="plugins/mapv/Mapv.js"></script>
    <script>
    	$("#date").datepicker({format: 'yyyy-mm-dd'});
        var nowTemp = new Date();
        $("#date").datepicker('setValue', nowTemp);

        //----------------------------------------------------------------------------------------------------------------------
        getAllCity();
        getChannels();

        //---------------------------------------------------------------------------------------------------------------------

        var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
        /*var checkin = $('#startTime').datepicker({
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
        });*/

        var checkin = $('#date').datepicker({
			onRender: function(date) {
			  return date.valueOf() < now.valueOf() ? '' : '';
			}
		  }).on('changeDate', function(ev) {
			checkin.hide();
			}).data('datepicker');

        var bmap = new BMap.Map("map");

        bmap.enableScrollWheelZoom(); // 启用滚轮放大缩小

        // 初始化地图,设置中心点坐标和地图级别
        bmap.centerAndZoom(new BMap.Point(120.602701, 32.227101), 10);

        bmap.addControl(new BMap.NavigationControl());

        bmap.addControl(new BMap.MapTypeControl());

        var mapv = new Mapv({
            drawTypeControl: false,
            map: bmap // 百度地图的map实例
        });


        //-------------------------------------------------------------------------------------------------------------------

        function getChannels(){

        	$('#channel').multiselect({
				dropRight: true,
				buttonWidth: 200,
				//enableFiltering: true,
				nonSelectedText:'请选择频段',
				//filterPlaceholder:'搜索',
				nSelectedText:'项被选中',
				includeSelectAllOption:true,
				selectAllText:'全选/取消全选',
				allSelectedText:'已全选',
				maxHeight:200,
				maxWidth:'100%'
				
			});

			var url = "interCloudChannel";

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
				  $('#channel').multiselect('dataprovider', newOptions);
				}
			});
        };

        toogle('interCloud');
        function drawMap(){
        	var S = Ladda.create( document.getElementById( 'search' ) );
        	S.start();
        	var returnData = []; // 取城市的点来做示例展示的点数据

			var date      = $('#date'). val();
			var hour	  = $('#hour').val();
			var minute	  = $('#minute').val();
			var channel	  = $('#channel').val();
			var citys     = $('#allCity').val();

 	//alert(citys);
			bmap.clearOverlays();

			if(date == '' || hour == '' || minute == '' || channel == '') {
				S.stop();
				alert('请用户输入选择信息');
				return;
			}

			//alert(date);
			//alert(hour);
			//alert(minute);
			//alert(channel);

			var params = {
				date:date,
				hour:hour,
				minute:minute,
				channel:channel				
			  };
			

			var url="interCloudCells";

			$.ajax({
				type:"GET",
				url:url,
				dataType:"json",
				data: {
					date: date,
					hour: hour,
					minute: minute,
					channel: channel.join(","),
					citys:citys
				},
				success:function(data){
					for (var i=0;i<data.length;i++) {
						returnData.push({
						lng: data[i].longitude,
						lat: data[i].latitude,
						count:data[i].PUSCH上行干扰电平
						});
					}


					var layer = new Mapv.Layer({
						mapv: mapv, // 对应的mapv实例
						zIndex: 1, // 图层层级
						dataType: 'point', // 数据类型mapv，点类型
						data: returnData, // 数据
						drawType: 'density', // 展示形式
						dataRangeControl: true ,
						drawOptions: { // 绘制参数
							type: "rect", // 网格类型，方形网格或蜂窝形
							size: 4, // 网格大小
							unit: 'px', // 单位
							opacity: '0.5',
							label: { // 是否显示文字标签
								show: true,
							},
							splitList: [
								{
									end: -120,
									color: 'blue'
								},{
									start: -120,
									end: -110,
									color: 'green'
								},{
									start: -110,
									end: -105,
									color: 'lime'
								},{
									start: -105,
									end: -100,
									color: 'yellow'
								},{
									start: -100,
									end: -90,
									color: 'magenta'
								},{
									start: -90,
									color: 'red'
								}
							],
							events: {
								click: function(e, data) {
									console.log('click',e, data)
								},
								// mousemove: function(e, data) {
								//     console.log('move',e, data)
								// }
							}
						},
					});
					S.stop();

				}
			});

			
			
        };


 function getAllCity(){
  $('#allCity').multiselect({
	  dropRight: true,
	  buttonWidth: '100%',
	  //enableFiltering: true,
	  nonSelectedText:'请选择城市',
	  //filterPlaceholder:'搜索',
	  nSelectedText:'项被选中',
	  includeSelectAllOption:true,
	  selectAllText:'全选/取消全选',
	  allSelectedText:'已选中所有平台类型',
	  maxHeight:200,
	  maxWidth:'100%'
  });
  var url = "LTEQuery/getAllCity";
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
	  $('#allCity').multiselect('dataprovider', newOptions);
	}
  });
}

    </script>
@endsection