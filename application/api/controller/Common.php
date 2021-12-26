<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\common\exception\UploadException;
use app\common\library\Upload;
use app\common\model\Area;
use app\common\model\Version;
use fast\Random;
use OSS\OssClient;
use think\Config;
use think\Hook;

/**
 * 公共接口
 */
class Common extends Api
{
    protected $noNeedLogin = ['init'];
    protected $noNeedRight = '*';

    /**
     * 加载初始化
     *
     * @param string $version 版本号
     * @param string $lng     经度
     * @param string $lat     纬度
     */
    public function init()
    {
        if ($version = $this->request->request('version')) {
            $lng = $this->request->request('lng');
            $lat = $this->request->request('lat');

            //配置信息
            $upload = Config::get('upload');
            //如果非服务端中转模式需要修改为中转
            if ($upload['storage'] != 'local' && isset($upload['uploadmode']) && $upload['uploadmode'] != 'server') {
                //临时修改上传模式为服务端中转
                set_addon_config($upload['storage'], ["uploadmode" => "server"], false);

                $upload = \app\common\model\Config::upload();
                // 上传信息配置后
                Hook::listen("upload_config_init", $upload);

                $upload = Config::set('upload', array_merge(Config::get('upload'), $upload));
            }

            $upload['cdnurl'] = $upload['cdnurl'] ? $upload['cdnurl'] : cdnurl('', true);
            $upload['uploadurl'] = preg_match("/^((?:[a-z]+:)?\/\/)(.*)/i", $upload['uploadurl']) ? $upload['uploadurl'] : url($upload['storage'] == 'local' ? '/api/common/upload' : $upload['uploadurl'], '', false, true);

            $content = [
                'citydata'    => Area::getCityFromLngLat($lng, $lat),
                'versiondata' => Version::check($version),
                'uploaddata'  => $upload,
                'coverdata'   => Config::get("cover"),
            ];
            $this->success('', $content);
        } else {
            $this->error(__('Invalid parameters'));
        }
    }

    /**
     * 上传文件
     * @ApiMethod (POST)
     * @param File $file 文件流
     */
    public function upload()
    {
        //默认普通上传文件
        $config = get_addon_config('alioss');
        $oss = new OssClient($config['accessKeyId'], $config['accessKeySecret'], $config['endpoint']);
        $file = $this->request->file('file');
        //检测删除文件或附件
        $checkDeleteFile = function ($attachment, $upload, $force = false) use ($config) {
            //如果设定为不备份则删除文件和记录 或 强制删除
            if ((isset($config['serverbackup']) && !$config['serverbackup']) || $force) {
                if ($attachment && !empty($attachment['id'])) {
                    $attachment->delete();
                }
                if ($upload) {
                    //文件绝对路径
                    $filePath = $upload->getFile()->getRealPath() ?: $upload->getFile()->getPathname();
                    @unlink($filePath);
                }
            }
        };
        try {
            $upload = new Upload($file);
            $attachment = $upload->upload();
        } catch (UploadException $e) {
            $this->error($e->getMessage());
        }
        //文件绝对路径
        $filePath = $upload->getFile()->getRealPath() ?: $upload->getFile()->getPathname();

        $url = $attachment->url;
        try {
            $ret = $oss->uploadFile($config['bucket'], ltrim($attachment->url, "/"), $filePath);

            //成功不做任何操作
        } catch (\Exception $e) {
            $checkDeleteFile($attachment, $upload, true);
            $this->error("上传失败");
        }
        $checkDeleteFile($attachment, $upload);
        $this->success("上传成功",  ['url' => $url, 'fullurl' => cdnurl($url, true)]);


    }
}
