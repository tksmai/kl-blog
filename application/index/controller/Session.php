<?php

namespace app\index\controller;

use app\index\model\Users;
use think\Cache;
use think\Request;
use think\Response;

class Session
{

    /**
     * POST /Session
     * 定义为登录（创建会话）
     *
     * @param Request $request
     * @return Response
     * Kanzaki Tsukasa
     */
    public function save(Request $request): Response
    {
        $username = $request->post('username');
        $password = $request->post('password');
        $user = new Users();
        $ret = $user->login($username, $password);
        if ($ret === true) {
            $time = time();
            $cache_key = base64_encode(md5(\serialize($user)) . ",{$time}");
            Cache::set($cache_key, $user);
            return json(['token' => $cache_key], 200);
        } else {
            $msg = '';
            $code = 0;
            switch (Users::getErrorCode()) {
                case Users::ERR_PASSWORD_WRONG:
                    $msg = '密码错误';
                    $code = 401;
                    break;
                case Users::ERR_INVALID_USERNAME:
                    $msg = '用户名须为电子邮件或以A-Z和数字组合';
                    $code = 400;
                    break;
                case Users::ERR_USERNAME_NOT_EXIST:
                    $msg = '用户名不存在，现在注册吗？';
                    $code = 401;
                    break;
                default:
                    $msg = '未知错误';
                    $code = 500;
                    break;
            }
            return json(['msg' => $msg, 'code' => $code], $code);
        }
    }
}
