$(document).ready(function() {
	toogle('relationNonHandover');
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

	var url = "relationNonHandover/getCitys";
	$.get(url,null,function(data){
		data = eval("("+data+")");
		$('#citys').multiselect('dataprovider', data);
	});
}
function query(){
	var citys = $("#citys").val();
	var params = {
	    city:citys
	};
  var queryBtn = Ladda.create(document.getElementById("queryBtn"));
  queryBtn.start();
	var url = "relationNonHandover/getDataHeader";
  	$.get(url,null,function(data){
    	var text = eval("("+data+")").text;
    	var textArr = text.split(",");
    	var fieldArr=new Array();
    	for(var i in textArr){
        	fieldArr[fieldArr.length]={field:textArr[fieldArr.length],title:textArr[fieldArr.length],width:200};
    	}

    	$('#resultTable').grid('destroy', true, true);
    	var grid = $("#resultTable").grid({
        	columns:fieldArr,
        	dataSource:{ 
            	url: 'relationNonHandover/getTableData', 
            	success: function(data){
              		data = eval("("+data+")");
              		grid.render(data);
                  queryBtn.stop();
            	} 
          	},
        	params : params,
        	pager: { limit: 10, sizes: [10, 20, 50, 100] },
        	autoScroll:true,
        	uiLibrary: 'bootstrap',
        	primaryKey : 'id',
        	autoLoad: true   
    	});
  	});
}

function exportFile(){
	var citys = $("#citys").val();
	var params = {
	    city:citys
	};
	var exportBtn = Ladda.create(document.getElementById("exportBtn"));
	exportBtn.start();

	var url = "relationNonHandover/getAllTableData";
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

