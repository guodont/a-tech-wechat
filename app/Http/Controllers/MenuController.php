<?php

namespace App\Http\Controllers;

use EasyWeChat\Foundation\Application;
use Illuminate\Http\Request;

use App\Http\Requests;

class MenuController extends Controller
{
    //
    public $menu;

    /**
     * MenuController constructor.
     * @param $menu
     */
    public function __construct(Application $wechat)
    {
        $this->menu = $wechat->menu;
    }


    public function menu()
    {
        $buttons = [
            [
                "type" => "click",
                "name" => "我要提问",
                "key"  => "NK110_ADD_QUESTION"

            ],
            [
                "type" => "view",
                "name" => "个人中心",
                "url"  => "http://wechat.workerhub.cn/userCenter"
            ],
            [
                "name"       => "测试",
                "sub_button" => [
                    [
                        "type" => "view",
                        "name" => "搜索",
                        "url"  => "http://www.baidu.com/"
                    ],
                    [
                        "type" => "click",
                        "name" => "绑定农科110账号",
                        "key"  => "NK110_BIND_ACCOUNT"
                    ],
                    [
                        "type" => "click",
                        "name" => "微信授权",
                        "key" => "NK110_AUTH"
                    ],
                ],
            ],
        ];
        $this->menu->add($buttons);
    }

    public function menus()
    {
        return $this->menu->current();
    }

}
