<?php

namespace App\Http\Controllers\systemManage;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;

//use App\Http\Controllers\Controller;

use PDO;

class paramsController extends Controller{

	public function getBaselineTreeData(){
    	$users = DB::select('select distinct user from templateParaBaseline order by user');
        $arrUser = array();
        $items = array();
        $itArr = array();
        foreach ($users as $user) {
            $userStr = $user->user;
            $templateNames = DB::table('templateParaBaseline')->where('user', '=', $userStr)->get();
            foreach ($templateNames as $templateName) {
                array_push($arrUser, array("text"=>$templateName->templateName,"id" => $templateName->id));
            }
            $items["text"] = $userStr;
            $items["nodes"] = $arrUser;
            $arrUser = array();
            array_push($itArr, $items);
        }
        return response()->json($itArr);
    }
    public function searchBaselineTreeData() {
        $inputData = Input::get('inputData');
        $inputData = "%".$inputData."%";
        $dsn = "mysql:host=localhost;dbname=mongs";
        $db = new PDO($dsn, 'root', 'mongs');

        $users = DB::select('select distinct user from templateParaBaseline order by user');
        $arrUser = array();
        $items = array();
        $itArr = array();
        foreach ($users as $user) {
            $userStr = $user->user;
            $sql = "select * from templateParaBaseline where user = '$userStr' and templateName like '$inputData'";
            $templateNames = $db -> query($sql) -> fetchAll(PDO::FETCH_ASSOC);
            if($templateNames){
            	foreach ($templateNames as $templateName) {
	            	$temp['text'] = $templateName['templateName'];
	            	$temp['id'] = $templateName['id'];
	               // array_push($arrUser, array("text"=>$templateName->templateName,"id" => $templateName->id));
	            	array_push($arrUser, $temp);
	            }
	            $items["text"] = $userStr;
	            $items["nodes"] = $arrUser;
	            $arrUser = array();
	            array_push($itArr, $items);
            }
            
        }
        return response()->json($itArr);
    }

    public function getBaselineTableData(){
    	$templateId = input::get("templateId");
       
        $dsn = "mysql:host=localhost;dbname=mongs";
        $db = new PDO($dsn, 'root', 'mongs');
        
        if($templateId == null){
            $sql = "select * from formulaParaBaseline";
        }else{
            $sql = "select * from formulaParaBaseline where templateId ='".$templateId."'";
        }
       //echo $sql; return;
        $res = $db->query($sql);

        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        $items = array();
        foreach ($row as $qr) {
        	//print_r($qr);
        	$templateIdTmp = $qr['templateId'];
			$rsTemplate = $db->query("select templateName from templateParaBaseline where id='$templateIdTmp'");
			$rowTemplate = $rsTemplate->fetchAll(PDO::FETCH_ASSOC);
			$templateName = $rowTemplate[0]['templateName'];
			$qr['templateId'] = $templateName;

            array_push($items, $qr);
        }
            
        $result = array();
        $result['text'] = 'id,moName,qualification,qualificationValue,parameter,recommendedValue,category,templateName,version,cellType';
        $result['rows'] = $items;
        echo json_encode($result);
    }

    public function downloadFile(){
        $dsn = "mysql:host=localhost;dbname=mongs";
        $db = new PDO($dsn, 'root', 'mongs');

        $filter='';
        $result = array();
        //$items = array();
        $fileContent = array();
        $csvContent = "";

        $templateId = isset($_REQUEST['templateId']) ? $_REQUEST['templateId'] : '';
        $templateName = isset($_REQUEST['templateName']) ? $_REQUEST['templateName'] : '';
        $filename="common/files/参数Baseline管理_".$templateName."_".date('YmdHis').".csv";

        
        $column = ' moName,qualification,qualificationValue,parameter,recommendedValue,category,version,cellType ';
        $result["text"] = $column;
        if($templateId == ''){
            $sql = "select".$column."from formulaParaBaseline";
        }else{
            $sql = "select".$column."from formulaParaBaseline where templateId='$templateId'";
        }
        $res = $db->query($sql);

        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        $items = array();
        foreach ($row as $qr) { 
            array_push($items, $qr);
        }
        $result['rows'] = $items;
        $result['total'] = count($items);
        $result['result'] = 'true';

        $this->resultToCSV2($result, $filename);
        $result['filename'] = $filename;
        if (count($items) > 1000) {
            $result['rows'] = array_slice($items, 0, 1000);
        }

        echo json_encode($result);
    }
    protected function resultToCSV2($result, $filename){
        $csvContent = mb_convert_encoding($result['text'] . "\n", 'GBK');
        $fp = fopen($filename, "w");
        fwrite($fp, $csvContent);

        foreach ($result['rows'] as $row) {
            fputcsv($fp, $row);
        }
        
        fclose($fp);
    }

    public function uploadFile(){
        $dsn = "mysql:host=localhost;dbname=mongs";
        $db = new PDO($dsn, 'root', 'mongs');
        

        

        $filename = $_FILES['fileImport']['tmp_name'];    
         //echo $filename; return;
        if (empty ($filename)) {         
            echo '请选择要导入的CSV文件！'; 
            exit;     
        }     
        if(file_exists("common/files/" . $_FILES['fileImport']['name'])){
           unlink("common/files/" . $_FILES['fileImport']['name']);
        }
        $result = move_uploaded_file($filename,
                "common/files/" . $_FILES['fileImport']['name']);

      
        setlocale(LC_ALL,NULL);
        $handle = fopen("common/files/" . $_FILES['fileImport']['name'], 'r');    
        $result = $this -> input_csv($handle); //解析csv   
        $len_result = count($result);    
        //echo $len_result;return;
        if($len_result==0){         
            echo '没有任何数据！';
            exit;     
        } 


        
        $db -> query("delete from formulaParaBaseline where templateId='".input::get("templateId")."'");
        $data_values='';    
        $name = '';
        for ($i = 1; $i < $len_result; $i++){               
            $moName = $result[$i][0];     
            $qualification = $result[$i][1]; 
            $qualificationValue = $result[$i][2]; 
            $parameter =$result[$i][3]; 
            $recommendedValue = $result[$i][4];
            $category = $result[$i][5];  
            $version = $result[$i][6]; 
            $cellType = $result[$i][7];
              //$templateName = $_REQUEST['templateName'];  
            $templateId = input::get("templateId");

            $data_values .="(NULL,'$moName','$qualification','$qualificationValue','$parameter','$recommendedValue','$category','$templateId','$version','$cellType'),"; 
            
        }    
        $data_values = mb_convert_encoding(substr($data_values,0,-1), 'UTF-8');//解析文件编码是UTF-8无需转码
        fclose($handle); //关闭指针 
        $sql = "insert into formulaParaBaseline values $data_values";
        $query = $db -> query($sql); 
          
      
        
        if($query){         
            echo "true";    
        }else{        
            echo 'false';     
        }
    }
    protected function input_csv($handle) {    
        $out = array ();    
        $n = 0;     
         while ($data = fgetcsv($handle,10000)) 
        {        
             $num = count($data);        
             for ($i = 0; $i < $num; $i++)
             {             
                 $out[$n][$i] = $data[$i];         
             }        
             $n++;     
         }     
       return $out; 
    }

    public function addMode(){
        $templateName = input::get('modeName');
        $description = input::get('modeDescription');

        if(!Auth::user()){
            echo "login";
            return;
        }
        $user = Auth::user() -> user;

        $dsn = "mysql:host=localhost;dbname=mongs";
        $db = new PDO($dsn, 'root', 'mongs');

        $sql = "insert into templateParaBaseline values (null,'$templateName','$user','$description')";
        $res = $db -> query($sql);
        if($res){
            echo true;
        }else{
            echo false;
        }

    }

    public function deleteMode(){
        $id = input::get('id');

        if(!Auth::user()){
            echo "login";
            return;
        }
        $user = Auth::user() -> user;
        //print_r($user);
        $dsn = "mysql:host=localhost;dbname=mongs";
        $db = new PDO($dsn, 'root', 'mongs');

        if($user == "admin"){
            $sql = "delete from templateParaBaseline where id = '$id'";
            $res = $db -> exec($sql);
            if($res){
                $sql = "delete from formulaParaBaseline where templateId = '$id'";
                $res = $db -> exec($sql);
                
                echo "1";
            }else{
                echo "2";
            }
        }else{
            $sql = "delete from templateParaBaseline where id = '$id' and user = '$user'";
            $res = $db -> exec($sql);
            if($res){
                $sql = "delete from formulaParaBaseline where templateId = '$id'";
                $res = $db -> exec($sql);

                echo "1";
            }else{
                echo "3";
            }

        }
        
    }

}