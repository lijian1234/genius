<?php
/**
 * Created by PhpStorm.
 * User: wang yang
 * Date: 2016/7/26
 * Time: 17:28
 */

namespace APP\Http\Controllers\Exporter;

use DateTime;
use DateInterval;

use App\Http\Requests\Request;
use App\Http\Requests\KpiExportRequest;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

use PHPExcel_Chart_DataSeries;

class AccessRateModel extends AbstractModel
{
    /**
     * 构造方法。
     * @param Request $request
     */
    public function __construct(KpiExportRequest $request){
        $this->request = $request;
        $this->chartType = PHPExcel_Chart_DataSeries::TYPE_LINECHART;
        $this->chartTitle = '无线接通率';
        $this->chartData = $this->getChartData();
    }

    /**
     * @return Collection
     */
    public function getChartData() {
        if ($this->chartData !== null) {
            return $this->chartData;
        }
        $startTime = $this->request->get('startTime',date_sub(new DateTime(), new DateInterval('P15D'))
            ->format('Y-m-d'));
        $endTime = $this->request->get('endTime',date('Y-m-d'));
        $sql = "select city as series,day_id as xTicks,无线接通率 as kpi from SysCoreTemp_city_day where day_id>='$startTime' and day_id<='$endTime'";
        return collect(DB::connection('autokpi')->select($sql));
    }
}