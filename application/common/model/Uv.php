<?php


namespace app\common\model;


use think\Model;

class Uv extends Model
{

    protected $name = 'uv';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    public function caoshi(){
        return $this->hasOne(Chaoshi::class,'id','pid',[],'LEFT')->setEagerlyType(0);
    }
}