<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Route::auth();

Route::get('/home', 'NetworkController@index');

Route::get('/network','NetworkController@index');

Route::get('/scale','NetworkScaleController@index');
Route::group(['prefix'=>'scale'],function(){
    Route::get('bscversion_type','NetworkScaleController@getBSCversionByType');
    Route::get('bscversion_city','NetworkScaleController@getBSCversionByCity');
    Route::get('bscSiteType','NetworkScaleController@getBSCSiteType');
    Route::get('bscSlave','NetworkScaleController@getBSCSlave');
    Route::get('bscCA','NetworkScaleController@getBSCCA');
    Route::get('meContextNum','NetworkScaleController@getMeContextNum');
    Route::get('cellNum','NetworkScaleController@getCellNum');
    Route::get('slaveNum','NetworkScaleController@getSlaveNum');
    Route::get('meContextNumByCity','NetworkScaleController@getMeContextNumByCity');
    Route::get('cellNumByCity','NetworkScaleController@getCellNumByCity');
    Route::get('slaveNumByCity','NetworkScaleController@getSlaveNumByCity');
    Route::get('numOnAutoKPI','NetworkScaleController@getNumOnAutoKPI');
    Route::get('numOnAutoKPIByCity','NetworkScaleController@getNumOnAutoKPIByCity');
    Route::get('rruandSlave_city','NetworkScaleController@getRRUAndSlaveByCity');
    Route::get('rruandSlave_slave','NetworkScaleController@getRRUAndSlaveBySlave');
});

Route::get('/switch','SwitchController@index');
Route::get('/switchSite','SwitchController@getSwitchSite');
Route::get('/switchData','SwitchController@getSwitchData');
Route::get('/switchDetail','SwitchController@getSwitchDetail');
Route::get('/handoverin','SwitchController@getHandOverIn');
Route::get('/handOverInDetail','SwitchController@getHandOverInDetail');

//zjj
Route::get('/paramQuery',function(){
	return view('parameterAnalysis.paramQuery');
});
Route::group(['prefix'=>'paramQuery','namespace' => 'ParameterAnalysis'],function(){
    Route::get('getParamTasks','ParamQueryController@getParamTasks');
    Route::post('getParamItems','ParamQueryController@getParamItems');
    Route::get('getParamCitys','ParamQueryController@getParamCitys');
    Route::get('getParamTableField','ParamQueryController@getParamTableField');
    Route::get('exportParamFile','ParamQueryController@exportParamFile');
});
Route::get('/consistencyCheck',function(){
	return view('parameterAnalysis.consistencyCheck');
});
Route::group(['prefix'=>'consistencyCheck','namespace'=>'ParameterAnalysis'],function(){
	Route::get('consistencyCheckDistribute','ConsistencyCheckController@getDistributeData');
	Route::get('getTableField','ConsistencyCheckController@getTableField');
	Route::post('getItems','ConsistencyCheckController@getItems');
	Route::get('exportFile','ConsistencyCheckController@exportFile');
});
Route::get('/weak',function(){
    return view('network.weak');
});
Route::group(['prefix'=>'weak'],function(){
    Route::get('baselineParamNum','WeakController@getBaselineParamNum');
    Route::get('baselineBSNum','WeakController@getBaselineBSNum');
    Route::get('consistencyParamNum','WeakController@getConsistencyParamNum');
    Route::get('consistencyBSNum','WeakController@getConsistencyBSNum');
});
Route::get('/badCellOverview','BadCellController@getBadCellData');
Route::get('/badCellOverview/drillDownDonutPie','BadCellController@getDrillDownDonutPie');
Route::get('/interfereOverview','InterfereController@getInterfereData');
Route::get('/currentAlarm','AlarmController@getCurrentAlarm');
Route::get('/currentAlarm/drillDownDonutPie','AlarmController@getDrillDownDonutPie');
Route::get('/historyAlarm','AlarmController@getHistoryAlarm');
Route::get('/historyAlarm/drillDownDonutPie','AlarmController@getHistoryDrillDownDonutPie');

//zjj

//lijian
Route::get('/LTEQuery',function(){
	return view('QueryAnalysis.LTEQuery');
});

Route::get('/LTEQuery/getLTETreeData', 'QueryAnalysis\LTEQueryController@getLTETreeData');

Route::get('/LTEQuery/getAllCity', 'QueryAnalysis\LTEQueryController@getAllCity');

Route::get('/LTEQuery/getAllSubNetwork', 'QueryAnalysis\LTEQueryController@getAllSubNetwork');

Route::get('/LTEQuery/getFormatAllSubNetwork', 'QueryAnalysis\LTEQueryController@getFormatAllSubNetwork');

Route::get('/LTEQuery/searchLTETreeData', 'QueryAnalysis\LTEQueryController@searchLTETreeData');

Route::post('/LTEQuery/templateQuery', 'QueryAnalysis\LTEQueryController@templateQuery');

Route::post('/LTEQuery/uploadFile', 'QueryAnalysis\LTEQueryController@uploadFile');

Route::get('/NBIQuery',function(){
	return view('QueryAnalysis.NBIQuery');
});

Route::get('/NBIQuery/getNbiTreeData', 'QueryAnalysis\NBIQueryController@getNbiTreeData');

Route::post('/NBIQuery/templateQuery', 'QueryAnalysis\NBIQueryController@templateQuery');

Route::get('/network',function(){
	return view('network.survey');
});

//zhangyan
Route::get('/baselineCheck',function(){
    return view('parameterAnalysis.baselineCheck');
});
Route::get('/BaselineCheck/getBaseTree', 'ParameterAnalysis\BaselineCheckController@getBaseTree');
Route::get('/BaselineCheck/getParamTasks', 'ParameterAnalysis\BaselineCheckController@getParamTasks');
Route::get('/BaselineCheck/getParamCitys', 'ParameterAnalysis\BaselineCheckController@getParamCitys');
Route::get('/BaselineCheck/getAllCity', 'ParameterAnalysis\BaselineCheckController@getAllCity');
Route::get('/BaselineCheck/getChartDataCategory', 'ParameterAnalysis\BaselineCheckController@getChartDataCategory');
Route::get('/BaselineCheck/getTableField', 'ParameterAnalysis\BaselineCheckController@getTableField');
Route::get('/BaselineCheck/getParamItems', 'ParameterAnalysis\BaselineCheckController@getParamItems');
Route::get('/BaselineCheck/baselineFile', 'ParameterAnalysis\BaselineCheckController@baselineFile');



//lijian
Route::get('lowAccess','NetworkChartsController@getLowAccess');
Route::get('lowAccessTrend','NetworkChartsController@getLowAccessTrend');

Route::get('highLost','NetworkChartsController@getHighLost');
Route::get('highLostTrend','NetworkChartsController@getHighLostTrend');

Route::get('badHandover','NetworkChartsController@getBadHandover');
Route::get('badHandoverTrend','NetworkChartsController@getBadHandoverTrend');


Route::get('erabSuccess','NetworkChartsController@getErabSuccessHandover');
Route::get('erabSuccessTrend','NetworkChartsController@getErabSuccessHandoverTrend');

Route::get('erabLost','NetworkChartsController@getErabsLost');
Route::get('erabLostTrend','NetworkChartsController@getErabsLostTrend');

Route::get('wirelessSuccess','NetworkChartsController@getWirelessSucc');
Route::get('wirelessSuccessTrend','NetworkChartsController@getWirelessSuccTrend');

Route::get('volteHandover','NetworkChartsController@getVolteHandover');
Route::get('volteHandoverTrend','NetworkChartsController@getVolteHandoverTrend');


Route::get('chart1WireSucc','NetworkChartsController@getChart1WireSucc');
Route::get('chart1WireSuccTrend','NetworkChartsController@getChart1WireSuccTrend');

Route::get('chart1ErbLost','NetworkChartsController@getChart1ErbLost');
Route::get('chart1ErbLostTrend','NetworkChartsController@getChart1ErbLostTrend');

Route::get('chart1VideoSucc','NetworkChartsController@getChart1VideoSucc');
Route::get('chart1VideoSuccTrend','NetworkChartsController@getChart1VideoSuccTrend');

Route::get('chart1EsrvccHander','NetworkChartsController@getChart1EsrvccHander');
Route::get('chart1EsrvccHanderTrend','NetworkChartsController@getChart1EsrvccHanderTrend');

//zhouyanqiu
Route::get('/GSMQuery', function () {
    return view('QueryAnalysis.GSMQuery');
});
Route::get('/GSMQuery/getGSMTreeData', 'QueryAnalysis\GSMQueryController@getGSMTreeData');

Route::get('/GSMQuery/searchGSMTreeData', 'QueryAnalysis\GSMQueryController@searchGSMTreeData');

Route::get('/GSMQuery/getAllCity', 'QueryAnalysis\GSMQueryController@getAllCity');

Route::post('/GSMQuery/templateQuery', 'QueryAnalysis\GSMQueryController@templateQuery');

Route::get('/NBIQuery/searchNBITreeData', 'QueryAnalysis\NBIQueryController@searchNBITreeData');


//lijian
Route::get('/threeKeysGauge', 'NetworkChartsController@getThreeKeysGauge');
Route::get('/volteGauge', 'NetworkChartsController@getvolteGauge');
Route::get('/videosGauge', 'NetworkChartsController@getVideoGauge');


//lijian
Route::get('/CustomQuery', function(){
    return view('QueryAnalysis.CustomQuery');
});

Route::get('/CustomQuery/getCustomTreeData', 'QueryAnalysis\CustomQueryController@getCustomTreeData');
Route::get('/CustomQuery/searchCustomTreeData', 'QueryAnalysis\CustomQueryController@getSearchCustomTreeData');
Route::get('/CustomQuery/getAllCity', 'QueryAnalysis\CustomQueryController@getAllCity');
Route::get('/getKpiFormula', 'QueryAnalysis\CustomQueryController@getKpiFormula');
Route::post('/getTable', 'QueryAnalysis\CustomQueryController@getTable');
Route::get('/deleteMode', 'QueryAnalysis\CustomQueryController@deleteMode');
Route::get('/insertMode', 'QueryAnalysis\CustomQueryController@insertMode');
Route::get('/saveMode', 'QueryAnalysis\CustomQueryController@saveMode');


//xuyang
Route::get('/userManage',function(){
    return view('systemManage.userManage');
});
Route::get('/userManage/templateQuery', 'systemManage\userController@templateQuery');
Route::get('/userManage/deleteUser', 'systemManage\userController@deleteUser');
Route::get('/userManage/updateUser', 'systemManage\userController@updateUser');

//zhouyanqiu
Route::get('/lowAccessCell', function () {
  return view('badCellAnalysis.lowAccessCell');
});

Route::get('/highLostCell', function () {
    return view('badCellAnalysis.highLostCell');
});

Route::get('/badHandoverCell', function () {
    return view('badCellAnalysis.badHandoverCell');
});
Route::get('/badCell/getAllCity', 'badCellAnalysis\badCellController@getAllCity');
Route::get('/badCell/templateQuery', 'badCellAnalysis\badCellController@templateQuery');
Route::get('/badCell/getalarmWorstCell', 'badCellAnalysis\badCellController@getalarmWorstCell');
Route::get('/badCell/getChartData', 'badCellAnalysis\badCellController@getChartData');

Route::post('/CustomQuery/saveModeChange', 'QueryAnalysis\CustomQueryController@saveModeChange');
//xuyang
Route::get('/emailManage',function(){
    return view('systemManage.emailManage');
});
Route::get('/emailManage/templateQuery', 'systemManage\emailController@templateQuery');
Route::get('/emailManage/openEmailFile', 'systemManage\emailController@openEmailFile');
Route::get('/emailManage/saveEmailFile', 'systemManage\emailController@saveEmailFile');

//xuyang
Route::get('/ENIQManage',function(){
    return view('systemManage.ENIQManage');
});
Route::get('/ENIQManage/Query4G', 'systemManage\ENIQController@Query4G');
Route::get('/ENIQManage/Query2G', 'systemManage\ENIQController@Query2G');
Route::get('/ENIQManage/updateENIQ', 'systemManage\ENIQController@updateENIQ');
Route::get('/ENIQManage/deleteENIQ', 'systemManage\ENIQController@deleteENIQ');

//xuyang
Route::get('/siteManage',function(){
    return view('systemManage.siteManage');
});
Route::get('/siteManage/TreeQuery', 'systemManage\siteController@TreeQuery');
Route::get('/siteManage/QuerySite4G', 'systemManage\siteController@QuerySite4G');
Route::get('/siteManage/QuerySite2G', 'systemManage\siteController@QuerySite2G');
Route::post('/siteManage/uploadFile', 'systemManage\siteController@uploadFile');
Route::get('/siteManage/downloadFile', 'systemManage\siteController@downloadFile');



//haile
Route::get('/weakCover',function(){
    return view('network.weakCover');
});
Route::get('/weakCoverDate','WeakCoverController@getDate');
Route::get('/weakCoverCells','WeakCoverController@getCells');
Route::get('/weakCoverCharts','WeakCoverController@getCharts');

Route::get('/interCloud',function(){
    return view('network.interCloud');
});
Route::get('/interCloudCells','InterCloudController@getCells');
Route::get('/interCloudChannel','InterCloudController@getChannels');

//lijian
Route::get('/interPointCloud', function(){
    return view('network.interPointCloud');
});
Route::get('/interPointCloudChannel', 'InterCloudController@getPointChannels');
Route::get('/interPointCloudCells','InterCloudController@getCells');

//xuyang
Route::get('/LTETemplateManage',function(){
    return view('QueryAnalysis.LTETemplateManage');
});
Route::get('/LTETemplateManage/getLTETreeData', 'QueryAnalysis\LTETemplateController@getLTETreeData');
Route::get('/LTETemplateManage/searchLTETreeData', 'QueryAnalysis\LTETemplateController@searchLTETreeData');
Route::get('/LTETemplateManage/getElementTree', 'QueryAnalysis\LTETemplateController@getElementTree');
Route::get('/LTETemplateManage/getKpiNamebyId', 'QueryAnalysis\LTETemplateController@getKpiNamebyId');
Route::post('/LTETemplateManage/getTreeTemplate', 'QueryAnalysis\LTETemplateController@getTreeTemplate');
Route::get('/LTETemplateManage/updateFormula', 'QueryAnalysis\LTETemplateController@updateFormula');
Route::get('/LTETemplateManage/deleteFormula', 'QueryAnalysis\LTETemplateController@deleteFormula');
Route::get('/LTETemplateManage/searchTreeTemplate', 'QueryAnalysis\LTETemplateController@searchTreeTemplate');
Route::get('/LTETemplateManage/updateElement', 'QueryAnalysis\LTETemplateController@updateElement');
Route::get('/LTETemplateManage/addMode', 'QueryAnalysis\LTETemplateController@addMode');
Route::get('/LTETemplateManage/deleteMode', 'QueryAnalysis\LTETemplateController@deleteMode');

Route::get('/kpiExport','Exporter\KpiExporter@export');
Route::get('/scaleExport','Exporter\ScaleExporter@export');
//lijian
Route::get('/weakExport','Exporter\NetworkChartsExporter@export');


//xuyang
Route::get('/NBITemplateManage',function(){
    return view('QueryAnalysis.NBITemplateManage');
});
Route::get('/NBITemplateManage/getNBITreeData', 'QueryAnalysis\NBITemplateController@getNBITreeData');
Route::get('/NBITemplateManage/searchNBITreeData', 'QueryAnalysis\NBITemplateController@searchNBITreeData');
Route::get('/NBITemplateManage/getElementTree', 'QueryAnalysis\NBITemplateController@getElementTree');
Route::get('/NBITemplateManage/getKpiNamebyId', 'QueryAnalysis\NBITemplateController@getKpiNamebyId');
Route::post('/NBITemplateManage/getTreeTemplate', 'QueryAnalysis\NBITemplateController@getTreeTemplate');
Route::get('/NBITemplateManage/updateFormula', 'QueryAnalysis\NBITemplateController@updateFormula');
Route::get('/NBITemplateManage/deleteFormula', 'QueryAnalysis\NBITemplateController@deleteFormula');
Route::get('/NBITemplateManage/searchTreeTemplate', 'QueryAnalysis\NBITemplateController@searchTreeTemplate');
Route::get('/NBITemplateManage/updateElement', 'QueryAnalysis\NBITemplateController@updateElement');
Route::get('/NBITemplateManage/addMode', 'QueryAnalysis\NBITemplateController@addMode');
Route::get('/NBITemplateManage/deleteMode', 'QueryAnalysis\NBITemplateController@deleteMode');

//xuyang
Route::get('/GSMTemplateManage',function(){
    return view('QueryAnalysis.GSMTemplateManage');
});
Route::get('/GSMTemplateManage/getGSMTreeData', 'QueryAnalysis\GSMTemplateController@getGSMTreeData');
Route::get('/GSMTemplateManage/searchGSMTreeData', 'QueryAnalysis\GSMTemplateController@searchGSMTreeData');
Route::get('/GSMTemplateManage/getElementTree', 'QueryAnalysis\GSMTemplateController@getElementTree');
Route::get('/GSMTemplateManage/getKpiNamebyId', 'QueryAnalysis\GSMTemplateController@getKpiNamebyId');
Route::post('/GSMTemplateManage/getTreeTemplate', 'QueryAnalysis\GSMTemplateController@getTreeTemplate');
Route::get('/GSMTemplateManage/updateFormula', 'QueryAnalysis\GSMTemplateController@updateFormula');
Route::get('/GSMTemplateManage/deleteFormula', 'QueryAnalysis\GSMTemplateController@deleteFormula');
Route::get('/GSMTemplateManage/searchTreeTemplate', 'QueryAnalysis\GSMTemplateController@searchTreeTemplate');
Route::get('/GSMTemplateManage/updateElement', 'QueryAnalysis\GSMTemplateController@updateElement');
Route::get('/GSMTemplateManage/addMode', 'QueryAnalysis\GSMTemplateController@addMode');
Route::get('/GSMTemplateManage/deleteMode', 'QueryAnalysis\GSMTemplateController@deleteMode');

//xuyang
Route::get('/storageManage',function(){
    return view('systemManage.storageManage');
});
Route::get('/storageManage/taskQuery', 'systemManage\storageController@taskQuery');
Route::get('/storageManage/getTaskTraceDir', 'systemManage\storageController@getTaskTraceDir');
Route::post('/storageManage/addTask', 'systemManage\storageController@addTask');
Route::get('/storageManage/deleteTask', 'systemManage\storageController@deleteTask');
Route::get('/storageManage/monitor', 'systemManage\storageController@monitor');
Route::get('/storageManage/runTask', 'systemManage\storageController@runTask');
Route::get('/storageManage/stopTask', 'systemManage\storageController@stopTask');

//lijian
Route::get('/weakCoverRatio',function(){
    return view('network.weakCoverRatio');
});
Route::get('/SearchWeakCoverRatio', 'WeakCoverRatioController@SearchWeakCoverRatio');

//xuyang
Route::get('/signalingBacktracking',function(){
    return view('complaintHandling.signalingBacktracking');
});
Route::get('/signalingBacktracking/getDataBase', 'complaintHandling\signalingBacktrackingController@getDataBase');
Route::get('/signalingBacktracking/getEventNameandEcgi', 'complaintHandling\signalingBacktrackingController@getEventNameandEcgi');
Route::post('/signalingBacktracking/getEventData', 'complaintHandling\signalingBacktrackingController@getEventData');
Route::get('/signalingBacktracking/getEventDataHeader', 'complaintHandling\signalingBacktrackingController@getEventDataHeader');
Route::post('/signalingBacktracking/getAllEventData', 'complaintHandling\signalingBacktrackingController@getAllEventData');
Route::get('/signalingBacktracking/showMessage', 'complaintHandling\signalingBacktrackingController@showMessage');
Route::post('/signalingBacktracking/exportCSV', 'complaintHandling\signalingBacktrackingController@exportCSV');

//xuyang
Route::get('/nav/getUser', 'navController@getUser');
Route::get('/nav/signout', 'navController@signout');

//xuyang
Route::get('/paramsManage',function(){
    return view('systemManage.paramsManage');
});
Route::get('/paramsManage/getBaselineTreeData', 'systemManage\paramsController@getBaselineTreeData');
Route::get('/paramsManage/searchBaselineTreeData', 'systemManage\paramsController@searchBaselineTreeData');
Route::get('/paramsManage/getBaselineTableData', 'systemManage\paramsController@getBaselineTableData');
Route::get('/paramsManage/downloadFile', 'systemManage\paramsController@downloadFile');
Route::post('/paramsManage/uploadFile', 'systemManage\paramsController@uploadFile');
Route::get('/paramsManage/addMode', 'systemManage\paramsController@addMode');
Route::get('/paramsManage/deleteMode', 'systemManage\paramsController@deleteMode');
//zjj
Route::get('/nav/getSessions', 'navController@getSessions');

//xuyang 参数分布
Route::get('/paramDistribution',function(){
    return view('parameterAnalysis.paramDistribution');
});
Route::get('/paramDistribution/getDate', 'ParameterAnalysis\paramDistributionController@getDate');
Route::get('/paramDistribution/getParameterList', 'ParameterAnalysis\paramDistributionController@getParameterList');
Route::get('/paramDistribution/getCity', 'ParameterAnalysis\paramDistributionController@getCity');
Route::post('/paramDistribution/getChartData', 'ParameterAnalysis\paramDistributionController@getChartData');
Route::get('/paramDistribution/getCitySelect', 'ParameterAnalysis\paramDistributionController@getCitySelect');
Route::post('/paramDistribution/getTableHeader', 'ParameterAnalysis\paramDistributionController@getTableHeader');
Route::get('/paramDistribution/getTableData', 'ParameterAnalysis\paramDistributionController@getTableData');
Route::post('/paramDistribution/getAllTableData', 'ParameterAnalysis\paramDistributionController@getAllTableData');
Route::post('/paramDistribution/exportCSV', 'ParameterAnalysis\paramDistributionController@exportCSV');

//xuyang 信令分析
Route::get('/signalingAnalysis',function(){
    return view('complaintHandling.signalingAnalysis');
});
Route::get('/signalingAnalysis/queryKeyword', 'complaintHandling\signalingAnalysisController@queryKeyword');

//xuyang 数据源管理
Route::get('/dataSourceManage',function(){
    return view('systemManage.dataSourceManage');
});
Route::get('/dataSourceManage/getNode', 'systemManage\dataSourceController@getNode');
Route::get('/dataSourceManage/getFileName', 'systemManage\dataSourceController@getFileName');
Route::post('/dataSourceManage/ctrTreeItems', 'systemManage\dataSourceController@ctrTreeItems');

//lijian 网格优化
Route::get('/GSMNeighborAnalysis',function(){
    return view('networkOptimization.NeighAnalysis');
});
Route::get('/networkOptimization/GSMNeighAnalysis','NetworkOptimization\GSMNeighAnalysisController@getGSMNeighData');
/*Route::get('/NetworkOptimization/getAllDatabase','NetworkOptimization\GSMNeighAnalysisController@getGSMNeighDatabases');*/
Route::get('/NetworkOptimization/getAllCity','NetworkOptimization\GSMNeighAnalysisController@getAllCity');
Route::get('/LTENeighborAnalysis',function(){
    return view('networkOptimization.LTENeighAnalysis');
});
Route::get('/networkOptimization/LTENeighAnalysis','NetworkOptimization\GSMNeighAnalysisController@getLTENeighData');
Route::get('/networkOptimization/GSMNeighAnalysisSplit','NetworkOptimization\GSMNeighAnalysisController@getGSMNeighDataSplit');
Route::get('/networkOptimization/LTENeighAnalysisSplit','NetworkOptimization\GSMNeighAnalysisController@getLTENeighDataSplit');
Route::get('/networkOptimization/GSMNeighAnalysisAll','NetworkOptimization\GSMNeighAnalysisController@getGSMNeighDataAll');
Route::get('/networkOptimization/GSMNeighAnalysisLteAll','NetworkOptimization\GSMNeighAnalysisController@getGSMNeighDataLteAll');

//xuyang 当前告警查询
Route::get('/currentAlarmQuery',function(){
    return view('alarmAnalysis.currentAlarmQuery');
});
Route::get('/currentAlarmQuery/getCitys', 'alarmAnalysis\currentAlarmQueryController@getCitys');
Route::get('/currentAlarmQuery/getTableData', 'alarmAnalysis\currentAlarmQueryController@getTableData');
Route::post('/currentAlarmQuery/getAllTableData', 'alarmAnalysis\currentAlarmQueryController@getAllTableData');

//xuyang 历史告警查询
Route::get('/historyAlarmQuery',function(){
    return view('alarmAnalysis.historyAlarmQuery');
});
Route::get('/historyAlarmQuery/getCitys', 'alarmAnalysis\historyAlarmQueryController@getCitys');
Route::get('/historyAlarmQuery/getTableData', 'alarmAnalysis\historyAlarmQueryController@getTableData');
Route::post('/historyAlarmQuery/getAllTableData', 'alarmAnalysis\historyAlarmQueryController@getAllTableData');

//xuyang PCI MOD 3分析
Route::get('/PCIMOD3Analysis',function(){
    return view('networkOptimization.PCIMOD3Analysis');
});
Route::get('/PCIMOD3Analysis/getAllCity', 'NetworkOptimization\PCIMOD3AnalysisController@getAllCity');
Route::get('/PCIMOD3Analysis/getMroPCIMOD3DataHeader', 'NetworkOptimization\PCIMOD3AnalysisController@getMroPCIMOD3DataHeader');
Route::get('/PCIMOD3Analysis/getMroPCIMOD3Data', 'NetworkOptimization\PCIMOD3AnalysisController@getMroPCIMOD3Data');
Route::get('/PCIMOD3Analysis/getAllMroPCIMOD3Data', 'NetworkOptimization\PCIMOD3AnalysisController@getAllMroPCIMOD3Data');

//zhujiaojiao A2门限分析
Route::get('/A2ThresholdAnalysis',function(){
    return view('networkOptimization.A2ThresholdAnalysis');
});
Route::get('/A2ThresholdAnalysis/getAllCity', 'NetworkOptimization\A2ThresholdAnalysisController@getAllCity');
Route::get('/A2ThresholdAnalysis/getMreA2ThresholdDataHeader', 'NetworkOptimization\A2ThresholdAnalysisController@getMreA2ThresholdDataHeader');
Route::get('/A2ThresholdAnalysis/getMreA2ThresholdData', 'NetworkOptimization\A2ThresholdAnalysisController@getMreA2ThresholdData');
Route::get('/A2ThresholdAnalysis/getAllMreA2ThresholdData', 'NetworkOptimization\A2ThresholdAnalysisController@getAllMreA2ThresholdData');
//A5门限分析
Route::get('/A5ThresholdAnalysis',function(){
    return view('networkOptimization.A5ThresholdAnalysis');
});
Route::get('/A5ThresholdAnalysis/getAllCity', 'NetworkOptimization\A5ThresholdAnalysisController@getAllCity');
Route::get('/A5ThresholdAnalysis/getMreA5ThresholdDataHeader', 'NetworkOptimization\A5ThresholdAnalysisController@getMreA5ThresholdDataHeader');
Route::get('/A5ThresholdAnalysis/getMreA5ThresholdData', 'NetworkOptimization\A5ThresholdAnalysisController@getMreA5ThresholdData');
Route::get('/A5ThresholdAnalysis/getAllMreA5ThresholdData', 'NetworkOptimization\A5ThresholdAnalysisController@getAllMreA5ThresholdData');

//同频补邻区
    //MRO
Route::get('/MROServeNeighAnalysis',function(){
    return view('networkOptimization.MROServeNeighAnalysis');
});
Route::get('/MROServeNeighAnalysis/getAllCity', 'NetworkOptimization\MRONeighAnalysisController@getAllCity');
Route::get('/MROServeNeighAnalysis/getMroServeNeighDataHeader', 'NetworkOptimization\MRONeighAnalysisController@getMroServeNeighDataHeader');
Route::get('/MROServeNeighAnalysis/getMroServeNeighData', 'NetworkOptimization\MRONeighAnalysisController@getMroServeNeighData');
Route::get('/MROServeNeighAnalysis/getAllMroServeNeighData', 'NetworkOptimization\MRONeighAnalysisController@getAllMroServeNeighData');
    //MRE
Route::get('/MREServeNeighAnalysis/getMreServeNeighDataHeader', 'NetworkOptimization\MRENeighAnalysisController@getMreServeNeighDataHeader');
Route::get('/MREServeNeighAnalysis/getMreServeNeighData', 'NetworkOptimization\MRENeighAnalysisController@getMreServeNeighData');
Route::get('/MREServeNeighAnalysis/getAllMreServeNeighData', 'NetworkOptimization\MRENeighAnalysisController@getAllMreServeNeighData');

//CDR补2G邻区
Route::get('/CDRServeNeighAnalysis',function(){
    return view('networkOptimization.CDRServeNeighAnalysis');
});
Route::get('/CDRServeNeighAnalysis/getAllCity', 'NetworkOptimization\CDRNeighAnalysisController@getAllCity');
Route::get('/CDRServeNeighAnalysis/getCdrServeNeighDataHeader', 'NetworkOptimization\CDRNeighAnalysisController@getCdrServeNeighDataHeader');
Route::get('/CDRServeNeighAnalysis/getCdrServeNeighData', 'NetworkOptimization\CDRNeighAnalysisController@getCdrServeNeighData');
Route::get('/CDRServeNeighAnalysis/getAllCdrServeNeighData', 'NetworkOptimization\CDRNeighAnalysisController@getAllCdrServeNeighData');

//xuyang 失败原因分析
Route::get('/failureAnalysis',function(){
    return view('badCellAnalysis.failureAnalysis');
});
Route::get('/failureAnalysis/getDataBase', 'badCellAnalysis\failureAnalysisController@getDataBase');
Route::post('/failureAnalysis/getChartData', 'badCellAnalysis\failureAnalysisController@getChartData');
Route::get('/failureAnalysis/getTableData', 'badCellAnalysis\failureAnalysisController@getTableData');
Route::post('/failureAnalysis/exportFile', 'badCellAnalysis\failureAnalysisController@exportFile');
Route::post('/failureAnalysis/getdetailDataHeader', 'badCellAnalysis\failureAnalysisController@getdetailDataHeader');
Route::get('/failureAnalysis/getdetailData', 'badCellAnalysis\failureAnalysisController@getdetailData');

//xuyang 无切换邻区分析
Route::get('/relationNonHandover',function(){
    return view('networkOptimization.relationNonHandover');
});
Route::get('/relationNonHandover/getCitys', 'NetworkOptimization\relationNonHandoverController@getCitys');
Route::get('/relationNonHandover/getDataHeader', 'NetworkOptimization\relationNonHandoverController@getDataHeader');
Route::get('/relationNonHandover/getTableData', 'NetworkOptimization\relationNonHandoverController@getTableData');
Route::post('/relationNonHandover/getAllTableData', 'NetworkOptimization\relationNonHandoverController@getAllTableData');

//xuyang 模板的复制功能
Route::get('/LTETemplateManage/copyMode', 'QueryAnalysis\LTETemplateController@copyMode');
Route::get('/GSMTemplateManage/copyMode', 'QueryAnalysis\GSMTemplateController@copyMode');
Route::get('/NBITemplateManage/copyMode', 'QueryAnalysis\NBITemplateController@copyMode');

//xuyang 通知功能
Route::post('/nav/addNotice', 'navController@addNotice');
Route::get('/nav/getNotice', 'navController@getNotice');
Route::get('/nav/readNotice', 'navController@readNotice');
Route::post('/nav/readAllNotice', 'navController@readAllNotice');

//xuyang 通知管理
Route::get('/noticeManage',function(){
    return view('systemManage.noticeManage');
});
Route::get('/noticeManage/getNotice', 'systemManage\noticeController@getNotice');
Route::get('/noticeManage/deleteNotice', 'systemManage\noticeController@deleteNotice');


//zhangyongcai  bulkcm留痕
Route::get('/bulkcmMark',function(){
    return view('parameterAnalysis.bulkcmMark');
});
Route::group(['prefix'=>'bulkcmMark','namespace' => 'ParameterAnalysis'],function(){
    Route::get('getParamTasks','BulkcmMarkController@getParamTasks');
    Route::get('getAllCity','BulkcmMarkController@getAllCity');
    Route::get('getBulkcmMarkDataHeader','BulkcmMarkController@getBulkcmMarkDataHeader');
    Route::get('getBulkcmMarkData','BulkcmMarkController@getBulkcmMarkData');
    Route::get('getAllBulkcmMarkData','BulkcmMarkController@getAllBulkcmMarkData');
});
//zhangyongcai  kgetpart留痕
Route::get('/kgetpartMark',function(){
    return view('parameterAnalysis.kgetpartMark');
});
Route::group(['prefix'=>'kgetpartMark','namespace' => 'ParameterAnalysis'],function(){
    Route::get('getParamTasks','KgetpartMarkController@getParamTasks');
    Route::get('getAllCity','KgetpartMarkController@getAllCity');
    Route::get('getKgetpartMarkDataHeader','KgetpartMarkController@getKgetpartMarkDataHeader');
    Route::get('getKgetpartMarkData','KgetpartMarkController@getKgetpartMarkData');
    Route::get('getAllKgetpartMarkData','KgetpartMarkController@getAllKgetpartMarkData');
    
});

//xuyang badCell LTE邻区补
Route::post('/badCell/getLTENeighborHeader', 'badCellAnalysis\badCellController@getLTENeighborHeader');
Route::get('/badCell/getLTENeighborData', 'badCellAnalysis\badCellController@getLTENeighborData');
Route::post('/badCell/getGSMNeighborHeader', 'badCellAnalysis\badCellController@getGSMNeighborHeader');
Route::get('/badCell/getGSMNeighborData', 'badCellAnalysis\badCellController@getGSMNeighborData');

//xuyang 切换差邻区分析
Route::get('/relationBadHandover',function(){
    return view('networkOptimization.relationBadHandover');
});
Route::get('/relationBadHandover/getCitys', 'NetworkOptimization\relationBadHandoverController@getCitys');
Route::get('/relationBadHandover/getDataHeader', 'NetworkOptimization\relationBadHandoverController@getDataHeader');
Route::get('/relationBadHandover/getTableData', 'NetworkOptimization\relationBadHandoverController@getTableData');
Route::post('/relationBadHandover/getAllTableData', 'NetworkOptimization\relationBadHandoverController@getAllTableData');