<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Common\DataBaseConnection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\Http\Requests;
use DateTime;
use DateInterval;
use PDO;
class NetworkScaleController extends Controller
{
  /**
   * @desc ：各地市服务器连接对象
  */
  private $dbc;
  /**
   * @desc ：
   * Time：2016/08/04 13:49:25
   * @author Wuyou
   * @param 参数类型
   * @return 返回值类型
  */
  public function index(){
  return view('network.scale');
  }
  /**
   * @desc :基站版本-类型
   * Time:2016/07/07 13:32:19
   * @author Wuyou
   * @param 
   * @return
  */
  public function getBSCversionByType(){
  $date = new DateTime();
  $date->sub(new DateInterval('P1D'));
  $yesDate = $date->format('ymd');
  $db = 'kget'.$yesDate;
  //$db = 'kget160612';
  $dsn = "mysql:host=localhost;dbname=$db";
  $dbn = new PDO($dsn, 'root', 'mongs');
  $sql_category = "select softwareVersion as category from TempSiteVersion where softwareVersion is not null and softwareVersion !='' and softwareVersion !='!!!!' group by softwareVersion";
  $rs = $dbn->query($sql_category,PDO::FETCH_OBJ);
  $res = $rs->fetchAll();
  $categories = array();
  $series = array();
  $categories = $this->getHighChartCategory($res);
  $sql_key = "select siteType from TempSiteVersion where siteType is not null group by siteType
    ";
    $rs = $dbn->query($sql_key,PDO::FETCH_OBJ);
  $rs = $rs->fetchAll();    
  foreach ($rs as $item) {
    $siteType = $item->siteType;
    $sql = "select count(*) as num,softwareVersion as category from TempSiteVersion where siteType = '$siteType' and softwareVersion is not null and softwareVersion !='' and softwareVersion !='!!!!' group by siteType,softwareVersion order by softwareVersion";
    $rs = $dbn->query($sql,PDO::FETCH_OBJ);
    $rs = $rs->fetchAll();
    $series = $this->getHighChartSeries($rs,$siteType,$series,$categories);
  }
  
   $data['category'] = $categories;
  $data['series'] = array();
  foreach ($series as $key=>$value) {
    $data['series'][] = ['name'=>$key,'data'=>$value];
  }
  return json_encode($data);

  }
  /**
   * @desc :基站版本-城市
   * Time:2016/07/07 13:32:19
   * @author Wuyou
   * @param 
   * @return 
  */
  public function getBSCversionByCity(){
    $dbc = new DataBaseConnection();
  $date = new DateTime();
  $date->sub(new DateInterval('P1D'));
  $yesDate = $date->format('ymd');
  $db = 'kget'.$yesDate;
  //$db = 'kget160612';
  $dsn = "mysql:host=localhost;dbname=$db";
  $dbn = new PDO($dsn, 'root', 'mongs');
  
  $sql_category = "select left(UP_CompatibilityIndex,4) as category from UpgradePackage where UpgradePackageId in (select substring_index(currentUpgradePackage,'=',-1) from ConfigurationVersion) and left(UP_CompatibilityIndex,4) != '!!!!' and left(UP_CompatibilityIndex,4) != '' group by left(UP_CompatibilityIndex,4) order by left(UP_CompatibilityIndex,4)";
  $rs = $dbn->query($sql_category,PDO::FETCH_OBJ);
  $rs = $rs->fetchAll();
  $categories = array();
  $categories = $this->getHighChartCategory($rs);
  //$res = DB::select('select connName,subNetwork from databaseconn ');
  $res = $dbc->getCity_subNetCategories();
  $series = array();
  foreach ($res as $items) {
    $city = $items->connName;
    $subNetwork = $items->subNetwork;
    $subNetwork = $dbc->reCombine($subNetwork);
    //dump($subNetwork);
    $sql = "select left(UP_CompatibilityIndex,4) as category,count(UP_CompatibilityIndex) as num from ((select subNetwork,substring_index(currentUpgradePackage,'=',-1)  currentUpgradePackage from ConfigurationVersion where subNetwork in(".$subNetwork.") )t left join 
    (select UpgradePackageId,UP_CompatibilityIndex from UpgradePackage group by UpgradePackageId)t1
    on t.currentUpgradePackage = t1.UpgradePackageId) where left(UP_CompatibilityIndex,4) != '!!!!' and left(UP_CompatibilityIndex,4) != '' GROUP BY left(UP_CompatibilityIndex,4)";
    //dump($sql);
    $rs = $dbn->query($sql,PDO::FETCH_OBJ);
    $rs = $rs->fetchAll();
    $series = $this->getHighChartSeries($rs,$city,$series,$categories);
  }
  $data['category'] = $categories;
  $data['series'] = array();
  foreach ($series as $key=>$value) {
    $data['series'][] = ['name'=>$key,'data'=>$value];
  }
  return json_encode($data);
  }
/**
 * @desc :基于覆盖维度
 * Time:2016/07/07 15:01:47
 * @author Wuyou
 * @param 
 * @return 
*/
  function getBSCSiteType(){
  $date = new DateTime();
  $date->sub(new DateInterval('P1D'));
  $yesDate = $date->format('ymd');
  $db = 'kget'.$yesDate;
  //$db = 'kget160612';
  $dsn = "mysql:host=localhost;dbname=$db";
  $dbn = new PDO($dsn, 'root', 'mongs');
  $sql = 'SELECT count(distinct meContext) num ,siteType as type FROM TempSiteType group by siteType';
  $rs = $dbn->query($sql,PDO::FETCH_OBJ);
  $rs = $rs->fetchAll();
  return $this->getHighChartPieData($rs);
  }
  /**
   * @desc :基于载波维度
   * Time:2016/07/08 09:24:03
   * @author Wuyou
   * @param 
   * @return 
  */
  function getBSCSlave(){
  $date = new DateTime();
  $date->sub(new DateInterval('P1D'));
  $yesDate = $date->format('ymd');
  $db = 'kget'.$yesDate;
  //$db = 'kget160612';
  $dsn = "mysql:host=localhost;dbname=$db";
  $dbn = new PDO($dsn, 'root', 'mongs');
  $sql = 'SELECT count(distinct meContext) num,carriesCount as type FROM TempParameterRRUAndSlaveCount where carriesCount is not null group by carriesCount';
  $rs = $dbn->query($sql,PDO::FETCH_OBJ);
  $rs = $rs->fetchAll();
  return $this->getHighChartPieData($rs);
  }
  /**
   * @desc :基于CA维度
   * Time:2016/07/08 09:23:43
   * @author Wuyou
   * @param 
   * @return 
  */
  function getBSCCA(){
  $date = new DateTime();
  $date->sub(new DateInterval('P1D'));
  $yesDate = $date->format('ymd');
  $db = 'kget'.$yesDate;
  //$db = 'kget160612';
  $dsn = "mysql:host=localhost;dbname=$db";
  $dbn = new PDO($dsn, 'root', 'mongs');
  $sql = 'select count(DISTINCT meContext) num,"CA" AS type from OptionalFeatureLicense where OptionalFeatureLicenseId = "CarrierAggregation" 
and serviceState = 1 
union 
select count(DISTINCT meContext) num ,"非CA" AS type from OptionalFeatureLicense where OptionalFeatureLicenseId != "CarrierAggregation" 
and serviceState != 1';
  $rs = $dbn->query($sql,PDO::FETCH_OBJ);
  $rs = $rs->fetchAll();
  return $this->getHighChartPieData($rs);
  }
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

   public function getHighChartPieData($result){
  $series = array();
  $category = array();
  foreach ($result as $item) {
    $num = $item->num;
    $type = $item->type;
    $series[$type] = floatval($num);
  }
  $data['series'] = array();
  foreach ($series as $key=>$value) {
    $data['series'][] = ['name'=>$key,'y'=>$value];
  }
  return json_encode($data);
   }
   /**
  * @desc ：获取每个城市的子网组
  * Time：2016/07/15 15:06:08
  * @author Wuyou
  * @param 参数类型
  * @return 返回值类型
   */
  /* protected function getSubNetworkByCity($city){
    $res = DB::select('select subNetwork from databaseconn where cityChinese = "'.$city.'"');
    $subNetwork = $res[0]->subNetwork;
    return $subNetwork;
   }*/
   /**
 * @desc :子网拼接 in 查询语句
 * Time:2016/07/01 18:40:04
 * @author Wuyou
 * @param 
 * @return 
*/
  protected function reCombine($subNetwork)
  {

  $subNetArr = explode(",", $subNetwork);
  $subNetsStr = '';
  foreach ($subNetArr as $subNet) {
    $subNetsStr.= "'".$subNet."',";
  }

  return substr($subNetsStr,0,-1);
  }
  /**
   * @desc ：基站数量
   * Time：2016/07/15 14:26:12
   * @author Wuyou
   * @param 参数类型
   * @return 返回值类型
  */
  public function getMeContextNum(){
  $date = new DateTime();
  $date->sub(new DateInterval('P1D'));
  $yesDate = $date->format('ymd');
  $db = 'kget'.$yesDate;
  //$db = 'kget160612';
  $dsn = "mysql:host=localhost;dbname=$db";
  $dbn = new PDO($dsn, 'root', 'mongs');
  $sql = "select count(DISTINCT meContext) from ENodeBFunction";
  $rs = $dbn->query($sql,PDO::FETCH_ASSOC);
  $rs = $rs->fetchColumn();
  //dump($rs);
  return $rs;
  }
  /**
   * @desc ：小区数量
   * Time：2016/07/15 14:26:23
   * @author Wuyou
   * @param 参数类型
   * @return 返回值类型
  */
  public function getCellNum(){
  $date = new DateTime();
  $date->sub(new DateInterval('P1D'));
  $yesDate = $date->format('ymd');
  $db = 'kget'.$yesDate;
  //$db = 'kget160612';
  $dsn = "mysql:host=localhost;dbname=$db";
  $dbn = new PDO($dsn, 'root', 'mongs');
  $sql = "select count(DISTINCT EUtranCellTDDId) from EUtranCellTDD";
  $rs = $dbn->query($sql,PDO::FETCH_ASSOC);
  $rs = $rs->fetchColumn();
  //dump($rs);
  return $rs;
  }
  /**
   * @desc ：载频数量
   * Time：2016/07/15 14:26:30
   * @author Wuyou
   * @param 参数类型
   * @return 返回值类型
  */
  public function getSlaveNum(){
  $date = new DateTime();
  $date->sub(new DateInterval('P1D'));
  $yesDate = $date->format('ymd');
  $db = 'kget'.$yesDate;
  //$db = 'kget160612';
  $dsn = "mysql:host=localhost;dbname=$db";
  $dbn = new PDO($dsn, 'root', 'mongs');
  $sql = "select sum(sectorCarrierRef) from TempParameterRRUAndSlaveCount";
  $rs = $dbn->query($sql,PDO::FETCH_ASSOC);
  $rs = $rs->fetchColumn();
  return $rs;
  }
  /**
   * @desc ：基站数量-各地市
   * Time：2016/07/15 14:26:12
   * @author Wuyou
   * @param 参数类型
   * @return 返回值类型
  */
  public function getMeContextNumByCity(){
    $dbc = new DataBaseConnection();
  $date = new DateTime();
  $date->sub(new DateInterval('P1D'));
  $yesDate = $date->format('ymd');
  $db = 'kget'.$yesDate;
  //$db = 'kget160612';
  $dsn = "mysql:host=localhost;dbname=$db";
  $dbn = new PDO($dsn, 'root', 'mongs');
  $city = Input::get('city');
  $city = mb_substr( $city,0,2,'UTF-8');
  //$subNetwork = $this->getSubNetworkByCity($city);
  //$subNetwork = $this->reCombine( $subNetwork);
  $subNetwork = $dbc->getSubNets($city);
  $sql = "select count(DISTINCT meContext) from ENodeBFunction where subNetwork in (".$subNetwork.")";
  $rs = $dbn->query($sql,PDO::FETCH_ASSOC);
  $rs = $rs->fetchColumn();
  return $rs;
  }
  /**
   * @desc ：小区数量-各地市
   * Time：2016/07/15 14:26:23
   * @author Wuyou
   * @param 参数类型
   * @return 返回值类型
  */
  public function getCellNumByCity(){
  $date = new DateTime();
  $date->sub(new DateInterval('P1D'));
  $yesDate = $date->format('ymd');
  $db = 'kget'.$yesDate;
  //$db = 'kget160612';
  $dsn = "mysql:host=localhost;dbname=$db";
  $dbn = new PDO($dsn, 'root', 'mongs');
  $city = Input::get('city');
  $city = mb_substr( $city,0,2,'UTF-8');
  $dbc = new DataBaseConnection();
  $subNetwork = $dbc->getSubNets($city);
  //$subNetwork = $this->getSubNetworkByCity($city);
  //$subNetwork = $this->reCombine( $subNetwork);
  $sql = "select count(DISTINCT EUtranCellTDDId) from EUtranCellTDD where subNetwork in (".$subNetwork.")";
  $rs = $dbn->query($sql,PDO::FETCH_ASSOC);
  $rs = $rs->fetchColumn();
  //dump($rs);
  return $rs;
  }
  /**
   * @desc ：载频数量-各地市
   * Time：2016/07/15 14:26:30
   * @author Wuyou
   * @param 参数类型
   * @return 返回值类型
  */
  public function getSlaveNumByCity(){
  $date = new DateTime();
  $date->sub(new DateInterval('P1D'));
  $yesDate = $date->format('ymd');
  $db = 'kget'.$yesDate;
  //$db = 'kget160612';
  $dsn = "mysql:host=localhost;dbname=$db";
  $dbn = new PDO($dsn, 'root', 'mongs');
  $city = Input::get('city');
  $city = mb_substr( $city,0,2,'UTF-8');
  $dbc = new DataBaseConnection();
  $subNetwork = $dbc->getSubNets($city);
  //$subNetwork = $this->getSubNetworkByCity($city);
  //$subNetwork = $this->reCombine( $subNetwork);
  $sql = "select sum(sectorCarrierRef) from TempParameterRRUAndSlaveCount where subNetwork in (".$subNetwork.")";
  $rs = $dbn->query($sql,PDO::FETCH_ASSOC);
  $rs = $rs->fetchColumn();
  if ($rs) {
    return $rs;
  }else{
    return '0';
  }
  
  }
/**
 * @desc ：载频分布-基于城市分布
 * Time：2016/07/15 14:27:15
 * @author Wuyou
 * @param 参数类型
 * @return 返回值类型
*/
  public function getRRUAndSlaveByCity(){
    $dbc = new DataBaseConnection();
  $date = new DateTime();
  $date->sub(new DateInterval('P1D'));
  $yesDate = $date->format('ymd');
  $db = 'kget'.$yesDate;
  //$db = 'kget160612';
  $dsn = "mysql:host=localhost;dbname=$db";
  $dbn = new PDO($dsn, 'root', 'mongs');
  
  $sql_category = "select band as category FROM TempParameterRRUAndSlaveCount where band is not null GROUP BY band";
  $rs = $dbn->query($sql_category,PDO::FETCH_OBJ);
  $rs = $rs->fetchAll();
  $categories = array();
  $categories = $this->getHighChartCategory($rs);
  //$res = DB::select('select connName,subNetwork from databaseconn ');
  $res = $dbc->getCity_subNetCategories();
  $series = array();
  foreach ($res as $items) {
  $city = $items->connName;
  $subNetwork = $items->subNetwork;
  $subNetwork = $dbc->reCombine($subNetwork);
  $sql = "select band as category, sum(sectorCarrierRef) num FROM TempParameterRRUAndSlaveCount where subNetwork in (".$subNetwork.") and band is not null GROUP BY band";
  $rs = $dbn->query($sql,PDO::FETCH_OBJ);
  $rs = $rs->fetchAll();
  $series = $this->getHighChartSeries($rs,$city,$series,$categories);
  }
  $data['category'] = $categories;
  $data['series'] = array();
  foreach ($series as $key=>$value) {
    $data['series'][] = ['name'=>$key,'data'=>$value];
  }
  return json_encode($data);

  }
  /**
   * @desc ：载频分布-基于频点分布
   * Time：2016/07/15 14:27:30
   * @author Wuyou
   * @param 参数类型
   * @return 返回值类型
  */
  public function getRRUAndSlaveBySlave(){
  $date = new DateTime();
  $date->sub(new DateInterval('P1D'));
  $yesDate = $date->format('ymd');
  $db = 'kget'.$yesDate;
  //$db = 'kget160612';
  $dsn = "mysql:host=localhost;dbname=$db";
  $dbn = new PDO($dsn, 'root', 'mongs');
  
  $sql = "select band as category, sum(sectorCarrierRef) num FROM  TempParameterRRUAndSlaveCount where band is not null GROUP BY band";
  //dump($sql);
  $rs = $dbn->query($sql,PDO::FETCH_OBJ);
  $rs = $rs->fetchAll();
   
  $categories = array();
  $series = array();
  foreach ($rs as $item) {
  $category = $item->category;
  $num = $item->num;
  $categories[] = $category;
  $series[''][] = floatval($num);
  }
  $data['category'] = $categories;
  $data['series'] =  array();
  foreach ($series as $key=>$value) {
  $data['series'][] = ['name'=>$key,'data'=>$value];
  }
  return json_encode($data);
  }
  /**
   * @desc ：从AutoKPI 上获取的(最大用户数，上行业务量，下行业务量，CSFB次数)
   * Time：2016/07/15 15:34:44
   * @author Wuyou
   * @param 参数类型
   * @return 返回值类型
  */
  public function getNumOnAutoKPI(){
  $date = new DateTime();
  $date = $date->sub(new DateInterval('P1D'));
  $day_id = $date->format('Y-m-d');
  $sql = "select sum(`最大RRC连接用户数`) maxUser, sum(`空口上行业务量GB`) upTraffic, sum(`空口下行业务量GB`) downTraffic, sum(`语音回落到GSM次数`) csfbCount from SysCoreTemp_city_day where day_id = '".$day_id."'";
  $result = DB::connection('autokpi')->select($sql);
  //dump($result);
  return json_encode($result[0]);
  
  }
  /**
   * @desc ：根据 城市 从AutoKPI 上获取的(最大用户数，上行业务量，下行业务量，CSFB次数) 
   * Time：2016/07/15 15:34:44
   * @author Wuyou
   * @param 参数类型
   * @return 返回值类型
  */
  public function getNumOnAutoKPIByCity(){
    $dbc = new DataBaseConnection();
  $date = new DateTime();
  $date = $date->sub(new DateInterval('P1D'));
  $day_id = $date->format('Y-m-d');
  $city = Input::get('city');
  $city = mb_substr( $city,0,2,'UTF-8');
  //$sql = "select connName from databaseconn where cityChinese = '".$city."'";
  //$rs = DB::select($sql);
  $rs = $dbc->getCityByCityChinese($city);
  $city = $rs[0]->connName;
  $sql = "select sum(`最大RRC连接用户数`) maxUser, sum(`空口上行业务量GB`) upTraffic, sum(`空口下行业务量GB`) downTraffic, sum(`语音回落到GSM次数`) csfbCount from SysCoreTemp_city_day where day_id = '".$day_id."' and city = '".$city."'";
  $result = DB::connection('autokpi')->select($sql);
  return json_encode($result[0]);
  }
}
