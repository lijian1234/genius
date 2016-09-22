@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>同频补邻区</h1>
	<ol class="breadcrumb">
		<li>
			<i class="fa fa-dashboard"></i>网络规划
		</li>
		<li>
			邻区分析
		</li>
		<li class="active">同频补邻区</li>
	</ol>
</section>
@endsection
@section('content')


<section class="content">
	<div class="row">
		<div class="col-sm-12">
			<div class="box">
				<div class="box-header with-border">
					<h3 class="box-title">MRO数据</h3>
					<div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
				</div>
				<div class="box-body">
					<div class="box">
						<div class="box-header with-border">
							<h3 class="box-title">查询条件</h3>
						</div>
						<div class="box-body">
							<form class="form-inline" role="form" id="queryForm">
								<div class="form-group">
								城市：
								</div>
								<div class="form-group">
									<label class="sr-only"></label>
				    				<p class="form-control-static">
				    					<select id="city" class="form-control input-sm">
										</select>  
				    				</p>					
							  	</div>
							  	<div class="form-group">
								日期：
								</div>
								<div class="form-group">
									<label class="sr-only"></label>
				    				<p class="form-control-static">
				    					<input id="dateTime" class="form-control" type="text" value=""/> 
				    				</p>					
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
				                    <button type="button" class="btn  btn-primary ladda-button" data-style="expand-right" onclick="exportFile()" id="exportBtn">
				                        <i class="fa fa-sign-out"></i>导出
				                    </button>
				                </div>
							</div>
						</div>
					</div>
				</div>
				<div class="box-footer">
					<div class="box">
						<div class="box-body" style="height:600px;overflow:auto;">
							<table id="mroServeNeighTable"></table>
						</div>
					</div>
				</div>
			</div>
		
		</div>
		
	</div>
	<div class="row">
		<div class="col-sm-12">
			<div class="box">
				<div class="box-header with-border">
					<h3 class="box-title">MRE数据</h3>
					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown" onClick="openConfigInfo()"><i class="fa fa-wrench"></i></button>
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
				</div>
				<div class="box-body">
					<div class="box">
						<div class="box-header with-border">
							<h3 class="box-title">查询条件</h3>
						</div>
						<div class="box-body">
							<form class="form-inline" role="form" id="queryForm_mre">
								<div class="form-group">
								城市：
								</div>
								<div class="form-group">
									<label class="sr-only"></label>
				    				<p class="form-control-static">
				    					<select id="city_mre" class="form-control input-sm">
										</select>  
				    				</p>					
							  	</div>
							  	<div class="form-group">
								日期：
								</div>
								<div class="form-group">
									<label class="sr-only"></label>
				    				<p class="form-control-static">
				    					<input id="dateTime_mre" class="form-control" type="text" value=""/> 
				    				</p>					
							  	</div>
							  	<input type="hidden" id="input1" value="">		  	
								<input type="hidden" id="input2" value="">		  	
								<input type="hidden" id="input3" value="">		  	
								<input type="hidden" id="input4" value="">		  	
								<input type="hidden" id="input5" value="">	
								<input type="hidden" id="input6" value="">		  	
								<input type="hidden" id="input7" value="">		  	
								<input type="hidden" id="input8" value="">
							</form>
						</div>
						<div class="box-footer">
							<div class="pull-right">
								<div class="btn-group">
				                    <button type="button" class="btn  btn-primary ladda-button" data-style="expand-right" onclick="query_mre()" id="queryBtn_mre">
				                        <i class="fa fa-search"></i>
				                        <span class="ladda-label">查询</span>
				                    </button>
				                </div>
				                <div class="btn-group">
				                    <button type="button" class="btn  btn-primary ladda-button" data-style="expand-right" onclick="exportFile_mre()" id="exportBtn_mre">
				                        <i class="fa fa-sign-out"></i>导出
				                    </button>
				                </div>
							</div>
						</div>
					</div>
				</div>
				<div class="box-footer">
					<div class="box">
						<div class="box-body" style="height:600px;overflow:auto;">
							<table id="mreServeNeighTable"></table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- 配置信息弹出框 -->
<div class="modal fade" id="config_information">
	<div class="modal-dialog" style="width:900px;">
		<div class="modal-content">
			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">&times;</span>
                </button>
                <h8 class="modal-title" id="mtitle">配置信息</h8>
            </div>
			<form class="form-inline" role="form" id="configForm">
			<div class="modal-body text-center row">
				<form class="form-horizontal" role="form" id="configForm">
			<div class="modal-body text-left row" style="margin-left:150px; ">
			<div class="form-group col-md-12">
			(
			</div>
				<div class="form-group col-md-12">
		    		(上报目标小区的呼叫占比>=
				    <input type="text" name="input1Temp" id="input1Temp" placeholder="" style="width:50px">
				    %  AND    
					目标小区上报最少呼叫次（小时）>=
				    <input type="text"  name="input2Temp" id="input2Temp" placeholder="" style="width:40px">  AND  
				    目标小区前2强比例>=
				    <input type="text"  name="input3Temp" id="input3Temp" placeholder="" style="width:50px">% ) 
				</div>
			  	<div class="form-group col-md-12">
			  		OR
			  	</div>
			  	<div class="form-group col-md-12">
				    (目标小区上报平均呼叫次（小时）>=
				    <input type="text"  name="input4Temp" id="input4Temp" placeholder="" style="width:40px">  AND   
				    目标小区前2强比例>=
				    <input type="text" name="input5Temp" id="input5Temp" placeholder="" style="width:50px">% )
				</div>
			  	<div class="form-group col-md-12">
			  	 )
			  	 </div>
			  	 <div class="form-group col-md-12">
			  		AND
			  	</div>
			  	<div class="form-group col-md-12">
				    (SC-RSRP>=
				    <input type="text" name="input6Temp" id="input6Temp" placeholder="" style="width:40px">  AND
				    SC-RSRQ>=
				    <input type="text" name="input7Temp" id="input7Temp" placeholder="" style="width:40px">  AND   
				    Nc-RSRP>
			    	<input type="text" name="input8Temp" id="input8Temp" placeholder="" style="width:40px">)
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" name="saveBtn" class="col-sm-1 col-sm-offset-11 btn btn-primary" id="saveBtn" onclick="updateConfigInfo()">确定</button>
			</div>
		</div>
		</form>
	</div>
</div>
@endsection


@section('scripts')
<link type="text/css" rel="stylesheet" href="plugins/datepicker/css/datepicker.css">
<script type="text/javascript" src="plugins/datepicker/js/bootstrap-datepicker.js"></script>
<script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" />

<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="plugins/datatables/grid.js"></script>
<link type="text/css" rel="stylesheet" href="plugins/datatables/grid.css" >

<!--loading-->
<link rel="stylesheet" href="plugins/loading/dist/ladda-themeless.min.css">
<script src="plugins/loading/js/spin.js"></script>
<script src="plugins/loading/js/ladda.js"></script>

@endsection

<script type="text/javascript" src="plugins/jQuery/jquery.min.js"></script>
<script type="text/javascript" src="dist/js/NetworkOptimization/MRONeighAnalysis.js"></script>



