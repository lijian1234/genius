/**
 * Created by wangyang on 2016/6/23.
 */

/**
 * Draw gauge.
 */


var chartRank = function(route,block) {
	$.ajax({
		type : "GET",
		url : route,
		//data : {range : "day"},
		dataType : "json",
		beforeSend : function () {
			$('#'+block).html('<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">')
		},
		success: function(data) {
			var data = data;
			$('#'+block).html('');
				var chartTest1 = new Highcharts.StockChart({

				 //$(block).highcharts('StockChart', {	 
					rangeSelector : {
						buttons : [{
							type : 'month',
							count : 1,
							text : '1m'
						}, {
							type : 'day',
							count : 6,
							text : '7D'
						}, {
							type : 'all',
							count : 1,
							text : 'All'
						}],
						selected : 1,
						//inputEnabled : false
						inputDateFormat: '%Y-%m-%d',
						inputEditDateFormat: '%Y-%m-%d',

					},

					legend:{enabled:true},
					credits:{enabled:false},

					yAxis: {
						tickPositions: data['yAxis'],
					},

					chart: {
						type:'column',
						renderTo: block,
						alignTicks: false
					},

					title: {
						text: null
					},
					series: data['series']
					//series: data['series']
			});
		}
	});	 
};

var chartTrend = function(route,block) {
	$.ajax({
		type : "GET",
		url : route,
		//data : {range : "day"},
		dataType : "json",
		beforeSend : function () {
			$('#'+block).html('<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">')
		},
		success: function(data) {
			var data = data;
			$(block).html('');
				var chartTest2 = new Highcharts.StockChart({

				 //$(block).highcharts('StockChart', {	 
					rangeSelector : {
						buttons : [{
							type : 'month',
							count : 1,
							text : '1m'
						}, {
							type : 'day',
							count : 1,
							text : '1D'
						}, {
							type : 'day',
							count : 3,
							text : '3D'
						}, {
							type : 'day',
							count : 7,
							text : '7D'
						}, {
							type : 'all',
							count : 1,
							text : 'All'
						}],
						selected : 1,
						//inputEnabled : false
						inputDateFormat: '%Y-%m-%d',
						inputEditDateFormat: '%Y-%m-%d',
					},

					legend:{enabled:true},
					credits:{enabled:false},

					yAxis: {
						tickPositions: data['yAxis'],
					},

					chart: {
						type:'line',
						renderTo: block,
						alignTicks: false
					},

					title: {
						text: null
					},

					series: data['series']
			});
		}
	});	 
};


//When document was loaded.
jQuery(document).ready( function() {
	var width = $("#chart-access").width();
	$("#chart-lost").width(width);
	$("#chart-handover").width(width);
	$("#chart_erab_success").width(width);
	$("#chart_erab_lost").width(width);
	$("#chart_wireless_success").width(width);
	$("#chart_volte_handover").width(width);
	$("#chart1_wireless_success").width(width);
	$("#chart1_erab_lost").width(width);
	$("#chart1_VideoCall_success").width(width);
	$("#chart1_eSRVCC_handover").width(width);

	chartRank('lowAccess','chart-access');
	chartRank('highLost','chart-lost');
	chartRank('badHandover','chart-handover');

	chartRank('erabSuccess','chart_erab_success');
	chartRank('erabLost','chart_erab_lost');
	chartRank('wirelessSuccess','chart_wireless_success');
	chartRank('volteHandover','chart_volte_handover');

	chartRank('chart1WireSucc','chart1_wireless_success');
	chartRank('chart1ErbLost','chart1_erab_lost');
	chartRank('chart1VideoSucc','chart1_VideoCall_success');
	chartRank('chart1EsrvccHander','chart1_eSRVCC_handover');

	$('#rank_threeKeys').click(function(){
		chartRank('lowAccess','chart-access');
		chartRank('highLost','chart-lost');
		chartRank('badHandover','chart-handover');
	});

	$('#trend_threeKeys').click(function(){
		chartTrend('lowAccessTrend','chart-access');
		chartTrend('highLostTrend','chart-lost');
		chartTrend('badHandoverTrend','chart-handover');
	});

	$('#rank_volte').click(function(){
		chartRank('erabSuccess','chart_erab_success');	
		chartRank('erabLost','chart_erab_lost'); 
		chartRank('wirelessSuccess','chart_wireless_success');	 
		chartRank('volteHandover','chart_volte_handover');	
	});

	$('#trend_volte').click(function(){
		chartTrend('erabSuccessTrend','chart_erab_success');  
		chartTrend('erabLostTrend','chart_erab_lost');
		chartTrend('wirelessSuccessTrend','chart_wireless_success'); 
		chartTrend('volteHandoverTrend','chart_volte_handover');			 
	});

	$('#rank_video').click(function(){
		chartRank('chart1WireSucc','chart1_wireless_success');
		chartRank('chart1ErbLost','chart1_erab_lost');
		chartRank('chart1VideoSucc','chart1_VideoCall_success');
		chartRank('chart1EsrvccHander','chart1_eSRVCC_handover');
	});

	$('#trend_video').click(function(){
		chartTrend('chart1WireSuccTrend','chart1_wireless_success');
		chartTrend('chart1ErbLostTrend','chart1_erab_lost');
		chartTrend('chart1VideoSuccTrend','chart1_VideoCall_success');
		chartTrend('chart1EsrvccHanderTrend','chart1_eSRVCC_handover');
	});
   

	// //VideoCall指标
	// chart('genius/public/chart1WireSucc','#chart1_wireless_success');

	// chart('genius/public/chart1ErbLost','#chart1_erab_lost');

	// chart('genius/public/chart1VideoSucc','#chart1_VideoCall_success');

	// chart('genius/public/chart1EsrvccHander','#chart1_eSRVCC_handover');




	
});

//Resize
$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
	$('.tab-content .chart.tab-pane.active').highcharts().reflow();
});
