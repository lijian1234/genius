$(function(){

<!-- For all the post method --> 
	$.ajaxSetup({  
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}  
    }); 


    $.get("nav/getUser",null,function(data){

    	if(data == "login"){
            window.location.href = 'login';
    		alert("尚未登录");
    	}else{
    		data = eval("("+data+")");
    		$("#user_user").html(data.user);
    		$("#user_type").html(data.type);
    		$("#user_email").html(data.email);
            //如果不是管理员，系统管理中的权限管理不显示
            if(data.type !== "admin"){
                $("#sys_container").css("width","250px");
                $("#sys_container equal-height-in").removeClass("col-sm-6").addClass("col-sm-12");
                $("#adminOnly").hide();
            }
            initNotice();
    	}
    })
     //统计每小时登录情况
    updateOlineNumber();
})

function signout(){
	$.get("nav/signout",null,function(data){

    	if(data == "success"){
    		window.location.href = 'login';
    	}
    })
}
function updateOlineNumber(){
    var date = new Date();
    var year = date.getFullYear();
    var mon = date.getMonth()+1;
    var day = date.getDate();
    var hour = date.getHours();    
    var min = date.getMinutes();     
    var sec = date.getSeconds(); 
    var params = {
        year:year,
        mon:mon,
        day:day,
        hour:hour
    }
    $.ajax({
        type:"get",
        url: "nav/getSessions",
        data:params,
        async: true,
        success: function(returnData){  
        }
    });
}
function addNotice(){
    $("#add_notice").modal();
}
function updateNotice(){
    var id = $("#noticeId").val();
    var title = $("#noticeTitle").val().trim();
    var content = $("#noticeContent").val().trim();
    if(!title){
        alert("标题不能为空！");
        return;
    }
    if(!content){
        alert("内容不能为空！");
        return;
    }
    var params = {
        id:id,
        title:title,
        content:content
    };
    var url = "nav/addNotice";
    $.post(url,params,function(data){
        if(data=="1"){
            alert("添加通知成功");
            $("#add_notice").modal('hide');
            $("#noticeTitle").val("");
            $("#noticeContent").val("");
            $("#noticeId").val("");
            initNotice();
        }
    })
}
function initNotice(){
    var url = "nav/getNotice";
    $.get(url,null,function(data){
        data = eval("("+data+")");
        var html = "";
        var ids = [];
        for(var i in data){
            html += "<li><a id='"+data[i].id+"' data-time='"+data[i].publishTime+"' data-publisher='"+data[i].publisher+"' data-content='"+data[i].content+"' onclick='readNotice(this)'>"+data[i].title+"</a></li>";
            ids.push(data[i].id);
        }

        $("#noticeUl").empty().append(html);
        if(data.length){
            $("#noticeNumber").html(data.length);
        }else{
            $("#noticeNumber").html("");
        }
        
        $("#noticeIds").val(ids.join(","));
    })
    if($("#noticeTable").length){
        doQueryNotice();
    }
}

function readNotice(notice){
    $("#read_notice").modal();
    $("#noticeId_read").val($(notice).attr("id"));
    $("#noticeTitle_read").html($(notice).html());
    $("#noticePublisher").html($(notice).attr("data-publisher"));
    $("#noticePublishTime").html($(notice).attr("data-time"));
    $("#noticeContent_read").html($(notice).attr("data-content"));

}
function setNoticeReaded(){
    var id = $("#noticeId_read").val();
    var url="nav/readNotice";
    $.get(url,{id:id},function(data){
        $("#read_notice").modal('hide');
        initNotice();
    })
}
function readAll(){
    var ids = $("#noticeIds").val();
    var url = "nav/readAllNotice";
    $.post(url,{ids:ids},function(data){
        initNotice();
        $("#noticeIds").val("");
    })
}