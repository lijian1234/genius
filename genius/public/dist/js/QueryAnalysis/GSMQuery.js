var GSMQueryTreeData = '';
var GSMQueryMoTreeId = '#GSMQueryMoTree';
$(document).ready(function() {
//     var nowTemp = new Date();
// $("#startTime").datepicker('setValue', nowTemp);
//设置日期
  setTime(); 
//数据库获取所有城市
  getAllCity();
  //设置输入框状态
  setInputStatus();
   //设置小时/15分钟选择
  setHQSelect();
   //设置树
  setTree();
   $('#GSMQueryMoTree').treeview('collapseAll', { silent: true });
 //设置表格
  setTable();

  toogle('GSMQuery');
})

  function setInputStatus(){
  //区域维度初始值设置
  $("#locationDim").val('city');
  $('#cellInput').attr('disabled', 'true');
  $('#erbsInput').attr('disabled', 'true');
  $('#cellInput').val('');
  $('#erbsInput').val('');
  $('#locationDim').change(function(){
    if($('#locationDim').val() == 'cell' || $('#locationDim').val() == 'cellGroup'){
      $('#cellInput').removeAttr('disabled');
      $('#erbsInput').attr('disabled', 'true');
    }else if($('#locationDim').val() == 'erbs'){
      $('#erbsInput').removeAttr('disabled');
      $('#cellInput').attr('disabled', 'true');
      $('#cellInput').val('');
    }else{
      $('#cellInput').attr('disabled', 'true');
      $('#erbsInput').attr('disabled', 'true');
      $('#cellInput').val('');
      $('#erbsInput').val('');
    }
  });

  //时间维度初始值设置
  $('#timeDim').val('day');
  $('#hourSelect').attr('disabled', 'disabled');
  $('#quarterSelect').attr('disabled', 'disabled');
  $('#timeDim').change(function(){
    if($('#timeDim').val() == 'hour' || $('#timeDim').val() == 'hourgroup'){
      $("#quarterSelect").multiselect("disable");
      $("#hourSelect").multiselect("enable");
    }else if($('#timeDim').val() == 'quarter'){
      $("#quarterSelect").multiselect("enable");
      $("#hourSelect").multiselect("enable");
    }else{
      $("#hourSelect").multiselect("disable");
      $("#quarterSelect").multiselect("disable");
    }
  });
}

//---------start of paramTree---------

//---------end of paramTree---------
function setTree(){
  var tree = '#GSMQueryMoTree';
  $(tree).treeview({data: getTree()}); //树
}

function getTree() {
  var url = "GSMQuery/getGSMTreeData";
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
 
//-------设置日期------//
function setTime(){
  $("#startTime").datepicker({format: 'yyyy-mm-dd'});  //返回日期
  $("#endTime").datepicker({format: 'yyyy-mm-dd'});

  var nowTemp = new Date();
  $("#startTime").datepicker('setValue', nowTemp);
  $("#endTime").datepicker('setValue', nowTemp);
  //alert(nowTemp);
  var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
  var checkin = $('#startTime').datepicker({
    onRender: function(date) {
      return date.valueOf() < now.valueOf() ? '' : '';
    }
  }).on('changeDate', function(ev) {
    if (ev.date.valueOf() > checkout.date.valueOf()) {
      var newDate = new Date(ev.date)
      newDate.setDate(newDate.getDate() + 1);
      checkout.setValue(newDate);
    }
    checkin.hide();
    $('#endTime')[0].focus();
    }).data('datepicker');
      var checkout = $('#endTime').datepicker({
      onRender: function(date) {
        //return date.valueOf() <= checkin.date.valueOf() ? 'disabled' : '';
        return date.valueOf() <= checkin.date.valueOf() ? '' : '';
      }
    }).on('changeDate', function(ev) {
      checkout.hide();
    }).data('datepicker');
}

//----------获得城市----------//

function getAllCity(){
  $('#allCity').multiselect({
      dropRight: true,
      buttonWidth: '100%',
      //enableFiltering: true,
      nonSelectedText:'请选择城市',
      //filterPlaceholder:'搜索',
      nSelectedText:'项被选中',
      includeSelectAllOption:true,
      selectAllText:'全选/取消全选',
      allSelectedText:'已选中所有平台类型',
      maxHeight:200,
      maxWidth:'100%'
  });
  var url = "GSMQuery/getAllCity";
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
      $('#allCity').multiselect('dataprovider', newOptions);
    }
  });
}
//设置小时和15分钟的状态
function setHQSelect(){
  $('#hourSelect').multiselect({
      buttonWidth: '100%',
      enableFiltering: true,
      nonSelectedText:'请选择小时',
      filterPlaceholder:'搜索',
      nSelectedText:'项被选中',
      includeSelectAllOption:true,
      selectAllText:'全选/取消全选',
      allSelectedText:'已选中所有小时',
      maxHeight:200,
      maxWidth:'100%'
  });

  $('#quarterSelect').multiselect({
      dropRight: true,
      buttonWidth: '100%',
      //enableFiltering: true,
      nonSelectedText:'请选择15分钟',
      //filterPlaceholder:'搜索',
      nSelectedText:'项被选中',
      includeSelectAllOption:true,
      selectAllText:'全选/取消全选',
      allSelectedText:'已选中所有',
      maxHeight:200,
      maxWidth:'100%'
  });
}

function getChooseCitys(){
  var citys = $('#allCity').val();
  return citys;
}

function getParams(table){
  var moTree      = $('#GSMQueryMoTree').val();
  var locationDim = $('#locationDim').val();
  var timeDim     = $('#timeDim').val();
  var startTime   = $('#startTime').val();
  var endTime     = $('#endTime').val();
  var citys       = $('#allCity').val();
  if(citys == null){
    alert("Please choose city first!");
    return false;
  }
  var GSMTree = $('#GSMQueryMoTree').treeview('getSelected');
  if(GSMTree == ''){
    alert("Please choose parameter tree first!");
    return false;
  }
  var moTree= GSMTree[0].text;
  var hour=$('#hourSelect').val();
  var min = $('#quarterSelect').val();
  var cell= $('#cellInput').val();
  var erbs= $('#erbsInput').val();
  //alert(moTree);
  var params = {
    template:moTree,
    locationDim:locationDim,
    timeDim:timeDim,
    startTime:startTime,
    endTime:endTime,
    hour:JSON.stringify(hour),//hour,
    //hour:hour,
    minute:JSON.stringify(min),//min,
    city:JSON.stringify(citys),//citys,
    //subNet:JSON.stringify(subNetworks),//subNetworks,
    cell:cell,
    erbs:erbs
    //action:action 
  };
  //alert(params)
  return params;
}
//查询
function doSearchGSM(table){
  var l = Ladda.create( document.getElementById( 'search' ) );
  var S = Ladda.create( document.getElementById( 'save' ) );
  var E = Ladda.create( document.getElementById( 'export' ) );
  l.start();
  S.start();
  E.start();
  var params = getParams(table);
  if(params == false){
    l.stop();
    S.stop();
    E.stop();
    return false;
  }
  $.post('GSMQuery/templateQuery', params, function(data){
    $('#GSMQueryFile').val(JSON.parse(data).filename);
    var fieldArr=new Array();
    var text=(JSON.parse(data).text).split(',');
   for(var i in JSON.parse(data).records[0]){  
          var textLength =text[fieldArr.length].length;
          var width = textLength * 15;
           if(text[fieldArr.length] == 'day'){
            width=textLength *30;
          }
        
          fieldArr[fieldArr.length]={field:i,title:text[fieldArr.length],width:width};
          
    }
    var newData = JSON.parse(data).records;

    $('#GSMQueryTable').grid('destroy', true, true);
    $("#GSMQueryTable").grid({
      columns:fieldArr,
      dataSource:newData,
      pager: { limit: 10, sizes: [10, 20, 50, 100] },
      autoScroll:true,
      uiLibrary: 'bootstrap'
    });
    if(table == 'file') {
      alert(JSON.parse(data).filename);
      download(JSON.parse(data).filename);
    }
    l.stop();
    S.stop();
    E.stop();
  });
}
function fileSave(table) {
  var fileName=$("#GSMQueryFile").val();
  alert(fileName);
  if(fileName!='')
  {
    download(fileName);
  }
  else
  {
    alert('No file generated so far!');
  }
}
function setTable(){
  // $("#LTEQueryTable").bootgrid({   //表格
  //       ajax: true,
  //       post: function ()
  //       {
  //           // To accumulate custom parameter with the request object 
  //           return {
  //               id: "b0df282a-0d67-40e5-8558-c9e93b7befed"
  //           };
  //       },
  //       url: "common/json/test.json"/*,
  //       formatters: {
  //           "link": function(column, row)
  //           {
  //               return "<a href=\"#\">" + column.id + ": " + row.id + "</a>";
  //           }
  //       }*/
  //   });
}
//搜索模板树
function searchGSMQuery() {
  var inputData = $('#paramQueryMoErbs').val();
  inputData = $.trim(inputData);
  if(inputData == '') {
    setTree();
    return;
  }
  var params = {
    inputData : inputData
  };
  var url = "GSMQuery/searchGSMTreeData";
  //var treeData;

  $.get("GSMQuery/searchGSMTreeData",params,function(data){
    data = "["+data+"]";
    var tree = '#GSMQueryMoTree';
    $(tree).treeview({data: data});
  });
}
//清除模板树
function clearGSMQuery(){
  $('#paramQueryMoErbs').val('');
  setTree();
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