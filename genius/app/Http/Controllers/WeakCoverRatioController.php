<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use DateTime;
use DateInterval;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use PDO;

class WeakCoverRatioController extends Controller{
	public function SearchWeakCoverRatio(){
		$host = "10.40.48.244";
		$port = "3306";
		$dbName = "MR_20160730_0800_1200_CZ";
		$dsn = "mysql:host=10.40.48.244;dbname=MR_20160730_0800_1200_CZ";
        $db = new PDO($dsn, 'root', 'mongs'); 

        $date = Input::get('date');
        $date = implode('', explode('-', $date));

        $dataBaseFlag = 'MR_'.$date;
        $flag = 0;
        $dataBases = array();
        $rows = $db->query("SHOW DATABASES;",PDO::FETCH_ASSOC)->fetchall(); 
        $db = null;
        foreach ($rows as $row) {
        	$database = $row['Database'];
        	if(strpos($database, $dataBaseFlag) !== false && count(explode('_', $database))==5){
        		$flag = 1;
        		array_push($dataBases, $database);
        	}
        }
        if($flag == 0){
        	return 'databaseNotExists';
        }

        $file = fopen('common/txt/MRO_CITY.txt','r');
        while(!feof($file)){
			$m[] = fgets($file,4096); 
		}
		foreach ($m as $citysArr) {
			$cityArr = explode('=', $citysArr);
			$allcity[$cityArr[0]] = $cityArr[1];
		}
		$city = array();
		$series = array();
		$return = array();
        foreach ($dataBases as $dataBase) {
        	$cityArr = explode('_', $dataBase);
      		$temp = trim($allcity[$cityArr[4]]);
      		array_push($city, $temp);
        	$dsn = "mysql:host=10.40.48.244;dbname=".$dataBase;
        	$db = new PDO($dsn, 'root', 'mongs');
        	$sql = "SELECT molecule.num / denominator.num1 * 100 AS ratio FROM(
						SELECT COUNT(ratio110) AS num FROM weakCoverage WHERE ratio110 > 0.2) molecule
					LEFT JOIN (
						SELECT COUNT(ratio110) AS num1 FROM weakCoverage) denominator 
					ON molecule.num <= denominator.num1";
			$rows = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
			$temp = $rows[0]['ratio'];
			$series['name'] = '';
			$series['data'][] = floatval($temp);
        }
        $return['date'] = Input::get('date');     
        $return['category'] = $city;
        $return['series'][] = $series;
        return json_encode($return);
	}
}