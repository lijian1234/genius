@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>参数管理</h1>
	<ol class="breadcrumb">
		<li>
			<i class="fa fa-dashboard"></i>系统管理
		</li>
		<li class="active">参数管理</li>
	</ol>
</section>
@endsection
@section('content')


<section class="content">
	<div class="row">
		<div class="col-sm-12">
			<ul class="nav nav-tabs" role="tablist">
				<li class="active"><a href="#table_tab_0" data-toggle="tab" id="table_tab_0_nav"
					aria-expanded="false">baseline管理</a></li>
				<!-- <li class=""><a href="#table_tab_1" data-toggle="tab" id="table_tab_1_nav"
					aria-expanded="false">2G站点</a></li> -->
			</ul>	
		</div>
		<!-- <div class="col-sm-12">   -->     
			<div class="tabs tab-content ">
				<div class=" tab-pane active" id="table_tab_0"">
					<div class="col-sm-3">
						<div class='box'>
							<div class="box-header with-border">
								<h3 class="box-title">模板</h3>
								<div class="box-tools pull-right">
			                        <button type="button"  class="btn btn-box-tool" onClick="addMode()" title="新增模板"><i class="fa fa-plus"></i></button>
			                        <button type="button"  class="btn btn-box-tool"  onClick="deleteMode()" title="删除模板"><i class="fa fa-minus"></i></button>
			                	</div>
							</div>
							<div class="box-body">

								<div class="input-group">
						           <input type="text" class="form-control" id="baselineManageQuery" aria-describedby="basic-addon1" placeholder="查询" />
					               <span class="input-group-btn">
						                <button class="btn btn-default" type="button" onClick="searchBaselineManageQuery()">
						                     &nbsp;<span class="glyphicon glyphicon-search btn-group-lg" aria-hidden="true"></span>
						                </button>
						                <button class="btn btn-default" type="button" onClick="clearBaselineManageQuery()">
											&nbsp;<span class="glyphicon glyphicon-remove btn-group-lg" aria-hidden="true"></span>
										</button>
					               	</span>
					            </div>
								
								<br />
								<div class="form-group"  style="height:600px; overflow:auto;overflow-x:hidden">
									<div id="baselineManageTree"></div>
								</div>
								<!-- <input type="hidden" value="" id="4GSiteValue"> -->
							</div>
						</div>
					</div>

					<div class="col-sm-9">
						<div class="box">
							<div class="box-header">
								<a id="addUser" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onClick="importBaselineManage()"><span class="ladda-label">导入</span></a>
								<a id="deleteUser" class="btn btn-default ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="exportBaselineManage()"><span class="ladda-label">导出</span></a> 
							</div>
							<div class="box-body">
								<input type="hidden" id="templateName" value="">
                              	<input type="hidden" id="templateId" value="">
					            <table id="baselineManageTable">
					            
					            </table>
				            </div>
						</div>
						
			        </div>
				</div>
				
			</div>

		<!-- </div> -->
	</div>
</section>
<!-- 导入弹出框 -->
<div class="modal fade" id="import_modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">&times;</span>
                </button>
                <h8 class="modal-title" id="mtitle">导入</h8>
            </div>
			<div class="modal-body col-sm-12">
					
				<div class="col-sm-8 col-sm-offset-2">
					<div class="input-group">
		               <input type="text" class="form-control" id="fileImportName">
		               <input type="file" accept=".csv" class="hidden" name="fileImport" id="fileImport" onchange="toName(this)">
		               <span class="input-group-btn">
		                  <button class="btn btn-default" type="button" onclick="fileImport.click()">选择文件</button>
		               </span>
		            </div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="col-sm-2 col-sm-offset-4 btn btn-primary" id="importBtn" onclick="importFile()">确定</button>
				<button type="button" class="col-sm-2 btn btn-default" id="cancelBtn" data-dismiss="modal">取消</button>
			</div>
		</div>
	</div>
</div>
<!-- 新增模板弹出框 -->
<div class="modal fade" id="add_mode">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">&times;</span>
                </button>
                <h8 class="modal-title" id="mtitle">添加模板</h8>
            </div>
			<form class="form-horizontal" role="form" id="modeForm">
			<div class="modal-body text-center">

					<!-- <input type="hidden" name="formulaId" id="formulaId" value=""> -->
					<!-- <input type="hidden" name="formulaUser" id="formulaUser" value=""> -->
					<div class="form-group">
						<label for="modeName" class="col-sm-2 col-sm-offset-2 control-label">模板名称：</label>
						<div class="col-sm-6">
							<input type="text" class="form-control" name="modeName" id="modeName" placeholder="模板名称" maxlength="50">
						</div>
					</div>
					<div class="form-group">
						<label for="modeDescription" class="col-sm-2 col-sm-offset-2 control-label">描述:</label>
						<div class="col-sm-6">
							<textarea class="form-control" name="modeDescription" id="modeDescription" style="height : 100px;resize: none;" maxlength="500"></textarea>
						</div>
					</div>
					
				
			</div>
			<div class="modal-footer">
				<button type="submit" name="submit" class="col-sm-2 col-sm-offset-4 btn btn-primary" onclick="updateMode()">保存</button>
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
<link rel="stylesheet" href="plugins/bootstrapvalidator-master/css/bootstrapValidator.min.css">
<script src="plugins/bootstrapvalidator-master/js/bootstrapValidator.min.js"></script>


<script type="text/javascript" src="plugins/ajaxFileUpload/ajaxfileupload.js"></script>

<style>
	#baselineManageTable td div{
		width:100%;
		white-space:nowrap;
		overflow:hidden;
		text-overflow:ellipsis;
	}
</style>
@endsection

<script type="text/javascript" src="plugins/jQuery/jquery.min.js"></script>


<script type="text/javascript" src="dist/js/systemManage/paramsManage.js"></script>

