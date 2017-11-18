<?php

namespace app\index\model;

use think\Cache;
use think\Db;
use think\Validate;

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
    const ERR_INVALID_USERNAME = 1;
    const ERR_USERNAME_EXIST = 2;
    const ERR_DB_ERROR = 3;
    const CACHE_KEY_USER_INFO_PREFIX = 'user_info_user_id_';
    private $username = '';
    private $userId = 0;
    private static $error = '';
    private static $errorCode = 0;

    public static function register(string $username, string $password): bool
    {
        if (self::checkUser($username)) {
            self::$errorCode = self::ERR_USERNAME_EXIST;
            return false;
        }
        $insdata = [];
        if (Validate::is($username, 'email')) {
            $insdata['email'] = $username;
        } elseif (Validate::is($username, 'alphaDash')) {
            $insdata['username'] = $username;
        } else {
            self::$errorCode = self::ERR_INVALID_USERNAME;
            return false;
        }
        $insdata['password'] = self::compressPwd($password);
        $insdata['reg_time'] = time();
        try {
            Db::name('users')->insert($insdata);
        } catch (\Exception $e) {
            self::$error = $e->getMessage();
            self::$errorCode = self::ERR_DB_ERROR;
            return false;
        }
        return true;
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

    public static function getErrorCode(): int
    {
        return self::$errorCode;
    }

    private static function compressPwd(string $password): string
    {
        return md5($password);
    }

    private static function checkUser(string $username): bool
    {
        $condition = [];
        if (Validate::is($username, 'email')) {
            $condition['email'] = $username;
        } elseif (Validate::is($username, 'alphaDash')) {
            $condition['username'] = $username;
        } else {
            return false;
        }
        $item = Db::name('users')
            ->where($condition)
            ->find();
        if ($item) {
            return true;
        }
        return false;
    }
}
