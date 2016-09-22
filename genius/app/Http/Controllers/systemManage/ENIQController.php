<?php

namespace App\Http\Controllers\systemManage;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use PDO;
use Illuminate\Support\Facades\Auth;

class ENIQController extends Controller{
	 public function Query4G(){
       
        $dsn = "mysql:host=localhost;dbname=mongs";
        $db = new PDO($dsn, 'root', 'mongs');
        
        $sql = "select * from databaseconn";
        $res = $db->query($sql);

        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        $items = array();
        foreach ($row as $qr) { 
            array_push($items, $qr);
        }
            
        $result = array();
        $result['text'] = 'id,Conn Name,City Chinese,Host,Port,DB Name,User Name,Passowrd,SubNetwork,SubNetwork Fdd';
        $result['rows'] = $items;
        echo json_encode($result);
    }
    public function Query2G(){
       
        $dsn = "mysql:host=localhost;dbname=mongs";
        $db = new PDO($dsn, 'root', 'mongs');
        
        $sql = "select * from databaseconn_2G";
        $res = $db->query($sql);

        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        $items = array();
        foreach ($row as $qr) { 
            array_push($items, $qr);
        }
            
        $result = array();
        $result['text'] = 'id,Conn Name,City Chinese,Host,Port,DB Name,User Name,Passowrd';
        $result['rows'] = $items;
        echo json_encode($result);
    }

    public function deleteENIQ(){
        
        $id = input::get("id");
        $sign = input::get("sign");
        $dsn = "mysql:host=localhost;dbname=mongs";
        $db = new PDO($dsn, 'root', 'mongs');

        if($sign == "4G"){
            $sql = "delete from databaseconn where id ='".$id."'";
        }else if($sign == "2G"){
            $sql = "delete from databaseconn_2G where id ='".$id."'";
        }
        
        $res = $db->query($sql);

        echo $sign;
     }

     public function updateENIQ(){
        
        $id = input::get("ENIQId");
        $sign = input::get("ENIQSign");
        $connName = input::get("connName");
        $cityChinese = input::get("cityChinese");
        $host = input::get("host");
        $port = input::get("port");
        $dbName = input::get("dbName");
        $userName = input::get("userName");
        $password = input::get("password");
        $subNetwork = input::get("subNetwork");
        $subNetworkFdd = input::get("subNetworkFdd");

        $dsn = "mysql:host=localhost;dbname=mongs";
        $db = new PDO($dsn, 'root', 'mongs');


        if($id){
            if($sign == "4G"){
                $sql = "UPDATE databaseconn SET connName = '".$connName."', cityChinese = '".$cityChinese."', host = '".$host."', port = '".$port."', dbName = '".$dbName."', userName = '".$userName."', password = '".$password."' , subNetwork = '".$subNetwork."', subNetworkFdd = '".$subNetworkFdd."' where id = '".$id."'";
            }else if($sign == "2G"){
                $sql = "UPDATE databaseconn_2G SET connName = '".$connName."', cityChinese = '".$cityChinese."', host = '".$host."', port = '".$port."', dbName = '".$dbName."', userName = '".$userName."', password = '".$password."' where id = '".$id."'";
            }
            
        }else{
            if($sign == "4G"){
                $sql = "INSERT INTO databaseconn VALUES (null,'".$connName."','".$cityChinese."','".$host."','".$port."','".$dbName."','".$userName."','".$password."','".$subNetwork."','".$subNetworkFdd."')";
            }else if($sign == "2G"){
                $sql = "INSERT INTO databaseconn_2G VALUES (null,'".$connName."','".$cityChinese."','".$host."','".$port."','".$dbName."','".$userName."','".$password."')";
            }
            
        }

        
        $res = $db->query($sql);
        echo $sign;
     }
}