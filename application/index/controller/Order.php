<?php

namespace app\index\controller;

use app\common\model\Fanyong;
use app\common\model\Fanyongorder;
use think\Controller;
use think\Db;
use think\Request;

class Order extends Base
{

    protected $noNeedLogin = [];
    protected $noNeedRight = '*';
    protected $layout = false;



    public function index()
    {

        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            $mimetypeQuery = [];
            $where = [];
            $filter = $this->request->request('filter');
            $filterArr = (array)json_decode($filter, true);
            if (isset($filterArr['mimetype']) && preg_match("/[]\,|\*]/", $filterArr['mimetype'])) {
                $this->request->get(['filter' => json_encode(array_diff_key($filterArr, ['mimetype' => '']))]);
                $mimetypeQuery = function ($query) use ($filterArr) {
                    $mimetypeArr = explode(',', $filterArr['mimetype']);
                    foreach ($mimetypeArr as $index => $item) {
                        if (stripos($item, "/*") !== false) {
                            $query->whereOr('mimetype', 'like', str_replace("/*", "/", $item) . '%');
                        } else {
                            $query->whereOr('mimetype', 'like', '%' . $item . '%');
                        }
                    }
                };
            }

            $model = new Fanyong();
            $offset = $this->request->get("offset", 0);
            $limit = $this->request->get("limit", 0);
            $total = $model
                ->where($where)
                ->where('merchant', $this->auth->id)
                ->order("id", "DESC")
                ->count();

            $list = $model
                ->where($where)
                ->where($mimetypeQuery)
                ->where('merchant', $this->auth->id)
                ->order("id", "DESC")
                ->limit($offset, $limit)
                ->select();

            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        $this->view->assign('title', "产品中心");

        return $this->view->fetch();
    }

    public  function order(){
        $this->assign('user',session('merchant'));
        return $this->view->fetch();
    }

    public function orderCenter(){
        $user=session('merchant');
        if($this->request->post()){
            $order_id=Db::name('fanyong')->where('merchant',$user['id'])->column('id','id');
            $map['p_id'] = ['in',$order_id];
            $limit=$this->request->post('limit',10);
            $this->model = new \app\common\model\Fanyongorder();
             $p_title=$this->request->post('p_title');
             $dl_status=$this->request->post('dl_status',1);
             if(!empty($p_title)){
                 $map['p_title']=['=',$p_title];
             }
            $map['dl_status']=['=',$dl_status];
            $list = $this->model
                ->with(['fanyong'])
                ->where($map)
                ->order('id','desc')
                ->paginate($limit);
            foreach ($list as $k=>$v){
                $v->json=json_decode($v->json,true);
                $v->configjson=json_decode($v->configjson,true);
                $v->apply_time=date("Y-m-d H:i:s",$v->time);
            }
            $result = array("total" => $list->total(), "rows" => $list->items(),'column'=>Db::name('keywords')->order('sort','desc')->column('value','key'));
            return json($result);
        }
        return $this->view->fetch();
    }

    public function productCenter(){
        return $this->view->fetch();
    }

    public  function actionOrder(){
        $id=$this->request->post('id');
        $dl_status=$this->request->post('dl_status');
        if(empty($id) ||  empty($dl_status)){
            return json([
                'code'=>0,
                'msg'=>'参数不对'
            ]);
        }
        $order=Fanyongorder::get($id);
        if(empty($order)){
            return json([
                'code'=>0,
                'msg='=>'订单不存在'
            ]);
        }
        if($order->dl_status !=1){
            return json([
                'code'=>0,
                'msg'=>'该状态不能操作'
            ]);
            $this->error('');
        }
        $order->dl_status=$dl_status;
        if($order->save()){
            return json([
                'code'=>1,
                'msg'=>'成功'
            ]);
        }

    }

}
