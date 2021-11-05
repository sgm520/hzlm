<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-present http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Released under the MIT License.
// +----------------------------------------------------------------------
// | Author: 老猫 <thinkcmf@126.com>
// +----------------------------------------------------------------------

namespace app\api\controller;

use app\admin\model\User;
use app\common\controller\Api;
use app\common\model\Article;
use app\common\model\ArticleCategory;
use app\common\model\Chaoshi;
use app\common\model\ChaoshiCategory;
use app\common\model\Chaoshigroup;
use app\common\model\FangyongLabel;
use app\common\model\FangyongPrice;
use app\common\model\Fanyong;
use app\common\model\Gonggao;
use app\common\model\SlideItem;
use app\common\model\Version;
use app\common\model\Xilie;
use mysql_xdevapi\Table;
use think\Db;

class Index extends Api
{


    protected $noNeedLogin = '';
    protected $noNeedRight = '*';



    /**
     * 文章分类API
     */
    public function article_category()
    {
        $category_article = new ArticleCategory();
        $category         = $category_article->field("id,name")->order('weight','desc')->where('status', 1)->select();
        $this->success(__('获取成功'), ['data'=>$category]);
    }



    /**
     * 文章api
     * @return false|string
     * @throws \think\db\exception\DbException
     */
    public function article()
    {
        $categoryId = $this->request->param("category");
        if (!empty($categoryId)) {
            $articleModel = new Article();
            $where        = ["category" => $categoryId, "status" => 1];
            $article      = $articleModel->where($where)->order('id desc')->paginate();
            $this->success(__('获取成功'), ['data'=>$article]);
        } else {
            $this->error(__('未提供分类ID'), []);
        }
    }

    public function add_view(){
        $article_id = $this->request->param("article_id");
        if(empty($article_id)){
            $this->error(__('未提供分类ID'), []);
        }
        $articleModel = new Article();
        $article= $articleModel->where('id',$article_id)->find();
        if(empty($article)){
            $this->success(__('获取成功'), []);
        }else{
            db('article')->where('id',$article_id)->update(['view'=>$article['view']+1]);
            $this->success(__('成功'), []);
        }
    }
    /**
     * 返佣产品列表  get需要获取产品类型  state
     * @return false|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function fanyong()
    {
        $lable = $this->request->param("label",0);
        $state = $this->request->param("state",1);
        $page = $this->request->param("page",1);
        $limit = $this->request->param("limit");
        $search = $this->request->param("search");
        $user=$this->auth->getUserinfo();
        if(!empty($lable)){
            $map['label_id']=$lable;
        }
        if(!empty($state)){
            $map['state']=$state;
        }
        if(!empty($serach)){
            $map['name'] = ['like', "%$search%"];
        }
        $map['status']=1;
        $fanyongModel = new Fanyong();
        $list         = $fanyongModel->where($map)->order('list_order','desc')->paginate('',true,[
            'page'=>$page,
            'list_rows'=>$limit,
        ]);
        foreach ($list as $k=>$v){
            $other=FangyongPrice::where('product_id',$v->id)->where('user_id',$user['agent_id'])->find();
            if(empty($other)){
                $v->price=$v['money'];
            }else{
                $v->price=$other['price'];
            }

            $v->json=json_decode($v->json,true);
            $v->configjson=json_decode($v->configjson,true);

        }

        $this->success(__('成功'), ['data'=>$list]);
    }

    // 返佣产品详情
    public function fanyong_detail()
    {
        $id = $this->request->param("id");
        if (!empty($id)) {
            $where        = ["id" => $id, "status" => 1];
            $fanyongModel = new Fanyong();
            $row          = $fanyongModel->where($where)->find();
            $row['json']=json_decode($row['json'],true);
            $row['configjson']=json_decode($row['configjson'],true);
            $this->success(__('成功'), ['data'=>$row]);
        } else {
            $this->error(__('非法数据'), []);
        }
    }

    public function series(){
        $xilei=new Xilie();
        $data= $xilei->where('status','normal')->order('weigh','desc')->select();
        $this->success(__('成功'), ['data'=>$data]);
    }

    public function label(){
        $label=new FangyongLabel();
        $data= $label->where('status','normal')->order('sort','desc')->select();
        $this->success(__('成功'), ['data'=>$data]);
    }



    /**
     * 超市分类
     * @return false|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function chaoshi_category()
    {
        $categoryId = $this->request->param("category");
        $category   = Db("chaoshi_category")->where("id", $categoryId)->select();
        $this->success(__('成功'), ['data'=>$category]);
    }


    public function update()
    {

        $version=Version::order('id','desc')->find();
        $this->success(__('成功'), ['data'=>$version]);
    }


    /**
     * 2021年7月8日16:2:35
     */
    public function tags()
    {
        $name = $this->request->param("name");
        if (empty($name)) {
            $this->error(__('name不能为空'), []);
        }
        //today_hot
        if ($name == 'today_hot') { //热门下款
            $today_hot = Chaoshi::where('status', 1)->where('today_hot', 1)->order("sort", "asc")->field('*,FROM_UNIXTIME(update_time,"%Y-%m-%d %H:%i:%s") update_time_text')->select(); //今日热门
            foreach ($today_hot as $k => $v) {
                $catgory                    = ChaoshiCategory::where(['id' => $v['category']])->field('id cate,name cate_name,logo cate_logo')->find();
                $today_hot[$k]['cate']      = $catgory['cate'];
                $today_hot[$k]['cate_name'] = $catgory['cate_name'];
                $today_hot[$k]['cate_logo'] = $catgory['cate_logo'];
            }
            $this->success(__('成功'), ['data'=>$today_hot]);
            die;
        } elseif ($name == 'hot_list') {//实时更新
            $hot_list = Chaoshi::where('status', 1)->where('hot_list', 1)->order("update_time", "desc")->field('*,FROM_UNIXTIME(update_time,"%Y-%m-%d %H:%i:%s") update_time_text')->select();//今日更新
            foreach ($hot_list as $k => $v) {
                $catgory               = ChaoshiCategory::where(['id' => $v['category']])->field('id cate,name cate_name,logo cate_logo')->find();
                $hot_list[$k]['cate']      = $catgory['cate'];
                $hot_list[$k]['cate_name'] = $catgory['cate_name'];
                $hot_list[$k]['cate_logo'] = $catgory['cate_logo'];
            }
            $this->success(__('成功'), ['data'=>$hot_list]);
        } elseif ($name == "new_loan") { //精品推荐
            $new_loan = Chaoshi::where('status', 1)->where('new_loan', 1)->order("sort", "asc")->field('*,FROM_UNIXTIME(update_time,"%Y-%m-%d %H:%i:%s") update_time_text')->select();
            foreach ($new_loan as $k => $v) {
                $catgory               = ChaoshiCategory::where(['id' => $v['category']])->field('id cate,name cate_name,logo cate_logo')->find();
                $new_loan[$k]['cate']      = $catgory['cate'];
                $new_loan[$k]['cate_name'] = $catgory['cate_name'];
                $new_loan[$k]['cate_logo'] = $catgory['cate_logo'];
            }
            $this->success(__('成功'), ['data'=>$new_loan]);
        } elseif ($name == 'series') {
            $data_ChaoshiCategory = ChaoshiCategory::where('state', 1)->where('category', 2)->order("list_order", "asc")->select();
            foreach ($data_ChaoshiCategory as $k => $v) {
                $data_Chaoshi = Chaoshi::where('status', 1)->where('category', $v['id'])->field('*,FROM_UNIXTIME(update_time,"%Y-%m-%d %H:%i:%s") update_time_text')->order("sort", "asc")->select();
                foreach ($data_Chaoshi as $k1 => $v2) {
                    $data_Chaoshi[$k1]['cate']      = $v['id'];
                    $data_Chaoshi[$k1]['cate_name'] = $v['name'];
                    $data_Chaoshi[$k1]['cate_logo'] = $v['logo'];
                }
                $data_ChaoshiCategory[$k]['children'] = $data_Chaoshi;
            }
            $this->success(__('成功'), ['data'=>$data_ChaoshiCategory]);
        } elseif ($name == "lending") { //网贷合集
            $data_ChaoshiCategory = ChaoshiCategory::where('state', 1)->where('category', 1)->order("list_order", "asc")->select();
            foreach ($data_ChaoshiCategory as $k => $v) {
                $data_ChaoshiCategory[$k]['children'] = Chaoshi::where('status', 1)->where('category', $v['id'])->field('*,FROM_UNIXTIME(update_time,"%Y-%m-%d %H:%i:%s") update_time_text')->order("sort", "asc")->select();
            }
            $this->success(__('成功'), ['data'=>$data_ChaoshiCategory]);
        }

        echo json_encode(["code" => 0, "msg" => "非法数据"], JSON_UNESCAPED_UNICODE);
        die;
    }

    public function chashigroup(){
        $d=Chaoshigroup::where('status',1)->select();
        $this->success(__('获取成功'), ['data'=>$d]);
    }

    public function search()
    {
        $name = $this->request->param("name");
        if (empty($name)) {
            $this->success(__('name不能为空'), []);
        }
        $data = Chaoshi::where('status', 1)->where('name', 'like', "%{$name}%")->order("sort", "asc")->field('*,FROM_UNIXTIME(update_time,"%Y-%m-%d %H:%i:%s") update_time_text')->select();
        foreach ($data as $k => $v) {
            $category              = ChaoshiCategory::where(['id' => $v['category']])->field('id cate,name cate_name,logo cate_logo')->find();
            $data[$k]['cate']      = $category['cate'];
            $data[$k]['cate_name'] = $category['cate_name'];
            $data[$k]['cate_logo'] = $category['cate_logo'];
        }
        $this->success(__('成功'), ['data'=>$data]);
    }

    /**
     * 返佣产品单页详情
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function fanyong_list()
    {
        $id = $this->request->param("id");
        if (!empty($id)) {
            $list = Db("fanyong")->where("id", $id)->find();
            if($list['status'] ==1){
                $this->success(__('成功'), ['data'=>$list]);
            }else{
                $this->success(__('产品已下架，请联系客服。'), []);
            }
            die;
        } else {
            $this->success(__('未查询到产品。'), []);
        }
    }

    /**
     * 申请订单  申请产品
     */
    public function to_product(){
        if($this->request->post()){
            $UserModel = new User();

            $data['pid']=$this->auth->id;
            $data['p_id']=input('p_id');
            $data['configjson']=  htmlspecialchars_decode(input('configjson'));

            $data['json']=  htmlspecialchars_decode(input('json'));
            $fanyong=db('fanyong')->where('id',$data['p_id'])->find();
            if(empty($data['p_id'])){
                $this->error(__('产品id不能为空'), []);
            }
            if(empty($fanyong['status'])){
                $this->error(__('产品已下架,请联系客服'), []);
            }
            $get_data = [
                "status" => 2,
                "ment" => $UserModel->GetOs(),
                "time" => time(),
                "p_title" => $fanyong['name'],
                "state" => $fanyong['state'],
                'user_ip'=>$this->get_ip()
            ];
            $in_data = array_merge($data,$get_data);
            $request = db("fanyong_order")->insert($in_data);
            if($request){
                $this->success(__('申请成功'), []);
            }else{
                $this->success(__('抱歉，未能提交成功，请检查填写。'), [],23);
            }
        }else{
            $this->success(__('抱歉，未能提交成功，请检查填写。'), [],24);
        }

    }


    public function get_ip()
    {
        if(!empty($_SERVER['HTTP_CLIENT_IP'])){
            $cip = $_SERVER['HTTP_CLIENT_IP'];
        }
        else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
            $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        }
        else if(!empty($_SERVER["REMOTE_ADDR"])){
            $cip = $_SERVER["REMOTE_ADDR"];
        }else{
            $cip = '';
        }
        preg_match("/[\d\.]{7,15}/", $cip, $cips);
        $cip = isset($cips[0]) ? $cips[0] : 'unknown';
        unset($cips);
        return $cip;
    }

    public function category_product()
    {
        $cid = $this->request->param("cid");
        if (empty($cid)) {
            $this->error(__('参数不能为空'), []);
        }
        $data = Chaoshi::where('status', 1)->where('category', $cid)->order("sort", "asc")->field('*,FROM_UNIXTIME(update_time,"%Y-%m-%d %H:%i:%s") update_time_text')->select();
        foreach ($data as $k => $v) {
            $category              = ChaoshiCategory::where(['id' => $v['category']])->field('id cate,name cate_name,logo cate_logo')->find();
            $data[$k]['cate']      = $category['cate'];
            $data[$k]['cate_name'] = $category['cate_name'];
            $data[$k]['cate_logo'] = $category['cate_logo'];
        }
        $this->success(__('申请成功'), ['data'=>$data]);
    }

    public function test(){

    }

    public function uv(){
        $pid=input('pid');
        $res=Db::name('uv')->whereTime('create_time', 'today')->where('pid',$pid)->find();
        if($res){
            Db::name('uv')->whereTime('create_time', 'today')->where('pid',$pid)->setInc('count',1);
        }else{
            Db::name('uv')->whereTime('create_time', 'today')->where('pid',$pid)->insertGetId([
                'create_time'=>time(),
                'count'=>1,
                'pid'=>$pid
            ]);
        }
        $this->success(__('成功'), []);

    }

    public function article_detail(){
        $article_id=input('id');
        $data= Db::name('article')->where('id',$article_id)->find();
        $this->success(__('申请成功'), ['data'=>$data]);
    }

    public function gonggao(){
        $data=Gonggao::all();
        $this->success(__('申请成功'), ['data'=>$data]);
    }


}
