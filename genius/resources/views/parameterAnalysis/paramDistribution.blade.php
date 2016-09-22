@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>参数分布</h1>
	<ol class="breadcrumb">
		<li>
			<i class="fa fa-dashboard"></i>日常优化
		</li>
		<li>
			<i class="fa fa-dashboard"></i>参数分析
		</li>
		<li class="active">参数分布</li>
	</ol>
</section>
@endsection
@section('content')


<section class="content">
	<div class="row">
		<div class="col-sm-3">
			<div class='box'>
				<div class="box-header with-border"> 
					<h3 class="box-title">日期</h3>
				</div>
				<div class="box-body">

					<select class="form-control" name="date" id="date">
					</select>

					
				</div>
			</div>	
			<div class='box'>
				<div class="box-header with-border">
					<h3 class="box-title">MO</h3>
				</div>
				<div class="box-body">

					<div class="input-group">
		           		<input type="text" class="form-control" id="queryMO" aria-describedby="basic-addon1" placeholder="请输入参数查询" />
              		 	<span class="input-group-btn">
			                <button class="btn btn-default" type="button" onClick="searchMO()">
			                     &nbsp;<span class="glyphicon glyphicon-search btn-group-lg" aria-hidden="true"></span>
			                </button>
			                <button class="btn btn-default" type="button" onClick="clearMO()">
								&nbsp;<span class="glyphicon glyphicon-remove btn-group-lg" aria-hidden="true"></span>
							</button>
		               	</span>
	            	</div>

					<br />
					<div class="form-group"  style="height:300px; overflow:auto;">
						<div id="MOQueryTree"></div>
					</div>
					<input type="hidden" value="" id="MOFlag">
				</div>
			</div>	

			<div class='box'>
				<div class="box-header with-border">
					<h3 class="box-title">参数</h3>
				</div>
				<div class="box-body">

					<div class="input-group">
		           		<input type="text" class="form-control" id="queryParam" aria-describedby="basic-addon1" placeholder="请输入参数查询" />
              		 	<span class="input-group-btn">
			                <button class="btn btn-default" type="button" onClick="searchParam()">
			                     &nbsp;<span class="glyphicon glyphicon-search btn-group-lg" aria-hidden="true"></span>
			                </button>
			                <button class="btn btn-default" type="button" onClick="clearParam()">
								&nbsp;<span class="glyphicon glyphicon-remove btn-group-lg" aria-hidden="true"></span>
							</button>
		               	</span>
	            	</div>

					<br />
					<div class="form-group"  style="height:300px; overflow:auto;overflow-x:hidden">
						<div id="paramQueryTree"></div>
					</div>
					<input type="hidden" value="" id="idNum">
					<input type="hidden" value="" id="paramFlag">
				</div>
			</div>
		</div>
		<div class="col-sm-9">	          
			
			<div class="box">
				<div class="box-header with-border">
					<h3 class="box-title">分布图</h3>
				</div>
				<div class="box-body">
		            <div id="parameterDistributeView" style=" heigth:540px;"></div>
	            </div>
			</div>
			<div class="box">
				<div class="box-header with-border">
					<h3 class="box-title">详情</h3>
				</div>
				<div class="box-body">
					<form class="form-horizontal" role="form" id="queryForm">
						<div class="form-group">
							
							<label for="citys" class="col-sm-2 control-label">城市：</label>
							<div class="col-sm-4">
								<select class="form-control" name="citys" id="citys" multiple="multiple">
								</select>
							</div>
						</div>
					</form>
				</div>
				<div class="box-footer">
					<div class="pull-right">
	                    <button type="button" class="btn  btn-primary" onclick="queryByCity()">
	                        <i class="fa fa-search"></i>查询
	                    </button>
	                    <button type="button" class="btn  btn-primary" onclick="exportByCity()">
	                        <i class="fa fa-sign-out"></i>导出
	                    </button>
	                </div>
				</div>
				<div class="box-body">
		            <table id="parameterDistributeTable">
		            </table>
	            </div>
			</div>
		</div>
	</div>

	
</section>

@endsection


@section('scripts')


<!-- <link type="text/css" rel="stylesheet" href="plugins/datepicker/css/datepicker.css">
<script type="text/javascript" src="plugins/datepicker/js/bootstrap-datepicker.js"></script> -->

<!--input select-->
<script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" />

<!--select2-->
<script type="text/javascript" src="plugins/select2/select2.js"></script>

<!--treeview-->
<script type="text/javascript" src="plugins/treeview/bootstrap-treeview.min.js"></script>

<!--datatables-->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="plugins/datatables/grid.js"></script>

<!--loading-->
<link rel="stylesheet" href="plugins/loading/css/ladda-themeless.min.css">
<script src="plugins/loading/js/spin.js"></script>
<script src="plugins/loading/js/ladda.js"></script>

<!-- highcharts -->
<script src="plugins/highcharts/js/highcharts.js"></script>
<script src="plugins/highcharts/js/modules/exporting.js"></script>

<!-- raphael -->
<!-- <script src="plugins/raphael/raphael-min.js"></script> -->

<!-- treegrid -->
<!-- <link rel="stylesheet" href="plugins/EasyUI/themes/bootstrap/easyui.css">
<link rel="stylesheet" href="plugins/EasyUI/themes/bootstrap/datagrid.css">
<link rel="stylesheet" href="dist/css/signalingBacktracking.css">
<script src="plugins/EasyUI/jquery.easyui.min.js"></script>
<script src="plugins/EasyUI/locale/datagrid-scrollview.js"></script> -->



<!--bootstrapvalidator-->
<!-- <link rel="stylesheet" href="plugins/bootstrapvalidator-master/css/bootstrapValidator.min.css">
<script src="plugins/bootstrapvalidator-master/js/bootstrapValidator.min.js"></script> -->

<style>
	.select2-container .select2-selection--single{
		height:34px;
		border-radius:0;
	   	border: 1px solid #d2d6de;
	}
	.select2-container--default .select2-selection--single .select2-selection__arrow{
		top:3px;
	}
	.node-MOQueryTree,
	.node-paramQueryTree{
		word-break: break-all;
	}
</style>

<script type="text/javascript" src="dist/js/parameterAnalysis/paramDistribution.js"></script>
@endsection

<script type="text/javascript" src="plugins/jQuery/jquery.min.js"></script>



