<?php


namespace app\common\model;


use think\Model;

class FangyongPrice extends Model
{
    protected $name = 'fangyong_price';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;

}