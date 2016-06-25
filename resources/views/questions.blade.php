@extends('layouts.master')

@section('title','农科110-热门问题')

@section('content')
    <div class="weui_panel weui_panel_access">
        <div class="question">
            <div class="weui_panel_hd">热门问题</div>
            <div class="weui_panel_bd">
                @foreach ($questions as $question)
                    <a href="./question/{{ $question->id }}" class="weui_media_box weui_media_appmsg">
                        <div class="weui_media_hd">
                            @if(explode(',',$question->images)[0] != null)
                                <img class="weui_media_appmsg_thumb"
                                     src="{{ explode(',',$question->images)[0] }}"
                                     alt="">
                            @else
                                <img class="weui_media_appmsg_thumb"
                                     src="http://storage.workerhub.cn/image/icon/icon_question.png"
                                     alt="">
                            @endif

                        </div>
                        <div class="weui_media_bd">
                            <h4 class="weui_media_title">{{ $question->title }}</h4>
                            <p class="weui_media_desc">{{ $question->user->name }}
                                发布于 {{ date('Y-m-d H:i',mb_substr($question->whenCreated),0,9) }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
@endsection