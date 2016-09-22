<?php

namespace App\Http\Controllers\systemManage;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use PDO;
use Illuminate\Support\Facades\Auth;

class storageController extends Controller{

	 public function taskQuery(){
        $dsn = "mysql:host=localhost;dbname=mongs";
        $db = new PDO($dsn, 'root', 'mongs');
       
        $id = input::get("id");
        if($id == 1){
            $type = "parameter";
        }else if($id == 2){
            $type = "ctrsystem";
        }
        else if($id == 3){
            $type = "cdrsystem";
        }
        else if($id == 4){
            $type = "ebmsystem";
        }
        if($id == 0){
            $sql = "select * from task order by createTime desc";
        }else{
            $sql = "select * from task where type = '$type' order by createTime desc";
        }        
        $res = $db->query($sql);

        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        $items = array();
        foreach ($row as $qr) { 
            array_push($items, $qr);
        }
            
        $result = array();
        $result['text'] = 'taskName,status,startTime,endTime,tracePath,owner,createTime,type';
        $result['rows'] = $items;
        echo json_encode($result);
    }

    public function getTaskTraceDir(){
        $dir = '/data/trace/';
        $type = input::get("type");
        if(is_dir($dir)){
            echo '['.$this -> tree($dir,1,false,$type).']';

        }
    }

    public function tree($directory,$pid,$flag,$type){ 
        $mydir = opendir($directory);
        $i=1; 
        $content=array();
        //$type = $_REQUEST['type'];
        while($file = readdir($mydir))
        {   
            if((is_dir("$directory/$file")) && ($file!=".") && ($file!="..")) 
            {   
                if ($type == 'parameter' && ($file == 'kget' | $flag)) {
                        $nodes = $this ->tree("$directory/$file",$pid.$i,true,$type);
                        if($nodes){
                            $content[$i-1]= '{"id":'.$pid.$i.',"text":"'.$file.'","iconCls":"icon-blank","path":"'."$directory/$file".'","nodes":['.$nodes.']}';
                        }else{
                            $content[$i-1]= '{"id":'.$pid.$i.',"text":"'.$file.'","iconCls":"icon-blank","path":"'."$directory/$file".'"}';
                        }
                        
                        $i=$i+1;
                    }
                if ($type == 'ctrsystem' && ($file == 'ctr' | $flag)) {
                        $nodes = $this ->tree("$directory/$file",$pid.$i,true,$type);
                        if($nodes){
                            $content[$i-1]= '{"id":'.$pid.$i.',"text":"'.$file.'","iconCls":"icon-blank","path":"'."$directory/$file".'","nodes":['.$nodes.']}';
                        }else{
                            $content[$i-1]= '{"id":'.$pid.$i.',"text":"'.$file.'","iconCls":"icon-blank","path":"'."$directory/$file".'"}';
                        }
                        $i=$i+1;
                    }   
                if ($type == 'cdrsystem' && ($file == 'cdr' | $flag)) {
                        $nodes = $this ->tree("$directory/$file",$pid.$i,true,$type);
                        if($nodes){
                            $content[$i-1]= '{"id":'.$pid.$i.',"text":"'.$file.'","iconCls":"icon-blank","path":"'."$directory/$file".'","nodes":['.$nodes.']}';
                        }else{
                            $content[$i-1]= '{"id":'.$pid.$i.',"text":"'.$file.'","iconCls":"icon-blank","path":"'."$directory/$file".'"}';
                        }
                        $i=$i+1;
                    }
                if ($type == 'ebmsystem' && ($file == 'ebm' | $flag)) {
                        $nodes = $this ->tree("$directory/$file",$pid.$i,true,$type);
                        if($nodes){
                            $content[$i-1]= '{"id":'.$pid.$i.',"text":"'.$file.'","iconCls":"icon-blank","path":"'."$directory/$file".'","nodes":['.$nodes.']}';
                        }else{
                            $content[$i-1]= '{"id":'.$pid.$i.',"text":"'.$file.'","iconCls":"icon-blank","path":"'."$directory/$file".'"}';
                        }
                        $i=$i+1;
                    }   
                 
            } 
        }  
        closedir($mydir); 
        return implode(",",$content);
    }

    public function addTask(){
        $dsn = "mysql:host=localhost;dbname=mongs";
        $db = new PDO($dsn, 'root', 'mongs');   

        $taskName=input::get("taskName");

        $rs = $db->query("select * from task where taskName='$taskName'");
        $num_rows = count($rs->fetchAll(PDO::FETCH_ASSOC));
        if($num_rows>0){
            echo "false";
        }else{
            $type=input::get("type");
            $createTime=input::get("createTime");
            $tracePath=input::get("tracePath");
            session_start();
            if(!Auth::user()){
                echo "login";
                return;
            }
            $owner=Auth::user() -> user;
            $sql= "insert into task values('$taskName','prepare','NULL','NULL','$tracePath','$owner','$createTime','$type')";
            $rs = $db->query($sql);

            $DecodeDir = '/opt/mongs/mongs_all_parser/etc/ctr/';
            if($type=='ctrsystem'){
                $fileName=$DecodeDir.'decode_'.$taskName.'.conf';
                $this->config(input::get("taskConfig"),$fileName);
            }
            /*if($_REQUEST['type']=='cdrsystem')
            {
                $fileName=TaskDir.'cdr/cdr_parser_'.$_POST['taskName'].'.conf';
                config($_POST['taskConfig'],$fileName);
            }
            if($_REQUEST['type']=='ebmsystem')
            {
                $fileName=TaskDir.'ebm/ebm_parser_'.$_POST['taskName'].'.conf';
                config($_POST['taskConfig'],$fileName);
            }*/
            echo "true";
        }
    }

    public function config($treeData,$fileName){
        $phpData= array();
        array_push($phpData, json_decode($treeData,true));
        $output="";
        foreach($phpData as $types){
            $output .= "[".$types["text"]."]\n";
            foreach($types["nodes"] as $protocols){
                $output .= "[[".$protocols["text"]."]]\n";
                foreach($protocols["nodes"] as $protocolss){
                    if(isset($protocolss["state"]['checked'])){
                        if($protocolss["state"]['checked'] == "1"){
                             $checked = "true";
                        }else{
                             $checked ="false";
                        }
                    }else{
                         $checked = "true";
                    }
                    if($checked=="true") {
                        $output .= $protocolss["text"]."\n";        
                    }else{
                        $output .= "#".$protocolss["text"]."\n";        
                    }
                }
            }
        }
        file_put_contents($fileName, $output);
    }

    public function deleteTask(){
        $dsn = "mysql:host=localhost;dbname=mongs";
        $db = new PDO($dsn, 'root', 'mongs');   

        $taskName = input::get("taskName");

        $sql= "delete from task where taskName='$taskName'";
        $rs = $db->query($sql);
        //$conn = mysql_connect(DBhost, DBuser, DBpasswd);
        $sql="DROP DATABASE ".$taskName;
        //print_r($sql);return;
        $query = $db->query($sql);
        echo "true";
    }

    public function monitor(){
        $taskName = input::get("taskName");
        $filename='common/files/monitor'.$taskName.'.txt';
        //$filename='files/monitortask1.txt';
        if(file_exists($filename)){
            $data=file_get_contents($filename);
            echo str_replace("\n","<br/>",$data);
            //echo $data;
        }
    }

    public function runTask(){
        $dsn = "mysql:host=localhost;dbname=mongs";
        $db = new PDO($dsn, 'root', 'mongs');   

        $taskName = input::get("taskName");
        $tracePath = input::get("tracePath");
        $startTime = input::get("startTime");
        $type = input::get("type");



        $sql= "update task set status='ongoing', startTime='$startTime' where taskName='$taskName'";
        $rs = $db->query($sql);
        $command="common/sh/start_mongs.sh ".$taskName." ".$tracePath." ".$type;
        exec($command);
        $command1="common/sh/parameterPrint.sh ".$taskName;
        exec($command1);
        //sleep(20);
        //file_put_contents('files/monitor.txt', $output);
        $sql= "select * from task where taskName='$taskName'";
        $rs = $db->query($sql);
        $row = $rs->fetchAll(PDO::FETCH_ASSOC)[0];
        //print_r($row);
        if($row['status']=='abort'){
            $return['status']="abort";
            $return['row']=array(
                'taskName' => $row['taskName'],
                'status' => $row['status'],
                'startTime' => $row['startTime'],
                'endTime' => $row['endTime'],
                'tracePath' => $row['tracePath'],
                'owner' => $row['owner'],
                'createTime' => $row['createTime'],
                'type' => $row['type']
                );
            echo json_encode($return);
        }else{
            $endTime=date('y-m-d H:i:s',time()+8*3600);
            $sql= "update task set status='complete', endTime='".$endTime."' where taskName='$taskName'";
            $rs = $db->query($sql);
            $return['status']="true";
            $return['row']=array(
            'taskName' => $row['taskName'],
            'status' => 'complete',
            'startTime' => $row['startTime'],
            'endTime' => $endTime,
            'tracePath' => $row['tracePath'],
            'owner' => $row['owner'],
            'createTime' => $row['createTime'],
            'type' => $row['type']
            );
            echo json_encode($return);
        }
    }

    public function stopTask(){
        $dsn = "mysql:host=localhost;dbname=mongs";
        $db = new PDO($dsn, 'root', 'mongs');   

        $taskName = input::get("taskName");
        $endTime=date('y-m-d H:i:s',time()+8*3600);
        $sql= "update task set status='abort', endTime='".$endTime."' where taskName='$taskName'";
        $rs = $db->query($sql);
        $sql= "select * from task where taskName='$taskName'";
        $rs = $db->query($sql);
        $row = $rs->fetchAll(PDO::FETCH_ASSOC)[0];
        system("common/sh/stop_monitor.sh ".$taskName);
        $return['status']="abort";
        $return['row']=array(
                'taskName' => $row['taskName'],
                'status' => "abort",
                'startTime' => $row['startTime'],
                'endTime' => $endTime,
                'tracePath' => $row['tracePath'],
                'owner' => $row['owner'],
                'createTime' => $row['createTime'],
                'type' => $row['type']
            );
        echo json_encode($return);
    }
}