<?php

namespace App\Http\Controllers\ParameterAnalysis;
use App\Http\Controllers\Controller;

use App\Http\Controllers\Common\DataBaseConnection;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;


use App\Task;
use App\User;
use App\DatabaseConn;
use PDO; 

class BulkcmMarkController extends Controller
{
	public function getBulkcmMarkDataHeader(){
	   	$dbname = Input::get('dataBase');
	    $dsn = "mysql:host=localhost;dbname=$dbname";
    	 
    	$db = new PDO($dsn, 'root', 'mongs');
	    $table = 'TempParameterBulkcmCompare';
	    $result = array();
	    $sql = "select subNetwork,meContext,EUtranCellTDDId,ecgi,DN,parameterName,parameter,oldValue,newValue from $table limit 1";
	    //subNetwork,meContext,EUtranCellTDDId,ecgi,DN,parameterName,parameter,oldValue,newValue
	    //dump($sql);
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

	public function getBulkcmMarkData(){
		$dbc=new DataBaseConnection();
	    $limit='';
	    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
	    $rows = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;
	    $offset = ($page-1)*$rows;
	    $limit=" limit $offset,$rows";
	    $filter = '';

     	$dbname = Input::get('dataBase');
     	$citys = Input::get('citys');
     	$paramLength = Input::get('paramLength');
     	//dump($citys);
	    $result = array();
	    $dsn = "mysql:host=localhost;dbname=$dbname";
    	$db = new PDO($dsn, 'root', 'mongs');  
	    $table = 'TempParameterBulkcmCompare';

	    $subNetwork = '';
    	if ($citys != '') {
      		foreach ($citys as $city) {
        		//$subNetwork .= $this->getSubNets($pdo, $city);
       			$subNetwork .= $dbc->getSubNets($city).',';
      		}
      		$subNetwork = substr($subNetwork , 0 , -1);
   		}

   		if($subNetwork != ''){
				$filter=" where subNetwork in (".$subNetwork.")";
			}
	    
	    $rs = $db->query("select count(*) totalCount from ".$table.$filter);
	    // $rs = $db->query("select subNetwork,meContext,EUtranCellTDDId,ecgi,DN,parameterName,parameter,oldValue,newValue from ".$table.$filter);
        $row = $rs->fetchAll(PDO::FETCH_ASSOC);
        $result["total"] = $row[0]['totalCount'];
        // $result["total"] = $row[0];
        $sql = "select * from ".$table.$filter.$limit;
        $res = $db->query($sql);
        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        $items = array();
        foreach ($row as $qr) {
         	$qr = $this->substring_paramData($qr);
            array_push($items, $qr);
        }
        $result['records'] = $items;
        return json_encode($result);
 
  }

  	function substring_paramData($row){
    	
    	foreach ($row as $key => $value) {
      		if ($key == 'DN') {
      			$row[$key] = substr($value, 0,30);
      		}
    	}
    	return $row;
  	}

  	public function getAllBulkcmMarkData(){

     	$dbname = Input::get('dataBase');
     	$citys = Input::get('citys');
	    $dsn = "mysql:host=localhost;dbname=$dbname";
    	$db = new PDO($dsn, 'root', 'mongs'); 
	    $table = 'TempParameterBulkcmCompare';
	    
	    $sql = "select subNetwork,meContext,EUtranCellTDDId,ecgi,DN,parameterName,parameter,oldValue,newValue from $table limit 1";
	    $rs = $db->query($sql,PDO::FETCH_ASSOC);
	    $keys = array();
	    if($rs){
	    	$rows = $rs->fetchall();
	    	$keys = array_keys($rows[0]);
	    }else{
		 	$result['error'] = 'error';
	      	return $result;
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

	    $dbc=new DataBaseConnection();
	    $subNetwork = '';
    	if ($citys != '') {
      		foreach ($citys as $city) {
        		//$subNetwork .= $this->getSubNets($pdo, $city);
       			$subNetwork .= $dbc->getSubNets($city).',';
      		}
      		$subNetwork = substr($subNetwork , 0 , -1);
   		}

   		if($subNetwork != ''){
				$filter=" where subNetwork in (".$subNetwork.")";
			}
		if($citys != '') {
			$sql = "select subNetwork,meContext,EUtranCellTDDId,ecgi,DN,parameterName,parameter,oldValue,newValue from ".$table.$filter;
		}else{
			$sql = "select subNetwork,meContext,EUtranCellTDDId,ecgi,DN,parameterName,parameter,oldValue,newValue from ".$table;
		}
	    //$sql = "select * from ".$table.$filter;
        $res = $db->query($sql);
        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        $items = array();
       	foreach ($row as $qr) {
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

	public function getParamTasks(){
  		$dsn = "mysql:host=localhost;dbname=information_schema";
        $db = new PDO($dsn, 'root', 'mongs');

		$filter = '';
	    $items = array();
	    $type = $_REQUEST['type'];
	    $sql = "select SCHEMA_NAME from SCHEMATA where SCHEMA_NAME like '$type%' ORDER BY SCHEMA_NAME";
	   
	    $res = $db->query($sql);

        $row = $res->fetchAll(PDO::FETCH_ASSOC);
	    foreach ($row as $task) {
	      array_push($items, ["id"=>$task["SCHEMA_NAME"],"text"=>$task["SCHEMA_NAME"]]);
	    }
	    echo json_encode($items);
 
  }

  public function getAllCity(){
  
    $cityClass = new DataBaseConnection();
    return $cityClass->getCityOptions();
  }

 //  /**
	//  * @desc ：根据城市获取相应的库
	//  * Time：2016/09/02 16:07:42
	//  * @author Wuyou
	//  * @param 参数类型
	//  * @return 返回值类型
	// */
	// public function getMRDatabase($city,$date){
	// 	$dbc = new DataBaseConnection();
 //    	return $dbc->getMRDatabase($city,$date);
	// }
}