@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>信令分析</h1>
	<ol class="breadcrumb">
		<li>
			<i class="fa fa-dashboard"></i>投诉处理
		</li>
		<li class="active">信令分析</li>
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
							<label for="keyword" class="col-sm-1 control-label">关键字</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" name="keyword" id="keyword" placeholder="输入关键字查询">
							</div>
						</div>
					</form>
				</div>
				<div class="box-footer">
					<div class="btn-group pull-right">
	                    <button type="button" class="btn  btn-primary ladda-button" data-style="expand-right" onclick="queryKeyword()" id="queryBtn">
	                        <i class="fa fa-search"></i>
	                        <span class="ladda-label">查询</span>
	                    </button>
	                </div>
				</div>
			</div>
			
			
		</div>
	</div>

	<div class="row">
		<div class="col-sm-12">
			<div class="box">
				<div class="box-header with-border">
					<h3 class="box-title">查询结果</h3>
				</div>
				<div class="box-body" id="logBox" style="height:300px;overflow:auto">
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
<!-- <script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" /> -->

<!--select2-->
<!-- <script type="text/javascript" src="plugins/select2/select2.js"></script> -->

<!--treeview-->
<!-- <script type="text/javascript" src="plugins/treeview/bootstrap-treeview.min.js"></script> -->

<!--datatables-->
<!-- <script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="plugins/datatables/grid.js"></script> -->

<!--loading-->
<link rel="stylesheet" href="plugins/loading/dist/ladda-themeless.min.css">
<script src="plugins/loading/js/spin.js"></script>
<script src="plugins/loading/js/ladda.js"></script>

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


@endsection

<script type="text/javascript" src="plugins/jQuery/jquery.min.js"></script>
<script type="text/javascript" src="dist/js/complaintHandling/signalingAnalysis.js"></script>


