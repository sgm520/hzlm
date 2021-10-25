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

    ];

    public function xilie(){
        return $this->belongsTo(Xilie::class,'state','id',[],'LEFT')->setEagerlyType(0);;
    }

    public function getMoneyAttr($name)
    {
       if(Session::get('admin.id') !=1){
            $other=FangyongPrice::where('product_id',$this->getAttr('id'))->where('user_id',Session::get('admin.id'))->find();
            if($other){
                return  $other->price;
            }else{
                return $this->value('money');
            }
       }else{
           return $this->value('money');
       }
    }


}