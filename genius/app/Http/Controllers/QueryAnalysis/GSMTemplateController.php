<?php

namespace App\Http\Controllers\QueryAnalysis;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;

use PDO;

class GSMTemplateController extends Controller
{
    public function getGSMTreeData()
    {
        $users = DB::select('select distinct user from template_2G');
        $arrUser = array();
        $items = array();
        $itArr = array();
        foreach ($users as $user) {
            $userStr = $user->user;
            $templateNames = DB::table('template_2G')->where('user', '=', $userStr)->get();
            foreach ($templateNames as $templateName) {
                array_push($arrUser, array("text"=>$templateName->templateName, "id"=>$templateName->id));
            }
            if($userStr == "admin") {
                $items["text"] = "common";
            }else{
                $items["text"] = $userStr;
            }

            $items["nodes"] = $arrUser;
            $arrUser = array();
            array_push($itArr, $items);
        }
        return response()->json($itArr);
    }
    public function searchGSMTreeData()
    {
        $inputData = Input::get('inputData');
        $inputData = "%".$inputData."%";
        $dsn = "mysql:host=localhost;dbname=mongs";
        $db = new PDO($dsn, 'root', 'mongs');

        $users = DB::select('select distinct user from template_2G');
        $arrUser = array();
        $items = array();
        $itArr = array();
        foreach ($users as $user) {
            $userStr = $user->user;
            $sql = "select * from template_2G where user = '$userStr' and templateName like '$inputData'";
            $templateNames = $db -> query($sql) -> fetchAll(PDO::FETCH_ASSOC);
            if($templateNames) {
                foreach ($templateNames as $templateName) {
	                $temp['text'] = $templateName['templateName'];
	                $temp['id'] = $templateName['id'];
	                array_push($arrUser, $temp);
	            }
	            if($userStr == "admin") {
	                $items["text"] = "common";
	            }else{
	                $items["text"] = $userStr;
	            }
	            $items["nodes"] = $arrUser;
	            $arrUser = array();
	            array_push($itArr, $items);
            }
        }
        return response()->json($itArr);
    }
    public function getElementTree()
    {
        $templateName =input::get('templateName');
        $user =input::get('user');
        if($user == "common") {
            $user = "admin";
        }
        $dsn = "mysql:host=localhost;dbname=mongs";
        $db = new PDO($dsn, 'root', 'mongs');
        $sql = "select elementId from template_2G where templateName = '$templateName' and user = '$user'";
        $resTem = $db -> query($sql);
        if($resTem) {
            $elementId = $resTem -> fetchAll(PDO::FETCH_ASSOC);
        }
        echo json_encode($elementId[0]);
    }

    public function getKpiNamebyId()
    {
        $dsn = "mysql:host=localhost;dbname=mongs";
        $db = new PDO($dsn, 'root', 'mongs');

        $table="kpiformula_2G";

        $idarr=explode(',',input::get("id"));
        $items = array();
        foreach($idarr as $id ) {	
            $sql = "select * from $table where id='$id'";
            $res =  $db -> query($sql);
            if($row = $res->fetchAll(PDO::FETCH_ASSOC)) {
                $data['text'] = $row[0]['kpiName'];
                $data['id'] = $row[0]['id'];
                $data['user'] = $row[0]['user'];
                array_push($items, $data);
            }
        }
        echo json_encode($items);
    }

    public function getTreeTemplate()
    {
        $dsn = "mysql:host=localhost;dbname=mongs";
        $db = new PDO($dsn, 'root', 'mongs');

        $table = "kpiformula_2G";

        $rsUsers =$db->query("select distinct user from $table order by user asc");
        $idNum = 1; 
        $items = array(); //存储最终结果
        $othItems = array(); //存储其他用户结果

        $othChildren = array(); //存储其他用户下的子用户
        if($rsUsers) {
            $result = array(); //存储用户结果
            $rsUsersArr = $rsUsers->fetchAll(PDO::FETCH_ASSOC);

            foreach( $rsUsersArr as $row) {
            $result['id'] = $idNum++;
                if($row['user']=="admin") {
                    $result['kpiName'] = "common";
                }else{
                    $result['kpiName'] = $row['user'];
                }
			
                $result['state'] = 'closed';
                $children = array();
                $user = $row['user'];
                $sql = "select * from $table where user = '$user'";
                $res = $db -> query($sql);
                $resArr = $res->fetchAll(PDO::FETCH_ASSOC);
                if($resArr) {
                    foreach( $resArr as $rs) {
                        $kpiName = $rs['kpiName'];
                        $kpiFormula = $rs['kpiFormula'];
                        $kpiPrecision = $rs['kpiPrecision'];
                        $kpiId = $rs['id'];
                        array_push($children, array("id" => $kpiId, "kpiName" => $kpiName, "kpiFormula" => $kpiFormula, "kpiPrecision" => $kpiPrecision));
                        $result['children'] = $children;
                    }
                    array_push($items, $result);
                }
            }
        }		
        echo json_encode($items);
    }

    public function updateFormula()
    {
        $dsn = "mysql:host=localhost;dbname=mongs";
        $db = new PDO($dsn, 'root', 'mongs');

        $id = input::get("formulaId");
        $name = input::get("name");
        $precision = input::get("precision");
        $formula = input::get("formula");

        $user = Auth::user() -> user;

        if($id) {
            if($user == "admin") {
                $sql = "UPDATE kpiformula_2G set kpiName = '$name', kpiformula = '$formula', kpiPrecision = '$precision' where id = '$id'";
            }else{
                $sql = "UPDATE kpiformula_2G set kpiName = '$name', kpiformula = '$formula', kpiPrecision = '$precision' where id = '$id' and user = '$user'";
            }
            $res = $db -> exec($sql);
        }else{
            $sql = "insert into kpiformula_2G values (null,'$name','$user','$formula','$precision')";
            $res = $db -> exec($sql);
        }
        if($res) {
            echo true;
        }else{
            echo false;
        }
    }

    public function deleteFormula()
    {
        $dsn = "mysql:host=localhost;dbname=mongs";
        $db = new PDO($dsn, 'root', 'mongs');

        $id = input::get("id");
        $user = Auth::user() -> user;
        if($user == "admin") {
            $sql = "delete from kpiformula_2G where id ='$id'";
        }else{
            $sql = "delete from kpiformula_2G where id ='$id' and user = '$user'";
        }
    	
        $res = $db -> exec($sql);
        if(!$res) {
            echo false;
            return;
        }

        /*删除后更新template表中相关内容*/
        $tableTem = "template_2G";
        $sqlTem = "SELECT elementId from $tableTem";
        $resTem = $db -> query($sqlTem) ->fetchAll(PDO::FETCH_ASSOC);
        $row = [];
        $i = 0;
	
        foreach($resTem as $rowTem) {
            $row[$i++] = $rowTem['elementId'];
        }
        $rowTem = [];
        $rowTem_1 = [];
        $k = 0;
        for($i=0; $i<count($row); $i++) {
            $rowTem[$i] = explode(',', $row[$i]);
            for($j=0; $j<count($rowTem[$i]); $j++) {
                if($rowTem[$i][$j] == $id) {
                    $rowTem_1[$k] = implode(',', $rowTem[$i]);
                    $k++;
                }
            }
        }
        $finalRow_0 = [];
        $finalRow_1 = [];
        $finalRow = [];
        for($i=0; $i<count($rowTem_1); $i++) {
            $m = 0;
            $finalRow_0[$i] = explode(',', $rowTem_1[$i]);
            $finalRow_1[$i] = array();
            for($j=0; $j<count($finalRow_0[$i]); $j++) {
                if($finalRow_0[$i][$j] != $id) {
                    $finalRow_1[$i][$m] = $finalRow_0[$i][$j];
                    $m++;
                }
            }
            $finalRow[$i] = implode(',', $finalRow_1[$i]);
        }
        $sql_update = [];
        for($i=0; $i<count($finalRow); $i++) {
            $old = $rowTem_1[$i];
            $new = $finalRow[$i];
            $sql_update[$i] = "UPDATE $tableTem SET elementId = '$new' WHERE elementId = '$old'";
            $db -> query($sql_update[$i]);
        }
        echo true;
    }

    public function searchTreeTemplate()
    {
        $dsn = "mysql:host=localhost;dbname=mongs";
        $db = new PDO($dsn, 'root', 'mongs');

        $formula = input::get("formula");

        $table = "kpiformula_2G";

        $rsUsers =$db -> query("select distinct user from $table order by user asc"); //筛选所有user
        $idNum = 1; 
        $items = array(); //存储最终结果
        $othItems = array(); //存储其他用户结果

        $othChildren = array(); //存储其他用户下的子用户
        if($rsUsers) {
            $result = array(); //存储用户结果
            $rsUsersArr = $rsUsers->fetchAll(PDO::FETCH_ASSOC);

            foreach($rsUsersArr as $row) {
                $result['id'] = $idNum++;
                if($row['user']=="admin") {
                    $result['kpiName'] = "common";
                }else{
                    $result['kpiName'] = $row['user'];
                }
                $result['state'] = 'closed';
                $children = array();
                $user = $row['user'];
                $sql = "select * from $table where user = '$user' and (kpiFormula like '%$formula%' or kpiName like '%$formula%')";
                $res = $db -> query($sql);
                $resArr = $res->fetchAll(PDO::FETCH_ASSOC);
                if($resArr) {
                    foreach( $resArr as $rs) {
                        $kpiName = $rs['kpiName'];
                        $kpiFormula = $rs['kpiFormula'];
                        $kpiPrecision = $rs['kpiPrecision'];
                        $kpiId = $rs['id'];
                        array_push($children, array("id" => $kpiId, "kpiName" => $kpiName, "kpiFormula" => $kpiFormula, "kpiPrecision" => $kpiPrecision));
                        $result['children'] = $children;
                    }
                    array_push($items, $result);
                }
            }
        }		
        echo json_encode($items);
    }

    public function updateElement()
    {
        $id =input::get('id');
        $ids =input::get('ids');

        $dsn = "mysql:host=localhost;dbname=mongs";
        $db = new PDO($dsn, 'root', 'mongs');

        $user = Auth::user() -> user;
        if($user=="admin") {
            $sql = "UPDATE template_2G SET elementId = '$ids' where id = '$id'";
        }else{
            $sql = "UPDATE template_2G SET elementId = '$ids' where id = '$id' and user = '$user'";
        }
        
        $res = $db -> exec($sql);
        if($res) {
            echo true;
        }else{
            echo false;
        }
    }

    public function addMode()
    {
        $templateName = input::get('modeName');
        $description = input::get('modeDescription');

        if(!Auth::user()) {
            echo "login";
            return;
        }
        $user = Auth::user() -> user;

        $dsn = "mysql:host=localhost;dbname=mongs";
        $db = new PDO($dsn, 'root', 'mongs');

        $sql = "insert into template_2G values (null,'$templateName',null,'$description','$user')";
        $res = $db -> query($sql);
        if($res) {
            echo true;
        }else{
            echo false;
        }
    }
    public function deleteMode()
    {
        $id = input::get('id');

        if(!Auth::user()) {
            echo "login";
            return;
        }
        $user = Auth::user() -> user;
        $dsn = "mysql:host=localhost;dbname=mongs";
        $db = new PDO($dsn, 'root', 'mongs');

        if($user == "admin") {
            $sql = "delete from template_2G where id = '$id'";
            $res = $db -> exec($sql);
            if(!$res) {
                echo "1";
            }else{
                echo "2";
            }
        }else{
            $sql = "delete from template_2G where id = '$id' and user = '$user'";
            $res = $db -> exec($sql);
            if($res) {
                echo "1";
            }else{
                echo "3";
            }
        }
    }
    public function copyMode()
    {
        $id=input::get("copyId");
        $templateName = input::get('modeName_copy');
        $description = input::get('modeDescription_copy');

        $dsn = "mysql:host=localhost;dbname=mongs";
        $db = new PDO($dsn, 'root', 'mongs');

        $sql = "select elementId from template_2G where id = '$id'";
        $res = $db -> query($sql);
        $item = $res->fetchAll(PDO::FETCH_ASSOC);
        $elementId = $item[0]['elementId'];
        $user = Auth::user() -> user;
        $sql = "insert into template_2G values (null,'$templateName','$elementId','$description','$user')";
        $res = $db -> exec($sql);
        if($res) {
            echo true;
        }else{
            echo false;
        }
    }
}