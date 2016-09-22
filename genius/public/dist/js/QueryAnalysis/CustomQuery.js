$(document).ready(function() {
	//设置树
  	setTree();
    $('#CustomQueryMoTree').treeview('collapseAll', { silent: true });
  	getAllCity();
   customDblClickFun();
  	//点击树获取名称
  	// $('#CustomQueryMoTree').on('nodeSelected', function(event, data){	
  	// 	var params = {
  	// 		treeData:data.text
  	// 	}
  	// 	$('#customName').val(data.text);

  	// 	$.get('getKpiFormula', params, function(data){
  	// 		$("#LTEFlag").val();
   //    		$('#edit_LTE').modal();
   //    		$('#customContext').val(data);
  	// 	})
  	// });

  	$('#inputName').css('display','none');

	toogle('CustomQuery');
});

function getText(text){
	var params = {
		treeData : text
	}
	$('#customName').val(text);
	$.get('getKpiFormula', params, function(data){
		//alert(data);
		// $("#LTEFlag").val();
		$('#customContext').val(data);
      	$('#edit_LTE').modal();
  //     		$('#customContext').val(data);
  		
  	});
}

function saveModeChange(){
	var templateName = $('#customName').val();
	var flag = $('#LTEFlag').val();
	var path = '';
	var content = $("#customContext").val();
 	var data = {
 		content:content,
 		templateName:templateName,
 		path:path
 	};
	$.post('CustomQuery/saveModeChange',data,function(data){
		if(data == 'login'){
			//alert('登陆过期！');
			window.location.href = 'login';
		}
 		$("#edit_LTE").modal('hide');
 		alert("保存成功！");	
	});
	
}

function saveMode(){
	var templateName = $('#customName').val();
	var customContext = $('#customContext').val();
	var params = {
		templateName:templateName,
		customContext:customContext
	};
	$.get('saveMode', params, function(data){
		if(data=='success'){
			alert('保存成功！');
		}else if(data == 'login'){
			window.location.href = 'login';
		}else{
			alert('保存失败！');
		}
	})
}

function deleteMode(type){


	
	var text = $("#CustomQueryMoTree .node-selected").text();

	if(!text){
		alert("请选择要删除的模板！");
		return;
	}
	var flag = confirm("确定删除该模板吗？");
	if(!flag){
		return;
	}
	var params = {
		templateName:text
	};
    $.get('deleteMode', params, function(data){
    	//$(".node-CustomQueryMoTree.node-selected").remove();

		setTree();
		customDblClickFun();
	})
	// $.get('deleteMode', params, function(data){
	// 	setTree();
	// })
}
function newBuild(type){
	//$('#inputName').css('display','inline-block');
	$('#inputName').modal();
	$('#insertName').val('');
	
}

function insertTable(){
	//$('#inputName').css('display','none');
	var insertName = $('#insertName').val();
	var params = {
		insertName:insertName
	}
	$.get('insertMode', params, function(data){
		if(data == 'success'){
			setTree();
			customDblClickFun();
			alert('插入成功！');
		}else if(data == 'login'){
			window.location.href = 'login';
		}else if(data == 'wrong'){
			alert('模板重名，请重新输入！');
		}
		$('#inputName').modal('hide');
	})
}


function run(type){
	var l = Ladda.create( document.getElementById( 'run' ) );
	var S = Ladda.create( document.getElementById( 'export' ) );
	// var SM = Ladda.create( document.getElementById( 'saveMode' ) );	
	var E = Ladda.create( document.getElementById( 'save' ) );
	// var N = Ladda.create( document.getElementById( 'newBuild' ) );
 //  	var D = Ladda.create( document.getElementById( 'delete' ) );
	l.start();
	S.start();
	// SM.start();
	E.start();
	// N.start();
 // 	D.start();
	var city = $('#allCity').val();
	var sql = $('#customContext').val();
	var templateName = $('#customName').val();
	if(templateName == null) {
		l.stop();
		S.stop();
		SM.stop();
		E.stop();
		N.stop();
		D.stop();
		alert('请选择模板！');
		return false;
	}
	//alert(templateName);return;

	if(city == null){
		l.stop();
		S.stop();
		// SM.stop();
		E.stop();
		// N.stop();
		// D.stop();
		alert('请选择城市筛选项！');
		return false;
	}
	var params = {
		templateName:templateName,
		city:city,
		sql:sql
	};
	//alert($('#_token').val());
	$.post('getTable', params, function(data){
		$('#customTableName').val(JSON.parse(data).filename);
		var fieldArr=new Array();
		var text=(JSON.parse(data).text).split(',');
		for(var i in JSON.parse(data).rows[0]){		  
		  fieldArr[fieldArr.length]={field:i,title:text[fieldArr.length],width:150};
		} 
		//console.log(fieldArr);
		var newData = JSON.parse(data).rows;
		
		/*for(var i=0; i<newData.length; i++){
			//var newData[i].datetime_id = newData[i].datetime_id;
			var test = newData[i].datetime_id;

			//console.log(test);
			//console.log(test);
		}*/
		//alert(newData.datetime_id);
		$('#CustomQueryTable').grid('destroy', true, true);
		$("#CustomQueryTable").grid({
		  columns:fieldArr,
		  dataSource:newData,
		  pager: { limit: 10, sizes: [10, 20, 50, 100] },
		  autoScroll:true,
		  uiLibrary: 'bootstrap'
		});
		if(type == 'file') {
			alert(JSON.parse(data).filename);
			download(JSON.parse(data).filename);
		}
		l.stop();
		S.stop();
		// SM.stop();
		E.stop();	
		// N.stop();
		// D.stop();
	});
}

function getNowFormatDate(date) {
    var date = new Date(date);
    var seperator1 = "-";
    var seperator2 = ":";
    var month = date.getMonth() + 1;
    var strDate = date.getDate();
    if (month >= 1 && month <= 9) {
        month = "0" + month;
    }
    if (strDate >= 0 && strDate <= 9) {
        strDate = "0" + strDate;
    }
    var currentdate = date.getFullYear() + seperator1 + month + seperator1 + strDate
            + " " + date.getHours() + seperator2 + date.getMinutes()
            + seperator2 + date.getSeconds();
    return currentdate;
}

function fileSave(table) {
  var fileName=$("#customTableName").val();
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
  var url = "CustomQuery/getAllCity";
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

function clearCustomQuery(){
	$('#paramQueryMoErbs').val('');
  	setTree();
  	customDblClickFun();
  	 $('#CustomQueryMoTree').treeview('collapseAll', { silent: true });
}

function searchCustomQuery(text){
	//var inputData = $('#paramQueryMoErbs').val();
	var inputData = text;
	inputData = $.trim(inputData);
	if(inputData == '') {
		setTree();
		customDblClickFun();
		return;
	}
	var params = {
		inputData : inputData
	};
	//var url = "CustomQuery/searchCustomTreeData";
	//var treeData;

	$.get("CustomQuery/searchCustomTreeData",params,function(data){
		data = "["+data+"]";
		var tree = '#CustomQueryMoTree';
		$(tree).treeview({data: data});
	});
}
 
function setTree(){
  	var tree = '#CustomQueryMoTree';
  	
 	$(tree).treeview({
 		data: getTree()
 	}); //树

 	// $("#CustomQueryMoTree").delegate("ul li","dblclick",function(){
 	// 	alert("1111")
		// 		 	$('#edit_LTE').modal();
		// 		})
}
  //最后一次触发节点Id
var lastSelectedNodeId = null;
  //最后一次触发时间
var lastSelectTime = null;
 //自定义业务方法
function customBusiness(data){
//    alert("双击获得节点名字： "+data.text);
	var text = data.text;

	getText(text);
	//searchCustomQuery(text);
  
    }
 function clickNode(event, data) {
   if (lastSelectedNodeId && lastSelectTime) {
      var time = new Date().getTime();
      var t = time - lastSelectTime;
      if (lastSelectedNodeId == data.nodeId && t < 300) {
      customBusiness(data);
       }
   }
    lastSelectedNodeId = data.nodeId;
    lastSelectTime = new Date().getTime();
	$('#customName').val(data.text);
}
   //自定义双击事件
 function customDblClickFun(){
   //节点选中时触发
   $('#CustomQueryMoTree').on('nodeSelected', function(event, data) {
    clickNode(event, data)
    });
   //节点取消选中时触发
   $('#CustomQueryMoTree').on('nodeUnselected', function(event, data) {
		
      clickNode(event, data);
//searchCustomQuery();
     });
    }

function getTree() {
	var url = "CustomQuery/getCustomTreeData";
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
	//alert(data);
	return treeData;
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