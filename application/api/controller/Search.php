<?php
namespace app\api\controller;

use app\common\controller\Api;
use app\common\model\Article;
use app\common\model\Fanyong;

class Search extends Api{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    /*
     * 文章搜索API
     */
    public function search_article(){
        $title = $this->request->param("title",'');
        if(empty($title)){
            $this->success(__('成功'), []);
        }
        $articleModel = new Article();
        $articleAll = $articleModel->where("title","like","%$title%")->select();
        if($articleAll){
            $this->success(__('成功'), ['data'=>$articleAll]);
        }else{
            $this->success(__('成功'), []);
        }

    }


    /**
     * 返佣产品搜索
     */
    public function search_fanyong(){
        $title = $this->request->param("name");
        $fanyongModel = new Fanyong();
        if(empty($title)){
            $this->success(__('成功'), []);
        }
        $fanyongAll = $fanyongModel->where('status',1)->where("name","like","%$title%")->select();
        if($fanyongAll){
            $this->success(__('成功'), ['data'=>$fanyongAll]);
        }else{
            $this->success(__('成功'), []);
        }
    }



}