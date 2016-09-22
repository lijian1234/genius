@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>弱覆盖小区比例</h1>
	<ol class="breadcrumb">
        <li><i class="fa fa-dashboard"></i> 专项研究</li>
        <li>弱覆盖分析</li>
        <li class="active">弱覆盖小区比例</li>
    </ol>
</section>
@endsection
@section('content')
<section class="content">
<div class="row">
	<div class="col-md-12"> 
		<div class="box">
            <div class="box-header">
                <div class="box-title">
                  <!--   <div class="form-group"> -->
                        <div >
                        当前日期：<input id="startTime" style="width:40%" type="text" value=""/>
                        </div>
                    <!-- </div> -->
                </div>
                <button id="search" class="btn btn-primary pull-right ladda-button" data-style="expand-right" href="#" onClick="search()"><span class="ladda-label">查询</span></button>
            </div>
        </div>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">弱覆盖小区占比</h3>
            </div>
            <div class="box-body">
                <div id="weakCoverRatio" style="height: 400px"></div>
            </div>    
        </div>
	</div>
</div>
</section>
@endsection
@section('scripts')
<link type="text/css" rel="stylesheet" href="plugins/datepicker/css/datepicker.css">
<script type="text/javascript" src="plugins/datepicker/js/bootstrap-datepicker.js"></script>

<!--loading-->
<link rel="stylesheet" href="plugins/loading/css/ladda-themeless.min.css">
<script src="plugins/loading/js/spin.js"></script>
<script src="plugins/loading/js/ladda.js"></script>

<script src="https://code.highcharts.com/stock/highstock.js"></script>
<script src="plugins/highcharts/js/highcharts.js"></script>
<script src="https://code.highcharts.com/stock/modules/exporting.js"></script>
@endsection
<!-- jQuery 2.2.0 -->
<script type="text/javascript" src="plugins/jQuery/jquery-2.0.2.min.js"></script>
<script type="text/javascript" src="dist/js/genius/weakCoverRatio.js"></script>
