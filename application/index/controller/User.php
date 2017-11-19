<?php

namespace app\index\controller;

use think\Loader;
use think\Request;
use think\Response;

class User
{
    /**
     * Post /User
     * 定义为注册新用户
     *
     * @return Response
     * Kanzaki Tsukasa
     */
    public function save(Request $request): Response
    {
        $data = $request->post();
        // 验证用户名和密码
        $validate = Loader::validate('Users');
        if (!$validate->check($data)) {
            return json(['msg' => $validate->getError()], 400);
        }
        return json(['msg' => '注册成功'], 200);
    }
}
