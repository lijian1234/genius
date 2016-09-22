var lastEvent;


$(function(){
	toogle('signalingBacktracking');

	initDataBase();
	initEventName();
	//initEcgi();

	//绑定信令图的tab页面，保证页面出来才开始画图，避免画图错位的问题
	$("#table_tab_1_nav").on("shown.bs.tab",function(){
		if($("#eventChoosedChange").val()=="true"){
			if($('#sectionchoose').val()=="true"){
				doSearchEvent_chart();
			}
		}
	})

	
})

function initDataBase(){
	var url="signalingBacktracking/getDataBase";
	var data = {"type":"ctrsystem"};
	$.get(url,data,function(data){
		if(data == "login"){
			alert("尚未登录，不能进行信令流程查询！");
		  	window.location.href = 'login';
		  	return;
		}
		data = eval(data);
		var database = $("#database").select2({
	        placeholder: "请选择日期",
	        //allowClear: true,
	        data:data
      	});
      	//从失败原因分析跳转过来的参数
		if(window.location.search){
			var task = window.location.search.split("&")[0].split("=")[1];
			var ueRef = window.location.search.split("&")[1].split("=")[1];
			$("#database").val(task);
			$("#ueref").val(ueRef);
			$("#ueRefChoosed").val(ueRef);
			getEventNameandEcgi(task);
			//queryProcess();
			filterProcess();
			$("#eventChoosedChange").val("true");
			$('#sectionchoose').val("true");
		}else{
			getEventNameandEcgi(data[0].id);
		}
      	$("#database").on("change",function(e){
      		$('#eventName').multiselect('dataprovider', null);
  			$('#ecgi').multiselect('dataprovider', null);
      		var db = $("#database").val();
      		getEventNameandEcgi(db);
      	})

	})
}

function initEventName(){
  	$('#eventName').multiselect({
	  	//dropRight: true,
 	 	buttonWidth: '100%',
	  	//enableFiltering: true,
	  	nonSelectedText:'事件名称',
	  	//filterPlaceholder:'搜索',
	  	nSelectedText:'项被选中',
	  	includeSelectAllOption:true,
	  	selectAllText:'全选/取消全选',
	  	allSelectedText:'已选中所有平台类型',
	  	maxHeight:200,
	  	maxWidth:'100%'
  	});
}
/*function initEcgi(){
  	$('#ecgi').multiselect({
	  	dropRight: true,
 	 	buttonWidth: '100%',
	  	//enableFiltering: true,
	  	nonSelectedText:'ECGI',
	  	//filterPlaceholder:'搜索',
	  	nSelectedText:'项被选中',
	  	includeSelectAllOption:true,
	  	selectAllText:'全选/取消全选',
	  	allSelectedText:'已选中所有平台类型',
	  	maxHeight:200,
	  	maxWidth:'100%'
  	});
}*/

function getEventNameandEcgi(db){
  	var url = "signalingBacktracking/getEventNameandEcgi";
  	var data={"database":db};
  	$.get(url,data,function(data){
  		if(data == "no database"){
  			alert("没有此数据库！");
  			return;
  		}
  		data = eval("("+data+")");
  		$('#eventName').multiselect('dataprovider', data.eventName);
  		//$('#ecgi').multiselect('dataprovider', data.ecgi);
  	})
}

function queryProcess(){
	$('#sectionchoose').val('false');
	$('#filterBtn').addClass("disabled");
	$('#exportBtn').addClass('disabled');
	$("#eventChoosedChange").val("true");
	doSearchEvent();
}

/*function doSearchEvent(){
	//$("#eventContent").html("");
	var task=$("#database").val();
	var params={
	 	eventName:$("#eventName").val(),

     	imsi:$("#imsi").val(),
	 	ueRef:$('#ueref').val(),
	 	enbS1apId:$("#enbs1apid").val(),
	 	mmeS1apId:$("#mmes1apid").val(),

	 	//ecgi:$("#ecgi").val(),

	 	filterSection:$("#sectionchoose").val(),
	 	ueRefChoosed:$("#ueRefChoosed").val(),
	 	db:task,
	 	type:'event'
   	};
   	
	params.viewType='table';
	//$("#eventContent").html("<table id='eventTable' style='width:500px;' fit='true'></table>");
	var url = "signalingBacktracking/getEventDataHeader";
	$.get(url,{"db":task},function(data){
		var fieldArr=new Array();
		var text=(JSON.parse(data).text).split(',');
		for(var i in text){	
			if(text[fieldArr.length]=="eventName"||text[fieldArr.length]=="eventTime"){
				fieldArr[fieldArr.length]={field:text[fieldArr.length],title:text[fieldArr.length],width:250};
			}else if(text[fieldArr.length]=="id"||text[fieldArr.length]=="direction"||text[fieldArr.length]=="contents"){
				fieldArr[fieldArr.length]={field:text[fieldArr.length],title:text[fieldArr.length],hidden:true};
			}else{
				fieldArr[fieldArr.length]={field:text[fieldArr.length],title:text[fieldArr.length],width:150};
			}
		}
		//$('#signalingTable').removeClass("hidden");
		//$('#signalingChart').addClass("hidden");
		//var newData = JSON.parse(data).rows;
		$('#signalingTable').grid('destroy', true, true);
		var grid = $("#signalingTable").grid({
		  	columns:fieldArr,
		  	//dataSource:"signalingBacktracking/getEventData",
		  	dataSource:{ 
		  		url: 'signalingBacktracking/getEventData', 
		  		data: {},
		  		success: function(data){
		  			data = eval("("+data+")");
		  			grid.render(data);

		  			if($("#sectionchoose").val()=="true"){
					 	$('#exportBtn').removeClass('disabled');
				 	}
		  		} 
		  	},
		  	params : params,
		  	pager: { limit: 10, sizes: [10, 20, 50, 100] },
		  	autoScroll:true,
		  	uiLibrary: 'bootstrap',
		  	autoLoad: true			  	
		});
		grid.on('rowSelect', function (e, $row, id, record) {
         	uechoosed(record.ueRef);
         	lastEvent = record;
     	});
     	grid.on('rowUnselect', function (e, $row, id, record) {
         	lastEvent = record;
     	});
     	$("#signalingTable").delegate("tbody tr","dblclick",function(){
     		eventMessageDetail(lastEvent.id);
     	})
     	

	})
	
}*/
$.extend($.fn.datagrid.methods, {  
     fixRownumber : function (jq) {  
         return jq.each(function () {  
             var panel = $(this).datagrid("getPanel");    
             var clone = $(".datagrid-cell-rownumber", panel).last().clone();   
             clone.css({"position" : "absolute", left : -1000 }).appendTo("body");  
             var width = clone.width("auto").width();  
             if (width > 25) {  
                 $(".datagrid-header-rownumber,.datagrid-cell-rownumber", panel).width(width + 5);  
                 $(this).datagrid("resize");    
                 clone.remove();  
                 clone = null;  
             } else {  
                 $(".datagrid-header-rownumber,.datagrid-cell-rownumber", panel).removeAttr("style");
				 $(this).datagrid("resize");  
             }  
        });  
   }  
 });

function doSearchEvent(){
	//$("#eventContent").html("");
	var task=$("#database").val();
	var params={
	 	eventName:$("#eventName").val(),

     	imsi:$("#imsi").val(),
	 	ueRef:$('#ueref').val(),
	 	enbS1apId:$("#enbs1apid").val(),
	 	mmeS1apId:$("#mmes1apid").val(),

	 	//ecgi:$("#ecgi").val(),

	 	filterSection:$("#sectionchoose").val(),
	 	ueRefChoosed:$("#ueRefChoosed").val(),
	 	db:task,
	 	type:'event'
   	};
   	
	params.viewType='table';

	$('#signalingTable').datagrid({
		url:'signalingBacktracking/getEventData',
		view:scrollview,
		rownumbers:true,
		singleSelect:true,
		autoRowHeight:false,
		pageSize:50,
		loadMsg:'',
		onClickRow:function (rowIndex, rowData) {uechoosed(rowData.ueRef);},
		onDblClickRow: function (rowIndex, rowData){eventMessageDetail(rowData.id);},
		columns:[[   
          {field:'eventName',title:'Event Name',width:250},   
          {field:'eventTime',title:'Event Time',width:180}, 
          {field:'imsi',title:'Imsi',width:100},
		  {field:'mTmsi',title:'MTmsi',width:100},
		  {field:'ueRef',title:'UE Ref',width:100},
		  {field:'enbS1apId',title:'ENBS1APId',width:100},
		  {field:'mmeS1apId',title:'MMES1APId',width:100}, 
		  {field:'ecgi',title:'ECGI',width:120}, 
		  {field:'gummei',title:'GUMMEI',width:120}
           ]],
	 	queryParams:params,
	 	onLoadSuccess : function () {
		 	$(this).datagrid("fixRownumber");
		 	if($("#sectionchoose").val()=="true"){
			 	$('#exportBtn').removeClass('disabled');
		 	}
	 	}
	});
	
}


function doSearchEvent_chart(){
	//$("#eventContent").html("");
	var task=$("#database").val();
	var params={
	 	eventName:$("#eventName").val(),

     	imsi:$("#imsi").val(),
	 	ueRef:$('#ueref').val(),
	 	enbS1apId:$("#enbs1apid").val(),
	 	mmeS1apId:$("#mmes1apid").val(),

	 	//ecgi:$("#ecgi").val(),

	 	filterSection:$("#sectionchoose").val(),
	 	ueRefChoosed:$("#ueRefChoosed").val(),
	 	db:task,
	 	type:'event'
   	};
   	
		//var url="eventData.php";
		params.viewType='flow';
		$.ajax({
			type:"post",
			url: "signalingBacktracking/getEventData",
			data:params,
			async: false,
			success: function(returnData){   
				//$('#signalingTable').addClass("hidden");
				//$('#signalingChart').removeClass("hidden");

				if(returnData=="false"){
					$("#signalingChart").html("数据库中无相应记录!");
				}else{			
					$("#signalingChart").html("");
					draw(returnData);
					if($("#sectionchoose").val()=="true"){
				 		$('#exportBtn').removeClass("disabled");
				 		$("#eventChoosedChange").val("false");
			 		}
				}
			}
		});
	 
}

function uechoosed(ueRef){
	$("#ueRefChoosed").val(ueRef);
	$('#filterBtn').removeClass("disabled");	
}

function eventMessageDetail(id){
	var task=$("#database").val();
	var data={
		"id": encodeURI(id),
		"db":encodeURI(task)
	};
	$.ajax({
		type:"get",
		url: "signalingBacktracking/showMessage",
		data:data,
		async: false,
		success: function(returnData){
			$("#message_modal").modal();
		 	$("#message").attr("src",returnData);
		}
	});		  
}

function filterProcess(){
	$("#sectionchoose").val("true");	
	doSearchEvent();
}

function exportProcess(){
	var task=$("#database").val();
	var params={
	 	eventName:$("#eventName").val(),

     	imsi:$("#imsi").val(),
	 	ueRef:$('#ueref').val(),
	 	enbS1apId:$("#enbs1apid").val(),
	 	mmeS1apId:$("#mmes1apid").val(),

	 	//ecgi:$("#ecgi").val(),

	 	filterSection:$("#sectionchoose").val(),
	 	ueRefChoosed:$("#ueRefChoosed").val(),
	 	db:task,
	 	type:'event'
   	};
   	var url = "signalingBacktracking/getAllEventData";
	$.post(url,params,function(data){
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
		var ueRef = $("#ueRefChoosed").val();
		var url = "signalingBacktracking/exportCSV";
		var data = {
			"fileContent":fileContent,
			"ueRef":ueRef
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

function changeView(type){
	if($("#eventChoosedView").val()!=type){
		$("#eventChoosedView").val(type);
		if($('#sectionchoose').val()=="true"){
			doSearchEvent_chart();
		}
	}
}

function draw(returnData){

	var obj=eval('('+returnData+')');
	var map=getMessageMap();
	var location={"UE":"130","eNB":"430","MME":"730","targeteNB":"880"};
	var width=960;
	var yinterval=60;
	obj.records = obj.rows;
	var rowlen=obj.records.length;
	var height=100+yinterval*rowlen;
	var paper = Raphael("signalingChart",width, height);　
	var yelement=50;

	paper.rect(location.eNB-38,yelement-20,76,40,3).attr("stroke","#ccc").attr("fill","#f8f8f8");
	var element=paper.text(location.eNB, yelement, "eNB");
	element.attr("font-size", '18px');

	paper.rect(location.MME-41,yelement-20,82,40,3).attr("stroke","#aaa").attr("fill","#eee");
	var element=paper.text(location.MME, yelement, "MME");
	element.attr("font-size", '18px');

	paper.rect(location.UE-32.5,yelement-20,65,40,3).attr("stroke","#aaa").attr("fill","#eee");
	var element=paper.text(location.UE, yelement, "UE");
	element.attr("font-size", '18px');

	paper.rect(location.targeteNB-66,yelement-20,132,40,3).attr("stroke","#ccc").attr("fill","#f8f8f8");
	var element=paper.text(location.targeteNB, yelement, "Target eNB");
	element.attr("font-size", '18px');
	
	var vlineeNodeb="M"+location.eNB+","+(yelement+20)+"V"+(height-10);
	var vlineMME="M"+location.MME+","+(yelement+20)+"V"+(height-10);
	var vlineUE="M"+location.UE+","+(yelement+20)+"V"+(height-10);
	var vlinetargeteNB="M"+location.targeteNB+","+(yelement+20)+"V"+(height-10);

	var line=paper.path(vlineeNodeb);
	line.attr("stroke", '#ccc');
	var line=paper.path(vlineMME);
	line.attr("stroke", '#aaa');
	var line=paper.path(vlineUE);
	line.attr("stroke", '#aaa');
	var line=paper.path(vlinetargeteNB);
	line.attr("stroke", '#ccc');

	var message= new Array();
	var box= new Array();
	for(var i=0; i<rowlen;i++){
		var typearr=obj.records[i].eventName.split('_');
		var type=typearr[0];

		//type = "X2";//test
		var id=obj.records[i].id;
		var eventName=obj.records[i].eventName;
		var direction=obj.records[i].direction;
		var eventTime=obj.records[i].eventTime;
		var ueRef=obj.records[i].ueRef;
		var ecgi=obj.records[i].ecgi;
		var y=yelement+(i+1)*yinterval;
		if(direction=="EVENT_VALUE_SENT"){
			var source=parseInt(location[map[type].source]);
			var target=parseInt(location[map[type].target]);
		}else{
			var source=parseInt(location[map[type].target]);
			var target=parseInt(location[map[type].source]);
		}
		if(target>source){
			var linetri='M'+source+','+(y-20)+'L'+(target-20)+','+(y-20)+'L'+(target-20)+','+(y-20)+'L'+(target)+','+y+'L'+(target-20)+','+(y+20)+'L'+(target-20)+','+(y+20)+'L'+source+','+(y+20)+'Z';	
		}else if(target<source){
			var linetri='M'+(target+20)+','+(y-20)+'L'+source+','+(y-20)+'L'+source+','+(y+20)+'L'+(target+20)+','+(y+20)+'L'+(target+20)+','+(y+20)+'L'+target+','+y+'L'+(target+20)+','+(y-20)+'Z';
		}else{
			var linetri='M'+(target+150)+','+(y-20)+'L'+(target+150)+','+(y+20)+'L'+(target-150)+','+(y+20)+'L'+(target-150)+','+(y-20)+'L'+(target+150)+','+(y-20)+'Z';
		}
		box[i]=paper.path(linetri);
		box[i].attr({"fill":map[type].color,"stroke":map[type].color,"opacity":"0.8","target":id,"title":ueRef});
		box[i].dblclick(function(){eventMessageDetail(this.attr('target'));});
		box[i].click(function(){uechoosed(this.attr('title'))});
		var mid=(source+target)/2;
		//var ymessage=y-10;
		var ymessage=y;
		//if(type=="INTERNAL"){
		//	ymessage=y;
		//}
		message[i]=paper.text(mid, ymessage, eventName);
		message[i].attr({"target":id,"font-size":"9","title":ueRef});
		message[i].dblclick(function(){eventMessageDetail(this.attr('target'));});
		message[i].click(function(){uechoosed(this.attr('title'))});
		//var linepath="M"+source+","+y+"H"+target;
		var time=paper.text(50, y, eventTime);
		time.attr({"font-size":"8"});
		if(type=="RRC"){
			var ueid=paper.text(mid, y+10, '(UE:'+ueRef+')');
			ueid.attr({"opacity":"0.7","font-size":"8","target":id,"title":ueRef});
			ueid.dblclick(function(){eventMessageDetail(this.attr('target'));});
			ueid.click(function(){uechoosed(this.attr('title'));});
		}
		if(type=="S1"){
			var ecgi=paper.text(mid, y+10, '(plmnId:'+ecgi+')');
			ecgi.attr({"opacity":"0.7","font-size":"8","target":id,"title":ueRef});
			ecgi.dblclick(function(){eventMessageDetail(this.attr('target'));});
			ecgi.click(function(){uechoosed(this.attr('title'));});
		}
	
	
	} 
}
function getMessageMap(){
	return {
		"RRC":{"source":"eNB","target":"UE","color":"green"},
		"S1":{"source":"eNB","target":"MME","color":"yellow"},
		"X2":{"source":"eNB","target":"targeteNB","color":"blue"},
		"INTERNAL":{"source":"eNB","target":"eNB","color":"gray"},
		"UE":{"source":"eNB","target":"eNB","color":"gray"}
	};
}

