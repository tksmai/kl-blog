<?php

namespace app\index\validate;

class Posts extends \think\Validate
{
    protected $rule = [
        'title|标题' => 'require|max:25',
        'post_content|内容' => 'require',
        'status|发布状态' => 'require|number|between:0,2',
    ];

    protected $message = [
        // 'title.require' => '标题必须',
        // 'post_content.require' => '内容必须',
        // 'status.require' => '发布状态必须',
        // 'status.number' => '状态值只能为数字',
        // 'status.between' => '状态值只能为0-2',
    ];
}
