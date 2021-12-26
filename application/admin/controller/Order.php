<?php


namespace app\admin\controller;


use app\common\controller\Backend;
use app\common\model\Fanyongorder;

class Order extends Backend
{

    public function _initialize()
    {
        $this->model=new Fanyongorder();
    }


}