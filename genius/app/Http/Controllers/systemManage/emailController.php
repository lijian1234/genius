<?php

namespace App\Http\Controllers\systemManage;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use PDO;
use Illuminate\Support\Facades\Auth;

class emailController extends Controller{

	 public function templateQuery(){
       
        $path = input::get("path");
        $flag = input::get("flag");
        $word=file($path);
        $result = array();
        $items = array();
        $itemRow = array();
        if($flag == 0){//指标邮箱
            $city='';
            foreach($word as $val){ 
            $row = explode('=', $val);
            if(count($row) == 1){
                if (trim($row[0]) != '') {
                    $city = substr(trim($row[0]),1,strlen(trim($row[0]))-2);
                    //echo "string".$city.';';
                }
                
            }else{
                    $itemRow['name'] =  trim($row[0]);
                    $itemRow['email'] = trim($row[1]);
                    $itemRow['city'] = $city;
                    array_push($items, $itemRow);

                    $result['text'] = 'Name,Email,City';
                    $result['rows'] = $items;

                }
                
            } 
        }else if($flag == 1){//参数邮箱
            for($i=1;$i<count($word);$i++){
                if(trim($word[$i]) != ''){
                      $row = explode('=', $word[$i]);
                      $itemRow['name'] =  trim($row[0]);
                      $itemRow['email'] = trim($row[1]);
                      array_push($items, $itemRow);
                }
                  
            }
            $result['text'] = 'Name,Email';
            $result['rows'] = $items;
        }
        echo json_encode($result);
    }

    public function openEmailFile(){
        
        $path = input::get("path");
        $myfile = fopen($path, "r") or die("Unable to open file!");
        // 输出单行直到 end-of-file
        while(!feof($myfile)) {
          echo fgets($myfile);
        }
        fclose($myfile);
    }

    public function saveEmailFile(){
        
        $path = input::get("path");
        $myfile = fopen($path, "w") or die("Unable to open file!");
        // 输出单行直到 end-of-file
        $content = input::get("content");
        
        echo $content;
        fwrite($myfile, $content);
        fclose($myfile);
        }
    }