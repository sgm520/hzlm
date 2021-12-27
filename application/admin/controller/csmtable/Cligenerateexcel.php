<?php
namespace app\admin\controller\csmtable;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use addons\csmtable\library\CsmRequest;
use addons\csmtable\library\CsmTableUtils;
use app\common\controller\Backend;
use think\App;

class Cligenerateexcel extends Backend
{

    protected $noNeedLogin = [
        "*"
    ];

    // private $uploadtmppath = ROOT_PATH . DS . 'runtime' . DS . 'tmp' . DS;
    private $uploadtmppath = RUNTIME_PATH . 'temp' . DS;

    private $xlstaskdao = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->xlstask = new \app\admin\model\csmtable\Xlstask();
    }

    /**
     * http://127.0.0.1/fastadmin_plugin_csmmeet/public/q3HJDu2RgE.php/csmtable/cligenerateexcel/index
     */
    public function index()
    {
        static::p('----generateExcelByClassname begin:');
        set_time_limit(0);
        $csmtable_xlstask_id = $this->request->request("csmtable_xlstask_id");

        $pp = $this->request->request("params");
        static::p($pp);
        static::p($csmtable_xlstask_id);
        $csmtable_xlstask_id = 119;
        $pp = '{"search":null,"filter":"{}","op":"{}","sort":"weigh","order":"desc","offset":"0","limit":"10","csmtable_classname":"app\/admin\/controller\/fa\/Test","csmtable_methodname":"index","csmtable_columns":"[{\"field\":\"id\",\"title\":\"ID\",\"formatter\":\"\",\"operate\":\"=\"},{\"field\":\"title\",\"title\":\"\u6807\u9898\",\"formatter\":\"\",\"operate\":\"=\"},{\"field\":\"admin_id\",\"title\":\"\u7ba1\u7406\u5458ID\",\"datasource\":\"auth\/admin\",\"datafield\":\"nickname\",\"formatter\":\"\",\"operate\":\"=\"},{\"field\":\"category_id\",\"title\":\"\u5206\u7c7bID(\u5355\u9009)\",\"datasource\":\"category\",\"formatter\":\"\",\"operate\":\"=\"},{\"field\":\"category_ids\",\"title\":\"\u5206\u7c7bID(\u591a\u9009)\",\"formatter\":\"\",\"operate\":\"=\"},{\"field\":\"week\",\"title\":\"\u661f\u671f(\u5355\u9009)\",\"formatter\":\"\",\"searchList\":{\"monday\":\"\u661f\u671f\u4e00\",\"tuesday\":\"\u661f\u671f\u4e8c\",\"wednesday\":\"\u661f\u671f\u4e09\"},\"operate\":\"=\"},{\"field\":\"flag\",\"title\":\"\u6807\u5fd7(\u591a\u9009)\",\"formatter\":\"\",\"searchList\":{\"hot\":\"\u70ed\u95e8\",\"index\":\"\u9996\u9875\",\"recommend\":\"\u63a8\u8350\"},\"operate\":\"FIND_IN_SET\"},{\"field\":\"genderdata\",\"title\":\"\u6027\u522b(\u5355\u9009)\",\"formatter\":\"\",\"searchList\":{\"male\":\"\u7537\",\"female\":\"\u5973\"},\"operate\":\"=\"},{\"field\":\"hobbydata\",\"title\":\"\u7231\u597d(\u591a\u9009)\",\"formatter\":\"\",\"searchList\":{\"music\":\"\u97f3\u4e50\",\"reading\":\"\u8bfb\u4e66\",\"swimming\":\"\u6e38\u6cf3\"},\"operate\":\"FIND_IN_SET\"},{\"field\":\"image\",\"title\":\"\u56fe\u7247\",\"formatter\":\"\",\"operate\":\"=\"},{\"field\":\"images\",\"title\":\"\u56fe\u7247\u7ec4\",\"formatter\":\"\",\"operate\":\"=\"},{\"field\":\"attachfile\",\"title\":\"\u9644\u4ef6\",\"formatter\":\"\",\"operate\":\"=\"},{\"field\":\"keywords\",\"title\":\"\u5173\u952e\u5b57\",\"formatter\":\"\",\"operate\":\"=\"},{\"field\":\"description\",\"title\":\"\u63cf\u8ff0\",\"formatter\":\"\",\"operate\":\"=\"},{\"field\":\"city\",\"title\":\"\u7701\u5e02\",\"formatter\":\"\",\"operate\":\"=\"},{\"field\":\"price\",\"title\":\"\u4ef7\u683c\",\"formatter\":\"\",\"operate\":\"BETWEEN\"},{\"field\":\"views\",\"title\":\"\u70b9\u51fb\",\"formatter\":\"\",\"operate\":\"=\"},{\"field\":\"startdate\",\"title\":\"\u5f00\u59cb\u65e5\u671f\",\"formatter\":\"\",\"operate\":\"RANGE\"},{\"field\":\"activitytime\",\"title\":\"\u6d3b\u52a8\u65f6\u95f4(datetime)\",\"formatter\":\"\",\"operate\":\"RANGE\"},{\"field\":\"year\",\"title\":\"\u5e74\",\"formatter\":\"\",\"operate\":\"=\"},{\"field\":\"times\",\"title\":\"\u65f6\u95f4\",\"formatter\":\"\",\"operate\":\"=\"},{\"field\":\"refreshtime\",\"title\":\"\u5237\u65b0\u65f6\u95f4(int)\",\"formatter\":\"Table.api.formatter.datetime\",\"operate\":\"RANGE\"},{\"field\":\"createtime\",\"title\":\"\u521b\u5efa\u65f6\u95f4\",\"formatter\":\"Table.api.formatter.datetime\",\"operate\":\"RANGE\"},{\"field\":\"updatetime\",\"title\":\"\u66f4\u65b0\u65f6\u95f4\",\"formatter\":\"Table.api.formatter.datetime\",\"operate\":\"RANGE\"},{\"field\":\"weigh\",\"title\":\"\u6743\u91cd\",\"formatter\":\"\",\"operate\":\"=\"},{\"field\":\"switch\",\"title\":\"\u5f00\u5173\",\"formatter\":\"\",\"searchList\":{\"0\":\"\u5426\",\"1\":\"\u662f\"},\"operate\":\"=\"},{\"field\":\"status\",\"title\":\"\u72b6\u6001\",\"formatter\":\"\",\"searchList\":{\"normal\":\"\u6b63\u5e38\",\"hidden\":\"\u9690\u85cf\"},\"operate\":\"=\"},{\"field\":\"state\",\"title\":\"\u72b6\u6001\u503c\",\"formatter\":\"\",\"searchList\":{\"0\":\"\u7981\u7528\",\"1\":\"\u6b63\u5e38\",\"2\":\"\u63a8\u8350\"},\"operate\":\"=\"}]","csmtable_xlspagesize":null}';
        $this->setProgress($csmtable_xlstask_id, 10);

        $params = json_decode($pp, true);

        $config = get_addon_config("csmtable");
        $adminaccount = $config["adminaccount"];

        CsmTableUtils::directLogin($adminaccount);

        $classname = str_replace('/', '\\', $this->getParamValue($params, 'csmtable_classname'));
        $methodname = $this->getParamValue($params, 'csmtable_methodname');

        $columnstr = $this->getParamValue($params, 'csmtable_columns');
        $columns = json_decode($columnstr, true);

        $excelPagesize = $this->getParamValue($params, 'csmtable_xlspagesize', 1000);
        $this->generateExcelByClassname($csmtable_xlstask_id, $classname, $methodname, $params, $columns, $excelPagesize);

        static::p('----generateExcelByClassname end:');
        return;
    }

    private function setProgress(&$csmtable_xlstask_id, $progress, $filename = '')
    {
        // $dao = new \app\admin\model\csmtable\Xlstask();
        // $this->xlstask->startTrans();
        $this->xlstask->where("id", "=", $csmtable_xlstask_id)->update([
            'progress' => $progress,
            'filename' => $filename,
            'updatetime' => time()
        ]);
        static::p('progress:' . $progress);
        // $dao->commit();
    }

    private function getParamValue(&$params, $key, $defaultvalue = null)
    {
        $sr = null;
        if (isset($params[$key])) {
            $sr = $params[$key];
        }
        $sr = ($sr == null) ? $defaultvalue : $sr;
        return $sr;
    }

    private function generateExcelByClassname(&$csmtable_xlstask_id, &$classname, &$methodname, &$params, &$columns, &$excelPagesize)
    {
        $pageno = 0; // 当前页数
        $pagesize = 1000;

        $excelRowIndex = 0; // 当前excel中的记录行数
        $excelRows = []; // Excel记录
        $excelFileNo = 1; // 第N个Excel
        $excelFiles = [];

        static::p("config excelPagesize:{$excelPagesize}");
        $request = CsmRequest::instance();
        $instance = new $classname($request);
        while (true) {
            $request->set('search', $this->getParamValue($params, 'search'));
            $request->set('filter', $this->getParamValue($params, 'filter'));
            $request->set('op', $this->getParamValue($params, 'op'));
            $request->set('sort', $this->getParamValue($params, 'sort'));
            $request->set('order', $this->getParamValue($params, 'order'));
            // $request->set('offset',$this->getParamValue($params,'offset'));
            $request->set('limit', $pagesize);
            $request->setMethodReturn("isAjax", true);
            $request->set("offset", $pageno * $pagesize);
            $sr = App::invokeMethod([
                $instance,
                $methodname
            ], null);
            $request->clear();
            if ($sr == null) {
                break;
            }

            $datarows = &$sr->getData()['rows'];
            $total = $sr->getData()['total'];

            static::p("--remote total:{$total}/pageno:{$pageno}/offset:" . $pageno * $pagesize);
            foreach ($datarows as &$row) {
                if ($excelRowIndex >= $excelPagesize) {
                    $progress = (int) ($pageno * $pagesize / $total * 70) + 10;
                    $this->setProgress($csmtable_xlstask_id, $progress);

                    static::p("------generate excel fileno:{$excelFileNo}/progress:{$progress}");
                    $excelFiles[] = static::saveExcel($columns, $excelRows, $excelFileNo);
                    $excelRowIndex = 0;
                    unset($excelRows);
                    $excelRows = [];
                    $excelFileNo ++;
                }
                $excelRows[] = $row;
                $excelRowIndex ++;
            }
            unset($datarows);
            unset($sr);
            $sr = null;

            if ($total <= $pageno * $pagesize) {
                break;
            }
            $pageno ++;
            // break;
        }

        // 有剩余的Excel row,就保存剩余的
        if ($excelRowIndex > 0) {
            static::p("--generate excel fileno:{$excelFileNo}");
            $excelFiles[] = static::saveExcel($columns, $excelRows, $excelFileNo);
        }
        // Excel保存到Zip
        $this->setProgress($csmtable_xlstask_id, 90);
        $zipfilename = static::saveExcelToZip($excelFiles);
        echo $zipfilename . '<BR>';
        $this->setProgress($csmtable_xlstask_id, 100, $zipfilename);
    }

    private function saveExcel(&$columns, &$rows, &$excelNo)
    {
        echo $excelNo . '<BR>';
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();
        $filename = 'excel-' . $excelNo;

        foreach ($columns as $k => $item) {
            $sheet->setCellValueByColumnAndRow($k + 1, 1, $item['title']);
        }

        $dsDatas = $this->getDataSourceDatas($columns, $rows);

        foreach ($rows as $k => $item) {
            foreach ($columns as $k2 => $column) {
                $vv = $item[$column['field']];
                $vv = $this->_convertValueByColumn($column, $vv, $dsDatas);
                $sheet->setCellValueByColumnAndRow($k2 + 1, $k + 2, $vv);
            }
        }
        unset($rows);
        unset($dsDatas);
        $filename = 'csmtable_' . time() . '_' . $excelNo . '.xlsx';
        $filepath = &$this->uploadtmppath;
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($filepath . $filename);
        unset($writer);
        $writer = null;
        return [
            'filename' => $filename,
            'filepath' => $filepath
        ];
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

                if ($im != null) {
                    $classname = $im[0];
                    $methodname = $im[1];

                    $request = CsmRequest::instance();
                    $request->setMethodReturn("isAjax", true);
                    $request->set('filter', '{"id":"' . implode(',', $ids) . '"}');
                    $request->set('op', '{"id":"in"}');
                    $request->set('sort', 'id');
                    $request->set('order', 'desc');

                    // \app\admin\controller\auth\Admin;
                    $instance2 = new $classname($request);
                    $json2 = App::invokeMethod([
                        $instance2,
                        $methodname
                    ], null);
                    $request->clear();

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
                foreach ($ssarr as $ssarrv) {
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
                $sr = date('Y-m-s h:i:s', $value);
            }
        } else if (isset($column['datasource']) && $column['datasource'] != null && $column['datasource'] != "") {
            // 时间型
            if (isset($dsDatas[$column['field']]) && $dsDatas[$column['field']] != null) {
                $dsDataitem = $dsDatas[$column['field']];
                if (isset($dsDataitem['ID#' . $value]) && $dsDataitem['ID#' . $value] != null) {
                    $sr = $dsDataitem['ID#' . $value];
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

    private function saveExcelToZip($excelFiles)
    {
        $zipfn = 'csmtable_' . time() . '.zip';
        $zipfilename = $this->uploadtmppath . $zipfn;
        $zip = new \ZipArchive();
        $zip->open($zipfilename, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        foreach ($excelFiles as $item) {
            $zip->addFile($item['filepath'] . $item['filename'], $item['filename']);
        }
        $zip->close();

        foreach ($excelFiles as $item) {
            unlink($item['filepath'] . $item['filename']);
        }
        return $zipfn;
    }
    
    private static function p($str){
        //echo( $str."<BR>\r\n" ) ; 
    }

}
