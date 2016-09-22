<?php

namespace App\Http\Controllers\complaintHandling;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use PDO;
use Illuminate\Support\Facades\Auth;

class signalingAnalysisController extends Controller{

	public function queryKeyword(){

		$keyword = input::get("keyword");
		$command="sudo common/sh/queryKeyword.sh ".$keyword;
		exec($command,$output,$ret);
		$result=[];
		if($ret !=0){
			$result['state']="查询异常状态码：".$ret;
        	echo json_encode($result);
        	return;
        }

		$mongs_home="/opt/lampp/htdocs/genius/public/";
		$filename="common/files/log/信令分析_关键词".$keyword."_".date('YmdHis').".log";
		$log = $mongs_home.$filename;
		$command1="sudo common/sh/FileFromHDFS.sh ".$log;
        exec($command1,$output,$ret);
        if($ret ==0){
        	$handle = fopen($filename, 'r');
        	$contents = fread($handle, filesize ($filename));
    		fclose($handle);
    		$result['state']=$ret;
    		$result['contents'] = $contents;
        }else{
        	$result['state']="生成文件异常状态码：".$ret;
        }
        echo json_encode($result);
	}
}