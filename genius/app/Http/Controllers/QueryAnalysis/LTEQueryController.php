<?php

namespace App\Http\Controllers\QueryAnalysis;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Common\DataBaseConnection;

//use App\Http\Controllers\Controller;

use PDO;
// use App\Http\Controllers\Controller;

// use Illuminate\Http\Request;
// use Illuminate\Http\Response;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\countersDB;
// use Illuminate\Support\Facades\Session;
// use Illuminate\Support\Facades\Input;
// use Illuminate\Support\Facades\Auth;

// use App\Task;
// use App\User;
// use App\DatabaseConn;

class LTEQueryController extends Controller
{
    public function init(){
        return view('QueryAnalysis.LTEQuery');
        //return view('QueryAnalysis.LTEQuery')."<h1>test</h1>";
    }

    public function uploadFile(){
        $filename = $_FILES['fileImport']['tmp_name'];    
        if (empty ($filename)) {         
            echo '请选择要导入的文件！'; 
            exit;     
        }     
        if(file_exists("common/files/" . $_FILES['fileImport']['name'])){
           unlink("common/files/" . $_FILES['fileImport']['name']);
        }
        $result = move_uploaded_file($filename,
                "common/files/" . $_FILES['fileImport']['name']);

        setlocale(LC_ALL,NULL);
        $files = file("common/files/" . $_FILES['fileImport']['name']);
        foreach ($files as $txt) {
            print_r($txt);
        }
    }

    public function getLTETreeData(){
        $users = DB::select('select distinct user from template');
        $arrUser = array();
        $items = array();
        $itArr = array();
        foreach ($users as $user) {
            $userStr = $user->user;
            $templateNames = DB::table('template')->where('user', '=', $userStr)->get();
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

    public function searchLTETreeData() {
        $inputData = Input::get('inputData');
        $inputData = "%".$inputData."%";
        $users = DB::table('template')->where('templateName', 'like', $inputData)->get();
        $items = array();
        foreach ($users as $user) {
            $templateName = '{"text":"'.$user->templateName.'","value":"'.$user->templateName.'"}';
            array_push($items, $templateName);
        }
        return response()->json($items);
    }

    // public function getAllCity(){
    //      $databaseConns = DB::select('select * from databaseconn');
    //     $items = array();
    //     foreach($databaseConns as $databaseConn){
    //         $city = '{"text":"'.$databaseConn->cityChinese.'","value":"'.$databaseConn->connName.'"}';
    //         array_push($items, $city);
    //     }
    //     return response()->json($items);
    // }
    public function getAllCity(){
        $cityClass = new DataBaseConnection();
        return $cityClass->getCityOptions();
    }

    public function getFormatAllSubNetwork(){
        $citys = Input::get('citys');
        $format = Input::get('format');
        $items = array();
        $subArr = array();
        foreach ($citys as $city) {
            $databaseConns = DB::table('databaseconn')->where('cityChinese', '=', $city)->get();
            foreach ($databaseConns as $databaseConn) {
                if($format == 'TDD'){
                    $subStr = $databaseConn->subNetwork;
                }else if($format == 'FDD'){
                    $subStr = $databaseConn->subNetworkFdd;
                }           
                $subArr = explode(',', $subStr);
                foreach ($subArr as $sub) {
                    $city = '{"text":"'.$sub.'","value":"'.$sub.'"}';
                    array_push($items, $city);
                } 
                //$subStr = $databaseConns[0]->subNetwork; 
                // $subArr = explode(',', $subStr);
                // foreach ($subArr as $sub) {
                //  $city = '{"text":"'.$sub.'","value":"'.$sub.'"}';
                //  array_push($items, $city);
            }
        }
        return response()->json($items);
    }

    public function getAllSubNetwork(){
        $citys = Input::get('citys');
        $format = Input::get('format');
        $items = array();
        $subArr = array();
        foreach ($citys as $city) {
            $databaseConns = DB::table('databaseconn')->where('cityChinese', '=', $city)->get();
            foreach ($databaseConns as $databaseConn) {
                if($format == 'TDD'){
                    $subStr = $databaseConn->subNetwork;
                }else if($format == 'FDD'){
                    $subStr = $databaseConn->subNetworkFdd;
                }     
                //$subStr = $databaseConn->subNetwork;
                $subArr = explode(',', $subStr);
                foreach ($subArr as $sub) {
                    $city = '{"text":"'.$sub.'","value":"'.$sub.'"}';
                    array_push($items, $city);
                } 
                //$subStr = $databaseConns[0]->subNetwork; 
                // $subArr = explode(',', $subStr);
                // foreach ($subArr as $sub) {
                //  $city = '{"text":"'.$sub.'","value":"'.$sub.'"}';
                //  array_push($items, $city);
            }
        }
        return response()->json($items);
    }

    public function templateQuery(){
        $template    = Input::get('template');
        $locationDim = Input::get('locationDim');
        $timeDim     = Input::get('timeDim');
        $startTime   = Input::get('startTime');
        $endTime     = Input::get('endTime');
        $hour        = Input::get('hour');
        $minute      = Input::get('minute');  
        $city        = Input::get('city');
        $subNetwork  = Input::get('subNet');
        $erbs        = Input::get('erbs');
        $cell        = Input::get('cell');
        $format      = Input::get('format');
        $action      = Input::get('action');
        $checkStyle  = Input::get('style');
        //print_r($subNetwork);return;
        $hour   = rtrim($hour, "]");
        $hour   = ltrim($hour, "[");
        $minute = rtrim($minute, ']');
        $minute = ltrim($minute, '[');

        $result = array();
        
        $LoadCounters = new LoadCounters();
        if ($format == 'FDD') {                                 //当Counters.txt不存在时。
            $counters = $LoadCounters->loadCounters_FDD();
        } else {
            $counters = $LoadCounters->loadCounters();
        }

        $aggTypes = $LoadCounters->loadAggTypes();
        
        //$citys = json_decode($city);
        $citysChinese = json_decode($city);
        $cityPY = new DataBaseConnection();
        $citys = $cityPY->getConnCity($citysChinese);

        $LTEQuery = new LTEQuery();
        $location = $LTEQuery->parseLocation($city, $subNetwork);

        $dsn = "mysql:host=localhost;dbname=mongs";
        // $db = DB::connection()->getpdo();
        // $db->dsn = $dsn;
        // $db->username = 'root';
        // $db->password = 'mongs';
        $dsn = "mysql:host=localhost;dbname=mongs";
        $db = new PDO($dsn, 'root', 'mongs');

        $kpis = $LTEQuery->getKpis($db, $template);

        $resultText = "";
        $items = array();
        $csvContent = "";
        $startQueryTime = time();
       //$city = '';
        foreach ($citys as $city) {
            $sql = "select host,port,dbName,userName,password from databaseconn where connName='" .$city . "'";
            $res = $db->query($sql);
            $row = $res->fetch();
            $host = $row["host"];
            $port = $row["port"];
            $dbName = $row["dbName"];
            $userName = $row["userName"];
            $password = $row["password"];

            $subNets = "";

            if ($locationDim !== "city") {
                if($format == 'TDD'){
                    $subNets = $LTEQuery->getSubNetsFromLoc($location, $city);
                }else if($format == 'FDD'){
                    $subNets = $LTEQuery->getSubNetsFromLocFDD($db, $city);
                }              
            } else {
                if ($format == 'FDD') {           
                    $subNets = $LTEQuery->getSubNetsFDD($db, $city, $format);
                } else {
                    //$subNets = $LTEQuery->getSubNets($db, $city);
                    $subNets = $LTEQuery->getSubNetsFromLoc($location, $city);
                }
            }
           // print_r($subNets);
            //return;
            $pmDbDSN = "dblib:host=" . $host . ":" . $port . ";dbname=" . $dbName;
            // $pmDB = DB::connection()->getpdo();
            // $pmDB->pmDbDSN = $pmDbDSN;
            // $pmDB->username = $userName;
            // $pmDB->password = $password;
            $pmDB = new PDO($pmDbDSN, $userName, $password);


            $resultText = "";

            $local_flag = $LTEQuery->is_local_query($template, $startTime, $timeDim, $format, $checkStyle);
            if($local_flag){
                if($city == 'changzhou1'){
                    continue;
                }
            }
            $queryResult = $LTEQuery->queryTemplate($db, $pmDB, $counters, $timeDim, $locationDim, $startTime,$endTime, $city, $subNets, $resultText, $aggTypes, $format,$local_flag);
            //print_r($queryResult);return;
            if($queryResult == 'NOTFINDLINE'){
                $result['error'] = 'NOTFINDLINE';
                echo json_encode($result);
                return;
            }

            foreach ($queryResult['rows'] as $qr) { 
                array_push($items, $qr);
            }
            $result['text'] = $resultText . $kpis['names'];
            unset($pmDB);     
        }
        $result['total'] = count($items);
        $result['rows'] = $items;
        $result['result'] = 'true';

        $filename = Input::get('template');
        $filename = "common/files/" . $filename . date('YmdHis') . ".csv";  
        $this->resultToCSV2($result, $filename);
        $result['filename'] = $filename;
        if (count($items) > 1000) {
            $result['rows'] = array_slice($items, 0, 1000);
        }

        echo json_encode($result);
    }

    protected function resultToCSV2($result, $filename)
    {
        $csvContent = mb_convert_encoding($result['text'] . "\n", 'gb2312', 'utf-8');
        $fp = fopen($filename, "w");
        fwrite($fp, $csvContent);
        foreach ($result['rows'] as $row) {
            fputcsv($fp, $row);
        }
        fclose($fp);
    }
}

class LoadCounters{
    public function loadAggTypes()
    {
        $aggTypeDefs = file("common/txt/AggTypeDefine.txt");
        $aggTypes = array();

        foreach ($aggTypeDefs as $aggTypeDef) {
            $aggType = explode("=", $aggTypeDef);
            $aggTypes[$aggType[0]] = $aggType[1];
        }
        return $aggTypes;
    }

    protected function loadCountersFromFile() {    //Counters.txt存在时
        $result = array();
        $lines = file("common/txt/Counters.txt");
        foreach($lines as $line){
            $pair = explode("=",$line);
            $result[$pair[0]]=$pair[1];
        }
        return $result;
    }

    protected function loadCountersFromDB() {   //Counters.txt不存在时。
        $port = '2640';
        $dbName = 'dwhdb';
        $dsn = "dblib:host=".$host.":".$port.";dbname=".$dbName;
        $db = new PDO($dsn, 'dcbo', 'dcbo');
        $tables = file("common/txt/Tables.txt");
        $result = array();
        $out = "";
        foreach ($tables as $table) {
        //Mysql For test
        //$sql = "desc $table";
        //Sybase
        $table  = trim($table);
        if($table == "") {
            continue;   
        }
        $table = substr($table,3);
            $sql = "select a.name as Field from dbo.syscolumns a, dbo.sysobjects b where a.id=b.id and b.name='$table"."_day'";
            $res = $db->query($sql, PDO::FETCH_ASSOC);
            foreach ($res as $row) {
                if (stripos($row['Field'], "pm") === false) {
                    continue;
                }
                $result[strtolower($row['Field'])] = $table;
                $out = $out . strtolower($row['Field']) . "=" . $table . "\n";
            }
        }
        file_put_contents("common/txt/Counters.txt", $out);
        return $result;
    }

    protected function loadCountersFromFile_FDD() { //Counters_FDD.txt存在时
        $result = array();
        $lines = file("common/txt/Counters_FDD.txt");
        foreach($lines as $line){
            $pair = explode("=",$line);
            $result[$pair[0]]=$pair[1];
        }
        //print_r($result);
        return $result;
    }

    protected function loadCountersFromDB_FDD() {   //Counters_FDD.txt不存在时。
        $host = '10.40.57.148';
        $port = '2640';
        $dbName = 'dwhdb';
        $dsn = "dblib:host=".$host.":".$port.";dbname=".$dbName;
        $db = new PDO($dsn, 'dcbo', 'dcbo');
        $tables = file("common/txt/Tables_FDD.txt");
        $result = array();
        $out = "";
        foreach ($tables as $table) {
                //Mysql For test
                //$sql = "desc $table";
                //Sybase
            $table  = trim($table);
            if($table == "") {
                continue;   
            }
            $table = substr($table,3);
            $sql = "select a.name as Field from dbo.syscolumns a, dbo.sysobjects b where a.id=b.id and b.name='$table"."_day'";
            $res = $db->query($sql, PDO::FETCH_ASSOC);
            foreach ($res as $row) {
                if (stripos($row['Field'], "pm") === false) {
                    continue;
                }
                $result[strtolower($row['Field'])] = $table;
                $out = $out . strtolower($row['Field']) . "=" . $table . "\n";
            }
        }
        file_put_contents("common/txt/Counters_FDD.txt", $out);
        return $result;
    }

    public function loadCounters() {
        $result = array();
        if (file_exists("common/txt/Counters.txt")) {
            $result = $this->loadCountersFromFile();
        } else {
            $result = $this->loadCountersFromDB();
        }
        return $result;
    }

    public function loadCounters_FDD() {
        $result = array();
        if (file_exists("common/txt/Counters_FDD.txt")) {
            $result = $this->loadCountersFromFile_FDD();
        } else {
            $result = $this->loadCountersFromDB_FDD();
        }
        return $result;
    }
}

class LTEQuery{
    protected function parserKPI($kpi, $counters, &$counterMap, &$nosum_map)
    {
        $pattern = "/[\(\)\+\*-\/]/";
        $columns = preg_split($pattern, $kpi);    
        $pattern_nosum="/(max|min|avg)\((.*)\)/";
        $matches = array();
        foreach ($columns as $column) {
            $column = trim($column);
            $counterName = $column;
            if (stripos($counterName, "pm") === false) {
                continue;
            }
            if (stripos($counterName, "_") !== false) {
                $elements = explode("_", $counterName);
                $counterName = $elements[0];
            }
            //$table = $counters[strtolower($counterName)];
            if(array_key_exists(strtolower($counterName),$counters)){
                $table = $counters[strtolower($counterName)];
            }else{
                //print_r(strtolower($counterName));
                return strtolower($counterName);
            }
            
            $timeDim = Input::get('timeDim');
            $table = ($timeDim == "day") ? $table . "_day" : $table . "_raw";
            if (preg_match($pattern_nosum,$kpi,$matches)){
                $counterMap[$kpi]=$table;
                $nosum_map[$kpi]="agg".count($nosum_map);             
                break;
            }
            if (!array_key_exists($column, $counterMap)) {
                $counterMap[$column] = $table;
            }
        }
    }

    protected function getCitySQL($tableName)
    {
        return "substring(SN,charindex('=',substring(SN,32,25))+32,charindex(',',substring(SN,32,25))-charindex('=',substring(SN,32,25))-1)";
    }

    protected function getAggType($aggTypes, $counterName)
    {
        if (!array_key_exists($counterName, $aggTypes)) {
            return "sum";
        }
        return trim($aggTypes[$counterName]);
    }

    protected function convertInternalCounter($counterName, $index)
    {
        $SQL = "sum(case DCVECTOR_INDEX when $index then $counterName else 0 end)";
        return str_replace("\n", "", $SQL);
    }

    function createTempTable($locationDim, $timeDim, $selectSQL, $whereSQL, $tableName, $counters,
    $groupSQL, $aggTypes,$local_flag,$nosum_map,$counterMap,$format)
    {
        $tables = array_keys(array_count_values($counterMap));
        $flag = 'true';
        foreach ($tables as $table) {
            if(trim(substr($table, 0, strlen($table) - 4)) == 'DC_E_CPP_GIGABITETHERNET'){
                $flag = 'false';
            }
        }//print_r($table.$flag);return;
        if(!$local_flag){
            if($format == 'TDD'){
                if($flag == 'false'){
                    $selectSQL .= "COUNT(DISTINCT(ERBS)) AS cellNum,";
                }else{
                    $selectSQL .= "COUNT(DISTINCT(EutranCellTDD)) AS cellNum,";
                } 
            }else if($format == 'FDD'){
                $selectSQL .= "COUNT(DISTINCT(ERBS)) AS cellNum,";
            }   
                    
        }
        // if(!$local_flag){
        //     $selectSQL .= "COUNT(DISTINCT(EutranCellTDD)) AS cellNum,";
        // }
        $pattern_nosum="/(max|min|avg)\((.*)\)/";
        foreach ($counters as $counter) {
            $counter = trim($counter);
            $counterName = $counter;
            if (preg_match($pattern_nosum,$counter)){
                $counterName=$nosum_map[$counter];
            }
            else if (stripos($counter, "_") !== false) {
                $elements = explode("_", $counter);
                $name = $elements[0];
                $index = $elements[1];
                $counter = $this->convertInternalCounter($name, $index);
            } else {
                $aggType = $this->getAggType($aggTypes, $counter);
                $counter = "$aggType($counter)";
            }
            $selectSQL = $selectSQL . $counter . " as '$counterName',";
        }//print_r($selectSQL);return;
        $selectSQL = substr($selectSQL, 0, strlen($selectSQL) - 1);
        if (!$local_flag) {
            return "($selectSQL from dc.$tableName $whereSQL $groupSQL)";
        } else {
            $tableName = substr("$tableName", 0, strripos("$tableName", "_"));
            if ($locationDim == 'city') {            
                $tableName = trim($tableName) . "_HOUR_CITY";           
            } elseif ($locationDim == 'subNetwork' || $locationDim == 'subNetworkGroup') {
                $tableName = trim($tableName) . "_HOUR_SUBNET";
            } elseif ($locationDim == 'erbs') {
                $tableName = trim($tableName) . "_HOUR_ERBS";
            }
            else {        
                $tableName = trim($tableName) . "_HOUR";           
            }
            return "($selectSQL from $tableName $whereSQL $groupSQL)";

           /* $tableName = substr("$tableName", 0, strripos("$tableName", "_"));
            $tableName = trim($tableName) . "_HOUR";
            return "($selectSQL from $tableName $whereSQL $groupSQL)";*/
        }
    }

    protected function createSQL($localDB, $pmDB, $kpiName, $counters, $timeDim, $locationDim,
    $startTime, $endTime, $city, $subNetwork, &$resultText, $aggTypes,$local_flag) 
    {
        $kpiset = "(" . $kpiName . ")";
        $location = "('" . str_replace(",", "','", $subNetwork) . "')";
        $kpis = "";
        $kpiNameStr = $kpiName . ',';
        $queryFormula = "select kpiName,kpiFormula,kpiPrecision,instr('$kpiNameStr',CONCAT(id,',')) as sort from kpiformula where id in " .$kpiset . " order by sort";
        //$queryFormula = "select kpiName,kpiFormula,kpiPrecision,instr('$kpiName',id) as sort from kpiformula where id in " .$kpiset . " order by sort";
        $index = 0;
        $tables = array();
        $tableName = current($tables);
        $selectSQL = "select";
        $counterMap = array();
        $nosum_map = array();
        $pattern_nosum = "/(max|min|avg)\((.*)\)/";
        $matches = array();
        foreach ($localDB->query($queryFormula) as $row) {
            $kpi = $row['kpiFormula'];
            $this->parserKPI($kpi, $counters, $counterMap, $nosum_map);
            if (preg_match($pattern_nosum,$kpi,$matches)){
                $kpi = $nosum_map[$kpi];
            }
            $formula = "cast(" . $kpi . " as decimal(18," . $row['kpiPrecision'] ."))";
            $kpis = $kpis . $formula . " as kpi" . $index . ",";
            $index++;
        }
        $kpis = substr($kpis, 0, strlen($kpis) - 1);
        $citySQL = $this->getCitySQL($tableName);
        if ($local_flag) {
            if($locationDim == 'city') {
                $citySQL = 'city';
            }else {
                $citySQL = 'subNetwork';
            }
        } 
        $joinSql = "dc." . $tableName;
        $time_id = "date_id";

        if($local_flag) {
            if($locationDim == 'city'){
                //$whereSQL = " where $time_id>='$startTime' and $time_id<='$endTime' and $citySQL in ('$city')";
                $whereSQL = " where $time_id>='$startTime' and $time_id<='$endTime'";
            }else{
                $whereSQL = " where $time_id>='$startTime' and $time_id<='$endTime' and $citySQL in $location";
            }
        }else{
            $whereSQL = " where $time_id>='$startTime' and $time_id<='$endTime' and $citySQL in $location";
        }

        $aggGroupSQL = "";
        $aggSelectSQL = "";
        $aggOrderSQL = "";

        if($timeDim == "daygroup"){
            $selectSQL = $selectSQL . " 'ALLDAY' as day,";
            $aggGroupSQL = "group by DAY,";
            $aggSelectSQL = "select AGG_TABLE0.day";
            $aggOrderSQL = $aggOrderSQL;
            $resultText = $resultText . "day,";
            //$aggSelectSQL = "select AGG_TABLE0.day,AGG_TABLE0.cellNum";
            //$resultText = $resultText . "day,cellNum,";
        }else if ($timeDim == "day") {
            if($local_flag){ 
                $selectSQL = $selectSQL . " date_id as day,";             
                $aggSelectSQL = "select AGG_TABLE0.day";
                $resultText = $resultText . "day,";
            }else{ 
                $selectSQL = $selectSQL . " convert(char(10),date_id) as day,";             
                $aggSelectSQL = "select AGG_TABLE0.day,AGG_TABLE0.cellNum";
                $resultText = $resultText . "day,cellNum,";
            }
            //$selectSQL = $selectSQL . " convert(char(10),date_id) as day,";
            $aggGroupSQL = "group by date_id,";
            #$aggSelectSQL = "select AGG_TABLE0.day";
            $aggOrderSQL = "order by AGG_TABLE0.day";
            #$resultText = $resultText . "day,";
            // $aggSelectSQL = "select AGG_TABLE0.day,AGG_TABLE0.cellNum";
            // $resultText = $resultText . "day,cellNum,";
        }else if ($timeDim == "hour") {
            if ($local_flag) {
                $selectSQL = $selectSQL . " date_id as day,hour_id as hour,";
                $aggSelectSQL = "select AGG_TABLE0.day,AGG_TABLE0.hour";
                $resultText = $resultText . "day,hour,";
            } else {
                $selectSQL = $selectSQL . " convert(char(10),date_id) as day,hour_id as hour,";
                $aggSelectSQL = "select AGG_TABLE0.day,AGG_TABLE0.hour,AGG_TABLE0.cellNum";
                $resultText = $resultText . "day,hour,cellNum,";
            }
            $aggGroupSQL = "group by date_id,hour_id,";
            $aggOrderSQL = "order by AGG_TABLE0.day,AGG_TABLE0.hour";
        }else if ($timeDim == "quarter") {
            $selectSQL = $selectSQL ." convert(char,date_id) as day,hour_id as hour,min_id as minute,";
            $aggGroupSQL = "group by date_id,hour_id,min_id,";
            //$aggSelectSQL = "select AGG_TABLE0.day,AGG_TABLE0.hour,AGG_TABLE0.minute";
            $aggOrderSQL = "order by AGG_TABLE0.day,AGG_TABLE0.hour,AGG_TABLE0.minute";
            //$resultText = $resultText . "day,hour,minute,";
            $aggSelectSQL = "select AGG_TABLE0.day,AGG_TABLE0.hour,AGG_TABLE0.minute,AGG_TABLE0.cellNum";
            $resultText = $resultText . "day,hour,minute,cellNum,";
        }else if ($timeDim == "hourgroup") {
            //$hourcollection = 'AllHour';
            // $hourcollection = Input::get('hour');
            // $hourcollection = ltrim($hourcollection, '[');
            // $hourcollection = rtrim($hourcollection, ']');
            // $hourcollection = '(' . $hourcollection . ')';

            $hourcollection = Input::get('hour'); 
            if($hourcollection=='null'){                
                $hourcollection = 'AllHour';
            }else{
                 $hourcollection = ltrim($hourcollection, '["');
                  $hourcollection = rtrim($hourcollection, '"]');               
                  $hourcollection = implode(',', explode('","', $hourcollection));           
            }

            if ($local_flag) {
                $selectSQL = $selectSQL . " date_id as day,\"$hourcollection\" as hour,";
                $aggSelectSQL = "select AGG_TABLE0.day,AGG_TABLE0.hour";
                $resultText = $resultText . "day,hour,";
            } else {
                $selectSQL = $selectSQL . " convert(char(10),date_id) as day,\"$hourcollection\" as hour,";
                $aggSelectSQL = "select AGG_TABLE0.day,AGG_TABLE0.hour,AGG_TABLE0.cellNum";
                $resultText = $resultText . "day,hour,cellNum,";
            }
            $aggGroupSQL = "group by date_id,hour,";
            //$aggSelectSQL = "select AGG_TABLE0.day,AGG_TABLE0.hour";
            $aggOrderSQL = "order by AGG_TABLE0.day,AGG_TABLE0.hour";
            //$resultText = $resultText . "day,hour,";
        }

        if ($locationDim == "city") {
            $selectSQL = $selectSQL . " '$city' as location,";
            $loc = "SN";
            $aggGroupSQL = $aggGroupSQL . "location";
            $aggSelectSQL = $aggSelectSQL . ",AGG_TABLE0.location,";
            $resultText = $resultText . "location,";
        }else if($locationDim == "subNetworkGroup"){
            $selectSQL = $selectSQL . "\"$subNetwork\" as location,";
            $loc = "SN";
            $aggGroupSQL = $aggGroupSQL . "location";
            $aggSelectSQL = $aggSelectSQL . ",AGG_TABLE0.location,";
            $resultText = $resultText . "location,";
        }else if ($locationDim == "subNetwork") {
            $selectSQL = $selectSQL . "$citySQL as location,";
            $loc = "SN";
            $aggGroupSQL = $aggGroupSQL . "location";
            $aggSelectSQL = $aggSelectSQL . ",AGG_TABLE0.location,";
            $resultText = $resultText . "location,";
        }else if ($locationDim == "erbs") {
            $selectSQL = $selectSQL . "$citySQL as subNet,erbs as location,";
            $loc = "ERBS";
            $aggSelectSQL = $aggSelectSQL . ",AGG_TABLE0.subNet,AGG_TABLE0.location,";
            if ($local_flag) {
                $aggGroupSQL = $aggGroupSQL . "subNetwork,location";
            } else {
                $aggGroupSQL = $aggGroupSQL . "SN,location";
            }
            $resultText = $resultText . "subNet,location,";
        }else if ($locationDim == "cell") {
            if ($local_flag) {
                //$selectSQL = $selectSQL . "$citySQL as subNet,cell as location,";
                $selectSQL = $selectSQL . "$citySQL as subNet,erbs as site,cell as location,";
            } else {
                //$selectSQL = $selectSQL . "$citySQL as subNet,EutranCellTDD as location,";
                $selectSQL = $selectSQL . "$citySQL as subNet,substring(substring(SN,charindex (',', substring(SN, 32, 25)) + 32),11,25) as site,EutranCellTDD as location,";
            }
            $loc = "EutranCellTDD";
            //$aggSelectSQL = $aggSelectSQL . ",AGG_TABLE0.subNet,AGG_TABLE0.location,";
            $aggSelectSQL = $aggSelectSQL . ",AGG_TABLE0.subNet,AGG_TABLE0.site,AGG_TABLE0.location,";
            if ($local_flag) {
                $aggGroupSQL = $aggGroupSQL . "subNetwork,location";
            } else {
                $aggGroupSQL = $aggGroupSQL . "SN,location";
            }
            //$resultText = $resultText . "subNet,location,";
            $resultText = $resultText . "subNet,site,location,";
        } else if($locationDim == "cellGroup") {
            //$cellInput = Input::get('cell');
            //$cellCollection = $cellInput == "" ? "All" : $cellInput;
            //$aggSelectSQL = $aggSelectSQL . ",'$cellCollection' as cellGroup,";
            //$resultText = $resultText . "cellGroup,";
            $aggSelectSQL = $aggSelectSQL.",";
            $resultText = $resultText;
            $aggGroupSQL = substr($aggGroupSQL, 0, strlen($aggGroupSQL) - 1);
            //print_r($aggGroupSQL);return;
        }
        //Create where sql.
        //$erbs = isset($_POST['erbs']) ? $_POST['erbs'] : "";
        $inputErbs = Input::get('erbs');
        $erbs = isset($inputErbs) ? $inputErbs : "";   
        if ($erbs != "" && $locationDim == "erbs") {
            $erbs = "('" . str_replace(",", "','", $erbs) . "')";
            $whereSQL = $whereSQL . " and erbs in " . $erbs;
        }

        //$cell = isset($_POST['cell']) ? $_POST['cell'] : "";
        $inputCell = Input::get('cell');
        $cell = isset($inputCell) ? $inputCell : "";
        //$cell = "LF32F01A";
        if ($cell != "" && $locationDim == "cell" || $locationDim == "cellGroup") {
            $cell = "('" . str_replace(",", "','", $cell) . "')";
            if ($local_flag) {
                $whereSQL = $whereSQL . " and cell in " . $cell;
            } else {
                $whereSQL = $whereSQL . " and EutranCellTDD in " . $cell;
            }
        }

        //$hour = isset($_POST['hour']) ? $_POST['hour'] : "";
        $inputHour = Input::get('hour');
        $inputHour = ltrim($inputHour, '["');
        $inputHour = rtrim($inputHour, '"]');
        $inputHour = implode(",", explode('","', $inputHour));
        $hour = isset($inputHour) ? $inputHour : "";
        if ($hour != 'null' && ($timeDim == "hour" || $timeDim == "quarter" || $timeDim ==
            "hourgroup")) {
            $hour = "(" . $hour . ")";
            $whereSQL = $whereSQL . " and hour_id in " . $hour;
        }

        //$min = isset($_POST['minute']) ? $_POST['minute'] : "";
        $inputMinute = Input::get('minute');
        $inputMinute = ltrim($inputMinute, '["');
        $inputMinute = rtrim($inputMinute, '"]');
        $inputMinute = implode(",", explode('","', $inputMinute));
        $min = isset($inputMinute) ? $inputMinute : "";
        if ($min != 'null' && $timeDim == "quarter") {
            $min = "(" . $min . ")";
            $whereSQL = $whereSQL . " and min_id in " . $min;
        }

        $templateNameCheck = Input::get('template');
        //$aggGroupSQL = "group by collecttime,location";
        $tables = array_keys(array_count_values($counterMap));
        //print_r(substr($tables[0], 0, strlen($tables[0]) - 4));
        if (count($tables) == 1) {
            $currTable = $tables[0];

            if (trim(substr($currTable, 0, strlen($currTable) - 4)) ==
                "DC_E_ERBS_EUTRANCELLRELATION"  && $timeDim != 'daygroup'/*&& $timeDim != 'hourgroup'*//*&&$timeDim!='day'*/) {
                $aggSelectSQL = $aggSelectSQL . "AGG_TABLE0.relation,";
                $selectSQL = $selectSQL . "EUtranCellRelation as relation,";
                $aggGroupSQL = $aggGroupSQL . ",relation";
                $resultText = $resultText . "EUtranCellRelation,";
            } else if (trim(substr($currTable, 0, strlen($currTable) - 4)) =="DC_E_ERBS_GERANCELLRELATION" && $timeDim != 'daygroup' &&$timeDim != 'hourgroup'&&$templateNameCheck != '2G邻区切换统计(不含GeranCellRelation)'/*&&$timeDim!='day'*/) {
                $aggSelectSQL = $aggSelectSQL . "AGG_TABLE0.relation,";
                $selectSQL = $selectSQL . "GeranCellRelation as relation,";
                $aggGroupSQL = $aggGroupSQL . ",relation";
                $resultText = $resultText . "GeranCellRelation,";
            }
        }

        $format = Input::get('format');
        //print_r($format);return;

        $aggSelectSQL = $aggSelectSQL . $kpis;
        $tempTableSQL = "";
        $index = 0;
        foreach ($tables as $table) {
            $countersForQuery = array_keys($counterMap, $table);
            $tableSQL = $this->createTempTable($locationDim, $timeDim, $selectSQL, $whereSQL, $table, $countersForQuery,
                $aggGroupSQL, $aggTypes, $local_flag,$nosum_map,$counterMap,$format);
            //print_r($tableSQL);return;
            $tableSQL = $tableSQL . "as AGG_TABLE$index ";
            if ($index == 0) {
                if ($index != (sizeof($tables) - 1)) {
                    $tableSQL = $tableSQL . " left join";
                }
            } else {
                if ($timeDim == "daygroup") {
                    $tableSQL = $tableSQL . "on AGG_TABLE0.location = AGG_TABLE$index.location";
                    //$tableSQL = $tableSQL;
                }else
                if ($timeDim == "day" || $timeDim == 'daygroup') {
                    $tableSQL = $tableSQL . "on AGG_TABLE0.day = AGG_TABLE$index.day";
                } else
                    if ($timeDim == "hour") {
                        $tableSQL = $tableSQL . "on AGG_TABLE0.day = AGG_TABLE$index.day and AGG_TABLE0.hour = AGG_TABLE$index.hour";
                    } else
                        if ($timeDim == "hourgroup") {
                            $tableSQL = $tableSQL . "on AGG_TABLE0.day = AGG_TABLE$index.day and AGG_TABLE0.hour = AGG_TABLE$index.hour";
                        } else
                            if ($timeDim == "quarter") {
                                $tableSQL = $tableSQL . "on AGG_TABLE0.day = AGG_TABLE$index.day and AGG_TABLE0.hour = AGG_TABLE$index.hour and AGG_TABLE0.minute = AGG_TABLE$index.minute";
                            }

                /*if ($locationDim != "cellGroup") {
                    $tableSQL = $tableSQL . " and AGG_TABLE0.location = AGG_TABLE$index.location";
                }*/

                if($locationDim == "cellGroup" || $timeDim == "daygroup"){
                    $tableSQL = $tableSQL;
                }else{
                    $tableSQL = $tableSQL . " and AGG_TABLE0.location = AGG_TABLE$index.location";
                }
                //$tableSQL = $tableSQL . "on AGG_TABLE0.collecttime = AGG_TABLE$index.collecttime and AGG_TABLE0.location = AGG_TABLE$index.location";
                if ($index != (sizeof($tables) - 1)) {
                    $tableSQL = $tableSQL . " left join ";
                }
            }
            $tempTableSQL = $tempTableSQL . $tableSQL;
            $index++;
        }
        $sql = $aggSelectSQL . " from " . $tempTableSQL . " $aggOrderSQL";
        $sql = str_replace("\n", "", $sql);
       //print_r($sql);return;
        return $sql;
    }

    public function queryTemplate($localDB, $pmDB, $counters, $timeDim, $locationDim, $startTime,
    $endTime, $city, $subNets, &$resultText, $aggTypes, $format, $local_flag)
    {
        $result = array();
        //Query text title
        $templateName = Input::get('template');
        $kpis = $this->getKpis($localDB, $templateName);
        $result['text'] = $kpis['names'];
        //Query content.
        $sql = $this->createSQL($localDB, $pmDB, $kpis['ids'], $counters, $timeDim, $locationDim,$startTime, $endTime, $city, $subNets, $resultText, $aggTypes, $local_flag);
        //print_r($sql);
        $items = array();
        try {
            if ($local_flag) {
                $dsn = "mysql:host=localhost;dbname=CountersBackup_".$city;
                // $countersDB = DB::connection()->getpdo();
                // $countersDB->dsn = $dsn;
                // $countersDB->username = 'root';
                // $countersDB->password = 'mongs';

                $countersDB = new PDO("mysql:host=localhost;dbname=CountersBackup_" . $city, 'root', 'mongs');
                $result['rows'] = $countersDB->query($sql, PDO::FETCH_ASSOC)->fetchAll();
                return $result;
            }
            if ($format == "TDD") {
                //$result['rows'] = $pmDB->query($sql, PDO::FETCH_ASSOC)->fetchAll();
                $rows = $pmDB->query($sql, PDO::FETCH_ASSOC);
                if($pmDB->errorInfo()[1] === 207 || $pmDB->errorInfo()[1] === 102){
                    return 'NOTFINDLINE';
                }
                $result['rows'] = $rows->fetchAll();

            } else
                if ($format == "FDD") {
                    $sql = str_replace("TDD", "FDD", $sql);
                    //$result['rows'] = $pmDB->query($sql, PDO::FETCH_ASSOC);
                    $rows = $pmDB->query($sql, PDO::FETCH_ASSOC);
                    if($pmDB->errorInfo()[1] === 207 || $pmDB->errorInfo()[1] === 102){
                        return 'NOTFINDLINE';
                    }
                    $result['rows'] = $rows->fetchAll();

                } else {
                    $resultTDD = $pmDB->query($sql, PDO::FETCH_ASSOC);

                    if($pmDB->errorInfo()[1] === 207 || $pmDB->errorInfo()[1] === 102){
                        return 'NOTFINDLINE';
                    }

                    foreach ($resultTDD as $row) {
                        array_push($items, $row);
                    }
                    $sqlFDD = str_replace("TDD", "FDD", $sql);
                    $resultFDD = $pmDB->query($sqlFDD, PDO::FETCH_ASSOC);
                    foreach ($resultFDD as $row) {
                        array_push($items, $row);
                    }
                    $result['rows'] = $items;
                }
                return $result;
        }
        catch (PDOException $e) {
            return "Error:" . $e;
        }

    }

    public function getLocalCounters(){
        $tables=file("common/txt/LocalTables.txt");
        $result=array();
        $conn = @mysql_connect('localhost', 'root', 'mongs');
        if(!$conn) {
            die('Could not connect: ' . mysql_error());
        }
        $db = "CountersBackup_changzhou";
        mysql_select_db($db, $conn);
        foreach($tables as $table){
            $table=trim($table);
            $sql="desc $table";
            //$items = $countersDb->query($sql)->fetchAll();
            $res = mysql_query($sql);
            while($item = mysql_fetch_assoc($res)){
                if (strpos($item['Field'],'pm') !== false){
                    $result[]=$item['Field'];
                }
            }
        }
        return $result;
    }

    public function checkIsDiff($template){
        $countersdsn = "mysql:host=localhost;dbname=CountersBackup_changzhou";
        //$dsn="mysql:host=localhost;dbname=CountersBackup_changzhou";
        //$db=new PDO($dsn,'root','mongs');
        $conn = @mysql_connect('localhost', 'root', 'mongs');
        if(!$conn) {
            die('Could not connect: ' . mysql_error());
        }
        $db = "CountersBackup_changzhou";
        mysql_select_db($db, $conn);
        $localCounters=$this->getLocalCounters();
        $dsn="mysql:host=localhost;dbname=mongs";
        $db = DB::connection()->getpdo();
        $db->dsn = $dsn;
        $db->username = 'root';
        $db->password = 'mongs';
        $sql="select elementId from template where templateName='$template'";
        $elementId = $db->query($sql)->fetchColumn();
        $sql="select kpiformula from kpiformula where id in ($elementId)";
        $items=$db->query($sql)->fetchAll();
        $result = array();
        foreach($items as $item){
            $pattern="/[\(\)\+\*-\/]/";
            $counters=preg_split($pattern,$item['kpiformula']);
            foreach($counters as $counter){
                $counter = trim($counter);
                 if (stripos($counter, "pm") === false) {
                    continue;
                 }
                 if (stripos($counter, "_") !== false) {
                    $elements = explode("_", $counter);
                    $counter = $elements[0];
                 }
                if (array_search($counter,$localCounters) === false) {
                    return false;
                }
            }
        }
        return true;
    }

    public function is_local_query($template,$startTime,$timeDim, $format,  $checkStyle) {
        $local_start = file("common/txt/localStart.txt");

        if($checkStyle == 'online'){
            return false;
        }else if($checkStyle == 'local'){
            return true;
        }

        if ($startTime < $local_start[0]){
            return false;
        }
        //Check Model.
        //$format = $_POST['format'];
        if($format == 'FDD') {
            return false;
        }

        if($timeDim == 'hour' || $timeDim == 'hourgroup'|| $timeDim == 'daygroup' || $timeDim=='day') {
            return $this->checkIsDiff($template);
        }
    }

    public function getSubNets($db, $city)
    {

        $SQL = "select subNetwork from databaseconn where connName = '$city'";
        $res = $db->query($SQL);
        $row = $res->fetch();
        $subNets = $row['subNetwork'];
        return $subNets;
    }

    public function getSubNetsFDD($db, $city, $format) {
        $sql = "select cityChinese from databaseconn where connName = '$city' group by cityChinese";
        $row = $db->query($sql)->fetch();
        $cityChinese = $row['cityChinese'];//print_r($cityChinese);return;
        $sql = "select subNetworkFdd from databaseconn where cityChinese='$cityChinese'";
        $res = $db->query($sql, PDO::FETCH_ASSOC);
        $rows = $res->fetchAll();
        //print_r($rows);return;
        $subNets = '';
        foreach ($rows as $row) {
            $subNets .= $row['subNetworkFdd'] . ',';
        }
        $subNets = substr($subNets,0,strlen($subNets)-1);
        //print_r($subNets);return;
        return $subNets;
        // $SQL = "select subNetworkFdd from databaseconn where connName = '$city'";
        // $res = $db->query($SQL);
        // $row = $res->fetch();
        // $subNets = $row['subNetworkFdd'];
        // return $subNets;
    }

    public function getSubNetsFromLocFDD($db, $city) {
        $sql = "select cityChinese from databaseconn where connName = '$city' group by cityChinese";
        $row = $db->query($sql)->fetch();
        $cityChinese = $row['cityChinese'];//print_r($cityChinese);return;
        $sql = "select subNetworkFdd from databaseconn where cityChinese='$cityChinese'";
        $res = $db->query($sql, PDO::FETCH_ASSOC);
        $rows = $res->fetchAll();
        //print_r($rows);return;
        $subNets = '';
        foreach ($rows as $row) {
            $subNets .= $row['subNetworkFdd'] . ',';
        }
        $subNets = substr($subNets,0,strlen($subNets)-1);
        //print_r($subNets);return;
        return $subNets;
    }

    public function getSubNetsFromLoc($location, $city) {
        $subNets = "";
        foreach ($location as $loc) {
            if ($loc['connName'] == $city || $city == substr($loc['connName'],0,strlen($loc['connName'])-1)) {  //$city
                $subNets .= $loc['citys'] . ',';
            }
        }
        $subNets = substr($subNets,0,strlen($subNets)-1); 
        return $subNets;
        // $subNets = "";
        // foreach ($location as $loc) {
        //     if ($loc['connName'] == $city) {
        //         $subNets = $loc['citys'];
        //     }
        // }
        // return $subNets;
    }

    public function getKpis($localDB, $templateName){
        //$templateName = $_POST['template'];
        $queryKpiset = "select elementId from template where templateName='$templateName'";
        $res = $localDB->query($queryKpiset);
        $kpis = $res->fetchColumn();
        $kpisStr = $kpis.',';
        $queryKpiName = "select kpiName,instr('$kpisStr',CONCAT(id,',')) as sort from kpiformula where id in ($kpis) order by sort";
        //$queryKpiName = "select kpiName,instr('$kpis',id) as sort from kpiformula where id in ($kpis) order by sort";
        //echo $queryKpiName;
        $res = $localDB->query($queryKpiName);
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

    public function parseLocation($city, $subNetwork)
    {
        //$citys = json_decode($city, true);
        $citysChinese = json_decode($city);
        $cityPY = new DataBaseConnection();
        $citys = $cityPY->getConnCity($citysChinese);
        
        $subNetworks = json_decode($subNetwork, true);
        $result = array();
        $item = array();
        foreach ($citys as $city) {
            $item['connName'] = $city;
            $databaseConns = DB::table('databaseconn')->where('connName', '=', $city)->get();
            $subStr = $databaseConns[0]->subNetwork; 
            $subArr = explode(',', $subStr);
            $newSubNetworks = '';
            foreach ($subArr as $sub) {
                foreach ($subNetworks as $newSubNetwork) {
                    if($sub == $newSubNetwork){                      
                        $newSubNetworks = $newSubNetworks . $sub . ',';                                  
                    }
                }
            }
            $newSubNetworks = substr($newSubNetworks,0,strlen($newSubNetworks)-1); 
            $item['citys'] = $newSubNetworks;
            array_push($result, $item);
        }
        return $result;
    } 
}