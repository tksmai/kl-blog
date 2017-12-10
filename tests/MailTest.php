<?php
namespace tests;

use app\common\helper\Mail;

class MailTest extends TestCase
{
    public function testSend()
    {
        Mail::setFromName('这是？'); // QQ邮箱此项貌似无效
        Mail::setSendTo('foo@bar.co');
        Mail::setSubject('主题？？');
        Mail::setBody('Hello Mail!');
        $this->assertTrue(Mail::exec(), Mail::getError());
    }
}
