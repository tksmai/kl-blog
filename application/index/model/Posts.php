<?php

namespace app\index\model;

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
    private $data = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function tableList()
    {

    }

    public static function read()
    {

    }

    public function add()
    {
        if ($this->data['id']) {
            return $this->edit();
        }

    }

    public function edit()
    {

    }

    public function del()
    {

    }
}
