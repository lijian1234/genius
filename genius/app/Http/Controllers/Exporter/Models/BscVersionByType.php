<?php
/**
 * Created by PhpStorm.
 * User: wang yang
 * Date: 2016/7/28
 * Time: 11:24
 */

namespace APP\Http\Controllers\Exporter;
use PDO;
use DateTime;
use DateInterval;
use PHPExcel_Chart_DataSeries;
use App\Http\Requests\Request;
use Illuminate\Support\Collection;

class BscVersionByType extends AbstractModel
{
    /**
     * BscVersionByType constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->chartTitle = "基于类型分布";
        $this->chartType = PHPExcel_Chart_DataSeries::TYPE_BARCHART;
        $this->chartData = $this->getChartData();
    }


    /**
     * @return Collection
     */
    public function getChartData()
    {
        if($this->chartData != NULL) {
            return $this->chartData;
        }
        $day_id = $this->request->get('day',date_sub(new DateTime(), new DateInterval('P1D'))->format('ymd'));
        $db = new PDO("mysql:host=192.168.3.144;dbname=kget$day_id","root","mongs");
        $sql = "select softwareVersion as series, siteType as xTicks, count(*) as kpi from TempSiteVersion where softwareVersion is not null and softwareVersion !='' and softwareVersion !='!!!!' and siteType is not null group by siteType,softwareVersion order by softwareVersion";
        $this->chartData = collect($db->query($sql)->fetchAll());
        return $this->chartData;
    }
}