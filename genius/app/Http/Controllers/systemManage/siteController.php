<?php

namespace App\Http\Controllers\systemManage;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use PDO;
use Illuminate\Support\Facades\Auth;

class siteController extends Controller{

    public function TreeQuery(){
        $table = input::get("table");
        $text = input::get("text");
        $value = input::get("value");

        $dsn = "mysql:host=localhost;dbname=mongs";
        $db = new PDO($dsn, 'root', 'mongs');
        
        $sql = "select id, cityChinese,connName from ".$table." group by cityChinese";
        $res = $db->query($sql);
        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        $items = array();
        $citys = array();
        array_push($citys, array("id"=>0,"text"=>"全部城市","value"=>"city"));
        foreach ($row as $qr) { 
            $array = array("id"=>$qr["id"],"text"=>$qr[$text],"value"=>$qr[$value]);
            array_push($citys, $array);
        }

        echo json_encode($citys);
    }


	 public function QuerySite4G(){
        $value = input::get("value");
        $table = input::get("table");

       
        $dsn = "mysql:host=localhost;dbname=mongs";
        $db = new PDO($dsn, 'root', 'mongs');

        $limit='';
        $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $rows = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;
        $offset = ($page-1)*$rows;
        $limit=" limit $offset,$rows";

        
        if($value == "city"){
            $sql = "select count(*) from ".$table;
        }else{
            $sql = "select count(*) from ".$table." where city like '$value%'";
        }
        $res = $db->query($sql);
        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        $result = array();
        $result["total"] = $row[0]['count(*)'];

        if($value == "city"){
            $sql = "select * from ".$table.$limit;
        }else{
            $sql = "select * from ".$table." where city like '$value%' order by id".$limit;
        }
       // echo $sql; return;
        $res = $db->query($sql);

        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        $items = array();
        foreach ($row as $qr) { 
            array_push($items, $qr);
        }
            
        
        $result['records'] = $items;
        echo json_encode($result);
    }
    public function QuerySite2G(){
        $value = input::get("value");
        $table = input::get("table");

       
        $dsn = "mysql:host=localhost;dbname=mongs";
        $db = new PDO($dsn, 'root', 'mongs');

        $limit='';
        $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $rows = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;
        $offset = ($page-1)*$rows;
        $limit=" limit $offset,$rows";
        
        if($value == "city"){
            $sql = "select count(*) from ".$table;
        }else{
            $sql = "select count(*) from ".$table." where city = '$value'";
        }
        $res = $db->query($sql);
        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        $result = array();
        $result["total"] = $row[0]['count(*)'];

        if($value == "city"){
            $sql = "select * from ".$table.$limit;
        }else{
            $sql = "select * from ".$table." where city = '$value' order by id".$limit;
        }
       // echo $sql; return;
        $res = $db->query($sql);

        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        $items = array();
        foreach ($row as $qr) { 
            array_push($items, $qr);
        }
            
        $result['records'] = $items;
        echo json_encode($result);
    }


    public function uploadFile(){
        $dsn = "mysql:host=localhost;dbname=mongs";
        $db = new PDO($dsn, 'root', 'mongs');
        

        
        $table=input::get("table");

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
        // echo $result; return;

        if($table=='siteManage')
        {
            $db -> query("delete from siteLte where city like '".input::get("city")."%'");
            $data_values='';    
            $name = '';
            for ($i = 1; $i < $len_result; $i++) 
            {               
                $ecgi = $result[$i][0];     
                $cellName = $result[$i][1]; 
                $siteName = $result[$i][2]; 
                $cellNameChinese = $result[$i][3];
                //mb_convert_encoding($cellNameChinese,"UTF-8","UTF-8");//编码转换为utf-8
                $longitude = $result[$i][4]; 
                $latitude = $result[$i][5];  
                $dir = $result[$i][6];  
                $pci = $result[$i][7];  
                $channel = $result[$i][8];  
                $coverageType = $result[$i][9]; 
                $tiltM = $result[$i][10];     
                $tiltE = $result[$i][11];  
                $Height = $result[$i][12];     
                $city = input::get("city");
                $importDate = input::get("importDate");
                //$band = $result[$i][15];
                $data_values .= "(NULL,'$ecgi','$cellName','$siteName','$cellNameChinese','$longitude','$latitude','$dir','$pci','$channel','$coverageType','$tiltM','$tiltE','$Height','$city','$importDate'),"; 
                
            }    
            $data_values = mb_convert_encoding(substr($data_values,0,-1), 'UTF-8');//解析文件编码是UTF-8无需转码
            //$data_values = substr($data_values,0,-1);//去掉最后一个逗号
            //echo "$data_values".$data_values;return;
            fclose($handle); //关闭指针 
            $sql = "insert into siteLte (id,ecgi,cellName,siteName,cellNameChinese,longitude,latitude,dir,pci,channel,coverageType,tiltM,tiltE,Height,city,importDate) values $data_values";
           // $sql = "insert into siteLte values $data_values";
            echo $sql;
            $query = $db -> query($sql); 
            $sign = "4G";
          
        }
        else if($table == '2GSiteManage'){
            $db -> query("delete from siteGsm where city='".input::get("city")."'");
            $data_values='';    
            $name = '';
            for ($i = 1; $i < $len_result; $i++) 
            {               
                $cell = $result[$i][0];     
                $cellIdentity = $result[$i][1]; 
                $band = $result[$i][2]; 
                $arfcn = $result[$i][3];
                $Longitude = $result[$i][4];
                $Latitude = $result[$i][5];
                $dir = $result[$i][6];
                $height = $result[$i][7];
                //mb_convert_encoding($cellNameChinese,"UTF-8","UTF-8");//编码转换为utf-8
                $plmnIdentity_mcc = $result[$i][8]; 
                $plmnIdentity_mnc = $result[$i][9];  
                $lac = $result[$i][10];
                $bcch = $result[$i][11];
                $bcc = $result[$i][12];  
                $ncc = $result[$i][13];  
                $dtmSupport = $result[$i][14];   
                $city = input::get("city") ;
                $importDate = input::get("importDate");
                /*$data_values .= "(NULL,'$ecgi','$cellName','$siteName','$cellNameChinese','$longitude','$latitude','$dir','$pci','$channel','$coverageType','$tiltM','$tiltE','$Height','$city','$importDate'),"; */
                $data_values .= "(NULL,'$cell','$cellIdentity','$band','$arfcn','$Longitude','$Latitude','$plmnIdentity_mcc','$plmnIdentity_mnc','$lac','$bcch','$bcc','$ncc','$dtmSupport','$city','$importDate','$dir','$height'),";
                
            }    
            //$data_values = substr($data_values,0,-1);//去掉最后一个逗号
            $data_values = mb_convert_encoding(substr($data_values,0,-1), 'UTF-8');//解析文件编码是UTF-8无需转码
            fclose($handle); //关闭指针 
            //$query = mysql_query("insert into siteGsm values $data_values"); 
            //echo "insert into siteLte values $data_values";
            $sql = "insert into siteGsm values $data_values";
            //echo $sql;return;
            $query = $db -> query($sql); 
            $sign = "2G";
        }
        if($query){         
            echo $sign;    
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

    public function downloadFile(){
        $dsn = "mysql:host=localhost;dbname=mongs";
        $db = new PDO($dsn, 'root', 'mongs');

        $filter='';
        $result = array();
        //$items = array();
        $fileContent = array();
        $csvContent = "";
        $table = input::get("table");

        if($table == 'siteManage'){
            $city = input::get("city");
            $filename="common/files/站点信息_".$city."_".date('YmdHis').".csv";
            /*get content*/
            
            $column = ' ecgi,cellName,siteName,cellNameChinese,longitude,latitude,dir,pci,channel,coverageType,tiltM,tiltE,Height,city,importDate,band';
            $result["text"] = $column;
            if($city == 'city'){
                $sql = "select".$column." from siteLte order by city";
            }else{
                $sql = "select".$column." from siteLte where city like '$city%' order by id";
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

        }else if($table == '2GSiteManage'){
            $city = input::get("city");
            $filename="common/files/站点信息_".$city."_".date('YmdHis').".csv";
            /*get content*/
            
            $column = ' CELL,CellIdentity,BAND,ARFCN,Longitude,Latitude,dir,height,plmnIdentity_mcc,plmnIdentity_mnc,LAC,BCCH,BCC,NCC,dtmSupport,city,importDate ';
            $result["text"] = $column;
            if($city == 'city'){
                $sql = "select".$column." from siteGsm order by city";
            }else{
                $sql = "select".$column." from siteGsm where city='$city' order by id";
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
        }
    

        $this->resultToCSV2($result, $filename,$table);
        $result['filename'] = $filename;
        if (count($items) > 1000) {
            $result['rows'] = array_slice($items, 0, 1000);
        }

        echo json_encode($result);

    }

     protected function resultToCSV2($result, $filename,$table)
    {
        $csvContent = mb_convert_encoding($result['text'] . "\n", 'GBK');
        $fp = fopen($filename, "w");
        fwrite($fp, $csvContent);
        if($table == 'siteManage'){
            foreach ($result['rows'] as $row) {
               // print_r($row['cellNameChinese']);
                $row['cellNameChinese'] = mb_convert_encoding( $row['cellNameChinese'], 'GBK');
                $row['coverageType'] = mb_convert_encoding( $row['coverageType'], 'GBK');
                fputcsv($fp, $row);
            }
        }else if($table == '2GSiteManage'){
             foreach ($result['rows'] as $row) {
                fputcsv($fp, $row);
            }
        }
        
        fclose($fp);
    }
}