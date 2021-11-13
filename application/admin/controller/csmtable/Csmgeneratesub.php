<?php
namespace app\admin\controller\csmtable;

use addons\csmtable\library\ACsmgenerateControl;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use addons\csmtable\library\CsmTableUtils;
use addons\csmtable\library\CsmRequest;
use think\App;
use addons\csmtable\library\XLSXWriter;

/**
 * Excel下载任务管理
 *
 * @icon fa fa-circle-o
 */
class Csmgeneratesub extends ACsmgenerateControl
{

    public function _initialize()
    {
        parent::_initialize();
    }


//     public function index()
//     {
//         set_time_limit(0);
//         $b = time();
//         $bb = time();
//         $id = $this->request->request("id");
//         $fileno = $this->request->request("fileno");
//         $offset = (int) $this->request->request("offset");
//         $params = $this->getXlstaskParams($id);
//         $columns = json_decode($this->getParamValue($params, 'csmtable_columns'), true);

//         $b = self::t('t1', $b);
//         // 主账号登录,部分数据读取需要权限
//         CsmTableUtils::directLogin($this->adminaccount);
//         $b = self::t('t2', $b);
//         // 读取control数据
//         $controlData = $this->callRemoteControl($params, $offset, $this->excelmaxrecoredcount);

//         $datarows = &$controlData->getData()['rows'];
//         $b = self::t('t3', $b);

//         // 读取id换取名称的数组
//         $dsDatas = $this->getDataSourceDatas($columns, $datarows);
//         $b = self::t('t4', $b);
//         // Excel处理
//         if (true) {
//             // Excel写入标题行
//             $arrData = [];
//             $arrDataCol = [];
//             foreach ($columns as &$item) {
//                 $arrDataCol[] = &$item['title'];
//             }
//             $arrData[] = $arrDataCol;

//             $b = self::t('t5', $b);
            
//             // 写入Data数据
//             foreach ($datarows as &$item) {
//                 $arrDataCol = [];
//                 foreach ($columns as &$column) {
//                     $vv = $item[$column['field']];
//                     $arrDataCol[] = $this->_convertValueByColumn($column, $vv, $dsDatas);
//                 }
//                 $arrData[] = $arrDataCol;
//             }

//             $filename = $this->saveToExcel($arrData, $id, $fileno);

//             self::t('tall', $bb);
//             return json([
//                 'filename' => $filename
//             ]);
//         }
//     }
    /**
     * http://127.0.0.1:80/fastadmin_plugin_csmmeet/public/q3HJDu2RgE.php/csmtable/csmgeneratesub/index?id=125&fileno=1&offset=0
     */
 
    public  function index()
    {
        set_time_limit(0);
        ini_set('display_errors', 'on');  
        $b = time();
        $bb = time();
        $id = $this->request->request("id");
        $fileno = $this->request->request("fileno");
        $offset = (int) $this->request->request("offset");
        $params = $this->getXlstaskParams($id);
        $columns = json_decode($this->getParamValue($params, 'csmtable_columns'), true);
        
        $b = self::t('t1', $b);
        // 主账号登录,部分数据读取需要权限
        CsmTableUtils::directLogin($this->adminaccount);
        $b = self::t('t2', $b);
        
 
        $b = self::t('t4', $b);
        // Excel处理
        if (true) {
            // Excel写入标题行
            $arrData = [];
            $arrDataCol = [];
            foreach ($columns as &$item) {
                $arrDataCol[] = &$item['title'];
            } 
            $arrData[] = $arrDataCol;
            
            $b = self::t('t5', $b);
               
            $controlpage = 0;
            while(true){
                $offset2 = $this->controlpagesize*$controlpage + $offset;
                $limit = $this->controlpagesize;
                if(($this->controlpagesize*($controlpage+1)) > $this->excelmaxrecoredcount){
                    echo '=';
                    $limit = $this->excelmaxrecoredcount - $offset2;
                }
                static::p('offset2='.$offset2.'/'.$limit);
                $controlpage++;

     
                
                // 读取control数据
                $controlData = $this->callRemoteControl($params, $offset2, $limit);
                $datarows = &$controlData->getData()['rows'];
                //add by chensm@20200606 关联model, 比如格式如 ['admin'=>['id'=>1,'username'=>'admin]], v2.0.4
                //需要转换为column的格式  ['admin.id'=>1,'admin.username'=>'admin']
                foreach($datarows as $index=>&$row){
                    foreach($row as $k=>&$item){
                        if(is_array($item)){
                            foreach($item as $kk=>$vv){
                                $datarows[$index]["{$k}.{$kk}"] = $vv;
                            }
                        }
                    }
                }
                //2.1.10 在1.2.0版本下,关联下载为空的问题
                foreach($datarows as $index=>&$row){
                    if(is_object($row)){
                        foreach($row->getRelation() as $k=>&$item){
                            foreach($item->getData() as $kk=>$vv){
                                $datarows[$index]["{$k}.{$kk}"] = $vv;
                            }
                        }
                    }
                }
                
                // 读取id换取名称的数组
                $dsDatas = $this->getDataSourceDatas($columns, $datarows);

                // 写入Data数据
                foreach ($datarows as &$item) {
                    $arrDataCol = [];
                    foreach ($columns as &$column) {
                        
                        $vv = '';
                        if(isset($column['field']) && isset($item[$column['field']])){
                            $vv = $item[$column['field']];
                        }
                        
                        $arrDataCol[] = $this->_convertValueByColumn($column, $vv, $dsDatas);
                    }
                    $arrData[] = $arrDataCol;
                }
                //总记录数>excel总记录数,则说明要换个文件了
                if(($offset2+$limit)>=($this->excelmaxrecoredcount + $offset)){
                    break;
                }
                //2.1.10 excel读取速度优化,减少当记录数过少时会重复读取的问题 : 实际的获取的记录数<准备获取的记录数,说明已经完成读取
                if($limit>count($datarows)){
                    break;
                }
 
//                 var_dump($dsDatas);
//                 var_dump($datarows);die();
                static::p('--------------');
            }

            $filename = $this->saveToExcel($arrData, $id, $fileno);
            
            self::t('tall', $bb);
            return json([
                'filename' => $filename
            ]);
        }
    }

    private function saveToExcel(&$arrData, $id, $fileno)
    {
        $b = time();
        $writer = new XLSXWriter();
        $b = self::t('t6', $b);
        
        $writer->writeSheet($arrData);
        $b = self::t('t7', $b);
        //var_dump($arrData);die();
        $filename = 'csmtable-excel-' . $id . '-' . time() . '-' . $fileno . '.xls';
        $filepath = &$this->uploadtmppath;
        $writer->writeToFile($filepath . $filename);

        $b = self::t('t8', $b);

        return $filename;
    }

    private function t($name, $b)
    {
        $tt = time() - $b;
        static::p("{$name}:" . ($tt) . ' s');
        return time();
    }

    private function formateExcel(&$spreadsheet, $rownum, $columnnum)
    {
        $cc = "A";
        for ($i = 0; $i < 27; $i ++) {
            $spreadsheet->getActiveSheet()
                ->getColumnDimension($cc)
                ->setAutoSize(true);
            $cc ++;
        }
    }

    /**
     * 将value根据table的options转换成文字
     */
    private function _convertValueByColumn(&$column, &$value, &$dsDatas)
    {
        $sr = '';
        if (isset($column['searchList']) && $column['searchList'] != null) {
            // searchlist类型的,将code转为name
            $searchList = $column['searchList'];
            // operate类型,字典数组,用逗号分隔
            if (isset($column['operate']) && $column['operate'] != null && $column['operate'] == 'FIND_IN_SET') {
                $ssarr = explode(",", $value);
                $sslabel = [];
                foreach ($ssarr as &$ssarrv) {
                    if (isset($searchList[$ssarrv])) {
                        $sslabel[] = $searchList[$ssarrv];
                    } else {
                        $sslabel[] = $ssarrv;
                    }
                }
                $sr = implode(',', $sslabel);
            } else {
                // 普通字典
                if (isset($searchList[$value])) {
                    $sr = $searchList[$value];
                }
            }
        } else if (isset($column['formatter']) && $column['formatter'] != null && $column['formatter'] == "Table.api.formatter.datetime") {
            // 时间型
            if ($value != null && $value != '') {
                //2.1.7 修复日期格式错误
                $sr = date('Y-m-d H:i:s', $value);
            }
        } else if (isset($column['datasource']) && $column['datasource'] != null && $column['datasource'] != "") {
            // datasource id转name
//             if (isset($dsDatas[$column['field']]) && $dsDatas[$column['field']] != null) {
//                 $dsDataitem = $dsDatas[$column['field']];
//                 if (isset($dsDataitem['ID#' . $value]) && $dsDataitem['ID#' . $value] != null) {
//                     $sr = $dsDataitem['ID#' . $value];
//                 }
//             }
//             if ($sr == null || $sr == '') {
//                 $sr = $value;
//             }
            //chensm@20200626:支持一对多关联,用逗号分隔
            if (isset($dsDatas[$column['field']]) && $dsDatas[$column['field']] != null) {
                $dsDataitem = $dsDatas[$column['field']];
                if(stripos($value,",")===false){
                    if (isset($dsDataitem['ID#' . $value]) && $dsDataitem['ID#' . $value] != null) {
                        $sr = $dsDataitem['ID#' . $value];
                    }
                }else{  
                    $vvalue = explode(",",$value);
                    
                    $vtext = [];
                    foreach($vvalue as $iitem){
                        if (isset($dsDataitem['ID#' . $iitem]) && $dsDataitem['ID#' . $iitem] != null) {
                            $vtext[] = $dsDataitem['ID#' . $iitem];
                        }
                    }
                    $sr = implode(",",$vtext);
                }

            }
            if ($sr == null || $sr == '') {
                $sr = $value;
            }
            
        } else {
            $sr = $value;
        }
        return $sr;
    }

    private function getDataSourceDatas(&$columns, &$rows)
    {
        $sr = [];
        foreach ($columns as &$column) {
            if (isset($column['datasource']) && $column['datasource'] != null) {
                $datafield = null;
                if (isset($column['datafield']) && $column['datafield'] != null) {
                    $datafield = $column['datafield'];
                } else {
                    $datafield = 'name';
                }
                $ids = [];
                foreach ($rows as $item) {
                    $ids[] = $item[$column['field']];
                }

                $im = CsmTableUtils::getInstanceAndMethod($column['datasource']);
                
                
                $alias = '';
                if(isset($column['datasourcealias']) && $column['datasourcealias']!=null && $column['datasourcealias']!=''){
                    $alias = $column['datasourcealias'].'.';
                }

                if ($im != null) {
                    $classname = $im[0];
                    $methodname = $im[1];

                    $request = CsmRequest::instance();
                    $request->setMethodReturn("isAjax", true);
                    $request->set('filter', '{"'.$alias.'id":"' . implode(',', $ids) . '"}');
                    $request->set('op', '{"'.$alias.'id":"in"}');
                    $request->set('sort', $alias.'id');
                    $request->set('order', 'desc');

                    // \app\admin\controller\auth\Admin;
                    $instance2 = new $classname($request);
                    $json2 = App::invokeMethod([
                        $instance2,
                        $methodname
                    ], null);

                    if ($json2 == null) {
                        break;
                    }

                    $datarows = &$json2->getData()['rows'];

                    $vvs = [];
                    foreach ($datarows as &$row) {
                        $vv = null;
                        if (isset($row[$datafield])) {
                            $vv = $row[$datafield];
                        } else {
                            $vv = $row->$datafield;
                        }

                        $vvs['ID#' . $row['id']] = $vv;
                    }
                    unset($json2);
                    unset($instance2);
                    $instance2 = null;
                }
                $sr[$column['field']] = $vvs;
            }
        }
        return $sr;
    }
 
    
    
}




 