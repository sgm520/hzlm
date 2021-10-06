<?php


namespace app\common\model;


use app\admin\logic\BalanceLogic;
use think\Model;

class Fanyongorder extends Model
{

    protected $name = 'fanyong_order';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    // 追加属性
    protected $append = [
        'status_str',
        'tel_str'
    ];
    public function fanyong(){
        return $this->belongsTo(Fanyong::class,'p_id','id',[],'LEFT')->setEagerlyType(0);;
    }

    public function getTelStrAttr($name)
    {
        return hidtel($this->getAttr('tel'));
    }

    const status=[
           0=>'未通过',
            3 => '未通过',
            1 => '已结算',
            2 => '审核中',
        ];

    public function getStatusStrAttr($name)
    {

        return self::status[$this->status];
    }

    public   function addfanyong($name)
    {

        $userModel = new User();
        $user = $userModel->get($this->pid);
        if(empty($user)){
            return  true;
        }
        $remark = $this->p_title;
        /**
         * 申请用户
         */
        if ($user->id) {
            $description = "直推客户奖励";
            BalanceLogic::balance($user->id, $this->tel, $name, $this->fmoney, $this->p_id, $description, $remark);
        }
        if ($user) {
            $return_rate1 = 0.05;
            $return_rate2 = 0.02;
            $s_id = explode(",", $user->parent_path);
            $rate = 0;
            foreach ($s_id as $k => $v) {
                if ($k == 0) {
                    $rate = $return_rate1;
                } else if ($k == 1) {
                    $rate = $return_rate2;
                }else{
                    continue;
                }
                $sid_user = $userModel->get($v);
                if($sid_user){
                    $des_fmoney =  bcmul($this->fmoney,$rate,3);
                    if($des_fmoney>0){
                        $description = $k+1 . "级直推奖励";
                        BalanceLogic::balance($v, $this->tel, $name, $des_fmoney, $this->p_id, $description, $remark);
                    }
                }

            }
        }
    }

}