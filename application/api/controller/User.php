<?php

namespace app\api\controller;

use app\admin\model\Admin;
use app\admin\model\fankui\Fankui;
use app\common\controller\Api;
use app\common\library\Ems;
use app\common\library\Sms;
use app\common\model\Tixian;
use fast\Random;
use think\Config;
use think\Db;
use think\Validate;

/**
 * 会员接口
 */
class User extends Api
{
    protected $noNeedLogin = ['login', 'mobilelogin', 'register', 'resetpwd', 'changeemail', 'changemobile', 'third','band','order'];
    protected $noNeedRight = '*';

    public function _initialize()
    {
        parent::_initialize();

//        if (!Config::get('fastadmin.usercenter')) {
//            $this->error(__('User center already closed'));
//        }

    }

    /**
     * 会员中心
     */
    public function index()
    {
        $this->success('', ['welcome' => $this->auth->nickname]);
    }

    /**
     * 会员登录
     *
     * @ApiMethod (POST)
     * @param string $account  账号
     * @param string $password 密码
     */
    public function login()
    {
        $account = $this->request->post('account');
        $password = $this->request->post('password');
        if (!$account || !$password) {
            $this->error(__('Invalid parameters'));
        }
        $ret = $this->auth->login($account, $password);
        if ($ret) {
            $data = ['userinfo' => $this->auth->getUserinfo()];
            $this->success(__('Logged in successful'), $data);
        } else {
            $this->error($this->auth->getError());
        }
    }

    /**
     * 客户列表-订单记录
     */
    public function order(){
        $field=['phone'=>'电话','name'=>'姓名','number'=>'编号','time'=>'时间'];
        $status = input('status');
        $state = input('style_id');
        $order = Db::name("fanyong_order")
            ->where('status',$status)
            ->where('state',$state)
            ->where('pid',$this->auth->id)
            ->order('time','desc')
            ->select();
        foreach ($order as $k=>$v){
            $order[$k]['json']=json_decode($v['json'],true);
            $order[$k]['configjson']=json_decode($v['configjson'],true);
            $order[$k]['data']='';
            foreach ( $order[$k]['configjson'] as $k1=>$v1){
                if(isset($field[$k1])){
                    $order[$k]['data']= $order[$k]['data'].$field[$k1].':'.$v1.',';
                }
            }
            $order[$k]['data'] =  substr($order[$k]['data'],0,strlen($order[$k]['data'])-1);
            $order[$k]['time_text'] = date('Y-m-d H:i:s',$v['time']);
            $order[$k]['url'] = Db::name('fanyong')->where('id',$v['p_id'])->value('logo');
        }

        $this->success('成功',["data"=>$order]);

    }

    /**
     * 手机验证码登录
     *
     * @ApiMethod (POST)
     * @param string $mobile  手机号
     * @param string $captcha 验证码
     */
    public function mobilelogin()
    {
        $mobile = $this->request->post('mobile');
        $captcha = $this->request->post('captcha');
        if (!$mobile || !$captcha) {
            $this->error(__('Invalid parameters'));
        }
        if (!Validate::regex($mobile, "^1\d{10}$")) {
            $this->error(__('Mobile is incorrect'));
        }
        if (!Sms::check($mobile, $captcha, 'mobilelogin')) {
            $this->error(__('Captcha is incorrect'));
        }
        $user = \app\common\model\User::getByMobile($mobile);
        if ($user) {
            if ($user->status != 'normal') {
                $this->error(__('Account is locked'));
            }
            //如果已经有账号则直接登录
            $ret = $this->auth->direct($user->id);
        } else {
            $ret = $this->auth->register($mobile, Random::alnum(), '', $mobile, []);
        }
        if ($ret) {
            Sms::flush($mobile, 'mobilelogin');
            $data = ['userinfo' => $this->auth->getUserinfo()];
            $this->success(__('Logged in successful'), $data);
        } else {
            $this->error($this->auth->getError());
        }
    }
    /**
     * 注册会员
     *
     * @ApiMethod (POST)
     * @param string $username 用户名
     * @param string $password 密码
     * @param string $email    邮箱
     * @param string $mobile   手机号
     * @param string $code     验证码
     */
    public function register()
    {
        $password = $this->request->post('password');
        $re_password = $this->request->post('re_password');
        $mobile = $this->request->post('mobile');
        $sms_code = $this->request->post('sms_code');
        $code = $this->request->post('code');
        $admin=Admin::where('code',$code)->find();
        $parent=\app\common\model\User::where('invite_code',$code)->find();
        $username=$mobile;
        if(empty($admin) && empty($parent)){
            $this->error(__('邀请码不正确'));
        }
        if (!$password) {
            $this->error(__('Invalid parameters'));
        }
        if($re_password !=$password){
            $this->error(__('2次密码不正确'));
        }

        if ($mobile && !Validate::regex($mobile, "^1\d{10}$")) {
            $this->error(__('Mobile is incorrect'));
        }
        $ret = Sms::check($mobile, $sms_code, 'register');
        if (!$ret) {
            $this->error(__('Captcha is incorrect'));
        }
        $ret = $this->auth->register($username,$password, $mobile,[]);
        if ($ret) {
            $user=$this->auth->getUser();
            $user->invite_code=make_coupon_card();
            if($admin){
                $user->agent_id=$code; //代理ID
                $user->parent_id=$code; //上级id
                $user->parent_path=$admin['code'];
            }else{
                $user->agent_id=$parent['agent_id']; //代理ID
                $user->parent_id=$code; //上级id
                $user->parent_path=$parent['invite_code'].','.$parent->parent_path;
            }
            $user->save();
            $data = ['userinfo' => $this->auth->getUserinfo()];
            $this->success(__('Sign up successful'), $data);
        } else {
            $this->error($this->auth->getError());
        }
    }

    /**
     * 注册会员
     *
     * @ApiMethod (POST)
     * @param string $username 用户名
     * @param string $password 密码
     * @param string $email    邮箱
     * @param string $mobile   手机号
     * @param string $code     验证码
     */
    public function register1()
    {
        $username = $this->request->post('user_login');
        $password = $this->request->post('user_pass');
        $mobile = $username;
        $s_id = $this->request->post('s_id',1);
        if (!$username || !$password) {
            $this->error(__('Invalid parameters'));
        }
        $parent= \app\common\model\User::get($s_id);
        IF(empty($parent)){
            $this->error(__('邀请码不正确'));
        }
        if ($mobile && !Validate::regex($mobile, "^1\d{10}$")) {
            $this->error(__('Mobile is incorrect'));
        }
        $rq = \app\common\model\User::where("username",$username)->find();
        if($rq){
            $this->error(__('账号存在'),[]);
        }
        $ret = $this->auth->register($username, $password, '', $mobile, []);
        if ($ret) {

            $user=$this->auth->getUser();
            if($s_id ==1){
                $s_id=0;
                $user->parent_path=$parent->parent_path;
            }else{
                $user->parent_path=$s_id.','.$parent->parent_path;
            }
            $user->parent_id=$s_id;

            $user->group_id=2;
            $user->save();
            $data = ['userinfo' => $this->auth->getUserinfo(),'token'=>$this->auth->getToken()];
            $this->success(__('Sign up successful'), ['data'=>$data]);
        } else {
            $this->error($this->auth->getError());
        }
    }

    /**
     * 退出登录
     * @ApiMethod (POST)
     */
    public function logout()
    {
        if (!$this->request->isPost()) {
            $this->error(__('Invalid parameters'));
        }
        $this->auth->logout();
        $this->success(__('Logout successful'));
    }



    /**
     * 修改会员个人信息
     *
     * @ApiMethod (POST)
     * @param string $avatar   头像地址
     * @param string $username 用户名
     * @param string $nickname 昵称
     * @param string $bio      个人简介
     */
    public function profile()
    {
        $user = $this->auth->getUser();
        $username = $this->request->post('username');
        $nickname = $this->request->post('nickname');
        $bio = $this->request->post('bio');
        $avatar = $this->request->post('avatar', '', 'trim,strip_tags,htmlspecialchars');
        if ($username) {
            $exists = \app\common\model\User::where('username', $username)->where('id', '<>', $this->auth->id)->find();
            if ($exists) {
                $this->error(__('Username already exists'));
            }
            $user->username = $username;
        }
        if ($nickname) {
            $exists = \app\common\model\User::where('nickname', $nickname)->where('id', '<>', $this->auth->id)->find();
            if ($exists) {
                $this->error(__('Nickname already exists'));
            }
            $user->nickname = $nickname;
        }
        $user->bio = $bio;
        $user->avatar = $avatar;
        $user->save();
        $this->success();
    }

    /**
     * 修改邮箱
     *
     * @ApiMethod (POST)
     * @param string $email   邮箱
     * @param string $captcha 验证码
     */
    public function changeemail()
    {
        $user = $this->auth->getUser();
        $email = $this->request->post('email');
        $captcha = $this->request->post('captcha');
        if (!$email || !$captcha) {
            $this->error(__('Invalid parameters'));
        }
        if (!Validate::is($email, "email")) {
            $this->error(__('Email is incorrect'));
        }
        if (\app\common\model\User::where('email', $email)->where('id', '<>', $user->id)->find()) {
            $this->error(__('Email already exists'));
        }
        $result = Ems::check($email, $captcha, 'changeemail');
        if (!$result) {
            $this->error(__('Captcha is incorrect'));
        }
        $verification = $user->verification;
        $verification->email = 1;
        $user->verification = $verification;
        $user->email = $email;
        $user->save();

        Ems::flush($email, 'changeemail');
        $this->success();
    }

    /**
     * 修改手机号
     *
     * @ApiMethod (POST)
     * @param string $mobile  手机号
     * @param string $captcha 验证码
     */
    public function changemobile()
    {
        $user = $this->auth->getUser();
        $mobile = $this->request->post('mobile');
        $captcha = $this->request->post('captcha');
        if (!$mobile || !$captcha) {
            $this->error(__('Invalid parameters'));
        }
        if (!Validate::regex($mobile, "^1\d{10}$")) {
            $this->error(__('Mobile is incorrect'));
        }
        if (\app\common\model\User::where('mobile', $mobile)->where('id', '<>', $user->id)->find()) {
            $this->error(__('Mobile already exists'));
        }
        $result = Sms::check($mobile, $captcha, 'changemobile');
        if (!$result) {
            $this->error(__('Captcha is incorrect'));
        }
        $verification = $user->verification;
        $verification->mobile = 1;
        $user->verification = $verification;
        $user->mobile = $mobile;
        $user->save();

        Sms::flush($mobile, 'changemobile');
        $this->success();
    }

    /**
     * 第三方登录
     *
     * @ApiMethod (POST)
     * @param string $platform 平台名称
     * @param string $code     Code码
     */
    public function third()
    {
        $url = url('user/index');
        $platform = $this->request->post("platform");
        $code = $this->request->post("code");
        $config = get_addon_config('third');
        if (!$config || !isset($config[$platform])) {
            $this->error(__('Invalid parameters'));
        }
        $app = new \addons\third\library\Application($config);
        //通过code换access_token和绑定会员
        $result = $app->{$platform}->getUserInfo(['code' => $code]);
        if ($result) {
            $loginret = \addons\third\library\Service::connect($platform, $result);
            if ($loginret) {
                $data = [
                    'userinfo'  => $this->auth->getUserinfo(),
                    'thirdinfo' => $result
                ];
                $this->success(__('Logged in successful'), $data);
            }
        }
        $this->error(__('Operation failed'), $url);
    }

    /**
     * 重置密码
     *
     * @ApiMethod (POST)
     * @param string $mobile      手机号
     * @param string $newpassword 新密码
     * @param string $captcha     验证码
     */
    public function resetpwd()
    {
        $account = $this->request->param('account');
        $mobile = $account;
        $newpassword = $this->request->post("newpassword");

        $confirm_password = $this->request->param('confirm_password');
        if(empty($confirm_password)){
            $this->error(__('确认密码不能为空'));
        }
        if (!$newpassword) {
            $this->error(__('Invalid parameters'));
        }
        if($newpassword != $confirm_password){
            $this->error(__('确认密码与新密码不一臻'));
        }
        if (!Validate::regex($mobile, "^1\d{10}$")) {
            $this->error(__('Mobile is incorrect'));
        }
        $user = \app\common\model\User::getByMobile($mobile);
        if (!$user) {
            $this->error(__('User not found'));
        }
        //模拟一次登录
        $this->auth->direct($user->id);
        $ret = $this->auth->changepwd($newpassword, '', true);
        if ($ret) {
            $this->success(__('Reset password successful'));
        } else {
            $this->error($this->auth->getError());
        }
    }

    /**
     * 个人中心页面
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function user(){
        $user = new \app\common\model\User();
        $u_data = $user
            ->where("id",$this->auth->id)
            ->field("password",true)
            ->find();
        $this->success('成功',["data"=>$u_data]);

    }

    public function tx_list(){
        $txModel = new Tixian();
        $u_data = $this->auth->getUser();
        $list = $txModel->where("user_id",$u_data->id)->order('tx_time','desc')->select();
        foreach($list as $k=>$v){
            $list[$k]['tx_time_text'] = date('Y-m-d H:i:s',$v['tx_time']);
        }
        $this->success('成功',["data"=>$list]);
    }

    /**
     * 收入明细
     */
    public function income_details(){
        $detail = Db::name("user_balance_log")->where("user_id",$this->auth->id)->order('create_time','desc')->select();
        if(empty($detail)) {
            $this->success('暂时没有数据', ["data" => []]);
        }
        foreach($detail as $k=>$v){
            $detail[$k]['create_time_text'] = date('Y-m-d H:i:s',$v['create_time']);
        }
        $this->success('成功',["data"=>$detail]);

    }

    /**
     * 返佣产品单页详情
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function fanyogn_list(){
        $id = $this->request->param("id");
        if(!empty($id)){
            $list = Db("fanyong")->where("id",$id)->find();
            $this->success('已提交,等待审核',["data"=>$list]);
        }else{
            $this->error('未查询到产品',[]);
        }
    }

    public function tx(){
        $u_user =\app\common\model\User::get($this->auth->id);
        if($this->request->isPost()){
            $param = $this->request->param();
            if(empty($param['money'])){
                $this->error('提现金额不能为空',[]);
            }
            if($param['money']<10){
                $this->error('提现金额不能小于10',[]);
            }
            if($param['money']>1000){
                $this->error('提现金额不能大于1000',[]);
            }
            $txModel = new Tixian();
            $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
            $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
            $count_tx=$txModel->where('user_id',$this->auth->id)->where('tx_time','between', array($beginToday,$endToday))->count();
            if($count_tx){
                $this->error('每日只能提现一次',[]);
            }
            if($u_user->ktx<$param['money']){
                $this->error('可提现金额不足',[]);
            }
            $rq_us= $this->tx_validate($param['money']);
            if($rq_us){
                $u_user->ktx=$u_user->ktx-$param['money'];
                $u_user->save();
                $this->success('已提交,等待审核',[]);
            }
            $this->error('提交失败',[]);
        }else{
            $user = new \app\admin\model\User();
            $u_data = $user
                ->where("id",$this->auth->id)
                ->field("id_card,al_pay_name,al_pay_account")
                ->find();
            $this->success('已提交,等待审核',["data"=>$u_data]);
        }
    }



    /*
     * 判断是否可提现（配合提现功能使用）
     */
    public function tx_validate($money){

        $user = new \app\common\model\User();
        $txModel = new Tixian();
        $uList = $user
            ->where("id",$this->auth->id)
            ->field("id_card,tx,ktx,income,al_pay_name,al_pay_account,mobile")
            ->find();
        $ins = [
            "money"=>$money, //提现金额
            "tx_time" => time(), //提现时间
            "state" => 2, //未处理
            "user_id" => $this->auth->id,
            "user_login" =>$uList['mobile'],
            "al_pay_name" => $uList["al_pay_name"],
            "al_pay_account" => $uList["al_pay_account"],
        ];
        return    $txModel->save($ins);
    }
    //实名认证
    public function real_name(){
        $id_card=input('id_card');
        $real_name=input('real_name');
        if(empty($real_name)){
            $this->error(__('真实姓名不能为空'),[]);
        }
        if(empty($id_card)){
            $this->error(__('身份证号码不能为空'),[]);
        }

        $u_data =$this->auth->getUser();
        if($u_data['is_real'] ==1){
            $this->error(__('你已实名 无需重复实名认证'),[]);
        }

        $u_data->real_name=$real_name;
        $u_data->id_card=$id_card;
        $u_data->is_real=1;
        $u_data->save();
        $this->success(__('实名认证通过'),[]);
    }


    public function band(){

        $param = $this->request->param();
        if(empty($param['al_pay_name'])){
            $this->error(__('支付宝名称不能为空'),[]);
        }
        if(empty($param['al_pay_account'])){
            $this->error(__('支付宝账号不能为空'),[]);
        }
        $u_data =\app\common\model\User::get($this->auth->id);

//        if($u_data['is_band'] ==1){
//            $this->error(__('你已绑定提现方式 无需重复'),[]);
//        }

        $u_data->al_pay_name=$param['al_pay_name'];
        $u_data->al_pay_account=$param['al_pay_account'];
        $u_data->is_band=1;
        $u_data->save();
        $this->success(__('实名认证通过'),[]);
    }

    public function fankui(){
        $context=input('text');
        $url=input('url');
        Fankui::create([
            'context'=>$context,
            'url'=>$url,
            'user_id'=>$this->auth->id,
        ]);
        $this->success(__('感谢你的反馈'),[]);
    }



    /**
     * 我的推广
     * @param $state
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function promote($state){
        $user=$this->auth->getUser();
        $userModel = new \app\admin\model\User();
        // true 直推 false 间推
        if ($state ==1){
            $user = $userModel->where('parent_id',$user->id)->field('username,logintime,mobile,jointime,id,parent_id,prevtime,income')->select();
            foreach($user as $k=>$v){
                $user[$k]['jointime_text'] = date('Y-m-d H:i:s',$v['jointime']);
                $user[$k]['username'] = $v['username'];
                $user[$k]['mobile'] = $v['mobile'];
            }
        }else{
            $user=self::Indirect_friends();
        }
        $this->success(__('获取成功'),['data'=>$user]);
    }


    public function Indirect_friends(){
        $userModel = new \app\admin\model\User();
        $data=$userModel->where('parent_id',$this->auth->id)->field('username,logintime,mobile,jointime,id,parent_id,prevtime,income')->select();//所有下下级
        $arr=[];
        foreach ($data as $k=>$v){
            $data1= $userModel->where('parent_id',$v['id'])->field('username,logintime,mobile,jointime,id,parent_id,prevtime,income')->select();
            foreach($data1 as $k1=>$v1){
                $data1[$k]['jointime_text'] = date('Y-m-d H:i:s',$v['jointime']);
                array_push($arr,$data1[$k1]);
            }

        }
       return $arr;
    }

}
