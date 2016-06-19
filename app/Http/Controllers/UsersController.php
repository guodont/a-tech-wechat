<?php

namespace App\Http\Controllers;

use EasyWeChat\Foundation\Application;
use Illuminate\Http\Request;

use App\Http\Requests;

class UsersController extends Controller
{
    //
    public $wechat;

    /**
     * UsersController constructor.
     * @param $wechat
     */
    public function __construct(Application $wechat)
    {
        $this->wechat = $wechat;
    }

    public function users()
    {
        $users = $this->wechat->user->lists();

        return $users;
    }

    public function user($openId)
    {
        $user = $this->wechat->user->get($openId);

        return $user;
    }

    public function userCenter()
    {
        $config = [
            'oauth' => [
                'scopes' => ['snsapi_userinfo'],
                'callback' => '/auth',
            ],
        ];

        $app = new Application($config);
        $oauth = $app->oauth;

        // 未登录
        if (empty($_SESSION['wechat_user'])) {
            $_SESSION['target_url'] = 'userCenter';
            return $oauth->redirect();
        }

        // 已经登录过
        $user = session('wechat.oauth_user'); // 拿到授权用户资料

        return view('usercenter', compact('user'));
    }

    public function auth()
    {
        $config = [
            'oauth' => [
                'scopes' => ['snsapi_userinfo'],
                'callback' => '/auth',
            ],
        ];
        
        $app = new Application($config);
        $oauth = $app->oauth;

        // 获取 OAuth 授权结果用户信息
        $user = $oauth->user();

        $_SESSION['wechat_user'] = $user->toArray();

        $targetUrl = empty($_SESSION['target_url']) ? '/' : $_SESSION['target_url'];

        header('location:'. $targetUrl);
    }
}
