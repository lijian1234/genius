@extends('layouts.nav')
@section('content-header')
<section class="content-header">
    <h1>一致性检查</h1>
    <ol class="breadcrumb">
       <li><i class="fa fa-dashboard"></i>日常优化 
    </li>
    <li>参数分析
    </li>
    <li class="active">一致性检查</li>
    </ol>
</section>
@endsection
@section('content')
<section class="content">
<div class="row">
	<div class="col-sm-3" >
		<div class="box">
			<div class="box-body">
        <form>
  				<div class="form-group">
  				    <label for="parameterAnalysisDate">日期</label>
  						<select id="parameterAnalysisDate" class="js-example-basic-single js-states form-group col-xs-10">
  						</select>
  				</div>
          <div class="form-group" style="height:600px;overflow:auto;">
              <div id="consistencyTree"></div>
          </div>
        </form>
			</div>
		</div>
		<div class="form-group">
			  <div id="templateTree"></div>
               <input type="hidden" id="templateId" value="">
               <input type="hidden" id="templateName" value="">
		</div>
	</div>
	<div class="col-sm-9">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">检查概要</h3>

                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="nav-tabs-custom">
                            <div class="tab-content">
                                <div class="chart tab-pane active" id="chart-consistency" style="position: relative;height: 400px;"></div>
                            </div>
                        </div>
                    <!-- ./box-body -->
                    </div>
                <!-- /.box -->
                </div>

            </div>
        <!-- /.col -->
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">检查详情</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="nav-tabs-custom">
                            <div class="row">
                              <form class="form-inline">
                                <div class="form-group col-sm-8">
                                    <label>城市</label>
                                  <select id="parameterAnalysisCity" class="form-control" multiple="multiple">
                                  </select>
                                </div>
                                <div class="form-group col-sm-4" style="text-align:right;">
                                    <button type="submit"  class="btn btn-primary" onclick="consistencyCheckDetailsSearch();return false;">查询</button>
                                    <button type="submit"  class="btn btn-primary" onclick="consistencyCheckExportTofile();return false;">导出</button>
                                </div>
                              </form>
                            </div>
                            <div class="tab-content">
                                <table id="consistencyCheckDetailTable" class="gj-grid-table table table-bordered table-hover">
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
				
	</div>
</div>

</section>
@endsection
@section('scripts')
<script src="plugins/highcharts/js/highstock.js"></script>
<script src="plugins/highcharts/js/highcharts-more.js"></script>
<!-- grid -->
<script type="text/javascript" src="plugins/bootstrap-grid/js/grid.js"></script>
<!--select2-->
<script type="text/javascript" src="plugins/select2/select2.js"></script>
<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" />
<script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<!-- treeview -->
<script src="plugins/treeview/bootstrap-treeview.min.js"></script>
<style>
.dropdown-menu {
   min-width:230px;
}
</style>
@endsection
<script type="text/javascript" src="plugins/jQuery/jquery.min.js"></script>
<script type="text/javascript" src="dist/js/parameterAnalysis/parameterAnalysis.js"></script>
<script type="text/javascript" src="dist/js/parameterAnalysis/consistencyCheck.js"></script>

