<?php

namespace app\index\validate;

use think\Validate;

class Users extends Validate
{
    protected $rule = [
        'username' => 'require|checkUserName',
        'password' => 'require',
    ];
    protected function checkUserName($username)
    {
        return (Validate::is($username, 'email') || Validate::is($username, 'alphaDash')) ?
        true : '用户名必须仅含字母、数字、下划线_及破折号-，或合法的电子邮件地址';
    }
}
