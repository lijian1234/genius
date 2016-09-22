<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use DateTime;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use PDO;

class NetworkChartsController extends Controller
{
    /** @var string  */
    private $datetime_id = 'concat(day_id,\' \', concat(if(hour_id>=10,hour_id,concat(0,hour_id)),\':00:00\'))';
    private $datetime_id1 = 'concat(DateId,\' \', concat(if(HourId>=10,HourId,concat(0,HourId)),\':00:00\'))';
    /**
     * @param Request $request
     */

    public function getVideoGauge(){
        $cityChinese = Input::get('city');
        if($cityChinese == 'province'){
            $dsn = "mysql:host=localhost;dbname=nbi"; 
            $db = new PDO($dsn, 'root', 'mongs');
            $sql =  "SELECT 100 * (SUM(ERAB_NbrSuccEstab_2) / SUM(ERAB_NbrAttEstab_2) * SUM(RRC_SuccConnEstab) / SUM(RRC_AttConnEstab)) as kpi from EutranCellTddQuarter GROUP BY DateId,HourId ORDER by DateId DESC, HourId DESC LIMIT 1";
            //print_r($sql);return;
            $res = $db->query($sql);
            $row = $res->fetch();
            $items = array();
            array_push($items, round(floatval($row['kpi']),2));

            $sql = "SELECT 100 * (SUM(ERAB_NbrReqRelEnb_2) - SUM(ERAB_NbrReqRelEnb_Normal_2) + SUM(ERAB_HoFail_2)) / SUM(ERAB_NbrSuccEstab_2) as kpi from EutranCellTddQuarter GROUP BY DateId,HourId ORDER by DateId DESC, HourId DESC LIMIT 1" ;
            $res = $db->query($sql);
            $row = $res->fetch();
            array_push($items, round(floatval($row['kpi']),2));

            $sql =  "SELECT 100 * SUM(IRATHO_SuccOutGeran) / SUM(IRATHO_AttOutGeran) as kpi from EutranCellTddQuarter GROUP BY DateId,HourId ORDER by DateId DESC, HourId DESC LIMIT 1";

            $res = $db->query($sql);  
            $row = $res->fetch();
            array_push($items, round(floatval($row['kpi']),2));
            return $items;
        }else{
            $citys = $this->LoadNbiEngCity();
            $city = $citys[$cityChinese];
            $city = trim($city);
            $dsn = "mysql:host=localhost;dbname=nbi"; 
            $db = new PDO($dsn, 'root', 'mongs');
            $sql =  "SELECT 100 * (SUM(ERAB_NbrSuccEstab_2) / SUM(ERAB_NbrAttEstab_2) * SUM(RRC_SuccConnEstab) / SUM(RRC_AttConnEstab)) as kpi from EutranCellTddQuarter where city='".$city."' GROUP BY DateId,HourId ORDER by DateId DESC, HourId DESC LIMIT 1";
            //print_r($sql);return;
            $res = $db->query($sql);
            $row = $res->fetch();
            $items = array();
            array_push($items, round(floatval($row['kpi']),2));

            $sql = "SELECT 100 * (SUM(ERAB_NbrReqRelEnb_2) - SUM(ERAB_NbrReqRelEnb_Normal_2) + SUM(ERAB_HoFail_2)) / SUM(ERAB_NbrSuccEstab_2) as kpi from EutranCellTddQuarter where city='".$city."' GROUP BY DateId,HourId ORDER by DateId DESC, HourId DESC LIMIT 1" ;
            $res = $db->query($sql);
            $row = $res->fetch();
            array_push($items, round(floatval($row['kpi']),2));

            $sql =  "SELECT 100 * SUM(IRATHO_SuccOutGeran) / SUM(IRATHO_AttOutGeran) as kpi from EutranCellTddQuarter where city='".$city."'  GROUP BY DateId,HourId ORDER by DateId DESC, HourId DESC LIMIT 1";

            $res = $db->query($sql);  
            $row = $res->fetch();
            array_push($items, round(floatval($row['kpi']),2));
            return $items;
        }   
    }

    public function getvolteGauge(){
        $cityChinese = Input::get('city');
        if($cityChinese == 'province'){
            $dsn = "mysql:host=localhost;dbname=nbi"; 
            $db = new PDO($dsn, 'root', 'mongs');
            $sql =  "SELECT 100*(SUM(ERAB_NbrSuccEstab_1)/SUM(ERAB_NbrAttEstab_1)*SUM(RRC_SuccConnEstab)/SUM(RRC_AttConnEstab)) as kpi from EutranCellTddQuarter GROUP BY DateId,HourId ORDER by DateId DESC, HourId DESC LIMIT 1";
            $res = $db->query($sql);
            $row = $res->fetch();
            $items = array();
            array_push($items, round(floatval($row['kpi']),2));

            $sql = "SELECT 100 * (SUM(ERAB_NbrReqRelEnb_1) - SUM(ERAB_NbrReqRelEnb_Normal_1) + SUM(ERAB_HoFail_1)) / SUM(ERAB_NbrSuccEstab_1) as kpi  from EutranCellTddQuarter GROUP BY DateId,HourId ORDER by DateId DESC, HourId DESC LIMIT 1";
            $res = $db->query($sql);
            $row = $res->fetch();
            array_push($items, round(floatval($row['kpi']),2));

            $sql = "SELECT 100 * SUM(IRATHO_SuccOutGeran) / SUM(IRATHO_AttOutGeran) as kpi from EutranCellTddQuarter GROUP BY DateId,HourId ORDER by DateId DESC, HourId DESC LIMIT 1";
            $res = $db->query($sql);
            $row = $res->fetch();
            array_push($items, round(floatval($row['kpi']),2));
            return $items;
        }else{
            $citys = $this->LoadNbiEngCity();
            $city = $citys[$cityChinese];
            $city = trim($city);
            $dsn = "mysql:host=localhost;dbname=nbi"; 
            $db = new PDO($dsn, 'root', 'mongs');
            $sql =  "SELECT 100*(SUM(ERAB_NbrSuccEstab_1)/SUM(ERAB_NbrAttEstab_1)*SUM(RRC_SuccConnEstab)/SUM(RRC_AttConnEstab)) as kpi from EutranCellTddQuarter where city='".$city."' GROUP BY DateId,HourId ORDER by DateId DESC, HourId DESC LIMIT 1";
            $res = $db->query($sql);
            $row = $res->fetch();
            $items = array();
            array_push($items, round(floatval($row['kpi']),2));

            $sql = "SELECT 100 * (SUM(ERAB_NbrReqRelEnb_1) - SUM(ERAB_NbrReqRelEnb_Normal_1) + SUM(ERAB_HoFail_1)) / SUM(ERAB_NbrSuccEstab_1) as kpi  from EutranCellTddQuarter where city='".$city."' GROUP BY DateId,HourId ORDER by DateId DESC, HourId DESC LIMIT 1";
            $res = $db->query($sql);
            $row = $res->fetch();
            array_push($items, round(floatval($row['kpi']),2));

            $sql = "SELECT 100 * SUM(IRATHO_SuccOutGeran) / SUM(IRATHO_AttOutGeran) as kpi from EutranCellTddQuarter where city='".$city."'  GROUP BY DateId,HourId ORDER by DateId DESC, HourId DESC LIMIT 1";
            $res = $db->query($sql);
            $row = $res->fetch();
            array_push($items, round(floatval($row['kpi']),2));
            return $items;
        }   
    }

    public function getThreeKeysGauge(){
        $cityChinese = Input::get('city');
        //print_r($cityChinese);
        if($cityChinese == 'province'){
            $dsn = "mysql:host=localhost;dbname=AutoKPI"; 
            $db = new PDO($dsn, 'root', 'mongs');
            $sql = "SELECT * FROM SysCoreTemp_city_hour ORDER BY id DESC LIMIT 1";
            $res = $db->query($sql);
            $row = $res->fetch();
            $items = array();
            array_push($items, round(floatval($row['无线接通率']),2), round(floatval($row['无线掉线率']),2), round(floatval($row['切换成功率']),2));
            $result = array();
            $result['data'] = $items;
            return $result;
        }else{
           $citys = $this->LoadEngCity();
            $city = $citys[$cityChinese];
            $city = trim($city);
            $dsn = "mysql:host=localhost;dbname=AutoKPI"; 
            $db = new PDO($dsn, 'root', 'mongs');
            $sql = "SELECT * FROM SysCoreTemp_city_hour where city='".$city."' ORDER BY id DESC LIMIT 1";
            $res = $db->query($sql);
            $row = $res->fetch();
            $items = array();
            array_push($items, round(floatval($row['无线接通率']),2), round(floatval($row['无线掉线率']),2), round(floatval($row['切换成功率']),2));
            $result = array();
            $result['data'] = $items;
            return $result; 
        }      
    }

    protected function LoadNbiEngCity(){
        $result = array();
        $lines = file("common/txt/mapCitysNbi.txt");
        foreach($lines as $line){
            $pair = explode("=",$line);
            $result[$pair[0]]=$pair[1];
        }
        return $result;
    }

    protected function LoadEngCity(){
        $result = array();
        $lines = file("common/txt/mapCitys.txt");
        foreach($lines as $line){
            $pair = explode("=",$line);
            $result[$pair[0]]=$pair[1];
        }
        return $result;
    }

    public function getLowAccess(Request $request) {
        //day
        $endTime = time();
        $startTime = strtotime('-60 days', $endTime);
        $endTime = date('Y-m-d', $endTime);       
        $startTime = date('Y-m-d', $startTime);

        $datetime_id = 'day_id';
        $strtotime = 'UNIX_TIMESTAMP(day_id)*1000 ';      
        //$days=60*24;
        $days=60;
        $arr=array();
        for($i=0;$i<$days;$i++)
        {   
            $arr[]=(strtotime($startTime)+$i*86400-28800)*1000;
        }
        $strTime=implode('\',\'',$arr);
        $sql =  'select city,' .
                $strtotime. 
                'as datetime_id'.
                ',无线接通率 as kpi from SysCoreTemp_city_hour where '.
                $strtotime .
                'in (\''.
                $strTime.
                '\') GROUP BY city,day_id';
        $result = DB::connection('autokpi')->select($sql);
        return$this->getStrToTimeTest($result);
    }

    public function getLowAccessTrend(Request $request) {
        //hour
        $datetime_id = 'concat(day_id,\' \', concat(if(hour_id>=10,hour_id,concat(0,hour_id)),\':00:00\'))';
        $strtotime = 'UNIX_TIMESTAMP(concat(day_id,\' \', concat(if(hour_id>=10,hour_id,concat(0,hour_id)),\':00:00\')))*1000 ';
        $calendar = new DateTime();
        $endTime = $calendar->format('Y-m-d H:00:00');
        $startTime = date('Y-m-d H:00:00',strtotime('-60 days'));
        //print_r($startTime);
        //$days=60*24;
        $days=60*24;
        $arr=array();
        for($i=0;$i<$days;$i++)
        {   
            $arr[]=(strtotime($startTime)+$i*3600)*1000;
        }
        $strTime=implode('\',\'',$arr);
        $sql =  'select city,' .
                $strtotime. 
                'as datetime_id'.
                ',无线接通率 as kpi from SysCoreTemp_city_hour where '.
                $strtotime .
                'in (\''.
                $strTime.
                '\')';
        $result = DB::connection('autokpi')->select($sql);
        return$this->getStrToTimeTest($result);
    }
    
    public function getStrToTimeTest($result){
        //print_r($result);
        $series = array();
        $category = array();
        $array = array();
        foreach($result as $item){
            $city = $item->city;
            $datetime = $item->datetime_id;
            $kpi = $item->kpi;
            $arr = array();
            if (array_search($city,$category) === false) {
                $category[] = $city;
            }

            if(!array_key_exists($city,$series)){
                $series[$city] = array();
            }
            array_push($arr, floatval($datetime));
            array_push($arr, round(floatval($kpi),2));
            $series[$city][]=$arr;
            array_push($array, floatval($kpi));
        }

        $yAxis = array();
        $maxPos = array_search(max($array), $array);
        $max = $array[$maxPos];
        $minPos = array_search(min($array), $array);
        $min = $array[$minPos];
        $yAxis = $this->getYAxis($max,$min);
        /*array_push($yAxis, $min);
        array_push($yAxis, $max);*/
        $data['yAxis'] = $yAxis;
        $data['series'] = array();
        foreach($series as $key=>$value) {
            $data['series'][] = ['name'=>$key,'data'=>$value];
            //$data[] = ['name'=>$key,'data'=>$value];
        }
        return json_encode($data);
    }

    protected function getYAxis($max, $min) {
         $yAxis = array();
         // $max = round($max ,2);
         // $min = round($min ,2);
         $max = ceil($max);
         $min = floor($min);
         $yAxis0 = $min;
         $yAxis2 = round(($min + $max)/2, 2);  
         $yAxis1 = round(($min + $yAxis2)/2, 2);
         $yAxis4 = $max;
         $yAxis3 = round(($max + $yAxis2)/2, 2);
         $yAxis5 = $yAxis4 + 0.0001;
         array_push($yAxis, $yAxis0, $yAxis1, $yAxis2, $yAxis3, $yAxis4, $yAxis5);
         return $yAxis;
    }
    
    /*protected function getYAxis($max, $min) {
         $yAxis = array();
         $max = round($max ,2);
         $min = round($min ,2);
         $yAxis0 = $min;
         $yAxis2 = ($min + $max)/2;
         $yAxis1 = ($min + $yAxis2)/2;
         $yAxis4 = $max;
         $yAxis3 = ($max + $yAxis2)/2;
         $yAxis5 = $yAxis4 + 0.0001;
         array_push($yAxis, $yAxis0, $yAxis1, $yAxis2, $yAxis3, $yAxis4, $yAxis5);
         return $yAxis;
    }*/

    public function getLowAccessday(Request $request) {
        $calendar = new DateTime();
        $endTime = $calendar->format('Y-m-d');
        //$startTime = $calendar->format('Y-m-d');
        $startTime = date('Y-m-d',strtotime('-7days'));
        //$startTime = $calendar->sub(new \DateInterval('PT24H'))->format('Y-m-d');
        $sql = 'select city, day_id as datetime_id, 无线接通率 as kpi from SysCoreTemp_city_day where day_id>=\''.
            $startTime.
            '\' and day_id<=\''.
            $endTime.'\'';
        $result = DB::connection('autokpi')->select($sql);
        //$this->getHighChartData($result);
        //dd($result);
        return$this->getHighChartData($result);
    }

    public function getHighLostTrend(Request $request) {
        //hour
        $datetime_id = 'concat(day_id,\' \', concat(if(hour_id>=10,hour_id,concat(0,hour_id)),\':00:00\'))';
        $strtotime = 'UNIX_TIMESTAMP(concat(day_id,\' \', concat(if(hour_id>=10,hour_id,concat(0,hour_id)),\':00:00\')))*1000 ';
        $calendar = new DateTime();
        $endTime = $calendar->format('Y-m-d H:00:00');
        $startTime = date('Y-m-d H:00:00',strtotime('-60 days'));
        //print_r($startTime);
        //$days=60*24;
        $days=60*24;
        $arr=array();
        for($i=0;$i<$days;$i++)
        {   
            $arr[]=(strtotime($startTime)+$i*3600)*1000;
        }
        $strTime=implode('\',\'',$arr);
        $sql =  'select city,' .
                $strtotime. 
                'as datetime_id'.
                ', 无线掉线率 as kpi from SysCoreTemp_city_hour where '.
                $strtotime .
                'in (\''.
                $strTime.
                '\')';
                //print_r($sql);
        $result = DB::connection('autokpi')->select($sql);
        return$this->getStrToTimeTest($result);
    }

    public function getBadHandoverTrend(Request $request) {
        //hour
        $datetime_id = 'concat(day_id,\' \', concat(if(hour_id>=10,hour_id,concat(0,hour_id)),\':00:00\'))';
        $strtotime = 'UNIX_TIMESTAMP(concat(day_id,\' \', concat(if(hour_id>=10,hour_id,concat(0,hour_id)),\':00:00\')))*1000 ';
        $calendar = new DateTime();
        $endTime = $calendar->format('Y-m-d H:00:00');
        $startTime = date('Y-m-d H:00:00',strtotime('-60 days'));
        //print_r($startTime);
        //$days=60*24;
        $days=60*24;
        $arr=array();
        for($i=0;$i<$days;$i++)
        {   
            $arr[]=(strtotime($startTime)+$i*3600)*1000;
        }
        $strTime=implode('\',\'',$arr);
        $sql =  'select city,' .
                $strtotime. 
                'as datetime_id'.
                ', 切换成功率 as kpi from SysCoreTemp_city_hour where '.
                $strtotime .
                'in (\''.
                $strTime.
                '\')';
        $result = DB::connection('autokpi')->select($sql);
        return$this->getStrToTimeTest($result);
    }
    
    public function getHighLost() {
         //day
        $endTime = time();
        $startTime = strtotime('-60 days', $endTime);
        $endTime = date('Y-m-d', $endTime);       
        $startTime = date('Y-m-d', $startTime);

        $datetime_id = 'day_id';
        $strtotime = 'UNIX_TIMESTAMP(day_id)*1000 ';      
        //$days=60*24;
        $days=60;
        $arr=array();
        for($i=0;$i<$days;$i++)
        {   
            $arr[]=(strtotime($startTime)+$i*86400-28800)*1000;
        }
        $strTime=implode('\',\'',$arr);
        $sql =  'select city,' .
                $strtotime. 
                'as datetime_id'.
                ',无线掉线率 as kpi from SysCoreTemp_city_hour where '.
                $strtotime .
                'in (\''.
                $strTime.
                '\') GROUP BY city,day_id';
        $result = DB::connection('autokpi')->select($sql);
        return$this->getStrToTimeTest($result);
    }

    public function getBadHandover(){
        //day
        $endTime = time();
        $startTime = strtotime('-60 days', $endTime);
        $endTime = date('Y-m-d', $endTime);       
        $startTime = date('Y-m-d', $startTime);

        $datetime_id = 'day_id';
        $strtotime = 'UNIX_TIMESTAMP(day_id)*1000 ';      
        //$days=60*24;
        $days=60;
        $arr=array();
        for($i=0;$i<$days;$i++)
        {   
            $arr[]=(strtotime($startTime)+$i*86400-28800)*1000;
        }
        $strTime=implode('\',\'',$arr);
        $sql =  'select city,' .
                $strtotime. 
                'as datetime_id'.
                ',切换成功率 as kpi from SysCoreTemp_city_hour where '.
                $strtotime .
                'in (\''.
                $strTime.
                '\') GROUP BY city,day_id';
        $result = DB::connection('autokpi')->select($sql);
        return$this->getStrToTimeTest($result);       
    }

    public function getErabSuccessHandover(){
        //day
        $endTime = time();
        $startTime = strtotime('-60 days', $endTime);
        $endTime = date('Y-m-d', $endTime);       
        $startTime = date('Y-m-d', $startTime);

        $datetime_id = 'DateId';
        $strtotime = 'UNIX_TIMESTAMP(DateId)*1000 ';      
        //$days=60*24;
        $days=60;
        $arr=array();
        for($i=0;$i<$days;$i++)
        {   
            $arr[]=(strtotime($startTime)+$i*86400-28800)*1000;
        }
        $strTime=implode('\',\'',$arr);
        $sql =  'select city,' .
                $strtotime. 
                'as datetime_id,'.
                ' 100*(SUM(ERAB_NbrSuccEstab_1)/SUM(ERAB_NbrAttEstab_1)*SUM(RRC_SuccConnEstab)/SUM(RRC_AttConnEstab)) as kpi from EutranCellTddQuarter where '.
                $strtotime .
                'in (\''.
                $strTime.
                '\') GROUP BY city,DateId';
                //print_r($sql);
        $result = DB::connection('nbi')->select($sql);
        return$this->getStrToTimeTest($result);
    }

    public function getErabSuccessHandoverTrend(){
        //hour
        $datetime_id = 'concat(DateId,\' \', concat(if(HourId>=10,HourId,concat(0,HourId)),\':00:00\'))';
        $strtotime = 'UNIX_TIMESTAMP(concat(DateId,\' \', concat(if(HourId>=10,HourId,concat(0,HourId)),\':00:00\')))*1000 ';
        $calendar = new DateTime();
        $endTime = $calendar->format('Y-m-d H:00:00');
        $startTime = date('Y-m-d H:00:00',strtotime('-60 days'));
        //print_r($startTime);
        //$days=60*24;
        $days=60*24;
        $arr=array();
        for($i=0;$i<$days;$i++)
        {   
            $arr[]=(strtotime($startTime)+$i*3600)*1000;
        }
        $strTime=implode('\',\'',$arr);
        $sql =  'select city,' .
                $strtotime. 
                'as datetime_id,'.
                ' 100*(SUM(ERAB_NbrSuccEstab_1)/SUM(ERAB_NbrAttEstab_1)*SUM(RRC_SuccConnEstab)/SUM(RRC_AttConnEstab)) as kpi from EutranCellTddQuarter where '.
                $strtotime .
                'in (\''.
                $strTime.
                '\') group by datetime_id,HourId,city';
        $result = DB::connection('nbi')->select($sql);
        return$this->getStrToTimeTest($result);
    }

    public function getErabsLostTrend(){
        //hour
        $datetime_id = 'concat(DateId,\' \', concat(if(HourId>=10,HourId,concat(0,HourId)),\':00:00\'))';
        $strtotime = 'UNIX_TIMESTAMP(concat(DateId,\' \', concat(if(HourId>=10,HourId,concat(0,HourId)),\':00:00\')))*1000 ';
        $calendar = new DateTime();
        $endTime = $calendar->format('Y-m-d H:00:00');
        $startTime = date('Y-m-d H:00:00',strtotime('-60 days'));
        //print_r($startTime);
        //$days=60*24;
        $days=60*24;
        $arr=array();
        for($i=0;$i<$days;$i++)
        {   
            $arr[]=(strtotime($startTime)+$i*3600)*1000;
        }
        $strTime=implode('\',\'',$arr);
        $sql =  'select city,' .
                $strtotime. 
                'as datetime_id,'.
                ' 100 * (SUM(ERAB_NbrReqRelEnb_1) - SUM(ERAB_NbrReqRelEnb_Normal_1) + SUM(ERAB_HoFail_1)) / SUM(ERAB_NbrSuccEstab_1) as kpi  from EutranCellTddQuarter where '.
                $strtotime .
                'in (\''.
                $strTime.
                '\') GROUP BY datetime_id,HourId,city';
        $result = DB::connection('nbi')->select($sql);
        return$this->getStrToTimeTest($result);
    }
    
    public function getWirelessSuccTrend(){
        //hour
        $datetime_id = 'concat(DateId,\' \', concat(if(HourId>=10,HourId,concat(0,HourId)),\':00:00\'))';
        $strtotime = 'UNIX_TIMESTAMP(concat(DateId,\' \', concat(if(HourId>=10,HourId,concat(0,HourId)),\':00:00\')))*1000 ';
        $calendar = new DateTime();
        $endTime = $calendar->format('Y-m-d H:00:00');
        $startTime = date('Y-m-d H:00:00',strtotime('-60 days'));
        //print_r($startTime);
        //$days=60*24;
        $days=60*24;
        $arr=array();
        for($i=0;$i<$days;$i++)
        {   
            $arr[]=(strtotime($startTime)+$i*3600)*1000;
        }
        $strTime=implode('\',\'',$arr);
        $sql =  'select city,' .
                $strtotime. 
                'as datetime_id,'.
                ' 100 * (SUM(HO_SuccOutInterEnbS1_1) + SUM(HO_SuccOutInterEnbX2_1) + SUM(HO_SuccOutIntraEnb_1)) / (SUM(HO_AttOutInterEnbS1_1) + SUM(HO_AttOutInterEnbX2_1) + SUM(HO_AttOutIntraEnb_1)) as kpi from EutranCellTddQuarter where '.
                $strtotime .
                'in (\''.
                $strTime.
                '\') group by datetime_id,HourId,city';
        $result = DB::connection('nbi')->select($sql);
        return$this->getStrToTimeTest($result);
    }

    public function getVolteHandoverTrend(){
        //hour
        $datetime_id = 'concat(DateId,\' \', concat(if(HourId>=10,HourId,concat(0,HourId)),\':00:00\'))';
        $strtotime = 'UNIX_TIMESTAMP(concat(DateId,\' \', concat(if(HourId>=10,HourId,concat(0,HourId)),\':00:00\')))*1000 ';
        $calendar = new DateTime();
        $endTime = $calendar->format('Y-m-d H:00:00');
        $startTime = date('Y-m-d H:00:00',strtotime('-60 days'));
        //print_r($startTime);
        //$days=60*24;
        $days=60*24;
        $arr=array();
        for($i=0;$i<$days;$i++)
        {   
            $arr[]=(strtotime($startTime)+$i*3600)*1000;
        }
        $strTime=implode('\',\'',$arr);
        $sql =  'select city,' .
                $strtotime. 
                'as datetime_id,'.
                '100 * SUM(IRATHO_SuccOutGeran) / SUM(IRATHO_AttOutGeran) as kpi from EutranCellTddQuarter where '.
                $strtotime .
                'in (\''.
                $strTime.
                '\') group by datetime_id,HourId,city';
                //print_r($sql);
        $result = DB::connection('nbi')->select($sql);
        return$this->getStrToTimeTest($result);
    }

    public function getErabsLost(){
        //day
        $endTime = time();
        $startTime = strtotime('-60 days', $endTime);
        $endTime = date('Y-m-d', $endTime);       
        $startTime = date('Y-m-d', $startTime);

        $datetime_id = 'day_id';
        $strtotime = 'UNIX_TIMESTAMP(DateId)*1000 ';      
        //$days=60*24;
        $days=60;
        $arr=array();
        for($i=0;$i<$days;$i++)
        {   
            $arr[]=(strtotime($startTime)+$i*86400-28800)*1000;
        }
        $strTime=implode('\',\'',$arr);
        $sql =  'select city,' .
                $strtotime. 
                'as datetime_id,'.
                ' 100 * (SUM(ERAB_NbrReqRelEnb_1) - SUM(ERAB_NbrReqRelEnb_Normal_1) + SUM(ERAB_HoFail_1)) / SUM(ERAB_NbrSuccEstab_1) as kpi from EutranCellTddQuarter where '.
                $strtotime .
                'in (\''.
                $strTime.
                '\') GROUP BY city,datetime_id';
        $result = DB::connection('nbi')->select($sql);
        return$this->getStrToTimeTest($result);
    }

    public function getWirelessSucc(){
        //day
        $endTime = time();
        $startTime = strtotime('-60 days', $endTime);
        $endTime = date('Y-m-d', $endTime);       
        $startTime = date('Y-m-d', $startTime);

        $datetime_id = 'DateId';
        $strtotime = 'UNIX_TIMESTAMP(DateId)*1000 ';      
        //$days=60*24;
        $days=60;
        $arr=array();
        for($i=0;$i<$days;$i++)
        {   
            $arr[]=(strtotime($startTime)+$i*86400-28800)*1000;
        }
        $strTime=implode('\',\'',$arr);
        $sql =  'select city,' .
                $strtotime. 
                'as datetime_id,'.
                '100 * (SUM(HO_SuccOutInterEnbS1_1) + SUM(HO_SuccOutInterEnbX2_1) + SUM(HO_SuccOutIntraEnb_1)) / (SUM(HO_AttOutInterEnbS1_1) + SUM(HO_AttOutInterEnbX2_1) + SUM(HO_AttOutIntraEnb_1)) as kpi from EutranCellTddQuarter where '.
                $strtotime .
                'in (\''.
                $strTime.
                '\') GROUP BY city,datetime_id';
        $result = DB::connection('nbi')->select($sql);
        return$this->getStrToTimeTest($result);
    }

    public function getVolteHandover(){
        //day
        $endTime = time();
        $startTime = strtotime('-60 days', $endTime);
        $endTime = date('Y-m-d', $endTime);       
        $startTime = date('Y-m-d', $startTime);

        $datetime_id = 'DateId';
        $strtotime = 'UNIX_TIMESTAMP(DateId)*1000 ';      
        //$days=60*24;
        $days=60;
        $arr=array();
        for($i=0;$i<$days;$i++)
        {   
            $arr[]=(strtotime($startTime)+$i*86400-28800)*1000;
        }
        $strTime=implode('\',\'',$arr);
        $sql =  'select city,' .
                $strtotime. 
                'as datetime_id,'.
                '100 * SUM(IRATHO_SuccOutGeran) / SUM(IRATHO_AttOutGeran) as kpi from EutranCellTddQuarter where '.
                $strtotime .
                'in (\''.
                $strTime.
                '\') GROUP BY city,datetime_id';
                //print_r($sql);
        $result = DB::connection('nbi')->select($sql);
        return$this->getStrToTimeTest($result);
    }

    public function getChart1WireSucc(){
        //day
        $endTime = time();
        $startTime = strtotime('-60 days', $endTime);
        $endTime = date('Y-m-d', $endTime);       
        $startTime = date('Y-m-d', $startTime);

        $datetime_id = 'DateId';
        $strtotime = 'UNIX_TIMESTAMP(DateId)*1000 ';      
        //$days=60*24;
        $days=60;
        $arr=array();
        for($i=0;$i<$days;$i++)
        {   
            $arr[]=(strtotime($startTime)+$i*86400-28800)*1000;
        }
        $strTime=implode('\',\'',$arr);
        $sql =  'select city,' .
                $strtotime. 
                'as datetime_id,'.
                '100 * (SUM(ERAB_NbrSuccEstab_2) / SUM(ERAB_NbrAttEstab_2) * SUM(RRC_SuccConnEstab) / SUM(RRC_AttConnEstab)) as kpi from EutranCellTddQuarter where '.
                $strtotime .
                'in (\''.
                $strTime.
                '\') GROUP BY city,datetime_id';
        $result = DB::connection('nbi')->select($sql);
        return$this->getStrToTimeTest($result);
    }

    public function getChart1ErbLost(){
        //day
        $endTime = time();
        $startTime = strtotime('-60 days', $endTime);
        $endTime = date('Y-m-d', $endTime);       
        $startTime = date('Y-m-d', $startTime);

        $datetime_id = 'DateId';
        $strtotime = 'UNIX_TIMESTAMP(DateId)*1000 ';      
        //$days=60*24;
        $days=60;
        $arr=array();
        for($i=0;$i<$days;$i++)
        {   
            $arr[]=(strtotime($startTime)+$i*86400-28800)*1000;
        }
        $strTime=implode('\',\'',$arr);
        $sql =  'select city,' .
                $strtotime. 
                'as datetime_id,'.
                '100 * (SUM(ERAB_NbrReqRelEnb_2) - SUM(ERAB_NbrReqRelEnb_Normal_2) + SUM(ERAB_HoFail_2)) / SUM(ERAB_NbrSuccEstab_2) as kpi from EutranCellTddQuarter where '.
                $strtotime .
                'in (\''.
                $strTime.
                '\') GROUP BY city,datetime_id';
        $result = DB::connection('nbi')->select($sql);
        return$this->getStrToTimeTest($result);
    }

    public function getChart1VideoSucc(){
        //day
        $endTime = time();
        $startTime = strtotime('-60 days', $endTime);
        $endTime = date('Y-m-d', $endTime);       
        $startTime = date('Y-m-d', $startTime);

        $datetime_id = 'DateId';
        $strtotime = 'UNIX_TIMESTAMP(DateId)*1000 ';      
        //$days=60*24;
        $days=60;
        $arr=array();
        for($i=0;$i<$days;$i++)
        {   
            $arr[]=(strtotime($startTime)+$i*86400-28800)*1000;
        }
        $strTime=implode('\',\'',$arr);
        $sql =  'select city,' .
                $strtotime. 
                'as datetime_id,'.
                '100 * SUM(IRATHO_SuccOutGeran) / SUM(IRATHO_AttOutGeran) as kpi from EutranCellTddQuarter where '.
                $strtotime .
                'in (\''.
                $strTime.
                '\') GROUP BY city,datetime_id';
        $result = DB::connection('nbi')->select($sql);
        return$this->getStrToTimeTest($result);
    }

    public function getChart1EsrvccHander(){
        //day
        $endTime = time();
        $startTime = strtotime('-60 days', $endTime);
        $endTime = date('Y-m-d', $endTime);       
        $startTime = date('Y-m-d', $startTime);

        $datetime_id = 'DateId';
        $strtotime = 'UNIX_TIMESTAMP(DateId)*1000 ';      
        //$days=60*24;
        $days=60;
        $arr=array();
        for($i=0;$i<$days;$i++)
        {   
            $arr[]=(strtotime($startTime)+$i*86400-28800)*1000;
        }
        $strTime=implode('\',\'',$arr);
        $sql =  'select city,' .
                $strtotime. 
                'as datetime_id,'.
                '100 * (SUM(HO_SuccOutInterEnbS1_2) + SUM(HO_SuccOutInterEnbX2_2) + SUM(HO_SuccOutIntraEnb_2)) / (SUM(HO_AttOutInterEnbS1_2) + SUM(HO_AttOutInterEnbX2_2) + SUM(HO_AttOutIntraEnb_2))as kpi from EutranCellTddQuarter where '.
                $strtotime .
                'in (\''.
                $strTime.
                '\') GROUP BY city,datetime_id';
        $result = DB::connection('nbi')->select($sql);
        return$this->getStrToTimeTest($result);
    }

    public function getChart1WireSuccTrend(){
        //hour
        $datetime_id = 'concat(DateId,\' \', concat(if(HourId>=10,HourId,concat(0,HourId)),\':00:00\'))';
        $strtotime = 'UNIX_TIMESTAMP(concat(DateId,\' \', concat(if(HourId>=10,HourId,concat(0,HourId)),\':00:00\')))*1000 ';
        $calendar = new DateTime();
        $endTime = $calendar->format('Y-m-d H:00:00');
        $startTime = date('Y-m-d H:00:00',strtotime('-60 days'));
        //print_r($startTime);
        //$days=60*24;
        $days=60*24;
        $arr=array();
        for($i=0;$i<$days;$i++)
        {   
            $arr[]=(strtotime($startTime)+$i*3600)*1000;
        }
        $strTime=implode('\',\'',$arr);
        $sql =  'select city,' .
                $strtotime. 
                'as datetime_id,'.
                '100 * (SUM(ERAB_NbrSuccEstab_2) / SUM(ERAB_NbrAttEstab_2) * SUM(RRC_SuccConnEstab) / SUM(RRC_AttConnEstab)) as kpi from EutranCellTddQuarter where '.
                $strtotime .
                'in (\''.
                $strTime.
                '\') group by datetime_id,HourId,city';
        $result = DB::connection('nbi')->select($sql);
        return$this->getStrToTimeTest($result);
    }

    public function getChart1ErbLostTrend(){
        $calendar = new DateTime();
        $endTime = $calendar->format('Y-m-d');
        $startTime = date('Y-m-d',strtotime('-7days'));
        $sql = 'select city, DateId as datetime_id,'.
            ' 100 * (SUM(ERAB_NbrReqRelEnb_2) - SUM(ERAB_NbrReqRelEnb_Normal_2) + SUM(ERAB_HoFail_2)) / SUM(ERAB_NbrSuccEstab_2) as kpi '.
            'from EutranCellTddQuarter where DateId>=\''.$startTime.'\' and DateId<=\''.$endTime.'\' group by datetime_id,city'; 
        $result = DB::connection('nbi')->select($sql);
        //$this->getHighChartData($result);
        //dd($result);
        return$this->getHighChartData($result);
    }

    public function getChart1VideoSuccTrend(){
        //hour
        $datetime_id = 'concat(DateId,\' \', concat(if(HourId>=10,HourId,concat(0,HourId)),\':00:00\'))';
        $strtotime = 'UNIX_TIMESTAMP(concat(DateId,\' \', concat(if(HourId>=10,HourId,concat(0,HourId)),\':00:00\')))*1000 ';
        $calendar = new DateTime();
        $endTime = $calendar->format('Y-m-d H:00:00');
        $startTime = date('Y-m-d H:00:00',strtotime('-60 days'));
        //print_r($startTime);
        //$days=60*24;
        $days=60*24;
        $arr=array();
        for($i=0;$i<$days;$i++)
        {   
            $arr[]=(strtotime($startTime)+$i*3600)*1000;
        }
        $strTime=implode('\',\'',$arr);
        $sql =  'select city,' .
                $strtotime. 
                'as datetime_id,'.
                '100 * SUM(IRATHO_SuccOutGeran) / SUM(IRATHO_AttOutGeran) as kpi from EutranCellTddQuarter where '.
                $strtotime .
                'in (\''.
                $strTime.
                '\') group by datetime_id,HourId,city';
        $result = DB::connection('nbi')->select($sql);
        return$this->getStrToTimeTest($result);
    }

    public function getChart1EsrvccHanderTrend(){
        //hour
        $datetime_id = 'concat(DateId,\' \', concat(if(HourId>=10,HourId,concat(0,HourId)),\':00:00\'))';
        $strtotime = 'UNIX_TIMESTAMP(concat(DateId,\' \', concat(if(HourId>=10,HourId,concat(0,HourId)),\':00:00\')))*1000 ';
        $calendar = new DateTime();
        $endTime = $calendar->format('Y-m-d H:00:00');
        $startTime = date('Y-m-d H:00:00',strtotime('-60 days'));
        $days=60*24;
        $arr=array();
        for($i=0;$i<$days;$i++)
        {   
            $arr[]=(strtotime($startTime)+$i*3600)*1000;
        }
        $strTime=implode('\',\'',$arr);
        $sql =  'select city,' .
                $strtotime. 
                'as datetime_id,'.
                '100 * (SUM(HO_SuccOutInterEnbS1_2) + SUM(HO_SuccOutInterEnbX2_2) + SUM(HO_SuccOutIntraEnb_2)) / (SUM(HO_AttOutInterEnbS1_2) + SUM(HO_AttOutInterEnbX2_2) + SUM(HO_AttOutIntraEnb_2))as kpi from EutranCellTddQuarter where '.
                $strtotime .
                'in (\''.
                $strTime.
                '\') group by datetime_id,HourId,city';
        $result = DB::connection('nbi')->select($sql);
        return$this->getStrToTimeTest($result);
    }

    public function getHighChartData($result){

        $series = array();
        $category = array();
        foreach($result as $item){
            $city = $item->city;
            $datetime = $item->datetime_id;
            $kpi = $item->kpi;
            if (array_search($datetime,$category) === false) {
                $category[] = $datetime;
            }

            if(!array_key_exists($city,$series)){
                $series[$city] = array();
            }
            $series[$city][]=floatval($kpi);
        }
        $data['category'] = $category;
        $data['series'] = array();
        foreach($series as $key=>$value) {
            $data['series'][] = ['name'=>$key,'data'=>$value];
        }
        return json_encode($data);
    }


}
