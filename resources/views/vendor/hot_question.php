<div class="weui_panel_bd" ng-app='nkWechat' ng-controller='DemoController'>
    <div infinite-scroll='reddit.nextPage()' infinite-scroll-disabled='reddit.busy'
         infinite-scroll-distance='1'>
        <a href="./question/{{ item.id }}" class="weui_media_box weui_media_appmsg"
           ng-repeat='item in reddit.items'>
            <div class="weui_media_hd">
                <img class="weui_media_appmsg_thumb"
                     src="http://storage.workerhub.cn/image/icon/icon_question.png"
                     alt="">
            </div>
            <div class="weui_media_bd">
                <h4 class="weui_media_title">{{ item.title }}</h4>
                <p class="weui_media_desc">{{ item.user.name }}
                    发布于 {{ item.whenCreated }}</p>
            </div>
        </a>
        <a href="" ng-show='reddit.busy' class="weui_media_box weui_media_appmsg">
            Loading data...
        </a>
    </div>
</div>