$(document).ready(function() {
	toogle('siteManage');
	// 加载用户表
	//doQuery4G();
	//doQuery2G();
	setTree4G();
	setTree2G();

})

function setTree4G(){
	var data_4G = {
		table : "databaseconn",
		text : "cityChinese",
		value : "connName"
	};
  $.get('siteManage/TreeQuery',data_4G,function(data){
    var options = {
      bootstrap2: false, 
      showTags: true,
      levels: 2,
      data:data,
      onNodeSelected: function(event, data) {
    		$("#4GSiteValue").val(data.value);
    		doQuery4G(data.value);
      	
       }
    };

    $('#4GQueryTree').treeview(options);  
  });
}
function setTree2G(){
	var data_2G = {
		table : "databaseconn_2G",
		text : "cityChinese",
		value : "connName"
	}
  $.get('siteManage/TreeQuery',data_2G,function(data){
    var options = {
      bootstrap2: false, 
      showTags: true,
      levels: 2,
      data:data,
      onNodeSelected: function(event, data) {
      		$("#2GSiteValue").val(data.value);
      		doQuery2G(data.value);
      	
       }
    };

    $('#2GQueryTree').treeview(options);  
  });
}

//清空模板树
function clear4GQuery(){
  $('#paramsQuery4G').val('');
  setTree4G();
}
function clear2GQuery(){
  $('#paramsQuery2G').val('');
  setTree2G();
}

//筛选模板树
function search4GQuery(){
   var pattern = $('#paramsQuery4G').val();
  
  	$('#4GQueryTree').on('searchComplete', function(event, data) {
	    //alert(data);
	    var moData = new Array();
	    for(i in data){
	      var obj = {
	        id : data[i].id,
	        text : data[i].text,
	        value : data[i].value
	      }
	      moData.push(obj);
	    }
	    var options = {
	      bootstrap2: false, 
	      showTags: true,
	      levels: 2,
	      data:moData,
	      onNodeSelected: function(event, data) {
      		$("#4GSiteValue").val(data.value);
      		doQuery4G(data.value);
	       }
	    };

	    $('#4GQueryTree').treeview(options);  
  	});
   	$('#4GQueryTree').treeview('search', [ pattern, {
	  	ignoreCase: true,   // case insensitive
	  	exactMatch: false,    // like or equals
	  	revealResults: true,  // reveal matching nodes
  	}]);

}
function search2GQuery(){
   var pattern = $('#paramsQuery2G').val();
  
  	$('#2GQueryTree').on('searchComplete', function(event, data) {
	    //alert(data);
	    var moData = new Array();
	    for(i in data){
	      var obj = {
	        id : data[i].id,
	        text : data[i].text,
	        value : data[i].value
	      }
	      moData.push(obj);
	    }
	    var options = {
	      bootstrap2: false, 
	      showTags: true,
	      levels: 2,
	      data:moData,
	      onNodeSelected: function(event, data) {
	      		$("#2GSiteValue").val(data.value);
	      		doQuery2G(data.value);
	       }
	    };

	    $('#2GQueryTree').treeview(options);  
  	});
   	$('#2GQueryTree').treeview('search', [ pattern, {
	  	ignoreCase: true,   // case insensitive
	  	exactMatch: false,    // like or equals
	  	revealResults: true,  // reveal matching nodes
  	}]);

}


function doQuery4G(value){
	var data = {
		value : value,
		table : "siteLte"
	}
  var fieldArr=new Array();
  var text = "id,ecgi,cellName,siteName,cellNameChinese,longitude,latitude,dir,pci,channel,coverageType,tiltM,tiltE,Height,city,importDate,band";
  var textArr = text.split(",");
  for(var i in textArr){     
    if(fieldArr.length == 0){
      fieldArr[fieldArr.length]={field:textArr[fieldArr.length],title:textArr[fieldArr.length],hidden : true};
    }else{
      fieldArr[fieldArr.length]={field:textArr[fieldArr.length],title:textArr[fieldArr.length],width:150};
    }
  } 
	$('#4GTable').grid('destroy', true, true);
	var grid = $("#4GTable").grid({
	  	columns:fieldArr,
	  	dataSource:{ 
        url: 'siteManage/QuerySite4G', 
        //data: {},
        success: function(data){
          data = eval("("+data+")");
          grid.render(data);

        } 
      },
      params : data,
	  	pager: { limit: 10, sizes: [10, 20, 50, 100] },
	  	autoScroll:true,
	  	uiLibrary: 'bootstrap',
	  	primaryKey : 'id',
      autoLoad: true   
	});
}
function doQuery2G(value){
	var data = {
		value : value,
		table : "siteGsm"
	}
  var fieldArr=new Array();
  var text = "id,CELL,CellIdentity,BAND,ARFCN,Longitude,Latitude,dir,height,plmnIdentity_mcc,plmnIdentity_mnc,LAC,BCCH,BCC,NCC,dtmSupport,city,importDate";
  var textArr = text.split(",");
  for(var i in textArr){     
    if(fieldArr.length == 0){
      fieldArr[fieldArr.length]={field:textArr[fieldArr.length],title:textArr[fieldArr.length],hidden : true};
    }else{
      fieldArr[fieldArr.length]={field:textArr[fieldArr.length],title:textArr[fieldArr.length],width:150};
    }
      
  } 

	$('#2GTable').grid('destroy', true, true);
	var grid = $("#2GTable").grid({
	  	columns:fieldArr,
	  	dataSource:{ 
        url: 'siteManage/QuerySite2G', 
        //data: {},
        success: function(data){
          data = eval("("+data+")");
          grid.render(data);

        } 
      },
      params : data,
	  	pager: { limit: 10, sizes: [10, 20, 50, 100] },
	  	autoScroll:true,
	  	uiLibrary: 'bootstrap',
	  	primaryKey : 'id',
      autoLoad: true   
	});
}

function import4G(){
	$("#import_modal").modal();
	$("#siteSign").val("4G");
	$("#fileImportName").val("");
	$("#fileImport").val("");
}
function import2G(){
	$("#import_modal").modal();
	$("#siteSign").val("2G");
	$("#fileImportName").val("");
	$("#fileImport").val("");
}

function toName(self){
	$("#fileImportName").val(self.value);
}
function importFile(){

	var siteSign = $("#siteSign").val();
	var table = "";
	var city = "";
	if(siteSign == "4G"){
		table = "siteManage";
		city = $("#4GSiteValue").val();
	}else if(siteSign == "2G"){
		table = "2GSiteManage";
		city = $("#2GSiteValue").val();
	}


	var data=getParam(table);
    if(data==false)
    {
        return false;
    }
    $.ajaxFileUpload({
      url : 'siteManage/uploadFile',   　
      data : data,
      fileElementId : "fileImport",           
      secureuri : false,                          
      dataType:'json',
      type: "post",                     
      success:function(data, status){ 
        $("#import_modal").modal('hide');
         //document.getElementById(table+'Loading').hidden=true;
         //$('#'+table+'Table').datagrid('reload');
     	if(data == "4G"){
     		doQuery4G(city);
		}else if(data == "2G"){
			doQuery2G(city);
		}
		alert("上传成功");

      },
      error:function(data, status, e){
      	alert("上传失败");
         //$.messager.alert('提示', '上传失败'+e, 'warning');
         //document.getElementById(table+'Loading').hidden=true;
      }
   });
}
//获取当前时间
function getNowFormatDate() {
    var date = new Date();
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
function getParam(table)
{
    var importDate = getNowFormatDate();
    if(table=='siteManage')
    {
        /*var node = $('#cityTree').tree('getSelected');
        var city = '';
        if (node){
           if($('#cityTree').tree('isLeaf',node.target)){//判断是否是叶子节点
               city = node.value;
            }      
        }*/
        var city = $("#4GSiteValue").val();    

      if(city ==''||city =='city')
      {
        alert("请选择城市！");
        return false;
      }
      var data={table:table,city:city,importDate:importDate};
  }else if(table == '2GSiteManage'){
    /*var node = $('#2GCityTree').tree('getSelected');
        var city = '';
        if (node){
           if($('#2GCityTree').tree('isLeaf',node.target)){//判断是否是叶子节点
               city = node.value;
            }      
        }  */  
         var city = $("#2GSiteValue").val();    
      if(city ==''||city =='city')
      {
        alert("请选择城市！");
        return false;
      }
      var data={table:table,city:city,importDate:importDate};
  }
    
   if($("#fileImport").val() == "")
    {
        alert("请选择上传的文件！");
        return false;
    }
    return data;
    
}
function export4G(){
	filetoExport("siteManage");
}
function export2G(){
	filetoExport("2GSiteManage");
}	

function filetoExport(table)
{   
    //document.getElementById(table+'Loading').hidden=false;
    if(table=='siteManage'){
      var city = $("#4GSiteValue").val();    

      if(city =='')
      {
        alert("请选择城市！");
        return false;
      }
        var params={
         city:city,
         table:table
        }
    }else if(table == '2GSiteManage'){
       var city = $("#2GSiteValue").val();    
      if(city =='')
      {
        alert("请选择城市！");
        return false;
      }
          var params={
           city:city,
           table:table
         }
    }
  
 $.get('siteManage/downloadFile',params,function(data){
   var data = eval('(' + data + ')');
        if(data['result']=='true')
    {
      var filepath = data['filename'].replace('\\','');
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