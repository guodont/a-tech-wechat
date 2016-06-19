@extends('layouts.master')

@section('title',$question->title)

@section('content')
    <div class="weui_panel_bd">
        <a href="javascript:void(0);" class="weui_media_box weui_media_appmsg">
            <div class="hd">
                <img class="image" src="http://storage.workerhub.cn/default_avatar.png"/>
            </div>
            <div class="weui_media_bd">
                <h4 class="weui_media_title">{{ $question->user->name }}</h4>
                <p class="weui_media_desc">发布于{{ date('y-m-d H:i',$question->whenCreated) }}</p>
            </div>
        </a>
    </div>
    <hr>
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
                        <li class="weui_uploader_file" style="background-image:url({{ $image }})"></li>
                    @endforeach
                </ul>
            </div>
        </article>
    </div>

@endsection