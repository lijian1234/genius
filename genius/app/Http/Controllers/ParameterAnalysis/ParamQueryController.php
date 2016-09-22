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
/**
 * @desc ：参数查询
 * Time：2016/08/01 17:46:15
 * @author Wuyou
 * @param 参数类型
 * @return 返回值类型
*/
class ParamQueryController extends Controller
{
  /**
   * getParamTasks()
   * 获取日期（数据库名称）
   * @param mixed $city
   * @return
   */
  public function getParamTasks(){
  $filter = '';
  $items = array();
  $i = 0;
  $user = Auth::user();//获取登录用户信息
  if ($user == NULL) {
  $items[$i] = 'login';
  return response()->json($items);
  }else{
  $userName=$user->user;//需要获取当前登录用户！！！！！
  //dump($userName);
  if ($user!='admin') {
  $filter = " and owner in('$userName','admin')";
  }
  } 
  // die;
  $tasks = DB::select("select * from task where type=:type and status=:status $filter order by taskName desc",['type'=>'parameter','status'=>'complete']);
  
  foreach ($tasks as $task) {
  $items[$i++] = '{"text":"'.$task->taskName.'"}';
  }
  return response()->json($items);//需要通过response返回响应数据
  }
  /**
   * getParamCitys()
   * 获取城市
   * @param mixed $city
   * @return
   */
  public function getParamCitys(){
  //return 'text';
  /*$databaseConns = DB::select('select * from databaseconn');
  $items = array();
  foreach ($databaseConns as $databaseConn) {
  $city = '{"text":"'.$databaseConn->cityChinese.'","value":"'.$databaseConn->connName.'"}';
  array_push($items, $city);
  }
  return response()->json($items);*/
    $dbc = new DataBaseConnection();
    return $dbc->getCityOptions();
  }
  /**
   * getParamTableField()
   * 获取表头
   * @return
   */
  public function getParamTableField(){
  
  $db = Input::get('db');
  $table = Input::get('table');
  if($table == 'EUtranFreqRelation_FDD'){
    $table = 'EUtranFreqRelation_2';
  }
  if($table == 'EUtranCellRelation_FDD'){
    $table = 'EUtranCellRelation_2';
  }

  $dsn = "mysql:host=localhost;dbname=$db";
  $dbn = new PDO($dsn, 'root', 'mongs');
  $result = array();
  //$query= "select * from ".$_REQUEST['table']." limit 1"; 
  $query= "select * from ".$table." limit 1";
  $rs = $dbn->query($query,PDO::FETCH_ASSOC);
  $rs = $rs->fetchAll();
  //dump($rs[0]);
  return $rs[0];
  
  }
   /**
   * getParamItems()
   * 获取数据
   * @return
   */
  public function getParamItems(){
    $dbc = new DataBaseConnection();

    $db = Input::get('db');
    $table = Input::get('table');
    if($table == 'EUtranFreqRelation_FDD'){
      $table = 'EUtranFreqRelation_2';
    }
    if($table == 'EUtranCellRelation_FDD'){
      $table = 'EUtranCellRelation_2';
    } 
    $erbs = Input::get('erbs');
    $citys = Input::get('citys');
    $paramLength = Input::get('paramLength');
  
    //dump($paramNames);
    //die;
    $conn = @mysql_connect('localhost', 'root', 'mongs');
    if (!$conn) {
    die('Could not connect: ' . mysql_error());
    }
    mysql_select_db($db, $conn);
  
    $dsn = "mysql:host=localhost;dbname=mongs";
    //$dbn = new PDO($dsn, 'root', 'mongs');
    $pdo = DB::connection()->getPdo();

    $displayStart = Input::get('page');
    $displayLength = Input::get('limit');
    $offset = ($displayStart - 1)*$displayLength;
    $orderFilter = " ";
    $limit = " limit $offset,$displayLength ";
    $filter = '';

    //获取数据开始
    $subNetwork = '';
    if ($citys != '') {
      foreach ($citys as $city) {
        //$subNetwork .= $this->getSubNets($pdo, $city);
        $subNetwork .= $dbc->getSubNets($city).',';
      }
      $subNetwork = substr($subNetwork , 0 , -1);
    }
    if (trim($erbs) != '') {
      $erbsArr = explode(",", $erbs);
      $erbsStr = "";
      for($i=0; $i<count($erbsArr); $i++){
        $erbsStr = $erbsStr."'".$erbsArr[$i]."',";
      }
      $erbsStr = substr($erbsStr, 0,strlen($erbsStr) -1);
      //var_dump($erbsStr);
      if ($subNetwork != '') {
        $filter = " where meContext in (".$erbsStr.") and subNetwork in (".$subNetwork.")";
      }else{
        $filter = " where meContext in (".$erbsStr.")";
      }
      }else{
        if ($subNetwork != '') {
        $filter = " where subNetwork in (".$subNetwork.")";
        }
      }
      $result = array();
      $rs = mysql_query("select count(*) from ".$table.$filter);
      //echo "select count(*) from ".$table.$filter;
      if ($rs) {
        $row = mysql_fetch_row($rs);
        $result["total"] = $row[0];
      }
      $rs = mysql_query("select * from ".$table.$filter.$orderFilter.$limit);
      $items = array();
      if ($rs) {
        while ($row = mysql_fetch_object($rs)) {
          $row = $this->substring_paramData($row,$paramLength);
          array_push($items, $row);
        }
        $result["records"] = $items;
      }
    return json_encode($result);
  }
   /**
  * @desc ：根据表格头宽截取数据
  * Time：2016/07/27 15:31:33
  * @author Wuyou
  * @param 参数类型
  * @return 返回值类型
   */
  function substring_paramData($row,$paramLength){
    $paramNames = array_keys($paramLength);
    foreach ($paramNames as $key => $value) {
      $length = $paramLength[$value];
      if ($row->$value != '') {
        $row->$value = substr($row->$value, 0,$length-1);
      }
    }
    return $row;
  }
  /**
   * exportParamFile()
   * 获取数据
   * @return
   */
  public function exportParamFile(Request $request){
    $dbc = new DataBaseConnection();

    $db = Input::get('db');
    $table = Input::get('table');
    if($table == 'EUtranFreqRelation_FDD'){
      $table = 'EUtranFreqRelation_2';
    }
    if($table == 'EUtranCellRelation_FDD'){
      $table = 'EUtranCellRelation_2';
    } 
    $erbs = Input::get('erbs');
    $citys = Input::get('citys');

    $conn = @mysql_connect('localhost', 'root', 'mongs');
    if (!$conn) {
    die('Could not connect: ' . mysql_error());
    }
    mysql_select_db($db, $conn);
    
    $dsn = "mysql:host=localhost;dbname=mongs";
    //$dbn = new PDO($dsn, 'root', 'mongs');
    $pdo = DB::connection()->getPdo();
    $pdo->dsn = $dsn;
    $pdo->username = 'root';
    $pdo->password = 'mongs';
     
    $filter='';
    $result = array();
    $items = array();
    $fileContent = array();
    $csvContent = "";

    //获取数据开始
    $subNetwork = '';
    if ($citys != '') {
      foreach ($citys as $city) {
        $subNetwork .= $dbc->getSubNets($city).',';
      }
      $subNetwork = substr($subNetwork , 0 , -1);
    }
    if (trim($erbs) != '') {
      $erbsArr = explode(",", $erbs);
      $erbsStr = "";
      for ($i=0; $i<count($erbsArr); $i++) { 
        $erbsStr = $erbsStr."'".$erbsArr[$i]."',";
      }
      $erbsStr = substr($erbsStr, 0,strlen($erbsStr) -1);
      if ($subNetwork != '') {
      $filter = " where meContext in (".$erbsStr.") and subNetwork in (".$subNetwork.")";
      }else{
      $filter = " where meContext in (".$erbsStr.")";
      }
    }else{
      if ($subNetwork != '') {
      $filter = " where subNetwork in (".$subNetwork.")";
      }
    }
    $rs = mysql_query("select count(*) from $table".$filter);
    if ($rs) {
      $row = mysql_fetch_row($rs);
      $result['total'] = $row[0];
    }
    $rs = mysql_query("select * from ".$table.$filter); 
  //get field
    if ($rs) {
      for ($i=0; $i<mysql_num_fields($rs); $i++) { 
        $items[$i] = mysql_field_name($rs,$i);
      }
      $csvContent = implode(",",$items);

      $csvContent = mb_convert_encoding($csvContent, 'gbk', 'utf-8'); 

      $fileContent[] = $csvContent.",";
      /*get content*/
      while($items = mysql_fetch_row($rs)){

        $csvContent = "";

        foreach ($items as $column) {
          $column = trim($column);

          $column = str_replace(",", " ", $column);
          $csvContent = $csvContent.",".$column;
        }
        $csvContent = substr($csvContent, 1, strlen($csvContent) - 1);
        //print($csvContent);
        $csvContent = $csvContent.",";
        $csvContent = mb_convert_encoding($csvContent, 'gbk', 'utf-8');

        $fileContent[] = $csvContent;
      }
      $filename = '';
      if ($erbs == '') {
        $filename="files/参数查询_".$db."_".$table."_".date('YmdHis').".csv";
      }else{
        $filename="files/参数查询_".$db."_".$table."_".$erbs."_".date('YmdHis').".csv";
      }
      $fp = fopen($filename,'w+');
      //chmod($filename,777);
      foreach ($fileContent as $line) {
        $lineArr = array();
        $lineArr = explode(',',$line);
        fputcsv($fp,$lineArr);
      }
      fclose($fp); 
      $result["result"] = 'true';
      $result["filename"] = $filename;
    }
    return json_encode($result);
  }
}
