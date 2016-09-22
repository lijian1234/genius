<?php
/**
* 文件名(GSMQueryController.php)
*
* 功能描述(GSM指标查询控制模块)
*
* @author lijian
*/
namespace App\Http\Controllers\QueryAnalysis;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use PDO;

/**
* 类名 GSMQueryController
* 类功能描述
* @package GSMQueryController
*/
class GSMQueryController extends Controller
{
    public function init()
    {
        return view('QueryAnalysis.GSMQuery');
    }

    public function getGSMTreeData()
    {
        $users = DB::select('select distinct user from template_2G');
        $arrUser = array();
        $items = array();
        $itArr = array();
        foreach ($users as $user) {
            $userStr = $user->user;
            $templateNames = DB::table('template_2G')->
                            where('user', '=', $userStr)->get();
            foreach ($templateNames as $templateName) {
                array_push($arrUser, array("text"=>$templateName->templateName));
            }
            $items["text"] = $userStr;
            $items["nodes"] = $arrUser;
            $arrUser = array();
            array_push($itArr, $items);
        }
        return response()->json($itArr);
    }

    public function searchGSMTreeData()
    {
        $inputData = Input::get('inputData');
        $inputData = "%".$inputData."%";
        $users = DB::table('template_2G')->
                where('templateName', 'like', $inputData)->get();
        $items = array();
        foreach ($users as $user) {
            $templateName = '{"text":"'.$user->templateName
                            .'","value":"'.$user->templateName.'"}';
            array_push($items, $templateName);
        }
        return response()->json($items);
    }
    public function getAllCity()
    {
        $databaseConns = DB::select('select * from databaseconn_2G');
        $items = array();
        foreach($databaseConns as $databaseConn){
            $city = '{"text":"'.$databaseConn->cityChinese
                    .'","value":"'.$databaseConn->connName.'"}';
            array_push($items, $city);
        }
        return response()->json($items);
    }

    protected function resultToCSV2($result, $filename)
    {
        $csvContent = mb_convert_encoding($result['text'] . "\n", 'gb2312', 'utf-8');
        $fp = fopen($filename, "w");
        fwrite($fp, $csvContent);
        foreach ($result['records'] as $row) {
            fputcsv($fp, $row);
        }
        fclose($fp);
    }

    public function templateQuery()
    {
        $dsn = "mysql:host=localhost;dbname=mongs";
        $db = new PDO($dsn, 'root', 'mongs');
        if ($db == null) {
            $result["result"] = "false";
            $result["reason"] = "Failed to connect to database.";
            echo json_encode($result);
            return;
        }
        $template = Input::get('template'); 
        $citys = json_decode(Input::get('city'), true);
        $startTime = Input::get('startTime');
        $endTime = Input::get('endTime');
        $min = Input::get('minute');
        $cell = Input::get('cell');
        $erbs = Input::get('erbs');
        $timeDim = Input::get('timeDim');
        $locationDim = Input::get('locationDim');
        $counters = $this->LoadCounters_2G();
        $kpis = $this->getKpis($db);

        $items = array();
        $resultText = "";
        $csvContent = "";
        foreach ($citys as $city) {
            $sql = "SELECT host,port,dbName,userName,password FROM databaseconn_2G 
                    WHERE connName = '" .$city. "'";
            $res = $db->query($sql);
            $row = $res->fetch(PDO::FETCH_ASSOC);
            $host = $row['host'];
            $port = $row['port'];
            $dbName = $row['dbName'];
            $userName = $row['userName'];
            $password = $row['password'];
            $pmDbDSN = "dblib:host=" .$host. ":" .$port. ";dbname=" .$dbName;
            $pmDB = new PDO($pmDbDSN, $userName, $password);
            if($pmDB == null) {
                $result["result"] = "false";
                $result["reason"] = "Failed to connect to database.";
                echo json_encode($result);
                return;
            } 
            $resultText = "";
            $queryResult = $this->queryTemplate($db, $pmDB, $counters, $timeDim, 
                                $locationDim, $startTime, $endTime, $city, $resultText);
            foreach ($queryResult['records'] as $qr) {
                $csvContent = $csvContent.implode(",", $qr)."\n";
                array_push($items, $qr);
            }
            $result['text'] = $resultText . $kpis['names'];
            $pmDB = null;
        }
        $result['total'] = count($items);
        $result['records'] = $items;
        $result['result'] = 'true';
        $template = Input::get('template');
        $filename = "common/files/" . $template . date('YmdHis') . ".csv";
        $startQueryTime=time();
        $this->resultToCSV2($result, $filename);
        $endQueryTime=time();
        $writeFileDuration=($endQueryTime-$startQueryTime)."s";
        $result['filename'] = $filename;
        echo json_encode($result);
    }

    protected function loadCounters_2G()
    {
        $result = array();
        if (file_exists("common/txt/Counters_2G.txt")) {
            $result = $this->loadCountersFromFile();
        }
        return $result;
    }

    protected function loadCountersFromFile()
    {
        $result = array();
        $lines = file("common/txt/Counters_2G.txt");
        foreach($lines as $line) {
            $pair = explode("=", $line);
            $result[$pair[0]]=$pair[1];
        }
        return $result;
    }


    protected function getKpis($localDB)
    {
        $templateName = Input::get('template');
        $queryKpiset = "select elementId from `template_2G` 
                        where templateName='$templateName'";
        $res = $localDB->query($queryKpiset, PDO::FETCH_ASSOC);
        $kpis = $res->fetchColumn();
        $queryKpiName = "select kpiName,instr('$kpis',id) as sort from kpiformula_2G 
                    where id in ($kpis) order by sort";
        $res = $localDB->query($queryKpiName, PDO::FETCH_ASSOC);
        $kpiNames = "";
        foreach ($res as $row) {
            $kpiNames = $kpiNames . $row['kpiName'] . ",";
        }
        $kpiNames = substr($kpiNames, 0, strlen($kpiNames) - 1);
        $result = array();
        $result['ids'] = $kpis;
        $result['names'] = $kpiNames;
        return $result;
    }

    protected function parserKPI($kpi, $counters, &$counterMap)
    {
        $pattern = "/[\(\)\+\*-\/]/";
        $columns = preg_split($pattern, $kpi);
        foreach ($columns as $column) {
            $column = trim($column);
            $counterName = $column;
            @$table = $counters[strtolower($counterName)];
            if (!array_key_exists($column, $counterMap)) {
                $counterMap[$column] = $table;
            }
        }
    }    


    protected function formulaTransform($formula)
    {
        if(strpos($formula, '(') == false && strpos($formula, ')') == false) {
            $formula = "SUM(".$formula.")";
            return $formula;
        }else{
            $firStr = '';
            $finStr = '';
            $formula = preg_replace("/\s/", "", $formula);
            $firPos = strpos($formula, '(');
            if($firPos != 0) {
                $firStr = substr($formula, 0, $firPos);
                $formula = substr($formula, $firPos);
            }
            $finPos = strrpos($formula, ')');
            if(($finPos+1) != strlen($formula)) {
                $finStr = substr($formula, $finPos+1);
                $formula = substr($formula, 0, $finPos+1);
            }

            $arr = [0];
            $sum = 0;
            for($i=0; $i<strlen($formula); $i++) {
                if($formula[$i] == '(') {
                    $sum = $sum + 1;
                }
                if($formula[$i] == ')') {
                    $sum = $sum - 1;
                }
                if($sum == 0) {
                    array_push($arr, $i);
                }
            }

            $comStr = '';
            $comStr = $this->formulaAddSum($arr, $formula);

            if(strlen($firStr) == 0 && strlen($finStr) == 0) {
                $comStr = $comStr;
            }else if(strlen($firStr) > 0 && strlen($finStr) == 0) {
                $comStr = $firStr . $comStr;
            }else if(strlen($firStr) == 0 && strlen($finStr) > 0) {
                $comStr = $comStr . $finStr;
            }else if(strlen($firStr) > 0 && strlen($finStr) > 0) {
                $comStr = $firStr . $comStr . $finStr;
            }       
            return $comStr;
        }   
    }

    protected function queryTemplate($localDB, $pmDB, $counters, $timeDim, 
        $locationDim, $startTime, $endTime, $city, &$resultText
    ) {
        $result = array();
        $kpis = $this->getKpis($localDB); 
        $result['text'] = $kpis['names']; //获取kpiformula_2G中kpiName字段
        $sql = $this->createSQL($localDB, $pmDB, $kpis['ids'], $counters, $timeDim, $locationDim,
                $startTime, $endTime, $city, $resultText);
        $result['records'] = $pmDB->query($sql, PDO::FETCH_ASSOC);
        return $result;

    }

    protected   function createSQL($localDB, $pmDB, $kpiName, $counters, $timeDim, 
        $locationDim, $startTime, $endTime, $city, &$resultText
    ) {
        //Creat the kpi name group
        $kpiset = "(" . $kpiName . ")";
        $kpis = "";
        //Query all the formula
        $queryFormula = "select kpiName,kpiFormula,kpiPrecision,instr('$kpiName',id) as sort 
                    from kpiformula_2G where id in " .$kpiset . " order by sort";
        $index = 0;
        $tables = array();
        $tableName = current($tables);
        $selectSQL = "SELECT";
        $counterMap = array();
        foreach ($localDB->query($queryFormula) as $row) {
            $kpi = $row['kpiFormula'];
            $this->parserKPI($kpi, $counters, $counterMap);
            $formula =  $row['kpiFormula'];
            $formula = "cast(" .$this->formulaTransform($formula)
                        . " as decimal(18," . $row['kpiPrecision'] ."))";    
            $kpis = $kpis . $formula . " as kpi" . $index . ",";
            $index++;
        } 
        $kpis = substr($kpis, 0, strlen($kpis) - 1);
        $joinSql = "dc." . $tableName;
        $time_id = "date_id";
        $my_time_id = "AGG_TABLE0.day";
        $whereSQL = " where $time_id>='$startTime' and $time_id<='$endTime'"; 
        $whereMySQL = " where $my_time_id>='$startTime' and $my_time_id<='$endTime'";
    
        $aggGroupSQL = "";
        $aggSelectSQL = "";
        $aggOrderSQL = "";
        $myGroup = "";
        if($timeDim == 'day') {
            if($locationDim == 'erbs') {
                $selectSQL = $selectSQL . " DATETIME_ID,CONVERT(char,date_id) as day,";
                $aggGroupSQL = " GROUP BY day,DATETIME_ID,BSC,";
            }else{
                $selectSQL = $selectSQL . " MOID,SESSION_ID,DATETIME_ID,CONVERT(char,date_id) as day,";
                $aggGroupSQL = " GROUP BY day,DATETIME_ID,SESSION_ID,MOID,";
            }
            $myGroup = " GROUP BY AGG_TABLE0.day,";
            $aggSelectSQL = " SELECT AGG_TABLE0.day";
            $resultText = $resultText . "day,";
            $aggOrderSQL = " ORDER BY AGG_TABLE0.day";
        }else if($timeDim == 'hour') {
            if($locationDim == 'erbs') {
                $selectSQL = $selectSQL . " DATETIME_ID,CONVERT(char,date_id) as day, hour_id as hour,";
                $aggGroupSQL = " GROUP BY day,hour,DATETIME_ID,BSC,";
            }else{
                $selectSQL = $selectSQL . " MOID,SESSION_ID,DATETIME_ID,CONVERT(char,date_id) as day, hour_id as hour,";
                $aggGroupSQL = " GROUP BY day,hour,DATETIME_ID,SESSION_ID,MOID,";
            }
            $myGroup = " GROUP BY AGG_TABLE0.day,AGG_TABLE0.hour,";
            $aggSelectSQL = " SELECT AGG_TABLE0.day,AGG_TABLE0.hour";
            $resultText = $resultText . "day,hour,";
            $aggOrderSQL = " ORDER BY AGG_TABLE0.day,AGG_TABLE0.hour";
        }else if($timeDim == 'quarter') {
            if($locationDim == 'erbs') {
                $selectSQL = $selectSQL . " DATETIME_ID,CONVERT(char,date_id) as day, hour_id as hour, min_id as minute,";
                $aggGroupSQL = " GROUP BY day,hour,minute,DATETIME_ID,BSC,";
            }else{
                $selectSQL = $selectSQL . " MOID,SESSION_ID,DATETIME_ID,CONVERT(char,date_id) as day, hour_id as hour, min_id as minute,";
                $aggGroupSQL = " GROUP BY day,hour,minute,DATETIME_ID,SESSION_ID,MOID,";
            }
            $myGroup = " GROUP BY AGG_TABLE0.day,AGG_TABLE0.hour,AGG_TABLE0.minute,";
            $aggSelectSQL = "SELECT AGG_TABLE0.day,AGG_TABLE0.hour,AGG_TABLE0.minute";
            $resultText = $resultText . "day,hour,minute,";
            $aggOrderSQL = " ORDER BY AGG_TABLE0.day,AGG_TABLE0.hour,AGG_TABLE0.minute";
        }else if($timeDim == 'hourgroup') {
            if($locationDim == 'erbs') {
                $hourcollection = Input::get('hour');
                $selectSQL = $selectSQL . " DATETIME_ID,convert(char,date_id) as day,'".$hourcollection."' as hour,";
                $aggGroupSQL = " group by day,hour,DATETIME_ID,BSC,";
            }else{
                $hourcollection = Input::get('hour');
                $selectSQL = $selectSQL . " MOID,SESSION_ID,DATETIME_ID,convert(char,date_id) as day,'".$hourcollection."' as hour,";
                $aggGroupSQL = " group by day,hour,DATETIME_ID,SESSION_ID,MOID,";
            }    
            $myGroup = " GROUP BY AGG_TABLE0.day,AGG_TABLE0.hour,";
            $aggSelectSQL = " SELECT AGG_TABLE0.day,AGG_TABLE0.hour";
            $aggOrderSQL = " order by AGG_TABLE0.day,AGG_TABLE0.hour";
            $resultText = $resultText . "day,hour,";
        }

        if($locationDim == 'city') {
            $selectSQL = $selectSQL . " '$city' as location,";
            $myGroup = $myGroup . " AGG_TABLE0.location";
            $aggGroupSQL = $aggGroupSQL . " location";
            $aggSelectSQL = $aggSelectSQL . ",AGG_TABLE0.location,";
            $resultText = $resultText . "location,";
        }else if($locationDim == 'cell') {
            $cell = Input::get('cell');
            $selectSQL = $selectSQL . " '$city' as location, CELL_NAME,";
            $myGroup = $myGroup . " AGG_TABLE0.location,AGG_TABLE0.CELL_NAME";
            $aggGroupSQL = $aggGroupSQL . " location,CELL_NAME";
            $aggSelectSQL = $aggSelectSQL . ",AGG_TABLE0.location,AGG_TABLE0.CELL_NAME,";
            $resultText = $resultText . " location,CELL_NAME,";
        }else if($locationDim == 'erbs') {
            $erbs = Input::get('erbs');
            $selectSQL = $selectSQL . " '$city' as location, BSC,";
            $myGroup = $myGroup . " AGG_TABLE0.location,AGG_TABLE0.BSC";
            $aggGroupSQL = $aggGroupSQL . " location";
            $aggSelectSQL = $aggSelectSQL . ",AGG_TABLE0.location,AGG_TABLE0.BSC,";
            $resultText = $resultText . " location,BSC,";
        }
        $inputErbs = Input::get('erbs');
        $erbs = isset($inputErbs) ? $inputErbs : "";
        if ($locationDim == "erbs") {
            if($erbs != '') {
                $erbs = "('" . str_replace(",", "','", $erbs) . "')";
                $whereSQL = $whereSQL . " and BSC in " . $erbs;
            }
        }

        $inputCell = Input::get('cell');
        $cell = isset($inputCell) ? $inputCell : "";
        if ($cell != "" && $locationDim == "cell" || $locationDim == "cellGroup") {
            $cell = "('" . str_replace(",", "','", $cell) . "')";
            $whereSQL = $whereSQL . " and CELL_NAME in " . $cell;
        }
        $inputHour = Input::get('hour');
        $inputHour = ltrim($inputHour, '[');
        $inputHour = rtrim($inputHour, ']');
        $inputHour = ltrim($inputHour, '"');
        $inputHour = rtrim($inputHour, '"');
        $inputHour = str_replace('","', ',', $inputHour);
        $hour = isset($inputHour) ? $inputHour : "";
        if ($hour != "" && ($timeDim == "hour" || $timeDim == "quarter" )) {
            $hour = "(" . $hour . ")";
            $whereSQL = $whereSQL . " and hour_id in " . $hour;
        }

        if ($hour != "" && $timeDim == "hourgroup") {
            $hour = "(" . $hour . ")";
            $whereSQL = $whereSQL . " and hour_id in " . $hour;
        }
        $inputMinute = Input::get('minute');
        $inputMinute = ltrim($inputMinute, '[');
        $inputMinute = rtrim($inputMinute, ']');
        $inputMinute = ltrim($inputMinute, '"');
        $inputMinute = rtrim($inputMinute, '"');
        $inputMinute = str_replace('","', ',', $inputMinute);
        $min = isset($inputMinute) ? $inputMinute : "";
        if ($min != "" && $timeDim == "quarter") {
            $min = "(" . $min . ")";
            $whereSQL = $whereSQL . " and min_id in " . $min;
        }
 
        $tables = file("common/txt/Tables_2G_bak.txt");
        @$tables = array_keys(array_count_values($counterMap));

        $tempTableSQL = "";
        $index = 0;
    
        foreach ($tables as $table) {
            $countersForQuery = array_keys($counterMap, $table);
            $tableSQL = $this->createTempTable_1($selectSQL, $whereSQL, $table, 
                        $countersForQuery, $aggGroupSQL, $timeDim);
            $tableSQL = $tableSQL . "as AGG_TABLE$index ";
            if ($index == 0) {
                if ($index != (sizeof($tables) - 1)) {
                    $tableSQL = $tableSQL . " left join";
                }
            } else {
                if ($timeDim == "day") {
                    if($locationDim == 'erbs') {
                        $tableSQL = $tableSQL . "on AGG_TABLE0.day = AGG_TABLE$index.day 
                            and AGG_TABLE0.DATETIME_ID=AGG_TABLE$index.DATETIME_ID 
                            and AGG_TABLE0.BSC=AGG_TABLE$index.BSC"; 
                    }else{
                        $tableSQL = $tableSQL . "on AGG_TABLE0.day = AGG_TABLE$index.day 
                            and AGG_TABLE0.DATETIME_ID=AGG_TABLE$index.DATETIME_ID 
                            and AGG_TABLE0.SESSION_ID=AGG_TABLE$index.SESSION_ID 
                            and AGG_TABLE0.MOID=AGG_TABLE$index.MOID";
                    }
                } else if ($timeDim == "hour") {
                    if($locationDim == 'erbs') {
                        $tableSQL = $tableSQL . "on AGG_TABLE0.day = AGG_TABLE$index.day 
                            and AGG_TABLE0.hour = AGG_TABLE$index.hour
                            and AGG_TABLE0.DATETIME_ID=AGG_TABLE$index.DATETIME_ID 
                            and AGG_TABLE0.BSC=AGG_TABLE$index.BSC";
                    }else{
                        $tableSQL = $tableSQL . "on AGG_TABLE0.day = AGG_TABLE$index.day 
                            and AGG_TABLE0.hour = AGG_TABLE$index.hour
                            and AGG_TABLE0.DATETIME_ID=AGG_TABLE$index.DATETIME_ID 
                            and AGG_TABLE0.SESSION_ID=AGG_TABLE$index.SESSION_ID 
                            and AGG_TABLE0.MOID=AGG_TABLE$index.MOID";
                    }
                } else if ($timeDim == "quarter") {
                    if($locationDim == 'erbs') {
                        $tableSQL = $tableSQL . "on AGG_TABLE0.day = AGG_TABLE$index.day 
                            and AGG_TABLE0.hour = AGG_TABLE$index.hour 
                            and AGG_TABLE0.minute = AGG_TABLE$index.minute 
                            and AGG_TABLE0.DATETIME_ID=AGG_TABLE$index.DATETIME_ID 
                            and AGG_TABLE0.BSC=AGG_TABLE$index.BSC";
                    }else{
                        $tableSQL = $tableSQL . "on AGG_TABLE0.day = AGG_TABLE$index.day 
                            and AGG_TABLE0.hour = AGG_TABLE$index.hour 
                            and AGG_TABLE0.minute = AGG_TABLE$index.minute 
                            and AGG_TABLE0.DATETIME_ID=AGG_TABLE$index.DATETIME_ID 
                            and AGG_TABLE0.SESSION_ID=AGG_TABLE$index.SESSION_ID 
                            and AGG_TABLE0.MOID=AGG_TABLE$index.MOID";
                    }   
                } else if ($timeDim == "hourgroup") {
                    if($locationDim == 'erbs') {
                        $tableSQL = $tableSQL . "on AGG_TABLE0.day = AGG_TABLE$index.day 
                            and AGG_TABLE0.hour = AGG_TABLE$index.hour 
                            and AGG_TABLE0.DATETIME_ID=AGG_TABLE$index.DATETIME_ID 
                            and AGG_TABLE0.BSC=AGG_TABLE$index.BSC";
                    }else{
                        $tableSQL = $tableSQL . "on AGG_TABLE0.day = AGG_TABLE$index.day 
                            and AGG_TABLE0.hour = AGG_TABLE$index.hour 
                            and AGG_TABLE0.DATETIME_ID=AGG_TABLE$index.DATETIME_ID 
                            and AGG_TABLE0.SESSION_ID=AGG_TABLE$index.SESSION_ID 
                            and AGG_TABLE0.MOID=AGG_TABLE$index.MOID"; 
                    }
                }

                if ($index != (sizeof($tables) - 1)) {
                    $tableSQL = $tableSQL . " left join ";
                }
            }
            $tempTableSQL = $tempTableSQL . $tableSQL;
            $index++;
        }
        $sql = $aggSelectSQL . $kpis . " FROM " 
            . $tempTableSQL . $myGroup . $aggOrderSQL; 
        return $sql;
    }


    protected function convertInternalCounter($counterName, $index)
    {
        $SQL = "sum(case DCVECTOR_INDEX when $index then $counterName else 0 end)";
        return str_replace("\n", "", $SQL);
    }

    protected function createTempTable_1($selectSQL, $whereSQL, 
        $tableName, $counters, $groupSQL, $timeDim
    ) {
        foreach ($counters as $counter) {
            $counter = trim($counter);
            $counterName = $counter;
       
            $selectSQL = $selectSQL . " sum(" . $counter . ") as '$counterName',";
        }
        $selectSQL = substr($selectSQL, 0, strlen($selectSQL) - 1);
        return "($selectSQL from dc.$tableName $whereSQL $groupSQL)";
    }

    protected function parseCity($city)
    {
        $result = array();
        foreach ($city as $cityRow) {
            if ($cityRow['checked'] === true) {
                $result[] = $cityRow['text'];
            }
        }
        return $result;
    }



    protected function formulaAddSum($arr, $formula)
    {
        if(count($arr) % 2 != 0 && count($arr) < 2) {
            return false;
        }
        $comStr = '';
        if(count($arr) == 2) {
            $comStr = "SUM" . $formula;
        }else if(count($arr) > 2) {
            for($i=0; $i<count($arr)-1; $i++) {
                if($i % 2 == 0 && $i == 0) {
                    $comStr = $comStr . "SUM" . substr($formula, $arr[$i], $arr[$i+1] - $arr[$i] + 1);
                }else if($i % 2 == 0 && $i != 0 && $i != count($arr) - 2) {
                    $comStr = $comStr . "SUM" . substr($formula, $arr[$i] + 1, $arr[$i+1] - $arr[$i]);
                }
                if($i % 2 == 1) {
                    $comStr = $comStr . $formula[$arr[$i+1]];
                }
                if($i == count($arr) - 2) {
                    $comStr = $comStr . "SUM" . substr($formula, $arr[$i]+1, $arr[$i+1] - $arr[$i] + 1);
                }
            }
        }
        return $comStr;
    }

}

    



