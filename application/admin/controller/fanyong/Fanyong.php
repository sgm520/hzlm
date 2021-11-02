<?php

namespace app\admin\controller\fanyong;


use app\common\controller\Backend;
use app\common\model\FangyongPrice;
use app\common\model\FanyongStyle;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;
use think\Model;
use think\Session;

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
                ->with('xilie')
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
        $Xilie = \app\common\model\Xilie::select();
        $Labe = \app\common\model\FangyongLabel::select();
        $this->assign('xilie', $Xilie);
        $this->assign('Lable', $Labe);
        return $this->view->fetch();
    }

    public function edit($ids = NULL)
    {

        $row = $this->model->get($ids);
        $this->modelValidate = false;
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $Xilie = \app\common\model\Xilie::select();
        $Labe = \app\common\model\FangyongLabel::select();
        $this->assign('xilie', $Xilie);
        $this->assign('Lable', $Labe);
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $params = $this->preExcludeFields($params);
                $result = false;
                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                        $row->validateFailException(true)->validate($validate);
                    }
                    if (!$this->auth->isSuperAdmin()) {
                        if (isset($params['money']) && !empty($params['money'])) {
                            $fangprice = new FangyongPrice();
                            $other=$fangprice->where('product_id',$ids)->where('user_id',$this->auth->id)->find();
                            if(empty($other)){
                                $fangprice = new FangyongPrice();
                                $result=  $fangprice->allowField(true)->save(['product_id' => $ids, 'createtime' => time(),'price'=>$params['money'],'user_id'=>$this->auth->id]);
                            }else{
                                $result= $fangprice->allowField(true)->save([
                                    'price'  => $params['money'],
                                ],['product_id' => $ids, 'user_id'=>$this->auth->id]);
                            }
                        }
                    }else{

                        $result = $row->allowField(true)->save($params);
                    }


                    Db::commit();
                } catch (ValidateException $e) {
                    halt(44);
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    halt(44);
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (Exception $e) {
                    halt(44);
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                if ($result !== false) {
                    $this->success();
                } else {
                    $this->error(__('No rows were updated'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }


}