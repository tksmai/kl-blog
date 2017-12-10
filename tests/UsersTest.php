<?php
namespace tests;

use app\index\model\Users;

class UsersTest extends TestCase
{
    const TEST_PAGE_SIZE = 15;
    const TEST_USER_NAME = 'foo';
    const TEST_EMAIL = 'bar@local.domain';
    const TEST_PASSWORD = '12345678';
    private $addData = [
        // 'post_id' => '0',
        // 'title' => '《干物妹！小埋》第2季公布主题曲详情 10月8日开播',
        // 'post_content' => self::POST_TEST_CONTENT,
        // 'tags' => ['干物妹', '小埋', '五毛妹', '秋季', '动画', '官方'],
    ];

    public function testRegister()
    {
        // $username = self::TEST_USER_NAME;
        $username = self::TEST_EMAIL;
        $password = self::TEST_PASSWORD;
        // $flag = Users::register($username, $password);
        // $this->assertTrue($flag, "Users Error Code:" . Users::getErrorCode() . " " . Users::getErrorMsg());
    }

    public function testUserNameLogin()
    {
        $username = self::TEST_USER_NAME;
        $password = self::TEST_PASSWORD;

        $user = new Users();
        $flag = $user->login($username, $password);
        $this->assertTrue($flag, "Users Error Code:" . Users::getErrorCode() . " " . Users::getErrorMsg());
    }

    public function testEmailLogin()
    {
        $username = self::TEST_EMAIL;
        $password = self::TEST_PASSWORD;

        $user = new Users();
        $flag = $user->login($username, $password);
        $this->assertTrue($flag, "Users Error Code:" . Users::getErrorCode() . " " . Users::getErrorMsg());
    }

}
