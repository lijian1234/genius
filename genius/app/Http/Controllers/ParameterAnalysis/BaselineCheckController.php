<?php

namespace App\Http\Controllers\ParameterAnalysis;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Common\DataBaseConnection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;

use App\User;
use App\DatabaseConn;
use PDO;
 
class BaselineCheckController extends Controller
{
    /**
     * @desc ：获取base检查的基本数据
     * Time：2016/06/28 14:53:44
     * @author zhangyan
     * @param 参数类型
     * @return 返回值类型
    */
      public function getBaseTree(){
        $users = DB::select('select distinct user from templateParaBaseline order by user');
        $arrUser = array();
        $items = array();
        $itArr = array();
        foreach ($users as $user) {
            $userStr = $user->user;
            $templateNames = DB::table('templateParaBaseline')->where('user', '=', $userStr)->get();
            foreach ($templateNames as $templateName) {
                array_push($arrUser, array("text"=>$templateName->templateName,"id"=>$templateName->id));
            }
            $items["text"] = $userStr;
            $items["nodes"] = $arrUser;
            $arrUser = array();
            array_push($itArr, $items);
        }
        return response()->json($itArr);
    }
    /**
     * getParamTasks()
     * 获取日期（数据库名称）
     * @param mixed $city
     * @return
     */
    public function getParamTasks(){
        $filter = '';
        $items = array();
        $i = 0;
        $user = Auth::user();//获取登录用户信息
        if ($user == NULL) {
             $items[$i] = 'login';
            return response()->json($items);
        }else{
             $userName=$user->user;//需要获取当前登录用户！！！！！
             if ($user != 'admin') {
                $filter = " and owner in('$userName','admin')";
             }
        }
        $tasks = DB::select("select * from task where type=:type and status=:status $filter order by taskName desc",['type'=>'parameter','status'=>'complete']);
        $items = array();
        $i=0;
        foreach ($tasks as $task) {
            $items[$i++]='{"text":"'.$task->taskName.'"}';
        }
        return json_encode($items);//需要通过response返回响应数据
       
    }
    /**
     * getParamCitys()
     * 获取城市
     * @param mixed $city
     * @return
     */
    public function getParamCitys(){
        $dbc = new DataBaseConnection();
        return $dbc->getCityOptions();
    }
    /**
     * @desc ：根据表格获取城市
     * Time：2016/06/29 17:06:27
     * @author zhangyan
     * @param 参数类型
     * @return 返回值类型
    */
    public function getAllCity(){
        $table = $_REQUEST['table'];
        $db = $_REQUEST['db'];

        $conn = @mysql_connect('localhost', 'root', 'mongs');
        if(!$conn){
            die('Could not connet: ' . mysql_error());
        }

        mysql_select_db($db, $conn);

        $rows = array();

        $sql = "SELECT connName,cityChinese FROM `" .$table. "` order by id ASC";
        $res = mysql_query($sql);
        if($res){
            while($row = mysql_fetch_row($res)){
                array_push($rows, $row);
            }
        }
        echo json_encode($rows);
    }
    
    /**
     * getParamTableField()
     * 获取表头
     * @return
     */
    public function getTableField(){

        $db = Input::get('db');
        $table = Input::get('table');
        $dsn = "mysql:host=localhost;dbname=$db";
        $dbn = new PDO($dsn, 'root', 'mongs');
        $result = array();
        $query= "select * from ".$_REQUEST['table']." limit 1"; 
        $rs = $dbn->query($query,PDO::FETCH_ASSOC);
        $rs = $rs->fetchAll();
        //dump($rs[0]);
        return $rs[0];
    }
    /**
     * @desc ：获取数据信息
     * Time：2016/06/29 17:54:26
     * @author zhangyan
     * @param 参数类型
     * @return 返回值类型
    */
    public function getChartDataCategory(){
        $dbc = new DataBaseConnection();
        $db = Input::get('db');
        $table = Input::get('table');
        $templateId = Input::get('templateId');
        $dsn = "mysql:host=localhost;dbname=$db";
        $dbn = new PDO($dsn, 'root', 'mongs');

        $sql_category = "select DISTINCT category from ParaCheckBaseline where category != '' and SUBSTRING(category,1,4) != '!!!!' and templateId = $templateId ORDER BY category";
        $rs = $dbn->query($sql_category,PDO::FETCH_OBJ);
        $rs = $rs->fetchAll();
        $categories = array();
        $categories = $this->getHighChartCategory($rs);
        $res = $dbc->getCity_subNetCategories();
        $series = array();
        foreach ($res as $items) {
            $city = $items->connName;
            $subNetwork = $items->subNetwork;
            $subNetwork = $dbc->reCombine($subNetwork);
            $sql= "select DISTINCT category ,count(*) as num from ParaCheckBaseline t where templateId = $templateId and subNetwork in(".$subNetwork.")"." GROUP BY t.category ORDER BY t.category ";
            $rs = $dbn->query($sql,PDO::FETCH_OBJ);
            $rs = $rs->fetchAll();
            $series = $this->getHighChartSeries($rs,$city,$series,$categories);
        }
        $data['category'] = $categories;
        $data['series'] = array();
        foreach ($series as $key=>$value) {
            $data['series'][] = ['name'=>$key,'data'=>$value];
        }
        return $data;
    }
    /**
     * @desc ：获取表格信息
     * Time：2016/07/04 17:11:41
     * @author zhangyan
     * @param 参数类型
     * @return 返回值类型
    */
    function getParamItems(){
       $dbc = new DataBaseConnection();

        $db = Input::get('db');
        $table = Input::get('table');
        $paramLength = Input::get('paramLength');
        $dsn = "mysql:host=localhost;dbname=$db";
        $dbn = new PDO($dsn, 'root', 'mongs');
        $citys = Input::get('citys');
        $displayStart = Input::get('page');
        $displayLength = Input::get('limit');
        $offset = ($displayStart - 1)*$displayLength;
        $limit = '';
        $filter = '';
        $limit = " limit $offset,$displayLength ";
        $subNetwork = '';
        //dump($citys);
        if ($citys != '') {
            foreach ($citys as $city) {
                //dump($city);
                //$subNetwork .= $this->getSubNets($dbn, $city);
                $subNetwork .= $dbc->getSubNets($city).',';
                //dump($subNetwork);
            }
            $subNetwork = substr($subNetwork , 0 , -1);
        }
        if(isset($_REQUEST['templateId'])){
                $templateId = isset($_REQUEST['templateId']) ? $_REQUEST['templateId'] : '';
                if(trim($templateId) != '' && $table == 'ParaCheckBaseline'){
                    if($subNetwork != ''){
                        $filter=" where templateId='".$_REQUEST['templateId']."' and subNetwork in (".$subNetwork.")";
                    }
                    else{
                        $filter=" where templateId='".$_REQUEST['templateId']."'";
                    }
                }else{
                    if($subNetwork != ''){
                        $filter=" where subNetwork in (".$subNetwork.")";
                    }
                    
                }
            }else{
                if($subNetwork != ''){
                        $filter=" where subNetwork in (".$subNetwork.")";
                    }
                //$filter=" where subNetwork in (".$subNetwork.")";
            }
        $result = array();
        $sqlCount = "select count(*) from ".$table.$filter;
        $rs = $dbn->query($sqlCount,PDO::FETCH_ASSOC);
        $result["total"] = $rs->fetchColumn();
        $sql = "select * from $table $filter $limit";
        //dump($sql);
        $rs = $dbn->query($sql,PDO::FETCH_OBJ);
        $res = $rs->fetchAll();
        $items = array();
        if ($res) {
            foreach ($res as $row) {
                $row = $this->substring_paramData($row,$paramLength);
                //dump($row);
                //die;
                array_push($items, $row);
            }
            $result["records"] = $res;
        }
        return $result;
    }
    /**
      * @desc ：根据表格头宽截取数据
      * Time：2016/07/27 15:31:33
      * @author Wuyou
      * @param 参数类型
      * @return 返回值类型
       */
    function substring_paramData($row,$paramLength){
        $paramNames = array_keys($paramLength);
        foreach ($paramNames as $key => $value) {
            $length = $paramLength[$value];
            if ($row->$value != '') {
                $row->$value = substr($row->$value, 0,$length-1);
            }
        }
        return $row;
    }
    function baselineFile(){
        $dbc = new DataBaseConnection();
        $db = Input::get('db');
        $table = Input::get('table');
        $dsn = "mysql:host=localhost;dbname=$db";
        $dbn = new PDO($dsn, 'root', 'mongs');
        $citys = Input::get('citys');

        $filter = '';
        $subNetwork = '';
        if ($citys != '') {
            foreach ($citys as $city) {
                //$subNetwork .= $this->getSubNets($dbn, $city);
                $subNetwork .= $dbc->getSubNets($city).',';
            }
            $subNetwork = substr($subNetwork , 0 , -1);
        }
        if(isset($_REQUEST['templateId'])){
                $templateId = isset($_REQUEST['templateId']) ? $_REQUEST['templateId'] : '';
                if(trim($templateId) != '' && $table == 'ParaCheckBaseline'){
                    if($subNetwork != ''){
                        $filter=" where templateId='".$_REQUEST['templateId']."' and subNetwork in (".$subNetwork.")";
                    }
                    else{
                        $filter=" where templateId='".$_REQUEST['templateId']."'";
                    }
                }else{
                    if($subNetwork != ''){
                        $filter=" where subNetwork in (".$subNetwork.")";
                    }
                    
                }
            }else{
                if($subNetwork != ''){
                        $filter=" where subNetwork in (".$subNetwork.")";
                    }
                //$filter=" where subNetwork in (".$subNetwork.")";
            }
        $result = array();
        $fileContent = array();
        $csvContent = "";
        $sqlCount = "select count(*) from ".$table.$filter;
        $rs = $dbn->query($sqlCount,PDO::FETCH_ASSOC);
        $result["total"] = $rs->fetchColumn();
        //dump($result["total"]);
        $sql = "select * from $table $filter";
        $rs = $dbn->query($sql,PDO::FETCH_ASSOC);
        //$row = $rs->fetch();
        $rs = $rs->fetchAll();
        if ($rs) {
            $row = $rs[0];
        }else{
            return 'null';
        }
        $fieldArr = array_keys($row);
        $csvContent= implode(",",$fieldArr);
        $csvContent = mb_convert_encoding($csvContent, 'gbk', 'utf-8'); 
        $fileContent[] = $csvContent.",";
        
        foreach ($rs as $row) {
            $csvContent = "";
            foreach ($row as $column)
            {
              $column = trim($column);
              $column = str_replace(",", " ", $column);
              $csvContent = $csvContent.",".$column;
            }
            $csvContent = substr($csvContent, 1, strlen($csvContent) - 1);
            $csvContent = $csvContent.",";
            $csvContent = mb_convert_encoding($csvContent, 'gbk', 'utf-8');
            $fileContent[] = $csvContent; 
        }
        $filename = '';
        $filename = "files/".$table."_".date('YmdHis').".csv";
        $fp = fopen($filename,'w+');
        foreach ($fileContent as $line) {
            $lineArr = array();
            $lineArr = explode(',',$line);
            fputcsv($fp,$lineArr);
        }
        fclose($fp); 
        $result["result"] = 'true';
        $result["filename"] = $filename;
        return $result;
    }
    /**
     * @desc ：获取category值
     * Time：2016/08/10 09:35:50
     * @author Wuyou
     * @param 参数类型
     * @return 返回值类型
    */
    public function getHighChartCategory($rs){
        $categories = array();
        foreach ($rs as $item) {
          $category = $item->category;
           if (array_search($category,$categories) === false) {
          $categories[] = $category;
          }
        }
        return $categories;

    }
    /**
     * @desc ：获取series值
     * Time：2016/08/10 09:35:16
     * @author Wuyou
     * @param 参数类型
     * @return 返回值类型
    */
    public function getHighChartSeries($rs,$seriesKey,$series,$categories){
  
        $l = 0;
        $k = 0;
        foreach ($rs as $item) {
            $category = $item->category;
            $num = $item->num;
            for($i=$k;$i<count($categories);$i++){
                if ($category == $categories[$i]) {
                    if(!array_key_exists($seriesKey,$series)){
                        $series[$seriesKey] = array();
                    }
                    $series[$seriesKey][] = floatval($num);
                    $k = $i + 1;
                    $l++;
                    break;
                }else{
                    if(!array_key_exists($seriesKey,$series)){
                        $series[$seriesKey] = array();
                    }
                    $series[$seriesKey][] = floatval(0);
                    $l++;
                }
            }
        }
        if($l < count($categories)){
            for($i= $l ;$i<count($categories);$i++){
                if(!array_key_exists($seriesKey,$series)){
                    $series[$seriesKey] = array();
                }
                $series[$seriesKey][] = floatval(0);
            }
        }
        return $series;
    }
}
