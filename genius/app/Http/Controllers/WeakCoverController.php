<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

use PDO;

class WeakCoverController extends Controller
{

    public function getDate(Request $request) {

    	// $dsn = "mysql:host=10.40.57.190:8066;dbname=information_schema";
    	// $dbn = new PDO($dsn, 'mr', 'mr'); 
        //$sql = "SELECT `SCHEMA_NAME` FROM `information_schema`.`SCHEMATA` WHERE `SCHEMA_NAME` LIKE 'MR_20______' ORDER BY `SCHEMA_NAME` DESC;";
        $sql = "SELECT `SCHEMA_NAME` FROM `information_schema`.`SCHEMATA` WHERE `SCHEMA_NAME` LIKE 'MR_20_%' ORDER BY `SCHEMA_NAME` DESC;";

        $dsn = "mysql:host=10.40.48.244;dbname=information_schema";
	    $dbn = new PDO($dsn, 'root', 'mongs');
	    $result = array();

	    $rs = $dbn->query($sql,PDO::FETCH_ASSOC);
	    //print_r($rs->fetchAll());return;
	    $rs = $rs->fetchAll();

	    //echo json_encode($rs);

	    $items = array();
        foreach($rs as $result){
            $date = '{"text":"'.$result["SCHEMA_NAME"].'","value":"'.$result["SCHEMA_NAME"].'"}';
            array_push($items, $date);
        }
	    //echo json_encode($rs);

        return response()->json($items);
    }

    public function getCells(Request $request) {

    	$date = $request['date'];
    	$city = $request['city'];
    	$busyTime = $request['busyTime'];

    	$citySim = '';  														 //用来连接数据库
    	$fp = fopen('common/txt/MRO_CITY.txt', 'r');
    	while (!feof($fp)) {
    		$citys = fgets($fp, 4096);
    		$citysArr = explode('=', $citys);
    		if($city == trim($citysArr[1])){
    			$citySim = trim($citysArr[0]);
    		}
    	}
    	$dbname = 'MR_'.$citySim;
    	fclose($fp);
		
		$time = '';
    	if($busyTime == 'earlyTime'){
    		$hour = ' 08:00:00';
    	}else if($busyTime == 'laterTime'){
    		$hour = ' 18:00:00'; 						//？？？？？？
    	}
    	$time = $date . $hour;

        //$sql = "SELECT * FROM (SELECT a.ecgi as `ecgi`, a.ratio110*100 as `ratio110`, b.latitude as `latitude`, b.longitude as `longitude`, b.dir as `dir`, b.band as `band` FROM (select ecgi, ratio110 from MRO_allweakCoverage)a LEFT JOIN (SELECT ecgi, longitude, latitude, dir, band FROM mongs.siteLte)b ON a.ecgi=b.ecgi)d;";
        $sql = "SELECT * FROM (SELECT a.ecgi as `ecgi`, a.ratio110*100 as `ratio110`, b.latitude as `latitude`, b.longitude as `longitude`, b.dir as `dir`, b.band as `band` FROM (select ecgi, ratio110 from mroWeakCoverage WHERE datetime_id='".$time."')a LEFT JOIN (SELECT ecgi, longitude, latitude, dir, band FROM GLOBAL.siteLte)b ON a.ecgi=b.ecgi)d;";
	    // $dsn = "mysql:host=10.40.48.244;dbname=".$date;
	    // $dbn = new PDO($dsn, 'root', 'mongs');
	    $dsn = "mysql:host=10.40.57.190:8066;dbname=$dbname";
    	$dbn = new PDO($dsn, 'mr', 'mr'); 
	    $result = array();

	    $rs = $dbn->query($sql,PDO::FETCH_ASSOC);//print_r($rs = $rs->fetchAll());return;
	    $rs = $rs->fetchAll();

	     return response()->json($rs);
    }


    public function getCharts(Request $request) {

    	$cells = $request['cells'];

    	//$date = $request['date'];

    	$cells=str_replace(",","\",\"",$cells);
    	$cells="\"".$cells."\"";


    	$date = $request['date'];
    	$city = $request['city'];
    	$busyTime = $request['busyTime'];

    	$citySim = '';  														 //用来连接数据库
    	$fp = fopen('common/txt/MRO_CITY.txt', 'r');
    	while (!feof($fp)) {
    		$citys = fgets($fp, 4096);
    		$citysArr = explode('=', $citys);
    		if($city == trim($citysArr[1])){
    			$citySim = trim($citysArr[0]);
    		}
    	}
    	$dbname = 'MR_'.$citySim;
    	fclose($fp);
		
		$time = '';
    	if($busyTime == 'earlyTime'){
    		$hour = ' 08:00:00';
    	}else if($busyTime == 'laterTime'){
    		$hour = ' 18:00:00'; 						//？？？？？？
    	}
    	$time = $date . $hour;

        //$sql = "SELECT ecgi, numLess80 as `large_then_minus80`, numLess80_90 as `between_minus80_minus90`, numLess90_100 as `between_minus90_minus100`, numLess100_110 as `between_minus100_minus110`, numLess110 as `less_then_minus110`  FROM MRO_allweakCoverage where ecgi in (".$cells.");";
        //$sql = "SELECT ecgi, numLess80, numLess80_90, numLess90_100, numLess100_110, numLess110 FROM MRO_allweakCoverage where ecgi in (".$cells.");";
    	$sql = "SELECT ecgi, numLess80, numLess80_90, numLess90_100, numLess100_110, numLess110 FROM mroWeakCoverage where ecgi in (".$cells.") AND datetime_id='".$time."';";

	    // $dsn = "mysql:host=10.40.48.244;dbname=".$date;
	    // $dbn = new PDO($dsn, 'root', 'mongs');
	    $dsn = "mysql:host=10.40.57.190:8066;dbname=$dbname";
    	$dbn = new PDO($dsn, 'mr', 'mr'); 
	    $result = array();

	    $rs = $dbn->query($sql,PDO::FETCH_ASSOC);
	    $rs = $rs->fetchAll();

	    $returnData=array();
	    $items=array();
	    

	    foreach($rs as $result){

	    	$series=array();

	    	$series['name']="A".$result['ecgi']."A";
	    	$series['data']=array($result['numLess80'],$result['numLess80_90'],$result['numLess90_100'],$result['numLess100_110'],$result['numLess110']);

	    	array_push($items,$series); 
            
        }

	     return response()->json($items);
    }

}