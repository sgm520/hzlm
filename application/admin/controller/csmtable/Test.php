<?php
namespace app\admin\controller\csmtable;

use app\common\controller\Backend;
use fast\Random;

/**
 * 测试管理
 *
 * @icon fa fa-circle-o
 */
class Test extends Backend
{

    /**
     * Test模型对象
     *
     * @var \app\admin\model\fa\Test
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\csmtable\Test();
        $this->view->assign("weekList", $this->model->getWeekList());
        $this->view->assign("flagList", $this->model->getFlagList());
        $this->view->assign("genderdataList", $this->model->getGenderdataList());
        $this->view->assign("hobbydataList", $this->model->getHobbydataList());
        $this->view->assign("statusList", $this->model->getStatusList());
        $this->view->assign("stateList", $this->model->getStateList());
        //$this->view->assign("totalviews", '142');
    }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */

    // http://127.0.0.1/fastadmin_plugin_1.2.0.20201008/public/KMAQCWvPYG.php/csmtable/test/generatedatas
    public function generatedatas()
    {
        $generatecount = 10;
        $subsieze = 1001;
        $count = $this->model->count();

        for ($i = 0; $i < $generatecount; $i ++) {
            $rows = [];

            for ($ii = 0; $ii < $subsieze; $ii ++) {
                $co = $i * $subsieze + $ii + $count;
                $param = [
                    'admin_id' => 1,
                    'category_id' => 1,
                    'category_ids' => '1,2',
                    'week' => 'monday',
                    'flag' => 'index',
                    'hobbydata' => 'music,swimming',
                    'city' => 'xxx',
                    'views' => Random::numeric(2),
                    'price' => 0,
                    'year' => 2020,
                    'status' => 'normal',
                    'state' => '1'
                ];
                $param['title'] = "我是{$co}篇测试文章" . time();
                $param['createtime'] = time();
                $param['content'] = Random::alpha(100);
                $rows[] = $param;
            }

            $this->model->saveAll($rows);
        }
        $this->success("生成完成记录" . $generatecount * $subsieze, null, null, '10000');
    }

    /**
     * 查看
     */
    public function index()
    {
        // 设置过滤方法
        $this->request->filter([
            'strip_tags'
        ]);
        if ($this->request->isAjax()) {
            // 如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list ($where, $sort, $order, $offset, $limit) = $this->buildparams();
            // var_dump($offset);
            $total = $this->model->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            // echo $this->model->getLastSql();

            $list = collection($list)->toArray();
            $result = array(
                "total" => $total,
                "rows" => $list,
                "totalviews" => 1530
            );

            return json($result);
        }
        return $this->view->fetch();
    }

    public function tt()
    {
        $id = $this->request->request('id');
        echo 'xxxxxxxxxxx';
    }
}
