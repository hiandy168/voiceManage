<?php

/**
 * LoginController.class.php 
 * @author timelesszhuang
 * @version voicemanage
 * @copyright 赵兴壮
 * @package  Controller
 * @todo BaseController判断用户登录状态之后进入主界面
 * 2014年8月12 12:25
 */

namespace Admin\Controller;

use Think\Controller;

class LoginController extends Controller {
    /*
     * index
     * 进入登录页面
     * @access public
     * @version voicemanage
     * @author timelesszhuang
     * @todo 登录验证用户密码状态
     */

    public function index() {
        $this->display();
    }

    /*
     * dologin
     * 进入登录页面   该操作实现远程salesmen登录实现
     * @access public
     * @version CLOTHRSMANAGE
     * @author timelesszhuang
     * @todo 登录验证
     */

    public function dologin() {
        $ver_code = I('post.vd_code');
        $verify_status = $this->check_verify($ver_code);
        if (!$verify_status) {
            $this->error('验证码输入错误或已过期！');
            exit;
        }
        $user_name = I('post.user_name');
        $condition['login_name'] = array('eq', $user_name);
        $password = I('post.user_password');
        if (!empty($user_name) && !empty($password)) {
            $password = md5(md5($user_name) . sha1($password));
            //发送远程请求的url
            //'CURL_PATH' => 'salesmen.cn/index.php/Api/',
            $path = C('CURL_PATH');
            $path.='Voicemanage/checkLogin';
            $loginParam[0] = $path;
            $loginParam[1]['login_name'] = $user_name;
            $loginParam[1]['login_pwd'] = $password;
            $checkResult = R('Home/Curl/sendcheckLoginData', $loginParam);
            $status = $checkResult['status'];
            if ($status == '20') {
                //对查询出的结果进行判断
                session('IS_LOGIN', 'TRUE');
                session('USER_ID', $checkResult['id']);
                session('USER_NAME', $checkResult['data']['name']);
                $this->success('登陆成功！', '../Index/index');
            } else if ($status != "") {
                if ($status == '10') {
                    $this->error('您的帐号禁止登录！');
                } else if ($status == '40') {
                    $this->error('用户名或密码错误！');
                } else if ($status == '30') {
                    $this->error('用户不存在！');
                }
            } else {
                $this->error('网络错误,请查看您的服务器的网络连接。');
            }
        } else {
            $this->error('用户名密码不为空！');
        }

//        $ver_code = I('post.vd_code');
//        $verify_status = $this->check_verify($ver_code);
//        if (!$verify_status) {
//            $this->error('验证码输入错误或已过期！');
//            exit;
//        }
//        $user_name = I('post.user_name');
//        $condition['login_name'] = array('eq', $user_name);
//        $password = I('post.user_password');
//        if (!empty($user_name) && !empty($password)) {//依据用户名查询      
//            $login = M('User');
//            $rs = $login->field('id,login_name,login_pwd')->where($condition)->find();
//            if ($rs) {     //对查询出的结果进行判断
//                $password = md5(md5($user_name) . sha1($password));
//                if ($password == $rs['login_pwd']) {//判断密码是否匹配
//                    if ($rs['login_status'] == 10) {
//                        $this->error('您的帐号禁止登录！');
//                        exit;
//                    }
//                    $where['user_id'] = array('eq', $rs['id']);
//                    $userInfoModel = M('UserInfo');
//                    $data = $userInfoModel->where($where)->find();
//                    $emailAccount = M('UserAccount')->where($where)->getField('emailaccount');
//                    session('IS_LOGIN', 'TRUE');
//                    session('USER_ID', $rs['id']);
//                    session('USER_NAME', $rs['login_name']);
//                    session('NAME', $data['name']);
//                    session('EMAIL', $emailAccount);
//                    $this->success('登陆成功！', '../Index/index');
//                } else {
//                    $this->error('您的输入密码错误！');
//                }
//            } else {
//                $this->error('您的输入用户名或者密码错误！');
//            }
//        } else {
//            $this->error('用户名或密码输入为空！');
//        }
    }

    /*
     * check_verify
     * 验证验证码是否正确 验证是否过时
     * @access private
     * @author timelesszhuang
     * @param $code 用户输入的验证码
     * @return boolen
     * @version CLOTHRSMANAGE
     * @author timelesszhuang
     * @todo 登录验证用户名
     */

    private function check_verify($code, $id = '') {
        $verify = new \Think\Verify();
        $verify->seKey = 'verify_login'; //验证码的加密密钥
        return $verify->check($code, $id);
    }

    /*
     * verify
     * 登录界面验证码实现
     * @access public
     * @author timelesszhuang
     * @version CLOTHRSMANAGE
     * @author timelesszhuang
     * @todo 登录验证
     */

    public function verify() {
        ob_clean();
        $verify = new \Think\Verify();
        $verify->useImgBg = false; //是否使用背景图片 默认为false
        //$verify->expire =; //验证码的有效期（秒）
        //$verify->fontSize = 70; //验证码字体大小（像素） 默认为25
        $verify->useCurve = false; //是否使用混淆曲线 默认为true
        $verify->useNoise = false; //是否添加杂点 默认为true
        //$verify->imageW = 70; //验证码宽度 设置为0为自动计算
        //$verify->imageH = 25; //验证码高度 设置为0为自动计算
        $verify->length = 4; //验证码位数
        //$verify->fontttf =;//指定验证码字体 默认为随机获取
        $verify->useZh = false; //是否使用中文验证码 默认false
        //$verify->bg = array(243, 251, 254); //验证码背景颜色 rgb数组设置，例如 array(243, 251, 254)
        $verify->seKey = 'verify_login'; //验证码的加密密钥
        $verify->entry();
    }

    /**
     * logout
     * 退出登录，清除session
     * @access public
     * @return boolean
     * @version dogocms 1.0
     */
    public function logout() {
        session('[destroy]');
        $this->success('您已经成功退出管理系统！', __MODULE__ . '/index');
    }

}
