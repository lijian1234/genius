<?php

namespace App\Http\Controllers\NetworkOptimization;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use PDO;
use Illuminate\Support\Facades\Auth;

class relationNonHandoverController extends Controller{

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
    public function getDataHeader(){

        $dsn = "mysql:host=localhost;dbname=AutoKPI";
        $db = new PDO($dsn, 'root', 'mongs');

        $head = $db ->query("select COLUMN_NAME from information_schema.COLUMNS where table_name = 'RelationNonHandover' and table_schema = 'AutoKPI'");
        $text = array();
        foreach ($head as $h) {
           array_push($text, $h["COLUMN_NAME"]);
        }
        $result = array();
        $result['text'] = implode(",",$text);
        echo json_encode($result);
    }
	public function getTableData(){
		$dsn = "mysql:host=localhost;dbname=AutoKPI";
        $db = new PDO($dsn, 'root', 'mongs');
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

        $result = array();
        $rs = $db->query("select count(*) from RelationNonHandover where city in ($citystr)");
        $row = $rs->fetchAll(PDO::FETCH_ASSOC);
        $result["total"] = $row[0]['count(*)'];

        $sql = "select * from RelationNonHandover where city in ($citystr)".$limit;
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

        $dsn = "mysql:host=localhost;dbname=AutoKPI";
        $db = new PDO($dsn, 'root', 'mongs');

        $city = input::get("city");

        $head = $db ->query("select COLUMN_NAME from information_schema.COLUMNS where table_name = 'RelationNonHandover' and table_schema = 'AutoKPI'");
        $text = array();
        foreach ($head as $h) {
           array_push($text, $h["COLUMN_NAME"]);
        }
        $result = array();
        $result['text'] = implode(",",$text);

        $phpCity = input::get("city");
        $citys = explode(",",$phpCity);
        $citystr = "";
        foreach ($citys as $city) {
            $citystr .= "'".$city."',";
        }
        $citystr = substr($citystr, 0,-1);

        $sql = "select * from RelationNonHandover where city in ($citystr)";
        $res = $db->query($sql);
        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        $items = array();
        foreach ($row as $qr) { 
            array_push($items, $qr);
        }
        $result['rows'] = $items;
        $result['total'] = count($items);
        $result['result'] = 'true';

        $filename="common/files/RelationNonHandover_".$phpCity."_".date('YmdHis').".csv";
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