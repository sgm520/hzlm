<?php


namespace app\index\controller;


use think\Config;
use think\Controller;
use think\Request;

class Base extends Controller
{

    public function _initialize(Request $request = null)
    {

        if(empty(session('merchant'))){
            $this->error(__('Please login first'), 'index/user/login');
        }
        $site = Config::get("site");
        // 配置信息
        $config = [
            'site'           => array_intersect_key($site, array_flip(['name', 'cdnurl', 'version', 'timezone', 'languages'])),
        ];


        $this->assign('site', $site);
        parent::_initialize();

    }
}