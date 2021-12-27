<?php
namespace app\api\controller;

use app\common\controller\Api;
use app\common\model\Option;
use think\Config;

class Setting extends Api {


    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    /**
     * 微信群和推广海报
     */
    public function app(){
        $appSettings  = Option::where('option_name', 'app')->value('option_value');
        $this->success(__('申请成功'), ['data'=>$appSettings]);

    }

    public function setting(){
        $state = $this->request->param("key");
        $pieces = explode("|", $state);
        foreach ($pieces as $k=>$v){
          $arr[$v]=  Config::get('site.'.$v);
        }
        $this->success(__('成功'), ['data'=>$arr]);
    }

}