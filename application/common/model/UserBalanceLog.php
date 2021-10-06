<?php


namespace app\common\model;


use think\Model;

class UserBalanceLog extends Model
{
// 表名,不含前缀
    protected $name = 'user_balance_log';
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;
    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;

    // 追加属性
    protected $append = [
        'tel_str',
        'k_tel_str',
    ];

    public function getTelStrAttr($name)
    {
        return hidtel($this->getAttr('tel'));
    }
    public function getKTelStrAttr($name)
    {
        return hidtel($this->getAttr('k_tel'));
    }

    public function fanyong(){
        return $this->belongsTo(Fanyong::class,'p_id','id',[],'LEFT')->setEagerlyType(0);;
    }
}