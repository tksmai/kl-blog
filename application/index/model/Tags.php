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
    const RELATION_SUFFIX = '_tags';
    const RELATION_TAG_ID_FIELD = 'tags_id';
    const RELATION_MODEL_ID_SUFFIX = '_id';
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

    public static function savePostsRelation(
        array $tagId,
        int $postId,
        string &$errorMsg = null) {
        $errorMsg = '';
        if (empty($tagId)) {
            $errorMsg = 'NO TAG ID';
            return false;
        }
        $table = 'posts_tags';
        try {
            $data = [];
            foreach ($tagId as $tag_id) {
                $tmp = [
                    'post_id' => $postId,
                    'tag_id' => $tag_id,
                ];
                $data[] = $tmp;
            }
            Db::name($table)
                ->insertAll($data, [], true);

        } catch (\Exception $e) {
            $errorMsg = $e->getMessage();
            return false;
        }
        return true;
    }

}
