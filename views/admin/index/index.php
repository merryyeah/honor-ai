<!DOCTYPE  html>
<html class="html">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <title>某某科技可视化大屏</title>
    <!-- 公共css -->
    <link href="/public/css/common.css" rel="stylesheet">
    <!-- 公共js -->
    <script src="/public/lib/jquery@3.5.1/jquery.min.js"></script>
    <script src="/public/js/utils.js?v=20220420V1"/></script>
</head>
<body>
    <div class="app-web">
        <!-- header -->
        <div class="app-header">
            <span class="app-title">某某科技有限公司3D可视化管理系统</span>
            <div class="app-header-right">
                <div class="right-item" onclick="goPage();">
                    <img src="/public/image/home/back.png"/>
                    <span>返回可视化</span>
                </div>
                <div  class="right-item" onclick="logout();">
                    <img src="/public/image/home/outline.png"/>
                    <span>退出</span></span>
                </div>
            </div>
        </div>
        <div class="app-container">
            <!-- 菜单 -->
            <div class="menu-container">
                <div class="menu-item active" data-href="/admin/config/camera">
                    <img class="icon" src="/public/image/home/摄像.png" alt=""><span>摄像头设置</span>
                </div>
                <div class="menu-item"  data-href="/admin/config/dept">
                    <img class="icon" src="/public/image/home/部门.png" alt=""><span>部门设置</span>
                </div>
                <div class="menu-item"  data-href="/admin/config/user">
                    <img class="icon" src="/public/image/home/人员.png" alt=""><span>人员</span>
                </div>
                <div class="menu-item"  data-href="/admin/config/account">
                    <img class="icon" src="/public/image/home/账号设置.png" alt=""><span>账号设置</span>
                </div>
            </div>
                <!-- 页面 -->
            <div class="app-page">
                <iframe id="appPageIframe" src="/admin/config/camera"></iframe>
            </div>
        </div>
    </div>
</body>

<script>
    $(".menu-item").on('click',function(){
        let index = $(this).index();
        $(`.menu-item`).removeClass('active');
        $(`.menu-item:nth-child(${index+1})`).addClass('active');
        let href = $(`.menu-item:nth-child(${index+1})`).attr("data-href")
        $('#appPageIframe').prop('src',href)
    });

    if(!window.localStorage.getItem('user')){
        window.location.href = "/admin/auth/login";
    }

    function goPage(){
        window.location.href = "/";
    }
    function logout(){
        window.location.href = "/admin/auth/logout";
    }
</script>
</html>
