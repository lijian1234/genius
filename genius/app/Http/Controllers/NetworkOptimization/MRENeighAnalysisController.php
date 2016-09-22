<?php

namespace App\Http\Controllers\NetworkOptimization;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Common\DataBaseConnection;

use PDO;

class MRENeighAnalysisController extends Controller{

	public function getMreServeNeighDataHeader(){
	   	$dbname = $this->getMRDatabase(Input::get('dataBase'));
	   	$dateTime = Input::get('dateTime');
	    $dsn = "mysql:host=10.40.57.190:8066;dbname=$dbname";
    	$db = new PDO($dsn, 'mr', 'mr'); 
	    $table = 'mreServeNeigh';
	    $result = array();
     	$sql = "select * from ".$table." WHERE mr_LteScEarfcn = mr_LteNcEarfcn AND datetime_id like '".$dateTime."%' limit 1";
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

	public function getMreServeNeighData(){
	 	$input1 = Input::get('input1');
	    $input2 = Input::get('input2');
	    $input3 = Input::get('input3');
	    $input4 = Input::get('input4');
	    $input5 = Input::get('input5');
	    $input6 = Input::get('input6');
	    $input7 = Input::get('input7');
	    $input8 = Input::get('input8');
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
	    $table = 'mreServeNeigh';
	    
	    $rs = $db->query("select count(*) totalCount from ".$table." WHERE mr_LteScEarfcn = mr_LteNcEarfcn AND (
      (nc_session_ratio >= ".$input1."/100 AND nc_session_num_min >= $input2 AND nc_times_ratio >= ".$input3."/100 )
      OR ( nc_session_num_avg >= $input4 AND ncTop2_times_ratio >= ".$input5."/100 )) AND ( avg_mr_LteScRSRP >=$input6 AND avg_mr_LteScRSRQ >=$input7 AND  avg_mr_LteNcRSRP >$input8) AND  datetime_id like '".$dateTime."%'");
        $row = $rs->fetchAll(PDO::FETCH_ASSOC);
        
        $result["total"] = $row[0]['totalCount'];

        $sql = "select * from ".$table." WHERE mr_LteScEarfcn = mr_LteNcEarfcn AND (
      (nc_session_ratio >= ".$input1."/100 AND nc_session_num_min >= $input2 AND nc_times_ratio >= ".$input3."/100 )
      OR ( nc_session_num_avg >= $input4 AND ncTop2_times_ratio >= ".$input5."/100 )) AND ( avg_mr_LteScRSRP >=$input6 AND avg_mr_LteScRSRQ >=$input7 AND  avg_mr_LteNcRSRP >$input8) AND  datetime_id like '".$dateTime."%' ".$limit;
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
  	public function getAllMreServeNeighData(){
	 	$input1 = Input::get('input1');
	    $input2 = Input::get('input2');
	    $input3 = Input::get('input3');
	    $input4 = Input::get('input4');
	    $input5 = Input::get('input5');
	    $input6 = Input::get('input6');
	    $input7 = Input::get('input7');
	    $input8 = Input::get('input8');

     	$dbname = $this->getMRDatabase(Input::get('dataBase'));
     	$dateTime = Input::get('dateTime');
	    $dsn = "mysql:host=10.40.57.190:8066;dbname=$dbname";
    	$db = new PDO($dsn, 'mr', 'mr'); 
	    $table = 'mreServeNeigh';
	    
	    $sql = "select * from $table mr_LteScEarfcn = mr_LteNcEarfcn AND (
      (nc_session_ratio >= ".$input1."/100 AND nc_session_num_min >= $input2 AND nc_times_ratio >= ".$input3."/100 )
      OR ( nc_session_num_avg >= $input4 AND ncTop2_times_ratio >= ".$input5."/100 )) AND ( avg_mr_LteScRSRP >=$input6 AND avg_mr_LteScRSRQ >=$input7 AND  avg_mr_LteNcRSRP >$input8) AND limit 1";
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

	    $sql = "select * from ".$table." WHERE mr_LteScEarfcn = mr_LteNcEarfcn AND (
      (nc_session_ratio >= ".$input1."/100 AND nc_session_num_min >= $input2 AND nc_times_ratio >= ".$input3."/100 )
      OR ( nc_session_num_avg >= $input4 AND ncTop2_times_ratio >= ".$input5."/100 )) AND ( avg_mr_LteScRSRP >=$input6 AND avg_mr_LteScRSRQ >=$input7 AND  avg_mr_LteNcRSRP >$input8) AND datetime_id like '".$dateTime."%'";

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