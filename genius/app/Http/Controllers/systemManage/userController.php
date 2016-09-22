<?php

namespace App\Http\Controllers\systemManage;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use PDO;
use Illuminate\Support\Facades\Auth;

class userController extends Controller{
	 public function templateQuery(){
       
        $dsn = "mysql:host=localhost;dbname=mongs";
        $db = new PDO($dsn, 'root', 'mongs');
        
        $sql = "select id,user,pwd,type,email from users";
        $res = $db->query($sql);

        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        $items = array();
        foreach ($row as $qr) { 
            array_push($items, $qr);
        }
            
        $result = array();
        $result['text'] = ',Account,Passwd,Type,email';
        $result['rows'] = $items;
        echo json_encode($result);
    }

    public function deleteUser(){
        
        $id = input::get("id");
        $dsn = "mysql:host=localhost;dbname=mongs";
        $db = new PDO($dsn, 'root', 'mongs');

        $sql = "delete from users where id ='".$id."'";
        $res = $db->query($sql);

        $r = true;
        echo $r;
     }

     public function updateUser(){
        
        $id = input::get("id");
        $userName = input::get("userName");
        $password = input::get("password");
        $type = input::get("type");
        $email = input::get("email");

        $dsn = "mysql:host=localhost;dbname=mongs";
        $db = new PDO($dsn, 'root', 'mongs');

        if($id){
            $sql = "UPDATE users SET user = '".$userName."', pwd = '".$password."', type = '".$type."', email = '".$email."' where id = '".$id."'";
        }else{
            $sql = "INSERT INTO users (user, pwd, type, email) VALUES ('".$userName."','".$password."','".$type."','".$email."')";
        }

        
        $res = $db->query($sql);
     }
}