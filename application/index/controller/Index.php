<?php

namespace app\index\controller;


class Index extends Base
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = '';

    public function index()
    {

        return $this->view->fetch();
    }

}
