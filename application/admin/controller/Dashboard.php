<?php

namespace app\admin\controller;

use app\admin\model\Admin;
use app\admin\model\User;
use app\common\controller\Backend;
use app\common\model\Attachment;
use fast\Date;
use think\Db;

/**
 * 控制台
 *
 * @icon   fa fa-dashboard
 * @remark 用于展示当前系统中的统计数据、统计报表及重要实时数据
 */
class Dashboard extends Backend
{

    protected $noNeedRight=['index','bind','withdrawal'];

    /**
     * 查看
     */
    public function index()
    {
        try {
            \think\Db::execute("SET @@sql_mode='';");
        } catch (\Exception $e) {

        }
        $column = [];
        $starttime = Date::unixtime('day', -6);
        $endtime = Date::unixtime('day', 0, 'end');
        $joinlist = Db("user")->where('jointime', 'between time', [$starttime, $endtime])
            ->field('jointime, status, COUNT(*) AS nums, DATE_FORMAT(FROM_UNIXTIME(jointime), "%Y-%m-%d") AS join_date')
            ->group('join_date')
            ->select();
        for ($time = $starttime; $time <= $endtime;) {
            $column[] = date("Y-m-d", $time);
            $time += 86400;
        }
        $userlist = array_fill_keys($column, 0);
        foreach ($joinlist as $k => $v) {
            $userlist[$v['join_date']] = $v['nums'];
        }
        $map['group_id']=0;
        $info=$this->auth->getUserInfo();
        if(!$this->auth->isSuperAdmin()){
            $map['agent_id']=$info['code'];
        }
        $info= Db::name('admin')->where('id',$this->auth->id)->find();
        $this->view->assign([
            'totaluser'       => User::where($map)->count(),
            'ktx'      =>$info['ktx'],
            'ytx'      => $info['ytx'],
            'al_pay_name'=>$info['al_pay_name'],
            'al_pay_account'=>$info['al_pay_account']
        ]);

        $this->assignconfig('column', array_keys($userlist));
        $this->assignconfig('userdata', array_values($userlist));

        return $this->view->fetch();
    }

    public  function bind(){
        $params = $this->request->post("row/a");
        $this->model = model('Admin');
        $row = $this->model->get(['id' => $this->auth->id]);
        $result = $row->save($params);
        if($result){
            $this->success('绑定成功','index');
        }else{
            $this->success('绑定失败','index');
        }
    }

    public function withdrawal(){
        $info= Db::name('admin')->where('id',$this->auth->id)->find();
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if (!is_numeric($params['money']) || $params['money'] <= 0) {
                return $this->error(__('金额错误'));
            }
            if($info['ktx']<$params['money']){
                $this->error(__("余额不足不能提现",'index'));
            }

            $id=Db::name('tixian')->insertGetId([
                'money'=>$params['money'],
                'tx_time'=>time(),
                'state'=>2,
                'user_login'=>$info['username'],
                'user_id'=>$this->auth->id,
                'al_pay_name'=>$info['al_pay_name'],
                'al_pay_account'=>$info['al_pay_account'],
                'type'=>2,
                'remark'=>'管理员申请提现',

            ]);
            if($id){
                Db::name('admin')->where('id',$this->auth->id)->setDec('ktx',$params['money']);
//                Db::name('admin')->where('id',$this->auth->id)->setInc('ytx',$params['money']);
                $this->success('提现成功');
            }else{
                $this->success('提现失败');
            }
        }

        $this->view->assign([
            'al_pay_name'=>$info['al_pay_name'],
            'ktx'      =>$info['ktx'],
            'al_pay_account'=>$info['al_pay_account']
        ]);
        return $this->view->fetch();
    }

}
