<?php

namespace app\common\helper;

class Timestamp
{
    public static function addTime(array &$data, array $fields)
    {
        $time = time();
        foreach ($fields as $field) {
            $data[$field] = $time;
        }
    }
}
