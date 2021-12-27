<?php


namespace app\admin\logic;


use app\admin\model\User;

class UserBalanceLogLogic
{

    /**
     * 给用户送钱
     * cmf_user_balance_log
     * @param $userId  用户id
     * @param $change   金额
     * @param $remark   备注
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function giving($userId,$change,$remark){
        $user = User::find($userId);
        $balance = $user["balance"];

        $ia = ["user_id"=>$userId,"create_time"=>time(),"tel"=>$user['tel'],"change"=>$change,"description"=>"注册会员","remark"=>$remark,"balance"=>$balance];
        $result = Db("user_balance_log")->insert($ia);
        if ($result){
            $user->save(['income'=>(int)($user->income + 5)]);
            return true;
        }
        return false;
    }
}