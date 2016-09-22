@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>邮箱管理</h1>
	<ol class="breadcrumb">
		<li>
			<i class="fa fa-dashboard"></i>系统管理
		</li>
		<li class="active">邮箱管理</li>
	</ol>
</section>
@endsection
@section('content')


<section class="content">
	<div class="row">
		<div class="col-sm-3">
			<div class='box'>
				<div class="box-header with-border">
					<h3 class="box-title">邮箱分类</h3>
				</div>
				<div class="box-body">

				<div class="input-group">
		           	<input type="text" class="form-control" id="paramsQueryEmail" aria-describedby="basic-addon1" placeholder="请输入模板名查询" />
		               <span class="input-group-btn">
			                <button class="btn btn-default" type="button" onClick="searchEmailQuery()">
			                     &nbsp;<span class="glyphicon glyphicon-search btn-group-lg" aria-hidden="true"></span>
			                </button>
			                <button class="btn btn-default" type="button" onClick="clearEmailQuery()">
								&nbsp;<span class="glyphicon glyphicon-remove btn-group-lg" aria-hidden="true"></span>
							</button>
		               	</span>
		            </div>

					<br />
					<div class="form-group"  style="height:600px; overflow:auto;overflow-x:hidden">
						<div id="EmailQueryTree"></div>
					</div>
					<input type="hidden" value="" id="emailFlag">
				</div>
			</div>	
		</div>
		<div class="col-sm-9">	          
			<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
				<div>
					<a id="editEmailFile" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onClick="editEmailFile()"><span class="ladda-label">编辑</span></a>
				</div>			
			</div>
			<div class="box">
				<div class="box-body">
		            <table id="emailTable">
		            
		            </table>
	            </div>
			</div>
		</div>
	</div>
</section>
<!-- 新增和修改用户弹出框 -->
<div class="modal fade" id="edit_email">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">&times;</span>
                </button>
                <h8 class="modal-title" id="mtitle">编辑邮箱</h8>
            </div>
		<!-- 	<form class="form-horizontal" role="form" id="emailForm"> -->
			<div class="modal-body text-center">
				<textarea class="form-control" name="emailFileContent" id="emailFileContent" style="height : 300px;resize: none;"></textarea>
				
					
				
			</div>
			<div class="modal-footer">
				<button type="submit" name="submit" class="col-sm-2 col-sm-offset-4 btn btn-primary" id="saveBtn" onclick="saveEmailFile()">保存</button>
				<button type="button" class="col-sm-2 btn btn-default" id="cancelBtn" data-dismiss="modal">取消</button>
			</div>
		<!-- 	</form> -->
		</div>
	</div>
</div>

@endsection


@section('scripts')


<!-- <link type="text/css" rel="stylesheet" href="plugins/datepicker/css/datepicker.css">
<script type="text/javascript" src="plugins/datepicker/js/bootstrap-datepicker.js"></script> -->

<!--input select-->
<!-- <script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" /> -->

<!--treeview-->
<script type="text/javascript" src="plugins/treeview/bootstrap-treeview.min.js"></script>

<!--datatables-->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="plugins/datatables/grid.js"></script>
<link type="text/css" rel="stylesheet" href="plugins/datatables/grid.css" >

<!--loading-->
<link rel="stylesheet" href="plugins/loading/css/ladda-themeless.min.css">
<script src="plugins/loading/js/spin.js"></script>
<script src="plugins/loading/js/ladda.js"></script>

<!--bootstrapvalidator-->
<!-- <link rel="stylesheet" href="plugins/bootstrapvalidator-master/css/bootstrapValidator.min.css">
<script src="plugins/bootstrapvalidator-master/js/bootstrapValidator.min.js"></script> -->
@endsection

<script type="text/javascript" src="plugins/jQuery/jquery.min.js"></script>
<script type="text/javascript" src="dist/js/systemManage/emailManage.js"></script>

