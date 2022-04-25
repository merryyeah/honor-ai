<?php
include(APP_PATH . '/views/admin/common/header.html');
?>
<link href="/public/css/account.css" rel="stylesheet">

<body>
<div id="app" v-clock class="account-page">
    <div class="page-top">
        <h4>账号配置</h4>
    </div>
    <div class="page-table">
        <el-form ref="form" class="account-form" :model="form" label-width="80px">
            <el-form-item label="用户名">
                <el-input type="text"  v-model="form.userName"></el-input>
            </el-form-item>
            <el-form-item label="密码">
                <div  class="password-input" style="width:300px;display:inline-block;">
                    <el-input :type="eyesShow?'text':'password'" disabled="disabled" v-model="form.password"></el-input>
                    <img class="eyes" v-if="eyesShow" @click="eyesShow=!eyesShow" src="/public/image/login/睁眼.png" alt="">
                    <img class="eyes" v-else @click="eyesShow=!eyesShow" src="/public/image/login/闭眼.png" alt="">
                </div>
                <el-button type="text" @click="openChangePassword" style="margin-left: 20px;">修改密码</el-button>
            </el-form-item>
        </el-form>
    </div>

    <el-dialog title="修改秘密" :visible.sync="dialogVisible" width="400px">
        <el-form ref="passw" :model="passw" label-width="80px">
            <el-form-item label="新密码">
                <div  class="password-input">
                    <el-input :type="eyesShow1?'text':'password'"  v-model="passw.newPwd"></el-input>
                    <img class="eyes" v-if="eyesShow1" @click="eyesShow1=!eyesShow1" src="/public/image/login/睁眼.png" alt="">
                    <img class="eyes" v-else @click="eyesShow1=!eyesShow1" src="/public/image/login/闭眼.png" alt="">
                </div>
            </el-form-item>
            <el-form-item label="确认密码">
                <div  class="password-input">
                    <el-input :type="eyesShow2?'text':'password'"  v-model="passw.newConfirmPwd"></el-input>
                    <img class="eyes" v-if="eyesShow2" @click="eyesShow2=!eyesShow2" src="/public/image/login/睁眼.png" alt="">
                    <img class="eyes" v-else @click="eyesShow2=!eyesShow2" src="/public/image/login/闭眼.png" alt="">
                </div>

            </el-form-item>

        </el-form>
        <span slot="footer" class="dialog-footer">
        <el-button @click="dialogVisible = false">取 消</el-button>
        <el-button type="primary" @click="confirm">确 认</el-button>
      </span>
    </el-dialog>

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
                dialogVisible: false,
                eyesShow:false,
                eyesShow1:false,
                eyesShow2:false,
                form: {
                    userName:'',
                    password:''
                },
                passw: {
                    newPwd:'',
                    newConfirmPwd:'',
                },

            }
        },
        methods: {
            openChangePassword(){
                this.dialogVisible = true
            },
            confirm() {
                if(!(this.passw.newPwd && this.passw.newPwd == this.passw.newConfirmPwd)){
                    this.$message.error('两次密码不一致');
                    return ;
                }
                http.post('/admin/auth/updatePwd', appVue.passw)
                    .then(function (res) {
                        if(res.result == "fail"){
                            appVue.$message.error(res.reason);
                        }else{
                            appVue.form.password = appVue.passw.newPwd
                            window.localStorage.setItem('user',JSON.stringify({
                                userName:appVue.form.userName,
                                password:appVue.passw.newPwd,
                            }))
                            appVue.$message.success('修改成功');
                            appVue.dialogVisible = false;
                        }

                    })
                    .catch(function (error) {
                        console.log(error);
                    })
            },
        },
        mounted() {
            if( window.localStorage.getItem('user')){
                this.form = JSON.parse(window.localStorage.getItem('user'))
            }
        },
    })
</script>

</html>