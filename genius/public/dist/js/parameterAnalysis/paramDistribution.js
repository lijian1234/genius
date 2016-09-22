$(function(){
	toogle('paramDistribution');

	
	initDate();
	setMOTree();
	initCitys();
	
})

function initDate(){
	var url="paramDistribution/getDate";
	$.get(url,null,function(data){
		data = eval(data);
		var date = $("#date").select2({
	        placeholder: "请选择日期",
	        //allowClear: true,
	        data:data
      	});
      	var task = getCurrentDate('kget');
      	$("#date").val(getCurrentDate('kget')).trigger('change');
      	if($("#date").val() == null){
	        $("#date").val(getYesterdayDate('kget')).trigger('change');
      	}
	})
}

function getCurrentDate(taskType){
  	var mydate = new Date();
  	var myyear = mydate.getYear();
  	var myyearStr = (myyear+"").substring(1);
  	var mymonth = mydate.getMonth()+1; //值范围0-11
  	var mydate = mydate.getDate();  //值范围1-31
  	var mymonthStr = "";
  	var mydateStr = "";
  	mymonthStr = mymonth >= 10 ? mymonth : '0' + mymonth;
  	mydateStr = mydate >= 10 ? mydate : '0' + mydate;
  	var kgetDate = taskType+myyearStr+mymonthStr+mydateStr;
  	return kgetDate;
}
function getYesterdayDate(taskType){
  	var mydate = new Date();
  	var yesterday_miliseconds = mydate.getTime() - 1000 * 60 * 60 * 24;
  	var Yesterday = new Date();
  	Yesterday.setTime(yesterday_miliseconds);

  	var yesterday_year = Yesterday.getYear().toString().substring(1.3);
  	var month_temp = Yesterday.getMonth() + 1;
  	var yesterday_month = month_temp > 9 ? month_temp.toString() : "0" + month_temp.toString();
  	var d = Yesterday.getDate();
  	var Day = d > 9 ? d.toString() : "0" + d.toString();
  	var kgetDate = taskType+yesterday_year+yesterday_month+Day;
  	return kgetDate;
}

function setMOTree(){
  $.get('common/json/parameterTreeData.json',null,function(data){
    date =eval("("+data+")");
    var options = {
 	 	bootstrap2: false, 
      	showTags: true,
      	levels: 2,
      	data:data,
      	onNodeSelected: function(event, data) {
      		$("#MOFlag").val(data.text);
      		setParamTree();
       	}
    };

    $('#MOQueryTree').treeview(options);  
  });
}
//清空模板树
function clearMO(){
  	$('#queryMO').val('');
  	setMOTree();
}

//筛选模板树
function searchMO(){
   	var pattern = $('#queryMO').val();
   	if(!pattern){
   		return;
   	}
  
  	$('#MOQueryTree').on('searchComplete', function(event, data) {
	    //alert(data);
	    var moData = new Array();
	    for(i in data){
	      	var obj = {
	        	id : data[i].id,
	        	text : data[i].text
	      	}
	      	moData.push(obj);
	    }
	    var options = {
	      	bootstrap2: false, 
	      	showTags: true,
	      	levels: 2,
	      	data:moData,
	      	onNodeSelected: function(event, data) {
	      		$("#MOFlag").val(data.text);
	      		setParamTree();
	       	}
	    };

	    $('#MOQueryTree').treeview(options);  
  	});
   	$('#MOQueryTree').treeview('search', [ pattern, {
	  	ignoreCase: true,   // case insensitive
	  	exactMatch: false,    // like or equals
	  	revealResults: true,  // reveal matching nodes
  	}]);

}
function setParamTree(pattern){
	var data = {
		"task" : $("#date").val(),
		"mo" : $("#MOFlag").val(),
		"pattern" : pattern
	};
	var url = "paramDistribution/getParameterList"
	$.get(url,data,function(data){
		
		data = JSON.parse(data);
		$("#idNum").val(data["count"]);
	    var options = {
	 	 	bootstrap2: false, 
	      	showTags: true,
	      	levels: 2,
	      	data:eval("("+data['content']+")"),
	      	onNodeSelected: function(event, data) {
	      		$("#paramFlag").val(data.text)
	      		parameterDistributeSearch();
	       	}
	    };

	    $('#paramQueryTree').treeview(options);  

	})

}
//清空模板树
function clearParam(){
  	$('#queryParam').val('');
  	setParamTree();
}

//筛选模板树
function searchParam(){
   	var pattern = $('#queryParam').val();
   	if(!pattern){
   		return;
   	}
   	if(!$("#MOFlag").val()){
   		alert("请先选择MO项！");
   		return;
   	}
  	setParamTree(pattern);
  	

}
function parameterDistributeSearch(){
	var databaseDate = $("#date").val();
	var mo = $("#MOFlag").val();
	var parameterName = $("#paramFlag").val();

	var databaseconnCity;
  	var databaseconnCityParam = {
    	db:'mongs',
    	table:'databaseconn'
  	}
    //$.ajaxSetup({async : false});  //同步执行
    $.get('paramDistribution/getCity',databaseconnCityParam,function(data){
      	databaseconnCity = JSON.parse(data);

  	 	var params_distribution={
	      	db:databaseDate, 
	      	table:mo,
	      	parameterName:parameterName,
	      	city:databaseconnCity
    	}; 
    	$.post("paramDistribution/getChartData",params_distribution,function(data){

    		var cat_str = JSON.stringify(JSON.parse(data).categories);
	      	var ser_str = JSON.stringify(JSON.parse(data).series);
	      	var cat_obj = eval("("+cat_str+")");  
	      	var ser_obj = eval("("+ser_str+")");
	      	var y_data = JSON.parse(data).y_data;


	      	$('#parameterDistributeView').highcharts({
          		chart: { type: 'column' },
	          	/*exporting: {   
	              	enabled:true,     
	          	}, */
          		title: {
          			text: parameterName+'分布',
              		x: -20 //center
          		},
          		credits: {  
            		enabled: false  
          		},
          		subtitle :{
              		text: '  ',
              		x: -20
          		},
          		xAxis: {
              		categories: cat_obj
          		},
          		yAxis: {
              		title: {
                  		text: 'Number'
              		},
              		plotLines: [{
                  		value: 0,
                  		width: 1,
                  		color: '#808080'
              		}],
              		tickPositions: y_data
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
		            //floating: true,
		            backgroundColor: '#FFFFFF'
          		},
            	series: ser_obj
      		}); 
      		$('#parameterDistributeView').addClass("myTables");
    	})

    });
}
function initCitys(){
	$('#citys').multiselect({
	  	//dropRight: true,
 	 	buttonWidth: '100%',
	  	//enableFiltering: true,
	  	nonSelectedText:'城市',
	  	//filterPlaceholder:'搜索',
	  	nSelectedText:'项被选中',
	  	includeSelectAllOption:true,
	  	selectAllText:'全选/取消全选',
	  	allSelectedText:'已选中所有城市',
	  	maxHeight:200,
	  	maxWidth:'100%'
  	});

	$.get("paramDistribution/getCitySelect",null,function(data){
		data = eval("("+data+")");
		$('#citys').multiselect('dataprovider', data);

	})
}

function queryByCity(){
	var task = $("#date").val();
	var mo = $("#MOFlag").val();
	if(!mo){
		alert("请先选择MO项！");
		return;
	}
	var parameterName = $("#paramFlag").val();
	if(!parameterName){
		alert("请先选择参数项！");
		return;
	}

 	var citys = $('#citys').val();
  if(!citys){
    citys = [];
    $("#citys option").each(function(){
      citys.push($(this).val());
    })
  }
  	//var cityNodes = cityTree.tree('getChecked');
  	var params = {
    	db:task,
        table:mo,
        parameterName:parameterName,
        city:JSON.stringify(citys)
  	}
  	$.post("paramDistribution/getTableHeader",params,function(data){

      var fieldArr=new Array();
      var text=(JSON.parse(data).text).split(',');
      for(var i in text){     
        if(fieldArr.length == 0){
          fieldArr[fieldArr.length]={field:text[fieldArr.length],title:text[fieldArr.length],hidden:true};
        }else{
          if(text[fieldArr.length] == "mo"){
            fieldArr[fieldArr.length]={field:text[fieldArr.length],title:text[fieldArr.length],width:300};
          }else{
            fieldArr[fieldArr.length]={field:text[fieldArr.length],title:text[fieldArr.length],width:textWidth(text[fieldArr.length])};
          }
          
        }
          
      }
      $('#parameterDistributeTable').grid('destroy', true, true);
      var grid = $("#parameterDistributeTable").grid({
          columns:fieldArr,
          //dataSource:"signalingBacktracking/getEventData",
          dataSource:{ 
            url: 'paramDistribution/getTableData', 
            //data: {},
            success: function(data){
              data = eval("("+data+")");
              grid.render(data);

            } 
          },
          params : params,
          pager: { limit: 10, sizes: [10, 20, 50, 100] },
          autoScroll:true,
          uiLibrary: 'bootstrap',
          autoLoad: true          
      });


  	})

}
function textWidth(text){
      var length = text.length;
      if(length > 15){
        return length*10;
      }
      return 150;
    }
function exportByCity(){
      var task = $("#date").val();
      var mo = $("#MOFlag").val();
      if(!mo){
        alert("请先选择MO项！");
        return;
      }
      var parameterName = $("#paramFlag").val();
      if(!parameterName){
        alert("请先选择参数项！");
        return;
      }

      var citys = $('#citys').val();
      if(!citys){
        citys = [];
        $("#citys option").each(function(){
          citys.push($(this).val());
        })
      }
      //var cityNodes = cityTree.tree('getChecked');
      var params = {
        db:task,
          table:mo,
          parameterName:parameterName,
          city:JSON.stringify(citys)
      }
      $.post("paramDistribution/getAllTableData",params,function(data){

        var filterData = eval("("+data+")").rows;

        var Data=new Array();
        var text=new Array();
        var j=0;
        for(var field in filterData[0]){
          text[j++]=field;
        }
        for(var i in filterData){
          var row=new Array();
          for(var j in text){
            row[j]=filterData[i][text[j]].replace(new RegExp(',', 'g'),' ').replace(new RegExp('\n', 'g'),' ');
            }   
          Data[i]=row.join(',');
        }
        
        var fileContent=text+'\n'+Data.join('\n');
        
        var url = "paramDistribution/exportCSV";
        var data = {
          "fileContent":fileContent,
          "citys" : citys.join("_")
        };
        $.post(url,data,function(data){
          if(data){
            download(data,'','data:text/csv;charset=utf-8');
          }else{
            alert("出现异常，请重试");
            return;
          }
          
        })

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