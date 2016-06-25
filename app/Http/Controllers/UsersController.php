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
        // http://sxnk110.workerhub.cn:9000/api/v1/wechat/my/questions?status=WAIT_RESOLVE&pageSize=10&page=1

        $user = session('wechat.oauth_user'); // 拿到授权用户资料

        // TODO 先判断用户是否已绑定农科110账号

        $client = new Client(['base_uri' => 'http://sxnk110.workerhub.cn:9000/api/v1/']);

        // 获取用户发布的问题
        $response = $client->request('GET', 'wechat/my/questions', [
            'headers' => [
                'WECHAT-OPEN-ID' => $user->getId(),
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ]);

        $questions = json_decode($response->getBody());

        return view('usercenter', compact('user', 'questions'));

        // TODO 跳到绑定账号视图
    }

    public function auth()
    {
        $response = $this->wechat->oauth->scopes(['snsapi_userinfo'])
            ->redirect();

        return $response;
    }

    public function bindAccount()
    {
        $user = session('wechat.oauth_user'); // 拿到授权用户资料

        return view('bindaccount', compact('user'));
    }

    public function doBindAccount(Request $request)
    {
        $openId = $request->input('openId');
        $avatar = $request->input('avatar');
        $userName = $request->input('name');
        $phone = $request->input('phone');
        $password = $request->input('password');

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

    public function sendTempMsg(Request $request)
    {

//        $userId = $request->input('openId');
//        $templateId = $request->input('templateId');
//        $url = $request->input('url');
//        $color = $request->input('color');
//        $first = $request->input('first');
//        $keyword1 = $request->input('keyword1');
//        $keyword2 = $request->input('keyword2');
//        $keyword3 = $request->input('keyword3');
//        $keyword4 = $request->input('keyword4');
//        $remark = $request->input('remark');
//
//        $data = array(
//            "first" => $first,
//            "keyword1" => $keyword1,
//            "keyword2" => $keyword2,
//            "keyword3" => $keyword3,
//            "keyword4" => $keyword4,
//            "remark" => $remark,
//        );

        $userId = 'o451ewNvK3JukkMqr0BaXw_MnASI';
        $templateId = 'K_kz-KlSLOR0MyPJTxgZdKMd6xCkzY-o1VCWcyRgmF0';
        $url = 'http://wechat.workerhub.cn/question/92';
        $color = '#FF0000';

        $data = array(
            "first" => "用户您好，您的提问已有回答",
            "keyword1" => "信息",
            "keyword2" => "刚刚",
            "keyword3" => "新回答",
            "keyword4" => "2016-06-26",
            "remark" => "详细结果请点击“详情”查看！",
        );

        $wechat = app('wechat');

        $notice = $wechat->notice;

        $messageId = $notice->uses($templateId)->withUrl($url)->andData($data)->color($color)->andReceiver($userId)->send();

    }
}
