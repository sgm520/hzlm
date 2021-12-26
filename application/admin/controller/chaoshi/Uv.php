<?php



namespace app\admin\controller\chaoshi;

use app\common\controller\Backend;

class Uv extends Backend
{
    protected $relationSearch = true;
    protected $searchFields = 'id';
    protected $multiFields = 'id';


    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\common\model\Uv();
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
                ->with('caoshi')
                ->whereTime('uv.create_time','today')
                ->where($where)
                ->order($sort, $order)
                ->paginate($limit);
            $result = array("total" => $list->total(), "rows" => $list->items());
            return json($result);
        }
        return $this->view->fetch();
    }
}