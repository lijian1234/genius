$(document).ready(function() {
	toogle('userManage');
	// 加载用户表
	doQueryUser();
	initValidata();
})

function doQueryUser(){
 
  $.get('userManage/templateQuery', "", function(data){
	var fieldArr=new Array();
	var text=(JSON.parse(data).text).split(',');
	for(var i in JSON.parse(data).rows[0]){		  
		if(fieldArr.length == 0){
			fieldArr[fieldArr.length]={field:i,title:text[fieldArr.length],width:50};
		}else{
			fieldArr[fieldArr.length]={field:i,title:text[fieldArr.length],width:150};
		}
	  	
	} 
	fieldArr[fieldArr.length-1].hidden = true;
		var newData = JSON.parse(data).rows;
		$('#userTable').grid('destroy', true, true);
		$("#userTable").grid({
		  	columns:fieldArr,
		  	dataSource:newData,
		  	pager: { limit: 10, sizes: [10, 20, 50, 100] },
		  	autoScroll:true,
		  	uiLibrary: 'bootstrap',
		  	primaryKey : 'id'
		});
  });
}
function deleteUser(){
	var data = getSelected();
	if(!data){
		alert("请选择需要删除的用户。");
		return;
	}
	var flag = confirm("确认删除id为"+data.id+"的用户吗？");
	if(flag){
		$.get('userManage/deleteUser',{"id":data.id},function(res){
			if(res){
				alert("删除成功。");
				doQueryUser();
			}
		})
	}
	
	
}
function addUser(){
	$("#add_edit_user").modal();
	$("form input").val("");
	$("#saveBtn").html("新增");

	$("#userForm").data('bootstrapValidator').destroy();
	initValidata();
}

function updateUser(){

	$("#userForm").data('bootstrapValidator').validate();
	var flag = $("#userForm").data('bootstrapValidator').isValid();
	if(!flag){
		return;
	}
	
	var params = $("#userForm").serialize().split("&");
	var data = {};
	for(var i = 0;i<params.length;i++){
		data[params[i].split("=")[0]] = decodeURIComponent(params[i].split("=")[1],true);
	}
	$.get('userManage/updateUser',data,function(res){
			
		$("#add_edit_user").modal('hide');
		doQueryUser();
	})
	
}
function editUser(){
	var data = getSelected();
	if(!data){
		alert("请选择需要修改的用户。");
		return;
	}
	$("#add_edit_user").modal();
	$("form input").val("");
	$("#saveBtn").html("更新");

	$("#userId").val(data.id);
	$("#userName").val(data.user);
	$("#password").val(data.pwd);
	$("#type").val(data.type);
	$("#email").val(data.email);

	$("#userForm").data('bootstrapValidator').destroy();
	initValidata();

}

function initValidata(){
	$('#userForm').bootstrapValidator({
	　　message: 'This value is not valid',
    	feedbackIcons: {
        　　　　　　　　valid: 'glyphicon glyphicon-ok',
        　　　　　　　　invalid: 'glyphicon glyphicon-remove',
        　　　　　　　　validating: 'glyphicon glyphicon-refresh'
    　　　　　　　　   },
	    fields: {
	        userName: {
	            //message: '用户名验证失败',
	            validators: {
	                notEmpty: {
	                    message: '用户名不能为空'
	                }
	            }
	        },
	        password: {
	            //message: '密码验证失败',
	            validators: {
	                notEmpty: {
	                    message: '密码不能为空'
	                }
	            }
	        },
	        type: {
	            //message: '用户类型验证失败',
	            validators: {
	                notEmpty: {
	                    message: '用户类型不能为空'
	                }
	            }
	        },
	        email: {
	            validators: {
	                notEmpty: {
	                    message: '邮箱地址不能为空'
	                },
	                 emailAddress: {
                        message: '邮箱地址格式有误'
                    }

	            }
	        }
	    }
	});
}

function getSelected(){
	var id = $("#userTable").find("tr.active").children("td").eq(0).children("div").html();
	var data = $('#userTable').grid('getById',id);
	return data;
	
}