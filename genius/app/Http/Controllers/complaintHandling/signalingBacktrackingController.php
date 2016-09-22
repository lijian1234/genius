<?php

namespace App\Http\Controllers\complaintHandling;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use PDO;
use Illuminate\Support\Facades\Auth;

class signalingBacktrackingController extends Controller{

	 public function getDataBase(){
        $dsn = "mysql:host=localhost;dbname=mongs";
        $db = new PDO($dsn, 'root', 'mongs');
       
        $type = input::get("type");


        $filter='';
        if(!Auth::user()){
            echo "login";
            return;
        }
        $user=Auth::user() -> user;

        if($user!='admin'){
            $filter=" and owner='$user'";
        }
        $sql = "select taskName from task where type='".$type."' and status='complete' $filter order by taskName asc";
        $res = $db->query($sql);

        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        $items = array();
        foreach ($row as $qr) { 
            array_push($items, ["id"=>$qr["taskName"],"text"=>$qr["taskName"]]);
        }
            
        echo json_encode($items);
    }
    public function getEventNameandEcgi(){
        $database = input::get("database");

        $dsn = "mysql:host=localhost;dbname=".$database;
        $db = new PDO($dsn, 'root', 'mongs');
       /* if(!$db){
            echo "no database";
            return;
        }*/

        $sql = "SELECT eventName FROM eventDetail group by eventName order by eventName";
        $res = $db->query($sql);
        $row = $res->fetchAll(PDO::FETCH_ASSOC);

        $items1 = array();
        foreach ($row as $qr) { 
            array_push($items1, ["label"=>$qr["eventName"],"value"=>$qr["eventName"]]);
        }
        $returnData["eventName"] = $items1;

        /*$sql = "SELECT ecgi FROM eventDetail group by ecgi order by ecgi";
        $res = $db->query($sql);
        $row = $res->fetchAll(PDO::FETCH_ASSOC);

        $items2 = array();
        foreach ($row as $qr) { 
            array_push($items2, ["label"=>$qr["ecgi"],"value"=>$qr["ecgi"]]);
        }
        $returnData["ecgi"] = $items2;*/

        echo json_encode($returnData);
    }

    public function getEventDataHeader(){
        $database = input::get("db");

        $dsn = "mysql:host=localhost;dbname=".$database;
        $db = new PDO($dsn, 'root', 'mongs');
        $head = $db ->query("select COLUMN_NAME from information_schema.COLUMNS where table_name = 'eventDetail' and table_schema = '$database'");
        $text = array();
        foreach ($head as $h) {
           array_push($text, $h["COLUMN_NAME"]);
        }
        $result = array();
        $result['text'] = implode(",",$text);
        echo json_encode($result);
    }


    public function getEventData(){

        $database = input::get("db");

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 50;
        $offset = ($page-1)*$rows;




        $dsn = "mysql:host=localhost;dbname=".$database;
        $db = new PDO($dsn, 'root', 'mongs');


        $filter='';

        if(input::get("filterSection")=='true'){
            $filter=" where ueRef='".input::get("ueRefChoosed")."'";
        }else {
            $filter=$this->getFilter('event');
        }
        $result = array();
        $sql="select * from eventDetail $filter order by eventTime asc";
        if(input::get("viewType")=='table'){
            //echo "select count(*) from ".TBevent.$filter;
            $rs = $db->query("select count(*) from eventDetail".$filter);
            $row = $rs->fetchAll(PDO::FETCH_ASSOC);

            $result["total"] = $row[0]['count(*)'];
            $sql=$sql." limit $offset,$rows";
        }


        $res = $db->query($sql);

        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        $items = array();
        foreach ($row as $qr) { 
            array_push($items, $qr);
        }
        $result['rows'] = $items;
            
        /*$result = array();

        $head = $db ->query("select COLUMN_NAME from information_schema.COLUMNS where table_name = 'eventDetail' and table_schema = '$database'");
        $text = array();
        foreach ($head as $h) {
           array_push($text, $h["COLUMN_NAME"]);
        }
        
        $result['text'] = implode(",",$text);
        $result['rows'] = $items;*/
        echo json_encode($result);
    }


    public function getAllEventData(){

        $database = input::get("db");

        $dsn = "mysql:host=localhost;dbname=".$database;
        $db = new PDO($dsn, 'root', 'mongs');


        $filter='';

        if(input::get("filterSection")=='true'){
            $filter=" where ueRef='".input::get("ueRefChoosed")."'";
        }else {
            $filter=$this->getFilter('event');
        }

        $sql="select * from eventDetail $filter order by eventTime asc";
        $res = $db->query($sql);

        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        $items = array();
        foreach ($row as $qr) { 
            array_push($items, $qr);
        }
            
        $result = array();

        $head = $db ->query("select COLUMN_NAME from information_schema.COLUMNS where table_name = 'eventDetail' and table_schema = '$database'");
        $text = array();
        foreach ($head as $h) {
           array_push($text, $h["COLUMN_NAME"]);
        }
        
        $result['text'] = implode(",",$text);
        $result['rows'] = $items;
        echo json_encode($result);
    }

    public function getFilter($type){
        $filter='';
        if($type=="event"){
            $eventName = input::get("eventName");
            
            if(isset($eventName)&&$eventName!=''){
                $filter = $this->checkFilter($filter);
                $eventName = implode(",",$eventName);
                $filter="$filter eventName in('".implode("','",explode(",",$eventName))."')";
            }
            //print_r($filter);return;
            $imsi = input::get("imsi");
            if(isset($imsi)&&$imsi!=''){
                $filter = $this->checkFilter($filter);
                $filter="$filter imsi ='".$imsi."'";
            }
            /*$ecgi = input::get("ecgi");
            if(isset($ecgi)&&$ecgi!=''){
                $filter = $this->checkFilter($filter);
                $ecgi = implode(",",$ecgi);
                $filter="$filter ecgi in('".implode("','",explode(",",$ecgi))."')";
            }*/
            $ueRef = input::get("ueRef");
            if(isset($ueRef)&&$ueRef!=''){
                $filter = $this->checkFilter($filter);
                $filter="$filter ueRef in('".implode("','",explode(",",$ueRef))."')";
            }
            $mmeS1apId = input::get("mmeS1apId");
            if(isset($mmeS1apId)&&$mmeS1apId!=''){
                $filter = $this->checkFilter($filter);
                $filter="$filter mmeS1apId in('".implode("','",explode(",",$mmeS1apId))."')";
            }
        }
        if($filter!=''){
            $filter=" where ".$filter;
        }
        return $filter;
    }

    public function checkFilter($filter){
        if($filter!=''){
            return "$filter and ";
        }
    }

    public function showMessage(){
        $id=input::get("id");
        $dbName=input::get("db");
        $command = 'sudo common/sh/wsharkparser.sh '.$dbName." ".$id;
        $return = exec($command);
        //echo "common/files/".$return;
        echo "/mongs_web/ctrsystem/files/".$return;
    }

    public function exportCSV(){
        $fileContent = input::get("fileContent");
        $ueRef = input::get("ueRef");
        $filename="common/files/信令流程_ueRef".$ueRef."_".date('YmdHis').".csv";


        $csvContent = mb_convert_encoding($fileContent, 'GBK');
        $fp = fopen($filename, "w");
        fwrite($fp, $csvContent);
        fclose($fp);

        echo $filename;
    }
    
}