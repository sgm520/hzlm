<?php


namespace app\common\model;


use think\Db;
use think\Model;

class FanyongTian extends  Model
{
// 表名
    protected $name = 'tixian';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'update_time';
    protected $updateTime = 'create_time';

    // 追加属性
    protected $append = [
    ];
    const  state=[
            0 => '拒绝',
            1 => '同意',
            2 => '待审核',
            3 => '其它',
        ];

}