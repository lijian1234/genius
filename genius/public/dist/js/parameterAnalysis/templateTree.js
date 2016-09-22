$(document).ready(function() {
  toogle('baselineCheck');
  //设置树
  setTree();
  getData();
  getAllCity();
  // getInit();
  //根据树，得到相应的值
  
});

//清空模板树
function clearLteQuery(){
  $('#paramQueryMoErbs').val('');
  setTree();
}

function setTree(){
  var tree = '#templateTree';
  $(tree).treeview({
  data: getTree(),
  onNodeSelected: function(event, data) {
	paramQuerySearch();
  }

  }); //树
}
 //--------start of tableSearch-----
function paramQuerySearch(){
  var params = getParam('paramQuery');
  initDistribution(params);
  //parameterSearch(params);
}
var table = null;
function parameterSearch(params)
{
  if(params == false){return false;}
  var fieldArr=new Array(); 
   $.get('paramQuery/getParamTableField',params,function(data){ 
	var paraName=data.split(',');	  
	for(var i in paraName){ 
	if(paraName[i]!='id')
	{	
	  if(paraName[i] == 'mo'){
		fieldArr[fieldArr.length]={field:paraName[i],title:paraName[i],width:300};

	  }else{
		fieldArr[fieldArr.length]={field:paraName[i],title:paraName[i],width:150};
	  }
	}
	}
	$('#paramQueryTable').grid('destroy', true, true);
	$("#paramQueryTable").grid({
	 // dataSource:'paramQuery/getParamItems/'+JSON.stringify(params),
	columns:fieldArr,
	dataSource: { url: 'paramQuery/getParamItems',type:'post', data: params},
	primaryKey: 'id',
	pager: { limit: 10, sizes: [10, 20, 50, 100] },
	autoScroll:true,
	//shrinkToFit: false,
	uiLibrary: 'bootstrap',
	});
	
  });
  }

function getTree(){
  var url = "BaselineCheck/getBaseTree";
  var treeData;
  $.ajax({
  type:"GET",
  url:url,
  dataType:"json",
  async:false,	
  success:function(data){
	treeData = data;
  }
  });
  return treeData;
}

function getAllCity(){
  $('#allCity').multiselect({
	  dropRight: true,
	  buttonWidth: 200,
	  //enableFiltering: true,
	  nonSelectedText:'请选择城市',
	  //filterPlaceholder:'搜索',
	  nSelectedText:'项被选中',
	  includeSelectAllOption:true,
	  selectAllText:'全选/取消全选',
	  allSelectedText:'已选中所有平台类型',
	  maxHeight:200,
	  maxWidth:'100%'
  });
  var url = "BaselineCheck/getParamCitys";
  $.ajax({
	type:"GET",
	url:url,
	dataType:"json",
	success:function(data){
	  var newOptions = new Array();
	  var obj = new Object();
	  $(data).each(function(k,v){
		var v = eval("("+v+")");
		obj = {
				label : v["text"],
				value : v["value"]
			};
		newOptions.push(obj);
	  });
	  $('#allCity').multiselect('dataprovider', newOptions);
	}
  });
}
//--end of paramQueryCity init--
function getData(){
//获取时间
  var url = "BaselineCheck/getParamTasks";
  $.ajax({
  type:"GET",
  url:url,
  //data:{ids:platform_type_ids},
  dataType:"json",
  success:function(data){
	var newOptions = new Array();
	var obj = new Object();
	if(data.length == 1 && data[0] == 'login'){
	window.location.href = 'login';
	}
	$(data).each(function(k,v){
	  var v = eval("("+v+")");
	  var i = 0;
	  var currentDate = getCurrentDate();
	  obj = {
		  id: v["text"],
		  text : v["text"]
		};
	  newOptions.push(obj);
	})
	   var paramQueryDateSelect = $('#paramQueryDate').select2({
		placeholder: "请选择日期",
		//allowClear: true,
		data:newOptions
	  });
	  //var value = $(paramQueryDateId).val();
	  $('#paramQueryDate').val(getCurrentDate()).trigger('change');
	 }
	});
  }
function getParam(action)
{
  if(action == 'paramQuery'){
	var task = $('#paramQueryDate').val();
	var moSelected = $('#templateTree').treeview('getSelected');
	if(moSelected == ''){
	alert("Please choose parameter tree first!");
	return false;
	}
	var mo = moSelected[0].text;
	var templateId = moSelected[0].id;
	if(task != null){
	  var params={
	  db:task,
	  table:mo,
	  templateId:templateId,
	  };
	  return params;
	}else{
	  alert("Please choose database first!");
	  return false;
	}
  }
}
 //--------end of tableSearch-------
 function getCurrentDate(type){
  var mydate = new Date();
  var myyear = mydate.getYear();
  var myyearStr = (myyear+"").substring(1);
  var mymonth = mydate.getMonth()+1; //值范围0-11
  var mydate = mydate.getDate();  //值范围1-31
  var mymonthStr = "";
  var mydateStr = "";
  mydate = mydate - 1;
  mymonthStr = mymonth >= 10 ? mymonth : '0' + mymonth;
  mydateStr = mydate >= 10 ? mydate : '0' + mydate;
  if(type == 'bulkcm' || type == 'kgetpart'){
   var kgetDate = type+myyearStr+mymonthStr+mydateStr;
  }else{
  var kgetDate = "kget"+myyearStr+mymonthStr+mydateStr;

  }
  return kgetDate;
}

function initDistribution(params){
  var databaseDate = '';
  var templateId = ''; 
   //传入城市
  var databaseconnCity;
  var databaseconnCityParam = {
	db:'mongs',
	table:'databaseconn'
  }
  $.ajaxSetup({async : false});	 //同步执行
  $.get('BaselineCheck/getAllCity',databaseconnCityParam,function(data){
	databaseconnCity =JSON.parse(data);
  });
  $.ajaxSetup({async : true});	//异步执行
  var params_distribution={db:params.db,  //无线掉线率
	table:params.table,
	city:databaseconnCity,
	templateId:params.templateId
  }; 
  $.get('BaselineCheck/getChartDataCategory',params_distribution,function(data){
  $('#categoryDistribution').highcharts({
	  chart: { type: 'column' },
	  exporting: {	 
		enabled:true,	
	  }, 
	  title: {
		text: 'category分布',
		x: -20 //center
	  },
	  credits: {  
	  enabled: false  
	  },
	  subtitle :{
		text: '	 ',
		x: -20
	  },
	  xAxis: {
		categories: data['category']
	  },
	  yAxis: {
		title: {
		  text: 'Number'
		},
		plotLines: [{
		  value: 0,
		  width: 1,
		  color: '#808080'
		}]
	  },
	 tooltip: {
		valueSuffix: ''
	  },
	  legend: {
	  layout: 'horizontal',
	  align: 'center',
	  x: 0,
	  verticalAlign: 'bottom',
	  y: 0,
	  backgroundColor: '#FFFFFF'
	  },
	  series: data['series']
	}); 
  });
}
function changeDate(val){
	var task = val;
	var moSelected = $('#templateTree').treeview('getSelected');
	// debugger;
	if(moSelected == ''){
	// alert("Please choose parameter tree first!");
	var mo='';
	var templateId='';
	}else{
	  var mo = moSelected[0].text;
	  var templateId = moSelected[0].id;
	}
	if(task != null){
	  var params={
	  db:task,
	  table:mo,
	  templateId:templateId,
	  };
	initDistribution(params)
	}else{
	  alert("Please choose database first!");
	  return false;
	}
}
//查询按钮
function parameterViewSearch(){
   var task = $('#paramQueryDate').val();
	var moSelected = $('#templateTree').treeview('getSelected');
	if(moSelected == ''){
	alert("Please choose parameter tree first!");
	return false;
	}
	var mo = moSelected[0].text;
	var templateId = moSelected[0].id;
	if(task != null){
	  var citys = $('#allCity').val();
	  var params = {
		db:task,
		table:'ParaCheckBaseline',
		filter:true,
		templateId:templateId,
		citys:citys
	  }
	}else{
	  alert("Please choose database first!");
	  return false;
	}
	 var fieldArr=new Array();
	$.get('BaselineCheck/getTableField',params,function(data){
	  //if(table == 'TempParameterCellPrint'){
	  //var paraName=data.split(',');	
	  /*for(var i in paraName){ 
	  if(paraName[i]!='id')
	  {	  
		if(paraName[i] == 'mo'){
		  fieldArr[fieldArr.length]={field:paraName[i],title:paraName[i],width:300};

		}else{
		  fieldArr[fieldArr.length]={field:paraName[i],title:paraName[i],width:150};

		}
	  }
	  }*/
	  var newDataLength = {};
	  for(var k in data){
	  if(k != 'id'){
	  var paraDataLength = 0;
	  var widthData = 0;
	  if (data[k]) {
		paraDataLength = data[k].length;
		widthData = paraDataLength * 15;
	  }
	  var paraNameLength = k.length;
	  var widthName = paraNameLength * 10;
	  if (k == 'mo') {
		widthData = paraDataLength * 10;
	  };
	  var width;
	  if (widthData > 300 && k !='mo') {
		width = 300;
		newDataLength[k] = 25;
	  }else{
		width = widthName > widthData ? widthName : widthData;
		if (paraDataLength == 0) {
		newDataLength[k] = paraNameLength;
		}else{
		newDataLength[k] = width;

		}
	  }
	  fieldArr[fieldArr.length]={field:k,title:k,width:width};
	  }
	  }
	  params['paramLength'] = newDataLength;
	  $('#tempParameterCellPrintTable').grid('destroy', true, true);
	  $("#tempParameterCellPrintTable").grid({
	  // dataSource:'paramQuery/getParamItems/'+JSON.stringify(params),
	  columns:fieldArr,
	  dataSource: { url: 'BaselineCheck/getParamItems', data: params},
	  primaryKey: 'id',
	  pager: { limit: 10, sizes: [10, 20, 50, 100] },
	  autoScroll:true,
	  //shrinkToFit: false,
	  uiLibrary: 'bootstrap',
	  //autoGenerateColumns: true,
	  //responsive: true,
	  });
	}); 
}
//导出
function exporttofile(){
  var task = $('#paramQueryDate').val();
  var moSelected = $('#templateTree').treeview('getSelected');
  //var rows = $("#tempParameterCellPrintTable").datagrid("getRows");
  var mo = moSelected[0].text;
  var templateId = moSelected[0].id;
  var citys = $('#allCity').val();
  var params = {
	db:task,
	table:'ParaCheckBaseline',
	filter:true,
	templateId:templateId,
	citys:citys
  };
  
  $.get('BaselineCheck/baselineFile',params,function(data){
	//alert(data);
	//var data = eval('(' + data + ')');
	if(data['result']=='true')
	{
	  var filepath = data['filename'].replace('\\','');
	  //window.open(filepath,'','data:text/csv;charset=utf-8');
	  download(filepath,'','data:text/csv;charset=utf-8');
	}
	else
	{
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

