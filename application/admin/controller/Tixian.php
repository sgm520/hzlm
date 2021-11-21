<?php


namespace app\admin\controller;


use app\common\controller\Backend;
use app\common\model\User;
use think\Db;
use think\exception\ErrorException;

class Tixian extends Backend

{

     public function _initialize()
     {
              $this->model=new \app\common\model\Tixian();
      }

    /**
     * 通过提现
     */

    public function agree($ids){
        // $model ...
        $row = $this->model->get($ids);
        if($row->state !=2 || $row->state==1){
            $this->error(__('该状态无法提现'));
        }
        if($row->type ==1){
            try {
                $UserModel=\app\common\model\User::findOrFail($row->user_id);
            }catch (ErrorException $e){
                halt($e);
            }
            if(!$UserModel){
                return $this->response()->error('找不到该用户')->refresh();
            }
            $row->state=1;
            $row->save();
            $UserModel->setInc('tx',$row->money);
        }else{
            $row->state=1;
            $row->save();
             Db::name('admin')->where('id',$row->user_id)->setInc('ytx',$row->money);
        }
        $this->success(__('提现通过'));
    }

    public function refuse($ids){
        // $model ...
        $row = $this->model->get($ids);
        if($row->state !=2 || $row->state==0){
            $this->error(__('该状态无法提现'));
        }
        try {
            $UserModel=\app\common\model\User::findOrFail($row->user_id);
        }catch (ErrorException $e){
            halt($e);
        }
        if(!$UserModel){
            return $this->response()->error('找不到该用户')->refresh();
        }
        $row->state=0;
        $row->save();
        $UserModel->setInc('ktx',$row->money);
        $this->success(__('提现通过'));
    }

}