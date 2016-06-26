@extends('layouts.master')

@section('title','农科110-热门问题')

@section('content')
    <div class="weui_panel weui_panel_access">
        <div class="question">
            <div class="weui_panel_hd">热门问题</div>
            @include('vendor.hot_question')
        </div>
    </div>
    <script type='text/javascript' src="http://storage.workerhub.cn/js/jquery-3.0.0.min.js"></script>
    <script type='text/javascript' src='http://storage.workerhub.cn/js/angularjs/1.5.7/angular.min.js'></script>
    <script type='text/javascript' src='http://storage.workerhub.cn/js/ng-infinite-scroll.js'></script>
    <script>
        angular.module('nkWechat', ['infinite-scroll']);

        var myApp = angular.module('nkWechat', ['infinite-scroll']);

        myApp.controller('DemoController', function ($scope, Reddit) {
            $scope.reddit = new Reddit();
        });

        // Reddit constructor function to encapsulate HTTP and pagination logic
        myApp.factory('Reddit', function ($http) {
            var Reddit = function () {
                this.items = [];
                this.busy = false;
                this.page = 1;
            };

            Reddit.prototype.nextPage = function () {
                if (this.busy) return;
                this.busy = true;

                var url = "http://sxnk110.workerhub.cn:9000/api/v1/questions?pageSize=10&page=" + this.page;

                $http.get(url).success(function (data) {
                    var items = data;
                    for (var i = 0; i < items.length; i++) {
                        this.items.push(items[i]);
                    }
                    this.page++;
                    this.busy = false;
                }.bind(this));

            };

            return Reddit;
        });
    </script>
@endsection