<?php
/**
 * Created by PhpStorm.
 * User: wang yang
 * Date: 2016/7/27
 * Time: 14:40
 */

namespace APP\Http\Controllers\Exporter;

use DateTime;
use DateInterval;
use App\Http\Requests\Request;
use Illuminate\Support\Facades\DB;
use PHPExcel_Chart_DataSeries;
use Illuminate\Database\Eloquent\Collection;

class HandoverRate extends AbstractModel
{
    /**
     * HandoverRate constructor.
     * @param Request $request
     */
    public function __construct(Request $request){
        $this->request = $request;
        $this->chartType = PHPExcel_Chart_DataSeries::TYPE_LINECHART;
        $this->chartTitle = '切换成功率';
        $this->chartData = $this->getChartData();
    }
    /**
     * @return Collection
     */
    public function getChartData()
    {
        if ($this->chartData !== null) {
            return $this->chartData;
        }
        $startTime = $this->request->get('startTime',date_sub(new DateTime(), new DateInterval('P15D'))
            ->format('Y-m-d'));
        $endTime = $this->request->get('endTime',date('Y-m-d'));
        $sql = "select city as series,day_id as xTicks,切换成功率 as kpi from SysCoreTemp_city_day where day_id>='$startTime' and day_id<='$endTime'";
        return collect(DB::connection('autokpi')->select($sql));
    }

}