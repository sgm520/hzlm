<?php


namespace app\admin\controller\article;


use app\common\model\Article;
use app\common\controller\Backend;
use app\common\model\ChaoshiCategory;


class Content extends Backend
{

    protected $relationSearch = true;
    protected $searchFields = 'id';
    protected $multiFields = 'id,status';

    /**
     * @var \app\common\model\Article
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model =new Article();
    }

    /**
     * 查看
     */
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

    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $this->token();
        }
        $category=\app\common\model\ArticleCategory::where('status',1)->select();
        $this->assign('category',$category);
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
        $this->modelValidate = true;
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $category=\app\common\model\ArticleCategory::where('status',1)->select();
        $this->assign('category',$category);
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