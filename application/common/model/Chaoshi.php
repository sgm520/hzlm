<?php


namespace app\common\model;


use think\Model;

class Chaoshi extends Model
{
    // 表名
    protected $name = 'chaoshi';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    // 追加属性
    protected $append = [
        'loan_label_str'
    ];
    protected $type = [
        'remarks'      =>  'json'
    ];


    public function category(){
       return $this->hasOne(ChaoshiCategory::class,'id','category',[],'LEFT')->setEagerlyType(0);
    }
    const loan_label=[0 => '无', 1 => '放水',2 =>'爆款',3=>'最新'];

    public function getLoanLabelStrAttr($name)
    {
        if(isset(self::loan_label[$this->getAttr('loan_label')])){
            return self::loan_label[$this->getAttr('loan_label')];
        }else{
            return ;
        }

    }
}