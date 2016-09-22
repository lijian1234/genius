<?php
/**
 * Created by PhpStorm.
 * User: wang yang
 * Date: 2016/7/26
 * Time: 17:34
 */

namespace APP\Http\Controllers\Exporter;

use PHPExcel_Chart;
use PHPExcel_Chart_Title;
use PHPExcel_Chart_Legend;
use PHPExcel_Chart_Layout;
use PHPExcel_Chart_PlotArea;
use PHPExcel_Chart_DataSeries;
use PHPExcel_Chart_DataSeriesValues;
use App\Http\Requests\Request;
use Illuminate\Support\Collection;

abstract class AbstractModel implements IModel
{
    /**
     * @var PHPExcel_Chart_DataSeries $chartType
     */
    protected $chartType;

    /**
     * @var Collection $chartData
     */
    protected $chartData;

    /**
     * @var string $chartTitle
     */
    protected $chartTitle;

    /**
     * @var Request $request
     */
    protected $request;

    /**
     * 创建ExcelChart。
     * 在默认情况下，只考虑最简单的XYChart。
     * 若需求更加复杂的chart,请在子类中重写该方法。
     * @return PHPExcel_Chart $chart
     */
    public function toExcelChart(){
        
        //创建dataSeriesLabels
        $dataSeriesLabels = array();
        $series = $this->chartData->pluck('series')->unique();
        foreach ($series as $item) {
            $dataSeriesLabels[] = new PHPExcel_Chart_DataSeriesValues('String',NULL,NULL,1,array($item));
        }
        unset($series);

        //创建xTickValues
        $xTicks = $this->chartData->pluck('xTicks')->unique();
        $xAxisTickValues = array(new PHPExcel_Chart_DataSeriesValues('String',NULL,NULL,$xTicks->count(),$xTicks->toArray()));
        unset($xTicks);

        //创建dataSeriesValues
        $group = $this->chartData->groupBy('series');
        $dataSeriesValues = array();
        foreach($group as $key => $value) {
            $dataSeriesValues[] = new PHPExcel_Chart_DataSeriesValues('Number',NULL,NULL,$value->count(),$value->pluck('kpi')->toArray());
        }
        unset($group);

        //创建dataSeries
        $series = new PHPExcel_Chart_DataSeries(
            $this->chartType,
            PHPExcel_Chart_DataSeries::GROUPING_STANDARD,
            range(0,count($dataSeriesValues)-1),
            $dataSeriesLabels,
            $xAxisTickValues,
            $dataSeriesValues
        );

        //创建plotArea
        $layout = new PHPExcel_Chart_Layout();
        $layout->setShowPercent(true);
        $plotArea = new PHPExcel_Chart_PlotArea($layout, array($series));

        //创建legend
        $legend = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_BOTTOM,NULL,false);

        //设置chartTitle.
        $title = new PHPExcel_Chart_Title($this->chartTitle);

        //设置y-Axis Label.
        $yAxisLabel = new PHPExcel_Chart_Title('Value');

        //创建chart.
        return new PHPExcel_Chart(
            'AccessRate',
            $title,
            $legend,
            $plotArea,
            true,
            0,
            NULL,
            $yAxisLabel
        );
    }


    /**
     * 创建指定格式的数组，可直接写入Excel表格
     * 数组格式
     * [
     * {'',xTick1,xTick2...xTickN},
     * {series1,number1_1,number1_2,...},
     * {seriesN,number_N_1,number_N_2,...}
     * ]
     * @return array
     */
    public function toExcelArray()
    {
        $chartData = $this->getChartData();
        $data = array();
        //第一行
        $data[] = array_merge([''],$chartData->pluck('xTicks')->unique()->toArray());
        //数据行
        foreach($chartData->groupBy('series') as $key=>$value) {
            $data[] = array_merge([$key],$value->pluck('kpi')->toArray());
        }
        return $data;
    }
}