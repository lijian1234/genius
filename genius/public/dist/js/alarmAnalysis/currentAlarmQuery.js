$(document).ready(function() {
	toogle('currentAlarmQuery');
	initCitys();
});

function initCitys(){
	$('#citys').multiselect({
	  	//dropRight: true,
 	 	buttonWidth: '100%',
	  	//enableFiltering: true,
	  	nonSelectedText:'选择城市',
	  	//filterPlaceholder:'搜索',
	  	nSelectedText:'项被选中',
	  	includeSelectAllOption:true,
	  	selectAllText:'全选/取消全选',
	  	allSelectedText:'已选中所有城市',
	  	maxHeight:200,
	  	maxWidth:'100%'
  	});

	var url = "currentAlarmQuery/getCitys";
	$.get(url,null,function(data){
		data = eval("("+data+")");
		$('#citys').multiselect('dataprovider', data);
	});
}
function query(){
	var citys = $("#citys").val();
	var flag = $('#regionalDimension').prop('checked');
	var placeDim = flag?"meContext":"eutranCell";
	var areaName = $("#areaName").val();
	if(!citys){
		alert("未选择城市无法查询！");
		return;
	}
	var params = {
	    placeDim:placeDim,
	    placeDimName:areaName,
	    city:citys.join(",")
	};
	var fieldArr=new Array();
  	var text = "Event_time,city,subNetwork,meContext,eutranCell,SP_text,Problem_text,Alarm_id";
  	var textArr = text.split(",");
  	for(var i in textArr){
  		if(textArr[fieldArr.length]=="Event_time")  {
  			fieldArr[fieldArr.length]={field:textArr[fieldArr.length],title:textArr[fieldArr.length],width:250};
  		}else if(textArr[fieldArr.length]=="Problem_text"){
  			fieldArr[fieldArr.length]={field:textArr[fieldArr.length],title:textArr[fieldArr.length],width:350};
  		}else{
  			fieldArr[fieldArr.length]={field:textArr[fieldArr.length],title:textArr[fieldArr.length],width:150};
  		}   
  		
  	}

  	$('#currentAlarmTable').grid('destroy', true, true);
	var grid = $("#currentAlarmTable").grid({
	  	columns:fieldArr,
	  	dataSource:{ 
        	url: 'currentAlarmQuery/getTableData', 
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
}

function exportFile(){
	var citys = $("#citys").val();
	var flag = $('#regionalDimension').prop('checked');
	var placeDim = flag?"meContext":"eutranCell";
	var areaName = $("#areaName").val();
	if(!citys){
		alert("未选择城市无法导出！");
		return;
	}
	var params = {
	    placeDim:placeDim,
	    placeDimName:areaName,
	    city:citys.join(",")
	};
	var exportBtn = Ladda.create(document.getElementById("exportBtn"));
	exportBtn.start();

	var url = "currentAlarmQuery/getAllTableData";
	$.post(url,params,function(data){
		var data = eval('(' + data + ')');
        if(data['result']=='true'){
      		var filepath = data['filename'].replace('\\','');
      		download(filepath,'','data:text/csv;charset=utf-8');
    	}else{
      		alert("There is error occured!");
    	}
    	exportBtn.stop();
	})
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
