<?php

namespace app\admin\controller\fanyong;

use app\admin\logic\BalanceLogic;
use app\admin\model\User;
use app\common\controller\Backend;
use app\common\model\FangyongPrice;
use app\common\model\UserBalanceLog;
use fast\Random;
use think\Db;
use think\Validate;

class Fanyongorder extends Backend
{

    protected $relationSearch = true;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\common\model\Fanyongorder();
    }

    public function index()
    {

        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            if(!$this->auth->isSuperAdmin()){
                $admin=$this->auth->getUserInfo();
                $user=Db::name('user')->where('agent_id',$admin['code'])->column('id','id');
                $map['pid'] = ['in',$user];
            }else{
                $map['pid'] = ['neq','not null'];
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $list = $this->model
                ->with(['fanyong','code'])
                ->where($where)
                ->where($map)
                ->order($sort, $order)
                ->paginate($limit);
            $bq=Db::name('label')->column('name','id');
            foreach ($list as $k=>$v){
                $v->json=json_decode($v->json,true);
                $v->configjson=json_decode($v->configjson,true);
            }
            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        $this->assignconfig('adminId',$this->auth->id);
        $this->assignconfig('column',Db::name('keywords')->order('sort','desc')->column('value','key'));
        $this->assign('adminId',$this->auth->id);
        return $this->view->fetch();

    }

    /**
     * 授权
     */
    public function Authorization($ids){

        if($this->request->isAjax()){
            return parent::edit($ids);
        }
        $row=$this->model->get($ids);
        $this->assign('row',$row);
        return $this->view->fetch();
    }

    /**
     * 授权
     */
    public function agree($ids){
        $row=$this->model->get($ids);
        if($this->request->isAjax()){
            if(empty($row)){
                $this->error('订单不存在');
            }

            if(in_array($row->status,[2,3])){
//                $this->error('该状态无法修改');
            }
            $agent=Db::name('admin')->where('id',$row->agent_id)->find();
            $params['fmoney']=$row->agent_price;
            if($params){
                try {
                    $row->setInc('fmoney',$params['fmoney']);
                    $row->status=3; //已通过
                    $prifit=$row->price-$row->agent_price;
                    if($prifit){
                        Db::name('admin')->where('id',$row->agent_id)->setInc('ktx',$prifit);
                    }
                    $user_balance_log = [
                        "user_id" =>   $row->agent_id, //合伙人id
                        "create_time" =>   time(),
                        "tel" =>   $agent['username'],
                        "k_tel" =>   $agent['username'], //管理员
                        "change" =>   $prifit,
                        "p_id" =>   $row->p_id,
                        "description" => '合伙人返佣',
                        "remark" =>   $row->p_title,
                    ];
                    UserBalanceLog::create($user_balance_log);
                    if($row->save()){
                        $row->addfanyong();
                    }
                    Db::commit();
                } catch (\Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                $this->success();
            }
        }
        $row=$this->model->get($ids);
        $this->assign('row',$row);
        return $this->view->fetch();
    }

    public function refuse($ids){
        $ids = $ids ? $ids : $this->request->post("ids");
        $row = $this->model->get($ids);
        if($row->status !=1){
            $this->error('该状态无法修改');
        }
        $row->status=2;
        $row->save();
        $this->success();
    }

    public function multi_refuse($ids){
        foreach ($ids as $k => $v) {
            $res = $this->model->where('id', $ids[$k])->value('status');
            if ($res) {
                $data = [
                    'status' => 2
                ];
                $this->model->where('id', $ids[$k])->update($data);
            }
        }
        $this->success();
    }

    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $this->token();
        }
        return parent::add();
    }


    /**
     * 编辑
     */
    public function edit($ids = null)
    {
        if ($this->request->isPost()) {
            $this->token();
        }
        $row = $this->model->get($ids);
        $this->modelValidate = false;
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        return parent::edit($ids);
    }

    /**
     * 删除
     */
    public function del($ids = "")
    {
        if (!$this->request->isPost()) {
            $this->error(__("Invalid parameters"));
        }
        $ids = $ids ? $ids : $this->request->post("ids");
        $row = $this->model->get($ids);
        $row->delete();
        $this->modelValidate = true;
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $this->success();
    }

}
