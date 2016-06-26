<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use GuzzleHttp\Client;
use EasyWeChat\Support\Log;

// qiNiu

// 引入鉴权类
use Qiniu\Auth;

// 引入上传类
use Qiniu\Storage\UploadManager;

class WechatController extends Controller
{

    const BASE_URL = "http://sxnk110.workerhub.cn:9000/api/v1/";
    const AUTH_URL = "http://sxnk110.workerhub.cn:9000/api/v1/wechat_auth";
    const SAVE_PATH = "/home/banana/web/a-tech-wechat/storage/app/public";

    /**
     * 处理微信的请求消息
     *
     * @return string
     */
    public function serve()
    {
        Log::info('request arrived.'); # 注意：Log 为 Laravel 组件，所以它记的日志去 Laravel 日志看，而不是 EasyWeChat 日志
        $wechat = app('wechat');
        $userApi = $wechat->user;
        $temporary = $wechat->material_temporary;
        $wechat->server->setMessageHandler(function ($message) use ($userApi, $temporary) {
            switch ($message->MsgType) {
                case 'event':
                    # 事件消息...
                    if ($message->Event == 'subscribe') {
                        return '您好,欢迎关注农事易';
                    }
                    if ($message->Event == 'CLICK') {
                        if ($message->EventKey == 'NK110_BIND_ACCOUNT') {
                            return "<a href='http://wechat.workerhub.cn/bindAccount'>点此绑定农科110账号</a>";
                        }
                        if ($message->EventKey == 'NK110_DOWNLOAD_APP') {
                            return "<a href='http://fir.im/lacd'>点此下载农事易App</a>";
                        }
                        if ($message->EventKey == 'NK110_AUTH') {
                            return '<a href=\'http://wechat.workerhub.cn/auth\'>微信授权</a>';
                        }
                        if ($message->EventKey == 'NK110_ADD_QUESTION') {
                            return '请用一段话或者一条语音消息描述您的问题,并直接回复给此公众号。问题提交成功后我们将返回给您信息。';
                        }
                        if ($message->EventKey == 'NK110_ABOUT_US') {
                            return '农科110';
                        }
                        if ($message->EventKey == 'NK110_COMPLAIN') {
                            return '投诉建议请直接在消息前加"反馈:" 例如:(反馈:这里写对我们的意见建议~)';
                        }
                    }
                    break;
                case 'text':
                    # 文字消息...
                    $client = new Client();
                    switch ($message->Content) {
                        case 'post':
                            break;
                        case 'get':
                            $response = $client->get('http://www.baidu.com');
                            return '返回状态码:' . $response->getStatusCode();
                            break;
                        default:
                            $question = array('categoryId' => '73', 'title' => $message->Content, 'content' => $message->Content);
                            //  先认证
                            //  请求认证 category=73
//                            $auth_client = new Client();
//                            $auth_response = $auth_client->request('POST', $this->auth_url, [
//                                'headers' => [
//                                    'WECHAT-AUTH-TOKEN' => base64_encode(hash_hmac("sha256", env("ATECH_TOKEN", "test"), env("ATECH_AES_KEY", "TUzMK0U321xLe241HfRA97OZhon0O7Rr7bSyA5Id"))),
//                                    'Accept' => 'application/json',
//                                    'Content-Type' => 'application/json'
//                                ],
//                                'body' => $message->FromUserName
//                            ]);

//                            Log::info('认证结果' . $auth_response->getStatusCode());

                            $client2 = new Client(['base_uri' => "http://sxnk110.workerhub.cn:9000/api/v1/"]);

                            Log::info('收到问题消息');

                            $response = $client2->request('POST', 'wechat/question', [
                                'headers' => [
                                    'WECHAT-OPEN-ID' => $message->FromUserName,
                                    'Accept' => 'application/json',
                                    'Content-Type' => 'application/json'
                                ],
                                'body' => json_encode($question)
                            ]);

                            Log::info('结果:' . $response->getStatusCode());
                            Log::info('收到问题消息2');

                            switch ($response->getStatusCode()) {
                                case 200:
                                    return $userApi->get($message->FromUserName)->nickname . '您好,您的问题已经提交成功,我们的专家将尽快为您解答,解答后将直接回复给您。';
                                    break;
                                default:
                                    return $userApi->get($message->FromUserName)->nickname . '您好,您的问题提交失败,请确保您已通过微信授权';
                                    break;
                            }
                            break;
                    }

                    break;
                case 'image':
                    # 图片消息...
                    $response = $this->fetchFile($message->PicUrl, 'images', $message->FromUserName, $message->CreateTime);
                    switch ($response->getStatusCode()) {
                        case 200:
                            return '上传成功' . $response->getBody()->getContents();
                            break;
                        case 401:
                            return 'token验证失败';
                            break;
                        case 400:
                            return '请求报文格式错误';
                            break;
                        case 404:
                            return '抓取资源不存在';
                            break;
                        case 478:
                            return '源站返回404外，所有非200的response都返回478';
                            break;
                        case 599:
                            return '服务端操作失败';
                            break;
                        default:
                            return $message->PicUrl;
                            break;
                    }
                    break;
                case 'voice':
                    # 语音消息...
                    $voiceFileName = 'wechat_voice' . $message->FromUserName . "_" . $message->CreateTime;
                    // 下载到本地
                    $temporary->download($message->MediaId, "/home/banana/web/a-tech-wechat/storage/app/public", $voiceFileName);
                    // 上传到七牛
                    $accessKey = env("QI_NIU_ACCESS_KEY", "Access_Key");
                    $secretKey = env("QI_NIU_SECRET_KEY", 'Secret_Key');

                    // 构建鉴权对象
                    $auth = new Auth($accessKey, $secretKey);

                    // 要上传的空间
                    $bucket = 'nk110-images';

                    // 生成上传 Token
                    $token = $auth->uploadToken($bucket);

                    // 要上传文件的本地路径
                    $filePath = "/home/banana/web/a-tech-wechat/storage/app/public" . $voiceFileName . '.amr';

                    // 上传到七牛后保存的文件名
                    $key = $voiceFileName;

                    // 初始化 UploadManager 对象并进行文件的上传
                    $uploadMgr = new UploadManager();

                    // 调用 UploadManager 的 putFile 方法进行文件的上传
                    list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
//
                    //  转码
                    $transcoding = $this->transcodingFile($voiceFileName);

                    $voice_question = array('categoryId' => '73', 'title' => '来自微信的语音问题', 'content' => '来自微信的语音问题', 'mediaId' => $voiceFileName . '_2');

                    $client2 = new Client(['base_uri' => "http://sxnk110.workerhub.cn:9000/api/v1/"]);

                    $response = $client2->request('POST', 'wechat/question', [
                        'headers' => [
                            'WECHAT-OPEN-ID' => $message->FromUserName,
                            'Accept' => 'application/json',
                            'Content-Type' => 'application/json'
                        ],
                        'body' => json_encode($voice_question)
                    ]);

                    switch ($response->getStatusCode()) {
                        case 200:
                            return $userApi->get($message->FromUserName)->nickname . '您好,您的问题已经提交成功,我们的专家将尽快为您解答,解答后将直接回复给您。';
                            break;
                        default:
                            return $userApi->get($message->FromUserName)->nickname . '您好,您的问题提交失败,请确保您已通过微信授权';
                            break;
                    }
                    break;

                case 'video':
                    # 视频消息...
                    return '这段视频拍的不错';
                    break;
                case 'location':
                    # 坐标消息...
                    return '坐标消息';
                    break;
                case 'link':
                    # 链接消息...
                    return '链接消息';
                    break;
                // ... 其它消息
                default:
                    # code...
                    return '其它消息';
                    break;
            }

        });

        return $wechat->server->serve();
    }

    /**
     *
     * 抓取并转存文件
     * @param string $file_url
     * @param string $file_type
     * @param string $open_id
     * @param string $timestamp
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function fetchFile($file_url = '', $file_type = 'images', $open_id = '', $timestamp = '')
    {
        $encodedURL = str_replace(array('+', '/'), array('-', '_'), base64_encode($file_url));
        $encodedEntryURI = str_replace(array('+', '/'), array('-', '_'), base64_encode('nk110-' . $file_type . ':wechat_' . $open_id . '_' . $timestamp));
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

    /**
     *
     * 转码并转存文件
     * @param string $file_name
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function transcodingFile($file_name = '')
    {
        $url = '/pfop/';

        $body = 'bucket=nk110-images&key=' . $file_name . '&fops=avthumb/mp3/ab/128k/ar/44100/acodec/libmp3lame|saveas/' . base64_encode('nk110-images:' . $file_name . '_2') . '&pipeline=nk110-video-queue-1';

        $sign = hash_hmac('sha1', $url . "\n" . $body, env('QINIU_SECRET_KEY', 'qiniu_secret_key'), true);
        $token = env('QINIU_ACCESS_KEY', 'qiniu_access_key') . ':' . str_replace(array('+', '/'), array('-', '_'), base64_encode($sign));

        $client = new Client();
        $response = $client->request('POST', 'http://api.qiniu.com' . $url, [
            'headers' => [
                'Authorization' => 'QBox ' . $token,
                'Content-Type' => 'application/x-www-form-urlencoded'
            ],
            'body' => $body
        ]);
        return $response;
    }
}
