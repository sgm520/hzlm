<?php

namespace app\admin\controller\fanyong;


use app\common\controller\Backend;
use app\common\model\FanyongStyle;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;
use think\Model;

class Fanyong extends Backend
{

    protected $state = ["大额分期", "企业贷", "信用卡"];
    protected $relationSearch = true;
    protected $searchFields = 'id';
    protected $multiFields = 'id,status';


    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\common\model\Fanyong();
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
                ->with('style')
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
        $fanyongstyle = FanyongStyle::where('delete', 1)->select();
        $this->assign('style', $fanyongstyle);
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
        $fanyongstyle = FanyongStyle::where('delete', 1)->select();
        $this->assign('style', $fanyongstyle);
        return parent::edit($ids);
    }


}