<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use PDO;
use DateTime;
use Illuminate\Support\Facades\Auth;

class navController extends Controller{

	public function getUser(){

		if(!Auth::user()){
            echo "login";
            return;
        }
        $user=Auth::user();

        echo json_encode($user);

	}

	public function signout(){
		if(Auth::check()){ 
            Auth::logout();
        }
		echo "success";
	}
	/**
	 * @desc ：获取平台每小时的session数
	 * Time：2016/08/17 15:02:16
	 * @author Wuyou
	 * @param 参数类型
	 * @return 返回值类型
	*/
	public function getSessions(){
		date_default_timezone_set('PRC');
		$year = Input::get('year');
		$mon = Input::get('mon');
		$day = Input::get('day');
		$hour = Input::get('hour');
		$currTime = mktime($hour, 0, 0, $mon, $day, $year);
		$day = date('Y-m-d',$currTime);
		$hour = date('H',$currTime);
		$currDate = date('Y-m-d H',$currTime);
		//$dirname  =  storage_path('framework/sessions');
		$dirname = '/opt/lampp/htdocs/genius/storage/framework/sessions';
		$filenum = 0;
		if (!file_exists($dirname)) {
			return false;  
		}
		$dir = opendir($dirname); 
		while ( $filename = readdir($dir)) {
			$newfile = $dirname.'/'.$filename;  
		        if(!is_dir($newfile)){  
		        	$fileTime = filemtime($newfile);
		        	$filectime = date("Y-m-d H",$fileTime);
		        	if ($currDate == $filectime) {
		        		$filenum++;  
		        	}
		        }  
		}
		DB::delete("delete from sessions where id='".$currDate."'");
		DB::insert("insert into sessions(id,date,hour,num) values('".$currDate."','".$day."',$hour,$filenum)");
		//echo "insert into sessions(id,date,hour,num) values('".$currDate."','".$day."',$hour,$filenum)";
		return $filenum;
	}
	public function addNotice(){
		$title = input::get("title");
		$content = input::get("content");
		$id = input::get("id");

	 	$user=Auth::user()->user;
	 	date_default_timezone_set("PRC");
	 	$time = date('YmdHis');
		$dsn = "mysql:host=localhost;dbname=mongs";
        $db = new PDO($dsn, 'root', 'mongs');
        if($id){
        	$sql = "UPDATE notification set publishTime = '$time' , title = '$title' , content = '$content' where id = '$id'";
        }else{
        	$sql = "INSERT INTO notification VALUES (null,'$time','$user','$title','$content','')";
        }
        
        $res = $db->exec($sql);

        print_r($res);
	}

	public function getNotice(){
		$user=Auth::user()->id;
		$dsn = "mysql:host=localhost;dbname=mongs";
        $db = new PDO($dsn, 'root', 'mongs');

        $sql = "select * from notification where readed not like '%$user%' order by publishTime desc";
        $res = $db->query($sql);
        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        $items = array();
        foreach ($row as $qr) { 
            array_push($items, $qr);
        }
        echo json_encode($items);

	}
	public function readNotice(){
		$user=Auth::user()->id;
		$id = input::get("id");
		$dsn = "mysql:host=localhost;dbname=mongs";
        $db = new PDO($dsn, 'root', 'mongs');

        $sql = "select readed from notification where id = '$id'";
        $res = $db->query($sql);
        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        if($row[0]['readed'] !=""){
        	$reader = $row[0]['readed'].",".$user;
        }else{
        	$reader = $user;
        }

        $sql = "update notification set readed = '$reader' where id = '$id'";
        $res = $db->exec($sql);
        print_r($res);

	}
	public function readAllNotice(){
		$user=Auth::user()->id;
		$ids = input::get("ids");
		$idArray = explode(",",$ids);


		$dsn = "mysql:host=localhost;dbname=mongs";
        $db = new PDO($dsn, 'root', 'mongs');

        foreach ($idArray as $id) {
        	$sql = "select readed from notification where id = '$id'";
	        $res = $db->query($sql);
	        $row = $res->fetchAll(PDO::FETCH_ASSOC);
	        if($row[0]['readed'] !=""){
	        	$reader = $row[0]['readed'].",".$user;
	        }else{
	        	$reader = $user;
	        }

	        $sql = "update notification set readed = '$reader' where id = '$id'";
	        $res = $db->exec($sql);
        }
	}
}