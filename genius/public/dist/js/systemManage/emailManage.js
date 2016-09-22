$(document).ready(function() {
	toogle('emailManage');

	setTree();

});
var emailQueryTreeData = "";
//设置左侧树
function setTree(){
  $.get('common/json/emailTreeData.json',null,function(data){
    emailQueryTreeData =eval("("+data+")");
    var options = {
      bootstrap2: false, 
      showTags: true,
      levels: 2,
      data:emailQueryTreeData,
      onNodeSelected: function(event, data) {
      	$("#emailFlag").val(data.attributes.flag);
      	doQueryEmail(data.attributes.flag);
       }
    };

    $('#EmailQueryTree').treeview(options);  
  });
}

//清空模板树
function clearEmailQuery(){
  $('#paramsQueryEmail').val('');
  setTree();
}

//筛选模板树
function searchEmailQuery(){
   var pattern = $('#paramsQueryEmail').val();
  
  	$('#EmailQueryTree').on('searchComplete', function(event, data) {
	    //alert(data);
	    var moData = [];
	    for(var i in data){
	      var obj = {
	        id : data[i].id,
	        text : data[i].text
	      };
	      moData.push(obj);
	    }
	    var options = {
	      bootstrap2: false, 
	      showTags: true,
	      levels: 2,
	      data:moData,
	      onNodeSelected: function(event, data) {
	      	var flag = data.attributes?data.attributes.flag:Number(data.id)-1;
	      	$("#emailFlag").val(flag);
       		doQueryEmail(flag);
	       }
	    };

	    $('#EmailQueryTree').treeview(options);  
  	});
   	$('#EmailQueryTree').treeview('search', [ pattern, {
	  	ignoreCase: true,   // case insensitive
	  	exactMatch: false,    // like or equals
	  	revealResults: true,  // reveal matching nodes
  	}]);

}

function doQueryEmail(flag){
	var data = {flag : flag};
	if(flag === '0'){
		data.path = "common/conf/user_email_list.conf";
	}else if(flag == 1){
		data.path = "common/conf/user_paracheck_list.conf";
	}

  	$.get('emailManage/templateQuery', data, function(data){

		var fieldArr=[];
		var text=(JSON.parse(data).text).split(',');
		for(var i in JSON.parse(data).rows[0]){		  
			fieldArr[fieldArr.length]={field:i,title:text[fieldArr.length],width:150};
		} 
		var newData = JSON.parse(data).rows;
		$('#emailTable').grid('destroy', true, true);
		$("#emailTable").grid({
		  	columns:fieldArr,
		  	dataSource:newData,
		  	pager: { limit: 10, sizes: [10, 20, 50, 100] },
		  	autoScroll:true,
		  	uiLibrary: 'bootstrap'
		});
  	});
}
//编辑文件
function editEmailFile(){
	
	var flag = $('#emailFlag').val();
	if(!flag){
		return;
	}
	$('#edit_email').modal();
 	$("#emailFileContent").val("");
	var path = '';
	if(flag === '0'){
		path = "common/conf/user_email_list.conf";
	}else if(flag == 1){
		path = "common/conf/user_paracheck_list.conf";
	}
	var data = {
		path:path
	};
	$.get('emailManage/openEmailFile',data,function(data){
       var content = $("#emailFileContent").val(data);
    },'html');
}

function saveEmailFile(){
	var flag = $('#emailFlag').val();
	var path = '';
	if(flag === '0'){
		path = "common/conf/user_email_list.conf";
	}else if(flag == 1){
		path = "common/conf/user_paracheck_list.conf";
	}
	var content = $("#emailFileContent").val();
 	var data = {
 		content:content,
 		path:path
 	};
	$.get('emailManage/saveEmailFile',data,function(data){
 		$("#edit_email").modal('hide');
 		
 		doQueryEmail(flag);

 		alert("保存成功！");
 		
	});
	
}