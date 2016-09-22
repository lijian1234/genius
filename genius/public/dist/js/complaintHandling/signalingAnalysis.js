$(function(){
	toogle('signalingAnalysis');
})

function queryKeyword(){
	//$("#queryBtn").attr("disabled","disabled");
	//$("#logBox").html("脚本执行中，请稍等<i class='fa fa-spinner fa-pulse'></i>");
	var query = Ladda.create(document.getElementById("queryBtn"));
	query.start();


	var keyword = $("#keyword").val();
	var url = "signalingAnalysis/queryKeyword";
	$.get(url,{"keyword":keyword},function(data){
		data = eval("("+data+")");
		$("#logBox").empty();
		if(data.state !=0){
			alert(data.state);
			
		}else{
			$("#logBox").append("<pre>"+data.contents+"</pre>");
		}
	 	
	 	//$("#queryBtn").removeAttr("disabled");
	 	query.stop();
	})
}