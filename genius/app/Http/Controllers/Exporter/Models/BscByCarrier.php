<?php
/**
 * Created by PhpStorm.
 * User: wang yang
 * Date: 2016/7/27
 * Time: 16:26
 */

namespace APP\Http\Controllers\Exporter;

use PDO;
use DateTime;
use DateInterval;
use App\Http\Requests\Request;
use PHPExcel_Chart_DataSeries;
class BscByCarrier extends AbstractModel
{
    /**
     * BscByCarrier constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->chartType = PHPExcel_Chart_DataSeries::TYPE_PIECHART;
        $this->chartTitle = '基于载频分布';
        $this->chartData = $this->getChartData();
    }


    public function getChartData()
    {
        if ($this->chartData !== null) {
            return $this->chartData;
        }
        $day_id = $this->request->get('day',date_sub(new DateTime(), new DateInterval('P1D'))->format('ymd'));
        $sql = "select '$day_id' as series,carriesCount as xTicks,count(meContext) kpi FROM TempParameterRRUAndSlaveCount where carriesCount is not null group by series,xTicks";
        $db = new PDO("mysql:host=192.168.3.144;dbname=kget$day_id","root","mongs");
        return collect($db->query($sql)->fetchAll());
    }
}