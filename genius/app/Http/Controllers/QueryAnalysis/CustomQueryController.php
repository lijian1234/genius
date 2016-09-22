<?php
/**
* 文件名(CustomQueryController.php)
*
* 功能描述(LTE语句查询控制模块)
*
* @author lijian
*/
namespace App\Http\Controllers\QueryAnalysis;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use PDO;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Common\DataBaseConnection;

/**
* 类名 CustomQueryController
* 类功能描述
* @package CustomQueryController
*/
class CustomQueryController extends Controller
{
    public function getCustomTreeData()
    {
        $users = DB::select('select distinct user from template');
        $arrUser = array();
        $items = array();
        $itArr = array();
        foreach ($users as $user) {
            $userStr = $user->user;
            $templateNames = DB::table('customTemplate')
                            ->where('user', '=', $userStr)->get();
            foreach ($templateNames as $templateName) {
                array_push($arrUser, array("text"=>$templateName->templateName));
            }
            $items["text"] = $userStr;
            $items["nodes"] = $arrUser;
            $arrUser = array();
            array_push($itArr, $items);
        }
        return response()->json($itArr);
    }

  	public function saveModeChange()
    {
        $templateName = Input::get('templateName');
        $customContext = Input::get('content');
        $user = Auth::user();//获取登录用户信息	
		if($user == null) {
            return 'login';
        }else{
            $userName=$user->user;
            $dsn = "mysql:host=localhost;dbname=mongs";
            $db = new PDO($dsn, 'root', 'mongs');
			if($db == null) {
                $result["result"] = "false";
                $result["reason"] = "Failed to connect to database.";
                echo json_encode($result);
                return;
            }
            $sql = "update customTemplate set kpiformula = \"".$customContext."\" where templateName = '"
                .$templateName."' and user = '".$userName."';";
            $rs = $db->query($sql);
            return 'success';
        }
    }

 
    public function getSearchCustomTreeData()
    {
        $inputData = Input::get('inputData');
        $inputData = "%".$inputData."%";
        $users = DB::table('customTemplate')->
                where('templateName', 'like', $inputData)->get();
        $items = array();
        foreach ($users as $user) {
            $templateName = '{"text":"'.$user->templateName.'","value":"'
                            .$user->templateName.'"}';
            array_push($items, $templateName);
        }
        return response()->json($items);
    }

    public function getAllCity(){
        $cityClass = new DataBaseConnection();
        return $cityClass->getCityOptions();
    }

    public function getKpiFormula()
    {
        $templateName = Input::get('treeData');
        $templateName = trim($templateName);
        $databaseconn = DB::table('customTemplate')->
                        where('templateName', 'like', $templateName)->get();
        $kpiformula = $databaseconn[0]->kpiformula;
        return $kpiformula;
    }

    public function getTable()
    {
        $cityArr = Input::get('city');
        $citys = array();
        $cityPY = new DataBaseConnection();
        foreach ($cityArr as $city) {
            $cityStr = $cityPY->getCityByCityChinese($city)[0]->connName;
            array_push($citys, $cityStr);
        }

        $templateName = Input::get('templateName');
        $templateName = trim($templateName);
        $databaseconn = DB::table('customTemplate')->
                        where('templateName', 'like', $templateName)->get();
        $webSql = $databaseconn[0]->kpiformula;
        $dsn = "mysql:host=localhost;dbname=mongs";
        $db = new PDO($dsn, 'root', 'mongs');
        if($db == null) {
            $result["result"] = "false";
		    $result["reason"] = "Failed to connect to database.";
		    echo json_encode($result);
		    return;
        }

        $result = array();
        $count = array();
        foreach ($citys as $city) {
            $sql = "SELECT host,port,dbName,userName,password FROM databaseconn 
                    WHERE connName = '".$city."'";
            $res = $db->query($sql);
            $row = $res->fetch(PDO::FETCH_ASSOC);
            $host = $row['host'];
            $port = $row['port'];
            $dbName = $row['dbName'];
            $userName = $row['userName'];
            $password = $row['password'];

            $pmDbDSN = "dblib:host=" .$host. ":" .$port. ";dbname=" .$dbName;
            $pmDB = new PDO($pmDbDSN, $userName, $password);

            if($pmDB == null) {
                $result["result"] = "false";
                $result["reason"] = "Failed to connect to database.";
                echo json_encode($result);
                return;
            }

            $rows = $pmDB->query($webSql);
            if ($pmDB->errorCode() != '00000') {
                $result['failed'] = 'failed';
                echo json_encode($result); 
            }else {
			    if($rows) {
			        while($res = $rows->fetch(PDO::FETCH_ASSOC)) {
			            array_push($count, $res);
                    }
                } 
            }		
        }
        if (array_key_exists("datetime_id", $count[0])) {
            $i = 0;
            foreach ($count as $counts) {
                $count[$i]['datetime_id'] = date('Y-m-d ', strtotime($counts['datetime_id']));
                $i++;
            }
        }
        $result['total'] = count($count);
        $result['rows'] = $count;
        $keys = array_keys($count[0]);
        $result['text'] = implode(",", $keys); 

        $templateName = Input::get('templateName');
        $filename = "common/files/" . $templateName . date('YmdHis') . ".csv";
        $this->resultToCSV2($result, $filename);
        $result['filename'] = $filename;           
        echo json_encode($result);
    }

    protected function resultToCSV2($result, $filename)
    {
        $csvContent = mb_convert_encoding($result['text'] . "\n", 'gb2312', 'utf-8');
        $fp = fopen($filename, "w");
        fwrite($fp, $csvContent);
        foreach ($result['rows'] as $row) {
            fputcsv($fp, $row);
        }
        fclose($fp);
    }

    public function deleteMode()
    {
        $dsn = "mysql:host=localhost;dbname=mongs";
        $db = new PDO($dsn, 'root', 'mongs');
        if($db == null) {
            $result["result"] = "false";
            $result["reason"] = "Failed to connect to database.";
            echo json_encode($result);
            return;
        }
        $templateName = Input::get('templateName');
        $sql = "delete from customTemplate where templateName = '$templateName'";
        $db->query($sql);
    }

    public function insertMode()
    {
        $templateName = Input::get('insertName');
        $user = Auth::user();//获取登录用户信息	
        if($user == null) {
            return 'login';
        }else{
            $userName=$user->user;
            $dsn = "mysql:host=localhost;dbname=mongs";
            $db = new PDO($dsn, 'root', 'mongs');
            if($db == null) {
                $result["result"] = "false";
                $result["reason"] = "Failed to connect to database.";
                echo json_encode($result);
                return;
            }
            $rs = $db->query("select count(*) as num from customTemplate 
            	        where user='$userName' and templateName='$templateName'");
            $row = $rs->fetch(PDO::FETCH_ASSOC);
            if($row['num'] > 0) {
                return 'wrong';
            }else{
                $sql = "insert into customTemplate (templateName,user) 
                        values('$templateName','$userName')";
                $res = $db->query($sql);
                return 'success';
            }	
        }
    }

    public function saveMode()
    {
        $templateName = Input::get('templateName');
        $customContext = Input::get('customContext');
        $user = Auth::user();//获取登录用户信息	
        if($user == null) {
            $items[$i] = 'login';
            return 'login';
        }else{
            $userName=$user->user;
            $dsn = "mysql:host=localhost;dbname=mongs";
            $db = new PDO($dsn, 'root', 'mongs');
            if($db == null) {
                $result["result"] = "false";
                $result["reason"] = "Failed to connect to database.";
                echo json_encode($result);
                return;
            }
            $rs = $db->query("update customTemplate set kpiformula = '$customContext' 
            	        where templateName = '$templateName' and user = '$userName';");
            return 'success';
        }
    }
}