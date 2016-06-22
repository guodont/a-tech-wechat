<?php

namespace App\Http\Controllers;

use EasyWeChat\Foundation\Application;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

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
        $user = session('wechat.oauth_user'); // 拿到授权用户资料

        $client = new Client();
        $response = $client->get('http://sxnk110.workerhub.cn:9000/api/v1/questions');
        $questions = json_decode($response->getBody());

        return view('usercenter', compact('user','questions'));
    }

    public function auth()
    {
        $response = $this->wechat->oauth->scopes(['snsapi_userinfo'])
            ->redirect();

        return $response;
    }

    public function bindAccount()
    {
        return view('bindaccount');
    }

    public function doBindAccount()
    {
        $client = new Client();
        $response = $client->get('http://sxnk110.workerhub.cn:9000/api/v1/question/');
        $question = json_decode($response->getBody());
        return view('question', compact('question'));
    }
}
