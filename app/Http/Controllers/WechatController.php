<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use GuzzleHttp\Client;

class WechatController extends Controller
{
    /**
     * 处理微信的请求消息
     *
     * @return string
     */
    public function serve()
    {
//        Log::info('request arrived.'); # 注意：Log 为 Laravel 组件，所以它记的日志去 Laravel 日志看，而不是 EasyWeChat 日志

        $wechat = app('wechat');
        $userApi = $wechat->user;
        $wechat->server->setMessageHandler(function ($message) use ($userApi) {
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
                        if ($message->EventKey == 'NK110_AUTH') {
                            return '<a href=\'http://wechat.workerhub.cn/auth\'>微信授权</a>';
                        }
                        if ($message->EventKey == 'NK110_ADD_QUESTION') {
                            return '请用一段话描述您的问题,并直接回复给此公众号。问题提交成功后我们将返回给您信息。';
                        }
                    }
                    break;
                case 'text':
                    # 文字消息...
                    // TODO 处理消息 提交到 nk110.workerhub.cn:9000/question
                    $client = new Client();
                    switch ($message->Content) {
                        case 'post':
                            break;
                        case 'get':
                            $response = $client->get('http://www.baidu.com');
                            return '返回状态码:'.$response->getStatusCode();
                            break;
                        default:
                            // 先认证
                            return $userApi->get($message->FromUserName)->nickname .'您好,您的问题已经提交成功,我们的专家将尽快为您解答,解答后将直接回复给您。' ;
                            break;
                    }
                    
                    break;
                case 'image':
                    # 图片消息...
                    $response = $this->fetchFile($message->PicUrl, 'images', $message->FromUserName, $message->CreateTime);
                    switch ($response->getStatusCode()) {
                        case 200:
                            return '上传成功';
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
                    $temporary->download($message->MediaId, "/home/banana/web/a-tech-wechat/storage/app/public", 'wechat_voice'.$message->FromUserName.$message->CreateTime.'.'.$message->Format);
                    return $message->MediaId;
                    break;
                case 'video':
                    # 视频消息...
                    break;
                case 'location':
                    # 坐标消息...
                    break;
                case 'link':
                    # 链接消息...
                    break;
                // ... 其它消息
                default:
                    # code...
                    break;
            }

        });

//        Log::info('return response.');

        return $wechat->server->serve();
    }

    public function fetchFile($file_url='', $file_type='images', $open_id='', $timestamp='')
    {
        $encodedURL = str_replace(array('+', '/'), array('-', '_'), base64_encode($file_url));
        $encodedEntryURI = str_replace(array('+', '/'), array('-', '_'), base64_encode('nk110-'.$file_type.':wechat_'.$open_id.'_'.$timestamp));
        $url = '/fetch/'.$encodedURL.'/to/'.$encodedEntryURI;
        $sign = hash_hmac('sha1', $url . "\n", env('QINIU_SECRET_KEY', 'qiniu_secret_key'), true);
        $token =  env('QINIU_ACCESS_KEY', 'qiniu_access_key') . ':' . str_replace(array('+', '/'), array('-', '_'), base64_encode($sign));
        $client = new Client();
        $response = $client->request('POST', 'http://iovip.qbox.me'.$url, [
            'headers' => [
                'Authorization' => 'QBox '.$token,
                'Accept'     => 'application/json',
                'Content-Type'      => 'application/x-www-form-urlencoded'
            ]
        ]);
        return $response;
    }
}
