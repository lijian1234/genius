<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use DateTime;
use DateInterval;
/**
 * @desc ：告警处理
 * Time：2016/07/20 15:39:12
 * @author Wuyou
 * @param 参数类型
 * @return 返回值类型
*/
class AlarmController extends Controller
{
  /**
   * @desc ：当前告警信息
   * Time：2016/07/20 15:49:35
   * @author Wuyou
   * @param 参数类型
   * @return 返回值类型
  */
  public function getCurrentAlarm(Request $request) {
    $sql_categoty = "select DISTINCT city as category from FMA_alarm_list where city is not null and city != '' order by city";
    $conn = DB::connection('alarm');
    $rs = $conn->select($sql_categoty);
    $categories = array();
    $categories = $this->getHighChartCategory($rs);
    $sql_key = "select Perceived_severity as type from FMA_alarm_list group by type order by type";
    $rs = $conn->select($sql_key);
    $series = array();
    //$drillDownSeries = array();
    foreach ($rs as $item) {
        $type = $item->type;
        $sql = "select city as category,
      case Perceived_severity
      when 1 then '1'
      when 2 then 'CRITICAL'
      when 3 then 'MAJOR'
      when 4 then 'MINOR'
      when 5 then 'WARNING'
      end as type
      ,
      count(*) as num from FMA_alarm_list where Perceived_severity='".$type."' and city is not null and city != '' group by city,Perceived_severity order by city";
      $rs = $conn->select($sql);
      $series = $this->getHighChartSeries($rs,$series,$categories,$type);
    }
    $data['category'] = $categories;
    $data['series'] = array();
    foreach ($series as $key=>$value) {
      $data['series'][] = ['name'=>$key,'data'=>$value];
    }
    return json_encode($data);
  }

  /**
   * @desc ：历史告警信息
   * Time：2016/07/20 15:49:35
   * @author Wuyou
   * @param 参数类型
   * @return 返回值类型
  */
  public function getHistoryAlarm(Request $request) {
    $date = new DateTime();
    $startDate = $date->sub(new DateInterval('P6D'));
    $startDate = $startDate->format('Y-m-d');
    $endDate = new DateTime();
    $endDate = $endDate->format('Y-m-d');
    //dump($startDate);
    //dump($endDate);
    //die;
    $sql_categoty = "select DISTINCT city as category from FMA_alarm_log where city is not null and city != '' order by city";
    $conn = DB::connection('alarm');
    $rs = $conn->select($sql_categoty);
    $categories = array();
    $categories = $this->getHighChartCategory($rs);
    $sql_key = "select Perceived_severity as type from FMA_alarm_log group by type order by type";
    $rs = $conn->select($sql_key);
    $series = array();
    //$drillDownSeries = array();
    foreach ($rs as $item) {
        $type = $item->type;
        $sql = "select city as category,
      case Perceived_severity
      when -1 then '-1'
      when 0 then '0'
      when 1 then '1'
      when 2 then 'CRITICAL'
      when 3 then 'MAJOR'
      when 4 then 'MINOR'
      when 5 then 'WARNING'
      end as type
      ,
      count(*) as num from FMA_alarm_log where Perceived_severity='".$type."' and city is not null and city != '' and (Event_time BETWEEN '".$startDate."' and '".$endDate."') group by city,Perceived_severity order by city";
      //dump($sql);
      //die;
      $rs = $conn->select($sql);
      //dump($rs);
      //die;
      
      $series = $this->getHighChartSeries($rs,$series,$categories,$type);
      // dump($series);
    }
    $data['category'] = $categories;
    $data['series'] = array();
    foreach ($series as $key=>$value) {
      $data['series'][] = ['name'=>$key,'data'=>$value];
    }
    return json_encode($data);
  }
  /**
   * @desc ：历史告警信息
   * Time：2016/07/20 15:49:55
   * @author Wuyou
   * @param 参数类型
   * @return 返回值类型
  */
  // public function getHistoryAlarm(Request $request) {
  //   $sql = 'select * from FMA_alarm_log_group_by_city_date;';
  //   $result = DB::connection('alarm')->select($sql);
  //   $series = array();
  //   foreach ($result as $item) {
  //     $city = $item->city;
  //     $date = $item->date;
  //     $alarm_num = $item->alarm_num;
  //     if (!array_key_exists($city, $series)) {
  //       $series[$city] = array();
  //     }
  //     $series[$city][] = [strtotime($date)*1000,$alarm_num];
  //   }
  //   foreach ($series as $key=>$value) {
  //     $data[] = ['name'=>$key,'data'=>$value];
  //   }
  //   return $data;
  // }
  /**
   * @desc ：
   * Time：2016/07/20 17:11:06
   * @author Wuyou
   * @param 参数类型
   * @return 返回值类型
  */
  public function getHighChartCategory($rs){
    $categories = array();
    foreach ($rs as $item) {
      $category = $item->category;
      if (array_search($category, $categories) === false) {
      $categories[] = $category;
      }
    }
    return $categories;

  }
  /**
   * @desc ：获取chart图形的 series数据
   * Time：2016/07/25 16:19:52
   * @author Wuyou
   * @param 参数类型
   * @return 返回值类型
  */
  public function getHighChartSeries($rs,$series,$categories,$type){
    if ($rs) {
      
    
    $l = 0;
    $k = 0;
    foreach ($rs as $item) {
    $category = $item->category;
    $num = $item->num;
    $seriesKey = $item->type;
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
  }else{
    for($i= 0 ;$i<count($categories);$i++){
      if(!array_key_exists($type,$series)){
      $series[$type] = array();
      }
      $series[$type][] = floatval(0);
    }
  }
    return $series;
  }
  /**
   * @desc ：当前告警柱状图 drilldown数据
   * Time：2016/07/25 09:14:03
   * @author Wuyou
   * @param 参数类型
   * @return 返回值类型
  */
  function getDrillDownDonutPie(){
    $city = Input::get('city');
    $sql = "select Perceived_severity as tempType,
    case Perceived_severity
    when 1 then '1'
    when 2 then 'CRITICAL'
    when 3 then 'MAJOR'
    when 4 then 'MINOR'
    when 5 then 'WARNING'
    end as type,
    count(*) as num from FMA_alarm_list where city='".$city."' group by Perceived_severity order by tempType";
    $conn = DB::connection('alarm');
    $rs = $conn->select($sql);
    $data = array();
    $perceived_severity_Data = array();
    $sp_text_data = array();
    foreach ($rs as $item) {
    $arr = array();
    $tempType = $item->tempType;
    $type = $item->type;
    $num = $item->num;
    $arr[$type] = floatval($num);
    foreach ($arr as $key => $value) {
      $perceived_severity_Data[] = ['name'=>$key,'y'=>$value];
    }
    $sp_test_sql = "select Perceived_severity as tempType,case Perceived_severity
      when 1 then '1'
      when 2 then 'CRITICAL'
      when 3 then 'MAJOR'
      when 4 then 'MINOR'
      when 5 then 'WARNING'
      end as type,SP_text,
      count(*) as num from FMA_alarm_list where city='".$city."' and Perceived_severity=".$tempType." group by SP_text";
    $rs_sp_text = $conn->select($sp_test_sql);
    foreach ($rs_sp_text as $item_sp) {
      $arr_sp = array();
      $type = $item_sp->type;
      $SP_text = $item_sp->SP_text;
      $num_sp = $item_sp->num;
      $arr_sp[$SP_text] = floatval($num_sp);
      foreach ($arr_sp as $key => $value) {
      $sp_text_data[$type][] = ['name'=>$key,'y'=>$value];
      }
      
    }
    }
    $data['perceived_severity'] = $perceived_severity_Data;
    $data['sp_text'] = $sp_text_data;
    //dump($data);
    return json_encode($data);
  }

  /**
   * @desc ：历史告警柱状图 drilldown数据
   * Time：2016/07/25 09:14:03
   * @author Wuyou
   * @param 参数类型
   * @return 返回值类型
  */
  function getHistoryDrillDownDonutPie(){
    $date = new DateTime();
    $startDate = $date->sub(new DateInterval('P6D'));
    $startDate = $startDate->format('Y-m-d');
    $endDate = new DateTime();
    $endDate = $endDate->format('Y-m-d');
    $city = Input::get('city');
    $sql = "select Perceived_severity as tempType,
    case Perceived_severity
    when -1 then '-1'
    when 0 then '0'
    when 1 then '1'
    when 2 then 'CRITICAL'
    when 3 then 'MAJOR'
    when 4 then 'MINOR'
    when 5 then 'WARNING'
    end as type,
    count(*) as num from FMA_alarm_log where city='".$city."' and (Event_time BETWEEN '".$startDate."' and '".$endDate."') group by Perceived_severity order by tempType";
    $conn = DB::connection('alarm');
    $rs = $conn->select($sql);
    $data = array();
    $perceived_severity_Data = array();
    $sp_text_data = array();
    foreach ($rs as $item) {
    $arr = array();
    $tempType = $item->tempType;
    $type = $item->type;
    $num = $item->num;
    $arr[$type] = floatval($num);
    foreach ($arr as $key => $value) {
      $perceived_severity_Data[] = ['name'=>$key,'y'=>$value];
    }
    $sp_test_sql = "select Perceived_severity as tempType,case Perceived_severity
      when -1 then '-1'
      when 0 then '0'
      when 1 then '1'
      when 2 then 'CRITICAL'
      when 3 then 'MAJOR'
      when 4 then 'MINOR'
      when 5 then 'WARNING'
      end as type,SP_text,
      count(*) as num from FMA_alarm_log where city='".$city."' and Perceived_severity=".$tempType." and (Event_time BETWEEN '".$startDate."' and '".$endDate."') group by SP_text";
    $rs_sp_text = $conn->select($sp_test_sql);
    foreach ($rs_sp_text as $item_sp) {
      $arr_sp = array();
      $type = $item_sp->type;
      $SP_text = $item_sp->SP_text;
      $num_sp = $item_sp->num;
      $arr_sp[$SP_text] = floatval($num_sp);
      foreach ($arr_sp as $key => $value) {
      $sp_text_data[$type][] = ['name'=>$key,'y'=>$value];
      }
      
    }
    }
    $data['perceived_severity'] = $perceived_severity_Data;
    $data['sp_text'] = $sp_text_data;
    //dump($data);
    return json_encode($data);
  }
}
