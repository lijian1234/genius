<?php

namespace App\Http\Controllers\ParameterAnalysis;
use App\Http\Controllers\Controller;

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

class paramDistributionController extends Controller{

	public function getDate(){
	 	$dsn = "mysql:host=localhost;dbname=mongs";
        $db = new PDO($dsn, 'root', 'mongs');

		$filter = '';
	    $items = array();
	    $i = 0;
	    $user = Auth::user();//获取登录用户信息
	     
       	$userName=$user->user;//需要获取当前登录用户！！！！！
      	if($user!='admin'){
        	$filter = " and owner in('$userName','admin')";
      	}
	    
	     // die;
	    $sql = "select * from task where type='parameter' and status='complete' $filter order by taskName desc";
	    //$tasks = DB::select("select * from task where type=:type and status=:status $filter order by taskName desc",['type'=>'parameter','status'=>'complete']);
	    $res = $db->query($sql);

        $row = $res->fetchAll(PDO::FETCH_ASSOC);
	    foreach ($row as $task) {
	      //$items[$i++] = '{"text":"'.$task->taskName.'"}';
	      array_push($items, ["id"=>$task["taskName"],"text"=>$task["taskName"]]);
	    }
	    //return response()->json($items);//需要通过response返回响应数据
	    echo json_encode($items);
	}

	public function getParameterList(){
		$schema = input::get("task");
		$table = input::get("mo");
		$pattern = input::get("pattern");

		$dsn = "mysql:host=localhost;dbname=information_schema";
        $db = new PDO($dsn, 'root', 'mongs');
        if($pattern){
        	$sql = "select COLUMN_NAME from `COLUMNS` where TABLE_NAME='$table' and TABLE_SCHEMA='$schema' and COLUMN_NAME not LIKE '%id' and COLUMN_NAME LIKE '%".$pattern."%'";
        }else{
        	$sql = "select COLUMN_NAME from `COLUMNS` where TABLE_NAME='$table' and TABLE_SCHEMA='$schema' and COLUMN_NAME not LIKE '%id'";
        }
       // print_r($sql);
		$rs = $db->query($sql);
		$items = array();
		$returnData = array();
		$i = 0;
		$j = 1;
		if ($rs) {
			$row = $rs->fetchAll(PDO::FETCH_ASSOC);
			foreach ($row as $r){ 
			//while ($row = mysql_fetch_array($rs)) {
				if($pattern){
					$j++;
					$items[$j] = '{"id":'.$j.',"text":"'.$r['COLUMN_NAME'].'"}';
				}else{
					if($i++ > 3){
						$j++;
						$items[$j] = '{"id":'.$j.',"text":"'.$r['COLUMN_NAME'].'"}';
					}
				}
				
				
			}
		}
		$content = implode(",", $items);
		//$returnData['content'] = '[{"id":1,"text":"","nodes":['.$content.']}]';
		$returnData['content'] = '['.$content.']';
		$returnData['count'] = $j;
		echo json_encode($returnData);

	}

	/*public function getCity(){
		

		$table = input::get("table");
		$dbName = input::get("db");

		$dsn = "mysql:host=localhost;dbname=".$dbName;
        $db = new PDO($dsn, 'root', 'mongs');

		$items = array();

		$sql = "SELECT connName,cityChinese FROM `" .$table. "` order by id ASC";
		$res = $db->query($sql);
		if($res){
			$row = $res->fetchAll(PDO::FETCH_ASSOC);
			foreach ($row as $r){
				array_push($items, $r);
			}
		}
		echo json_encode($items);
	}*/
	public function getCity(){
		

		$table = input::get("table");
		$dbName = input::get("db");

		$dsn = "mysql:host=localhost;dbname=".$dbName;
        $db = new PDO($dsn, 'root', 'mongs');

		$items = array();

		$sql = "SELECT DISTINCT cityChinese FROM `" .$table. "` order by id ASC";
		$res = $db->query($sql);
		if($res){
			$row = $res->fetchAll(PDO::FETCH_ASSOC);
			foreach ($row as $r){
				array_push($items, $r);
			}
		}
		echo json_encode($items);
	}

	public function getChartData(){
		$table = input::get("table");
	    $parameterName = input::get("parameterName");
	    $dbName = input::get("db");

     	$dsn1 = "mysql:host=localhost;dbname=".$dbName;
        $db1 = new PDO($dsn1, 'root', 'mongs');



	    $dsn = "mysql:host=localhost;dbname=mongs";
	    $db = new PDO($dsn, 'root', 'mongs');

	    $city = input::get('city');
	   // print_r($city);
	    $allYAxis = array();
	    $series = array();
	    $categories = array();
	    $subNetwork_ = '';
	   
	    $sql_parameterDistribute = "select DISTINCT $parameterName from $table ORDER BY CAST($parameterName AS signed)";
	    $rs_parameterDistribute =  $db1->query($sql_parameterDistribute);
	    //print_r($rs_parameterDistribute);
	    if($rs_parameterDistribute){
	    	$rows = $rs_parameterDistribute->fetchAll(PDO::FETCH_ASSOC);
			foreach ($rows as $row){
	        //while ($rows = mysql_fetch_assoc($rs_parameterDistribute)) {
	            if($row[$parameterName] !="" && substr($row[$parameterName], 0,4) !="!!!!"){
	                array_push($categories, $row[$parameterName]);
	            }
	            
	        }
	    }
	    for($i=0; $i<count($city); $i++){
	        $cityEng = $city[$i]['cityChinese'];
	        $subNetwork_ = substr($this->getSubNets($db, $cityEng) , 0 , -1);
	        $sql_filter_ = "select DISTINCT $parameterName ,count(*) as occurs from $table t where subNetwork in(".$subNetwork_.")"." GROUP BY CAST(t.$parameterName AS signed) ORDER BY CAST(t.$parameterName AS signed) ";
	        //echo "string".$sql_filter_;
	        $rs_filter =  $db1->query($sql_filter_);
	        $series_ = array();
	        $array_ = array();
	        $series_['name'] = $cityEng;
	        if($rs_filter){
	            $k = 0 ;
	            $l = 0;
	            $rows = $rs_filter->fetchAll(PDO::FETCH_ASSOC);
				foreach ($rows as $row){
         		//while($rows = mysql_fetch_assoc($rs_filter)){
                 	if($row[$parameterName] !="" && substr($row[$parameterName], 0,4) !="!!!!"){
	                    for($j =$k; $j<count($categories); $j++){
	                        if($row[$parameterName] == $categories[$j]){
	                            array_push($array_, intval($row['occurs']));
	                            $k = $j + 1;
	                            $l++;
	                            break;
	                        }else{
	                            array_push($array_ , 0);
	                            $l++;
	                        }
	                    }
	                    
	                }
	            }
	            if($l < count($categories)){
	                for($j = $l ;$j<count($categories);$j++){
	                     array_push($array_ , 0);
	                }
	            }
	         }else{
	            for($j =0; $j<count($categories); $j++){
	                array_push($array_, intval(0));
	            }
	         }
	         $series_['data'] = $array_;
	         array_push($allYAxis, $array_);
	         array_push($series, $series_);
	    }
	   //循环获取最大最小值
	    //if (count($categories) > 0) {
	       
	     

	    $maxValue = $minValue = $this->isNull($allYAxis);
	   // $maxValue = $allYAxis[0][0];
	   // $minValue = $allYAxis[0][0];
	    for($i=0; $i<count($allYAxis); $i++){
	        for($j=0; $j<count($allYAxis[$i]); $j++){
	            if($allYAxis[$i][$j] > $maxValue){
	                $maxValue = $allYAxis[$i][$j];
	            }
	             /*if($allYAxis[$i][$j] < $minValue){
	                $minValue = $allYAxis[$i][$j];
	            }*/
	            $minValue = 0;
	        }
	    }
	    $y_data = $this->getValues($minValue,$maxValue);
	    sort($y_data);
	    //}
	   // $y_data = getValues($minValue,$maxValue);
	   // sort($y_data);

	    
	    $returnData['categories']=$categories;
	    $returnData['series'] = $series;
	    $returnData['y_data'] = isset($y_data) ? $y_data : [null,null,null,null,null];
	    
	    echo json_encode($returnData);

	    /**
	     * getSubNets()
	     * @param mixed $db, $city
	     * @return
	     */

	}

	public function isNull($arr = array()){
        for($i=0; $i<count($arr); $i++){
            for($j=0; $j<count($arr[$i]); $j++){
                if($arr[$i][$j] != "null"){
                    return $arr[$i][$j];
                }
            }
        }
    }
    public function getValues($min, $max){
        $y_data = array();
        $y_data[2] = ($min + $max)/2;
        $y_data[3] = ($max + $y_data[2])/2;
        $y_data[1] = ($min + $y_data[2])/2;
        $y_data[0] = $min;
        $y_data[4] = $max;
        //print_r($y_data);
        for($i=0; $i<count($y_data); $i++){
            //$y_data[$i] = 100 * $y_data[$i];
            $y_data[$i] = round($y_data[$i], 0);
        }
        return $y_data;
    }
    public function getSubNets($db, $city){

        $SQL = "select subNetwork from databaseconn where cityChinese = '$city'";
        $res = $db->query($SQL);
        $row = $res->fetch(PDO::FETCH_ASSOC);
        $subNets = $row['subNetwork'];
        $subNetArr = explode(",", $subNets);
        $subNetsStr = '';
        foreach ($subNetArr as $subNet) {
            $subNetsStr.= "'".$subNet."',";
        }
        return $subNetsStr;
    }

    public function getCitySelect(){

    	$dsn = "mysql:host=localhost;dbname=mongs";
	    $db = new PDO($dsn, 'root', 'mongs');

    	$rs = $db->query("select DISTINCT cityChinese from databaseconn");
		
		$items = array();
		$citys = array();

		$rows = $rs -> fetchAll(PDO::FETCH_ASSOC);
		foreach($rows as $row){
			array_push($citys,array("label"=>$row['cityChinese'],"value"=>$row['cityChinese']));
		}
		echo json_encode($citys);
		
    }
    public function getTableHeader(){

		
	  	$schema = input::get("db");
	  	$table = input::get("table");
	  
	  	$dsn = "mysql:host=localhost;dbname=information_schema";
        $db = new PDO($dsn, 'root', 'mongs');

	  	$sql = "select COLUMN_NAME from `COLUMNS` where TABLE_NAME='$table' and TABLE_SCHEMA='$schema' and COLUMN_NAME LIKE '%id'";
	  	$rs = $db->query($sql);
	  	$params = '';
	  	$i = 0;
	  	if ($rs) {
	  		$rows = $rs -> fetchAll(PDO::FETCH_ASSOC);
	    	foreach($rows as $row){
	      		if($i++ > 0){
        			$params = $params.$row['COLUMN_NAME'].',';
	      		}
	      
	    	}
	  	}
	  	$dsn = "mysql:host=localhost;dbname=".$schema;
        $db = new PDO($dsn, 'root', 'mongs');
	  	$parameterName = input::get("parameterName");
	
	  	$query= "select id,recordTime,mo,subNetwork,meContext,$params $parameterName from ".$table." limit 1"; 
	  	$result=$db->query($query); 
	  	$items = array();
	  	$content = '';
	  	if($result){
	  		$rows = $result -> fetchAll(PDO::FETCH_ASSOC);

	  		//print_r($rows);return;
	  		foreach ($rows[0] as $key=>$value) {
	  			array_push($items,$key);
	  		}
	    	//print_r($items);return;
	    	
	  	}   
  	 	$result = array();
        $result['text'] = implode(",",$items);
        echo json_encode($result);
    }

    public function getTableData(){
    	
		$schema = input::get("db");
	  	$table = input::get("table");

		$dsn = "mysql:host=localhost;dbname=information_schema";
        $db = new PDO($dsn, 'root', 'mongs');

	  	$sql = "select COLUMN_NAME from `COLUMNS` where TABLE_NAME='$table' and TABLE_SCHEMA='$schema' and COLUMN_NAME LIKE '%id'";
	  	$rs = $db->query($sql);
	  	$params = '';
  		$i = 0;
		if ($rs) {
	  		$rows = $rs -> fetchAll(PDO::FETCH_ASSOC);
	    	foreach($rows as $row){
	      		if($i++ > 0){
        			$params = $params.$row['COLUMN_NAME'].',';
	      		}
	    	}
	  	}
		$dsn1 = "mysql:host=localhost;dbname=".$schema;
        $db1 = new PDO($dsn1, 'root', 'mongs');

		$dsn = "mysql:host=localhost;dbname=mongs";
		$db = new PDO($dsn, 'root', 'mongs');

		$limit='';
		$page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
		$rows = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;
		//$table = $_REQUEST['table'];
		$parameterName = input::get("parameterName");
		$orderFilter = '';
		$queryFilter = '';
		
		$sort = isset($_REQUEST['sort']) ? strval($_REQUEST['sort']) : 'recordTime';  
		$order = isset($_REQUEST['order']) ? strval($_REQUEST['order']) : 'desc'; 
		$orderFilter = " order by $sort $order ";

		$offset = ($page-1)*$rows;
		$limit=" limit $offset,$rows";
		$filter='';
		
		$citys = json_decode($_REQUEST['city'], true);
		/*$citys = $this->parseCity($phpCity);
		print_r($citys);*/
		$subNetwork = '';
		foreach ($citys as $city) {
			$subNetwork .= $this->getSubNets($db, $city);
		}
		$subNetwork = substr($subNetwork , 0 , -1);
		if($subNetwork != ''){
			$filter=" where subNetwork in (".$subNetwork.")";
		}
		$result = array();
		$rs = $db1->query("select count(*) from ".$table.$filter);
		if($rs){
			$row = $rs->fetchAll(PDO::FETCH_ASSOC);
            $result["total"] = $row[0]['count(*)'];
		}
		$sql = "select id,recordTime,mo,subNetwork,meContext,$params $parameterName from ".$table.$filter.$orderFilter.$limit;
		$rs = $db1->query($sql);
		$items = array();
		if($rs){
			$row = $rs->fetchAll(PDO::FETCH_ASSOC);
			foreach ($row as $r) {
				array_push($items, $r);
			}
			
			$result["records"] = $items;
		}
		echo json_encode($result);


    }
 /*   public function parseCity($city){
	    $result = array();
	    foreach ($city as $cityRow) {
	    	$result[] = $cityRow;
	        if ($cityRow['checked'] === true) {
	            //$result[] = $cityRow['text'];
	            if (isset($cityRow['value'])) {
	            	$result[] = $cityRow['value'];
	            }else{
	            	$result[] = $cityRow['text'];
	            }
	            
	        }
	    }
	    return $result;
	}*/
	public function getAllTableData(){
    	
		$schema = input::get("db");
	  	$table = input::get("table");

		$dsn = "mysql:host=localhost;dbname=information_schema";
        $db = new PDO($dsn, 'root', 'mongs');

	  	$sql = "select COLUMN_NAME from `COLUMNS` where TABLE_NAME='$table' and TABLE_SCHEMA='$schema' and COLUMN_NAME LIKE '%id'";
	  	$rs = $db->query($sql);
	  	$params = '';
  		$i = 0;
		if ($rs) {
	  		$rows = $rs -> fetchAll(PDO::FETCH_ASSOC);
	    	foreach($rows as $row){
	      		if($i++ > 0){
        			$params = $params.$row['COLUMN_NAME'].',';
	      		}
	    	}
	  	}
		$dsn1 = "mysql:host=localhost;dbname=".$schema;
        $db1 = new PDO($dsn1, 'root', 'mongs');

		$dsn = "mysql:host=localhost;dbname=mongs";
		$db = new PDO($dsn, 'root', 'mongs');

		//$limit='';
		//$page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
		//$rows = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;
		//$table = $_REQUEST['table'];
		$parameterName = input::get("parameterName");
		$orderFilter = '';
		$queryFilter = '';
		
		$sort = isset($_REQUEST['sort']) ? strval($_REQUEST['sort']) : 'recordTime';  
		$order = isset($_REQUEST['order']) ? strval($_REQUEST['order']) : 'desc'; 
		$orderFilter = " order by $sort $order ";

		//$offset = ($page-1)*$rows;
		//$limit=" limit $offset,$rows";
		$filter='';
		
		$citys = json_decode($_REQUEST['city'], true);
		/*$citys = $this->parseCity($phpCity);
		print_r($citys);*/
		$subNetwork = '';
		foreach ($citys as $city) {
			$subNetwork .= $this->getSubNets($db, $city);
		}
		$subNetwork = substr($subNetwork , 0 , -1);
		if($subNetwork != ''){
			$filter=" where subNetwork in (".$subNetwork.")";
		}
		$result = array();
		$rs = $db1->query("select count(*) from ".$table.$filter);
		if($rs){
			$row = $rs->fetchAll(PDO::FETCH_ASSOC);
            $result["total"] = $row[0]['count(*)'];
		}
		$sql = "select id,recordTime,mo,subNetwork,meContext,$params $parameterName from ".$table.$filter.$orderFilter;
		$rs = $db1->query($sql);
		$items = array();
		if($rs){
			$row = $rs->fetchAll(PDO::FETCH_ASSOC);
			foreach ($row as $r) {
				array_push($items, $r);
			}
			
			$result["rows"] = $items;
		}
		echo json_encode($result);


    }

    public function exportCSV(){
        $fileContent = input::get("fileContent");
        $citys = input::get("citys");
        $filename="common/files/参数分布".$citys."_".date('YmdHis').".csv";


        $csvContent = mb_convert_encoding($fileContent, 'GBK');
        $fp = fopen($filename, "w");
        fwrite($fp, $csvContent);
        fclose($fp);

        echo $filename;
    }

}