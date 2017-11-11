<?php

namespace app\index\model;

use think\Cache;
use think\Db;

/**
 * Users Model
 * Data Dictionary
 * user_id int(11) pk
 * username varchar(50)
 * nickname varchar(200)
 * email varchar(100)
 * password varchar(64)
 * reg_time int
 *
 * Kanzaki Tsukasa
 */
class Users
{
    const CACHE_KEY_USER_INFO_PREFIX = 'user_info_user_id_';
    private $username = '';
    private $userId = 0;

    public function register()
    {

    }

    public function login()
    {

    }

    public function saveSession()
    {

    }

    public static function getInfo(int $userId): array
    {
        $user = Cache::get(self::CACHE_KEY_USER_INFO_PREFIX . $userId);
        if (empty($user)) {
            $user = Db::name('users')
                ->field(['user_id', 'username', 'nickname', 'email', 'reg_time'])
                ->find($userId);
            Cache::set(self::CACHE_KEY_USER_INFO_PREFIX . $userId, $user, 86400);
        }
        return $user;
    }
}
