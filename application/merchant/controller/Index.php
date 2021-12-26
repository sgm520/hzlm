<?php


namespace app\merchant\controller;

class Index extends \app\common\controller\Merchantend
{


    protected $noNeedLogin = ['login'];
    protected $noNeedRight = ['index', 'logout'];
    protected $layout = '';

    public function index()
    {
    }

    public function login(){}

    

}