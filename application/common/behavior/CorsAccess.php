<?php
namespace app\common\behavior;

use \think\Config;

/**
 * 跨域允许行为
 *
 * Kanzaki Tsukasa
 */
class CorsAccess
{
    public function run()
    {
        if (Config::get('app_debug') === true) {
            $http_origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
            header("Access-Control-Allow-Origin: {$http_origin}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Allow-Methods: GET,POST,OPTIONS,PUT,DELETE');
            header('Access-Control-Allow-Headers: ' . implode(',', [
                'DNT',
                'X-Mx-ReqToken',
                'Keep-Alive',
                'User-Agent',
                'X-Requested-With',
                'If-Modified-Since',
                'Cache-Control',
                'Content-Type',
                'X-Token',
                'X-Info',
                'X-Filename',
                'X-Area-Id',
                'X-Device-Id',
                'X-Platform',
            ]));
            header('Access-Control-Expose-Headers: ' . implode(',', [
                'DNT',
                'X-Mx-ReqToken',
                'Keep-Alive',
                'User-Agent',
                'X-Requested-With',
                'If-Modified-Since',
                'Cache-Control',
                'Content-Type',
                'X-Token',
                'X-Info',
                'X-Count',
            ]));
            if (isset($_SERVER['REQUEST_METHOD']) &&
                $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
                json([], 204);
            }
        }
    }
}
