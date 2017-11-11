<?php
namespace app\common\helper;

class DbValue
{
    /**
     * 对MYSQL LIKE 条件进行转义
     *
     * @param string $str
     * @return string
     * Kanzaki Tsukasa
     */
    public static function likeQuote(string $str): string
    {
        return strtr($str, array("\\\\" => "\\\\\\\\", '_' => '\_', '%' => '\%', "\'" => "\\\\\'"));
    }
}
