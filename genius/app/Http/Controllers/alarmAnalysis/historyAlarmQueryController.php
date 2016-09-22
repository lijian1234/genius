<?php

namespace App\Http\Controllers\alarmAnalysis;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use PDO;
use Illuminate\Support\Facades\Auth;

class historyAlarmQueryController extends Controller{

	public function getCitys(){

        $dsn = "mysql:host=localhost;dbname=mongs";
        $db = new PDO($dsn, 'root', 'mongs');

        $sql = "select cityChinese,connName  from databaseconn ORDER BY cityChinese";
        $res = $db->query($sql);

        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        $items = array();
        foreach ($row as $qr) {
        	if(count($items)>0 && $items[count($items)-1]['label'] == $qr['cityChinese']){
        		$items[count($items)-1]['value'] = $items[count($items)-1]['value'].",".$qr['connName'];
        	}else{
        		array_push($items, ["value"=>$qr['connName'],"label"=>$qr['cityChinese']]);
        	}
            
        }
        echo json_encode($items);
	}

	public function getTableData(){
		$dsn = "mysql:host=localhost;dbname=Alarm";
        $db = new PDO($dsn, 'root', 'mongs');
        $placeDim = input::get("placeDim");
        $placeDimName = input::get("placeDimName");
        $dateFrom = input::get("dateFrom");
        $dateTo = input::get("dateTo");
        $phpCity = input::get("city");

        $citys = explode(",",$phpCity);
        $citystr = "";
        foreach ($citys as $city) {
                $citystr .= "'".$city."',";
        }
        $citystr = substr($citystr, 0,-1);

        $limit='';
        $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $rows = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;
        $offset = ($page-1)*$rows;
        $limit=" limit $offset,$rows";

        if($placeDimName !=''){
                $queryFilter = " where to_days(Event_time) between to_days('$dateFrom') and to_days('$dateTo') and city in (".$citystr.") and ".$placeDim."='$placeDimName' and Perceived_severity != 5 order by Event_time desc";
        }else{
                $queryFilter = " where to_days(Event_time) between to_days('$dateFrom') and to_days('$dateTo') and city in (".$citystr.") and  Perceived_severity != 5 order by Event_time desc";
        }

        $result = array();
        //$rs = mysql_query("select count(*) from ".$_REQUEST['table'].$queryFilter);
        $rs = $db->query("select count(*) from FMA_alarm_log".$queryFilter);
        $row = $rs->fetchAll(PDO::FETCH_ASSOC);
        $result["total"] = $row[0]['count(*)'];

        $sql = "select Event_time,city,subNetwork,meContext,eutranCell,Cease_time,SP_text,Problem_text,Alarm_id from FMA_alarm_log".$queryFilter.$limit;
        //print_r($sql);return;
        $res = $db->query($sql);
        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        $items = array();
        foreach ($row as $qr) { 
            array_push($items, $qr);
        }
        $result['records'] = $items;
        echo json_encode($result);

	}

        public function getAllTableData(){
            $dsn = "mysql:host=localhost;dbname=Alarm";
            $db = new PDO($dsn, 'root', 'mongs');
            $placeDim = input::get("placeDim");
            $placeDimName = input::get("placeDimName");
            $dateFrom = input::get("dateFrom");
            $dateTo = input::get("dateTo");
            $phpCity = input::get("city");

            $citys = explode(",",$phpCity);
            $citystr = "";
            foreach ($citys as $city) {
                    $citystr .= "'".$city."',";
            }
            $citystr = substr($citystr, 0,-1);

            if($placeDimName !=''){
                    $queryFilter = " where to_days(Event_time) between to_days('$dateFrom') and to_days('$dateTo') and city in (".$citystr.") and ".$placeDim."='$placeDimName' and Perceived_severity != 5 order by Event_time desc";
            }else{
                    $queryFilter = " where to_days(Event_time) between to_days('$dateFrom') and to_days('$dateTo') and city in (".$citystr.") and  Perceived_severity != 5 order by Event_time desc";
            }

            $result = array();
            $sql = "select Event_time,city,subNetwork,meContext,eutranCell,Cease_time,SP_text,Problem_text,Alarm_id from FMA_alarm_log".$queryFilter;

            $res = $db->query($sql);
            $row = $res->fetchAll(PDO::FETCH_ASSOC);
            $items = array();
            foreach ($row as $qr) { 
                array_push($items, $qr);
            }
            $result["text"] = "Event_time,city,subNetwork,meContext,eutranCell,Cease_time,SP_text,Problem_text,Alarm_id";
            $result['rows'] = $items;
            $result['total'] = count($items);
            $result['result'] = 'true';

            $filename="common/files/FMA_alarm_log_".date('YmdHis').".csv";
            $this->resultToCSV2($result, $filename);
            $result['filename'] = $filename;
            if (count($items) > 1000) {
                    $result['rows'] = array_slice($items, 0, 1000);
            }

            echo json_encode($result);
                

        }
        protected function resultToCSV2($result, $filename){
            $csvContent = mb_convert_encoding($result['text'] . "\n", 'GBK');
            $fp = fopen($filename, "w");
            fwrite($fp, $csvContent);
            foreach ($result['rows'] as $row) {
                    fputcsv($fp, $row);
            }
            fclose($fp);
        }
}