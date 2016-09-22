<?php
/**
 * Created by PhpStorm.
 * User: wang yang
 * Date: 2016/7/20
 * Time: 15:11
 */

namespace APP\Http\Controllers\Exporter;

use App\Http\Requests\KpiExportRequest;
use App\Http\Requests\Request;
use PHPExcel;
use PHPExcel_Writer_Excel2007;
use App\Http\Controllers\Controller;

class KpiExporter extends Controller
{
    /**
     * 导出指标概览报告。
     * @param KpiExportRequest $request
     * @return string $fileName
     */
    public function export(KpiExportRequest $request) {

        //创建EXCEL文档对象
        $excel = new PHPExcel();

        //创建Sheet关键三项
        //$excel对象初始包含一个sheet,创建新的sheet使用createSheet方法。
        $sheetKey3 = $excel->getSheet(0);
        $sheetKey3->setTitle('关键三项指标');

        //创建无线接通率Model
        $access = new AccessRateModel($request);
        $chartAccess = $access->toExcelChart()->setTopLeftPosition('A1')->setBottomRightPosition('T20');
        $arrayAccess = $access->toExcelArray();
        $sheetKey3->addChart($chartAccess);
        $sheetKey3->fromArray($arrayAccess,NULL,'A21');

        //创建无线掉线率Model
        $lost = new LostRateModel($request);
        $chartLost = $lost->toExcelChart()->setTopLeftPosition('A27')->setBottomRightPosition('T46');
        $arrayLost = $lost->toExcelArray();
        $sheetKey3->addChart($chartLost);
        $sheetKey3->fromArray($arrayLost,NULL,'A47');

        //创建切换成功率Model
        $handover = new HandoverRate($request);
        $chartHandover = $handover->toExcelChart()->setTopLeftPosition('A53')->setBottomRightPosition('T72');
        $arrayHandover = $handover->toExcelArray();
        $sheetKey3->addChart($chartHandover);
        $sheetKey3->fromArray($arrayHandover,NULL,'A73');

        //创建VoLte指标Sheet
        $sheetVoLte = $excel->createSheet(1);
        $sheetVoLte->setTitle('VoLte指标');
        //创建VoLte接入成功率Model
        $voLteAccess = new VolteAccessRate($request);
        $chartVoLteAccess = $voLteAccess->toExcelChart()->setTopLeftPosition('A1')->setBottomRightPosition('T20');
        $arrayVoLteAccess = $voLteAccess->toExcelArray();
        $sheetVoLte->addChart($chartVoLteAccess);
        $sheetVoLte->fromArray($arrayVoLteAccess,NULL,'A21');


        //创建ExcelWriter
        $writer = new PHPExcel_Writer_Excel2007($excel);
        $writer->setIncludeCharts(true);

        $writer->save('NetworkKpi.xlsx');
        return 'NetworkKpi.xlsx';
    }
}