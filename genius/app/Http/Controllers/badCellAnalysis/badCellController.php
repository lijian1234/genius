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

class badCellController extends Controller
{
  public function getAllCity(){
    // $databaseConns = DB::select('select * from databaseconn');
    //     $items = array();
    //     foreach($databaseConns as $databaseConn){
    //         $city = '{"text":"'.$databaseConn->cityChinese.'","value":"'.$databaseConn->connName.'"}';
    //         array_push($items, $city);
    //     }
    //     return response()->json($items);
    $cityClass = new DataBaseConnection();
    return $cityClass->getCityOptions();
  }

  public function templateQuery(){
    $startTime = Input::get('startTime');
    $endTime = Input::get('endTime');
    //$city = Input::get('city');
    $cell = Input::get('cell');
    $table = Input::get('table');

    $cityArr = Input::get('city');
    $city = array();
    $cityPY = new DataBaseConnection();
    foreach ($cityArr as $citys) {
      $cityStr = $cityPY->getCityByCityChinese($citys)[0]->connName;
      array_push($city, $cityStr);
    }

    $conn = @mysql_connect('localhost', 'root', 'mongs');
    if (!$conn) {
        die('Could not connect: ' . mysql_error());
    }

    mysql_select_db('AutoKPI', $conn);

    if ($cell=='') 
    {
      if (strcmp($startTime,$endTime) == 0) 
      {
        $filter=" where day_id='".$startTime."'";
      }
      else if (strcmp($startTime,$endTime) < 0) 
      {
        $filter=" where day_id>='".$startTime."' and day_id<='".$endTime."'";
      }
    }
    else
    {
      if (strcmp($startTime,$endTime) == 0) 
      {
        $filter=" where day_id='".$endTime."' and cell='".$cell."'";
      }
      else if (strcmp($startTime,$endTime) < 0) 
      {
        $filter=" where day_id>='".$startTime."' and day_id<='".$endTime."' and cell='".$cell."'";
      }
    }
    
    if($table == 'lowAccessCell_ex'){
      $query= "select city,subNetwork,cell, count(*) as 小时数, sum(RRC建立失败次数) as RRC建立失败次数 from " .$table.$filter." group by subNetwork,cell order by 小时数 desc"; 
      //print_r($query);return;
    }else if($table == 'badHandoverCell_ex'){
      $query= "select city,subNetwork,cell, count(*) as 小时数,sum(准备切换失败数) as 准备切换失败数 ,sum(执行切换失败数) as 执行切换失败数 ,sum(异频准备切换失败数) as 异频准备切换失败数 ,sum(同频准备切换失败数) as 同频准备切换失败数 ,
      sum(同频执行切换失败数) as 同频执行切换失败数,sum(异频执行切换失败数) as 异频执行切换失败数 from " .$table.$filter." group by subNetwork,cell order by 小时数 desc"; 
     // print_r($query);return;
    }else{
      $query= "select city,subNetwork,cell, count(*) as 小时数, ERAB建立失败次数 from " .$table.$filter." group by subNetwork,cell order by 小时数 desc"; 
    }
    
    //print_r($query);return; 
    $result=mysql_query($query); 
    $items = array();     
    for($i=0;$i<mysql_num_fields($result);$i++)   
    {   
    $items[$i]=mysql_field_name($result,$i);
    }
    $content=implode(",",$items); 
    //echo   $content;

    $item = $this->getData($city, $cell, $startTime, $endTime, $content, $table);
    //print_r($item);
    echo json_encode($item);
  }

  protected function getData($cityArr, $cell, $startTime, $endTime, $content, $table){
      $cityconn = array();
      $cityFilter = '(';
      for($i=0; $i<count($cityArr); $i++){
        $cityFilter .= "city='".$cityArr[$i]. "' or " ;
      }
      $cityFilter = substr($cityFilter,0,strlen($cityFilter)-3); 
      $cityFilter .= ")";
      //print_r($cityFilter);return;
      $conn = @mysql_connect('localhost', 'root', 'mongs');
      if (!$conn) {
        die('Could not connect: ' . mysql_error());
      }

    if(count($cityArr)==0){  
      if ($cell=='') 
      {
        if (strcmp($startTime,$endTime) == 0) 
        {
          $filter=" where day_id='".$startTime."'";
        }
        else if (strcmp($startTime,$endTime) < 0) 
        {
          $filter=" where day_id>='".$startTime."' and day_id<='".$endTime."'";
        }
      }
      else
      {
        if (strcmp($startTime,$endTime) == 0) 
        {
          $filter=" where day_id='".$endTime."' and cell='".$cell."'";
        }
        else if (strcmp($startTime,$endTime) < 0) 
        {
          $filter=" where day_id>='".$startTime."' and day_id<='".$endTime."' and cell='".$cell."'";
        }
      }
    }else{
      if ($cell=='') 
      {
        if (strcmp($startTime,$endTime) == 0) 
        {
          $filter=" where day_id='".$endTime."' and ".$cityFilter;
        }
        else if (strcmp($startTime,$endTime) < 0) 
        {
          $filter=" where day_id>='".$startTime."' and day_id<='".$endTime."' and ".$cityFilter;
        }
      }
      else
      {
        if (strcmp($startTime,$endTime) == 0) 
        {
          $filter=" where day_id='".$endTime."' and cell='".$cell."' and ".$cityFilter;
        }
        else if (strcmp($startTime,$endTime) < 0) 
        {
          $filter=" where day_id>='".$startTime."' and day_id<='".$endTime."' and cell='".$cell."' and ".$cityFilter;
        }
      }
    } 
      
      mysql_select_db('AutoKPI', $conn);
      $result = array();
      // $rs = mysql_query("select count(*) from (select city,subNetwork,cell, count(*) as times from lowAccessCell_ex ".$filter." group by subNetwork,cell order by times desc) as tmp");
      // $row = mysql_fetch_row($rs);
      // $result["total"] = $row[0];
      if($table=='lowAccessCell_ex'){
        $rs = mysql_query("select * from (select city,subNetwork,cell, count(*) as 小时数, RRC建立失败次数 as RRC建立失败次数 from ".$table.$filter." group by subNetwork,cell order by 小时数 desc) as tmp");
      }else if($table == 'badHandoverCell_ex'){
      $rs = mysql_query("select * from (select city,subNetwork,cell, count(*) as 小时数,sum(准备切换失败数) as 准备切换失败数,sum(执行切换失败数) as 执行切换失败数 ,sum(异频准备切换失败数) as 异频准备切换失败数,sum(同频准备切换失败数) as 同频准备切换失败数 ,sum(同频执行切换失败数) as 同频执行切换失败数,sum(异频执行切换失败数) as 异频执行切换失败数 from " .$table.$filter." group by subNetwork,cell order by 小时数 desc) as tmp"); 
      //print_r("select * from (select city,subNetwork,cell, count(*) as times,sum(准备切换失败数) as 准备切换失败数,sum(执行切换失败数) as 执行切换失败数 ,sum(异频准备切换失败数) as 异频准备切换失败数,sum(同频准备切换失败数) as 同频准备切换失败数 ,sum(同频执行切换失败数) as 同频执行切换失败数,sum(异频执行切换失败数) as 异频执行切换失败数 from " .$table.$filter." group by subNetwork,cell order by times desc) as tmp");return;
      }else{
        $rs = mysql_query("select * from (select city,subNetwork,cell, count(*) as 小时数, ERAB建立失败次数  from ".$table.$filter." group by subNetwork,cell order by 小时数 desc) as tmp");
      }
       
        //print_r("select * from (select city,subNetwork,cell, count(*) as times from ".$table.$filter." group by subNetwork,cell order by times desc) as tmp");return;

       $items = array();
    if($table=='lowAccessCell_ex'){
      while($row = mysql_fetch_assoc($rs)){
        $row['RRC建立失败次数'] = floatval($row['RRC建立失败次数']);
        array_push($items, $row);
      }
    }else if($table == 'highLostCell_ex'){
        while($row = mysql_fetch_assoc($rs)){
        $row['ERAB建立失败次数'] = floatval($row['ERAB建立失败次数']);
        array_push($items, $row);
    }
  }else if($table == 'badHandoverCell_ex'){
        while($row = mysql_fetch_assoc($rs)){
        $row['准备切换失败数'] = floatval($row['准备切换失败数']);
        $row['执行切换失败数'] = floatval($row['执行切换失败数']);
        $row['异频准备切换失败数'] = floatval($row['异频准备切换失败数']);
        $row['同频准备切换失败数'] = floatval($row['同频准备切换失败数']);
        $row['同频执行切换失败数'] = floatval($row['同频执行切换失败数']);
        $row['异频执行切换失败数'] = floatval($row['异频执行切换失败数']);
        array_push($items, $row);
    }
  }
    
      //print_r($items);return;
      $result['records'] = count($items);
      $result["rows"] = $items;
      //print_r($items);
      $result["content"] = $content;
      $filename = "common/files/" . $table . date('YmdHis') . ".csv";
      $result['filename'] = $filename;
      $this->resultToCSV2($result, $filename);
      //echo json_encode($result);
      //print_r($result);//($result);
      return $result;
  }

  protected function resultToCSV2($result, $filename){
    $csvContent = mb_convert_encoding($result['content'] . "\n", 'gb2312', 'utf-8');
    $fp = fopen($filename, "w");
    fwrite($fp, $csvContent);
    foreach ($result["rows"] as $row) {
        fputcsv($fp, $row);
    }
    fclose($fp);
  }
  protected function getalarmWorstCell(){
      $conn = @mysql_connect('localhost', 'root', 'mongs');
      if (!$conn) {
        die('Could not connect: ' . mysql_error());
      }
      mysql_select_db('Alarm', $conn);
      $result = array();
      $table = Input::get('table');
      $cell = Input::get('rowCell');

      //print_r($table);return;
      $rowData_2 = explode('_',$cell);
      $rowData_3 = $rowData_2[0];
      if($rowData_3 == $cell){
        $rowData_3 = substr($rowData_3,0,strlen($rowData_3)-1);
      }
      //print_r($table);return;
      //$rs = mysql_query("select * from ".$table.$filter." where $row='cell' ");
     if($table=='lowAccessCell'){  
     	$tableF = 'FMA_alarm_log';
     	$rs = mysql_query("select Event_time,meContext,eutranCell,Cease_time,SP_text,Problem_text from ".$tableF." where meContext='".$rowData_3."' order by Event_time DESC");
     //print_r("select Event_time,meContext,eutranCell,Cease_time,SP_text,Problem_text from ".$table." where meContext='".$rowData_3."'");return;
 	 }else {
 	 	$tableF = 'FMA_alarm_log';
 	 	$rs = mysql_query("select Event_time,Problem_text,Cease_time,SP_text from ".$tableF." where meContext='".$rowData_3."'order by Event_time DESC");
 	 	//print_r("select Event_time,Problem_text,Cease_time,SP_text from ".$table." where meContext='".$rowData_3."'");return;
	  }
      //print_r("select Event_time,meContext,eutranCell,Cease_time,SP_text,Problem_text from ".$table." where meContext='".$rowData_3."'");return;
       
        //print_r("select * from (select city,subNetwork,cell, count(*) as times from ".$table.$filter." group by subNetwork,cell order by times desc) as tmp");return;
      $items = array();
      while($row = mysql_fetch_assoc($rs)){
        array_push($items, $row);
      }
      $result['records'] = count($items);
      $result["rows"] = $items;
      //print_r($items);
      if($table=='lowAccessCell'){
     	$result["content"] = "Event_time,meContext,eutranCell,Cease_time,SP_text,Problem_text";
 	 }else {
 	 	$result["content"] = "Event_time,Problem_text,Cease_time,SP_text";
 	 	//print_r("select Event_time,Problem_text,Cease_time,SP_text from ".$table." where meContext='".$rowData_3."'");return;
	  }
      //$result["content"] = "Event_time,meContext,eutranCell,Cease_time,SP_text,Problem_text";
      $filename = "common/files/" . $table . date('YmdHis') . ".csv";
      $result['filename'] = $filename;
      //$this->resultToCSV2($result, $filename);
      //echo json_encode($result);
      return $result;

  }


  public function getChartData(){
   // print_r('123');
      $conn = @mysql_connect('localhost', 'root', 'mongs');
      if (!$conn) {
        die('Could not connect: ' . mysql_error());
      }
      mysql_select_db('AutoKPI', $conn);
      $result = array();
      $table = Input::get('table');

      //print_r($table);return;
      $cell = Input::get('rowCell');
      $startTime = Input::get('startTime');
      $endTime = Input::get('endTime');
      $yAxis_name_left = Input::get('yAxis_name_left');
      $yAxis_name_right= Input::get('yAxis_name_right');

       if (strcmp($startTime,$endTime) == 0) 
    {
        $res=mysql_query("select day_id,hour_id,". $yAxis_name_left.",". $yAxis_name_right." from ".$table." where day_id='".$endTime."' and cell='".$cell."'");
    }
    else if (strcmp($startTime,$endTime) < 0) 
    {
        $res=mysql_query("select day_id,hour_id,". $yAxis_name_left.",".$yAxis_name_right." from ".$table." where day_id>='".$startTime."' and day_id<='". $endTime."' and cell='".$cell."'");
    }
    //print_r("select day_id,hour_id,". $yAxis_name_left.",".$yAxis_name_right." from ".$table." where day_id>='".$startTime."' and day_id<='". $endTime."' and cell='".$cell."'");return;
    $yAxis = array();
    $yAxis_2 = array();
    $items = array();
    $returnData = array();
    $series = array();
    $series_2 = array();
    $categories = array();
    
    while($line = mysql_fetch_row($res))
    {
        // print_r($line);

        //$data['data']=$line[4];
        $time=strval(strval($line[0])." ".strval($line[1])).":00";

        $time =mb_convert_encoding($time, 'gb2312', 'utf-8');
        
        array_push($yAxis,$line[2]);
        array_push($yAxis_2,$line[3]);        
        array_push($categories,$time);
        
    }
    
    $series['name']= $yAxis_name_left;
    $series['color']='#89A54E';
    $series['type']='spline';
    $series['data']=$yAxis;
    

    $series_2['name']=$yAxis_name_right;
    $series_2['color']='#4572A7';
    $series_2['type']='column';
    $series_2['yAxis']=1;
    $series_2['data']=$yAxis_2;
    
    array_push($items,$series_2);
    array_push($items,$series);

    $returnData['categories']=$categories;
    $returnData['series']=$items;

    echo json_encode($returnData);

 }


 // public function fetchPageData(){
 //    $db_settings = array(
 //    'rdbms' => 'MYSQLi',
 //    'db_server' => 'SERVER_NAME OR IP',
 //    'db_user' => 'DB USER',
 //    'db_passwd' => 'DB PASS',
 //    'db_name' => 'DB NAME',
 //    'db_port' => '3306',
 //    'charset' => 'utf8',
 //    'use_pst' => true, // use prepared statements
 //    'pst_placeholder' => 'question_mark'
 //  );
   
 //  $ds = new dacapo($db_settings, null);
   
 //  $page_settings = array(
 //    "selectCountSQL" => "SELECT count(id) as totalrows FROM customers",
 //    "selectSQL" => "SELECT c.id as customer_id, c.lastname, c.firstname, c.email, g.gender, c.date_updated
 //                    FROM customers c INNER JOIN lk_genders g ON (c.lk_genders_id = g.id)",
 //    "page_num" => $_POST['page_num'],
 //    "rows_per_page" => $_POST['rows_per_page'],
 //    "columns" => $_POST['columns'],
 //    "sorting" => isset($_POST['sorting']) ? $_POST['sorting'] : array(),
 //    "filter_rules" => isset($_POST['filter_rules']) ? $_POST['filter_rules'] : array()
 //  );
   
 //  $jfr = new jui_filter_rules($ds);
 //  $jdg = new bs_grid($ds, $jfr, $page_settings, $_POST['debug_mode'] == "yes" ? true : false);
   
 //  $data = $jdg->get_page_data();
   
 //  // data conversions (if necessary)
 //  foreach($data['page_data'] as $key => $row) {
 //    // this will convert Lastname to a link
 //    $data['page_data'][$key]['cell'] = "<a href=\"/test/{$row['customer_id']}\">{$row['cell']}</a>";
 //    // this will format date_updated (attention date_convert is a local function)
 //   $data['page_data'][$key]['RRC建立失败次数'] = "<a href=\"/test/{$row['customer_id']}\">{$row['RRC建立失败次数']}</a>";
 //  }
   
 //  echo json_encode($data);

    
 //   }

    public function getLTENeighborHeader(){
        $cityEN = Input::get('city');
        $cityCH = $this->ENcityToCHcity($cityEN);
        $dbname = $this->getMRDatabase($cityCH);
        $dsn = "mysql:host=10.40.57.190:8066;dbname=$dbname";
        $db = new PDO($dsn, 'mr', 'mr'); 
        $table = 'mreServeNeigh';
        $result = array();
        $sql = "select * from $table limit 1";
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
    public function getLTENeighborData(){

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

        $cell = Input::get('cell');
        $dsn = "mysql:host=localhost;dbname=mongs";
        $db = new PDO($dsn, 'root', 'mongs');
        $sql = "select ecgi from siteLte where cellName = '$cell'";
        $res = $db->query($sql);
        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        $ecgi = $row[0]['ecgi'];

        $cityEN = Input::get('city');
        $cityCH = $this->ENcityToCHcity($cityEN);
        $dbname = $this->getMRDatabase($cityCH);

        $dateTime = Input::get('dateTime');

        $result = array();
        $return = array();
        $dsn = "mysql:host=10.40.57.190:8066;dbname=$dbname";
        $db = new PDO($dsn, 'mr', 'mr'); 
        $table = 'mreServeNeigh';
        $sql = "select * from $table limit 1";
        $rs = $db->query($sql,PDO::FETCH_ASSOC);
        $keys = array();
        $rows = $rs->fetchall();
        $keys = array_keys($rows[0]);
        $text = '';
        foreach ($keys as $key) {
            if($key == 'id'){
                continue;
            }
            $text .= $key.',';
        }
        $text = substr($text,0,strlen($text)-1);
        $result['text'] = $text;
        $sql = "SELECT * FROM $table WHERE ecgi = '$ecgi' AND mr_LteScEarfcn != mr_LteNcEarfcn AND (
          (nc_session_ratio >= ".$input1."/100 AND nc_session_num_hour >= $input2 AND nc_times_ratio >= ".$input3."/100 )
          OR ( nc_session_num_hour >= $input4 AND ncTop2_times_ratio >= ".$input5."/100 )) AND isdefined IS NULL AND datetime_id like '".$dateTime."%';";
        $rows = $db->query($sql,PDO::FETCH_ASSOC)->fetchall();
        /*if (count($rows) == 0) {
            $result['error'] = 'error';
            return json_encode($result);
        }*/
        $return["total"] = count($rows);
        $rowsId = array();
        foreach ($rows as $row) {
          array_shift($row);
          array_push($rowsId, $row);
        }
        $result['rows'] = $rowsId;
        //$result['filename'] = $filename;
        $sql = "SELECT * FROM $table WHERE ecgi = '$ecgi' AND mr_LteScEarfcn != mr_LteNcEarfcn AND (
          (nc_session_ratio >= ".$input1."/100 AND nc_session_num_hour >= $input2 AND nc_times_ratio >= ".$input3."/100 )
          OR ( nc_session_num_hour >= $input4 AND ncTop2_times_ratio >= ".$input5."/100 )) AND isdefined IS NULL AND datetime_id like '".$dateTime."%' $limit;";
        //print_r($sql);
        $rows = $db->query($sql,PDO::FETCH_ASSOC)->fetchall();
        $allData = array();
        foreach ($rows as $row) {
          array_push($allData, $row);
        }
        $return['records'] = $allData;
        echo json_encode($return);
    }
    public function getMRDatabase($city){
        $dbc = new DataBaseConnection();
        return $dbc->getMRDatabase($city);
    }
    public function ENcityToCHcity($cityEN) {
        $cityCH = '';
        switch ($cityEN) {
            case 'changzhou':
                $cityCH = '常州';
                break;
            case 'nantong':
                $cityCH = '南通';
                break;
            case 'suzhou':
                $cityCH = '苏州';
                break;
            case 'wuxi':
                $cityCH = '无锡';
                break;
            case 'zhenjiang':
                $cityCH = '镇江';
                break;
        }
        return $cityCH;
    
    }

    public function getGSMNeighborHeader(){
        $cityEN = Input::get('city');
        $cityCH = $this->ENcityToCHcity($cityEN);
        $dbname = $this->getMRDatabase($cityCH);
        $dsn = "mysql:host=10.40.57.190:8066;dbname=$dbname";
        $db = new PDO($dsn, 'mr', 'mr'); 
        $table = 'mreServerNeighIrat';
        $result = array();
        $sql = "select * from $table limit 1";
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
    public function getGSMNeighborData(){

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

        $cell = Input::get('cell');
        $dsn = "mysql:host=localhost;dbname=mongs";
        $db = new PDO($dsn, 'root', 'mongs');
        $sql = "select ecgi from siteLte where cellName = '$cell'";
        $res = $db->query($sql);
        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        $ecgi = $row[0]['ecgi'];

        $cityEN = Input::get('city');
        $cityCH = $this->ENcityToCHcity($cityEN);
        $dbname = $this->getMRDatabase($cityCH);

        $dateTime = Input::get('dateTime');

        $result = array();
        $return = array();
        $dsn = "mysql:host=10.40.57.190:8066;dbname=$dbname";
        $db = new PDO($dsn, 'mr', 'mr'); 
        $table = 'mreServerNeighIrat';
        $sql = "select * from $table limit 1";
        $rs = $db->query($sql,PDO::FETCH_ASSOC);
        $keys = array();
        $rows = $rs->fetchall();
        $keys = array_keys($rows[0]);
        $text = '';
        foreach ($keys as $key) {
            if($key == 'id'){
                continue;
            }
            $text .= $key.',';
        }
        $text = substr($text,0,strlen($text)-1);
        $result['text'] = $text;
        $sql = "SELECT * FROM $table WHERE ecgi = '$ecgi' AND (
        (nc_session_ratio >= ".$input1."/100 AND nc_session_num_hour >= $input2 AND nc_times_ratio >= ".$input3."/100 )
        OR ( nc_session_num_hour >= $input4 AND ncTop2_times_ratio >= ".$input5."/100 ))
        AND ( avg_mr_GsmNcellCarrierRSSI >$input6 AND avg_mr_LteScRSRQ >=$input7 ) AND isdefined IS NULL AND datetime_id like '".$dateTime."%';";
        $rows = $db->query($sql,PDO::FETCH_ASSOC)->fetchall();
        /*if (count($rows) == 0) {
            $result['error'] = 'error';
            return json_encode($result);
        }*/
        $return["total"] = count($rows);
        $rowsId = array();
        foreach ($rows as $row) {
          array_shift($row);
          array_push($rowsId, $row);
        }
        $result['rows'] = $rowsId;
        //$result['filename'] = $filename;
        $sql = "SELECT * FROM $table WHERE ecgi = '$ecgi' AND (
        (nc_session_ratio >= ".$input1."/100 AND nc_session_num_hour >= $input2 AND nc_times_ratio >= ".$input3."/100 )
        OR ( nc_session_num_hour >= $input4 AND ncTop2_times_ratio >= ".$input5."/100 ))
        AND ( avg_mr_GsmNcellCarrierRSSI >$input6 AND avg_mr_LteScRSRQ >=$input7 ) AND isdefined IS NULL AND datetime_id like '".$dateTime."%' $limit;";
        //print_r($sql);
        $rows = $db->query($sql,PDO::FETCH_ASSOC)->fetchall();
        $allData = array();
        foreach ($rows as $row) {
          array_push($allData, $row);
        }
        $return['records'] = $allData;
        echo json_encode($return);
    }

}


