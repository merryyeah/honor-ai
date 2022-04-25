<?php
include(APP_PATH . '/views/admin/common/header.html');
?>
<link href="/public/css/login.css" rel="stylesheet">

<body>
<div id="app" v-clock class="login-page">
    <div class="login-top">
        <div class="text"> <span>某某科技有限公司3D可视化管理系统</span></div>
    </div>

    <div class="login-info">
        <div class="info">
            <img class="login-t" src="/public/image/login/登录.png" alt="">
            <div class="input login-user">
                <input type="text" id="userName" v-model="form.userName" placeholder="请输入用户名">
            </div>
            <div class="input login-password">
                <input :type="eyesShow?'text':'password'"   v-model="form.password" placeholder="请输入密码">
                <img id="passwordHide" v-if="!eyesShow" @click="eyesShow =!eyesShow" src="/public/image/login/闭眼.png" alt="">
                <img  id="passwordShow" v-else @click="eyesShow =!eyesShow" src="/public/image/login/睁眼.png" alt="">
            </div>
            <div class="login-submit" @click="login()">立即登录</div>
        </div>
    </div>

</div>
</body>
<?php
include(APP_PATH . '/views/admin/common/footer.html');
?>
<script>
    let appVue = new Vue({
        el: '#app',
        data: function () {
            return {
                eyesShow:false,
                form: {
                    userName:'',
                    password:''
                }
            }
        },
        methods: {
            login(){
                console.log(appVue.form)
                let data = {
                    userName: appVue.user,
                    password: appVue.password
                }
                http.post('/admin/auth/doLogin', appVue.form)
                    .then(function (res) {
                        if(res.result == "fail"){
                            appVue.$message.error(res.reason);
                        }else{
                            window.localStorage.setItem('user',JSON.stringify(appVue.form))
                            appVue.$message.success('登录成功');
                            setTimeout(()=>{
                                window.location.href = "/admin/index/index";
                            },500)
                        }
                    })

            }
        },
        mounted() {
            if( window.localStorage.getItem('user')){
                this.form = JSON.parse(window.localStorage.getItem('user'))
            }
        },
    })
</script>

</html>