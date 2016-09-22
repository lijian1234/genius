var parameterAnalysisDateId = '#parameterAnalysisDate';
var parameterAnalysisCityId = '#parameterAnalysisCity';
var paramQueryMoTreeId = '#paramQueryMoTree';
var paramQueryMoTreeData = '';

$(document).ready(function() {
//--start of parameterAnalysisDate init--
  $(parameterAnalysisDateId).select2();
  var url = "paramQuery/getParamTasks";
  $.ajax({
  type:"GET",
  url:url,
  dataType:"json",
  success:function(data){
  if(data.length == 1 && data[0] == 'login'){
  window.location.href = 'login';
  }
  var newOptions = new Array();
  var obj = new Object();
  $(data).each(function(k,v){
  var v = eval("("+v+")");
  var i = 0;
  obj = {
    id: v["text"],
    text : v["text"]
  };
    newOptions.push(obj);
  })
  var parameterAnalysisDateSelect = $(parameterAnalysisDateId).select2({
  height:50,
  placeholder: "请选择日期",
  //allowClear: true,
  data:newOptions
  });
  //var value = $(parameterAnalysisDateId).val();
  var task = getCurrentDate('kget');
  $(parameterAnalysisDateId).val(getCurrentDate('kget')).trigger('change');
  if($(parameterAnalysisDateId).val() == null){
  $(parameterAnalysisDateId).val(getYesterdayDate('kget')).trigger('change');
  }
   }
  });
//--end of parameterAnalysisDate init--
  $(parameterAnalysisCityId).multiselect({
    dropRight: true,
    buttonWidth: 230,
    //enableFiltering: true,
    nonSelectedText:'请选择城市',
    //filterPlaceholder:'搜索',
    nSelectedText:'项被选中',
    includeSelectAllOption:true,
    selectAllText:'全选/取消全选',
    allSelectedText:'已选中所有平台类型',
    maxHeight:200,
    width:220
  });
  var url = "paramQuery/getParamCitys";
  $.ajax({
  type:"GET",
  url:url,
  dataType:"json",
  success:function(data){
    var newOptions = new Array();
    var obj = new Object();
    $(data).each(function(k,v){
    var v = eval("("+v+")");
    obj = {
        label : v["text"],
        value : v["value"]
      };
    newOptions.push(obj);
    });
    $(parameterAnalysisCityId).multiselect('dataprovider', newOptions);
  }
  });

//--end of parameterAnalysisCity init--

//---------start of paramTree---------

  $.get('common/json/parameterTreeData.json',null,function(data){
  paramQueryMoTreeData =eval("("+data+")");
  var options = {
  bootstrap2: false, 
  showTags: true,
  levels: 2,
  data:paramQueryMoTreeData,
  onNodeSelected: function(event, data) {
  paramQuerySearch();
   }
  };

  $('#paramQueryMoTree').treeview(options);  
  });
//---------end of paramTree---------
})
//-----start of moTreeView--------
//根据关键字搜索树
function search(treeId,erbId){
  searchParamMoTree(treeId,erbId);
}
function clearSearch(treeId,erbId){
  clearParamMoTree(treeId,erbId);
}
function searchParamMoTree(treeId,erbId){
   var pattern = $('#'+erbId).val();
  
   $('#'+treeId).on('searchComplete', function(event, data) {
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
  paramQuerySearch();
   }
  };

  $('#'+treeId).treeview(options);
  });
   $('#'+treeId).treeview('search', [ pattern, {
  ignoreCase: true,   // case insensitive
  exactMatch: false,  // like or equals
  revealResults: true,  // reveal matching nodes
  }]);

}
//清空搜索历史
function clearParamMoTree(treeId,erbId){
  $('#'+treeId).treeview('clearSearch');
  var options = {
  bootstrap2: false, 
  showTags: true,
  levels: 2,
  data:paramQueryMoTreeData,
  onNodeSelected: function(event, data) {
  paramQuerySearch();
   }
  };

  $('#'+treeId).treeview(options);
  $('#'+erbId).val('') ; 
}
//-----end of moTreeView--------
 //--------start of tableSearch-----
function paramQuerySearch(){
  var params = getParam('paramQuery');
  parameterSearch(params);
}
function paramQueryExport(){
  var params = getParam('paramQuery');
  parameterExport(params);
}
function getParam(action)
{
  if(action == 'paramQuery'){
  var task = $(parameterAnalysisDateId).val();
  var moSelected = $(paramQueryMoTreeId).treeview('getSelected');
  if(moSelected == ''){
  alert("Please choose parameter tree first!");
  return false;
  }
  var mo = moSelected[0].text;
  var citys = $(parameterAnalysisCityId).val();
  var erbs = $('#paramQueryErbs').val();
  if(task != null){
  var params={
  db:task,
  table:mo,
  erbs:erbs,
  citys:citys
  };
  return params;
  }else{
  alert("Please choose database first!");
  return false;
  }
  }
}
var table = null;
function parameterSearch(params)
{
  var l = Ladda.create( document.getElementById( 'search' ) );
  var E = Ladda.create( document.getElementById( 'export' ) );
  l.start();  
  E.start();
  if(params == false){return false;}
  var fieldArr=new Array(); 
    $.get('paramQuery/getParamTableField',params,function(data){ 
      var newDataLength = {};
      for(var k in data){
      if(k != 'id'){
        var paraDataLength = 0;
        var widthData = 0;
        if (data[k]) {
          paraDataLength = data[k].length;
          widthData = paraDataLength * 15;
        }
        var paraNameLength = k.length;
        var widthName = paraNameLength * 12;
        if (k == 'mo') {
        widthData = paraDataLength * 10;
      };
  var width;
  if (widthData > 300 && k !='mo') {
    width = 300;
    newDataLength[k] = 25;
  }else{
    width = widthName > widthData ? widthName : widthData;
    if (paraDataLength == 0) {
    newDataLength[k] = paraNameLength;
    }else{
    newDataLength[k] = width;
    }
  }
  fieldArr[fieldArr.length]={field:k,title:k,width:width};
  }
  }
  params['paramLength'] = newDataLength;
  $('#paramQueryTable').grid('destroy', true, true);
  $("#paramQueryTable").grid({
  columns:fieldArr,
  dataSource: { url: 'paramQuery/getParamItems',type:'post', data: params},
  primaryKey: 'id',
  pager: { limit: 10, sizes: [10, 20, 50, 100] },
  autoScroll:true,
  uiLibrary: 'bootstrap',
  });
  l.stop();
  E.stop();
  });
  }
  function parameterExport(params){
    var l = Ladda.create( document.getElementById( 'search' ) );
    var E = Ladda.create( document.getElementById( 'export' ) );
    l.start();  
    E.start();
    $.get('paramQuery/exportParamFile',params,function(data){
      l.stop();
      E.stop();
      var data = eval('('+data+')');
      if(data.result=='true'){
        var filepath = data.filename.replace('\\','');
        download(filepath,'','data:text/csv;charset=utf-8');
      }else{
        alert("There is error occured!");
      }
    }); 
  }
 //--------end of tableSearch-------
 //-------------------------------common-----------------------------------
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