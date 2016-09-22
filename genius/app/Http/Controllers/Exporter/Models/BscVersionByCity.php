<?php
/**
 * Created by PhpStorm.
 * User: efjlmmo
 * Date: 2016/7/28
 * Time: 9:11
 */

namespace APP\Http\Controllers\Exporter;

use PDO;
use DateTime;
use DateInterval;
use PHPExcel_Chart_DataSeries;
use App\Http\Requests\Request;
class BscVersionByCity extends AbstractModel
{
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->chartType = PHPExcel_Chart_DataSeries::TYPE_BARCHART;
        $this->chartTitle = "基站类型分布(基于城市)";
        $this->chartData = $this->getChartData();
    }

    public function getChartData()
    {
        if ($this->chartData !== NULL) {
            return $this->chartData;
        }

        $day_id = $this->request->get('day',date_sub(new DateTime(),new DateInterval('P1D'))->format("ymd"));
        $db = new PDO("mysql:host=192.168.3.144;dbname=kget$day_id","root","mongs");
        $sql = "select t2.city as series, t3.version as xTicks, count(*) as kpi  from (select subNetwork,substring_index(currentUpgradePackage,'=',-1) as currentUpgradePackage from ConfigurationVersion) as t1 left join  
                (select connName as city, subNetwork as subNet from mongs.databaseconn) as  t2 on LOCATE(t1.subNetwork,t2.subNet) > 0
                left join (select UpgradePackageId,SUBSTRING_INDEX(UP_CompatibilityIndex,'_',1) as version from UpgradePackage group by UpgradePackageId) as t3 on t3.UpgradePackageId=t1.currentUpgradePackage
                where t3.version != \"\" and LOCATE(\"!!!!\",t3.version) = 0 and t2.city is not null group by series,xTicks";
        $this->chartData = collect($db->query($sql)->fetchAll());
        return $this->chartData;
    }

}