<?php

namespace App\Http\Controllers\QueryAnalysis;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

use Illuminate\Support\Facades\DB;

use PDO;

class NBIQueryController extends Controller{
    public function getNbiTreeData(){
        $users = DB::select('select distinct user from templateNbi');
        $arrUser = array();
        $items = array();
        $itArr = array();
        foreach ($users as $user) {
            $userStr = $user->user;
            $templateNames = DB::table('templateNbi')->where('user', '=', $userStr)->get();
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

    public function templateQuery(){
        $locationDim = Input::get('locationDim');
        $timeDim     = Input::get('timeDim');
        $startTime   = Input::get('startTime');
        $endTime     = Input::get('endTime');
        $city        = Input::get('city');
        $templateName= Input::get('template');
        $erbs        = Input::get('erbs');
        $cell        = Input::get('cell');
        $minutes     = Input::get('minute');
        $hours       = Input::get('hour');
        //print_r(json_decode($hours));return;

        if(is_array(json_decode($hours))){
            $hour = implode(',', json_decode($hours));
        }else{
            $hour = $hours;
        }
            
        //print_r($hour);return;

        if(is_array(json_decode($minutes))){
            $minute = implode(',', json_decode($minutes));
        }else{
            $minute = $minutes;
        }
        /*if($minutes == null){
            $minute = $minutes;
        }else{
            
            $minute = implode(',', json_decode($minutes));
        }*/

        $citys       = json_decode($city);

        $nbiQuery = new nbiQuery();

        //$dsn = "mysql:host=localhost;dbname=mongs";
        $conNbi = @mysql_connect("localhost","root","mongs") or die("选择nbi数据库失败！");
        mysql_select_db("nbi",$conNbi);

        //筛选项：模版
        $nbiKpis = $nbiQuery->getNbiKpis(); 
        $nbiFormulaFilter = $nbiQuery->parseNbiKpiTest($nbiKpis);
        $nbiFormulaFilterPreision = $nbiQuery->parseNbiKpiPre($nbiKpis);
        $nbiFormulaFilterPreision = explode(',',$nbiFormulaFilterPreision);

        $selectEutranCellTdd = $nbiQuery->getSelectEutranCellTdd($locationDim, $timeDim, $nbiFormulaFilter, $hour, $minute, $cell);
        
        $ThisDate = '';
        for($i = strtotime($startTime); $i <= strtotime($endTime); $i += 86400)
        {
            $ThisDate = $ThisDate . '\'' . date("Y-m-d",$i) . '\',';
        }
        $ThisDate = substr($ThisDate,0,strlen($ThisDate)-1); 
        //筛选项：日期
        $selectDateId = $selectEutranCellTdd . " where DateId in ($ThisDate)";

        //筛选项：时间维度
        //$timeDim = $_POST['timeDim']; 
        $selectTimeDim = '';
        $selectTimeDim = $nbiQuery->getSelectTimeDim($timeDim, $hour, $minute, $selectDateId);
        
        //筛选项：区域维度 
        $nbiLocation = $nbiQuery->getLocation($locationDim, $citys);
        $selectNbiLocation = $selectTimeDim . " and (City = $nbiLocation)";

        //筛选项：基站or小区    
        $selectNbiLocations = $nbiQuery->getSelectNbiLocation($selectNbiLocation, $locationDim, $erbs, $cell);

        $select = $nbiQuery->getSelect($locationDim, $timeDim, $selectNbiLocations);
        
        //print_r($select); return;

        $rs = mysql_query("select count(*) from (".$select.") t",$conNbi);
        if($rs){
            $row = mysql_fetch_row($rs);
            $result["total"] = $row[0];
        }
 
        $query = mysql_query($select,$conNbi);
        $rows = [];
        $i = 0;
        if($query){
            while($row = mysql_fetch_row($query)){
                $rows[$i++] = $row;  
            }
        }
        //print_r($rows);
        $getItems = new GetItems();
        $items = $getItems->getItems($locationDim, $timeDim, $rows, $nbiFormulaFilterPreision);
        //print_r($items);
        $resultText = $getItems->getResultText($locationDim, $timeDim);
        //print_r($resultText);

        $result['text'] = $resultText .','.$nbiKpis['names'];
        //print_r($result['text']);

        //print_r(count($items));
        $result['rows'] = $items;

        $result['result'] = 'true';
        
        $result['total'] = count($items);

        $filename = "common/files/" .$templateName. date('YmdHis') . ".csv";

        $result['filename'] = $filename;

        $getItems->resultToCSV2($result,$filename);

        echo json_encode($result);
    }
}

class GetItems{
    public function resultToCSV2($result,$filename){
        $csvContent = mb_convert_encoding($result['text'] . "\n", 'gb2312', 'utf-8');
        $fp = fopen($filename, "w");
        fwrite($fp,$csvContent);
        foreach ($result['rows'] as $row) {
            fputcsv($fp,$row);
        }
        fclose($fp);
    }

    public function diffBetweenTwoDays ($day1, $day2)    //计算相隔天数
    {
          $second1 = strtotime($day1);
          $second2 = strtotime($day2);
            
          if ($second1 < $second2) {
            $tmp = $second2;
            $second2 = $second1;
            $second1 = $tmp;
          }
          return ($second1 - $second2) / 86400;
    }

    public function getResultText($locationDim, $timeDim){
        if($locationDim != "city" && $locationDim != "cellGroup"){
            if($timeDim == 'day'){
                return "day,city,location";
            }else if($timeDim == 'hour'){
                return "day,hour,city,location";
            }else if($timeDim == 'quarter'){
               return "day,hour,minute,city,location";
            }else if($timeDim = 'hourgroup') {
                return "day,hourgroup,city,location";
            }
        }else if($locationDim == "cellGroup") {
            if($timeDim == 'day'){
                return "day,city,cellgroup";
            }else if($timeDim == 'hour'){
                return "day,hour,city,cellgroup";
            }else if($timeDim == 'quarter'){
                return "day,hour,minute,city,cellgroup";
            }else if($timeDim = 'hourgroup') {
                return "day,hourgroup,cellgroup,city";
            }
        }
        else{
            if($timeDim == 'day'){
                return "day,city";
            }else if($timeDim == 'hour' || $timeDim == 'hourgroup'){
                return "day,hour,city";
            }else if($timeDim == 'quarter'){
                return "day,hour,minute,city";
            }else if($timeDim = 'hourgroup') {
                return "day,hourgroup,city,location";
            }
        }
    }

    public function getItems($locationDim, $timeDim, $rows, $nbiFormulaFilterPreision){
        $items = array();
        if($locationDim != "city" && $locationDim != "cellGroup"){
            if($timeDim == 'day'){
                for($i=0; $i<count($rows); $i++){
                    $items[$i]['day'] = $rows[$i][0];
                    $items[$i]['city'] = $rows[$i][1];
                    $items[$i]['location'] = $rows[$i][2];
                    $k = 0;
                    $para = [];
                    for($j=3; $j<count($rows[$i]); $j++){
                        $para[0] = $nbiFormulaFilterPreision[$k++];
                        $items[$i]['kpi'.($j-3)] = $rows[$i][$j];
                        $items[$i]['kpi'.($j-3)] = round($items[$i]['kpi'.($j-3)], $para[0]);
                    }
                }
            }else if($timeDim == 'hour'){
            for($i=0; $i<count($rows); $i++){
                $items[$i]['day'] = $rows[$i][0];
                $items[$i]['hour'] = $rows[$i][1];
                $items[$i]['city'] = $rows[$i][2];
                $items[$i]['location'] = $rows[$i][3];
                $k = 0;
                $para = [];
                for($j=4; $j<count($rows[$i]); $j++){
                    $para[0] = $nbiFormulaFilterPreision[$k++]; 
                    $items[$i]['kpi'.($j-4)] = $rows[$i][$j];
                    $items[$i]['kpi'.($j-4)] = round($items[$i]['kpi'.($j-4)], $para[0]);
                }
            }
            }else if($timeDim == 'quarter'){
                for($i=0; $i<count($rows); $i++){
                    $items[$i]['day'] = $rows[$i][0] . "                                                                                                         ";
                    $items[$i]['hour'] = $rows[$i][1];
                    $items[$i]['minute'] = $rows[$i][2];
                    $items[$i]['city'] = $rows[$i][3];
                    $items[$i]['location'] = $rows[$i][4];
                    $k = 0;
                    $para = [];
                    for($j=5; $j<count($rows[$i]); $j++){
                        $para[0] = $nbiFormulaFilterPreision[$k++];     
                        $items[$i]['kpi'.($j-5)] = $rows[$i][$j];
                        $items[$i]['kpi'.($j-5)] = round($items[$i]['kpi'.($j-5)], $para[0]);
                    }
                }  
            }else if($timeDim == 'hourgroup'){
                for($i=0; $i<count($rows); $i++){
                    $items[$i]['day'] = $rows[$i][0];
                    $items[$i]['hour'] = $rows[$i][1];
                    $items[$i]['city'] = $rows[$i][2];
                    $items[$i]['location'] = $rows[$i][3];
                    $k = 0;
                    $para = [];
                    for($j=4; $j<count($rows[$i]); $j++){
                        $para[0] = $nbiFormulaFilterPreision[$k++]; 
                        $items[$i]['kpi'.($j-4)] = $rows[$i][$j];
                        $items[$i]['kpi'.($j-4)] = round($items[$i]['kpi'.($j-4)], $para[0]);
                    }
                }    
            }
        }else if($locationDim == "cellGroup") {
            if($timeDim == 'day'){
                for($i=0; $i<count($rows); $i++){
                    $items[$i]['day'] = $rows[$i][0];
                    //$items[$i]['cellgroup'] = $rows[$i][1];
                    $items[$i]['city'] = $rows[$i][1];
                    $items[$i]['location'] = $rows[$i][2];
                    $k = 0;
                    $para = [];
                    for($j=3; $j<count($rows[$i]); $j++){
                        $para[0] = $nbiFormulaFilterPreision[$k++];
                        $items[$i]['kpi'.($j-3)] = $rows[$i][$j];
                        $items[$i]['kpi'.($j-3)] = round($items[$i]['kpi'.($j-3)], $para[0]);
                    }
                }
            }else if($timeDim == 'hour'){
                for($i=0; $i<count($rows); $i++){
                    $items[$i]['day'] = $rows[$i][0];
                    $items[$i]['hour'] = $rows[$i][1];
                    //$items[$i]['cellgroup'] = $rows[$i][2];
                    $items[$i]['city'] = $rows[$i][2];
                    $items[$i]['location'] = $rows[$i][3];
                    $k = 0;
                    $para = [];
                    for($j=4; $j<count($rows[$i]); $j++){
                        $para[0] = $nbiFormulaFilterPreision[$k++]; 
                        $items[$i]['kpi'.($j-4)] = $rows[$i][$j];
                        $items[$i]['kpi'.($j-4)] = round($items[$i]['kpi'.($j-4)], $para[0]);
                    }
                }
            }else if($timeDim == 'quarter'){
                for($i=0; $i<count($rows); $i++){
                    $items[$i]['day'] = $rows[$i][0] . "                                                                                                         ";
                    $items[$i]['hour'] = $rows[$i][1];
                    $items[$i]['minute'] = $rows[$i][2];
                    //$items[$i]['cellgroup'] = $rows[$i][3];
                    $items[$i]['city'] = $rows[$i][3];
                    $items[$i]['location'] = $rows[$i][4];
                    $k = 0;
                    $para = [];
                    for($j=5; $j<count($rows[$i]); $j++){
                        $para[0] = $nbiFormulaFilterPreision[$k++];     
                        $items[$i]['kpi'.($j-5)] = $rows[$i][$j];
                        $items[$i]['kpi'.($j-5)] = round($items[$i]['kpi'.($j-5)], $para[0]);
                    }
                }  
            }else if($timeDim == 'hourgroup'){
                for($i=0; $i<count($rows); $i++){
                    $items[$i]['day'] = $rows[$i][0];
                    $items[$i]['hour'] = $rows[$i][1];
                    //$items[$i]['cellgroup'] = $rows[$i][2];
                    
                    $items[$i]['city'] = $rows[$i][2];
                    $items[$i]['location'] = $rows[$i][3];
                    $k = 0;
                    $para = [];
                    for($j=4; $j<count($rows[$i]); $j++){
                        $para[0] = $nbiFormulaFilterPreision[$k++]; 
                        $items[$i]['kpi'.($j-4)] = $rows[$i][$j];
                        $items[$i]['kpi'.($j-4)] = round($items[$i]['kpi'.($j-4)], $para[0]);
                    }
                }
            }
        }
        else{
            if($timeDim == 'day'){
                for($i=0; $i<count($rows); $i++){
                    $items[$i]['day'] = $rows[$i][0];
                    $items[$i]['location'] = $rows[$i][1];
                    $k = 0;
                    $para = [];
                    for($j=2; $j<count($rows[$i]); $j++){
                        $para[0] = $nbiFormulaFilterPreision[$k++];
                        $items[$i]['kpi'.($j-2)] = $rows[$i][$j];
                        $items[$i]['kpi'.($j-2)] = round($items[$i]['kpi'.($j-2)], $para[0]);
                    }
                }
            }else if($timeDim == 'hour'){
                for($i=0; $i<count($rows); $i++){
                    $items[$i]['day'] = $rows[$i][0];
                    $items[$i]['hour'] = $rows[$i][1];
                    $items[$i]['location'] = $rows[$i][2];
                    $k = 0;
                    $para = [];
                    for($j=3; $j<count($rows[$i]); $j++){
                        $para[0] = $nbiFormulaFilterPreision[$k++]; 
                        $items[$i]['kpi'.($j-3)] = $rows[$i][$j];
                        $items[$i]['kpi'.($j-3)] = round($items[$i]['kpi'.($j-3)], $para[0]);
                    }
                }
            }else if($timeDim == 'quarter'){
                for($i=0; $i<count($rows); $i++){
                    $items[$i]['day'] = $rows[$i][0] . "                                                                                                         ";
                    $items[$i]['hour'] = $rows[$i][1];
                    $items[$i]['minute'] = $rows[$i][2];
                    $items[$i]['location'] = $rows[$i][3];
                    $k = 0;
                    $para = [];
                    for($j=4; $j<count($rows[$i]); $j++){
                        $para[0] = $nbiFormulaFilterPreision[$k++];     
                        $items[$i]['kpi'.($j-4)] = $rows[$i][$j];
                        $items[$i]['kpi'.($j-4)] = round($items[$i]['kpi'.($j-4)], $para[0]);
                    }
                }  
            }else if($timeDim == 'hourgroup'){
                for($i=0; $i<count($rows); $i++){
                    $items[$i]['day'] = $rows[$i][0];
                    $items[$i]['hour'] = $rows[$i][1];
                    $items[$i]['city'] = $rows[$i][2];
                    //$items[$i]['location'] = $rows[$i][3];
                    $k = 0;
                    $para = [];
                    for($j=3; $j<count($rows[$i]); $j++){
                        $para[0] = $nbiFormulaFilterPreision[$k++]; 
                        $items[$i]['kpi'.($j-3)] = $rows[$i][$j];
                        $items[$i]['kpi'.($j-3)] = round($items[$i]['kpi'.($j-3)], $para[0]);
                    }
                }
            }
        }
        return $items;
    }
}

class nbiQuery{
    public function getSelect($locationDim, $timeDim, $selectNbiLocations){
        if($locationDim == "erbs"){
            if($timeDim == "day"){
                $select = $selectNbiLocations . "group by DateId,City,ManagedElement";
                return $select;
            }else if($timeDim == "hour"){
                $select = $selectNbiLocations . "group by DateId,HourId,City,ManagedElement";
                return $select;
            }else if($timeDim == "quarter"){
                $select = $selectNbiLocations . "group by DateId,HourId,MinId,City,ManagedElement";
                return $select;
            }else if($timeDim == "hourgroup"){
                $select = $selectNbiLocations . "group by DateId,City,ManagedElement";
                return $select;
            }
        }else if($locationDim == "cell"){
            if($timeDim == "day"){
                $select = $selectNbiLocations . "group by DateId,City,EutranCellTdd";
                return $select;
            }else if($timeDim == "hour"){
                $select = $selectNbiLocations . "group by DateId,HourId,City,EutranCellTdd";
                return $select;
            }else if($timeDim == "quarter"){
                $select = $selectNbiLocations . "group by DateId,HourId,MinId,City,EutranCellTdd";
                return $select;
            }else if($timeDim == "hourgroup"){
                $select = $selectNbiLocations . "group by DateId,City,EutranCellTdd";
                return $select;
            }
        }else if($locationDim == "cellGroup"){
            if($timeDim == "day"){
                $select = $selectNbiLocations . "group by DateId,City";
                return $select;
            }else if($timeDim == "hour"){
                $select = $selectNbiLocations . "group by DateId,HourId,City";
                return $select;
            }else if($timeDim == "quarter"){
                $select = $selectNbiLocations . "group by DateId,HourId,MinId,City";
                return $select;
            }else if($timeDim == "hourgroup"){
                $select = $selectNbiLocations . "group by DateId,City";
                return $select;
            }
        }else{
            if($timeDim == "day"){
                $select = $selectNbiLocations . "group by DateId,City";
                return $select;
            }else if($timeDim == "hour"){
                $select = $selectNbiLocations . "group by DateId,HourId,City";
                return $select;
            }else if($timeDim == "quarter"){
                $select = $selectNbiLocations . "group by DateId,HourId,MinId,City";
                return $select;
            }else if($timeDim == "hourgroup"){
                $select = $selectNbiLocations . "group by DateId,City";
                return $select;
            }
        }
    }

    public function getSelectNbiLocation($selectNbiLocation, $locationDim, $erbs, $cell){
        if($locationDim == "erbs" && $erbs != ''){
            $erbsArr = explode(",", $erbs);
            $erbsStr = "";
            for($i=0; $i<count($erbsArr); $i++){
                $erbsStr = $erbsStr."'".$erbsArr[$i]."',";
            }
            $erbsStr = substr($erbsStr, 0,strlen($erbsStr) -1);
            $selectNbiLocations = $selectNbiLocation . " and (ManagedElement in (".$erbsStr."))";
            return $selectNbiLocations;
        }else if($locationDim == "cell" || $locationDim == "cellGroup" && $cell != ''){
            $cellArr = explode(",", $cell);
            $cellStr = "";
            for($i=0; $i<count($cellArr); $i++){
                $cellStr = $cellStr."'".$cellArr[$i]."',";
            }
            $cellStr = substr($cellStr, 0,strlen($cellStr) -1);
            if($cell != ''){
                $selectNbiLocations = $selectNbiLocation . " and (EutranCellTdd in (".$cellStr."))";
                return $selectNbiLocations;
            }else{
                $selectNbiLocations = $selectNbiLocation;
                return $selectNbiLocations;
            }
        }
        else{
            $selectNbiLocations = $selectNbiLocation;
            return $selectNbiLocations;
        }
    }

    public function getLocation($locationDim, $citys){
        //$locationDim = $_POST['locationDim'];

        // $phpCity = json_decode($_POST['city'], true);
        // $citys = parseCity($phpCity);
        $citysSql = [];
        $citysRows = '';
        for($i=0; $i<count($citys); $i++){
            if($citys[$i] == "常州"){
                $citysSql[$i] = 'ERICSSON-CMJS-CZ';
            }else if($citys[$i] == "无锡"){
                $citysSql[$i] = 'ERICSSON-CMJS-WX';
            }else if($citys[$i] == "镇江"){
                $citysSql[$i] = 'ERICSSON-CMJS-ZJ';
            }else if($citys[$i] == "南通"){
                $citysSql[$i] = 'ERICSSON-CMJS-NT';
            }else if($citys[$i] == "苏州"){
                $citysSql[$i] = 'ERICSSON-CMJS-SZ';
            }else if($citys[$i] == "atest"){
                $citysSql[$i] = 'test';
            }
            // if($citys[$i] == "changzhou"){
            //     $citysSql[$i] = 'ERICSSON-CMJS-CZ';
            // }else if($citys[$i] == "wuxi"){
            //     $citysSql[$i] = 'ERICSSON-CMJS-WX';
            // }else if($citys[$i] == "zhenjiang"){
            //     $citysSql[$i] = 'ERICSSON-CMJS-ZJ';
            // }else if($citys[$i] == "nantong"){
            //     $citysSql[$i] = 'ERICSSON-CMJS-NT';
            // }else if($citys[$i] == "suzhou"){
            //     $citysSql[$i] = 'ERICSSON-CMJS-SZ';
            // }else if($citys[$i] == "atest"){
            //     $citysSql[$i] = 'test';
            // }
            $citysRows = $citysRows . "'" . $citysSql[$i] . "'" . ' or City=';
        } 
        $citysRows = substr($citysRows,0,strlen($citysRows)-9);
        return $citysRows;
    }
    public function getSelectTimeDim($timeDim, $hourId, $minId, $selectDateId){
        if($timeDim == "day"){
            $selectTimeDim = $selectDateId;
            return $selectTimeDim;
        }else if($timeDim == "hour"){
            //$hourId = $_POST['hour'];
            if ($hourId =='null') {
                $hourId = '0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23';
            }
            $hourId = "(".$hourId.")";
            $selectTimeDim = $selectDateId . " and HourId in" . $hourId;
            return $selectTimeDim;
        }else if($timeDim == "quarter"){
            //$hourId = $_POST['hour']; 
            if($hourId == 'null'){
               $hourId = '0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23'; 
            }
            $hourId = "(".$hourId.")";
            //$minId = $_POST['minute'];
            if($minId == 'null'){
                $minId = '0,15,30,45';
            }
            $minId = "(".$minId.")";
            $selectTimeDim = $selectDateId . " and HourId in ".$hourId." and MinId in ".$minId;
            return $selectTimeDim;
        }else if($timeDim == "hourgroup") {        
            //$hourId = $_POST['hour'];
            if ($hourId =='null') {
                $hourId = '0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23';
            }
            $hourId = "(".$hourId.")";
            $selectTimeDim = $selectDateId . " and hourId in ".$hourId;
            return $selectTimeDim;
        }
    }

    public function getSelectEutranCellTdd($locationDim, $timeDim, $nbiFormulaFilter, $hour, $minute, $cell){
        if($locationDim == "erbs"){
            if($timeDim == "day") {
                return "select DateId,City,ManagedElement,$nbiFormulaFilter from EutranCellTdd_station_day"; 
            }else if($timeDim == "hour") {
                return "select DateId,HourId,City,ManagedElement,$nbiFormulaFilter from EutranCellTdd_station_hour"; 
            }else if($timeDim == "quarter") {
                return "select DateId,HourId,MinId,City,ManagedElement,$nbiFormulaFilter from EutranCellTdd"; 
            }else if($timeDim == "hourgroup"){
                if($hour == ''){
                    return "select DateId,'24h' as hourgroup,City,ManagedElement,$nbiFormulaFilter from EutranCellTdd_station_hour";
                }else{
                    return "select DateId,('".$hour."') as hourgroup,City,ManagedElement,$nbiFormulaFilter from EutranCellTdd_station_hour";
                }
                 
            }
        }else if($locationDim == "cell"){
            if($timeDim == "day") {
                return "select DateId,City,EutranCellTdd,$nbiFormulaFilter from EutranCellTdd_cell_day"; 
            }else if($timeDim == "hour") {
                return "select DateId,HourId,City,EutranCellTdd,$nbiFormulaFilter from EutranCellTdd_cell_hour"; 
            }else if($timeDim == "quarter") {
                return "select DateId,HourId,MinId,City,EutranCellTdd,$nbiFormulaFilter from EutranCellTdd"; 
            }else if($timeDim == "hourgroup") {
                if($hour == ''){
                    return "select DateId,'24h' as hourgroup,EutranCellTdd,City,$nbiFormulaFilter from EutranCellTdd_cell_hour"; 
                }else{
                    return "select DateId,('".$hour."') as hourgroup,EutranCellTdd,City,$nbiFormulaFilter from EutranCellTdd_cell_hour"; 
                }
            }
        }else if($locationDim == "cellGroup") {
            if($timeDim == "day"){
                return "select DateId,City,('cellgroup') as cellgroup,$nbiFormulaFilter from EutranCellTdd_cell_day"; 
                //return "select DateId,City,('".$cell."') as cellgroup,$nbiFormulaFilter from EutranCellTdd_cell_day"; 
            }else if($timeDim == "hour") {
                return "select DateId,HourId,City,('cellgroup') as cellgroup,$nbiFormulaFilter from EutranCellTdd_cell_hour"; 
            }else if($timeDim == "quarter") {
                return "select DateId,HourId,MinId,City,('cellgroup') as cellgroup,$nbiFormulaFilter from EutranCellTdd"; 
            }else if($timeDim == "hourgroup") {
                if($hour == ''){
                    return "select DateId,'24h' as hourgroup,('cellgroup') as cellgroup,City,$nbiFormulaFilter from EutranCellTdd_cell_hour"; 
                }else{
                    return "select DateId,('".$hour."') as hourgroup,('cellgroup') as cellgroup,City,$nbiFormulaFilter from EutranCellTdd_cell_hour"; 
                }           
            }
        }else{
            if($timeDim == "day") {
                return "select DateId,City,$nbiFormulaFilter from EutranCellTddQuarter"; 
            }else if($timeDim == "hour") {
                return "select DateId,HourId,City,$nbiFormulaFilter from EutranCellTddQuarter"; 
            }else if($timeDim == "quarter") {
                return "select DateId,HourId,MinId,City,$nbiFormulaFilter from EutranCellTddQuarter"; 
            }else if($timeDim == "hourgroup") {
                if($hour == ''){
                    return "select DateId,'24h' as hourgroup,City,$nbiFormulaFilter from EutranCellTddQuarter";  
                }else{
                    return "select DateId,('".$hour."') as hourgroup,City,$nbiFormulaFilter from EutranCellTddQuarter";  
                }
                //$selectEutranCellTdd = "select DateId,('".$_REQUEST['hour']."') as hourgroup,EutranCellTdd,City,$nbiFormulaFilter from EutranCellTddQuarter";         
            }
        }
    }

    public function parseNbiKpiPre($nbiKpis) {
        $nbiKpisIds = $nbiKpis['ids'];
        $nbiKpiFormulaData = "select kpiPrecision from kpiFormulaNbi where id in ($nbiKpisIds)";
        $query = mysql_query($nbiKpiFormulaData);
        $nbiKpiFormulaRows = '';
        while($row = mysql_fetch_row($query)){
            $nbiKpiFormulaRows = $nbiKpiFormulaRows . $row[0] . ',';
        }
        return $nbiKpiFormulaRows;
    }

    protected function formulaTransform($formula){
        $result="";
        if (strpos($formula,'+')==FALSE && strpos($formula,'-')==FALSE && strpos($formula,'*')==FALSE &&
            strpos($formula,'/')==FALSE && strpos($formula,'(')==FALSE && strpos($formula,')')==FALSE) 
        {
            $formula = "SUM(".$formula.")";
            return $formula;
        }

        $fields = preg_split("/[-+*\/() ]+/", $formula);

        $formula = " ".$formula." ";
        $formula = str_replace("+"," + ",$formula);
        $formula = str_replace("-"," - ",$formula);
        $formula = str_replace("*"," * ",$formula);
        $formula = str_replace("/"," / ",$formula);
        $formula = str_replace("("," ( ",$formula);
        $formula = str_replace(")"," ) ",$formula);

        foreach ($fields as $value)
        {
            if ($value=="" || is_numeric($value) || $value=="power" || $value=="log10" || $value=="max"
                || $value=="POWER" || $value=="LOG10" || $value=="MAX" || $value=="AVG" || $value=="avg")//preg_match('/^[0-9]*/i',$value)
            {
                continue;
            }

            $new_value = "SUM(".$value.")";

            $formula = str_replace(" ".$value." ",$new_value,$formula);
        }

        $formula = str_replace(" ","",$formula);

         if (strpos($formula,'MAX(SUM(')!==FALSE) {
            $formula = str_replace("MAX(SUM(","(MAX(",$formula);
        }

        if (strpos($formula,'max(SUM(')!==FALSE) {
            $formula = str_replace("max(SUM(","(MAX(",$formula);
        }

         if (strpos($formula,'AVG(SUM(')!==FALSE) {
            $formula = str_replace("AVG(SUM(","(AVG(",$formula);
        }

        if (strpos($formula,'avg(SUM(')!==FALSE) {
            $formula = str_replace("avg(SUM(","(avg(",$formula);
        }
        
        return $formula;    
    }

    public function parseNbiKpiTest($nbiKpis){
        $nbiKpisIds = $nbiKpis['ids'];
        $nbiKpisNames = $nbiKpis['names'];
        $nbiKpiFormulaData = "select kpiFormula from kpiFormulaNbi where id in($nbiKpisIds)";
        
        $res = mysql_query($nbiKpiFormulaData);
        $nbiKpiFormula = '';
        $i = 0;
        $row = [];
        while($rows = mysql_fetch_row($res)){
            $row[$i++] = $rows[0];
        }

        $nbiKpis = $this->getNbiKpis(); 
        $nbiKpisNames = $nbiKpis['names'];
        $nbiKpisNames = substr($nbiKpisNames,0,strlen($nbiKpisNames)-1);
        $nbiKpisNames = explode(',',$nbiKpisNames); 
        for($i=0; $i<count($row); $i++){

            $nbiKpiFormula = $nbiKpiFormula . $this->formulaTransform($row[$i]) . " as '".$nbiKpisNames[$i]."',";
            
        }
        $nbiKpiFormula = substr($nbiKpiFormula,0,strlen($nbiKpiFormula)-1);
        return $nbiKpiFormula;
    }

    public function getNbiKpis(){
        $con = @mysql_connect('localhost','root','mongs',true);
        if(!mysql_select_db('mongs')){
            echo '连接失败!';
        }
        $templateName = Input::get('template');
        //$templateName = $_POST['templateNbi'];
        $queryKpiset = "select elementId from templateNbi where templateName='$templateName'";
        $res = mysql_query($queryKpiset);
        $nbiKpis = mysql_fetch_array($res);
        $nbiKpisNum = $nbiKpis['elementId'];
        $queryNbiKpiName = "select kpiName,instr('$nbiKpisNum',id) as sort from kpiFormulaNbi where id in ($nbiKpisNum)";
        $query = mysql_query($queryNbiKpiName);
        $result = [];
        $result['ids'] = $nbiKpisNum;
        $result['names'] = '';
        while($row = mysql_fetch_row($query)){
           $result['names'] = $result['names'] . $row[0] . ',';
        }
        return $result;
    }
}