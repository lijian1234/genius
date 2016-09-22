<?php

namespace App\Http\Controllers\systemManage;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use PDO;
use Illuminate\Support\Facades\Auth;

class dataSourceController extends Controller{

	public function getNode(){

		$dsn = "mysql:host=localhost;dbname=mongs";
        $db = new PDO($dsn, 'root', 'mongs');

        $sql = "select * from ipAddressInfo";
        $res = $db->query($sql);

        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        $items = array();
        foreach ($row as $qr) { 
            array_push($items, ["value"=>$qr['ossAddress'],"text"=>$qr['nodName'],"sshUserName"=>$qr['sshUserName'],"sshPassword"=>$qr['sshPassword']]);
        }
        echo json_encode($items);
	}

	public function getFileName(){
		$strServer = input::get('remoteIp');
		$strServerPort = "22";  
		$strServerUsername = input::get('userName');  
		$strServerPassword = input::get('userPassword');
		$resConnection = ssh2_connect($strServer, $strServerPort);
		$array = array();
		if(ssh2_auth_password($resConnection, $strServerUsername, $strServerPassword)){  
		    $resSFTP = ssh2_sftp($resConnection); 
		    if($strServer == '10.40.57.189') {
				$dir = "ssh2.sftp://{$resSFTP}/data/trace/ctr/autobackup/changzhou";   	
		    }elseif($strServer == 'localhost'){
		    	$dir = "ssh2.sftp://{$resSFTP}/data/trace/ctr";
		    }else{
		    	$dir = "ssh2.sftp://{$resSFTP}/data/trace/ctr/autobackup";
		    }

			$files=scandir($dir);
			krsort($files);
			$items = array();
			foreach ($files as $file) {
				if($file == '.'){
					continue;
				}else if($file == '..'){
					continue;
				}else{
					$items['label'] = $file;
					$items['value'] = $file;
					array_push($array, $items);
				}
			}
		}
		echo json_encode($array);
	}

	public function ctrTreeItems(){
		$erbs = input::get('erbs');
		//$point = input::get('point'); //文件名
		$points = input::get('points');//节点名
		$filesName = input::get('point');
		$erbsArr = array();
		$erbsArr = explode(',',$erbs);

		$strServer = input::get('remoteIp');
		$strServerPort = "22";  
		$strServerUsername = input::get('userName'); 
		$strServerPassword = input::get('userPassword');

		$resConnection = ssh2_connect($strServer, $strServerPort);

		$idNum = 1;

		$allCtr = array();
		$ctrTime = array();
		$items = array();
		$childrengz = array();
		$allChildrengz = array();
		$succFilesName = array();
		
		if(ssh2_auth_password($resConnection, $strServerUsername, $strServerPassword)){  
	    	$resSFTP = ssh2_sftp($resConnection); 
	    	if($strServer == '10.40.57.189') {
	    		$dir = "ssh2.sftp://{$resSFTP}/data/trace/ctr/autobackup/changzhou";
	    	}elseif($strServer == 'localhost'){
	    		$dir = "ssh2.sftp://{$resSFTP}/data/trace/ctr";
	    	}else {
	    		$dir = "ssh2.sftp://{$resSFTP}/data/trace/ctr/autobackup";
	    	}
		    $file=scandir($dir); 
		   	krsort($file);

		    //$file = $this->getFile($dir);

		    foreach ($filesName as $fileName) {
		    	foreach ($file as $value) {
			    	if($value == '.' || $value == '..' || $fileName != $value){
			    		continue;
			    	}else{
			    		array_push($succFilesName, $fileName);
			    		$ctrTime['id'] = $idNum;
			    		$ctrTime['kpiName'] = $value;
			    		$idNum++;
			    	}
			    	array_push($allCtr, $ctrTime);
			    }
		    }
		    //print_r($succFilesName);return;

		    //gz压缩包匹配
		    $idNum = 1;
		    foreach ($succFilesName as $succFileName) {
		    	$childrenId = 1;

		    	if($strServer == '10.40.57.189') {
		    		$dirsgz = "ssh2.sftp://{$resSFTP}/data/trace/ctr/autobackup/changzhou/$succFileName";
		    	}elseif($strServer == 'localhost'){
		    		$dirsgz = "ssh2.sftp://{$resSFTP}/data/trace/ctr/$succFileName";
		    	}else {
		    		$dirsgz = "ssh2.sftp://{$resSFTP}/data/trace/ctr/autobackup/$succFileName";
		    	}

		    	//$dirsgz = "ssh2.sftp://{$resSFTP}/data/trace/ctr/autobackup/changzhou/$succFileName";
		    	//$filesgz = scandir($dirsgz);
		    	$filesgz = $this->getFile($dirsgz);
		    	//print_r($filesgz);return;
		    	foreach ($filesgz as $filegz) {
		  			foreach ($erbsArr as $erb) {
		  				$filePos = strpos($filegz, $erb);
		  				if($filePos == false) {
		  					continue;
		  				} else {
		  					$allChildrengz['id'] = $idNum . $childrenId;
		  					//$allChildrengz['kpiName'] = $filegz; 
		  					//$filesize = $dirsgz.'/'.$filegz;
		  					$allChildrengz['kpiName'] = str_replace($dirsgz, '', $filegz);;
		  					$allChildrengz['size'] = filesize($filegz) . ' B';
		  					$childrenId++;
		  					array_push($childrengz, $allChildrengz);
		  				}
		  			}
		    	}
		    	$num = $idNum - 1;
		    	$allCtr[$num]['children'] = $childrengz;
		    	$childrengz = array();

		    	$idNum++;    	
		    }
		}
		echo json_encode($allCtr);
	}

	public function getFile($dir){
		$fileArr = array();
		$file=scandir($dir);
		//return $file;
		if($file){
			krsort($file);
			foreach ($file as $value) {
				if($value != "." && $value != ".."){
					//if(is_dir($dir."/".$value)){
					if(!strpos($value,".gz")){
						$fileArr = array_merge($fileArr, $this->getFile($dir."/".$value));
						//array_push($fileArr, $dir."/".$value);
					}else{
						array_push($fileArr, $dir."/".$value);
					}
				}

				
			}
		}
		
		return $fileArr;
	}
}