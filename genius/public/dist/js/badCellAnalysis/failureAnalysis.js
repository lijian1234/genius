$(function(){
  toogle("failureAnalysis");

	initDataBase();
	initProcess();

	//绑定信令图的tab页面，保证页面出来才开始画图，避免画图错位的问题
	$("#table_tab_0_nav").on("shown.bs.tab",function(){
		if(chartDatas){
			setChart();
		}else{
			$("#resultView").empty();
		}
	})
});
var chartDatas;
function initDataBase(){

	var url = "failureAnalysis/getDataBase";
	var data = {"type":"ctrsystem"};
	$.get(url,data,function(data){
		if(data == "login"){
		    alert("尚未登录！");
		  	window.location.href = 'login';
		  	return;
		}
		data = eval("("+data+")");
		var dataBase = $("#dataBase").select2({
	        placeholder: "请选择数据库",
	        //allowClear: true,
	        data:data
      	});
	});
}

function initProcess(){
  	$.get('common/json/ctrProcess.json',null,function(data){
	    var processTreeData =eval("("+data+")");
	    var process = $("#process").select2({
          placeholder: "请选择流程",
          //allowClear: true,
          data:processTreeData
        }); 
  	});
}

function query(){
	var task = $("#dataBase").val();
	var resultTable = $("#process").val();
	var drillDownText = 'result';
    if (resultTable == 'internalProcUeCtxtRelease') {
      	drillDownText = '3gppCause';
    }
    var fieldCol=DrillDownColMobilityResult(resultTable);
    if(fieldCol==false){return false};
    var params={
       	resultTable:resultTable,
       	db:task,
       	drillDown:drillDownText,
       	type:'mobilityResult'
   	};

   	showTable(params,fieldCol);
   	showMobilityResultDistribution(params);
}
function DrillDownColMobilityResult(resultTable){
  	var fieldArr=new Array();
  	if (resultTable == 'internalProcUeCtxtRelease') {
      	fieldArr[fieldArr.length]={field:'3gppCause',title:'3gppCause',width:250};
  	}else{
       	fieldArr[fieldArr.length]={field:'result',title:'result',width:250};
  	}
  
  	fieldArr[fieldArr.length]={field:'value',title:'Value',width:80};
  	fieldArr[fieldArr.length]={field:'total',title:'Total',width:100};
  	fieldArr[fieldArr.length]={field:'share',title:'Share',width:150};
  	var fieldCol=new Array(fieldArr); 
  	return fieldArr;
}
function showTable(params,fieldCol){
	$('#resultTable').grid('destroy', true, true);
	var grid = $("#resultTable").grid({
	  	columns:fieldCol,
	  	dataSource:{ 
        	url: 'failureAnalysis/getTableData', 
        	success: function(data){
      			data = eval("("+data+")");
      			if(data.message){
      				alert(data.message);
      				return;
      			}
        		grid.render(data);

        	} 
      	},
    	params : params,
	  	pager: { limit: 10, sizes: [10, 20, 50, 100] },
	  	autoScroll:true,
	  	uiLibrary: 'bootstrap',
	  	primaryKey : 'id',
      	autoLoad: true   
	});
  grid.on('rowSelect', function (e, $row, id, record) {
      var result = $row.children().eq(0).children().html();
      $("#selectedResult").val(result);
      detailTable(result);
  });
  
}
function showMobilityResultDistribution(params){
    $.post('failureAnalysis/getChartData',params,function(data){ 
        var resultDataStr = JSON.stringify(JSON.parse(data).resultData);
        var resultDataObj = eval("("+resultDataStr+")");
        if(!resultDataObj){
        	chartDatas = null;
        	$("#resultView").empty();
        	return;
        }
        chartDatas = resultDataObj;
        setChart();
  	});
    
}
function setChart(){
	$('#resultView').highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            reflow:true
        },
        title: {
            text: 'Share'
        },
        tooltip: {
          pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
    	credits:{enabled:false},
    	plotOptions: {
        	pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    color: '#000000',
                    connectorColor: '#000000',
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                },
                point: {
                    events: {
                        click: function(event) {
                              console.log(this)
                              $("#selectedResult").val(this.name);
                              detailTable(this.name);
                        }
                    }
                },
        	}
        
    	},
    	series:[chartDatas]
	});
}
function detailTable(result){
  var task = $("#dataBase").val();
  var resultTable = $("#process").val();
  var drillDownText = 'result';
            
  var params={
      table:resultTable,
      db:task,
      type:"mobilityResult",
      result : result
  };

  var url = "failureAnalysis/getdetailDataHeader";
  $.post(url,params,function(data){
    var text = eval("("+data+")").text;
    var textArr = text.split(",");
    var fieldArr=new Array();
    for(var i in textArr){
      if(textArr[fieldArr.length]=="id"){
        fieldArr[fieldArr.length]={field:textArr[fieldArr.length],title:textArr[fieldArr.length],hidden:true};
      }else if(textArr[fieldArr.length]=="imsi" || textArr[fieldArr.length]=="mTmsi" || textArr[fieldArr.length]=="ueRef" || textArr[fieldArr.length]=="enbS1apId" || textArr[fieldArr.length]=="mmeS1apId"|| textArr[fieldArr.length]=="ecgi" || textArr[fieldArr.length]=="gummei"|| textArr[fieldArr.length]=="duration"){
        fieldArr[fieldArr.length]={field:textArr[fieldArr.length],title:textArr[fieldArr.length],width:150};
      }else if(textArr[fieldArr.length]=="result"){
        fieldArr[fieldArr.length]={field:textArr[fieldArr.length],title:textArr[fieldArr.length],width:(result.length *10+16)};
      }else{
        fieldArr[fieldArr.length]={field:textArr[fieldArr.length],title:textArr[fieldArr.length],width:250};
      }
      
    }

    $('#detailTable').grid('destroy', true, true);
    var grid = $("#detailTable").grid({
        columns:fieldArr,
        dataSource:{ 
            url: 'failureAnalysis/getdetailData', 
            success: function(data){
              data = eval("("+data+")");
              grid.render(data);
            } 
          },
        params : params,
        pager: { limit: 10, sizes: [10, 20, 50, 100] },
        autoScroll:true,
        uiLibrary: 'bootstrap',
        primaryKey : 'id',
        autoLoad: true   
    });
    grid.on('rowSelect', function (e, $row, id, record) {
      $("#ueRef").val(record.ueRef);
  });

  });
}





function exportFile(){
	var task = $("#dataBase").val();
	var resultTable = $("#process").val();
	var drillDownText = 'result';
  var result = $("#selectedResult").val();
  if(!result){
    alert("请先查询详情在进行导出！");
    return;
  }         
    var params={
       	table:resultTable,
       	db:task,
       	type:"mobilityResult",
        result : result
  	};
  	var exportBtn = Ladda.create(document.getElementById("exportBtn"));
	exportBtn.start();
    $.post('failureAnalysis/exportFile',params,function(data){
    	exportBtn.stop();
    	var data = eval('(' + data + ')');
        if(data['result']=='true'){
      		var filepath = data['filename'].replace('\\','');
      		download(filepath,'','data:text/csv;charset=utf-8');
    	}else{
      		alert("There is error occured!");
    	}
  	});
}

function download(url) {
  	var browerInfo = getBrowerInfo();
  	if (browerInfo=="chrome"){
      	download_chrome(url);
  	} else if (browerInfo == "firefox") {
      	download_firefox(url);
  	}
}

function download_chrome(url){
    var aLink = document.createElement('a');
    aLink.href=url;
   	aLink.download = url;
    var evt = document.createEvent("HTMLEvents");
    evt.initEvent("click", false, false);
    aLink.dispatchEvent(evt);
}

function download_firefox(url){
    window.open(url);
}
function getBrowerInfo(){
 	var uerAgent = navigator.userAgent.toLowerCase();
 	var format =/(msie|firefox|chrome|opera|version).*?([\d.]+)/;
 	var matches = uerAgent.match(format);
 	return matches[1].replace(/version/, "'safari"); 
}

function openProcedure(){
  var task = $("#dataBase").val();
  var ueRef = $("#ueRef").val();
  if(!ueRef){
    alert("请先选择一条详情数据！");
    return;
  }
  //window.location.href = 'signalingBacktracking?task='+task+'&ueRef='+ueRef;
  window.open('signalingBacktracking?task='+task+'&ueRef='+ueRef);
}