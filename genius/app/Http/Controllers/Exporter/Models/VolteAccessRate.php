<?php
/**
 * Created by PhpStorm.
 * User: efjlmmo
 * Date: 2016/7/27
 * Time: 15:34
 */

namespace APP\Http\Controllers\Exporter;

use DateTime;
use DateInterval;
use App\Http\Requests\Request;
use PHPExcel_Chart_DataSeries;
use DB;

class VolteAccessRate extends  AbstractModel
{

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->chartType = PHPExcel_Chart_DataSeries::TYPE_LINECHART;
        $this->chartTitle = 'Volte无线接通率';
        $this->chartData = $this->getChartData();
    }

    public function getChartData()
    {
        if ($this->chartData !== null) {
            return $this->chartData;
        }
        $startTime = $this->request->get('startTime',date_sub(new DateTime(), new DateInterval('P15D'))
            ->format('Y-m-d'));
        $endTime = $this->request->get('endTime',date('Y-m-d'));
        $sql =  "select City as series,DateId as xTicks, 100 * (SUM(HO_SuccOutInterEnbS1_1) + SUM(HO_SuccOutInterEnbX2_1) + SUM(HO_SuccOutIntraEnb_1)) / (SUM(HO_AttOutInterEnbS1_1) + SUM(HO_AttOutInterEnbX2_1) + SUM(HO_AttOutIntraEnb_1)) as kpi from EutranCellTddQuarter where DateId>='$startTime' and DateId<='$endTime' group by DateId,City";
        return collect(DB::connection('nbi')->select($sql));
    }
}