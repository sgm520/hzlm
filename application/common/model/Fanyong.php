<?php


namespace app\common\model;


use think\Model;
use app\admin\controller\auth;
use think\Session;

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

    protected $type = [
        'more'      =>  'json',
        'configjson'      =>  'json',
        'json'      =>  'json',

    ];

    // 追加属性
    protected $append = [
        'back_money',
    ];

    public function xilie(){
        return $this->belongsTo(Xilie::class,'state','id',[],'LEFT')->setEagerlyType(0);
    }

    public function bq(){
        return $this->belongsTo(Label::class,'label_id','id',[],'LEFT')->setEagerlyType(0);
    }
    public function getBackMoneyAttr($name)
    {


       if(Session::get('admin.id') !=1){
            $other=FangyongPrice::where('product_id',$this->getData('id'))->where('user_id',Session::get('admin.id'))->find();
            if($other){
                return  $other->price;
            }else{
                return $this->getData('money');
            }
       }else{
           return $this->getData('money');
       }
    }



}