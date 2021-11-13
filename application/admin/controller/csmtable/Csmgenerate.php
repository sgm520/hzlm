<?php
namespace app\admin\controller\csmtable;

use GuzzleHttp\Client;
use addons\csmtable\library\ACsmgenerateControl;
use addons\csmtable\library\CsmTableUtils;

class Csmgenerate extends ACsmgenerateControl
{
    
    public function _initialize()
    {
        parent::_initialize();
    }
    
    // http://127.0.0.1/fastadmin_plugin_csmmeet/public/q3HJDu2RgE.php/csmtable/csmgenerate/test?id=192
    public function test()
    {
        
        $id = $this->request->request("id");
        $config = get_addon_config("csmtable");
        $privatehosturl = $config["privatehosturl"];
        // $url = "http://127.0.0.1/fastadmin_plugin_csmmeet/public/q3HJDu2RgE.php/csmtable/cligenerateexcel/index";
        $privatehosturl = ($privatehosturl != null && $privatehosturl != '') ? $privatehosturl : ($_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["SERVER_NAME"]);
        
        $url = $privatehosturl . $this->request->baseFile() . '/csmtable/csmgenerate/index?id=' . $id;
        // $url = "https://fademo.163fan.com/1oSm3piAar.php/csmtable/csmgenerate/test?id=171";
        static::p($url);
        
        $this->callremote2($url);
    }
    
    private function callremote2($url)
    {
        $sr = \fast\Http::sendRequest($url);
        var_dump($sr);die();
    }
    
    /**
     * http://127.0.0.1/fastadmin_plugin_csmmeet/public/q3HJDu2RgE.php/csmtable/csmgenerate/index?id=192
     * 1.获取记录条数,切分任务
     * 2.根据返回,处理zip
     */
    public function index($ids=null)
    {
        static::p('----generateExcelByClassname begin:');
        set_time_limit(0);
        // 根据id获取xlstask row
        $id = $this->request->request("id");
        
        //v2.1.8 增加日志和查询日志调试功能,便于排查问题(从下载任务的[测试生成Excel]按钮访问)
        if(!empty($ids)){
            $id = $ids;
        }
        
        $params = $this->getXlstaskParams($id);
        $this->setProgress($id, 10);
        
        // 主账号登录,部分数据读取需要权限
        CsmTableUtils::directLogin($this->adminaccount);
        
        
        // 按记录数切分多个任务
        $recordtotal = $this->getDataRecordTotal($params);
        
        $excelCount = (int) ($recordtotal / $this->excelmaxrecoredcount); // 总记录数/单个xls记录数=200/100=2
        
        if ($recordtotal == 0) {
            // 没有数据,则报错
            $this->setErrorXlstask($id, "没有数据，无法下载");
            static::p('----generateExcelByClassname end:');
            return;
        }

        $result = '';
        $excelFiles = [];
        for ($i = 0; $i <= $excelCount; $i ++) {
            static::p($i);
            $fileno =  ($i + 1) ; 
            $offset = $i * $this->excelmaxrecoredcount;
            $result .= 'Row'.($i+1).' - '.$this->getSubTaskUrl()."?id={$id}&fileno={$fileno}&offset={$offset}";
            $subtasksrstr = $this->callremote($this->getSubTaskUrl(), [
                "id" => $id,
                "fileno" =>$fileno,
                'offset' => $offset
            ]);
            $result .= ' - ' . $subtasksrstr .'\r\n<BR>';
            
  
            $progress = (int) 80 * ($i + 1) / ($excelCount + 2) + 10;
            $this->setProgress($id, $progress);
            
            $subtasksr = json_decode($subtasksrstr, true);
            
            if ($subtasksr != null && $subtasksr['filename'] != null) {
                $excelFiles[] = $subtasksr;
                static::p($subtasksr);
            } else {
                $this->setErrorXlstask($id, '下载报错,可能是PHP内存设置过小或数据查询报错造成');
                return;
            }
        }
        
        $this->setProgress($id, 90);
        $zipfilename = static::saveExcelToZip($excelFiles);
        $this->setProgress($id, 100, $zipfilename,$result);
        static::p('----generateExcelByClassname end:'.$result);
        //v2.1.8 增加日志和查询日志调试功能,便于排查问题
        echo $result.'<BR>完成生成'.$zipfilename;
        return;
    }
    
    private function callremote($url, $param)
    {
        static::p('-------------------------------------------begin');
        static::p('callremote.url=' . $url . '?id=' . $param['id'] . '&offset=' . $param['offset'] . '&fileno=' . $param['fileno']);
        
        $client = new Client();
        
        $res = $client->request('GET', $url, [
            'query' => $param
        ])->getBody();
        $sr = (string) $res;
        static::p('callremote.sr=' . $sr);
        static::p('-------------------------------------------end');
        return $sr;
    }
    
    public function saveExcelToZip(&$excelFiles)
    {
        $zipfn = 'csmtable_' . time() . '.zip';
        $zipfilename = $this->uploadtmppath . $zipfn;
        $zip = new \ZipArchive();
        $zip->open($zipfilename, \ZipArchive::CREATE | \ZipArchive::CREATE);
        static::p('saveExcelToZip');
        static::p($excelFiles);
        foreach ($excelFiles as $item) {
            $zip->addFile($this->uploadtmppath . $item['filename'], $item['filename']);
        }
        $zip->close();
        
        foreach ($excelFiles as $item) {
            unlink($this->uploadtmppath . $item['filename']);
        }
        return $zipfn;
    }
    
    
    // 获取子任务的url
    private function getSubTaskUrl()
    {
        $privatehosturl = $this->privatehosturl;
        $privatehosturl = ($privatehosturl != null && $privatehosturl != '') ? $privatehosturl : ($_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["SERVER_NAME"]);
        return $privatehosturl . $this->request->baseFile() . '/csmtable/csmgeneratesub/index';
    }
    
    private function setProgress(&$csmtable_xlstask_id, $progress, $filename = '',$errormsg='')
    {
        $this->xlstaskdao->where("id", "=", $csmtable_xlstask_id)->update([
            'progress' => $progress,
            'filename' => $filename,
            'updatetime' => time(),
            'errormsg'=>$errormsg//v2.1.8 增加日志和查询日志调试功能,便于排查问题
        ]);
        static::p('progress:' . $progress);
    }
    
    private function setErrorXlstask($csmtable_xlstask_id, $errormsg)
    {
        $this->xlstaskdao->where("id", "=", $csmtable_xlstask_id)->update([
            'iserror' => 'Y',
            'errormsg' => substr($errormsg, 0, 1000),
            'updatetime' => time()
        ]);
        static::p('progress:' . $errormsg);
    }
}
