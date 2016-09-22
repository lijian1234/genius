@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>入库管理</h1>
	<ol class="breadcrumb">
		<li>
			<i class="fa fa-dashboard"></i>系统管理
		</li>
		<li class="active">入库管理</li>
	</ol>
</section>
@endsection
@section('content')


<section class="content">
	<div class="row">
		<div class="col-sm-3">
			<div class='box'>
				<div class="box-header with-border">
					<h3 class="box-title">入库类型</h3>
				</div>
				<div class="box-body">

					<div class="input-group">
		           		<input type="text" class="form-control" id="paramsQueryStorage" aria-describedby="basic-addon1" placeholder="请输入入库类型查询" />
              		 	<span class="input-group-btn">
			                <button class="btn btn-default" type="button" onClick="searchStorageQuery()">
			                     &nbsp;<span class="glyphicon glyphicon-search btn-group-lg" aria-hidden="true"></span>
			                </button>
			                <button class="btn btn-default" type="button" onClick="clearStorageQuery()">
								&nbsp;<span class="glyphicon glyphicon-remove btn-group-lg" aria-hidden="true"></span>
							</button>
		               	</span>
	            	</div>

					<br />
					<div class="form-group"  style="height:600px; overflow:auto;overflow-x:hidden">
						<div id="storageQueryTree"></div>
					</div>
					<input type="hidden" value="" id="storageFlag">
				</div>
			</div>	
		</div>
		<div class="col-sm-9">	          
			<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
				<div class="box-tools">
                   <div class="btn-group">
                        <button type="button" class="btn  btn-primary" onclick="addTask()">
	                        <i class="fa fa-plus"></i>新建
	                    </button>
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-default" onclick="deleteTask()">
	                        <i class="fa fa-remove"></i>删除
	                    </button>
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary" onclick="runTask()">
	                        <i class="fa fa-play"></i>启动
	                    </button>
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn  btn-danger" onclick="stopTask()">
	                        <i class="fa fa-stop"></i>停止
	                    </button>
                    </div>
                    
                </div>		
			</div>
			<div class="box">
				<div class="box-body">
		            <table id="storageTable">
		            
		            </table>
	            </div>
			</div>
			<div class="box">
				<div class="box-header with-border">
					<h3 class="box-title">日志</h3>
				</div>
				<div class="box-body" style="height:200px; overflow:auto;overflow-x:hidden">
		            <p id="log" style="word-wrap: break-word;"></p>
	            </div>
			</div>
		</div>
	</div>
</section>
<!-- 新增弹出框 -->
<div class="modal fade" id="add_task">
	<div class="modal-dialog" style="width:900px;">
		<div class="modal-content">
			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">&times;</span>
                </button>
                <h8 class="modal-title" id="mtitle">New Paramter Task</h8>
            </div>
			<div class="modal-body row">
				<div class="col-sm-4">
					<div class='box'>
						<div class="box-header with-border">
							<h3 class="box-title">Data Trace</h3>
						</div>
						<div class="box-body">

							<div class="input-group">
								<input type="hidden" id="paramsQueryDataTrace_change">
				           		<input type="text" class="form-control" id="paramsQueryDataTrace" aria-describedby="basic-addon1" placeholder="请输入参数查询" />
		              		 	<span class="input-group-btn">
					                <button class="btn btn-default" type="button" onClick="searchDataTraceQuery()">
					                     &nbsp;<span class="glyphicon glyphicon-search btn-group-lg" aria-hidden="true"></span>
					                </button>
					                <button class="btn btn-default" type="button" onClick="clearDataTraceQuery()">
										&nbsp;<span class="glyphicon glyphicon-remove btn-group-lg" aria-hidden="true"></span>
									</button>
				               	</span>
			            	</div>

							<br />
							<div class="form-group" id="DataTraceQueryTreeDiv"  style="height:280px; overflow:auto;overflow-x:hidden;">
								<div id="DataTraceQueryTree"></div>
							</div>
							<input type="hidden" value="" id="dataTraceFlag">
						</div>
					</div>	
				</div>
				<div class="col-sm-4">
					<div class='box' id="chooseEventBox">
						<div class="box-header with-border">
							<h3 class="box-title">事件选择</h3>
						</div>
						<div class="box-body">
							<div class="form-group" style="height:334px; overflow:auto;;overflow-x:hidden;">
								<div id="eventQueryTree"></div>
							</div>
						</div>
					</div>

				</div>
				<div class="col-sm-4">
					<div class="box">
						<div class="box-header with-border">
							<h3 class="box-title">任务名称</h3>
						</div>
						<div class="box-body">
							<div class="form-group">
								<!-- <label for="taskName" class="control-label">任务名称：</label> -->
								<input type="text" class="form-control" name="taskName" id="taskName" placeholder="请输入任务名称" maxlength="18">
							</div>
						</div>
					</div>

				</div>
					
				
			</div>
			<div class="modal-footer">
				<button type="submit" name="submit" class="col-sm-1 col-sm-offset-5 btn btn-primary" id="saveBtn" onclick="saveTask()">保存</button>
				<button type="button" class="col-sm-1 btn btn-default" id="cancelBtn" data-dismiss="modal">取消</button>
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

<style>
.treeview .list-group-item {
    cursor: pointer;
    word-break: break-all;
}
#storageTable td div{
	width:100%;
	overflow:hidden;
	white-space:nowrap;
	text-overflow:ellipsis;
}
</style>
@endsection

<script type="text/javascript" src="plugins/jQuery/jquery.min.js"></script>
<script type="text/javascript" src="dist/js/systemManage/storageManage.js"></script>

