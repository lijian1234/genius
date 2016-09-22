<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SwitchController extends Controller
{
    //
    public function index()
    {
        return view('network.switch');
    }

    public function getswitchSite(Request $request) {

        $sql = 'select cellName,longitude,latitude,dir,band '.'from siteLte;';

        $result = DB::connection('mongs')->select($sql);

        return json_encode($result);
    }

    public function getswitchData(Request $request) {
        $date = $request['date'];
        $cell = $request['cell'];

        $sql = 'select scell,mlongitude,mlatitude,mdir,mband'.
            ',slongitude,slatitude,sdir,sband,执行切换失败数 as failCount,准备切换尝试数 as handoverAttemptCount,切换成功率 as handoverSuccessRatio from SysRelation_cell_day where day_id = \''.$date.'\' and cell = \''.$cell.'\' and slongitude is not null and slatitude is not null and sdir is not null and sband is not null and scell is not null;';

        $result = DB::connection('autokpi')->select($sql);
        //dd($result);

        return json_encode($result);
    }

    public function getHandOverIn(Request $request) {
        $date = $request['date'];
        $cell = $request['cell'];

        $sql = 'select cell,mlongitude,mlatitude,mdir,mband'.
            ',slongitude,slatitude,sdir,sband,执行切换失败数 as failCount,准备切换尝试数 as handoverAttemptCount,切换成功率 as handoverSuccessRatio from SysRelation_cell_day where day_id = \''.
            $date.'\' and scell = \''.$cell.'\'';

        $result = DB::connection('autokpi')->select($sql);
        //dd($result);

        return json_encode($result);
    }

    public function getswitchDetail(Request $request) {
        $date = $request['date'];
        $scells = $request['scells'];
        $cell = $request['cell'];
        
        $sql = 'select * '.
            'from SysRelation_cell_day where day_id = \''.
            $date.'\' and cell = \''.$cell.'\' and scell in (\''.implode("','",$scells).'\');';
        
        $result = DB::connection('autokpi')->select($sql);
        //dd($result);
        $reData['data'] = $result;

        return json_encode($reData);
    }

    public function getHandOverInDetail(Request $request) {
        $date = $request['date'];
        $cell = $request['cell'];
        $cells = $request['cells'];

        $sql = 'select * '.
            'from SysRelation_cell_day where day_id = \''.
            $date.'\' and scell = \''.$cell.'\' and cell in (\''.implode("','",$cells).'\');';

        $result = DB::connection('autokpi')->select($sql);
        //dd($result);
        $reData['data'] = $result;

        return json_encode($reData);
    }
}