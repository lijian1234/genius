<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

use PDO;

class InterCloudController extends Controller
{

    public function getChannels(Request $request) {

        /*$sql = 'select DISTINCT channel from siteLte order by channel;';

        $result = DB::connection('mongs')->select($sql);

        return json_encode($result);*/

        //$sql = 'select DISTINCT channel from siteLte order by channel;';
        $sql = 'select DISTINCT band from siteLte order by band;';

        $results = DB::connection('mongs')->select($sql);

        $items = array();
        foreach($results as $result){
            $channel = '{"text":"'.$result->band.'","value":"'.$result->band.'"}';
            array_push($items, $channel);
        }
        return response()->json($items);
    }

    public function getCells(Request $request) {

    	$date = $request['date'];
    	$hour = $request['hour'];
    	$minute = $request['minute'];
    	$channel = $request['channel'];
        $citys = $request['citys'];
        //print_r($citys);
        if($citys != ''){
            $dsn = "mysql:host=localhost;dbname=mongs";
            $db = new PDO($dsn, 'root', 'mongs');
            $cityStr = implode("','", $citys); 
            $sql = "select connName from databaseconn where cityChinese in ('".$cityStr."')"; 
            //print_r($sql);
            $res = $db->query($sql);
            $rows = $res->fetchall();
            $cityArr = array();
            foreach ($rows as $row) {
                $city = $row['connName'];
                //print_r($row['connName']);
                if($city == 'changzhou1'){
                    continue;
                }
                array_push($cityArr,$city);
            }
            $citys = implode("','", $cityArr);
        }
        
//return;
        $channel = "'".$channel."'";
        $channel = str_replace(',', "','", $channel);

        $sql = "select DISTINCT channel from siteLte where band in ($channel)  order by channel;";
        $results = DB::connection('mongs')->select($sql);
        $resultChannel = '';
        foreach ($results as $result) {
            //print_r($result->channel . ',');
            $resultChannel = $resultChannel . $result->channel . ',';
        }
        $resultChannel = substr($resultChannel,0,strlen($resultChannel)-1);
        $resultChannel = "'". $resultChannel . "'";
        $resultChannel = str_replace(',', "','", $resultChannel); 
        //echo $resultChannel;
        if($citys==''){
            $sql = "select longitude,latitude,PUSCH上行干扰电平,dir,cell from interfereCellQuarter_cell_quarter where day_id = "."'".$date."'"." and hour_id = ".$hour." and quarter_id = ".$minute." and channel in (".$resultChannel.");";
        }else{
            $sql = "select longitude,latitude,PUSCH上行干扰电平,dir,cell from interfereCellQuarter_cell_quarter where day_id = "."'".$date."'"." and hour_id = ".$hour." and quarter_id = ".$minute." and channel in (".$resultChannel.") and city in ('".$citys."');";
        }
        

        //echo $sql;return;

        //$results = DB::connection('AutoKPI')->select($sql);

        /*$items = array();
        foreach($results as $result){
            array_push($items, $result);
        }
        return response()->json($items);*/


        /*$rs = mysql_unbuffered_query($sql);
		$items = array();
		while($row = mysql_fetch_object($rs)){
			array_push($items, $row);
		}
		//echo mysql_fetch_array($query,MYSQL_NUM);
		mysql_close($conn);
		$result["rows"] = $items;
	  	echo json_encode($result);*/



	    $dsn = "mysql:host=localhost;dbname=AutoKPI";
	    $dbn = new PDO($dsn, 'root', 'mongs');
	    $result = array();

	    $rs = $dbn->query($sql,PDO::FETCH_ASSOC);
	    $rs = $rs->fetchAll();

	    echo json_encode($rs);
    }


}