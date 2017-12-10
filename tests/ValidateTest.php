<?php
namespace tests;

use think\Loader;

class ValidateTest extends TestCase
{
    public function testCheckUser()
    {
        $validate = Loader::validate('app\\index\\validate\\Users');

        // $this->dump($validate);

        // $data = ['username' => 'foo_'];
        // $this->dump($validate->check($data));
        // $this->dump($validate->getError());

        // $data = ['username' => '_bar'];
        // $this->dump($validate->check($data));
        // $this->dump($validate->getError());

        // $data = ['username' => 'foo-bar'];
        // $this->dump($validate->check($data));
        // $this->dump($validate->getError());

        // $data = ['username' => '1235'];
        // $this->dump($validate->check($data));
        // $this->dump($validate->getError());

        // $data = ['username' => '1235_'];
        // $this->dump($validate->check($data));
        // $this->dump($validate->getError());

        // $data = ['username' => '<?php'];
        // $this->dump($validate->check($data));
        // $this->dump($validate->getError());

        // $data = ['username' => '@bb.code'];
        // $this->dump($validate->check($data));
        // $this->dump($validate->getError());

        // $data = ['username' => 'a@bb.code'];
        // $this->dump($validate->check($data));
        // $this->dump($validate->getError());

        // $data = ['name' => '@bb.code'];
        // $this->dump($validate->check($data));
        // $this->dump($validate->getError());
    }
}
