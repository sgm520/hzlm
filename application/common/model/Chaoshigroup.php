<?php


namespace app\common\model;

use think\Model;

class Chaoshigroup extends Model
{
    // 表名,不含前缀
    protected $name = 'caoshi_group';
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;
    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
}