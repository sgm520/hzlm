<?php

namespace app\admin\controller\fanyong;

use app\admin\logic\BalanceLogic;
use app\admin\model\User;
use app\common\controller\Backend;
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
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
//            halt($where);
            $list = $this->model
                ->with('fanyong')
                ->where($where)
                ->order($sort, $order)
                ->paginate($limit);
            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }

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
            $params = $this->request->post("row/a");
            if($params){
                try {
                    $row->setInc('xlines',$params['xlines']);
                    $row->setInc('fmoney',$params['fmoney']);
                    $row->status=1;
                    if($row->save()){
                        $row->addfanyong($row->getAttr('name'));
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
        if($row->status !=2){
            $this->error('该状态无法修改');
        }
        $row->status=3;
        $row->save();
        $this->success();
    }

    public function multi_refuse($ids){
        foreach ($ids as $k => $v) {
            $res = $this->model->where('id', $ids[$k])->value('status');
            if ($res) {
                $data = [
                    'status' => 0
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
