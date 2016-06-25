@extends('layouts.master')

@section('title',$question->title)

@section('content')
    <style>
        .custom-audio-weixin {
            position:relative;
        }
        .custom-audio-bar {
            width: 185px;
            height: 42px;
            left: 50px;
            cursor: pointer;
        }
        .custom-audio-bar, .custom-audio-unread {
            display: inline-block;
            position: absolute;
        }
        .custom-audio-bar{
            background: url(https://b.yzcdn.cn/v2/image/wap/audio/sprite_v5.png) no-repeat;
            background-size: 400px 175px;
        }
        .clearfix, .container .content {
            zoom: 1;
        }
        .custom-audio-time{
            display:block;
            color: #999;
            font-size: 14px;
            position: absolute;
            left: 240px;
            bottom: 5px
        }
        .custom-audio-weixin{
            margin: 20px;
        }
        .custom-audio-animation {
            margin-top:12px;
            margin-left: 22px;
            width: 13px;
            height: 17px;
        }

        .hide {
            display: block;
            visibility: hidden;
        }

        .custom-audio-bar img {
            position: absolute;
            /* left: 21px;
             top: 12px;*/
            z-index: 2;
        }
        .custom-audio-animation-static, .custom-audio-bar, .custom-audio-btn, .custom-audio-unread {
            background: url(https://b.yzcdn.cn/v2/image/wap/audio/sprite_v5.png) no-repeat;
            background-size: 400px 175px;
        }

        .custom-audio-animation-static {
            background-position: -180px -105px;
            width: 13px;
            height: 17px;
            display: block;
            position: absolute;
            left: 21px;
            top: 12px;
            z-index: 1;
        }


        .custom-audio-status-play .custom-audio-bar .custom-audio-animation {
            display: block!important;
            visibility: visible;
        }
    </style>
    <div class="weui_panel_bd">
        <a href="javascript:void(0);" class="weui_media_box weui_media_appmsg">
            <div class="hd">
                @if ($question->user->avatar != '')
                    <img class="image" src="http://storage.workerhub.cn/{{ $question->user->avatar }}"/>
                @else
                    <img class="image" src="http://storage.workerhub.cn/default_avatar.png"/>
                @endif
            </div>
            <div class="weui_media_bd">
                <h4 class="weui_media_title">{{ $question->user->name }}</h4>
                <p class="weui_media_desc">发布于{{ date('y-m-d H:i',$question->whenCreated) }}</p>
            </div>
        </a>
    </div>
    <hr>

    @if ($question->mediaId == '')
        <div class="article">
            <h4 class="title">{{ $question->title }}</h4>
            <article class="weui_article">
                <section>
                    <p>
                        {{ $question->content }}
                    </p>
                </section>
                <div class="weui_uploader_bd">
                    <ul class="weui_uploader_files">
                        @foreach(explode(',',$question->images) as $image)
                            <li class="weui_uploader_file">
                                <img src="{{ $image }}" alt="">
                            </li>
                        @endforeach
                    </ul>
                </div>
            </article>
        </div>
    @else
        <div class="custom-audio js-audio" data-src="http://storage.workerhub.cn/{{ $question->mediaId }}"
             data-reload="true" data-loop="false">
            <div class="custom-audio-weixin clearfix">
                <div>
                    {{----}}
                    {{--<img class="custom-audio-logo js-not-share gifs"--}}
                         {{--src="https://img.yzcdn.cn/upload_files/2015/01/26/FoX21i8VqwlCwjyDW618p66vd7g8.png?imageView2/2/w/80/h/80/q/75/format/png"--}}
                         {{--alt="��Ƶ����logo" width="40" height="40">--}}
                    {{----}}
					<span class="custom-audio-bar">
						<img class="js-animation custom-audio-animation hide js-not-share gifs"
                             src="https://b.yzcdn.cn/v2/image/wap/audio/player.gif" alt="" width="13"
                             height="17"> <i class="custom-audio-animation-static js-animation-static"></i>
                        <!-- <img class="custom-audio-loading js-loading js-not-share" src="https://b.yzcdn.cn/v2/image/wap/common/loading.gif" alt="loading"> -->
						<span class="custom-audio-status js-status"></span>
					</span>
                    <span class="custom-audio-time js-duration">点击播放语音</span>
                </div>

            </div>
        </div>
    @endif

    @if ($question->answer != '')
        <hr>
        <div class="weui_panel_bd">
            <a href="javascript:void(0);" class="weui_media_box weui_media_appmsg">
                <div class="hd">
                    <img class="image" src="http://storage.workerhub.cn/default_avatar.png"/>
                </div>
                <div class="weui_media_bd">
                    <h4 class="weui_media_title">{{ $question->expert->realName }}</h4>
                    <p class="weui_media_desc">回答于{{ date('y-m-d H:i',$question->whenUpdated) }}</p>
                </div>
            </a>
        </div>
        <hr>
        <article class="weui_article">
            <section>
                <p>
                    {{ $question->answer }}
                </p>
            </section>
        </article>
    @endif


    <script charset="utf-8" src="https://b.yzcdn.cn/v2/build/wap/common_jquery_75554d22a0.js"></script>
    <script charset="utf-8" src="https://b.yzcdn.cn/v2/build/wap/showcase/modules/audio_37df0347f6.js"></script>

@endsection