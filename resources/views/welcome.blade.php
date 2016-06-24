<!DOCTYPE html>
<html>
    <head>
        <title>Laravel</title>

        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

        <style>
            html, body {
                height: 100%;
            }

            body {
                margin: 0;
                padding: 0;
                width: 100%;
                display: table;
                font-weight: 100;
                font-family: 'Lato';
            }

            .container {
                text-align: center;
                display: table-cell;
                vertical-align: middle;
            }

            .content {
                text-align: center;
                display: inline-block;
            }

            .title {
                font-size: 42px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="content">
                <h2>{{ $code  }}}</h2>
                <div class="title">欢迎关注农科110微信服务号:)</div>
                <img src="{{ $qrcode->url($result->ticket) }}" alt="">
            </div>
        </div>
    </body>
</html>
