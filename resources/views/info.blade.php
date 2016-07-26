@extends('layouts.master')

@section('title',$info['title'])

@section('content')
    <div class="msg">
        <div class="weui_msg">
            <div class="weui_icon_area"><i class="{{ $info['icon'] }}"></i></div>
            <div class="weui_text_area">
                <h2 class="weui_msg_title">操作提示</h2>
                <p class="weui_msg_desc">{{ $info['message'] }}</p>
            </div>
        </div>
    </div>
@endsection
