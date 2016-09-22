$(document).ready(function() {
	toogle('dataSourceManage');
	initNode();
	initFileName();
	initTable();
});

function initNode(){
	var url = "dataSourceManage/getNode";
	$.get(url,null,function(data){
		data = eval("("+data+")");
		var html = "";
		for(var i in data){
			html += "<option value='"+data[i].value+"' data-sshUserName='"+data[i].sshUserName+"' data-sshPassword='"+data[i].sshPassword+"'>"+data[i].text+"</option>"
		}
		$("#node").append(html);

		//获取第一个节点的文件名列表
		var node = $("#node option:selected");
		getFileName(node);
		//绑定change事件
		 $("#node").on("change",function(){
		 	var node = $("#node option:selected");
			getFileName(node);
		 })
	});
}
function initFileName(){
  	$('#fileName').multiselect({
	  	//dropRight: true,
 	 	buttonWidth: '100%',
	  	//enableFiltering: true,
	  	nonSelectedText:'选择文件',
	  	//filterPlaceholder:'搜索',
	  	nSelectedText:'项被选中',
	  	includeSelectAllOption:true,
	  	selectAllText:'全选/取消全选',
	  	allSelectedText:'已选中所有平台类型',
	  	maxHeight:200,
	  	maxWidth:'100%'
  	});
}
function getFileName(node){

	var remoteIp = node.val();
  	var userName = node.attr("data-sshUserName");
  	var userPassword = node.attr("data-sshPassword");
  	var params = {
    	remoteIp:remoteIp,
	    userName:userName,
	    userPassword:userPassword
  	};

  	$("#exportBtn").removeAttr("disabled");
  	if(remoteIp == 'localhost'){
  		$("#exportBtn").attr("disabled","disabled");
  	}

  	$.get("dataSourceManage/getFileName",params,function(data){
      	var returnData = JSON.parse(data);
      	$('#fileName').multiselect('dataprovider', returnData);
  	});
}
function initTable(){
	var table= '#fileTable';
	$(table).treegrid({    
	    idField:'id',    
	    treeField:'kpiName',    
	    columns:[[    
	        {title:'Name',field:'kpiName',width:900,
	        	formatter:function(val,row){  
                   return "<input type='checkbox' onclick=show('"+row.id+"')  id='check_"+row.id+"' "+(row.checked?'checked':'')+"/>" + row.kpiName;
              	}
            },    
	        {field:'size',title:'Size',width:100}   
	    ]]
  	}); //树
}
function query(){
	//$("#queryBtn").attr("disabled","disabled");
	$("#exportBtn").attr("disabled","disabled");
	var query = Ladda.create(document.getElementById("queryBtn"));
	query.start();

	var node = $("#node option:selected");
	var remoteIp = node.val();
  	var userName = node.attr("data-sshUserName");
  	var userPassword = node.attr("data-sshPassword");
	
	var ctrDldPoint = $("#fileName").val(); //获取文件名
	var erbs = $("#baseStation").val();
	var ctrDldPoints =  node.html(); //获取节点名

	var params = {
		point:ctrDldPoint,
		erbs:erbs,
		points:ctrDldPoints,
	    remoteIp:remoteIp,
	    userName:userName,
	    userPassword:userPassword
	  };
	  $.post("dataSourceManage/ctrTreeItems",params,function(data){
	  		
	      	var returnData = JSON.parse(data);
	      	$("#fileTable").treegrid("loadData",returnData);
	      	query.stop();
			$("#exportBtn").removeAttr("disabled");
		  	
	  });
}

function show(checkid){
    var s = '#check_'+checkid;
    //alert( $(s).attr("id"));
    //alert($(s)[0].checked);
    /*选子节点*/
    var nodes = $("#fileTable").treegrid("getChildren",checkid);
    if(nodes != null){
    	for(i=0;i<nodes.length;i++){
        	$(('#check_'+nodes[i].id))[0].checked = $(s)[0].checked;     
    	}
    }
    //选上级节点
    if($(s)[0].checked == false){
        var parent = $("#fileTable").treegrid("getParent",checkid);
        if(parent != null){
        	$(('#check_'+parent.id))[0].checked  = false;
        }
       
    }else{
        var parent = $("#fileTable").treegrid("getParent",checkid);
        var flag= true;
        if(parent != null){
        	var sons = parent.children; 
        	for(i=0; i<sons.length; i++){
        		if($(('#check_'+$(s).attr("id"))).checked==false){
        			flag = false;
        			break;
        		}
        	} 
        	if(flag){
        		$(('#check_'+parent.id))[0].checked  = true;
        	}
        }
        
    }
}

function exportFile(){

	var j = 0;
	var id = 1,checkid;
	var childrenId = [];
	for(id=1; ; id++){
		var nodes = $("#fileTable").treegrid("getChildren",id);
		if(nodes != ''){
			//parentId = id;
			for(i=1; i<=nodes.length; i++){
				childrenId[j] = id + '' +i;
				j++;
			}
		}else if(nodes.length == 0){
			break;
		}
	}
	//alert(childrenId);
	var gzFile = [];
	var j = 0;
	for(i=0; i<childrenId.length; i++) {
		var checkId = 'check_' + childrenId[i];
		var status = $("#"+checkId).is(':checked');
		if(status){
			var text = $("#"+checkId).parent().text();
			gzFile[j] = text + ';';
			j++;
		}
	}
	params = {
		file : gzFile
	}
	var node = $("#node option:selected");
	var remoteIp = node.val();
  	//var userName = node.attr("data-sshUserName");
  	//var userPassword = node.attr("data-sshPassword");

	/*if(remoteIp == '10.40.57.189') { //机房ip
		remoteIp = '7.140.28.88:805';
	}else if(remoteIp == 'localhost'){
		remoteIp = '7.140.28.88:803';
	}else if(remoteIp == '10.40.48.244'){ //南通224
		remoteIp = '7.140.28.88:808';
	}else if(remoteIp == '10.40.48.245'){ //南通224
		remoteIp = '7.140.28.88:807';
	}  */
	var routeIp = "http://"+remoteIp+"/mongs_web/SystemManager/copyFiles.php?file="+gzFile;

	//chrome和firefox都适用。改成download() chrome浏览器会无法下载文件。
	//window.open("http://10.40.57.189/mongs_web/SystemManager/copyFiles.php?file="+gzFile);
	//都可以了
	window.open(routeIp);
	//window.open("http://7.140.28.88:805/mongs_web/SystemManager/copyFiles.php?file="+gzFile);

	$("#CTRLoading").css('display','none');
	return;
}
