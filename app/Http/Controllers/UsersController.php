<?php

namespace App\Http\Controllers;

use EasyWeChat\Foundation\Application;
use EasyWeChat\Server\BadRequestException;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

use App\Http\Requests;
use EasyWeChat\Support\Log;

class UsersController extends Controller
{
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

        // TODO 先判断用户是否已绑定农科110账号

        return view('bindaccount', compact('user'));
    }

    public function doBindAccount(Request $request)
    {
        $openId = $request->input('openId');
        $avatar = $request->input('avatar');
        $userName = $request->input('name');
        $phone = $request->input('phone');
        $password = $request->input('password');

        $response = $this->fetchFile($avatar, 'images', $openId);

        $avatarKey = json_decode($response->getBody()->getContents(), true)['key'];

        $data = array('phone' => $phone, 'password' => $password, 'openId' => $openId, 'avatar' => $avatarKey, 'userName' => $userName);

        $client2 = new Client(['base_uri' => 'http://sxnk110.workerhub.cn:9000/api/v1/']);

        Log::info('绑定公众号');

        try {
            $response = $client2->request('POST', 'bindWeChat', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ],
                'body' => json_encode($data)
            ]);

            Log::info('结果:' . $response->getStatusCode());

            $info = [
                'title' =>'账号绑定成功',
                'message' =>'您的农科110账号与微信已成功绑定!',
                'icon' =>'weui_icon_success weui_icon_msg'
            ];

            return view('info', compact('info', 'info'));

        } catch (BadRequestException $exception) {
            $info = [
                'title' =>'账号绑定失败',
                'message' =>'账号绑定失败,请检查手机号和密码是否正确!',
                'icon' =>'weui_icon_msg weui_icon_warn'
            ];

            return view('info', compact('info', 'info'));

        }
        

//        $wechat = app('wechat');
//
//        $qrcode = $wechat->qrcode;
//
//        $result = $qrcode->temporary(56, 3600);

//        成功: weui_icon_success weui_icon_msg
//        警告: weui_icon_safe weui_icon_safe_warn
//        错误: weui_icon_msg weui_icon_warn

//        if($response->getStatusCode() =='200' || $response->getStatusCode() =='201') {
//            $info = [
//                'title' =>'账号绑定成功',
//                'message' =>'您的农科110账号与微信已成功绑定!',
//                'icon' =>'weui_icon_success weui_icon_msg'
//            ];
//        } else{
//            $info = [
//                'title' =>'账号绑定失败',
//                'message' =>'账号绑定失败,请检查手机号和密码是否正确!',
//                'icon' =>'weui_icon_msg weui_icon_warn'
//            ];
//        }


    }

    public function sendTempMsg(Request $request)
    {

        $userId = $request->input('openId');
        $templateId = $request->input('templateId');
        $url = $request->input('url');
        $color = $request->input('color');
        $first = $request->input('first');
        $keyword1 = $request->input('keyword1');
        $keyword2 = $request->input('keyword2');
        $keyword3 = $request->input('keyword3');
        $keyword4 = $request->input('keyword4');
        $remark = $request->input('remark');

        $data = array(
            "first" => $first,
            "keyword1" => $keyword1,
            "keyword2" => $keyword2,
            "keyword3" => $keyword3,
            "keyword4" => $keyword4,
            "remark" => $remark,
        );

        $wechat = app('wechat');

        $notice = $wechat->notice;

        $messageId = $notice->uses($templateId)->withUrl($url)->andData($data)->color($color)->andReceiver($userId)->send();

    }

    public function fetchFile($file_url = '', $file_type = 'images', $open_id = '')
    {
        $encodedURL = str_replace(array('+', '/'), array('-', '_'), base64_encode($file_url));
        $encodedEntryURI = str_replace(array('+', '/'), array('-', '_'), base64_encode('nk110-' . $file_type . ':wechat_' . $open_id . '_avatar'));
        $url = '/fetch/' . $encodedURL . '/to/' . $encodedEntryURI;

        $sign = hash_hmac('sha1', $url . "\n", env('QINIU_SECRET_KEY', 'qiniu_secret_key'), true);
        $token = env('QINIU_ACCESS_KEY', 'qiniu_access_key') . ':' . str_replace(array('+', '/'), array('-', '_'), base64_encode($sign));

        $client = new Client();
        $response = $client->request('POST', 'http://iovip.qbox.me' . $url, [
            'headers' => [
                'Authorization' => 'QBox ' . $token,
                'Accept' => 'application/json',
                'Content-Type' => 'application/x-www-form-urlencoded'
            ]
        ]);
        return $response;
    }
}
