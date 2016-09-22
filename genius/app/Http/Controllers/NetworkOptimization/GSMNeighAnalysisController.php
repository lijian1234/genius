<?php

namespace App\Http\Controllers\NetworkOptimization;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Common\DataBaseConnection;

use PDO;

class GSMNeighAnalysisController extends Controller{
  public function getGSMNeighData(){
    $input1 = Input::get('input1');
    $input2 = Input::get('input2');
    $input3 = Input::get('input3');
    $input4 = Input::get('input4');
    $input5 = Input::get('input5');
    $input6 = Input::get('input6');
    $input7 = Input::get('input7');
    $dateTime = Input::get('dateTime');
    $dbname = $this->getMRDatabase(Input::get('select'));
    $dsn = "mysql:host=10.40.57.190:8066;dbname=$dbname";
    $db = new PDO($dsn, 'mr', 'mr'); 
    $table = 'mreServerNeighIrat';
    $result = array();
    $sql = "select * from $table where datetime_id like '".$dateTime."%' limit 1";
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

  public function getGSMNeighDataSplit(){
    $limit='';
    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
    $rows = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;
    $offset = ($page-1)*$rows;
    $limit=" limit $offset,$rows";

    $input1 = Input::get('input1');
    $input2 = Input::get('input2');
    $input3 = Input::get('input3');
    $input4 = Input::get('input4');
    $input5 = Input::get('input5');
    $input6 = Input::get('input6');
    $input7 = Input::get('input7');
    $input8 = Input::get('input8');
    $input9 = Input::get('input9');
    $dbname = $this->getMRDatabase(Input::get('select'));
    $dateTime = Input::get('dateTime');
    $return = array();
    $dsn = "mysql:host=10.40.57.190:8066;dbname=$dbname";
    $db = new PDO($dsn, 'mr', 'mr'); 
    $table = 'mreServerNeighIrat';
    $sql = "select * from $table limit 1";
    $rs = $db->query($sql,PDO::FETCH_ASSOC);
    $keys = array();
    if($rs){
    	$rows = $rs->fetchall();
    	$keys = array_keys($rows[0]);
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
    $sql = "SELECT * FROM $table WHERE (
        (nc_session_ratio >= ".$input1."/100 AND nc_session_num_min >= $input2 AND nc_times_ratio >= ".$input3."/100 )
        OR ( nc_session_num_avg >= $input4 AND ncTop2_times_ratio >= ".$input5."/100 )
        OR ( nc_session_num_max > $input8 AND ncTop2_times_ratio > ".$input9."/100 ))
        AND ( avg_mr_GsmNcellCarrierRSSI >$input6 AND avg_mr_LteScRSRQ >=$input7 ) AND isdefined IS NULL AND datetime_id like '".$dateTime."%';";
    $rows = $db->query($sql,PDO::FETCH_ASSOC)->fetchall();
    if (count($rows) == 0) {
        $result['error'] = 'error';
        return json_encode($result);
    }
    $rowsId = array();
    foreach ($rows as $row) {
      array_shift($row);
      array_push($rowsId, $row);
    }
    $return["total"] = count($rows);
    $result['rows'] = $rowsId;
    $filename = "common/files/GSMNeighborAnalysis" . date('YmdHis') . ".csv";
    $this->resultToCSV2($result, $filename);
    //$result['filename'] = $filename;
    $sql = "SELECT * FROM $table WHERE (
        (nc_session_ratio >= ".$input1."/100 AND nc_session_num_min >= $input2 AND nc_times_ratio >= ".$input3."/100 )
        OR ( nc_session_num_avg >= $input4 AND ncTop2_times_ratio >= ".$input5."/100 )
        OR ( nc_session_num_max > $input8 AND ncTop2_times_ratio > ".$input9."/100 ))
        AND ( avg_mr_GsmNcellCarrierRSSI >$input6 AND avg_mr_LteScRSRQ >=$input7 ) AND isdefined IS NULL AND datetime_id like '".$dateTime."%' $limit;";
    $rows = $db->query($sql,PDO::FETCH_ASSOC)->fetchall();
    $allData = array();
    foreach ($rows as $row) {
      array_push($allData, $row);
    }
    $return['records'] = $allData;
    $return['filename'] = $filename;
    echo json_encode($return);
  }  

  public function getLTENeighDataSplit(){
    $limit='';
    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
    $rows = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;
    $offset = ($page-1)*$rows;
    $limit=" limit $offset,$rows";

    $input1 = Input::get('input1');
    $input2 = Input::get('input2');
    $input3 = Input::get('input3');
    $input4 = Input::get('input4');
    $input5 = Input::get('input5');
    $input6 = Input::get('input6');
    $input7 = Input::get('input7');
    $input8 = Input::get('input8');
    //$dbname = Input::get('select');
    $dbname = $this->getMRDatabase(Input::get('select'));
    $dateTime = Input::get('dateTime');

    $result = array();
    $return = array();
    $dsn = "mysql:host=10.40.57.190:8066;dbname=$dbname";
    $db = new PDO($dsn, 'mr', 'mr'); 
    $table = 'mreServeNeigh';
 	$sql = "select * from $table limit 1";
    $rs = $db->query($sql,PDO::FETCH_ASSOC);
    $keys = array();
    if($rs){
    	$rows = $rs->fetchall();
    	$keys = array_keys($rows[0]);
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
    $sql = "SELECT * FROM $table WHERE mr_LteScEarfcn != mr_LteNcEarfcn AND (
      (nc_session_ratio >= ".$input1."/100 AND nc_session_num_min >= $input2 AND nc_times_ratio >= ".$input3."/100 )
      OR ( nc_session_num_avg >= $input4 AND ncTop2_times_ratio >= ".$input5."/100 )) AND ( avg_mr_LteScRSRP >=$input6 AND avg_mr_LteScRSRQ >=$input7 AND  avg_mr_LteNcRSRP >$input8) AND isdefined IS NULL AND datetime_id like '".$dateTime."%';";
    $rows = $db->query($sql,PDO::FETCH_ASSOC)->fetchall();
    if (count($rows) == 0) {
        $result['error'] = 'error';
        return json_encode($result);
    }
    $return["total"] = count($rows);
    $rowsId = array();
    foreach ($rows as $row) {
      array_shift($row);
      array_push($rowsId, $row);
    }
    $result['rows'] = $rowsId;
    $filename = "common/files/GSMNeighborAnalysis" . date('YmdHis') . ".csv";
    $this->resultToCSV2($result, $filename);
    //$result['filename'] = $filename;
    $sql = "SELECT * FROM $table WHERE mr_LteScEarfcn != mr_LteNcEarfcn AND (
      (nc_session_ratio >= ".$input1."/100 AND nc_session_num_min >= $input2 AND nc_times_ratio >= ".$input3."/100 )
      OR ( nc_session_num_avg >= $input4 AND ncTop2_times_ratio >= ".$input5."/100 )) AND ( avg_mr_LteScRSRP >=$input6 AND avg_mr_LteScRSRQ >=$input7 AND  avg_mr_LteNcRSRP >$input8) AND isdefined IS NULL AND datetime_id like '".$dateTime."%' $limit;";
    $rows = $db->query($sql,PDO::FETCH_ASSOC)->fetchall();
    $allData = array();
    foreach ($rows as $row) {
      array_push($allData, $row);
    }
    $return['records'] = $allData;
    $return['filename'] = $filename;
    echo json_encode($return);
  }

  public function getLTENeighData(){
     $input1 = Input::get('input1');
    $input2 = Input::get('input2');
    $input3 = Input::get('input3');
    $input4 = Input::get('input4');
    $input5 = Input::get('input5');
    $dateTime = Input::get('dateTime');
    $dbname = $this->getMRDatabase(Input::get('select'));
    $dsn = "mysql:host=10.40.57.190:8066;dbname=$dbname";
    $db = new PDO($dsn, 'mr', 'mr'); 
    $table = 'mreServeNeigh';
    $result = array();
    /*$sql = "SELECT COLUMN_NAME from information_schema.COLUMNS where table_name = '".$table."' and table_schema = '".$dbname."'; ";*/
    $sql = "select * from $table where datetime_id like '".$dateTime."%' limit 1";
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

public function getGSMNeighDataLteAll(){
    $dbname = $this->getMRDatabase(Input::get('select'));
    $dateTime = Input::get('dateTime');
    $result = array();
    $return = array();
    $dsn = "mysql:host=10.40.57.190:8066;dbname=$dbname";
    $db = new PDO($dsn, 'mr', 'mr'); 
    $table = 'mreServeNeigh';

    /*$sql = "SELECT COLUMN_NAME from information_schema.COLUMNS where table_name = '".$table."' and table_schema = '".$dbname."'; ";
    $rows = $db->query($sql,PDO::FETCH_ASSOC)->fetchall();
    if(empty($rows)){
      $result['error'] = 'error';
      echo json_encode($result);
      return;
    }*/
    $sql = "select * from $table limit 1";
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

    $sql = "SELECT * FROM $table WHERE datetime_id like '".$dateTime."%';";
 
    $rows = $db->query($sql,PDO::FETCH_ASSOC)->fetchall();
    if (count($rows) == 0) {
        $result['error'] = 'error';
        return $result;
    }
    $rowsId = array();
    foreach ($rows as $row) {
      array_shift($row);
      array_push($rowsId, $row);
    }
    $result['rows'] = $rowsId;
    $filename = "common/files/GSMNeighborAnalysisLteAll" . date('YmdHis') . ".csv";
    $this->resultToCSV2($result, $filename);
    $return['filename'] = $filename;
    return $return;
  }

  public function getGSMNeighDataAll(){
    $dbname = $this->getMRDatabase(Input::get('select'));
    $dateTime = Input::get('dateTime');
    $result = array();
    $return = array();
    $dsn = "mysql:host=10.40.57.190:8066;dbname=$dbname";
    $db = new PDO($dsn, 'mr', 'mr'); 
    $table = 'mreServerNeighIrat';

 	$sql = "select * from $table limit 1";
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
   // $table1 = 'mreServerNeighIrat';
    $sql = "SELECT * FROM $table WHERE datetime_id like '".$dateTime."%';";
    // $sql1 = "SELECT * FROM $table1 WHERE (
    //     (nc_session_ratio >= ".$input1."/100 AND nc_session_num_hour >= $input2 AND nc_times_ratio >= ".$input3."/100 )
    //     OR ( nc_session_num_hour >= $input4 AND ncTop2_times_ratio >= ".$input5."/100 ))
    //     AND ( avg_mr_GsmNcellCarrierRSSI >- 90 AND avg_mr_LteScRSRQ >=- 15 ) AND isdefined IS NULL $limit;"; 
    $rows = $db->query($sql,PDO::FETCH_ASSOC)->fetchall();
    if (count($rows) == 0) {
        $result['error'] = 'error';
        return $result;
    }
    $rowsId = array();
    foreach ($rows as $row) {
      array_shift($row);
      array_push($rowsId, $row);
    }
    $result['rows'] = $rowsId;
    $filename = "common/files/GSMNeighborAnalysis" . date('YmdHis') . ".csv";
    $this->resultToCSV2($result, $filename);
    $return['filename'] = $filename;
    return $return;
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

	/*public function getGSMNeighDatabases(){
		$dsn = "mysql:host=10.40.57.190:8066;dbname=Global";
        $db = new PDO($dsn, 'mr', 'mr'); 
        $sql = "SHOW DATABASES;";
       	$rows = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
       	$result = array();
       	$text = array();
       	$i = 0;
       	foreach ($rows as $row) {
       	 	if(strpos($row['Database'], 'MR')===0){
       	 		$mrArr = $row['Database'];
       	 		$city = '{"text":"'.$mrArr.'","value":"'.$mrArr.'"}';
       	 		// $result[$i]['text'] = $mrArr;
       	 		// $result[$i++]['value'] = $mrArr;
       	 		array_push($result, $city);
       	 	}
       	} 

       	echo json_encode($result);
	} */
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
