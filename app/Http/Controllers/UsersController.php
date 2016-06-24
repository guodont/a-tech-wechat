<?php

namespace App\Http\Controllers;

use EasyWeChat\Foundation\Application;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

use App\Http\Requests;
use EasyWeChat\Support\Log;

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

        $question = array('categoryId' => '73', 'title' => 'ddddssss', 'content' => 'dddddd');
        // 先认证
        $client2 = new Client(['base_uri' => 'http://sxnk110.workerhub.cn:9000/api/v1/']);

        Log::info('收到问题消息');

        $response = $client2->request('POST', 'question', [
            'headers' => [
                'X-AUTH-TOKEN' => '2b80b635-e584-44fd-b991-5d0f6c187f5f',
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode($question)
        ]);

        Log::info('结果:' . $response->getStatusCode());

        $wechat = app('wechat');

        $qrcode = $wechat->qrcode;

        $result = $qrcode->temporary(56, 3600);

        return view('welcome', compact('result', 'qrcode'));

    }
}
