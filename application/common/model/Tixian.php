<?php


namespace app\common\model;


use think\Model;

class Tixian extends Model
{
    // 表名
    protected $name = 'tixian';
    // 开启自动写入时间戳字段
    // 定义时间戳字段名
    protected $autoWriteTimestamp=false;
    // 追加属性
    protected $append = [
    ];
    const  refuse=0;
    const  success=1;

}