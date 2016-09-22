$(document).ready(function(){
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
	setTime();
	//getAllDatabase();
	getAllCity();
	toogle('LTENeighborAnalysis');

});
function setTime(){
  $("#dateTime").datepicker({format: 'yyyy-mm-dd'});  //返回日期
  var nowTemp = new Date();
  $("#dateTime").datepicker('setValue', nowTemp);
  //alert(nowTemp);
  var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
  var checkin = $('#dateTime').datepicker({
	onRender: function(date) {
	  return date.valueOf() < now.valueOf() ? '' : '';
	}
  }).on('changeDate', function(ev) {
	checkin.hide();
	}).data('datepicker');
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
function getAllCity(){
	$('#city').multiselect({
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
  	var url = "NetworkOptimization/getAllCity";
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
		    $('#city').multiselect('dataprovider', newOptions);
	  	}
	});
}
/*function getAllDatabase(){
	$('#dataBase').multiselect({
	  dropRight: true,
	  buttonWidth: '100%',
	  //enableFiltering: true,
	  nonSelectedText:'请选择库名',
	  //filterPlaceholder:'搜索',
	  nSelectedText:'项被选中',
	  includeSelectAllOption:true,
	  selectAllText:'全选/取消全选',
	  allSelectedText:'已选中所有平台类型',
	  maxHeight:200,
	  maxWidth:'100%'
  });*/
  //var url = "LTEQuery/getAllCity";
  /*var url = "NetworkOptimization/getAllDatabase";
  $.ajax({
	type:"GET",
	url:url,
	dataType:"json",
	success:function(data){
		//console.log(data);
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
	  $('#dataBase').multiselect('dataprovider', newOptions);
	}
  });
}
*/

function doSearchGSM(type){
    var l = Ladda.create( document.getElementById( 'search' ) );
    var E = Ladda.create( document.getElementById( 'export' ) );

   	l.start();	
   	E.start();

   	var select = $('#city').val();
   	var dateTime = $('#dateTime').val();
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
 		select:select,
 		dateTime:dateTime
 	}
	$.get('networkOptimization/LTENeighAnalysis', params, function(data){
		if(data.error == 'error'){
			alert("数据不存在，请重新选择！");
			l.stop();
			E.stop();
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
		$('#GSMNeighTable').grid('destroy', true, true);
		var grid = $("#GSMNeighTable").grid({
		  columns:fieldArr,
		  params:params,
		  dataSource:{
		  	url: 'networkOptimization/LTENeighAnalysisSplit', 
	        success: function(data){
	          data = eval("("+data+")");
	          if(data.error == 'error'){
	          		$('#GSMNeighTable').grid('destroy', true, true);
					alert("数据不存在，请重新选择！");
					l.stop();
					E.stop();
					return;
				}
	          // console.log($('#filename').val(JSON.parse(data).filename));
	          $('#filename').val(data.filename);
	          if(type == 'file') {
			  	alert(data.filename);
			  	download(data.filename);
			  	//return;
			}
	          grid.render(data);
	          l.stop();
			  E.stop();
			  $('#exportBtn').removeAttr('disabled');
	        } 
		  },
		  pager: { limit: 10, sizes: [10, 20, 50, 100] },
		  autoScroll:true,
		  uiLibrary: 'bootstrap'
		});
		
	});
}

function exportAll(){
	var l = Ladda.create( document.getElementById( 'search' ) );
    var E = Ladda.create( document.getElementById( 'export' ) );
   	l.start();	
   	E.start();

   	var select = $('#city').val();
   	var dateTime = $('#dateTime').val();
   
 	var params = {
 		select:select,
 		dateTime:dateTime
 	}
	/*$.get('networkOptimization/GSMNeighAnalysisAll', params, function(data){
		data = eval("("+data+")");
	    console.log(data.filename);
	    if(data.error == 'error'){
			alert("数据不存在，请重新选择！");
			l.stop();
			E.stop();
			return;
		}
	    $('#filename').val(data.filename);
	    console.log($('#filename').val());
	    
		alert(data.filename);
		download(data.filename);
		
	 //    l.stop();
		// E.stop();			
	});*/
	$.get('networkOptimization/GSMNeighAnalysisLteAll', params, function(data){
		if(data.error == 'error'){
			alert("数据不存在，请重新选择！");
			l.stop();
			E.stop();
			return;
		}
	    $('#filenameLte').val(data.filename);
	    console.log($('#filenameLte').val());
	    
		alert(data.filename);
		download(data.filename);
		
	    l.stop();
		E.stop();			
	});
}

function fileSave(){
	var fileName=$("#filename").val();
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

function textWidth(text){
    var length = text.length;
    if(length > 15){
        return length*10;
    }
    return 150;
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