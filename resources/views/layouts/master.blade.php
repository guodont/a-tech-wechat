<html>
<head>
    <title>@yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="http://cdn.bootcss.com/weui/0.4.2/style/weui.css" rel="stylesheet">
    <style>
        body,html{
            height:100%;-webkit-tap-highlight-color:transparent
        }
        body{
            overflow-x:hidden;background-color:#fbf9fe;    line-height: 1.6;
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
        }
        .page_title{
            text-align: center;
            font-size: 34px;
            color: #3cc51f;
            font-weight: 400;
            margin: 0 15%;
        }
        div{
            display: block;
        }
        *{
            margin:0;
            padding: 0;
        }
        .title{
            padding-left:15px;
            padding-top: 20px;
        }
        h4{
            font-weight:normal !important;
        }
        .image{
            width:100px;
            height:100px;
            border-radius:50px;
        }
        .hd .image{
            width:50px;
            height:50px;
            border-radius:50px;
        }
        .weui_media_bd{
            padding-left: 10px;
        }
        .weui_media_box{
            padding-bottom:0px !important;
        }
        .hd {
            padding: 5px !important;
        }
    </style>
</head>
<body>
<div class="container">
    @yield('content')
</div>
</body>
</html>