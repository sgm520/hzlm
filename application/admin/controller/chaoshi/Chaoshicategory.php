<?php

namespace app\admin\controller\chaoshi;


use app\common\controller\Backend;
use app\common\model\Chaoshigroup;
use app\common\model\FanyongStyle;
use think\Model;

class Chaoshicategory extends Backend
{

    protected $relationSearch = true;
    protected $searchFields = 'id';
    protected $multiFields = 'id,state';


    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\common\model\ChaoshiCategory();
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
                ->with('group')
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
        $group = Chaoshigroup::where('status',1)->select();
        $this->assign('group', $group);
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
        $group = Chaoshigroup::where('status',1)->select();
        $this->assign('group', $group);
        return parent::edit($ids);
    }

}