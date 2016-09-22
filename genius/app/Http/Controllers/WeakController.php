<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;

use App\Http\Controllers\Common\DataBaseConnection;
use Illuminate\Http\Request;
use App\Http\Requests;
use DateTime;
use DateInterval;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use PDO;
/**
 * @desc ：短板概览
 * Time：2016/07/22 09:47:46
 * @author Wuyou
 * @param 参数类型
 * @return 返回值类型
*/
class WeakController extends Controller
{
    /**
     * @desc ：baseline检查-参数数量分布数据
     * Time：2016/07/22 10:00:08
     * @author Wuyou
     * @param 参数类型
     * @return 返回值类型
    */
    public function getBaselineParamNum(){
        $date = new DateTime();
        $date->sub(new DateInterval('P1D'));
        $yesDate = $date->format('ymd');
        $db = 'kget'.$yesDate;
        //$db = 'kget160612';
        $dsn = "mysql:host=localhost;dbname=$db";
        $dbn = new PDO($dsn, 'root', 'mongs');
        //$rs_city = $this->getCity();
        //$res = DB::select('select connName,subNetwork from databaseconn ');
        $dbc = new DataBaseConnection();
        $rs_city = $dbc->getCityCategories();
        $categories = $this->getHighChartCategory($rs_city);
        $res = $dbc->getCity_subNetCategories();
        $series = array();
        foreach ($res as $items) {
            $arr = array();
            $city = $items->connName;
            $subNetwork = $items->subNetwork;
            $subNetwork = $dbc->reCombine($subNetwork);
            $sql = "select count(id) num from ParaCheckBaseline where (category = 'A' or category = 'M') and subNetwork in (".$subNetwork.")";
            //dump($sql);
            $rs = $dbn->query($sql,PDO::FETCH_OBJ);
            $rs = $rs->fetchColumn();
            array_push($arr, $city);
            array_push($arr, floatval($rs));
            $series[] = $arr;
        }
        $data['category'] = $categories;
        $data['series'] = array();
        $data['series'][] = ['name'=>'city','data'=>$series];
        //dump($data);
        return json_encode($data);
    }
    /**
     * @desc ：baseline检查-基站数量分布数据
     * Time：2016/07/22 11:21:01
     * @author Wuyou
     * @param 参数类型
     * @return 返回值类型
    */
    public function getBaselineBSNum(){
        $date = new DateTime();
        $date->sub(new DateInterval('P1D'));
        $yesDate = $date->format('ymd');
        $db = 'kget'.$yesDate;
        //$db = 'kget160612';
        $dsn = "mysql:host=localhost;dbname=$db";
        $dbn = new PDO($dsn, 'root', 'mongs');
        //$rs_city = $this->getCity();
        //$categories = $this->getHighChartCategory($rs_city);
        //$res = DB::select('select connName,subNetwork from databaseconn ');
        $dbc = new DataBaseConnection();
        $rs_city = $dbc->getCityCategories();
        $categories = $this->getHighChartCategory($rs_city);
        $res = $dbc->getCity_subNetCategories();
        $series = array();
        foreach ($res as $items) {
            $arr = array();
            $city = $items->connName;
            $subNetwork = $items->subNetwork;
            $subNetwork = $this->reCombine($subNetwork);
            $sql = "select count(distinct meContext) num from ParaCheckBaseline where (category = 'A' or category = 'M') and subNetwork in (".$subNetwork.")";
            //dump($sql);
            $rs = $dbn->query($sql,PDO::FETCH_OBJ);
            $rs = $rs->fetchColumn();
            array_push($arr, $city);
            array_push($arr, floatval($rs));
            $series[] = $arr;
        }
        $data['category'] = $categories;
        $data['series'] = array();
        $data['series'][] = ['name'=>'city','data'=>$series];
        //dump($data);
        return json_encode($data);
    }
    /**
     * @desc ：一致性检查-基站数量分布数据
     * Time：2016/07/22 11:21:01
     * @author Wuyou
     * @param 参数类型
     * @return 返回值类型
    */
    public function getConsistencyParamNum(){
        $date = new DateTime();
        $date->sub(new DateInterval('P1D'));
        $yesDate = $date->format('ymd');
        $db = 'kget'.$yesDate;
        //$db = 'kget160612';
        $dsn = "mysql:host=localhost;dbname=$db";
        $dbn = new PDO($dsn, 'root', 'mongs');
        //$rs_city = $this->getCity();
        //$categories = $this->getHighChartCategory($rs_city);
        //$res = DB::select('select connName,subNetwork from databaseconn ');
        $dbc = new DataBaseConnection();
        $rs_city = $dbc->getCityCategories();
        $categories = $this->getHighChartCategory($rs_city);
        $res = $dbc->getCity_subNetCategories();
        $series = array();
        foreach ($res as $items) {
            $arr = array();
            $city = $items->connName;
            $subNetwork = $items->subNetwork;
            $subNetwork = $this->reCombine($subNetwork);
            $sql = "select sum(num) from (select count(*) num from TempEUtranCellFreqRelation where subNetwork in (".$subNetwork.") union all
select count(*) num from TempEUtranCellRelationUnidirectionalNeighborCell where subNetwork in (".$subNetwork.") union all
select count(*) num from TempEUtranCellRelationExistNeighborCellWithoutX2 where subNetwork in (".$subNetwork.") union
select count(*) num from TempEUtranCellRelationManyNeighborCell where subNetwork in (".$subNetwork.") union all
select count(*) num from TempEUtranCellRelationFewNeighborCell where subNetwork in (".$subNetwork.") union all
select count(*) num from TempExternalEUtranCellTDDActivePlmnListCheck where subNetwork in (".$subNetwork.") union all
select count(*) num from TempEUtranCellRelationNeighOfPci where subNetwork in (".$subNetwork.") union all
select count(*) num from TempEUtranCellRelationNeighOfNeighPci where subNetwork in (".$subNetwork.") union all
select count(*) num from TempGeranCellRelation2GNeighbor where subNetwork in (".$subNetwork.") union all
select count(*) num from TempParameter2GKgetCompare where subNetwork in (".$subNetwork.") union all
select count(*) num from TempExternalNeigh4G where subNetwork in (".$subNetwork.") union all
select count(*) num from TempParameterQCI_A1A2 where subNetwork in (".$subNetwork.") union all
select count(*) num from TempParameterQCI_B2A2critical where subNetwork in (".$subNetwork.") union all
select count(*) num from TempTermPointToENB_ENBID_ipAddress where subNetwork in (".$subNetwork.") union all
select count(*) num from TempTermPointToENB_ENBID_usedIpAddress where subNetwork in (".$subNetwork.") union all
select count(*) num from TempTermPointToENB_IP where subNetwork in (".$subNetwork.") union all
select count(*) num from TempTermPointToENB_X2Status where subNetwork in (".$subNetwork.") union all
select count(*) num from TempTermPointToMme_S1_MMEGI where subNetwork in (".$subNetwork.") union all
select count(*) num from TempTermPointToMme_S1_MME where subNetwork in (".$subNetwork.") union all
select count(*) num from TempTermPointToMme_S1_tac where subNetwork in (".$subNetwork.")
) t";
            $rs = $dbn->query($sql,PDO::FETCH_OBJ);
            $rs = $rs->fetchColumn();
            array_push($arr, $city);
            array_push($arr, floatval($rs));
            $series[] = $arr;
        }
        $data['category'] = $categories;
        $data['series'] = array();
        $data['series'][] = ['name'=>'city','data'=>$series];
        //dump($data);
        return json_encode($data);
    }
    /**
     * @desc ：一致性检查-基站数量分布数据
     * Time：2016/07/22 11:21:01
     * @author Wuyou
     * @param 参数类型
     * @return 返回值类型
    */
    public function getConsistencyBSNum(){
        $date = new DateTime();
        $date->sub(new DateInterval('P1D'));
        $yesDate = $date->format('ymd');
        $db = 'kget'.$yesDate;
        //$db = 'kget160612';
        $dsn = "mysql:host=localhost;dbname=$db";
        $dbn = new PDO($dsn, 'root', 'mongs');
        //$rs_city = $this->getCity();
        //$categories = $this->getHighChartCategory($rs_city);
        //$res = DB::select('select connName,subNetwork from databaseconn ');
        $dbc = new DataBaseConnection();
        $rs_city = $dbc->getCityCategories();
        $categories = $this->getHighChartCategory($rs_city);
        $res = $dbc->getCity_subNetCategories();
        $series = array();
        foreach ($res as $items) {
            $arr = array();
            $city = $items->connName;
            $subNetwork = $items->subNetwork;
            $subNetwork = $this->reCombine($subNetwork);
            $sql = "select sum(num) from (select count(distinct meContext) num from TempEUtranCellFreqRelation where subNetwork in (".$subNetwork.") union all
select count(distinct meContext) num from TempEUtranCellRelationUnidirectionalNeighborCell where subNetwork in (".$subNetwork.") union all
select count(distinct meContext) num from TempEUtranCellRelationExistNeighborCellWithoutX2 where subNetwork in (".$subNetwork.") union
select count(distinct meContext) num from TempEUtranCellRelationManyNeighborCell where subNetwork in (".$subNetwork.") union all
select count(distinct meContext) num from TempEUtranCellRelationFewNeighborCell where subNetwork in (".$subNetwork.") union all
select count(distinct meContext) num from TempExternalEUtranCellTDDActivePlmnListCheck where subNetwork in (".$subNetwork.") union all
select count(distinct meContext) num from TempEUtranCellRelationNeighOfPci where subNetwork in (".$subNetwork.") union all
select count(distinct meContext) num from TempEUtranCellRelationNeighOfNeighPci where subNetwork in (".$subNetwork.") union all
select count(distinct meContext) num from TempGeranCellRelation2GNeighbor where subNetwork in (".$subNetwork.") union all
select count(distinct meContext) num from TempParameter2GKgetCompare where subNetwork in (".$subNetwork.") union all
select count(distinct meContext) num from TempExternalNeigh4G where subNetwork in (".$subNetwork.") union all
select count(distinct meContext) num from TempParameterQCI_A1A2 where subNetwork in (".$subNetwork.") union all
select count(distinct meContext) num from TempParameterQCI_B2A2critical where subNetwork in (".$subNetwork.") union all
select count(distinct meContext) num from TempTermPointToENB_ENBID_ipAddress where subNetwork in (".$subNetwork.") union all
select count(distinct meContext) num from TempTermPointToENB_ENBID_usedIpAddress where subNetwork in (".$subNetwork.") union all
select count(distinct meContext) num from TempTermPointToENB_IP where subNetwork in (".$subNetwork.") union all
select count(distinct meContext) num from TempTermPointToENB_X2Status where subNetwork in (".$subNetwork.") union all
select count(distinct meContext) num from TempTermPointToMme_S1_MMEGI where subNetwork in (".$subNetwork.") union all
select count(distinct meContext) num from TempTermPointToMme_S1_MME where subNetwork in (".$subNetwork.") union all
select count(distinct meContext) num from TempTermPointToMme_S1_tac where subNetwork in (".$subNetwork.")
) t";
            $rs = $dbn->query($sql,PDO::FETCH_OBJ);
            $rs = $rs->fetchColumn();
            array_push($arr, $city);
            array_push($arr, floatval($rs));
            $series[] = $arr;
        }
        $data['category'] = $categories;
        $data['series'] = array();
        $data['series'][] = ['name'=>'city','data'=>$series];
        //dump($data);
        return json_encode($data);
    }
    /**
     * @desc ：获取所有城市
     * Time：2016/07/22 10:05:20
     * @author Wuyou
     * @param 参数类型
     * @return 返回值类型
    */
    /*public function getCity(){
        $sql = "select connName category from databaseconn";
        $rs = DB::select($sql);
        return $rs;
    }*/
    /**
     * @desc ：highcharts图形的category
     * Time：2016/07/22 10:17:05
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
     * @desc :子网拼接 in 查询语句
     * Time:2016/07/01 18:40:04
     * @author Wuyou
     * @param 
     * @return 
    */
    protected function reCombine($subNetwork){

        $subNetArr = explode(",", $subNetwork);
        $subNetsStr = '';
        foreach ($subNetArr as $subNet) {
            $subNetsStr.= "'".$subNet."',";
        }

        return substr($subNetsStr,0,-1);
    }
    /**
     * @desc ：highcharts图形的series
     * Time：2016/07/22 10:20:08
     * @author Wuyou
     * @param 参数类型
     * @return 返回值类型
    */
    public function getHighChartSeries($rs,$city,$series,$categories){
        $l = 0;
        $k = 0;
        foreach ($rs as $item) {
            $category = $item->category;
            $num = $item->num;
            $arr = array();
            for ($i=$k; $i < count($categories); $i++) { 
                if ($category == $categories[$i]) {
                    $arr[] = $city;
                    $arr[] = floatval($num);
                    $series[] = $arr;
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
