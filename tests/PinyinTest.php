<?php
namespace tests;

use Overtrue\Pinyin\Pinyin;

class PinyinTest extends TestCase
{
    private $pinyin = null;

    public function __construct()
    {
        $this->pinyin = new Pinyin();
    }
    public function testPinyin()
    {
        $test_str = '你好，这里是Cortana，小娜！';
        // $this->export($test_str);

        // $res = $this->pinyin->convert($test_str);
        // $this->export($res);

        // $res = $this->pinyin->convert($test_str, PINYIN_UNICODE);
        // $this->export($res);

        // $res = $this->pinyin->convert($test_str, PINYIN_ASCII);
        // $this->export($res);

        // $res = $this->pinyin->sentence($test_str);
        // $this->export($res);

        // $res = $this->pinyin->sentence($test_str, true);
        // $this->export($res);

    }
}
