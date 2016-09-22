$(document).ready(function() {
	toogle('ENIQManage');
	// 加载用户表
	doQuery4G();
	doQuery2G();
	initValidata();

});

function doQuery4G(){
	//加载4GENIQ表
  $.get('ENIQManage/Query4G', "", function(data){
	var fieldArr=[];
	var text=(JSON.parse(data).text).split(',');
	for(var i in JSON.parse(data).rows[0]){		  
		if(fieldArr.length === '0'){
			fieldArr[fieldArr.length]={field:i,title:text[fieldArr.length],hidden : true};
		}else{
			if(text[fieldArr.length]=="SubNetwork" || text[fieldArr.length]=="SubNetwork Fdd"){
				fieldArr[fieldArr.length]={field:i,title:text[fieldArr.length],width:250,cssClass:'nowrap'};
			}else{
				fieldArr[fieldArr.length]={field:i,title:text[fieldArr.length],width:150};
			}

			
		}
	  	
	} 
	var newData = JSON.parse(data).rows;
	$('#4GTable').grid('destroy', true, true);
	$("#4GTable").grid({
	  	columns:fieldArr,
	  	dataSource:newData,
	  	pager: { limit: 10, sizes: [10, 20, 50, 100] },
	  	autoScroll:true,
	  	uiLibrary: 'bootstrap',
	  	primaryKey : 'id'
	});
  });
}
function doQuery2G(){
	 //加载2GENIQ表
  $.get('ENIQManage/Query2G', "", function(data){
	var fieldArr=[];
	var text=(JSON.parse(data).text).split(',');
	for(var i in JSON.parse(data).rows[0]){		  
		if(fieldArr.length === '0'){
			fieldArr[fieldArr.length]={field:i,title:text[fieldArr.length],hidden : true};
		}else{
			fieldArr[fieldArr.length]={field:i,title:text[fieldArr.length],width:140};

			
		}
	  	
	} 
	var newData = JSON.parse(data).rows;
	$('#2GTable').grid('destroy', true, true);
	$("#2GTable").grid({
	  	columns:fieldArr,
	  	dataSource:newData,
	  	pager: { limit: 10, sizes: [10, 20, 50, 100] },
	  	autoScroll:true,
	  	uiLibrary: 'bootstrap',
	  	primaryKey : 'id'
	});
  });
}
function delete4G(){
	var data = getSelected("4GTable");
	if(!data){
		alert("请选择需要删除的数据。");
		return;
	}
	var flag = confirm("确认删除吗？");
	if(flag){
		$.get('ENIQManage/deleteENIQ',{"id":data.id,"sign":"4G"},function(res){
			if(res){
				alert("删除成功。");
				doQuery4G();
			}
		});
	}
	
	
}
function delete2G(){
	var data = getSelected("2GTable");
	if(!data){
		alert("请选择需要删除的数据。");
		return;
	}
	var flag = confirm("确认删除吗？");
	if(flag){
		$.get('ENIQManage/deleteENIQ',{"id":data.id,"sign":"2G"},function(res){
			if(res){
				alert("删除成功。");
				doQuery2G();
			}
		});
	}
	
	
}
function add4G(){
	$("#add_edit_ENIQ").modal();
	$("form input").val("");
	$("form textarea").val("");
	$("form textarea").parents(".form-group").show();
	$("#saveBtn").html("新增");

	$("#ENIQSign").val("4G");
	$("#ENIQForm").data('bootstrapValidator').destroy();
	initValidata();
}
function add2G(){

	$("#add_edit_ENIQ").modal();
	$("form input").val("");
	$("form textarea").val("");
	$("form textarea").parents(".form-group").hide();
	$("#saveBtn").html("新增");

	$("#ENIQSign").val("2G");
	$("#ENIQForm").data('bootstrapValidator').destroy();
	initValidata();
}

function updateENIQ(){

	$("#ENIQForm").data('bootstrapValidator').validate();
	var flag = $("#ENIQForm").data('bootstrapValidator').isValid();
	if(!flag){
		return;
	}
	
	var params = $("#ENIQForm").serialize().split("&");
	var data = {};
	for(var i = 0;i<params.length;i++){
		data[params[i].split("=")[0]] = decodeURIComponent(params[i].split("=")[1],true);
	}
	$.get('ENIQManage/updateENIQ',data,function(res){
		$("#add_edit_ENIQ").modal('hide');
		if(res == "4G"){
			doQuery4G();
		}else if(res == "2G"){
			doQuery2G();
		}
		
	});
	
}
function edit4G(){
	var data = getSelected("4GTable");
	if(!data){
		alert("请选择需要修改的数据。");
		return;
	}

	$("#add_edit_ENIQ").modal();
	$("form input").val("");
	$("form textarea").val("");
	$("form textarea").parents(".form-group").show();
	$("#saveBtn").html("更新");

	$("#ENIQSign").val("4G");

	$("#ENIQId").val(data.id);
	$("#connName").val(data.connName);
	$("#cityChinese").val(data.cityChinese);
	$("#host").val(data.host);
	$("#port").val(data.port);
	$("#dbName").val(data.dbName);
	$("#userName").val(data.userName);
	$("#password").val(data.password);

	$("#subNetwork").val(data.subNetwork);
	$("#subNetworkFdd").val(data.subNetworkFdd);


	$("#ENIQForm").data('bootstrapValidator').destroy();
	initValidata();

}
function edit2G(){
	var data = getSelected("2GTable");
	if(!data){
		alert("请选择需要修改的数据。");
		return;
	}

	$("#add_edit_ENIQ").modal();
	$("form input").val("");
	$("form textarea").val("");
	$("form textarea").parents(".form-group").hide();
	$("#saveBtn").html("更新");

	$("#ENIQSign").val("2G");

	$("#ENIQId").val(data.id);
	$("#connName").val(data.connName);
	$("#cityChinese").val(data.cityChinese);
	$("#host").val(data.host);
	$("#port").val(data.port);
	$("#dbName").val(data.dbName);
	$("#userName").val(data.userName);
	$("#password").val(data.password);



	$("#ENIQForm").data('bootstrapValidator').destroy();
	initValidata();

}

function initValidata(){
	$('#ENIQForm').bootstrapValidator({
	　　message: 'This value is not valid',
    	feedbackIcons: {
        　　　　　　　　valid: 'glyphicon glyphicon-ok',
        　　　　　　　　invalid: 'glyphicon glyphicon-remove',
        　　　　　　　　validating: 'glyphicon glyphicon-refresh'
    　　　　　　　　   },
	    fields: {
	        connName: {
	            validators: {
	                notEmpty: {
	                    message: 'Conn Name不能为空'
	                }
	            }
	        },
	        cityChinese: {
	            validators: {
	                notEmpty: {
	                    message: 'City Chinese不能为空'
	                }
	            }
	        },
	        host: {
	            validators: {
	                notEmpty: {
	                    message: 'Host不能为空'
	                }
	            }
	        },
	        port: {
	            validators: {
	                notEmpty: {
	                    message: 'Port不能为空'
	                }
	            }
	        },
	        dbName: {
	            validators: {
	                notEmpty: {
	                    message: 'DB Name不能为空'
	                }
	            }
	        },
	        userName: {
	            validators: {
	                notEmpty: {
	                    message: 'User Name不能为空'
	                }
	            }
	        },
	        password: {
	            validators: {
	                notEmpty: {
	                    message: 'Password不能为空'
	                }
	            }
	        },
	    }
	});
}

function getSelected(table){
	var id = $("#"+table).find("tr.active").children("td").eq(0).children("div").html();
	var data = $('#'+table).grid('getById',id);
	return data;
	
}