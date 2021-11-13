<?php
namespace app\admin\controller\csmtable;

use app\common\controller\Backend;
use app\admin\library\Auth;
/**
 * Excel下载任务管理
 *
 * @icon fa fa-circle-o
 */
class Csmxlstable extends Backend
{
    protected $noNeedRight = ["*"];
    private $uploadtmppath = RUNTIME_PATH . 'temp' . DS;

    /**
     * Xlstask模型对象
     *
     * @var \app\admin\model\csmtable\Xlstask
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\csmtable\Xlstask();
    }

    /**
     * 前台轮询查询下载进度
     * http://127.0.0.1/fastadmin_plugin_csmmeet/public/q3HJDu2RgE.php/csmtable/csmxlstable/queryGenerageStatus
     */
    public function queryGenerageStatus()
    {
        $filesource = $this->request->request("filesource");
        $auth = Auth::instance();

        $row = $this->model->where("admin_id", "=", $auth->id)
            ->where("filesource", '=', $filesource)
            ->where("status", "=", "normal")
            ->field("id,createtime,progress,iserror,errormsg")
            ->order("id", "desc")
            ->find();
        // echo $this->model->getLastSql();
        if ($row != null) {
            // $row->filesource = str_replace(Config::get('upload.cdnurl'), '', $row->filesource);
            $row->createtime = date('Y-m-d H:i:s', $row->createtime);
        }

        return $this->success('', null, [
            'row' => $row
        ]);
    }

    public function download()
    {
        $auth = Auth::instance();
        $id = $this->request->request("id");
        $row = $this->model->where("admin_id", "=", $auth->id)
            ->where("id", "=", $id)
            ->find();

        if ($row == null) {
            $this->error("文件不存在,请重新下载!");
        }
        $filename = $row->filename;
        //var_dump($filename);
        // $filename='csmtable_1588643591.zip';//完整文件名（路径加名字）
        if (! file_exists($this->uploadtmppath . $filename)) {
            header('HTTP/1.1 404 NOT FOUND');
        } else {
            $file = fopen($this->uploadtmppath . $filename, "rb");
            Header("Content-type: application/octet-stream");
            Header("Accept-Ranges: bytes");
            Header("Accept-Length: " . filesize($this->uploadtmppath . $filename));
            Header("Content-Disposition: attachment; filename=" . $filename);
            echo fread($file, filesize($this->uploadtmppath . $filename));
            fclose($file);
            exit();
        }
    }
}
