<?php
namespace tests;

use app\index\model\Tags;

class TagsTest extends TestCase
{
    public $tagData = ['PHP', 'JAVA', 'C++', 'NODEJS', 'CSharp'];
    public function testInsertOnce()
    {
        $rec = Tags::insertOnce($this->tagData);
    }
}
