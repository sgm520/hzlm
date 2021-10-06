<?php

namespace app\admin\controller\chaoshi;


use app\common\controller\Backend;
use app\common\model\ChaoshiCategory;
use app\common\model\FanyongStyle;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;
use think\Model;

class Chaoshi extends Backend
{

    protected $relationSearch = true;
    protected $searchFields = 'id';
    protected $multiFields = 'id,status';


    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\common\model\Chaoshi();
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
            $list = $this->model
                ->with('category')
                ->where($where)
                ->order($sort, $order)
                ->paginate($limit);
            $result = array("total" => $list->total(), "rows" => $list->items());
            return json($result);
        }
        return $this->view->fetch();
    }

    public function add($ids = NULL)
    {
        if ($this->request->isPost()) {
            $this->token();
            return parent::add();
        }
        $category = ChaoshiCategory::where('state', 1)->select();
        $this->assign('category', $category);
        return $this->view->fetch();
    }

    public function edit($ids = NULL)
    {
        if ($this->request->isPost()) {
            $this->token();
        }
        $row = $this->model->get($ids);
        $this->modelValidate = false;
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $category = ChaoshiCategory::where('state', 1)->select();
        $this->assign('category', $category);
        return parent::edit($ids);
    }
    public function batchUpdateTime(){
        $ids=input();

        if(count($ids['ids']) ==0){
            $this->error(__('No Results were found'));
        }
        $list=[];
        foreach ($ids['ids'] as $k=>$v){
            $list[]=['id'=>$v,'update_time'=>time()];
        }
        Db::startTrans();
        try {
            $result= $this->model->saveAll($list);
            Db::commit();
        } catch (ValidateException $e) {
            Db::rollback();
            $this->error($e->getMessage());
        } catch (PDOException $e) {
            Db::rollback();
            $this->error($e->getMessage());
        } catch (Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if ($result !== false) {
            $this->success();
        } else {
            $this->error(__('No rows were updated'));
        }

    }
}