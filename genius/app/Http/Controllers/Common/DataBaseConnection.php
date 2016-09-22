<?php

namespace App\Http\Controllers\Common;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;


use App\Task;
use App\User;
use App\DatabaseConn;
use PDO;
/**
 * @desc ：各地市服务器连接信息
 * Time：2016/08/04 16:32:21
*/
class DataBaseConnection
{
    /**
     * getCityOptions()
     * 获取城市--汉字转拼音
     * @param mixed $city
     * @return
     */
    public function getConnCity($citys){
        //print_r($citys);return;
        $dsn = "mysql:host=localhost;dbname=mongs";
        $db = new PDO($dsn, 'root', 'mongs');
        $cityArr = array();
        foreach ($citys as $city) {
            $sql = "select connName from databaseconn where cityChinese='" .$city . "'";
            $rows = $db->query($sql)->fetchall();
            foreach ($rows as $row) {
                $cityStr = $row['connName'];
                array_push($cityArr, $cityStr);         
            }   
        }
        return $cityArr;
    }
    
    /**
     * getCityOptions()
     * 获取下拉列表--城市
     * @param mixed $city
     * @return
     */
    public function getCityOptions(){
      //return 'text';
      $databaseConns = DB::select('select cityChinese from databaseconn group by cityChinese');
      $items = array();
      foreach ($databaseConns as $databaseConn) {
        $city = '{"text":"'.$databaseConn->cityChinese.'","value":"'.$databaseConn->cityChinese.'"}';
        array_push($items, $city);
      }
      return response()->json($items);
    }
    /**
     * getCityCategories()
     * 获取下拉列表--城市
     * @param mixed $city
     * @return
     */
    public function getCityCategories(){
      //return 'text';
      $databaseConns = DB::select("select case cityChinese
						when '常州' then 'changzhou'
						when '南通' then 'nantong'
						when '无锡' then 'wuxi'
						when '苏州' then 'suzhou'
						when '镇江' then 'zhenjiang'
						end as category
						from databaseconn group by cityChinese");
      return $databaseConns;
    }
     /**
     * getCityCategories()
     * 获取下拉列表--城市
     * @param mixed $city
     * @return
     */
    public function getCityByCityChinese($cityChinese){
      //return 'text';
      $databaseConns = DB::select("select case cityChinese
						when '常州' then 'changzhou'
						when '南通' then 'nantong'
						when '无锡' then 'wuxi'
						when '苏州' then 'suzhou'
						when '镇江' then 'zhenjiang'
						end as connName
						from databaseconn where cityChinese='".$cityChinese."'");
     return $databaseConns;
    }
    /**
     * getCityCategories()
     * 获取下拉列表--城市
     * @param mixed $city
     * @return
     */
    public function getCity_subNetCategories(){
      //return 'text';
      $databaseConns = DB::select("select case cityChinese
				when '常州' then 'changzhou'
				when '南通' then 'nantong'
				when '无锡' then 'wuxi'
				when '苏州' then 'suzhou'
				when '镇江' then 'zhenjiang'
				end as connName,GROUP_CONCAT(if(subNetworkFDD != '',CONCAT(subNetwork,',',subNetworkFDD),subNetwork)) subNetwork
				from databaseconn group by cityChinese");
      return $databaseConns;
    }
    /**
   * @desc ：重新拼接子网 供in 查询
   * Time：2016/07/01 18:40:04
   * @author Wuyou
   * @param 子网集合
   * @return 字符串
  */
  public function reCombine($subNetwork) {

    $subNetArr = explode(",", $subNetwork);
    $subNetworkArr = array();
    foreach ($subNetArr as $subNet) {
    	if (array_search($subNet, $subNetworkArr) === false) {
    		array_push($subNetworkArr, $subNet);
    	}
    }
    $subNetsStr = '';
    foreach ($subNetworkArr as $subNet) {
      $subNetsStr.= "'".$subNet."',";
    }

    return substr($subNetsStr,0,-1);
  }
  /**
   * @desc ：根据城市查询子网
   * Time：2016/07/02 15:48:21
   * @author Wuyou
   * @param 参数类型
   * @return 返回值类型
  */
  public function getSubNets($city) {

    $SQL = "select if(subNetworkFDD != '',CONCAT(subNetwork,',',subNetworkFDD),subNetwork) subNetwork from databaseconn where cityChinese = '$city'";
    $res = DB::select($SQL);
    $subNetworkArr = array();
    $subNetworkStr = '';
    foreach ($res as $value) {
    	$subNetworkStr .= $value->subNetwork.',';
    }
    $subNetworkStr = substr($subNetworkStr,0,-1);
    //$subNets = $res[0]->subNetwork;
    return $this->reCombine($subNetworkStr);
  }
  	/**
   	* @desc ：根据城市匹配相应的MR库
   	* Time：2016/07/02 15:48:21
   	* @author Wuyou
   	* @param 参数类型
   	* @return 返回值类型
  	*/
	public function getMRDatabase($cityChinese) {
		$database = '';
		switch ($cityChinese) {
			case '常州':
				$database = 'MR_CZ';
				break;
			case '南通':
				$database = 'MR_NT';
				break;
			case '苏州':
				$database = 'MR_SZ';
				break;
			case '无锡':
				$database = 'MR_WX';
				break;
			case '镇江':
				$database = 'MR_ZJ';
				break;
		}
		return $database;
    
	}
}
