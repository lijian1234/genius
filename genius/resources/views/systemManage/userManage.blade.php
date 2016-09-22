@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>账户管理</h1>
	<ol class="breadcrumb">
		<li>
			<i class="fa fa-dashboard"></i>系统管理
		</li>
		<li class="active">用户管理</li>
	</ol>
</section>
@endsection
@section('content')


<section class="content">
	<div class="row">
		<div class="col-sm-12">	          
			<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
				<div>
					<a id="addUser" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onClick="addUser()"><span class="ladda-label">新增</span></a>
					<a id="deleteUser" class="btn btn-default ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="deleteUser()"><span class="ladda-label">删除</span></a> 
					<a id="deleteUser" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="editUser()"><span class="ladda-label">修改</span></a> 
				</div>			
			</div>
			<div class="box">
				<div class="box-body">
		            <table id="userTable">
		            
		            </table>
	            </div>
			</div>
		</div>
	</div>
</section>
<!-- 新增和修改用户弹出框 -->
<div class="modal fade" id="add_edit_user">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">&times;</span>
                </button>
                <h8 class="modal-title" id="mtitle">账户信息</h8>
            </div>
			<form class="form-horizontal" role="form" id="userForm">
			<div class="modal-body text-center">

					<input type="hidden" name="id" id="userId" value="">
					<div class="form-group">
						<label for="userName" class="col-sm-2 col-sm-offset-2 control-label">用户名：</label>
						<div class="col-sm-6">
							<input type="text" class="form-control" name="userName" id="userName" placeholder="用户名" maxlength="18">
						</div>
					</div>
					<div class="form-group">
						<label for="password" class="col-sm-2 col-sm-offset-2 control-label">密码：</label>
						<div class="col-sm-6">
							<input type="text" class="form-control" name="password" id="password" placeholder="密码" maxlength="18">
						</div>
					</div>
					<div class="form-group">
						<label for="type" class="col-sm-2 col-sm-offset-2 control-label">类型：</label>
						<div class="col-sm-6">
							<input type="text" class="form-control" name="type" id="type" placeholder="类型" maxlength="20">
						</div>
					</div>
					<div class="form-group">
						<label for="email" class="col-sm-2 col-sm-offset-2 control-label">email：</label>
						<div class="col-sm-6">
							<input type="email" class="form-control" name="email" id="email" placeholder="email" maxlength="255"/>
						</div>
					</div>
				
			</div>
			<div class="modal-footer">
				<button type="submit" name="submit" class="col-sm-2 col-sm-offset-4 btn btn-primary" id="saveBtn" onclick="updateUser()">保存</button>
				<button type="button" class="col-sm-2 btn btn-default" id="cancelBtn" data-dismiss="modal">取消</button>
			</div>
			</form>
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
<!-- <script type="text/javascript" src="plugins/treeview/bootstrap-treeview.min.js"></script> -->

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
<link rel="stylesheet" href="plugins/bootstrapvalidator-master/css/bootstrapValidator.min.css">
<script src="plugins/bootstrapvalidator-master/js/bootstrapValidator.min.js"></script>
@endsection

<script type="text/javascript" src="plugins/jQuery/jquery.min.js"></script>
<script type="text/javascript" src="dist/js/systemManage/userManage.js"></script>

