@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>数据源管理</h1>
	<ol class="breadcrumb">
		<li>
			<i class="fa fa-dashboard"></i>系统管理
		</li>
		<li class="active">数据源管理</li>
	</ol>
</section>
@endsection
@section('content')


<section class="content">
	<div class="row">
		<div class="col-sm-12">
			<div class="box">
				<div class="box-header with-border">
					<h3 class="box-title">查询条件</h3>
				</div>
				<div class="box-body">
					<form class="form-horizontal" role="form" id="queryForm">
						<div class="form-group">
							<label for="node" class="col-sm-1 control-label">节点</label>
							<div class="col-sm-3">
								<select class="form-control" name="node" id="node">
								</select>
							</div>
							<label for="fileName" class="col-sm-1 control-label">文件名</label>
							<div class="col-sm-3">
								<select class="form-control" name="fileName" id="fileName" multiple="true">
								</select>
							</div>
							<label for="baseStation" class="col-sm-1 control-label">基站</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" name="baseStation" id="baseStation">
							</div>
							

						</div>
					</form>
				</div>
				<div class="box-footer">
					<div class="pull-right">
						<div class="btn-group">
		                    <button type="button" class="btn  btn-primary ladda-button" data-style="expand-right" onclick="query()" id="queryBtn">
		                        <i class="fa fa-search"></i>
		                        <span class="ladda-label">查询</span>
		                    </button>
		                </div>
		                <div class="btn-group">
		                    <button type="button" class="btn  btn-primary" onclick="exportFile()" id="exportBtn">
		                        <i class="fa fa-sign-out"></i>导出
		                    </button>
		                </div>
					</div>
				</div>
			</div>

			
		</div>
		
	</div>
	<div class="row">
		<div class="col-sm-12">
			<div class="box">
				<div class="box-body" style="height:400px;overflow:auto;">
					<table id="fileTable"></table>
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

<!--treeview-->
<!-- <script type="text/javascript" src="plugins/treeview/bootstrap-treeview.min.js"></script> -->

<!--select2-->
<!-- <script type="text/javascript" src="plugins/select2/select2.js"></script> -->

<!--datatables-->
<!-- <script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="plugins/datatables/grid.js"></script>
<link type="text/css" rel="stylesheet" href="plugins/datatables/grid.css" > -->

<!-- treegrid -->
<link rel="stylesheet" href="plugins/EasyUI/themes/bootstrap/easyui.css">
<link rel="stylesheet" href="plugins/EasyUI/themes/bootstrap/datagrid.css">
<link rel="stylesheet" href="dist/css/LTETemplateManage.css">
<script src="plugins/EasyUI/jquery.easyui.min.js"></script>

<!--loading-->
<link rel="stylesheet" href="plugins/loading/dist/ladda-themeless.min.css">
<script src="plugins/loading/js/spin.js"></script>
<script src="plugins/loading/js/ladda.js"></script>

<!--bootstrapvalidator-->
<!-- <link rel="stylesheet" href="plugins/bootstrapvalidator-master/css/bootstrapValidator.min.css">
<script src="plugins/bootstrapvalidator-master/js/bootstrapValidator.min.js"></script> -->


<!-- <script type="text/javascript" src="plugins/ajaxFileUpload/ajaxfileupload.js"></script> -->

@endsection

<script type="text/javascript" src="plugins/jQuery/jquery.min.js"></script>
<script type="text/javascript" src="dist/js/systemManage/dataSourceManage.js"></script>



