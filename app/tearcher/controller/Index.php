<?php
namespace app\tearcher\controller;

use app\BaseController;

class Index extends BaseController
{
    public function index()
    {
        echo 'tearcher';
    }

    public function hello($name = 'ThinkPHP6')
    {
        return 'hello,' . $name;
    }
}
