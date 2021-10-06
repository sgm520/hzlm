<?php


namespace app\common\model;


use think\Model;

class ChaoshiCategory extends Model
{
    // 表名
    protected $name = 'chaoshi_category';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'update_time';
    protected $updateTime = 'create_time';
    // 追加属性
    protected $append = [
        'categorystr'
    ];

    public function group(){
        return $this->hasOne(Chaoshigroup::class,'id','group_id',[],'LEFT')->setEagerlyType(0);
    }
    public function getCategoryStrAttr($name)
    {
        if($this->getAttr('category') ==1){
            return '网贷合集';

        }else if($this->getAttr('category') ==2){
            return   '系列大全';
        }
    }
}