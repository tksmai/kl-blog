<?php

namespace app\index\model;

use app\common\helper\DbValue;
use app\common\helper\Pinyin;
use app\common\helper\Timestamp;
use app\index\model\Tags;
use app\index\model\Users;
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
 * post_content text
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
    const DEFAULT_PAGE_SIZE = 15;
    const STATUS_PUBLISHED = 2;
    const STATUS_SAVE_ONLY = 1;
    const STATUS_FORBIDDEN = 0;
    const STATUS_DELETE = -1;

    private static $listKeyWord = '';
    private static $listCondition = [];
    private static $listPage = [];
    private static $listField = ['post_id', 'title', 'publisher', 'tags', 'create_time', 'publish_time', 'modify_time', 'status'];
    private static $listSearchKey = ['tags', 'publisher', 'title'];
    private static $listOrder = 'post_id desc';

    private static $insertTimeField = ['create_time', 'modify_time'];
    private static $modifyTimeField = ['modify_time'];

    private static $readField = ['post_id', 'title', 'publisher', 'tags', 'post_content', 'create_time', 'publish_time', 'modify_time', 'status'];

    private $data = [];
    private $error = '';
    private $postId = 0;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * 查询关键词处理
     *
     * @param string $keyword 查询关键词
     * @param array $searchKey 数据表字段数组
     * @return bool
     * Kanzaki Tsukasa
     */
    public static function setKeyWord(string $keyword, array $searchKey = null): bool
    {
        if ($keyword === '') {
            return false;
        }
        self::$listKeyWord = DbValue::likeQuote(trim($keyword));
        if (is_null($searchKey)) {
            $searchKey = self::$listSearchKey;
        }
        $searchKey = implode('|', $searchKey);
        self::$listCondition[$searchKey] = self::$listKeyWord;
        return true;
    }

    /**
     * 分页设置
     *
     * @param int $p
     * @param int $size
     * @return bool
     * Kanzaki Tsukasa
     */
    public static function setPage(int $p, int $size = null): bool
    {
        is_null($size) && $size = self::DEFAULT_PAGE_SIZE;
        self::$listPage = [$p, $size];
        return true;
    }

    public static function setOrder(string $order): bool
    {
        self::$listOrder = $order;
        return true;
    }

    /**
     * 获取文章表格
     *
     * @return array
     * Kanzaki Tsukasa
     */
    public static function tableList(int $status = null): array
    {
        $keyword = self::$listKeyWord;
        $condition = self::$listCondition;
        if (isset($status) && !isset($condition['status'])) {
            $condition['status'] = ['egt', $status];
        } elseif (!isset($status) && !isset($condition['status'])) {
            // 默认为不删除
            $condition['status'] = ['egt', self::STATUS_FORBIDDEN];
        }
        $field = self::$listField;
        $page = implode(',', self::$listPage);
        return Db::name('Posts')
            ->where($condition)
            ->field($field)
            ->page($page)
            ->select();
    }

    public static function read(int $postId, int $status = null): array
    {
        if (!isset($status)) {
            $status = self::STATUS_PUBLISHED;
        }
        $condition = [
            'status' => ['egt', $status],
            'post_id' => $postId,
        ];
        $field = self::$readField;
        $item = Db::name('Posts')
            ->where($condition)
            ->field($field)
            ->find();
        if ($item) {
            $item['tags'] = explode(',', $item['tags']);
        } else {
            $item = [];
        }
        return $item;
    }

    /**
     * 获取操作ID
     *
     * @return int
     * Kanzaki Tsukasa
     */
    public function getId(): int
    {
        return $this->postId;
    }

    /**
     * 插入文章
     * （数据在构造时载入）
     *
     * @param int $publisherId 发布者 ID
     * @return void
     * Kanzaki Tsukasa
     */
    public function add(int $publisherId)
    {
        if (!empty($this->data['post_id'])) {
            return $this->edit();
        } else {
            unset($this->data['post_id']);
        }
        // deal publisher
        $userInfo = Users::getInfo($publisherId);
        $publisher = empty($userInfo['nickname']) ?
        $userInfo['username'] : $userInfo['nickname'];
        $this->data['publisher'] = empty($publisher) ?
        self::DEFAULT_PUBLISHER : $publisher;
        $this->data['publisher_id'] = empty($publisherId) ?
        self::DEFAULT_PUBLISHER_ID : $publisherId;
        // deal title pinyin
        $title_pinyin = Pinyin::keywords2Pinyin($this->data['title']);
        $this->data['title_pinyin'] = $title_pinyin[0] . '|' . str_replace(' ', '', $title_pinyin[0]);
        // time deal
        Timestamp::addTime($this->data, self::$insertTimeField);
        // status deal
        $this->data['status'] = isset($this->data['status']) ?
        $this->data['status'] : self::STATUS_SAVE_ONLY;
        // tag deal
        $tags = [];
        if (!empty($this->data['tags'])) {
            $tags = Tags::insertOnce($this->data['tags']);
            $tags_pinyin = Pinyin::keywords2Pinyin($this->data['tags']);
            $this->data['tags'] = implode(',', $this->data['tags']);
            $this->data['tags_pinyin'] = implode(',', $tags_pinyin) .
            '|' .
            str_replace(' ', '', implode(',', $tags_pinyin));
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

    public function del(int $postId): bool
    {
        try {
            $data = [
                'post_id' => $postId,
                'status' => self::STATUS_DELETE,
            ];
        } catch (\Exception $e) {

        }
    }

    /**
     * 获取错误信息
     *
     * @return string
     * Kanzaki Tsukasa
     */
    public function getError(): string
    {
        return $this->error;
    }

}
