<?php


namespace app\common\model;


use think\Model;

class ArticleCategory extends Model
{
    // 表名
    protected $name = 'article_category';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'update_time';
    protected $updateTime = 'create_time';
    // 追加属性
    protected $append = [
    ];
}