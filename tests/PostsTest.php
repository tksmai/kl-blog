<?php
namespace tests;

use app\index\model\Posts;
use think\Db;

class PostsTest extends TestCase
{
    const TEST_PAGE_SIZE = 15;
    private $addData = [
        'post_id' => '0',
        'title' => '《干物妹！小埋》第2季公布主题曲详情 10月8日开播',
        'post_content' => self::POST_TEST_CONTENT,
        'tags' => ['干物妹', '小埋', '五毛妹', '秋季', '动画', '官方'],
    ];

    public function testAddPost()
    {
        $post = new Posts($this->addData);
        $flag = $post->add(1);
        // $this->dump($flag);
        $post_id = $post->getId();
        // $this->dump($post_id);
        // $this->dump($post->getError());
        $this->assertEquals($flag, true);
        // $this
        // $this->dump(json_encode($this->addData));
        $field = array_keys($this->addData);
        // $this->export($field);
    }

    public function testListPost()
    {
        $size = self::TEST_PAGE_SIZE;
        Posts::setPage(1, $size);
        // Posts::setKeyWord('nihao');
        $posts = Posts::tableList();
        // $this->dump($posts);
        // 字段测试
        $listField = ['post_id', 'title', 'publisher', 'tags', 'create_time', 'publish_time', 'modify_time', 'status'];
        foreach ($posts as $post) {
            foreach ($listField as $field) {
                $this->assertArrayHasKey($field, $post);
            }
        }
        // 分页测试
        $this->assertGreaterThanOrEqual(count($posts), $size, '分页失效');
        // 状态测试
        $this->randStatus();
        $this->checkStatus(Posts::STATUS_PUBLISHED);
        $this->checkStatus(Posts::STATUS_SAVE_ONLY);
        $this->checkStatus(Posts::STATUS_FORBIDDEN);
        $this->checkStatus(Posts::STATUS_DELETE);
    }

    private function randStatus()
    {
        $p = 1;
        $size = self::TEST_PAGE_SIZE;
        Posts::setPage($p, $size);
        while ($list = Posts::tableList(Posts::STATUS_DELETE)) {
            foreach ($list as $item) {
                $item['status'] = rand(-1, 2);
                Db::name('posts')->update($item);
            }
            $p++;
            Posts::setPage($p, $size);
        }
    }

    private function checkStatus($status = 2)
    {
        $size = self::TEST_PAGE_SIZE;
        $p = 1;
        Posts::setPage($p, $size);
        while ($list = Posts::tableList($status)) {
            foreach ($list as $item) {
                $this->assertGreaterThanOrEqual($status, $item['status']);
            }
            $p++;
            Posts::setPage($p, $size);
        }
    }

    const POST_TEST_CONTENT = <<<EOF
根据日本漫画家三角头创造的人气漫画《干物妹！小埋》改编的第2季TV动画此前已确定将于今秋开播。昨日（9月11日）官方公开了第2季的首播日期为2017年10月8日，一起主题曲概况也得到了发布。<br>
<br>

<ignore_js_op>

<center>

<img id="aimg_1175" aid="1175" src="http://zfh.moe/data/attachment/forum/201709/13/214811rpidcwy6bs48gipf.jpg" zoomfile="data/attachment/forum/201709/13/214811rpidcwy6bs48gipf.jpg" file="data/attachment/forum/201709/13/214811rpidcwy6bs48gipf.jpg" class="zoom" onclick="zoom(this, this.src, 0, 0, 0)" width="500" inpost="1" onmouseover="showMenu({'ctrlid':this.id,'pos':'12'})" lazyloaded="true" height="336" initialized="true">

</center>

<div class="tip tip_4 aimg_tip" id="aimg_1175_menu" style="position: absolute; z-index: 301; left: 226.5px; top: 487px; display: none;" disautofocus="true" initialized="true">
<div class="xs0">
<p><strong>5_170913153455_1_lit.jpg</strong> <em class="xg1">(76 Bytes, 下载次数: 0)</em></p>
<p>
<a href="http://zfh.moe/forum.php?mod=attachment&amp;aid=MTE3NXw3ZDhlMmYwYnwxNTA5ODcxNzUxfDExfDI3MTQ%3D&amp;nothumb=yes" target="_blank">下载附件</a>

</p>

<p class="xg1 y">2017-9-13 21:48 上传</p>

</div>
<div class="tip_horn"></div>
</div>


</ignore_js_op>
<br>
<br>
根据官方公开的情报，TV动画《干物妹！小埋》第2季的OP主题曲由土间埋（CV：田中爱美）演唱，曲名为《两面性☆表里两面的人生！（原名：にめんせい☆ウラオモテライフ！）》。ED主题曲则由土间埋（CV：田中爱美）、海老名菜菜（CV：影山灯）、本场切绘（CV：白石晴香）、橘·希尔芬福特（CV：古川由利奈）四位人物组成的组合“妹S”演唱，曲名为《小埋体操（原名：うまるん体操）》。OP和ED的单曲将于2017年11月15日同日出售。<br>
<br>
《干物妹！小埋》TV动画第2季在制造阵型方面根本连续了第1季的阵型，动画仍然由动画工房担任制造，太田雅彦担任导演，大隈孝晴担任副导演，青岛崇担任系列构成和剧本，高野绫担任人物设计。声优阵型为土间埋CV：田中爱美、土间和平CV：野岛健儿、海老名菜菜CV：影山灯、本场切绘CV：白石晴香、橘·希尔芬福特CV：古川由利奈、崩巴CV：安元洋贵、橘·亚力克斯CV：柿原彻也、叶课长CV：小清水亚美。<br>
<br>
EOF;
}
