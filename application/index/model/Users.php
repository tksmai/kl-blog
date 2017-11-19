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
    const ERR_USERNAME_NOT_EXIST = 3;
    const ERR_PASSWORD_WRONG = 4;
    const ERR_DB_ERROR = 10;
    const CACHE_KEY_USER_INFO_PREFIX = 'user_info_user_id_';
    private $username = '';
    private $userId = 0;
    private $nickname = '';
    private $email = '';
    private static $error = '';
    private static $errorCode = 0;

    /**
     * 用户注册
     *
     * @param string $username
     * @param string $password
     * @return bool
     * Kanzaki Tsukasa
     */
    public static function register(string $username, string $password): bool
    {
        $user = self::checkUser($username);
        if (isset($user['user_id']) && $user['user_id'] > 0) {
            self::$errorCode = self::ERR_USERNAME_EXIST;
            return false;
        } elseif ($user === false) {
            self::$errorCode = self::ERR_INVALID_USERNAME;
            return false;
        }
        // 重用checkUser返回的$condition
        $insdata = $user;
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

    /**
     * 用户登录
     *
     * @param string $username
     * @param string $password
     * @return bool
     * Kanzaki Tsukasa
     */
    public function login(string $username, string $password): bool
    {
        $user = self::checkUser($username);
        if ($user === false) {
            self::$errorCode = self::ERR_INVALID_USERNAME;
        } elseif (!isset($user['user_id'])) {
            self::$errorCode = self::ERR_USERNAME_NOT_EXIST;
            return false;
        }

        $cPassword = self::compressPwd($password);
        if ($cPassword !== $user['password']) {
            self::$errorCode = self::ERR_PASSWORD_WRONG;
            return false;
        }
        $this->userId = $user['user_id'];
        $this->username = $user['username'];
        $this->nickname = $user['nickname'];
        $this->email = $user['email'];
        return true;
    }

    public function saveSession()
    {

    }

    /**
     * 获取用户信息
     * （供其他Model调用）
     *
     * @param int $userId
     * @return array
     * Kanzaki Tsukasa
     */
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

    /**
     * 获取错误代码
     *
     * @return int
     * Kanzaki Tsukasa
     */
    public static function getErrorCode(): int
    {
        return self::$errorCode;
    }

    /**
     * 获取错误信息
     * 仅出现Db错误才有错误信息（供调试用）
     *
     * @return string
     * Kanzaki Tsukasa
     */
    public static function getErrorMsg(): string
    {
        return self::$error;
    }

    private static function compressPwd(string $password): string
    {
        return md5($password);
    }

    /**
     * 根据用户名或电邮地址检出用户
     *
     * @param string $username
     * @return mixed 用户名非法返回false，用户存在返回单行信息，不存在返回查询条件
     * Kanzaki Tsukasa
     */
    private static function checkUser(string $username)
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
        if ($item > 0) {
            return $item;
        }
        return $condition;
    }
}
