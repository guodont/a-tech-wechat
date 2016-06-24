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

        return view('usercenter', compact('user', 'questions'));
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

        $user = session('wechat.oauth_user'); // 拿到授权用户资料
        $openId = $user->getId();
        $avatar = $user->getAvatar();
        $userName = $user->getName();
        $phone = Request::get('phone');
        $password = Request::get('password');

        $data = array('phone' => $phone, 'password' => $password, 'openId' => $openId, 'avatar' => $avatar, 'userName' => $userName);

        $client2 = new Client(['base_uri' => 'http://sxnk110.workerhub.cn:9000/api/v1/']);

        Log::info('绑定公众号');

        $response = $client2->request('POST', 'bindWeChat', [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode($data)
        ]);

        Log::info('结果:' . $response->getStatusCode());

        $wechat = app('wechat');

        $qrcode = $wechat->qrcode;

        $result = $qrcode->temporary(56, 3600);
        
        return view('welcome', compact('result', 'qrcode'));

    }
}
