$(function(){
	toogle('paramsManage');

	setTree();
  	$('#baselineManageTree').treeview('collapseAll', { silent: true });


  	getBaselineManageTable();

  	initValidata_mode();

});

function setTree(){
	var tree = '#baselineManageTree';
	$(tree).treeview({
		data: getTree(),
	  	onNodeSelected: function(event, data) {
	      	if(data.id){
	      		$("#templateId").val(data.id);
				$("#templateName").val(data.text);
	      		getBaselineManageTable(data.id);
	      	}
	      	
	   	}
	}); //树
}

function getTree() {
  var url = "paramsManage/getBaselineTreeData";
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

//清空模板树
function clearBaselineManageQuery(){
  $('#baselineManageQuery').val('');
  setTree();
  $('#baselineManageTree').treeview('collapseAll', { silent: true });
}

//筛选模板树
function searchBaselineManageQuery() {
  var inputData = $('#baselineManageQuery').val();
  inputData = $.trim(inputData);
  if(inputData === '') {
	setTree();
	return;
  }
  var params = {
	inputData : inputData
  };
  var url = "paramsManage/searchBaselineTreeData";
  //var treeData;

  	$.get(url,params,function(data){
		//data = "["+data+"]";
		var tree = '#baselineManageTree';
		$(tree).treeview({
			data: data,
			onNodeSelected: function(event, data) {
				if(data.id){
					$("#templateId").val(data.id);
					$("#templateName").val(data.text);
		      		getBaselineManageTable(data.id);
		      	}
	      	
	   		}
		});
		$('#baselineManageTree').treeview('collapseAll', { silent: true });
  	});
}

function getBaselineManageTable(templateId){
	var url = "paramsManage/getBaselineTableData";
	var data = {"templateId":templateId};
	$.get(url,data,function(data){

		var fieldArr=[];
		var text=(JSON.parse(data).text).split(',');
		for(var i in JSON.parse(data).rows[0]){		  
			if(text[fieldArr.length] == "id"){
				fieldArr[fieldArr.length]={field:i,title:text[fieldArr.length],hidden : true};
			}else{
				fieldArr[fieldArr.length]={field:i,title:text[fieldArr.length],width:250};
			}
		  	
		} 
		var newData = JSON.parse(data).rows;
		$('#baselineManageTable').grid('destroy', true, true);
		$("#baselineManageTable").grid({
		  	columns:fieldArr,
		  	dataSource:newData,
		  	pager: { limit: 10, sizes: [10, 20, 50, 100] },
		  	autoScroll:true,
		  	uiLibrary: 'bootstrap',
		  	primaryKey : 'id'
		});
	});
}

function exportBaselineManage(){   
	var params={
     	templateName:$('#templateName').val(),
     	templateId:$('#templateId').val(),
    };  
 	$.get('paramsManage/downloadFile',params,function(data){
   		data = eval('(' + data + ')');
        if(data.result=='true'){
      		var filepath = data.filename.replace('\\','');
      		download(filepath,'','data:text/csv;charset=utf-8');
    	}else{
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

function importBaselineManage(){
	$("#import_modal").modal();
	$("#fileImportName").val("");
	$("#fileImport").val("");
}
function toName(self){
	$("#fileImportName").val(self.value);
}
function importFile(){



	var data=getParam();
    if(data==='false'){
        return false;
    }
    $.ajaxFileUpload({
      url : 'paramsManage/uploadFile',   　
      data : data,
      fileElementId : "fileImport",           
      secureuri : false,                          
      dataType:'json',
      type: "post",                     
      success:function(data, status){ 
        $("#import_modal").modal('hide');
         //document.getElementById(table+'Loading').hidden=true;
         //$('#'+table+'Table').datagrid('reload');
     	getBaselineManageTable($("#templateId").val());
		alert("上传成功");

      },
      error:function(data, status, e){
      	alert("上传失败");
         //$.messager.alert('提示', '上传失败'+e, 'warning');
         //document.getElementById(table+'Loading').hidden=true;
      }
   });
}
function getParam(){
    var templateId = $("#templateId").val();
    if(!templateId){
        alert("请选择模板!");
        return false;
    }
    var data={templateId:templateId};
    
   	if($("#fileImport").val() === ""){
    	alert("请选择上传的文件！");
        return false;
    }
    return data;
    
}

function addMode(){
	$("#add_mode").modal();

	$("#add_mode input").val("");
	$("#add_mode textarea").val("");

	$("#modeForm").data('bootstrapValidator').destroy();
	initValidata_mode();
}
function updateMode(){
	$("#modeForm").data('bootstrapValidator').validate();
	var flag = $("#modeForm").data('bootstrapValidator').isValid();
	if(!flag){
		return;
	}

	var params = $("#modeForm").serialize().split("&");
	var data = {};
	for(var i = 0;i<params.length;i++){
		data[params[i].split("=")[0]] = decodeURIComponent(params[i].split("=")[1],true);
	}

	$.get('paramsManage/addMode',data,function(res){

		if(res == 'login'){
			alert("尚未登录，不能添加模板");
		  	window.location.href = 'login';
		  	return;
		}
		if(res){
			alert("添加成功！");
			setTree();
		}else{
			alert("添加失败!");
		}
		$("#add_mode").modal('hide');
		
	});
}


function initValidata_mode(){
	$('#modeForm').bootstrapValidator({
	　　message: 'This value is not valid',
    	feedbackIcons: {
        　　　　　　　　valid: 'glyphicon glyphicon-ok',
        　　　　　　　　invalid: 'glyphicon glyphicon-remove',
        　　　　　　　　validating: 'glyphicon glyphicon-refresh'
    　　　　　　　　   },
	    fields: {
	        modeName: {
	            //message: '用户名验证失败',
	            validators: {
	                notEmpty: {
	                    message: '模板名称不能为空'
	                }
	            }
	        }
	    }
	});
}

function deleteMode(){
	if(!$('#baselineManageTree').treeview('getSelected')[0] || !$('#baselineManageTree').treeview('getSelected')[0].id){
		alert("尚未选择模板");
		return;
	}
	var flag = confirm("确认删除该模板吗？");
	if(!flag){
		return;
	}
	var id = $('#baselineManageTree').treeview('getSelected')[0].id;
	$.get('paramsManage/deleteMode',{"id":id},function(res){

		if(res == 'login'){
			alert("尚未登录，不能删除模板");
		  	window.location.href = 'login';
		  	return;
		}
		if(res == "1"){
			alert("删除成功！");
			setTree();
		}else if(res == "2"){
			alert("删除失败！");
		}else if(res == "3"){
			alert("没有权限删除该模板！");
		}
		
	});

}