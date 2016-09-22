$(document).ready(function() {
	toogle('noticeManage');
	// 加载用户表
	doQueryNotice();
})

function doQueryNotice(){
 
  $.get('noticeManage/getNotice', "", function(data){
	var fieldArr=new Array();
	var text=(JSON.parse(data).text).split(',');
	for(var i in JSON.parse(data).rows[0]){		  
		if(fieldArr.length == 0){
			fieldArr[fieldArr.length]={field:text[fieldArr.length],title:text[fieldArr.length],width:30};
		}else if(text[fieldArr.length]=="content"){
			fieldArr[fieldArr.length]={field:text[fieldArr.length],title:text[fieldArr.length],width:450};
		}else{
			fieldArr[fieldArr.length]={field:text[fieldArr.length],title:text[fieldArr.length],width:150};
		}
	  	
	} 
	var newData = JSON.parse(data).rows;
	$('#noticeTable').grid('destroy', true, true);
	$("#noticeTable").grid({
	  	columns:fieldArr,
	  	dataSource:newData,
	  	pager: { limit: 10, sizes: [10, 20, 50, 100] },
	  	autoScroll:true,
	  	uiLibrary: 'bootstrap',
	  	primaryKey : 'id'
	});
  });
}
function deleteNotice_man(){
	var data = getSelected();
	if(!data){
		alert("请选择需要删除的通知。");
		return;
	}
	var flag = confirm("确认删除id为"+data.id+"的通知吗？");
	if(flag){
		$.get('noticeManage/deleteNotice',{"id":data.id},function(res){
			if(res){
				alert("删除成功。");
				doQueryNotice();
				initNotice();
			}
		})
	}
	
	
}
function addNotice_man(){
	$("#add_notice").modal();
}
function editNotice_man(){
	var data = getSelected();
	if(!data){
		alert("请选择需要修改的通知。");
		return;
	}
	$("#add_notice").modal();

	$("#noticeId").val(data.id);
	$("#noticeTitle").val(data.title);
	$("#noticeContent").val(data.content);
}

function getSelected(){
	var id = $("#noticeTable").find("tr.active").children("td").eq(0).children("div").html();
	var data = $('#noticeTable').grid('getById',id);
	return data;
	
}