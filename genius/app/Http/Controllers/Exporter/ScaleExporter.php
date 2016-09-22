<?php

/**
 * Created by PhpStorm.
 * User: wang yang
 * Date: 2016/7/20
 * Time: 15:09
 */

namespace  APP\Http\Controllers\Exporter;

use PDO;
use DateTime;
use DateInterval;
use PHPExcel;
use PHPExcel_Chart;
use PHPExcel_Chart_Legend;
use PHPExcel_Chart_Title;
use PHPExcel_Chart_Layout;
use PHPExcel_Chart_DataSeries;
use PHPExcel_Chart_PlotArea;
use PHPExcel_Chart_DataSeriesValues;
use PHPExcel_Writer_Excel2007;
use App\Http\Requests\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ScaleExportRequest;
use Illuminate\Database\Eloquent\Collection;

class ScaleExporter extends Controller
{

    /**
     * 创建Excel报告.
     * @param Request $request
     * @return string $fileName
     */
    public function export(ScaleExportRequest $request) {
        //创建excel对象.
        $excel = new PHPExcel();

        // 创建基站类型分布sheet.
        $sheetBSC = $excel->getSheet(0);
        $sheetBSC->setTitle('基站类型分布');
        
        $bscByType = new BscByType($request);
        $sheetBSC->addChart($bscByType->toExcelChart()->setTopLeftPosition('A1')->setBottomRightPosition('G20'));
        $sheetBSC->fromArray($bscByType->toExcelArray(),NULL,'A21');
        
        $bscByCA = new BscByCA($request);
        $sheetBSC->addChart($bscByCA->toExcelChart()->setTopLeftPosition('H1')->setBottomRightPosition('O20'));
        $sheetBSC->fromArray($bscByCA->toExcelArray(),NULL,'H21');
        
        $bscByCarrier = new BscByCarrier($request);
        $sheetBSC->addChart($bscByCarrier->toExcelChart()->setTopLeftPosition('P1')->setBottomRightPosition('U20'));
        $sheetBSC->fromArray($bscByCarrier->toExcelArray(),NULL,'P21');

        //创建基站版本分布sheet
        $sheetBSCVersion = $excel->createSheet(1);
        $sheetBSCVersion->setTitle('基站版本分布');

        $bscVersionByCity = new BscVersionByCity($request);
        $sheetBSCVersion->addChart($bscVersionByCity->toExcelChart()->setTopLeftPosition('A1')->setBottomRightPosition('H20'));
        $sheetBSCVersion->fromArray($bscVersionByCity->toExcelArray(),NULL,'A21');

        $bscVersionByType = new BscVersionByType($request);
        $sheetBSCVersion->addChart($bscVersionByType->toExcelChart()->setTopLeftPosition('H1')->setBottomRightPosition('O20'));
        $sheetBSCVersion->fromArray($bscVersionByType->toExcelArray(),NULL,'H21');

        $sheetCarrier = $excel->createSheet(2);
        $sheetCarrier->setTitle('载频频点分布');

        $carrierByCity = new CarrierByCity($request);
        $sheetCarrier->addChart($carrierByCity->toExcelChart()->setTopLeftPosition('A1')->setBottomRightPosition('H20'));
        $sheetCarrier->fromArray($carrierByCity->toExcelArray(),NULL,'A21');

        $carrierByChannel = new CarrierByChannel($request);
        $sheetCarrier->addChart($carrierByChannel->toExcelChart()->setTopLeftPosition('H1')->setBottomRightPosition('O20'));
        $sheetCarrier->fromArray($carrierByChannel->toExcelArray(),NULL,'H21');

        $writer = new PHPExcel_Writer_Excel2007($excel);
        $writer->setIncludeCharts(true);
        $writer->save('NetworkScale.xlsx');
        return 'NetworkScale.xlsx';

    }

//    /**
//     * 创建图表
//     * @param Collection $chartData
//     * @param string $xKey
//     * @param string $yKey
//     * @param string $chartType
//     * @return PHPExcel_Chart $chart
//     */
//    private function createChart($chartData,$xKey,$yKey,$chartType) {
//
//        // Create xAxisTickValues.
//        $xKeys = $chartData->pluck($xKey)->all();
////        dd($xKeys);
//        $xAxisTickValues = array (
//            new PHPExcel_Chart_DataSeriesValues('String',NULL,NULL,count($xKeys),$xKeys)
//        );
//
//        // Create dataSeriesValues.
//        $values = $chartData->pluck($yKey)->all();
////        dd($values);
//        $dataSeriesValues = array(
//            new PHPExcel_Chart_DataSeriesValues('Number',NULL,NULL,count($values),$values)
//        );
//
//        // Create dataSeries
//        $series = new PHPExcel_Chart_DataSeries(
//            $chartType,
//            NULL,
//            range(0,count($dataSeriesValues)-1),
//            NULL,
//            $xAxisTickValues,
//            $dataSeriesValues
//        );
//
//        // Set up layout object for the pie chart.
//        $layout = new PHPExcel_Chart_Layout();
////        $layout->setShowVal(TRUE);
//        $layout->setShowPercent(TRUE);
//
//        // Set series in plot area.
//        $plotArea = new PHPExcel_Chart_PlotArea($layout,array($series));
//
//        // Set legend.
//        $legend = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_BOTTOM,NULL,FALSE);
//        // Set title.
//        $title = new PHPExcel_Chart_Title('占比分布');
//
//        // Create chart.
//        $chart = new PHPExcel_Chart(
//            'percentage',
//            $title,
//            $legend,
//            $plotArea,
//            true,
//            0,
//            NULL,
//            Null
//        );
//        return $chart;
//    }

//    /**
//     * Create a specified array for excel.
//     * @param Collection $chartData
//     */
//    private function getArrayData($chartData,$xKey,$yKey){
//        return [
//            collect([''])->merge($chartData->pluck($xKey))->all(),
//            collect('数目')->merge($chartData->pluck($yKey))->all()
//        ];
//    }
}