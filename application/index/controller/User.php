<?php

namespace app\index\controller;

use addons\wechat\model\WechatCaptcha;
use app\admin\model\Merchant;
use app\common\model\Attachment;
use fast\Random;
use think\Controller;
use think\Session;
use think\Validate;

/**
 * 会员中心
 */
class User extends Controller
{
    protected $layout = 'default';
    protected $noNeedLogin = ['login', 'register', 'third'];
    protected $noNeedRight = ['*'];



    /**
     * 会员中心
     */
    public function index()
    {
        $this->view->assign('title', __('User center'));
        return $this->view->fetch();
    }

    /**
     * 注册会员
     */

    /**
     * 会员登录
     */
    public function login()
    {

        if ($this->request->isPost()) {
            $account = $this->request->post('account');
            $password = $this->request->post('password');
            $rule = [
                'account'   => 'require',
                'password'  => 'require',
            ];

            $msg = [
                'account.require'  => 'Account can not be empty',
                'password.require' => 'Password can not be empty',
            ];
            $data = [
                'account'   => $account,
                'password'  => $password,
            ];
            $validate = new Validate($rule, $msg);
            $result = $validate->check($data);
            if (!$result) {
                return json(['code'=>0,'msg'=>'账号或者密码必填']);
            }
            $field = Validate::is($account, 'email') ? 'email' : (Validate::regex($account, '/^1\d{10}$/') ? 'mobile' : 'username');
            $user = \app\common\model\Merchant::get([$field => $account]);
            if (!$user) {
                return json(['code'=>0,'msg'=>'账号不存在']);
            }

            if ($user->status != 'normal') {
                return json(['code'=>0,'msg'=>'账号被锁定']);
            }
            if ($user->password != $this->getEncryptPassword($password, $user->salt)) {
                return json(['code'=>0,'msg'=>'密码错误']);
            }

            $user->loginfailure = 0;
            $user->logintime = time();
            $user->loginip = request()->ip();
            $user->token = Random::uuid();
            $user->save();
            Session::set("merchant", $user->toArray());
            return json(['code'=>1,'msg'=>'登录成功']);

        }

        return $this->view->fetch();
    }

    /**
     * 退出登录
     */
    public function logout()
    {
        Session::set("merchant", '');
        $this->error(__('退出成功'), 'index/user/login');
    }

    /**
     * 获取密码加密后的字符串
     * @param string $password 密码
     * @param string $salt     密码盐
     * @return string
     */
    public function getEncryptPassword($password, $salt = '')
    {
        return md5(md5($password) . $salt);
    }


}
