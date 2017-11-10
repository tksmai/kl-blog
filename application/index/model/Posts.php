<?php

namespace app\index\model;

use app\common\helper\Timestamp;
use app\index\model\Tags;
use \think\Db;

/**
 * Posts Model
 *
 * Data Dictionary
 * post_id int(11) pk
 * title varchar(255)
 * publisher varchar(255)
 * publisher_id int(11) // fk users.user_id
 * tags varchar(255)
 * meta text // the content for post
 * create_time int(11)
 * publish_time int(11)
 * modify_time int(11)
 * status tinyint(8)
 *
 * Kanzaki Tsukasa
 */
class Posts
{
    const DEFAULT_PUBLISHER_ID = 0;
    const DEFAULT_PUBLISHER = '匿名';
    const STATUS_SAVE_ONLY = 2;
    const STATUS_PUBLISHED = 1;
    const STATUS_FORBIDDEN = 0;
    const STATUS_DELETE = -1;

    private static $insertTimeField = ['create_time', 'modify_time'];
    private static $modifyTimeField = ['modify_time'];

    private $data = [];
    private $error = '';
    private $postId = 0;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public static function tableList()
    {

    }

    public static function read(int $id)
    {

    }

    public function getId()
    {
        return $this->postId;
    }

    public function add(int $publisherId)
    {
        if ($this->data['post_id']) {
            return $this->edit();
        } else {
            unset($this->data['post_id']);
        }
        // deal publisher
        $userInfo = Db::name('Users')
            ->field('username')
            ->find($publisherId);
        $this->data['publisher'] = empty($userInfo['username']) ?
        self::DEFAULT_PUBLISHER : $userInfo['username'];
        $this->data['publisher_id'] = empty($publisherId) ?
        self::DEFAULT_PUBLISHER_ID : $publisherId;
        // time deal
        Timestamp::addTime($this->data, self::$insertTimeField);
        // status deal
        $this->data['status'] = isset($this->data['status']) ?
        $this->data['status'] : self::STATUS_SAVE_ONLY;
        // tag deal
        $tags = [];
        if (!empty($this->data['tags'])) {
            $tags = Tags::insertOnce($this->data['tags']);
            $this->data['tags'] = implode(',', $this->data['tags']);
        }

        // insert
        try {
            $this->postId = intval(Db::name('Posts')->insertGetId($this->data));
            $errorMsg = '';
            Tags::savePostsRelation($tags, $this->postId, $errorMsg);
            $flag = true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $flag = false;
        }
        if (!empty($errorMsg)) {
            $flag = false;
            $this->error = 'Tags Model Relation:' . $errorMsg;
        }
        return $flag;
    }

    public function edit()
    {

    }

    public function del()
    {

    }

    public function getError()
    {
        return $this->error;
    }

}
