<?php

namespace app\index\controller;

use app\index\model\Posts;
use think\Loader;
use think\Request;
use think\Response;

class Post
{
    /**
     * GET /Post
     * 定义为获取Post列表
     *
     * @param Request $request
     * @return Response
     * Kanzaki Tsukasa
     */
    public function index(Request $request): Response
    {
        $page = $request->get('p/d', 1);
        Posts::setPage($page);
        $list = Posts::tableList(Posts::STATUS_PUBLISHED);
        return json($list, 200);
    }

    /**
     * GET /Post/:id
     * 定义为获取单个Post
     *
     * @param Request $request
     * @param int $id
     * @return Response
     * Kanzaki Tsukasa
     */
    public function read(Request $request, int $id): Response
    {
        $item = Posts::read($id, Posts::STATUS_PUBLISHED);
        $code = $item ? 200 : 204;
        return json($item, $code);
    }

    /**
     * POST /Post
     * 定义为新增Post
     *
     * @param Request $request
     * @return Response
     * Kanzaki Tsukasa
     */
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
        // 获取用户资料，此处写死1
        $user = ['user_id', 1];
        if ($post->add($user['user_id'])) {
            $rec = ['msg' => 'success', 'post_id' => $post->getId()];
            $code = 200;
        } else {
            $rec = ['msg' => $post->getError(), 'post_id' => 0];
            $code = 500;
        }
        return json($rec, $code);
    }
}
