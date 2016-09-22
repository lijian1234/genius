<?php
/**
 * Created by PhpStorm.
 * User: wang yang
 * Date: 2016/7/27
 * Time: 16:24
 */

namespace APP\Http\Controllers\Exporter;
use PDO;
use DateTime;
use DateInterval;
use App\Http\Requests\Request;
use PHPExcel_Chart_DataSeries;


class BscByType extends AbstractModel
{
    /**
     * BscByType constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->chartType = PHPExcel_Chart_DataSeries::TYPE_PIECHART;
        $this->chartTitle = '基于类型分布';
        $this->chartData = $this->getChartData();
    }

    public function getChartData()
    {
        if ($this->chartData !== null) {
            return $this->chartData;
        }
        $day_id = $this->request->get('day',date_sub(new DateTime(), new DateInterval('P1D'))->format('ymd'));
        $sql = "SELECT '$day_id' as series,siteType as xTicks,count(distinct meContext) as kpi FROM TempSiteType group by series,xTicks";
        $db = new PDO("mysql:host=192.168.3.144;dbname=kget$day_id","root","mongs");
        return collect($db->query($sql)->fetchAll());
    }
}