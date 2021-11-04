<?php

namespace app\admin\model\fankui;

use app\common\model\User;
use think\Model;


class Fankui extends Model
{

    

    

    // 表名
    protected $name = 'fankui';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];


    public function user(){
        return $this->belongsTo(User::class,'user_id','id')->setEagerlyType(0);
    }

    







}
