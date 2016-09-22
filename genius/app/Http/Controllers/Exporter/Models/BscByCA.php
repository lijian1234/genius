<?php
/**
 * Created by PhpStorm.
 * User: wang yang
 * Date: 2016/7/27
 * Time: 16:29
 */

namespace APP\Http\Controllers\Exporter;

use PDO;
use DateTime;
use DateInterval;
use App\Http\Requests\Request;
use PHPExcel_Chart_DataSeries;

class BscByCA extends AbstractModel
{
    /**
     * BscByCA constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->chartType = PHPExcel_Chart_DataSeries::TYPE_PIECHART;
        $this->chartTitle = '基于CA分布';
        $this->chartData = $this->getChartData();
    }

    public function getChartData()
    {
        if ($this->chartData !== null) {
            return $this->chartData;
        }
        $day_id = $this->request->get('day',date_sub(new DateTime(), new DateInterval('P1D'))->format('ymd'));
        $sql = "select '$day_id' as series,if(OptionalFeatureLicenseId='CarrierAggregation' and serviceState=1,'CA','非CA') as xTicks,count(*) as kpi from OptionalFeatureLicense group by series,xTicks";
        $db = new PDO("mysql:host=192.168.3.144;dbname=kget$day_id","root","mongs");
        return collect($db->query($sql)->fetchAll());
    }

}