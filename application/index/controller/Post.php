<?php

namespace app\index\controller;

use app\index\model\Posts;
use app\index\model\Users;
use think\Db;
use think\Request;
use think\Response;

class Post
{
    public function index(Request $request): Response
    {
        $page = $request->get('p/d', 1);
        Posts::setPage($page);
        $list = Posts::tableList();
        return json($list, 200);
    }

    public function read(Request $request, int $id): Response
    {
        $item = Db::name('posts')->find(13);
        $item['tags'] = explode(',', $item['tags']);
        return json($item, 200);
    }

    public function save(Request $request): Response
    {
        $data = $request->post();
        $user = Users::getInfo(1);
        return json($user, 200);
    }
}
