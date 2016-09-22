<?php

namespace App\Http\Controllers\NetworkOptimization;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Common\DataBaseConnection;

use PDO;

class MRONeighAnalysisController extends Controller{

	public function getMroServeNeighDataHeader(){
	   	$dbname = $this->getMRDatabase(Input::get('dataBase'));
	   	$dateTime = Input::get('dateTime');
	    $dsn = "mysql:host=10.40.57.190:8066;dbname=$dbname";
    	$db = new PDO($dsn, 'mr', 'mr'); 
	    $table = 'mroServeNeigh';
	    $result = array();
     	$sql = "select * from ".$table." WHERE datetime_id like '".$dateTime."%' limit 1";
	    $rs = $db->query($sql,PDO::FETCH_ASSOC);
	    if($rs){
	    	$rows = $rs->fetchall();
	    	if (count($rows) > 0) {
	    		return $rows[0];
	    	}else{
	    		$result['error'] = 'error';
	      		return $result;
	    	}
	    }else{
		 	$result['error'] = 'error';
	      	return $result;
	    }
	}

	public function getMroServeNeighData(){
	    $limit='';
	    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
	    $rows = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;
	    $offset = ($page-1)*$rows;
	    $limit=" limit $offset,$rows";

     	$dbname = $this->getMRDatabase(Input::get('dataBase'));
     	$dateTime = Input::get('dateTime');
	    $result = array();
	    $dsn = "mysql:host=10.40.57.190:8066;dbname=$dbname";
    	$db = new PDO($dsn, 'mr', 'mr');  
	    $table = 'mroServeNeigh';
	    
	    $rs = $db->query("select count(*) totalCount from ".$table." WHERE datetime_id like '".$dateTime."%'");
        $row = $rs->fetchAll(PDO::FETCH_ASSOC);
        
        $result["total"] = $row[0]['totalCount'];

        $sql = "select * from ".$table." WHERE datetime_id like '".$dateTime."%' ".$limit;
        $res = $db->query($sql);
        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        if (count($row) == 0) {
	        $result['error'] = 'error';
	        return json_encode($result);
	    }
        $items = array();
        foreach ($row as $qr) { 
            array_push($items, $qr);
        }
        $result['records'] = $items;
	    
	    echo json_encode($result);
  	}
  	public function getAllMroServeNeighData(){

     	$dbname = $this->getMRDatabase(Input::get('dataBase'));
     	$dateTime = Input::get('dateTime');
	    $dsn = "mysql:host=10.40.57.190:8066;dbname=$dbname";
    	$db = new PDO($dsn, 'mr', 'mr'); 
	    $table = 'mroServeNeigh';
	    
	    $sql = "select * from $table limit 1";
	    $rs = $db->query($sql,PDO::FETCH_ASSOC);
	    $keys = array();
	    if($rs){
	    	$rows = $rs->fetchall();
	    	if (count($rows) > 0) {
	    		$keys = array_keys($rows[0]);
	    	}else{
	    		$result['error'] = 'error';
	      		return json_encode($result);
	    	}
	    	
	    }else{
		 	$result['error'] = 'error';
	      	return json_encode($result);
	    }
	    $text = '';
	    foreach ($keys as $key) {
	    	if($key == 'id'){
		        continue;
		      }
	      	$text .= $key.',';
	    }
	    $text = substr($text,0,strlen($text)-1);
	    $result['text'] = $text;

	    $sql = "select * from ".$table." WHERE datetime_id like '".$dateTime."%'";
        $res = $db->query($sql);
        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        if (count($row) == 0) {
	        $result['error'] = 'error';
	        return json_encode($result);
	    }
        $items = array();
       	foreach ($row as $qr) {
       		array_shift($qr);
	      	array_push($items, $qr);
	    }

        $result['rows'] = $items;
        $result['total'] = count($items);
        $result['result'] = 'true';

        $filename="files/".$dbname."_".$table."_".date('YmdHis').".csv";
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
    /**
	 * @desc ：获取城市列表
	 * Time：2016/09/02 16:07:42
	 * @author Wuyou
	 * @param 参数类型
	 * @return 返回值类型
	*/
	public function getAllCity(){
		$dbc = new DataBaseConnection();
  		return $dbc->getCityOptions();
	}
	/**
	 * @desc ：根据城市获取相应的库
	 * Time：2016/09/02 16:07:42
	 * @author Wuyou
	 * @param 参数类型
	 * @return 返回值类型
	*/
	public function getMRDatabase($city){
		$dbc = new DataBaseConnection();
    	return $dbc->getMRDatabase($city);
	}
}