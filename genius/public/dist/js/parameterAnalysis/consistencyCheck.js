var parameterAnalysisDateId = '#parameterAnalysisDate';
var consistencyTreeId = "#consistencyTree";
var parameterAnalysisCityId = '#parameterAnalysisCity';
var tableId = "#consistencyCheckDetailTable";
$(function(){
  toogle('consistencyCheck');
  //-------start of date init----------------
  $(parameterAnalysisDateId).on('change',function(node){
    consistencyCheckSearch();
  })
  //-------end of date init------------------
  //-------start of init consistencyTree-----
  var url = "common/json/consistencyTreeData.json";
  $.get(url,null,function(data){
  var consistencyTreeData =eval("("+data+")");
  var options = {
  bootstrap2: false, 
  showTags: true,
  levels: 2,
  data:consistencyTreeData,
  onNodeSelected: function(event, data) {
    consistencyCheckSearch();
   }
  };

  $(consistencyTreeId).treeview(options);
  $(consistencyTreeId).treeview('selectNode', [ 0, { silent: true } ]);
  });
  //-------end of init consistencyTree-----
})

function consistencyCheckSearch(){
  $(tableId).grid('destroy', true, true);
  var params = getParams();
  if(params == false){
  return false;
  }
  var url = "consistencyCheck/consistencyCheckDistribute";
  chart_column(url,params,'#chart-consistency')
}
var chart_column = function(route,params,block) {
  $.ajax({
  type : "GET",
  url : route,
  data : params,
  dataType : "json",
  beforeSend : function () {
  $(block).html('<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">')
  },
  success: function(data) {
  $(block).html('');
  $(block).highcharts({
  chart: {
  type: 'column'
  },
  title: {
  text: "数量分布"
  },
  subtitle: {
  text: null
  },
  xAxis: {
  categories: data['category'],
  crosshair: true
  },
  yAxis: {
  min: 0,
  title: {
  text: null
  }
  },
  tooltip: {
  shared: true,
  useHTML: true
  },
  plotOptions: {
  column: {
  pointPadding: 0.2,
  borderWidth: 0
  }
  },
  legend: {
  enabled: false
  },
  credits: {
  enabled: false,
  },
  series: data['series']
  });
  }
  })
}
function consistencyCheckDetailsSearch(){
  var params = getParams();
  if(params == false){return false;};
  var citys = $(parameterAnalysisCityId).val();
  params['citys'] = citys;
  var fieldArr=new Array(); 
  $.get('consistencyCheck/getTableField',params,function(data){
    $(tableId).grid('destroy', true, true);
    if (data['result'] == 'error') {
      alert('没有记录');
      return;
    }else{
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
      var widthName = paraNameLength * 13;
       
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
      $(tableId).grid('destroy', true, true);
      $(tableId).grid({
      columns:fieldArr,
      dataSource: { url: 'consistencyCheck/getItems',type:'post', data: params},
      //primaryKey: 'id',
      pager: { limit: 10, sizes: [10, 20, 50, 100] },
      autoScroll:true,
      uiLibrary: 'bootstrap',
      });
    }
  });
}
function consistencyCheckExportTofile(){
  var params = getParams();
  if(params == false){return false;};
  var citys = $(parameterAnalysisCityId).val();
  params['citys'] = citys;
  $.get('consistencyCheck/exportFile',params,function(data){
  if(data.result=='true')
  {
  var filepath = data.filename.replace('\\','');
  download(filepath,'','data:text/csv;charset=utf-8');
  }
  else
  {
  alert("没有记录");
  }
  }); 
}
function getParams(){
  var task = $(parameterAnalysisDateId).val();
  if(task == null){
  task = getYesterdayDate('kget')
  $(parameterAnalysisDateId).val(task).trigger('change');
  }
  var treeSelected = $(consistencyTreeId).treeview('getSelected');
  if(treeSelected == ''){
  alert("Please choose parameter tree first!");
  return false;
  }
  var type = treeSelected[0].value;
  if(type == ''){
  alert("请选择相应的检查类型");
  return false;
  }
  var table = '';
  if (type == 'rypd') {
  table = 'TempEUtranCellFreqRelation';
  }else if(type == 'dxlq'){
  table = 'TempEUtranCellRelationUnidirectionalNeighborCell';

  }else if(type == 'ylqwx2'){
  table = 'TempEUtranCellRelationExistNeighborCellWithoutX2';
  }else if(type == 'lqgd'){
  table = 'TempEUtranCellRelationManyNeighborCell';
  }else if(type== 'lqgs'){
  table = 'TempEUtranCellRelationFewNeighborCell';
  }else if(type== 'activePlmnList'){
  table = 'TempExternalEUtranCellTDDActivePlmnListCheck';
  }else if(type== 'pciyjct'){
  table = 'TempEUtranCellRelationNeighOfPci';
  }else if(type== 'pciejct'){
  table = 'TempEUtranCellRelationNeighOfNeighPci';
  }else if(type== '2Glq'){
  table = 'TempGeranCellRelation2GNeighbor';
  }else if(type== '2Gwbcs'){
  table = 'TempParameter2GKgetCompare';
  }else if(type== '4Gwblqdy'){
  table = 'TempExternalNeigh4G';
  }else if(type== 'a1a2'){
  table = 'TempParameterQCI_A1A2';
  }else if(type== 'b2a2critical'){
  table = 'TempParameterQCI_B2A2critical';
  }else if(type== 'x2Ip'){
  table = 'TempTermPointToENB_ENBID_ipAddress';
  }else if(type== 'x2userIp'){
  table = 'TempTermPointToENB_ENBID_usedIpAddress';
  }else if(type== 'x2eNBId'){
  table = 'TempTermPointToENB_IP';
  }else if(type== 'x2status'){
  table = 'TempTermPointToENB_X2Status';
  }else if(type== 'S1-MMEGI-dif'){
  table = 'TempTermPointToMme_S1_MMEGI_dif';
  }else if(type== 'S1-MMEGI-unnecessary'){
  table = 'TempTermPointToMme_S1_MMEGI_unnecessary';
  }else if(type== 'S1-UsedIP'){
  table = 'TempTermPointToMme_S1_UsedIP';
  }else if(type== 'MMEGI-Tac'){
  table = 'TempTermPointToMme_S1_MMEGI_Tac';
  }else if(type== 'Tac-MMEGI'){
  table = 'TempTermPointToMme_S1_Tac_MMEGI';
  }else if(type== 'tacCell'){
  table = 'TempParameterTAC_EUtranCellTDD';
  }else if(type== 'tacDir'){
  table = 'TempParameter_TAC_AZ_DIS_INf';
  }else if(type== 'tacMRO'){
  table = 'TempParameter_TAC_MRO';
  }else if(type== 'tacMRE'){
  table = 'TempParameter_TAC_MRE';
  }else if(type== 'a5Threshold1Rsrp'){
  table = 'TempA5Threshold1Rsrp';
  }else if(type== 'a5Threshold2Rsrp'){
  table = 'TempA5Threshold2Rsrp';
  }else if(type== 'b2Threshold1RsrpGeran'){
  table = 'TempB2Threshold1RsrpGeranOffset';
  }else if(type== 'b2Threshold2Geran'){
  table = 'TempB2Threshold2GeranOffset';
  }else if(type== 'GeranFrequency_1'){
  table = 'TempGeranFrequency_1_check';
  }
  var params = {
  db:task,
  table:table
  }
  if (table == '') {
  params = getParams();
  }
  return params;
}