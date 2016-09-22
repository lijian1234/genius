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

use PDO;
 /**
  * @desc ：一致性检查
  * Time：2016/08/01 17:33:34
  * @author Wuyou
  * @param 参数类型
  * @return 返回值类型
 */
class ConsistencyCheckController extends Controller
{
	/**
	 * @desc ：各地市服务器连接对象
	*/
	private $dbc;
	/**
	 * @desc ：页面分布数据
	 * Time：2016/08/01 17:33:47
	 * @author Wuyou
	 * @param 参数类型
	 * @return 返回值类型
	*/
	public function getDistributeData(){
		$dbc = new DataBaseConnection();
		$db = Input::get('db');
		$table = Input::get('table');
		$dsn = "mysql:host=localhost;dbname=$db";
		$dbn = new PDO($dsn, 'root', 'mongs');

		//$res = DB::select('select connName,subNetwork from databaseconn ');
		$res = $dbc->getCity_subNetCategories();
		$series = array();
		$category = array();
		foreach ($res as $items) {
			$city = $items->connName;
			$subNetwork = $items->subNetwork;
			//$subNetwork = $this->reCombine($subNetwork);
			$subNetwork = $dbc->reCombine($subNetwork);
			//dump($subNetwork);
			if (array_search($city,$category) === false) {
				$category[] = $city;
			}
			if ($table == 'TempEUtranCellRelationUnidirectionalNeighborCell') {
				$sql = "select count(*) as occurs from $table t where status='ON' and subNetwork in(".$subNetwork.")";
			}else{
			  $sql = "select count(*) as occurs from $table t where subNetwork in(".$subNetwork.")";
			}
			//dump($sql);
			$rs = $dbn->query($sql,PDO::FETCH_ASSOC);
			if ($rs) {
				$occurs = $rs->fetchColumn();
				$seriesData[] = floatval($occurs);
			}else{
				$seriesData[] = 0;
			}
			

		}
		$data['category'] = $category;
		$data['series'] = array();
		$data['series'][] = ['name'=>'number','data'=>$seriesData];
		return json_encode($data);
	   
	}
	   
	/**
	 * @desc ：获得表头字段
	 * Time：2016/07/01 18:42:00
	 * @author Wuyou
	 * @param 参数类型
	 * @return 返回值类型
	*/
	public function getTableField(){
		$db = Input::get('db');
	    $table = Input::get('table');
	    $dsn = "mysql:host=localhost;dbname=$db";
	    $dbn = new PDO($dsn, 'root', 'mongs');
	    $result = array();
	    $query= "select * from ".$_REQUEST['table']." limit 1"; 
	    $rs = $dbn->query($query,PDO::FETCH_ASSOC);
	    $rs = $rs->fetchAll();
	    if (count($rs) > 0 ) {
	    	return $rs[0];
	    }else{
	    	$result = array();
	    	$result['result'] = 'error';
	    	return $result;
	    }
	}
	/**
	 * @desc ：获取记录数
	 * Time：2016/07/01 18:42:19
	 * @author Wuyou
	 * @param 参数类型
	 * @return 返回值类型
	*/
	public function getItems(){
	 	$dbc = new DataBaseConnection();

		$db = Input::get('db');
		$table = Input::get('table');
		$paramLength = Input::get('paramLength');
		$dsn = "mysql:host=localhost;dbname=$db";
		$dbn = new PDO($dsn, 'root', 'mongs');
		$citys = Input::get('citys');
		$displayStart = Input::get('page');
		$displayLength = Input::get('limit');
		$offset = ($displayStart - 1)*$displayLength;
		$limit = '';
		$filter = '';
		$limit = " limit $offset,$displayLength ";
		$subNetwork = '';
		if ($citys != '') {
			foreach ($citys as $city) {
				//dump($city);
				//$subNetwork .= $this->getSubNets($dbn, $city);
				$subNetwork .= $dbc->getSubNets($city).',';
				//dump($subNetwork);
			}
			$subNetwork = substr($subNetwork , 0 , -1);
		}
		if ($subNetwork != '') {
			$filter=" where subNetwork in (".$subNetwork.")";
		}
		$result = array();
		$sqlCount = "select count(*) from ".$table.$filter;
		$rs = $dbn->query($sqlCount,PDO::FETCH_ASSOC);
		$result["total"] = $rs->fetchColumn();
		$sql = "select * from $table $filter $limit";
		$rs = $dbn->query($sql,PDO::FETCH_OBJ);
		$res = $rs->fetchAll();
		$items = array();
		if ($res) {
			foreach ($res as $row) {
				$row = $this->substring_paramData($row,$paramLength);
				//dump($row);
				//die;
				array_push($items, $row);
			}
			$result["records"] = $res;
		}
		return $result;
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
	 * @desc ：文件导出
	 * Time：2016/08/01 17:40:30
	 * @author Wuyou
	 * @param 参数类型
	 * @return 返回值类型
	*/
	public function exportFile(){
		$dbc = new DataBaseConnection();

		$db = Input::get('db');
		$table = Input::get('table');
		$dsn = "mysql:host=localhost;dbname=$db";
		$dbn = new PDO($dsn, 'root', 'mongs');
		$citys = Input::get('citys');
		$filter = '';
		$subNetwork = '';
		if ($citys != '') {
			foreach ($citys as $city) {
				//$subNetwork .= $this->getSubNets($dbn, $city);
				$subNetwork .= $dbc->getSubNets($city).',';
			}
			$subNetwork = substr($subNetwork , 0 , -1);
		}
		if ($subNetwork != '') {
			$filter = " where subNetwork in (".$subNetwork.")";
		}
		$result = array();
		$fileContent = array();
		$csvContent = "";
		$sqlCount = "select count(*) from ".$table.$filter;
		$rs = $dbn->query($sqlCount,PDO::FETCH_ASSOC);
		$result["total"] = $rs->fetchColumn();
		//dump($result["total"]);
		$sql = "select * from $table $filter";
		$rs = $dbn->query($sql,PDO::FETCH_ASSOC);
		//$row = $rs->fetch();
		$rs = $rs->fetchAll();
		if (count($rs) >0 ) {
			$row = $rs[0];
		}else{
			return 'null';
		}
		$fieldArr = array_keys($row);
		$csvContent= implode(",",$fieldArr);
		$csvContent = mb_convert_encoding($csvContent, 'gbk', 'utf-8'); 
		$fileContent[] = $csvContent.",";
		
		foreach ($rs as $row) {
			$csvContent = "";
			foreach ($row as $column)
			{
			  $column = trim($column);
			  $column = str_replace(",", " ", $column);
			  $csvContent = $csvContent.",".$column;
			}
			$csvContent = substr($csvContent, 1, strlen($csvContent) - 1);
			$csvContent = $csvContent.",";
			$csvContent = mb_convert_encoding($csvContent, 'gbk', 'utf-8');
			$fileContent[] = $csvContent; 
		}
		$filename = '';
		$filename = "files/".$table."_".date('YmdHis').".csv";
		$fp = fopen($filename,'w+');
		foreach ($fileContent as $line)	{
			$lineArr = array();
			$lineArr = explode(',',$line);
			fputcsv($fp,$lineArr);
		}
		fclose($fp); 
		$result["result"] = 'true';
		$result["filename"] = $filename;
		return $result;
	}
	/**
	 * @desc ：重新拼接子网 供in 查询
	 * Time：2016/07/01 18:40:04
	 * @author Wuyou
	 * @param 子网集合
	 * @return 字符串
	*/
	/*protected function reCombine($subNetwork)	{

		$subNetArr = explode(",", $subNetwork);
		$subNetsStr = '';
		foreach ($subNetArr as $subNet) {
			$subNetsStr.= "'".$subNet."',";
		}

		return substr($subNetsStr,0,-1);
	}*/
	/**
	 * @desc ：
	 * Time：2016/07/02 15:48:21
	 * @author Wuyou
	 * @param 参数类型
	 * @return 返回值类型
	*/
	/*protected function getSubNets($db, $city)	{

		$SQL = "select subNetwork from databaseconn where connName = '$city'";
		$res = DB::select($SQL);
		$subNets = $res[0]->subNetwork;
		return $this->reCombine($subNets);
	}*/
}
