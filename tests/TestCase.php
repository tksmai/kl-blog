<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2015 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------
namespace tests;

class TestCase extends \think\testing\TestCase
{
    protected $baseUrl = 'http://localhost';

    protected function dump($data)
    {
        echo PHP_EOL;
        var_dump($data);
        echo PHP_EOL;
    }

    protected function export($data)
    {
        echo PHP_EOL;
        var_export($data);
        echo PHP_EOL;
    }

    protected function json($data)
    {
        echo PHP_EOL;
        echo json_encode($data);
        echo PHP_EOL;
    }

    protected function phpse($data)
    {
        echo PHP_EOL;
        echo \serialize($data);
        echo PHP_EOL;
    }
}
