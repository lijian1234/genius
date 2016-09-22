@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>低接入小区</h1>
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-dashboard"></i>日常优化</a>
		</li>
		<li><a href="#"><i class="fa fa-dashboard"></i>差小区分析</a>
		</li>
		<li class="active"><a href="#">低接入小区</a>
		</li>
	</ol>
</section>
@endsection
@section('content')



<section class="content">
	<div class="row">
		<div class="col-sm-12">
			<div class='box'>
				<div class="box-header">
					<h3 class="box-title">查询条件</h3>
						<div class="box-tools pull-right">
	                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
	                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
	                    </div>
				</div>
				<div class="box-body">
					<table class="table">
									<tr>
										
										<td style="width: 66px;">城市</td>
										<th>
											<select id="allCity" class="form-control input-sm" multiple="multiple">
											</select>   
										</th>	

										<td>小区</td>
										<th>
											<div class="input-group input-group-md" style="width:100%">
												<input id="cellInput" class="form-control" type="text" value=""/>
											</div>
										</th>
									</tr>
									<tr>
										<td>起始日期</td>
										<th>
											<div class="input-group input-group-md" style="width:100%">
												<input id="startTime" class="form-control" type="text" value=""/>
											</div>
										</th>
										<td>结束日期</td>
										<th>
											<div class="input-group input-group-md"  style="width:100%">
												<input id="endTime" class="form-control" type="text" value=""/>										
											</div>
										</th>
									</tr>
								</table>
				</div>
				<div class="box-footer" style="text-align:right">
					
						<a id="search" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onClick="doSearchbadCell('table','低接入小区')"><span class="ladda-label ">查询</span></a>
						<a id="export" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="doSearchbadCell('file','低接入小区')"><span class="ladda-label">导出</span></a>
						<input id="badCellFile" value='' hidden="true" />	
						<input id='inputCategory' value='lowAccessCell' hidden="true" />
						<input id ="tableChoose" value='lowAccessCell' hidden="true" />
						<input id="chooseTable" value='lowAccessCell' hidden="true" />
					
				</div>
			</div>
			<div class="box">
				<div class='box-header'>
					<h3 class="box-title">小区列表</h3>
					<div class="box-tools pull-right">
	                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
	                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
	                </div>
				</div>
				<div class="box-body">
					<div class="table-responsive">
						
						<table id="badCellTable">
						</table>
					</div>
				</div>	
			</div>
			<div class="box">
				<div class="box-header">
					<h3 class="box-title">Alarm</h3>
						<div class="box-tools pull-right">
	                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
	                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
	                    </div>
				</div>
				<div class="box-body"  style="position:relative; height:400px; overflow:auto">
					<table class="table" id="alarmWorstCellTable">
					</table>
					<div class="zhaozi" id="alarm_zhaozi"></div>
				</div>
			</div>
			<div class="box" >
				<div class="box-header">
					<h3 class="box-title">趋势图</h3>
					<div class="box-tools pull-right">
	                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
	                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
	                </div>
				</div>
				<div class="box-body" style="position:relative;">
					<table class="table">
						<tr>
							<td >主轴</td>
								<th >
									<select id="worstCellChartPrimaryAxisType" class="form-control">
										<option value="无线接通率">无线接通率</option>
										<option value="RRC建立成功率">RRC建立成功率</option>
										<option value="ERAB建立成功率">ERAB建立成功率</option>
									</select>
								</th>

								<td>辅轴</td>
								<th >
									<select id="worstCellChartAuxiliaryAxisType" class="form-control">
										<option value="RRC建立请求次数">RRC建立请求次数</option>
										<option value="RRC建立成功次数">RRC建立成功次数</option>
										<option value="RRC建立失败次数">RRC建立失败次数</option>
										<option value="ERAB建立请求次数">ERAB建立请求次数</option>
										<option value="ERAB建立成功次数">ERAB建立成功次数</option>
										<option value="ERAB建立失败次数">ERAB建立失败次数</option>
									</select>
								</th>
								<input type="text" id='mapCell' hidden='true' />
						</tr>				
					</table>
					<div id="worstCellContainer" style="position: relative;height: 400px;"></div>
					<!-- <div id="worstCellContainer" style="width:100%;height:100%" ></div> -->
					<div class="zhaozi" id="chart_zhaozi"></div>
				</div>	
			</div>
			<div class="box">
				<div class="box-header">
					<h3 class="box-title">地理化分析</h3>
					<div class="box-tools pull-right">
	                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
	                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
	                </div>
				</div>
				<div class="box-body" style="position:relative;">
					<div id="map" style="position: relative;height: 600px;width:100%" ></div>
					<div class="zhaozi" id="map_zhaozi"></div>
				</div>
			</div>
			<div class="box">
				<div class="box-header">
					<h3 class="box-title">邻区分析</h3>
					<div class="box-tools pull-right">
	                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
	                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
	                </div>
				</div>
				<div class="box-body">
					<ul class="nav nav-tabs" role="tablist">
						<li class="active"><a href="#table_tab_0" data-toggle="tab" id="table_tab_0_nav"
							aria-expanded="false">LTE补邻区</a></li>
						<li class=""><a href="#table_tab_1" data-toggle="tab" id="table_tab_1_nav"
							aria-expanded="false">GSM补邻区</a></li>
					</ul>
					<div class="tabs tab-content ">
						<div class=" tab-pane active" id="table_tab_0" style="position:relative; height:500px; overflow:auto">
							<table id="LTETable"></table>
							<div class="zhaozi" id="LTE_zhaozi"></div>
						</div>
						<div class=" tab-pane" id="table_tab_1" style="position:relative; height:500px; overflow:auto">
							<table id="GSMTable"></table>
							<div class="zhaozi" id="GSM_zhaozi"></div>
						</div>
					</div>
				</div>
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



@endsection
@section('scripts')
<script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" />
<!-- jQuery 2.2.0 -->
<!-- datepicker -->
 <link type="text/css" rel="stylesheet" href="plugins/datepicker/css/datepicker.css">
 <script type="text/javascript" src="plugins/datepicker/js/bootstrap-datepicker.js"></script>

<!-- Bootstrap WYSIHTML5 -->

<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" />
<script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<script src="plugins/highcharts/js/highcharts.js"></script>

<!--loading-->
<link rel="stylesheet" href="plugins/loading/css/ladda-themeless.min.css">
<script src="plugins/loading/js/spin.js"></script>
<script src="plugins/loading/js/ladda.js"></script>

<script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=XtxLWdHvIBw0FKDLBh835SwO"></script>
<script src="plugins/mapv/Mapv.js"></script>


@endsection
<script src="plugins/jQuery/jquery-2.0.2.min.js"></script>
<script type="text/javascript" src="dist/js/badCellAnalysis/badCell.js"></script>

<style>
	.zhaozi{
		width:100%;
		height:100%;
		position:absolute;
		top:0;
		left:0;
		display:none;
		background-color:#000;
		opacity:.6;
	}
</style>
