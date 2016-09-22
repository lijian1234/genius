<?php
/**
 * Created by PhpStorm.
 * User: wang yang
 * Date: 2016/7/28
 * Time: 14:40
 */

namespace APP\Http\Controllers\Exporter;
use PDO;
use DateTime;
use DateInterval;
use App\Http\Requests\Request;
use PHPExcel_Chart_DataSeries;

class CarrierByChannel extends AbstractModel
{
    /**
     * CarrierByChannel constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->chartTitle = "基于频点分布";
        $this->chartType = PHPExcel_Chart_DataSeries::TYPE_BARCHART;
        $this->chartData = $this->getChartData();
    }


    public function getChartData()
    {
        if ($this->chartData != NULL) {
            return $this->chartData;
        }

        $day_id = $this->request->get('day',date_sub(new DateTime(), new DateInterval('P1D'))->format('ymd'));
        $db = new PDO("mysql:host=192.168.3.144;dbname=kget$day_id","root","mongs");
        $sql = "select '$day_id' as series,t1.band as xTicks, count(*) as kpi FROM (select band, subNetwork from TempParameterRRUAndSlaveCount where band is not null) as  t1 left join (select connName, subNetwork from mongs.databaseconn) as t2
            on LOCATE(t1.subNetwork,t2.subNetwork) > 0 where t2.connName is not null group by series,xTicks";
        $this->chartData = collect($db->query($sql,PDO::FETCH_ASSOC)->fetchAll());
        return $this->chartData;
    }

}