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
                "key" => "NK110_ADD_QUESTION"
            ],
            [
                "type" => "view",
                "name" => "热门问题",
                "url" => "http://wechat.workerhub.cn/questions"
            ],
            [
                "name" => "我的",
                "sub_button" => [
                    [
                        "type" => "click",
                        "name" => "关于我们",
                        "key" => "NK110_ABOUT_US"
                    ],
                    [
                        "type" => "click",
                        "name" => "投诉建议",
                        "key" => "NK110_COMPLAIN"
                    ],
                    [
                        "type" => "view",
                        "name" => "搜索",
                        "url" => "http://www.baidu.com/"
                    ],
                    [
                        "type" => "click",
                        "name" => "绑定农科110账号",
                        "key" => "NK110_BIND_ACCOUNT"
                    ],
                    [
                        "type" => "click",
                        "name" => "下载农事易客户端",
                        "key" => "NK110_DOWNLOAD_APP"
                    ],
//                    [
//                        "type" => "click",
//                        "name" => "微信授权",
//                        "key" => "NK110_AUTH"
//                    ],
                    [
                        "type" => "view",
                        "name" => "我的问题",
                        "url" => "http://wechat.workerhub.cn/userCenter"
                    ]
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
