<?php

namespace app\index\model;

use app\common\helper\Timestamp;
use think\Db;

/**
 * Tag Model
 *
 * Data Dictionary
 * tag_id int(11) pk
 * tag_name varchar(255)
 * create_time int(11)
 *
 * Kanzaki Tsukasa
 */
class Tags
{
    public static $insertTimeField = ['create_time'];
    /**
     * 插入（一次）标签
     *
     * @param array $tags
     * @return array $tag_id_arr
     * Kanzaki Tsukasa
     */
    public static function insertOnce(array $tags): array
    {
        $condition = ['tag_name' => ['in', $tags]];
        $dbTags = Db::name('tags')
            ->where($condition)
            ->column(['tag_id', 'tag_name']);
        $diff = array_diff($tags, $dbTags);

        if (!empty($diff)) {
            $insertData = [];
            foreach ($diff as $item) {
                $col = ['tag_name' => $item];
                Timestamp::addTime($col, self::$insertTimeField);
                $insertData[] = $col;
            }
            Db::name('tags')->insertAll($insertData);
        }
        return Db::name('tags')->where($condition)->column('tag_id');
    }

}
