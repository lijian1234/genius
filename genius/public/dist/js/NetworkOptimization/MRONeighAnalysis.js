$(function(){
	
	toogle('MROServeNeighAnalysis');

	//getAllDatabase();
	getAllCity("#city");
	getAllCity("#city_mre");
	setTime("#dateTime");
	setTime("#dateTime_mre"); 
	$('#input1').val(3);
	$('#input2').val(3);
	$('#input3').val(50);
	$('#input4').val(10);
	$('#input5').val(50);
	$('#input6').val(-115);
	$('#input7').val(-10);
	$('#input8').val(-110);
	$('#input1Temp').val(3);
	$('#input2Temp').val(3);
	$('#input3Temp').val(50);
	$('#input4Temp').val(10);
	$('#input5Temp').val(50);
	$('#input6Temp').val(-115);
	$('#input7Temp').val(-10);
	$('#input8Temp').val(-110);

});
function setTime(timeId){
  $(timeId).datepicker({format: 'yyyy-mm-dd'});  //返回日期
  var nowTemp = new Date();
  $(timeId).datepicker('setValue', nowTemp);
  //alert(nowTemp);
  var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
  var checkin = $(timeId).datepicker({
	onRender: function(date) {
	  return date.valueOf() < now.valueOf() ? '' : '';
	}
  }).on('changeDate', function(ev) {
	checkin.hide();
	}).data('datepicker');
}
function getAllCity(cityId){
	$(cityId).multiselect({
    dropRight: true,
    buttonWidth: 160,
    //enableFiltering: true,
    nonSelectedText:'请选择城市',
    //filterPlaceholder:'搜索',
    nSelectedText:'项被选中',
    includeSelectAllOption:true,
    selectAllText:'全选/取消全选',
    allSelectedText:'已选中所有平台类型',
    maxHeight:200,
    width:220
  	});
  	var url = "MROServeNeighAnalysis/getAllCity";
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
		    $(cityId).multiselect('dataprovider', newOptions);
	  	}
	});
}
function openConfigInfo(){
	$("#config_information").modal();
}
function updateConfigInfo(){
	$("#input1").val($("#input1Temp").val());
	$("#input2").val($("#input2Temp").val());
	$("#input3").val($("#input3Temp").val());
	$("#input4").val($("#input4Temp").val());
	$("#input5").val($("#input5Temp").val());
	$("#input6").val($("#input6Temp").val());
	$("#input7").val($("#input7Temp").val());
	$("#input8").val($("#input8Temp").val());
	$("#config_information").modal('hide');
}
function query(){
	var dataBase = $("#city").val();
	var dateTime = $('#dateTime').val();
	var params = {
		dataBase : dataBase,
		dateTime:dateTime
	}

	var queryBtn = Ladda.create( document.getElementById( 'queryBtn' ) );
    var exportBtn = Ladda.create( document.getElementById( 'exportBtn' ) );
   	queryBtn.start();	
   	exportBtn.start();

   	$.get('MROServeNeighAnalysis/getMroServeNeighDataHeader', params, function(data){
		if(data.error == 'error'){
			alert("数据不存在，请重新选择！");
			queryBtn.stop();
			exportBtn.stop();
			return;
		}
		var fieldArr=new Array();
		for(var k in data){
			
		  	if(fieldArr.length == 0){
		      	fieldArr[fieldArr.length]={field:k,title:k,hidden : true};
		    }else{
		    	if (k == 'datetime_id') {
		    		fieldArr[fieldArr.length]={field:k,title:k,width:180};
		    	}else{
			      	fieldArr[fieldArr.length]={field:k,title:k,width:textWidth(k)};
				}
		    }
		}
		$('#mroServeNeighTable').grid('destroy', true, true);
		var grid = $("#mroServeNeighTable").grid({
		  	columns:fieldArr,
		  	params:params,
		  	dataSource:{
			  	url: 'MROServeNeighAnalysis/getMroServeNeighData', 
		        success: function(data){
		          	data = eval("("+data+")");
		          	if(data.error == 'error'){
		          		$('#mroServeNeighTable').grid('destroy', true, true);
						alert("数据不存在，请重新选择！");
						queryBtn.stop();
						exportBtn.stop();
						return;
					}
		          	grid.render(data);

		          	queryBtn.stop();
				  	exportBtn.stop();
		        } 
		  	},
		  	pager: { limit: 10, sizes: [10, 20, 50, 100] },
		  	autoScroll:true,
		  	uiLibrary: 'bootstrap'
		});
		
	});
}
//mre数据查询
function query_mre(){
	var dataBase = $("#city_mre").val();
	var dateTime = $('#dateTime_mre').val();
	var input1 = $('#input1').val();
   	var input2 = $('#input2').val();
   	var input3 = $('#input3').val();
   	var input4 = $('#input4').val();
   	var input5 = $('#input5').val();
   	var input6 = $('#input6').val();
   	var input7 = $('#input7').val();
   	var input8 = $('#input8').val();

   	if(input1 == ''){
   		input1 = 3;
   	}
   	if(input2 == ''){
   		input2 = 3;
   	}
   	if(input3 == ''){
   		input3 = 50;
   	}
   	if(input4 == ''){
   		input4 = 10;
   	}
   	if(input5 == ''){
   		input5 = 50;
   	}
   	if(input6 == ''){
   		input6 = -115;
   	}
   	if(input7 == ''){
   		input7 = -10;
   	}
   	if(input8 == ''){
   		input8 = -110;
   	}
	var params = {
		input1:input1,
 		input2:input2,
 		input3:input3,
 		input4:input4,
 		input5:input5,
 		input6:input6,
 		input7:input7,
 		input8:input8,
		dataBase : dataBase,
		dateTime:dateTime
	}

	var queryBtn_mre = Ladda.create( document.getElementById( 'queryBtn_mre' ) );
    var exportBtn_mre = Ladda.create( document.getElementById( 'exportBtn_mre' ) );
   	queryBtn_mre.start();	
   	exportBtn_mre.start();

   	$.get('MREServeNeighAnalysis/getMreServeNeighDataHeader', params, function(data){
		if(data.error == 'error'){
			alert("数据不存在，请重新选择！");
			queryBtn_mre.stop();
			exportBtn_mre.stop();
			return;
		}
		var fieldArr=new Array();
		for(var k in data){
			
		  	if(fieldArr.length == 0){
		      	fieldArr[fieldArr.length]={field:k,title:k,hidden : true};
		    }else{
		    	if (k == 'datetime_id') {
		    		fieldArr[fieldArr.length]={field:k,title:k,width:180};
		    	}else{
			      	fieldArr[fieldArr.length]={field:k,title:k,width:textWidth(k)};
				}
		    }
		}
		$('#mreServeNeighTable').grid('destroy', true, true);
		var grid = $("#mreServeNeighTable").grid({
		  	columns:fieldArr,
		  	params:params,
		  	dataSource:{
			  	url: 'MREServeNeighAnalysis/getMreServeNeighData', 
		        success: function(data){
		          	data = eval("("+data+")");
		          	if(data.error == 'error'){
		          		$('#mreServeNeighTable').grid('destroy', true, true);
						alert("数据不存在，请重新选择！");
						queryBtn_mre.stop();
						exportBtn_mre.stop();
						return;
					}
		          	grid.render(data);

		          	queryBtn_mre.stop();
				  	exportBtn_mre.stop();
		        } 
		  	},
		  	pager: { limit: 10, sizes: [10, 20, 50, 100] },
		  	autoScroll:true,
		  	uiLibrary: 'bootstrap'
		});
		
	});
}
function textWidth(text){
    var length = text.length;
    if(length > 15){
        return length*10;
    }
    return 150;
}

function exportFile(){

	var dataBase = $("#city").val();
	var dateTime = $('#dateTime').val();

	var params = {
		dataBase : dataBase,
		dateTime:dateTime
	}

	var queryBtn = Ladda.create( document.getElementById( 'queryBtn' ) );
    var exportBtn = Ladda.create( document.getElementById( 'exportBtn' ) );
   	queryBtn.start();	
   	exportBtn.start();
   	var url = "MROServeNeighAnalysis/getAllMroServeNeighData";
   	$.get(url,params,function(data){
   		var data = eval('(' + data + ')');
   		if(data.error == 'error'){
      		$('#mroServeNeighTable').grid('destroy', true, true);
			alert("数据不存在，请重新选择！");
			queryBtn.stop();
			exportBtn.stop();
			return;
		}
        if(data['result']=='true'){
      		var filepath = data['filename'].replace('\\','');
      		download(filepath,'','data:text/csv;charset=utf-8');
    	}else{
      		alert("数据不存在，请重新选择！");
    	}
    	queryBtn.stop();
    	exportBtn.stop();
   	})
}
function exportFile_mre(){

	var dataBase = $("#city_mre").val();
	var dateTime = $('#dateTime_mre').val();
	var input1 = $('#input1').val();
   	var input2 = $('#input2').val();
   	var input3 = $('#input3').val();
   	var input4 = $('#input4').val();
   	var input5 = $('#input5').val();
   	var input6 = $('#input6').val();
   	var input7 = $('#input7').val();
   	var input8 = $('#input8').val();

   	if(input1 == ''){
   		input1 = 3;
   	}
   	if(input2 == ''){
   		input2 = 3;
   	}
   	if(input3 == ''){
   		input3 = 50;
   	}
   	if(input4 == ''){
   		input4 = 10;
   	}
   	if(input5 == ''){
   		input5 = 50;
   	}
   	if(input6 == ''){
   		input6 = -115;
   	}
   	if(input7 == ''){
   		input7 = -10;
   	}
   	if(input8 == ''){
   		input8 = -110;
   	}
	var params = {
		input1:input1,
 		input2:input2,
 		input3:input3,
 		input4:input4,
 		input5:input5,
 		input6:input6,
 		input7:input7,
 		input8:input8,
		dataBase : dataBase,
		dateTime:dateTime
	}

	var queryBtn_mre = Ladda.create( document.getElementById( 'queryBtn_mre' ) );
    var exportBtn_mre = Ladda.create( document.getElementById( 'exportBtn_mre' ) );
   	queryBtn_mre.start();	
   	exportBtn_mre.start();
   	var url = "MREServeNeighAnalysis/getAllMreServeNeighData";
   	$.get(url,params,function(data){
   		var data = eval('(' + data + ')');
   		if(data.error == 'error'){
      		$('#mreServeNeighTable').grid('destroy', true, true);
			alert("数据不存在，请重新选择！");
			queryBtn_mre.stop();
			exportBtn_mre.stop();
			return;
		}
        if(data['result']=='true'){
      		var filepath = data['filename'].replace('\\','');
      		download(filepath,'','data:text/csv;charset=utf-8');
    	}else{
      		alert("数据不存在，请重新选择！");
    	}
    	queryBtn_mre.stop();
    	exportBtn_mre.stop();
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