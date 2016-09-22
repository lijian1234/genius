@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>NBI指标查询</h1>
	<ol class="breadcrumb">
		<li><i class="fa fa-dashboard"></i>日常优化
		</li>
		<li>指标分析
		</li>
		<li class="active">NBI指标查询</li>
	</ol>
</section>
@endsection
@section('content')



<section class="content">
	<div class="row">
		<div class="col-sm-3">
			<div class="box">
				<div class="box-header with-border">
					<h3 class="box-title">模板</h3>
					<div class="box-tools pull-right">
                        <div class="btn-group">
	                        <a class="btn btn-box-tool fa fa-wrench" href="NBITemplateManage"></a>
                        </div>
                    </div>
				</div>

				<div class="box-body">
					  	<div class="input-group">
					    	<input type="text" class="form-control" id="paramQueryMoErbs" aria-describedby="basic-addon1" placeholder="请输入模板名查询" />
							<span class="input-group-btn">
								<button class="btn btn-default" type="button" onClick="searchNBIQuery()">
								&nbsp;<span class="glyphicon glyphicon-search btn-group-lg" aria-hidden="true"></span>
			                	</button>
								<button class="btn btn-default" type="button" onClick="clearNBIQuery()">
								&nbsp;<span class="glyphicon glyphicon-remove btn-group-lg" aria-hidden="true"></span>
								</button>
							</span>
						</div>
						<br/>

						<div class="form-group" style="height:600px; overflow:auto;overflow-x:hidden">
					  		<div id="NBIQueryMoTree"></div>
						</div>
				</div>

			</div>
		</div>
		<div class="col-sm-9">	
			<div class="box">  
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
							<td style="width:15%">区域维度</td>
								<th  style="width:35%">
									<select id="locationDim" class="form-control input-sm">
										<option value='city'>城市</option>
										<option value='erbs'>基站</option>
										<option value='cell'>小区</option>
										<option value='cellGroup'>小区组</option>
									</select>
								</th>
								<td style="width:15%">时间维度</td>
									<th style="width:35%">				
										<select id="timeDim" class="form-control input-sm">
											<option value='day'>天</option>
											<option value='hour'>小时</option>
											<option value='hourgroup'>小时组</option>
											<option value='quarter'>15分钟</option>
										</select>
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
						<tr>
							<td>城市</td>
							<th>
								<select id="allCity" class="form-control input-sm" multiple="multiple">
								</select>   
							</th>
						</tr>
						<tr>	
							<td>基站</td>
								<th>
									<div class="input-group input-group-md" style="width:100%">
									<input id="erbsInput" class="form-control" type="text" value=""/>
									</div>
								</th>
								<td>小区</td>
								<th>
									<!-- <div class="input-group input-group-md" style="width:100%">
									<input id="cellInput" class="form-control" type="text" value=""/>
									</div> -->
									<div class="input-group">									 
									    <input type="text" class="form-control" id="cellInput">
									    <input type="file" class="hidden" name="fileImport" id="fileImport" onchange="toName(this)">
									    <span class="input-group-btn">
									        <button class="btn btn-default" type="button" onclick="fileImport.click()">选择文件</button>
									    </span>
									</div>
								</th>
						</tr>
						<tr>
							<td>小时</td>
								<th>
									<select id="hourSelect" class="form-control" multiple="multiple">
										<option value='0'>0</option>
										<option value='1'>1</option>
										<option value='2'>2</option>
										<option value='3'>3</option>
										<option value='4'>4</option>
										<option value='5'>5</option>
										<option value='6'>6</option>
										<option value='7'>7</option>
										<option value='8'>8</option>
										<option value='9'>9</option>
										<option value='10'>10</option>
										<option value='11'>11</option>
										<option value='12'>12</option>
										<option value='13'>13</option>
										<option value='14'>14</option>
										<option value='15'>15</option>
										<option value='16'>16</option>
										<option value='17'>17</option>
										<option value='18'>18</option>
										<option value='19'>19</option>
										<option value='20'>20</option>
										<option value='21'>21</option>
										<option value='22'>22</option>
										<option value='23'>23</option>
									</select>
								</th>
								<td>15分钟</td>
									<th>
										<select id="quarterSelect" class="form-control" multiple="multiple">
											<option value='0'>0</option>
											<option value='15'>15</option>
											<option value='30'>30</option>
											<option value='45'>45</option>
										</select>
									</th>
								</tr>
					</table>
					<input id="NBIQueryFile" value='' hidden="true">								
					</div>
			
		
			<div class="box-footer" style="text-align:right;">
				<a id="search" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onClick="doSearch('table')"><span class="ladda-label">查询</span></a>

				<a id="save" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="fileSave('file')"><span class="ladda-label">保存</span></a> 

				<a id="export" class="btn btn-primary ladda-button hidden" data-color='red' data-style="expand-right" href="#"  onClick="doSearch('file')"><span class="ladda-label">导出</span></a> 
			</div>
			</div>

			<div class="box">
				<div class="box-header with-border">
					<h3 class="box-title">查询数据</h3>
					<div class="box-tools pull-right">
						<span id="loadSaveData" class="glyphicon glyphicon-save" aria-hidden="true" onClick="fileSave('file')"></span>
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
				</div>

				<div class="box-body">
		            <table id="NBIQueryTable">
		            </table>
	            </div>
			</div>
		</div>
	</div>
</section>

@endsection

@section('scripts')
<style type="text/css"> 
    .treeview span.indent{
    	margin:0;
    }
    #loadSaveData {
    	cursor:pointer;
    	color:#97a0b3;
    }
    #loadSaveData:hover {
    	color: #606c84;
    }
    /*table thead th,
    table tbody tr{
		width:auto; @important
	}
	table thead th > div,
	table tbody tr > div{
		white-space: nowrap;
	}*/
</style> 
<link type="text/css" rel="stylesheet" href="plugins/datepicker/css/datepicker.css">
<script type="text/javascript" src="plugins/datepicker/js/bootstrap-datepicker.js"></script>

<!--input select-->
<script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" />

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

<!--fileStyle-->
<script type="text/javascript" src="plugins/ajaxFileUpload/ajaxfileupload.js"></script>
@endsection

<script type="text/javascript" src="plugins/jQuery/jquery.min.js"></script>
<!-- <script type="text/javascript" src="dist/js/QueryAnalysis/LTEQuery.js"></script> -->
<script type="text/javascript" src="dist/js/QueryAnalysis/NBIQuery.js"></script>