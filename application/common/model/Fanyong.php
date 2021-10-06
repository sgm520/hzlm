<?php


namespace app\common\model;


use think\Model;
use function Couchbase\defaultDecoder;

class Fanyong extends Model
{
// 表名
    protected $name = 'fanyong';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    // 追加属性
    protected $append = [
    ];
    protected $type = [
        'more'      =>  'json'
    ];

    public function style(){
        return $this->belongsTo(FanyongStyle::class,'state','id',[],'LEFT')->setEagerlyType(0);;
    }




}