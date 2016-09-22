<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Requests;
use DateTime;
use DateInterval;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class InterfereController extends Controller
{

    /**
     * @param Request $request
     */
    public function getInterfereData(Request $request) {
        $startDate = new DateTime();
        $startDate->sub(new DateInterval('P2M'));
        $endDate = new DateTime();
        $endDate->sub(new DateInterval('P1D'));
        $startDateId = $startDate->format('Y-m-d');
        $endDateId = $endDate->format('Y-m-d');
        $sql = 'select day_id as time,city,高干扰小区占比 as ratio from interfereRate_city_day where day_id BETWEEN "'.$startDateId.'" and "'.$endDateId.'"';
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
            $ratio = $item->ratio;
            $time = strtotime($item->time);
            $arr = array();
            if (array_search($city,$category) === false) {
                $category[] = $city;
            }

            if(!array_key_exists($city,$series)){
                $series[$city] = array();
            }
            array_push($arr, floatval($time)*1000);
            array_push($arr, floatval($ratio));
            $series[$city][] = $arr;
        }
        $data['series'] = array();
        foreach($series as $key=>$value) {
            $data['series'][] = ['name'=>$key,'data'=>$value];
        }
        return json_encode($data);
    }


}
