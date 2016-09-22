@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>站点管理</h1>
	<ol class="breadcrumb">
		<li>
			<i class="fa fa-dashboard"></i>系统管理
		</li>
		<li class="active">站点管理</li>
	</ol>
</section>
@endsection
@section('content')


<section class="content">
	<div class="row">
		<div class="col-sm-12">
			<ul class="nav nav-tabs" role="tablist">
				<li class="active"><a href="#table_tab_0" data-toggle="tab" id="table_tab_0_nav"
					aria-expanded="false">4G站点</a></li>
				<li class=""><a href="#table_tab_1" data-toggle="tab" id="table_tab_1_nav"
					aria-expanded="false">2G站点</a></li>
			</ul>	
		</div>
		<!-- <div class="col-sm-12">   -->     
			<div class="tabs tab-content ">
				<div class=" tab-pane active" id="table_tab_0"">
					<div class="col-sm-3">
						<div class='box'>
							<!-- <div class="box-header with-border">
								<h3 class="box-title">邮箱分类</h3>
							</div> -->
							<div class="box-body">

								<div class="input-group">
						           <input type="text" class="form-control" id="paramsQuery4G" aria-describedby="basic-addon1" placeholder="请输入城市名查询" />
					               <span class="input-group-btn">
						                <button class="btn btn-default" type="button" onClick="search4GQuery()">
						                     &nbsp;<span class="glyphicon glyphicon-search btn-group-lg" aria-hidden="true"></span>
						                </button>
						                <button class="btn btn-default" type="button" onClick="clear4GQuery()">
											&nbsp;<span class="glyphicon glyphicon-remove btn-group-lg" aria-hidden="true"></span>
										</button>
					               	</span>
					            </div>

								<br />
								<div class="form-group"  style="height:600px; overflow:auto;overflow-x:hidden">
									<div id="4GQueryTree"></div>
								</div>
								<input type="hidden" value="" id="4GSiteValue">
							</div>
						</div>
					</div>

					<div class="col-sm-9">
						<div class="box">
							<div class="box-header">
								<a id="addUser" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onClick="import4G()"><span class="ladda-label">导入</span></a>
								<a id="deleteUser" class="btn btn-default ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="export4G()"><span class="ladda-label">导出</span></a> 
							</div>
							<div class="box-body">
					            <table id="4GTable">
					            
					            </table>
				            </div>
						</div>
						
			        </div>
				</div>
				<div class=" tab-pane" id="table_tab_1"">
					<div class="col-sm-3">
						<div class='box'>
							<!-- <div class="box-header with-border">
								<h3 class="box-title">邮箱分类</h3>
							</div> -->
							<div class="box-body">

								<div class="input-group">
					           		<input type="text" class="form-control" id="paramsQuery2G" aria-describedby="basic-addon1" placeholder="请输入城市名查询" />
					               <span class="input-group-btn">
						                <button class="btn btn-default" type="button" onClick="search2GQuery()">
						                     &nbsp;<span class="glyphicon glyphicon-search btn-group-lg" aria-hidden="true"></span>
						                </button>
						                <button class="btn btn-default" type="button" onClick="clear2GQuery()">
											&nbsp;<span class="glyphicon glyphicon-remove btn-group-lg" aria-hidden="true"></span>
										</button>
					               	</span>
					            </div>

								<br />
								<div class="form-group"  style="height:600px; overflow:auto;overflow-x:hidden">
									<div id="2GQueryTree"></div>
								</div>
								<input type="hidden" value="" id="2GSiteValue">
							</div>
						</div>
					</div>
					<div class="col-sm-9">
						<div class="box">
							<div class="box-header">
								<a id="addUser" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onClick="import2G()"><span class="ladda-label">导入</span></a>
								<a id="deleteUser" class="btn btn-default ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="export2G()"><span class="ladda-label">导出</span></a> 
							</div>
							<div class="box-body">
					            <table id="2GTable">
					            
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
					
				<input type="hidden" name="siteSign" id="siteSign" value="">
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
@endsection

<script type="text/javascript" src="plugins/jQuery/jquery.min.js"></script>


<script type="text/javascript" src="dist/js/systemManage/siteManage.js"></script>
<!-- <style>
	.nowrap > div{
		white-space:nowrap!important; 
	}
</style> -->

