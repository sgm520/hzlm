<?php


namespace app\admin\logic;


use app\admin\model\User;
use app\common\model\UserBalanceLog;

class BalanceLogic
{
    static public function balance($user_id,$tel,$name,$change,$p_id,$description,$remark){
        $user = User::get($user_id);
        $user->setInc('income',$change);
        $user->setInc('ktx',$change);
        $user->save();
        $user_balance_log = [
            "user_id" =>   $user_id,
            "create_time" =>   time(),
            "tel" =>   $user->mobile,
            "name" =>   $name,
            "k_tel" =>   $tel,
            "change" =>   $change,
            "p_id" =>   $p_id,
            "description" =>   $description,
            "remark" =>   $remark,
        ];
        $result = UserBalanceLog::create($user_balance_log);
        return $result;
    }
}
