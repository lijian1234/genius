<?php
/**
 * Created by PhpStorm.
 * User: wang yang
 * Date: 2016/7/26
 * Time: 17:00
 */

namespace APP\Http\Controllers\Exporter;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface IModel
 * @package APP\Http\Controllers\Exporter
 *
 * This interface define a model which we want to write to excel/pdf/....
 * The model should be a chart or a array (or other style in future).
 */
interface IModel
{
    /**
     * @return mixed
     */
    function toExcelChart();

    /**
     * @return mixed
     */
    function toExcelArray();

    /**
     * 返回Model所需数据，返回格式应该为一个约定的数组。
     * 格式:
     * [
     *   {series:'series1',xTick:'tick1',kpi:'kpi1'},
     *   .
     *   .
     *   .
     *   {series:'seriesN',xTick:'tickN',kpi:'kpiN'}
     * ]
     * series:序列名
     * xTick: X值
     * kpi: 指标值
     * @return Collection
     */
    function getChartData();
}