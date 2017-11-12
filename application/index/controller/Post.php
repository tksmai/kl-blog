<?php

namespace app\index\controller;

use app\index\model\Posts;
use think\Db;
use think\Loader;
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
        $item = Db::name('posts')->find($id);
        $item['tags'] = explode(',', $item['tags']);
        return json($item, 200);
    }

    public function save(Request $request): Response
    {
        $data = $request->post();
        // validate
        $validate = Loader::validate('Posts');
        $rec = $validate->check($data);
        if ($rec === false) {
            return json(['msg' => $validate->getError()], 400);
        }
        $post = new Posts($data);
        if ($post->add(1)) {
            $rec = ['msg' => 'success', 'post_id' => $post->getId()];
            $code = 200;
        } else {
            $rec = ['msg' => $post->getError(), 'post_id' => 0];
            $code = 500;
        }
        return json($rec, $code);
    }
}
