@extends('layouts.master')

@section('title','绑定农科110账号')

@section('content')
    <form action="./doBindAccount" method="post">
        <div class="weui_cells weui_cells_form">
            <div class="weui_cell">
                <div class="weui_cell_hd">
                    <label class="weui_label">手机号</label>
                </div>
                <div class="weui_cell_bd weui_cell_primary">
                    <input class="weui_input" type="number" pattern="[0-9]*" placeholder="请输入手机号"></div>
            </div>
            <div class="weui_cell">
                <div class="weui_cell_hd">
                    <label class="weui_label">密码</label>
                </div>
                <div class="weui_cell_bd weui_cell_primary">
                    <input class="weui_input" type="text" placeholder="请输入密码"></div>
            </div>
        </div>
        <div class="weui_btn_area">
            <input class="weui_btn weui_btn_primary" type="submit" id="showTooltips" value="确认绑定"/>
        </div>
    </form>
@endsection