<?php

namespace app\admin\controller\chaoshi;


use app\common\controller\Backend;
use app\common\model\FanyongStyle;
use think\Model;

class Group extends Backend
{

    protected $relationSearch = true;
    protected $searchFields = 'id';
    protected $multiFields = 'id,state';


    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\common\model\Chaoshigroup();
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

        }
        return parent::add();
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
        return parent::edit($ids);
    }

    public function multi($ids = "")
    {
        $row = $this->model->get($ids);
        $params = $this->request->post("params");

        if($params =='top=1'){
           $top=  $this->model->where('top',1)->find();
           if(!empty($top) && $top['id'] !=$ids){
               $this->error(__('你有一个被置顶了就不要操作了'));
           }else{
               $row->top=!$row->top;
               $row->save();
                $this->success();
           }
        }

    }
}