<?php

namespace app\common\helper;

use Overtrue\Pinyin\Pinyin as OPinyin;

class Pinyin
{
    private static $pinyinIns = null;
    public static function instance(): OPinyin
    {
        if (
            is_null(self::$pinyinIns) ||
            !(self::$pinyinIns instanceof OPinyin)
        ) {
            self::$pinyinIns = new OPinyin();
        }
        return self::$pinyinIns;
    }

    public static function keywords2Pinyin($keywords, bool $withTone = null): array
    {
        if (is_string($keywords)) {
            $keywords = [$keywords];
        } elseif (!is_array($keywords)) {
            return [];
        }
        if (is_null($withTone)) {
            $withTone = false;
        }
        // validate and convert
        $res_arr = [];
        foreach ($keywords as $keyword) {
            if (!is_string($keyword)) {
                return [];
            }
            $res_arr[] = self::instance()->sentence($keyword, $withTone);
        }

        return $res_arr;
    }
}
