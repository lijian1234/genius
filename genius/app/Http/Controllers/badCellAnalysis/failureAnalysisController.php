<?php

namespace App\Http\Controllers\badCellAnalysis;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Requests;
use DateTime;
use DateInterval;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

use App\Http\Controllers\Common\DataBaseConnection;

use PDO;
use Illuminate\Support\Facades\Auth;

class failureAnalysisController extends Controller{
	public function getDataBase(){

		$dsn = "mysql:host=localhost;dbname=mongs";
        $db = new PDO($dsn, 'root', 'mongs');
       
        $type = input::get("type");


        $filter='';
        if(!Auth::user()){
            echo "login";
            return;
        }
        $user=Auth::user() -> user;

        if($user!='admin'){
            $filter=" and owner='$user'";
        }
        $sql = "select taskName from task where type='".$type."' and status='complete' $filter order by taskName asc";
        $res = $db->query($sql);

        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        $items = array();
        foreach ($row as $qr) { 
            array_push($items, ["id"=>$qr["taskName"],"text"=>$qr["taskName"]]);
        }
            
        echo json_encode($items);
	}
    public function getChartData(){
        $sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'result';
        $order = isset($_POST['order']) ? strval($_POST['order']) : 'asc';

        $dsn = "mysql:host=localhost;dbname=mongs";
        $db = new PDO($dsn, 'root', 'mongs');

        $dataBases = array();
        $rows = $db->query("SHOW DATABASES;",PDO::FETCH_ASSOC)->fetchall(); 
        $flag = 0;
        foreach ($rows as $row) {
            $database = $row['Database'];
            if($database == input::get("db")){
                $flag = 1;
                break;
            }
        }
        $result = array();
        if($flag == 0){
            $result['message'] = '没有该数据库';
            echo json_encode($result);
            return;    
        }
        $dsn = "mysql:host=localhost;dbname=".input::get("db");
        $db = new PDO($dsn, 'root', 'mongs');
       
        $table=input::get('resultTable');
        $drillDown=input::get('drillDown');
        $drillDownArr=explode(',',$drillDown);
        $rs = $db->query("select count(*) from ".$table);
        $row = $rs->fetchAll(PDO::FETCH_ASSOC);
        $total=$row[0]['count(*)'];

        $rs = $db->query("select $drillDown, count(*) from $table group by $drillDown ");
        $items = array();
        $items['type'] = 'pie';
        $items['name'] = 'shares';
        $data = array();
        
        $row = $rs->fetchAll(PDO::FETCH_ASSOC);
        foreach ($row as $r) {
            $array = array();
            $share=number_format(100*$r['count(*)']/$total,2);
            foreach($drillDownArr as $key)
                $record["$key"]=$r[$key];
            array_push($array, $record["$key"]);
            $record["share"]=$share;
            array_push($array, floatval($share));
            array_push($data, $array);
        }
        $items['data'] = $data;
        $result["resultData"] = $items;
        echo json_encode($result);
    }
    public function getTableData(){
        $limit='';
        $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $rows = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;
        $offset = ($page-1)*$rows;
        $limit=" limit $offset,$rows";

        //$sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'result';
        //$order = isset($_POST['order']) ? strval($_POST['order']) : 'asc';

        $dsn = "mysql:host=localhost;dbname=mongs";
        $db = new PDO($dsn, 'root', 'mongs');

        $dataBases = array();
        $rows = $db->query("SHOW DATABASES;",PDO::FETCH_ASSOC)->fetchall(); 
        $flag = 0;
        foreach ($rows as $row) {
            $database = $row['Database'];
            if($database == input::get("db")){
                $flag = 1;
                break;
            }
        }
        $result = array();
        if($flag == 0){
            $result['message'] = '没有该数据库';
            echo json_encode($result);
            return;    
        }
        $dsn = "mysql:host=localhost;dbname=".input::get("db");
        $db = new PDO($dsn, 'root', 'mongs');

        $table=input::get('resultTable');
        $drillDown=input::get('drillDown');
        $drillDownArr=explode(',',$drillDown);
        $rs = $db->query("select count(*) from ".$table);
        $row = $rs->fetchAll(PDO::FETCH_ASSOC);
        $total=$row[0]['count(*)'];

        
        $rs = $db->query("select $drillDown from $table group by $drillDown");
        $result["total"] = count($rs->fetchAll(PDO::FETCH_ASSOC));
        $rs = $db->query("select $drillDown, count(*) as sum from $table group by $drillDown order by sum desc $limit");
        $items = array();
        $row = $rs->fetchAll(PDO::FETCH_ASSOC);
        foreach ($row as $r) {
            $share=number_format(100*$r['sum']/$total,2)."%";
            foreach($drillDownArr as $key)
            $record["$key"]=$r[$key];
            $record["value"]=$r["sum"];
            $record["total"]=$total;
            $record["share"]=$share;
            array_push($items, $record);
        }
        $result["records"] = $items;
        echo json_encode($result);      
    }

    public function getTableField(){
        $dsn = "mysql:host=localhost;dbname=".input::get("db");
        $db = new PDO($dsn, 'root', 'mongs');

        $database = input::get("db");
        $table = input::get("table");

        $head = $db ->query("select COLUMN_NAME from information_schema.COLUMNS where table_name = '$table' and table_schema = '$database'");
        $text = array();
        foreach ($head as $h) {
           array_push($text, $h["COLUMN_NAME"]);
        }
        $result = array();
        $result['text'] = implode(",",$text);
        return implode(",",$text);
    }
    public function getdetailDataHeader(){
        $dsn = "mysql:host=localhost;dbname=".input::get("db");
        $db = new PDO($dsn, 'root', 'mongs');

        $database = input::get("db");
        $table = input::get("table");

        $head = $db ->query("select COLUMN_NAME from information_schema.COLUMNS where table_name = '$table' and table_schema = '$database'");
        $text = array();
        foreach ($head as $h) {
           array_push($text, $h["COLUMN_NAME"]);
        }
        $result = array();
        $result['text'] = implode(",",$text);
        echo json_encode($result);
    }
    public function getdetailData(){
        $dsn = "mysql:host=localhost;dbname=".input::get("db");
        $db = new PDO($dsn, 'root', 'mongs');

        $limit='';
        $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $rows = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;
        $offset = ($page-1)*$rows;
        $limit=" limit $offset,$rows";

        $drillDownText = "result";
        $table=input::get('table');
        $filter = input::get("result");

        $rs = $db->query("select count(*) from ".$table." where result ='".$filter."'");
        $row = $rs->fetchAll(PDO::FETCH_ASSOC);
        $total=$row[0]['count(*)'];

        $res = $db->query("select * from ".$table." where result ='".$filter."' order by eventTime desc $limit");
        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        $items = array();
        foreach ($row as $qr) { 
            array_push($items, $qr);
        }
        $result["total"] = $total;
        $result["records"] = $items;
        echo json_encode($result);
    }
    public function exportFile(){
        $dsn = "mysql:host=localhost;dbname=mongs";
        $db = new PDO($dsn, 'root', 'mongs');

        $dataBases = array();
        $rows = $db->query("SHOW DATABASES;",PDO::FETCH_ASSOC)->fetchall(); 
        $flag = 0;
        foreach ($rows as $row) {
            $database = $row['Database'];
            if($database == input::get("db")){
                $flag = 1;
                break;
            }
        }
        $result = array();
        if($flag == 0){
            $result['result'] = 'false';
            echo json_encode($result);
            return;    
        }
        $dsn = "mysql:host=localhost;dbname=".input::get("db");
        $db = new PDO($dsn, 'root', 'mongs');

        $drillDownText = "result";
        $fileContent = array();
        $csvContent = "";

        $table=input::get('table');
        $filter = input::get("result");

        $res = $db->query("select * from $table where result ='$filter' order by eventTime desc");
        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        $items = array();
        foreach ($row as $qr) { 
            array_push($items, $qr);
        }
        $result["text"] = $this->getTableField();
        $result['rows'] = $items;
        $result['total'] = count($items);
        $result['result'] = 'true';
        $filename="common/files/".$drillDownText."_".$table."_".date('YmdHis').".csv";

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