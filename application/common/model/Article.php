<?php


namespace app\common\model;


use think\Model;

class Article extends Model
{

    // 表名
    protected $name = 'article';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    // 追加属性
    protected $append = [
        'create_time_text'
    ];

    public function category(){
       return $this->belongsTo(ArticleCategory::class,'category','id',[],'LEFT')->setEagerlyType(0);
    }

    public function getCreateTimeTextAttr($name)
    {
        return date('Y-m-d h:i:s', $this->getAttr('create_time'));
    }
}