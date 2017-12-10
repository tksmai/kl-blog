<?php

namespace app\index\controller;

use app\common\helper\Response as RH;
use app\index\model\Users;
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
            return json(['msg' => $validate->getError()], RH::CODE_REQ_PARAM_ERR);
        }
        if (Users::register($data['username'], $data['password'])) {
            return json(['msg' => '注册成功'], 200);
        } else {
            switch (Users::getErrorCode()) {
                case Users::ERR_USERNAME_EXIST:
                    $msg = '用户已存在';
                    break;

                default:
                    $msg = Users::getErrorMsg();
                    break;
            }
            return json(['msg' => $msg, 'code' => Users::getErrorCode()], RH::CODE_ACTION_FAILED);
        }

    }
}
