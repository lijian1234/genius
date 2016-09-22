<?php

namespace APP\Http\Controllers\Exporter;

use PDO;
use DateTime;
use DateInterval;
use PHPExcel;
use PHPExcel_Chart;
use PHPExcel_Chart_Legend;
use PHPExcel_Chart_Title;
use PHPExcel_Chart_Layout;
use PHPExcel_Chart_DataSeries;
use PHPExcel_Chart_PlotArea;
use PHPExcel_Chart_DataSeriesValues;
use PHPExcel_Writer_Excel2007;
use App\Http\Requests\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\NetworkChartsRequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
class NetworkChartsExporter extends Controller
{

    /**
     * 创建Excel报告.
     * @param Request $request
     * @return string $fileName
     */
    public function export(NetworkChartsRequest $request) {
        //创建excel对象.
        $excel = new PHPExcel();

        //创建基站类型分布sheet.
        $sheetBSC = $excel->getSheet(0);
        $sheetBSC->setTitle('差小区概览');

        $access = new BadCellByCity($request);
        $sheetBSC->addChart($access->toExcelChart()->setTopLeftPosition('A1')->setBottomRightPosition('G20')); //对角长度
        $sheetBSC->fromArray($access->toExcelArray(),NULL,'A21');

        $sheetABN = $excel->createSheet(1);
        $sheetABN->setTitle('告警数量');

        //当前告警
        $access = new AlarmByNum($request);
        $sheetABN->addChart($access->toExcelChart()->setTopLeftPosition('A1')->setBottomRightPosition('G20')); //对角长度
        $sheetABN->fromArray($access->toExcelArray(),NULL,'A21');

        //历史告警
        $access = new AlarmByHistory($request);
        $sheetABN->addChart($access->toExcelChart()->setTopLeftPosition('H1')->setBottomRightPosition('CF20')); //对角长度
        $sheetABN->fromArray($access->toExcelArray(),NULL,'H21');

        //$sheetIFO = $excel->createSheet(2);
        // $sheetIFO->setTitle('干扰概览');
        // //干扰概览
        // $access = new InterfereOverview($request);
        // $sheetIFO->addChart($access->toExcelChart()->setTopLeftPosition('A1')->setBottomRightPosition('G20')); //对角长度
        // $sheetIFO->fromArray($access->toExcelArray(),NULL,'A21');

        $sheetPTO = $excel->createSheet(2);
        $sheetPTO->setTitle('参数概览');
        //Baseline检查->参数数量分布
        $access = new ParamByNum($request);
        $sheetPTO->addChart($access->toExcelChart()->setTopLeftPosition('A1')->setBottomRightPosition('H20'));
        $sheetPTO->fromArray($access->toExcelArray(),NULL,'A21');
        //Baseline检查->基站数量分布
        $access = new ErbsByNum($request);
        $sheetPTO->addChart($access->toExcelChart()->setTopLeftPosition('I1')->setBottomRightPosition('P20'));
        $sheetPTO->fromArray($access->toExcelArray(),NULL,'I21');
        //一致性检查->参数数量分布
        $access = new ParamByConsistencyNum($request);
        $sheetPTO->addChart($access->toExcelChart()->setTopLeftPosition('A23')->setBottomRightPosition('H43'));
        $sheetPTO->fromArray($access->toExcelArray(),NULL,'A44');
        //一致性检查->小区数量分布
        $access = new ErbsByConsistencyNum($request);
        $sheetPTO->addChart($access->toExcelChart()->setTopLeftPosition('I23')->setBottomRightPosition('P43'));
        $sheetPTO->fromArray($access->toExcelArray(),NULL,'I44');

        $writer = new PHPExcel_Writer_Excel2007($excel);
        $writer->setIncludeCharts(true);
        $writer->save('NetworkWeak.xlsx');
        return 'NetworkWeak.xlsx';

    }
}

//一致性检查->小区数量分布
class ErbsByConsistencyNum extends AbstractModel{
	public function __construct(NetworkChartsRequest $request){
		$this->request = $request;
        $this->chartType = PHPExcel_Chart_DataSeries::TYPE_BARCHART;
        $this->chartTitle = '小区数量分布';
        $this->chartData = $this->getChartData();
	}

	public function getChartData(){
        if($this->chartData !== null){
            return $this->chartData;
        }
        $date = new DateTime();
        $date->sub(new DateInterval('P1D'));
        $yesDate = $date->format('ymd');
        $yesDate1 = $date->format('Y-m-d');
        $db = 'kget'.$yesDate;
        $dsn = "mysql:host=localhost;dbname=$db";
        $dbn = new PDO($dsn, 'root', 'mongs');
        $res = DB::select('select connName,subNetwork from databaseconn ');
        $sql = '';
        foreach ($res as $items) {
        	$subNetwork = $items->subNetwork;
        	$subNetwork = $this->reCombine($subNetwork);
        	$city = $items->connName;
        	//$sql = $sql ." select count(distinct meContext) as kpi, '$city' as xTicks, '$yesDate1' as series from ParaCheckBaseline where (category = 'A' or category = 'M') and subNetwork in (".$subNetwork.") UNION ";
        	$sql = $sql . " select sum(num) as kpi, '$city' as xTicks, '$yesDate1' as series from (
        					select count(distinct meContext) num from TempEUtranCellFreqRelation where subNetwork in (".$subNetwork.") union all
							select count(distinct meContext) num from TempEUtranCellRelationUnidirectionalNeighborCell where subNetwork in (".$subNetwork.") union all
							select count(distinct meContext) num from TempEUtranCellRelationExistNeighborCellWithoutX2 where subNetwork in (".$subNetwork.") union
							select count(distinct meContext) num from TempEUtranCellRelationManyNeighborCell where subNetwork in (".$subNetwork.") union all
							select count(distinct meContext) num from TempEUtranCellRelationFewNeighborCell where subNetwork in (".$subNetwork.") union all
							select count(distinct meContext) num from TempExternalEUtranCellTDDActivePlmnListCheck where subNetwork in (".$subNetwork.") union all
							select count(distinct meContext) num from TempEUtranCellRelationNeighOfPci where subNetwork in (".$subNetwork.") union all
							select count(distinct meContext) num from TempEUtranCellRelationNeighOfNeighPci where subNetwork in (".$subNetwork.") union all
							select count(distinct meContext) num from TempGeranCellRelation2GNeighbor where subNetwork in (".$subNetwork.") union all
							select count(distinct meContext) num from TempParameter2GKgetCompare where subNetwork in (".$subNetwork.") union all
							select count(distinct meContext) num from TempExternalNeigh4G where subNetwork in (".$subNetwork.") union all
							select count(distinct meContext) num from TempParameterQCI_A1A2 where subNetwork in (".$subNetwork.") union all
							select count(distinct meContext) num from TempParameterQCI_B2A2critical where subNetwork in (".$subNetwork.") 
							) t UNION ";
        }
        //print_r($sql);return;
        $sql = rtrim($sql, 'UNION ') . " ORDER by xTicks"; 
        $db = new PDO("mysql:host=localhost;dbname=$db","root","mongs");
        return collect($db->query($sql)->fetchAll());
    }
    protected function reCombine($subNetwork){
        $subNetArr = explode(",", $subNetwork);
        $subNetsStr = '';
        foreach ($subNetArr as $subNet) {
            $subNetsStr.= "'".$subNet."',";
        }
        return substr($subNetsStr,0,-1);
    }
}
//一致性检查->参数数量分布
class ParamByConsistencyNum extends AbstractModel{
	public function __construct(NetworkChartsRequest $request){
		$this->request = $request;
        $this->chartType = PHPExcel_Chart_DataSeries::TYPE_BARCHART;
        $this->chartTitle = '参数数量分布';
        $this->chartData = $this->getChartData();
	}

	public function getChartData(){
        if($this->chartData !== null){
            return $this->chartData;
        }
        $date = new DateTime();
        $date->sub(new DateInterval('P1D'));
        $yesDate = $date->format('ymd');
        $yesDate1 = $date->format('Y-m-d');
        $db = 'kget'.$yesDate;
        $dsn = "mysql:host=localhost;dbname=$db";
        $dbn = new PDO($dsn, 'root', 'mongs');
        $res = DB::select('select connName,subNetwork from databaseconn ');
        $sql = '';
        foreach ($res as $items) {
        	$subNetwork = $items->subNetwork;
        	$subNetwork = $this->reCombine($subNetwork);
        	$city = $items->connName;
        	//$sql = $sql ." select count(distinct meContext) as kpi, '$city' as xTicks, '$yesDate1' as series from ParaCheckBaseline where (category = 'A' or category = 'M') and subNetwork in (".$subNetwork.") UNION ";
        	$sql = $sql . " select sum(num) as kpi, '$city' as xTicks, '$yesDate1' as series from (
	        			select count(*) num from TempEUtranCellFreqRelation where subNetwork in (".$subNetwork.") union all
						select count(*) num from TempEUtranCellRelationUnidirectionalNeighborCell where subNetwork in (".$subNetwork.") union all
						select count(*) num from TempEUtranCellRelationExistNeighborCellWithoutX2 where subNetwork in (".$subNetwork.") union
						select count(*) num from TempEUtranCellRelationManyNeighborCell where subNetwork in (".$subNetwork.") union all
						select count(*) num from TempEUtranCellRelationFewNeighborCell where subNetwork in (".$subNetwork.") union all
						select count(*) num from TempExternalEUtranCellTDDActivePlmnListCheck where subNetwork in (".$subNetwork.") union all
						select count(*) num from TempEUtranCellRelationNeighOfPci where subNetwork in (".$subNetwork.") union all
						select count(*) num from TempEUtranCellRelationNeighOfNeighPci where subNetwork in (".$subNetwork.") union all
						select count(*) num from TempGeranCellRelation2GNeighbor where subNetwork in (".$subNetwork.") union all
						select count(*) num from TempParameter2GKgetCompare where subNetwork in (".$subNetwork.") union all
						select count(*) num from TempExternalNeigh4G where subNetwork in (".$subNetwork.") union all
						select count(*) num from TempParameterQCI_A1A2 where subNetwork in (".$subNetwork.") union all
						select count(*) num from TempParameterQCI_B2A2critical where subNetwork in (".$subNetwork.") 
					) t UNION ";
        }
        //print_r($sql);return;
        $sql = rtrim($sql, 'UNION ') . " ORDER by xTicks"; 
        $db = new PDO("mysql:host=localhost;dbname=$db","root","mongs");
        return collect($db->query($sql)->fetchAll());
    }
    protected function reCombine($subNetwork){
        $subNetArr = explode(",", $subNetwork);
        $subNetsStr = '';
        foreach ($subNetArr as $subNet) {
            $subNetsStr.= "'".$subNet."',";
        }
        return substr($subNetsStr,0,-1);
    }
}

//Baseline检查->基站数量分布
class ErbsByNum extends AbstractModel{
	public function __construct(NetworkChartsRequest $request){
		$this->request = $request;
        $this->chartType = PHPExcel_Chart_DataSeries::TYPE_BARCHART;
        $this->chartTitle = '基站数量分布';
        $this->chartData = $this->getChartData();
	}

	public function getChartData(){
        if($this->chartData !== null){
            return $this->chartData;
        }
        $date = new DateTime();
        $date->sub(new DateInterval('P1D'));
        $yesDate = $date->format('ymd');
        $yesDate1 = $date->format('Y-m-d');
        $db = 'kget'.$yesDate;
        $dsn = "mysql:host=localhost;dbname=$db";
        $dbn = new PDO($dsn, 'root', 'mongs');
        $res = DB::select('select connName,subNetwork from databaseconn ');
        $sql = '';
        foreach ($res as $items) {
        	$subNetwork = $items->subNetwork;
        	$subNetwork = $this->reCombine($subNetwork);
        	$city = $items->connName;
        	$sql = $sql ." select count(distinct meContext) as kpi, '$city' as xTicks, '$yesDate1' as series from ParaCheckBaseline where (category = 'A' or category = 'M') and subNetwork in (".$subNetwork.") UNION ";
        }
        //print_r($sql);return;
        $sql = rtrim($sql, 'UNION ') . " ORDER by xTicks"; 
        $db = new PDO("mysql:host=localhost;dbname=$db","root","mongs");
        return collect($db->query($sql)->fetchAll());
    }

    protected function reCombine($subNetwork){
        $subNetArr = explode(",", $subNetwork);
        $subNetsStr = '';
        foreach ($subNetArr as $subNet) {
            $subNetsStr.= "'".$subNet."',";
        }
        return substr($subNetsStr,0,-1);
    }
} 

//Baseline检查->参数数量分布
class ParamByNum extends AbstractModel{
	public function __construct(NetworkChartsRequest $request){
		$this->request = $request;
        $this->chartType = PHPExcel_Chart_DataSeries::TYPE_BARCHART;
        $this->chartTitle = '参数数量分布';
        $this->chartData = $this->getChartData();
	}

	public function getChartData(){
        if($this->chartData !== null){
            return $this->chartData;
        }
        $date = new DateTime();
        $date->sub(new DateInterval('P1D'));
        $yesDate = $date->format('ymd');
        $yesDate1 = $date->format('Y-m-d');
        $db = 'kget'.$yesDate;
        $dsn = "mysql:host=localhost;dbname=$db";
        $dbn = new PDO($dsn, 'root', 'mongs');
        $res = DB::select('select connName,subNetwork from databaseconn ');
        $sql = '';
        foreach ($res as $items) {
        	$subNetwork = $items->subNetwork;
        	$subNetwork = $this->reCombine($subNetwork);
        	$city = $items->connName;
        	$sql = $sql ." select count(id) as kpi, '$city' as xTicks, '$yesDate1' as series from ParaCheckBaseline where (category = 'A' or category = 'M') and subNetwork in (".$subNetwork.") UNION ";
        }
        //print_r($sql);
        $sql = rtrim($sql, 'UNION ') . " ORDER by xTicks"; 
        $db = new PDO("mysql:host=localhost;dbname=$db","root","mongs");
        return collect($db->query($sql)->fetchAll());
    }

    protected function reCombine($subNetwork){
        $subNetArr = explode(",", $subNetwork);
        $subNetsStr = '';
        foreach ($subNetArr as $subNet) {
            $subNetsStr.= "'".$subNet."',";
        }
        return substr($subNetsStr,0,-1);
    }
}

//干扰概览
// class InterfereOverview extends AbstractModel{
//     public function __construct(NetworkChartsRequest $request){
//         $this->request = $request;
//         $this->chartType = PHPExcel_Chart_DataSeries::TYPE_LINECHART;
//         $this->chartTitle = '干扰概览';
//         $this->chartData = $this->getChartData();
//     }

//     public function getChartData(){
//         if($this->chartData !== null){
//             return $this->chartData;
//         }
//         $dayId = $this->request->get('day',date_sub(new DateTime(), new DateInterval('P1D'))->format('Y-m-d'));
//         $sql = 'select day_id as xTicks,city as series,高干扰小区占比 as kpi from interfereRate_city_day where day_id = "'.$dayId.'"';
//         $db = new PDO("mysql:host=localhost;dbname=AutoKPI","root","mongs");
//         return collect($db->query($sql)->fetchAll());
//     }
// }

//历史告警
class AlarmByHistory extends AbstractModel{
    public function __construct(NetworkChartsRequest $request){
        $this->request = $request;
        $this->chartType = PHPExcel_Chart_DataSeries::TYPE_LINECHART;
        $this->chartTitle = '历史告警';
        $this->chartData = $this->getChartData();
    }

    public function getChartData(){
        if($this->chartData !== null){
            return $this->chartData;
        }
        $sql = "select date as xTicks,city as series, alarm_num as kpi from FMA_alarm_log_group_by_city_date where date>'2016-05-01';";
        $db = new PDO("mysql:host=localhost;dbname=Alarm","root","mongs");
        return collect($db->query($sql)->fetchAll());
    }
}
//xTicks:X轴  kpi:数据
//当前告警
class AlarmByNum extends AbstractModel{
    public function __construct(NetworkChartsRequest $request){
        $this->request = $request;
        $this->chartType = PHPExcel_Chart_DataSeries::TYPE_BARCHART;
        $this->chartTitle = '当前告警';
        $this->chartData = $this->getChartData();
    }

    public function getChartData(){
        if($this->chartData !== null){
            return $this->chartData;
        }
        $sql_categoty = "select DISTINCT city as category from FMA_alarm_list where city is not null and city != '' order by city";
        $conn = DB::connection('alarm');
        $rs = $conn->select($sql_categoty);
        $categories = array();
        $categories = $this->getHighChartCategory($rs);
        $sql_key = "select Perceived_severity as type from FMA_alarm_list group by type order by type";
        $rs = $conn->select($sql_key);
        $series = array();
        $sql = '';
        foreach ($rs as $item) {
            $type = $item->type;
            $sql = $sql . " select city as xTicks,
              case Perceived_severity
              when 1 then '1'
              when 2 then 'CRITICAL'
              when 3 then 'MAJOR'
              when 4 then 'MINOR'
              when 5 then 'WARNING'
              end as series
              , 
              count(*) as kpi from FMA_alarm_list where Perceived_severity='".$type."' and city is not null and city != '' group by city,Perceived_severity UNION ";
        }
        $sql = rtrim($sql, 'UNION ') . " ORDER by xTicks"; 
        $db = new PDO("mysql:host=localhost;dbname=Alarm","root","mongs");
        return collect($db->query($sql)->fetchAll());
    }

    public function getHighChartCategory($rs){
        $categories = array();
        foreach ($rs as $item) {
            $category = $item->category;
            if (array_search($category, $categories) === false) {
                $categories[] = $category;
            }
        }
        return $categories;
    }
}

//xTicks:X轴  kpi:数据
//差小区概览
class BadCellByCity extends AbstractModel
{
    /**
     * BadCellByCity constructor.
     * @param Request $request
     */
    public function __construct(NetworkChartsRequest $request)
    {
        $this->request = $request;
        $this->chartType = PHPExcel_Chart_DataSeries::TYPE_BARCHART;//TYPE_BARCHART  TYPE_PIECHART TYPE_LINECHART 柱 饼 线
        $this->chartTitle = '差小区概览';
        $this->chartData = $this->getChartData();
    }

    public function getChartData()
    {
        //echo "succ";
        if ($this->chartData !== null) {
            return $this->chartData;
        }
        $dayId = $this->request->get('day',date_sub(new DateTime(), new DateInterval('P1D'))->format('Y-m-d'));
        //$sql = "SELECT city,count(city) as num,'badHandoverCell' as type from badHandoverCell_ex where day_id = '".$dayId."' group by city union select city,count(city) as num,'highLostCell' as type from highLostCell_ex where day_id = '".$dayId."' group by city union select city,count(city) as num,'lowAccessCell' as type from lowAccessCell_ex where day_id = '".$dayId."' group by city";
        $sql = "SELECT 'badHandoverCell' as series, city as xTicks, count(city) as kpi from badHandoverCell_ex where day_id = '".$dayId."' group by city UNION SELECT 'lowAccessCell' as series, city as xTicks, count(city) as kpi from lowAccessCell_ex where day_id = '".$dayId."' group by city union SELECT 'highLostCell' as series, city as xTicks, count(city) as kpi from highLostCell_ex where day_id = '".$dayId."' group by city";
        $db = new PDO("mysql:host=localhost;dbname=AutoKPI","root","mongs");
        return collect($db->query($sql)->fetchAll());
    }
}
