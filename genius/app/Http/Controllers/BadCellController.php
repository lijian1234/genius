<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Requests;
use DateTime;
use DateInterval;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class BadCellController extends Controller
{

    /**
     * @param Request $request
     */
    public function getBadCellData(Request $request) {
        $date = new DateTime();
        $date->sub(new DateInterval('P1D'));
        $dayId = $date->format('Y-m-d');
        $sql = 'select city,count(city) as num,"badHandoverCell" as type from badHandoverCell_ex where day_id = "'.$dayId.'" group by city union select city,count(city) as num,"highLostCell" as type from highLostCell_ex where day_id = "'.$dayId.'" group by city union select city,count(city) as num,"lowAccessCell" as type from lowAccessCell_ex where day_id = "'.$dayId.'" group by city';  
        $result = DB::connection('autokpi')->select($sql);
        //$this->getHighChartData($result);
        //dd($result);
        return $this->getHighChartData($result);
    }
    
    public function getHighChartData($result){

        $series = array();
        $category = array();
        foreach($result as $item){
            $city = $item->city;
            $num = $item->num;
            $type = $item->type;
            if (array_search($city,$category) === false) {
                $category[] = $city;
            }

            if(!array_key_exists($type,$series)){
                $series[$type] = array();
            }
            $series[$type][] = floatval($num);
        }
        $data['category'] = $category;
        $data['series'] = array();
        foreach($series as $key=>$value) {
            $data['series'][] = ['name'=>$key,'data'=>$value];
        }
        return json_encode($data);
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
    $date = new DateTime();
    $date->sub(new DateInterval('P1D'));
    $dayId = $date->format('Y-m-d');
    $sql = 'select city,count(city) as num,"badHandoverCell" as type from badHandoverCell_ex where day_id = "'.$dayId.'" and city="'.$city.'" union select city,count(city) as num,"highLostCell" as type from highLostCell_ex where day_id = "'.$dayId.'" and city="'.$city.'" union select city,count(city) as num,"lowAccessCell" as type from lowAccessCell_ex where day_id = "'.$dayId.'" and city="'.$city.'"';
    $conn = DB::connection('autokpi');
    $rs = $conn->select($sql);
    $data = array();
    $badCellType_Data = array();
    foreach ($rs as $item) {
        $arr = array();
        $type = $item->type;
        $num = $item->num;
        $arr[$type] = floatval($num);
        foreach ($arr as $key => $value) {
          $badCellType_Data[] = ['name'=>$key,'y'=>$value];
        }
    }
    $data['badCellType_Data'] = $badCellType_Data;
    //dump($data);
    return json_encode($data);
  }

}
