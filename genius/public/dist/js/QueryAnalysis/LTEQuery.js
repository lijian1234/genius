$(document).ready(function() {
	//设置日期
  setTime(); 
  
  //设置树
  setTree();
  $('#LTEQueryMoTree').treeview('collapseAll', { silent: true });

  //设置表格
  setTable();

  //设置输入框状态
  setInputStatus();

  //数据库获取所有城市
  getAllCity();
  
  //初始化下拉框
  $('#subNetworks').multiselect({
	  buttonWidth: '100%',
	  enableFiltering: true,
	  nonSelectedText:'请选择子网',
	  filterPlaceholder:'搜索',
	  nSelectedText:'项被选中',
	  includeSelectAllOption:true,
	  selectAllText:'全选/取消全选',
	  allSelectedText:'已选中所有子网',
	  maxHeight:200,
	  maxWidth:'100%'
  });

  //数据库获取对应subNwork
  $('#allCity').change(function(){
	getAllSubNetwork();
  });

  //数据库获取对应模式TDD/FDD
  $('#LTEFormat').change(function(){
	getFormatAllSubNetwork();
  });

  //设置小时/15分钟选择
  setHQSelect();

  //设置查询方式
  //setCheckedType();
  $('#checkedType').bootstrapToggle('off');

  toogle('LTEQuery');
});


//导入小区
function toName(self){
	$.ajaxFileUpload({
      url : 'LTEQuery/uploadFile',   　
      //data : data,
      fileElementId : "fileImport",           
      secureuri : false,                          
      dataType:'json',
      type: "post",                     
      success:function(data, status){ 
        $("#cellInput").val(data);
      },
      error:function(data, status, e){
      	alert("上传失败");
      }
   });
}

//清空模板树
function clearLteQuery(){
  $('#paramQueryMoErbs').val('');
  setTree();
  $('#LTEQueryMoTree').treeview('collapseAll', { silent: true });
}

//筛选模板树
function searchLTEQuery() {
  var inputData = $('#paramQueryMoErbs').val();
  inputData = $.trim(inputData);
  if(inputData == '') {
	setTree();
	return;
  }
  var params = {
	inputData : inputData
  };
  var url = "LTEQuery/searchLTETreeData";
  //var treeData;

  $.get("LTEQuery/searchLTETreeData",params,function(data){
	data = "["+data+"]";
	var tree = '#LTEQueryMoTree';
	$(tree).treeview({data: data});
  });
}

function setHQSelect(){
  $('#hourSelect').multiselect({
	  buttonWidth: '100%',
	  enableFiltering: true,
	  nonSelectedText:'请选择小时',
	  filterPlaceholder:'搜索',
	  nSelectedText:'项被选中',
	  includeSelectAllOption:true,
	  selectAllText:'全选/取消全选',
	  allSelectedText:'已选中所有小时',
	  maxHeight:200,
	  maxWidth:'100%'
  });

  $('#quarterSelect').multiselect({
	  dropRight: true,
	  buttonWidth: '100%',
	  //enableFiltering: true,
	  nonSelectedText:'请选择15分钟',
	  //filterPlaceholder:'搜索',
	  nSelectedText:'项被选中',
	  includeSelectAllOption:true,
	  selectAllText:'全选/取消全选',
	  allSelectedText:'已选中所有',
	  maxHeight:200,
	  maxWidth:'100%'
  });
}

//获取所有被选择的城市
function getChooseCitys(){
  var citys = $('#allCity').val();
  return citys;
}

function getFormatAllSubNetwork(){
	var citys = getChooseCitys();
	var format = $('#LTEFormat').val();
  	var params = {
  		format: format,
		citys : citys
	}
	$.get("LTEQuery/getFormatAllSubNetwork",params,function(data){
		var newOptions = new Array();
		var obj = new Object();
		$(data).each(function(k,v){
		  	var v = eval("("+v+")");
		  obj = {
			label : v["text"],
			value : v["value"],
			selected : true
		  };
		  newOptions.push(obj);
		});
		$('#subNetworks').multiselect('dataprovider', newOptions);
	  });
}

function getAllSubNetwork(){
  var citys = getChooseCitys();
  var format = $('#LTEFormat').val();
  var params = {
  	format:format,
	citys : citys
  }
  
  $.get("LTEQuery/getAllSubNetwork",params,function(data){
	var newOptions = new Array();
	var obj = new Object();
	$(data).each(function(k,v){
	  var v = eval("("+v+")");
	  obj = {
		label : v["text"],
		value : v["value"],
		selected : true
	  };
	  newOptions.push(obj);
	});
	$('#subNetworks').multiselect('dataprovider', newOptions);
  });
}

function getAllCity(){
  $('#allCity').multiselect({
	  dropRight: true,
	  buttonWidth: '100%',
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
  var url = "LTEQuery/getAllCity";
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

function setInputStatus(){
  $('#LTEFormat').removeAttr("disabled"); 
  //区域维度初始值设置
  $("#locationDim").val('city');
  $('#cellInput').attr('disabled', 'true');
  $('#erbsInput').attr('disabled', 'true');
  $('#cellInput').val('');
  $('#erbsInput').val('');
  $('#locationDim').change(function(){
	if($('#locationDim').val() == 'cell' || $('#locationDim').val() == 'cellGroup'){
	  $('#cellInput').removeAttr('disabled');
	  $('#erbsInput').attr('disabled', 'true');
	  $('#erbsInput').val('');
	}else if($('#locationDim').val() == 'erbs'){
	  $('#cellInput').attr('disabled', 'true');
	  $('#erbsInput').removeAttr('disabled');
	  $('#cellInput').val('');
	}else{
	  $('#cellInput').attr('disabled', 'true');
	  $('#erbsInput').attr('disabled', 'true');
	  $('#cellInput').val('');
	  $('#erbsInput').val('');
	}
  });

  //时间维度初始值设置
  $('#timeDim').val('day');
  //$('#hourSelect').attr('disabled', 'disabled');
  $('#quarterSelect').attr('disabled', 'disabled');
  $('#timeDim').change(function(){
	if($('#timeDim').val() == 'hour' || $('#timeDim').val() == 'hourgroup'){
	  $("#quarterSelect").multiselect("disable");
	  $("#hourSelect").multiselect("enable");
	}else if($('#timeDim').val() == 'quarter'){
	  $("#quarterSelect").multiselect("enable");
	  $("#hourSelect").multiselect("enable");
	}else{
	  $("#hourSelect").multiselect("enable");
	  $("#quarterSelect").multiselect("disable");
	}
  });
}

function setTable(){
  // $("#LTEQueryTable").bootgrid({	  //表格
  //	   ajax: true,
  //	   post: function ()
  //	   {
  //		   // To accumulate custom parameter with the request object 
  //		   return {
  //			   id: "b0df282a-0d67-40e5-8558-c9e93b7befed"
  //		   };
  //	   },
  //	   url: "common/json/test.json"/*,
  //	   formatters: {
  //		   "link": function(column, row)
  //		   {
  //			   return "<a href=\"#\">" + column.id + ": " + row.id + "</a>";
  //		   }
  //	   }*/
  //   });
}

function setTree(){
  var tree = '#LTEQueryMoTree';
  $(tree).treeview({data: getTree()}); //树
}

function setTime(){
  $("#startTime").datepicker({format: 'yyyy-mm-dd'});  //返回日期
  $("#endTime").datepicker({format: 'yyyy-mm-dd'});

  var nowTemp = new Date();
  $("#startTime").datepicker('setValue', nowTemp);
  $("#endTime").datepicker('setValue', nowTemp);
  //alert(nowTemp);
  var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
  var checkin = $('#startTime').datepicker({
	onRender: function(date) {
	  return date.valueOf() < now.valueOf() ? '' : '';
	}
  }).on('changeDate', function(ev) {
	if (ev.date.valueOf() > checkout.date.valueOf()) {
	  var newDate = new Date(ev.date)
	  newDate.setDate(newDate.getDate() + 1);
	  checkout.setValue(newDate);
	}
	checkin.hide();
	$('#endTime')[0].focus();
	}).data('datepicker');
	  var checkout = $('#endTime').datepicker({
	  onRender: function(date) {
		//return date.valueOf() <= checkin.date.valueOf() ? 'disabled' : '';
		return date.valueOf() <= checkin.date.valueOf() ? '' : '';
	  }
	}).on('changeDate', function(ev) {
	  checkout.hide();
	}).data('datepicker');
}

function getTree() {
  var url = "LTEQuery/getLTETreeData";
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

function getParams(action){
  var locationDim = $('#locationDim'). val();
  var timeDim	  = $('#timeDim').val();
  var startTime	  = $('#startTime').val();
  var endTime	  = $('#endTime').val();
  var format	  = $('#LTEFormat').val();
  var citys		  = $('#allCity').val();
  if(citys == null){
	alert("Please choose city first!");
	return false;
  }
  var subNetworks = $('#subNetworks').val();
  if(subNetworks == null){
	alert("Please choose subNetwork first!");
	return false;
  }
  var LTETree	  = $('#LTEQueryMoTree').treeview('getSelected');
  if(LTETree == ''){
	alert("Please choose parameter tree first!");
	return false;
  }
  var moTree	  = LTETree[0].text;
  var hour		  = $('#hourSelect').val();
  var min		  = $('#quarterSelect').val();
  var cell		  = $('#cellInput').val();
  var erbs		  = $('#erbsInput').val();

  var flag = $('#checkedType').prop('checked');
  if(flag){
  	var checkStyle = 'local';
  }else{
  	checkStyle = 'online';
  }

  var params = {
	template:moTree,
	locationDim:locationDim,
	timeDim:timeDim,
	startTime:startTime,
	endTime:endTime,
	hour:JSON.stringify(hour),//hour,
	//hour:hour,
	minute:JSON.stringify(min),//min,
	city:JSON.stringify(citys),//citys,
	subNet:JSON.stringify(subNetworks),//subNetworks,
	erbs:erbs,
	cell:cell,
	format:format,
	style:checkStyle,
	action:action 
  };
  return params;
}

//查询
function doSearchLTE(table){
  var flag = $('#checkedType').prop('checked');
  if(flag){
  	var route = 'LTEQuery/templateQueryOnline';
  	if($('#LTEFormat').val() == 'FDD'){
  		alert('本地不能查询FDD');
  		$('#checkedType').bootstrapToggle('off');
  		return;
  	}
  	if($('#timeDim').val() == 'quarter'){
  		alert('本地不能查询15分钟');
  		$('#checkedType').bootstrapToggle('off');
  		return;
  	}
  }else{
  	route = 'LTEQuery/templateQueryLocal';
  }

  var l = Ladda.create( document.getElementById( 'search' ) );
  var S = Ladda.create( document.getElementById( 'save' ) );
  var E = Ladda.create( document.getElementById( 'export' ) );
  l.start();
  S.start();
  E.start();
  var params = getParams();
  if(params == false){
	l.stop();
	S.stop();
	E.stop();
	return false;
  }
  $.post('LTEQuery/templateQuery', params, function(data){

  	console.log(JSON.parse(data).error);
  	if(JSON.parse(data).error == 'NOTFINDLINE'){
  		l.stop();
		S.stop();
		E.stop();
  		alert('不存在的字段名');
  		return false;
  	}
  	
	$('#LTEQueryFile').val(JSON.parse(data).filename);
	var fieldArr=new Array();
	var text=(JSON.parse(data).text).split(',');
	for(var i in JSON.parse(data).rows[0]){		  
	  fieldArr[fieldArr.length]={field:i,title:text[fieldArr.length],width:textWidth(text[fieldArr.length])};
	} 
	var newData = JSON.parse(data).rows;

	$('#LTEQueryTable').grid('destroy', true, true);
	$("#LTEQueryTable").grid({
	  columns:fieldArr,
	  dataSource:newData,
	  pager: { limit: 10, sizes: [10, 20, 50, 100] },
	  autoScroll:true,
	  uiLibrary: 'bootstrap'
	});
	if(table == 'file') {
	  alert(JSON.parse(data).filename);
	  download(JSON.parse(data).filename);
	}
	l.stop();
	S.stop();
	E.stop();
  });
}

function textWidth(text){
    var length = text.length;
    if(length > 15){
        return length*10;
    }
    return 150;
}


function fileSave(table) {
  var fileName=$("#LTEQueryFile").val();
  alert(fileName);
  if(fileName!='')
  {
	download(fileName);
  }
  else
  {
	alert('No file generated so far!');
  }
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