<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>某某科技可视化大屏</title>
    <!-- import CSS -->
    <link rel="stylesheet" href="/public/lib/element-ui/theme/index.css">
    <link href="/public/css/dashboard.css?v=20220421V2" rel="stylesheet">
    <style>
        [v-clock] {
            display: none;detectionLog
        }
    </style>
</head>

<body>
<div id="app" v-clock class="dashboard-page">
    <div class="dashboard-top">
        <div class="text"> <span>某某科技有限公司3D可视化管理系统</span></div>
        <div class="info">
            <div class="top-left">{{time}}</div>
            <div class="top-right">
                <div class="right-item" @click="setPageType">
                    <i class="el-icon-sort" style="transform:rotate(90deg)"></i>
                    <span v-if="pageType == 'dashboard'">切换简洁版</span>
                    <span v-else>切换可视化版</span>
                </div>
                <a class="right-item" @click="showLogin = true">
                    <i class="el-icon-setting"></i>
                    <span>后台配置</span>
                </a>
                <div class="right-item" onclick="logout();">
                    <img src="/public/image/home/outline.png" />
                    <span>退出</span></span>
                </div>
            </div>
        </div>
    </div>
    <div class="dashboard" v-show="pageType == 'dashboard'">
        <div class="dashboard-left">
            <div class="map-tip">
                <span class="icon warning1"></span>
                <span class="text">一般风险</span>
                <span class="icon warning2"></span>
                <span class="text">中级风险</span>
                <span class="icon warning3"></span>
                <span class="text">高级风险</span>
            </div>
            <div class="map-block">
                <div class="map-id" @dblclick="doubleclick">
                    <img src="/public/image/home/map.png" alt="">
                    <div class="index-point" v-for="(c,index) in cameraList" :title="c.camera_name" @click="cameraClick(c)"
                         :style="returnCameraIndex(c)">
                        <!--  <img src="/public/image/gif/index2.gif"> -->
                        <div class="camera-hover">
                            <img src="/public/image/home/camera.png">
                            <img class="hover" src="/public/image/home/camera1.png">
                        </div>
                    </div>
                    <div class="index-point" v-for="(c,index) in ngListCarmeta" @click="cameraClickNgList(c,index)"
                         :style="returnCameraIndex(c.cameraInfo)">
                        <img src="/public/image/gif/index2.gif">
                    </div>
                </div>
            </div>
            <div></div>
        </div>
        <div class="dashboard-right">
            <div class="right-block warning-block">
                <div class="title" @click="blockarrow.warning = !blockarrow.warning">
                    行为预警
                    <span>
              <i class="el-icon-arrow-down" v-show="!blockarrow.warning"></i>
              <i class="el-icon-arrow-up" v-show="blockarrow.warning"></i>
            </span>
                </div>
                <div class="info" v-show="blockarrow.warning">
                    <div class="warning-item flex-center button-green" @click="allAction()">全部</div>

                    <div class="warning-item flex-center" v-for="(item,index) in actionStatistics" @click="action(item)" :key="index">
                        <div>
                            <p class="w-t">{{item.action}}</p>
                            <p class="w-i" :style="{'color': item.cnt>0?'#F71130':'#3BA4BA'}">+{{item.cnt}}</p>
                        </div>
                    </div>

                </div>
            </div>
            <div class="right-block search-block">
                <div class="title" @click="blockarrow.search = !blockarrow.search">
                    摄像头查找
                    <span>
              <i class="el-icon-arrow-down" v-show="!blockarrow.search"></i>
              <i class="el-icon-arrow-up" v-show="blockarrow.search"></i>
            </span>
                </div>
                <div class="info" v-show="blockarrow.search">
                    <div class="search">
                        <input type="text" @input="search" v-model="searchValue" placeholder="请输入员工姓名"></input>
                        <div class="button-green flex-center">搜索</div>
                    </div>
                    <div class="info-tree">
                        <el-tree :data="fullDeptTree" node-key="id" class="user-tree" highlight-current check-strictly ref="tree"
                                 :filter-node-method="filterNode" :props="defaultProps" @node-click="handleNodeClickUser">
                <span class="custom-tree-node" slot-scope="{ node, data }">
                  <span>
                    <span v-if="data.type == 'dept'">{{ node.label }}</span>
                    <i v-if="data.type == 'user'" style="color: #b0d8f4;padding-left: 5px;">{{ node.label }}</i>
                  </span>
                </span>
                        </el-tree>
                    </div>
                </div>
            </div>
            <div class="right-block inspection-block">
                <div class="title" @click="blockarrow.inspection = !blockarrow.inspection">
                    智能巡检
                    <span>
              <i class="el-icon-arrow-down" v-show="!blockarrow.inspection"></i>
              <i class="el-icon-arrow-up" v-show="blockarrow.inspection"></i>
            </span>
                </div>
                <div class="info" v-show="blockarrow.inspection">
                    <div class="inspection-list">
                        <div class="title2">日常巡检</div>
                        <div class="inspection-item" v-for="(index) in 4" :key="index">
                            <div class="t-text">日常巡检{{index}}</div>
                            <div class="button-green" @click="setAutoCheckStatus(index,'open')">运行</div>
                            <div class="button-red" @click="setAutoCheckStatus(index,'stop')">停止</div>
                            <div class="button-blue" @click="getAutoCheck(index)">编辑</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="right-block inspection-block">
                <div class="title" @click="blockarrow.arInspection = !blockarrow.arInspection">
                    AR巡检
                    <span>
                      <i class="el-icon-arrow-down" v-show="!blockarrow.arInspection"></i>
                      <i class="el-icon-arrow-up" v-show="blockarrow.arInspection"></i>
                    </span>
                </div>
                <div class="info" v-show="blockarrow.arInspection">
                    <div class="arInspection-list">
                        AR巡检需要搭配配套硬件设备。请联系相关负责人，谢谢!
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="dashboard concise" v-show="pageType == 'concise'">
        <div class="dashboard-left">
            <div v-for="(item) in 4" class="dashboard-big-image-item">
                <div class="big-image" v-if="screenData[item-1]" @click="openImage(screenData[item-1])"
                     :style="{'backgroundImage':`url(${screenData[item-1].image_url})`}">
                </div>
            </div>

        </div>
        <div class="dashboard-right">
            <div class="concise-tab">
                <div @click="conciseRightType = 1" :class="{'active':conciseRightType== 1}">今日预警</div>
                <div @click="conciseRightType = 2" :class="{'active':conciseRightType == 2}">实时监控</div>
            </div>
            <div class="concise-list">
                <template v-if="conciseRightType == 1">
                    <div class="concise-ngList">
                        <div class="ngList-item" v-for="(n,index) in warningList" :key="index">
                            <div class="ngList-item-image" :style="{'backgroundImage':`url(${n.image_url})`}"></div>
                            <div class="ngList-item-desc">
                                <p>序号 {{n.detection_log_id}} </p>
                                <p>时间 {{n.gmt_create}}</p>
                            </div>
                            <div class="button-green flex-center"  @click="openImage(n)">
                                查看图片
                            </div>
                        </div>

                    </div>
                    <el-pagination style="text-align: right;" layout="prev, pager, next" :page-size="20"
                                   @current-change="handleCurrentChange" :current-page.sync="detectionLogForm.page" :total="detectionLogTatal">
                    </el-pagination>
                </template>

                <template v-if="conciseRightType == 2">
                    <div class="concise-cameraList">
                        <div class="cameraList-item cameraList-item-title">
                            <div class="cameraList-item-cell">序号</div>
                            <div class="cameraList-item-cell">部门</div>
                            <div class="cameraList-item-cell">操作</div>
                        </div>
                        <div class="cameraList-scroll">
                            <div class="cameraList-item" v-for="(c,index) in conciseCameraList" :key="index">
                                <div class="cameraList-item-cell">{{c.camera_id}}</div>
                                <div class="cameraList-item-cell">{{c.deptName}}</div>
                                <div class="cameraList-item-cell">
                                    <div class="button-green flex-center" @click="cameraClick(c)">
                                        播放
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <el-pagination style="text-align: right;" layout="prev, pager, next" :page-size="20"
                                   @current-change="conciseCurrentChange" :current-page.sync="conciseCamera.page"
                                   :total="conciseCameraTatal">
                    </el-pagination>
                </template>
            </div>

        </div>

    </div>



    <el-dialog title="行为预警" class="app-dialog" :visible.sync="warningDialogVisible" width="1200px"
               @opened="warningDialogVisibleOpened">
        <el-form :inline="true" :model="detectionLogForm" size="mini" class="warning-form-inline">
            <el-form-item label="开始时间">
                <el-date-picker v-model="detectionLogForm.startTime" type="datetime" value-format="yyyy-MM-dd HH:mm:ss"
                                style="width:175px" placeholder="选择日期时间">
                </el-date-picker>

            </el-form-item>
            <el-form-item label="结束时间">
                <el-date-picker value-format="yyyy-MM-dd HH:mm:ss" style="width:175px" v-model="detectionLogForm.endTime"
                                type="datetime" placeholder="选择日期时间">
                </el-date-picker>
            </el-form-item>
            <el-form-item label="负责人">
                <el-select style="width:100px" filterable clearable v-model="detectionLogForm.userId" placeholder="请选择">
                    <el-option :label="u.name" :value="u.user_id" v-for="(u,index) in  userList" ::key="index"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="地点">
                <el-select style="width:100px" filterable clearable v-model="detectionLogForm.addr" placeholder="请选择">
                    <el-option :label="u" :value="index" v-for="(u,index) in cameraNames" ::key="index"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="行为">
                <el-select style="width:100px" filterable clearable v-model="detectionLogForm.action" placeholder="请选择">
                    <el-option :label="u" :value="index" v-for="(u,index) in actions" ::key="index"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="openWarning">搜索</el-button>
                <el-button type="primary" @click="exportDetectionLog">导出</el-button>
            </el-form-item>
        </el-form>
        <el-table :data="warningList" class="app-table" height="400" size="small" border>
            <el-table-column label="序号" width="100" prop="detection_log_id"></el-table-column>
            <el-table-column label="状态" prop="status">
                <template slot-scope="scope">
                <span>
                  <span  v-if="scope.row.is_read == '1'" >已读</span>
                  <span style="color:red;" v-if="scope.row.is_read == '0'">未读</span>
                </span>
                </template>

            </el-table-column>
            <el-table-column label="行为" prop="action"></el-table-column>
            <el-table-column label="创建时间" prop="gmt_create" width="200"></el-table-column>
            <el-table-column label="地点" prop="addr"></el-table-column>
            <el-table-column label="图片" prop="image_url" show-overflow-tooltip>
                <template slot-scope="scope">
            <span style="color: #3D7087;cursor: pointer;"
                  @click="openTableItem('image',scope.row)">{{scope.row.image_url}}</span>
                </template>
            </el-table-column>
            <el-table-column label="视频" prop="video_url" show-overflow-tooltip>
                <template slot-scope="scope">
            <span style="color: #3D7087;cursor: pointer;"
                  @click="openTableItem('video',scope.row)" >{{scope.row.video_url}}</span>
                </template>
            </el-table-column>
            <el-table-column label="负责人" prop=""></el-table-column>
            <el-table-column label="等级" prop=""></el-table-column>
        </el-table>
        <el-pagination style="text-align: right;" layout="prev, pager, next" :page-size="20"
                       @current-change="handleCurrentChange" :current-page.sync="detectionLogForm.page" :total="detectionLogTatal">
        </el-pagination>
    </el-dialog>


    <el-dialog :title="`编辑日常巡检${inspectionIndex}`" class="app-dialog" :visible.sync="checkInfoDialogVisible"
               width="672px">

        <el-form :model="autoCheckInfo" size="mini" label-width="120px" class="warning-form-inline">
            <el-form-item label="巡检时间">
                <el-time-picker

                        format="HH:mm"
                        value-format="HH:mm"
                        v-model="autoCheckInfo.begin"
                        placeholder="开始时间">
                </el-time-picker>
                -
                <el-time-picker

                        format="HH:mm"
                        value-format="HH:mm"
                        v-model="autoCheckInfo.end"
                        placeholder="结束时间">
                </el-time-picker>

            </el-form-item>
            <el-form-item label="播放时间">
                <el-select filterable clearable v-model="autoCheckInfo.times" placeholder="请选择">
                    <el-option label="1分钟" value="1"></el-option>
                    <el-option label="5分" value="5"></el-option>
                    <el-option label="10分钟" value="10"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="播放时间间隔">
                <el-select filterable clearable v-model="autoCheckInfo.interval_times" placeholder="请选择">
                    <el-option label="1分钟" value="1"></el-option>
                    <el-option label="2分" value="2"></el-option>
                    <el-option label="3分" value="3"></el-option>
                    <el-option label="3分" value="4"></el-option>
                    <el-option label="5分" value="5"></el-option>
                    <el-option label="10分钟" value="10"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="添加摄像头">
                <el-select filterable clearable multiple v-model="autoCheckInfo.camera_list" placeholder="请选择">
                    <el-option :label="c.camera_name" :value="c.camera_id" :key="index" v-for="(c,index) in cameraList">
                    </el-option>
                </el-select>
            </el-form-item>

            <el-form-item style="text-align: right;">
                <!-- <el-button >删除</el-button> -->
                <el-button type="primary" @click="setAutoCheck">保存</el-button>
            </el-form-item>
        </el-form>

    </el-dialog>


    <el-dialog title="视频播放" class="app-dialog" :visible.sync="videoDialogVisible" width="672px">
        <div class="video-play">
            <video style="width: 100%;" id="videoPlay" :src="videoUrl" controls></video>
            <div class="desc" v-if="videoDesc">{{videoDesc}}</div>
        </div>
    </el-dialog>
    <el-dialog title="查看大图" class="app-dialog" :visible.sync="imageDialogVisible" width="800px">
        <div class="big-image">
            <div class="image-center">
                <img style="height: 100%;" :src="imageUrl"></img>
                <div class="desc" v-if="imageDesc">{{imageDesc}}</div>
            </div>
        </div>
    </el-dialog>

    <div class="login-dialog" v-if="showLogin">
        <div class="login-mask" @click="showLogin = false"></div>
        <div class="login-info">
            <div class="info">
                <img class="login-t" src="/public/image/login/登录.png" alt="">
                <div class="input login-user">
                    <input type="text" id="userName" v-model="loginForm.userName" placeholder="请输入用户名">
                </div>
                <div class="input login-password">
                    <input :type="eyesShow?'text':'password'"   v-model="loginForm.password" placeholder="请输入密码">
                    <img id="passwordHide" v-if="!eyesShow" @click="eyesShow =!eyesShow" src="/public/image/login/闭眼.png" alt="">
                    <img  id="passwordShow" v-else @click="eyesShow =!eyesShow" src="/public/image/login/睁眼.png" alt="">
                </div>
                <div class="login-submit" @click="login()">立即登录</div>
            </div>
        </div>
    </div>
</div>
</body>
<!-- import Vue before Element -->
<script src="/public/lib/vue/vue.min.js"></script>
<script src="/public/lib/vue/axios.min.js"></script>
<!-- import JavaScript -->
<script src="/public/lib/element-ui/lib/index.js"></script>
<script src="/public/lib/jquery@3.5.1/jquery.min.js"></script>
<script src="/public/js/ImageTools.js"></script>
<script src="/public/js/utils.js?v=20220420V1"></script>
<script>
    let appVue = new Vue({
        el: '#app',
        data: function () {
            return {
                pageType: 'dashboard',//concise dashboard
                serverTime:parseInt( +new Date()/1000),
                time: '',
                blockarrow: {
                    warning: true,
                    search: true,
                    inspection: true,
                    arInspection : false,
                },
                filterTimer: '',
                searchValue: '',
                actionStatistics: [],
                fullDeptTree: [],
                userList: [],
                cameraList: [],
                cameraNames : [],
                actions : [],
                defaultProps: {
                    children: 'children',
                    label: 'label'
                },
                imageUrl: '',
                imageDesc:'',
                imageDialogVisible: false,
                videoUrl: '',
                videoDesc:'',
                videoDialogVisible: false,
                warningDialogVisible: false,
                checkInfoDialogVisible: false,
                detectionLogTatal: 0,
                detectionLogForm: {
                    page: 1,
                    pageSize: 20,
                    startTime: '',
                    endTime: '',
                    userId: '',
                    addr: '',
                    action: '',
                },
                warningList: [],
                inspectionIndex: 1,
                autoCheckInfo: {
                },
                ngList: [],
                ngListCarmeta: [],
                screenData: [],
                conciseRightType: '1',
                conciseCameraTatal: 0,
                conciseCamera: {
                    page: 1,
                    pageSize: 20,
                },
                conciseCameraList: [],
                isBig:false,
                showLogin:false,
                eyesShow:false,
                loginForm: {
                    userName:'',
                    password:''
                }
            }
        },
        methods: {
            setPageType() {
                if (this.pageType == 'dashboard') {
                    this.pageType = 'concise';
                    this.detectionLogForm.page = 1
                    this.openWarning()
                } else {
                    this.pageType = 'dashboard';
                }


            },
            getConfig() {
                let self = this;
                self.getDetection()
                http.post('/front/data/config', {
                }).then(function (res) {
                    if (res.result == "fail") {
                        self.$message.error(res.reason);
                    } else {
                        self.getTime(res.serviceTime)
                        setInterval(() => {
                            self.getDetection()
                        }, res.detectionIntervalTime * 1000 || 10000)
                    }
                })
            },
            getActionStatistics() {
                let self = this;
                http.post('/front/data/getActionStatistics', {
                }).then(function (res) {
                    if (res.result == "fail") {
                        self.$message.error(res.reason);
                    } else {
                        self.actionStatistics = res.data
                    }
                })
            },
            getTime(serverTime) {
                setInterval(() => {
                    this.serverTime ++
                    this.time = appGetTime(this.serverTime)
                    let currentHH = new Date(this.serverTime * 1000).getHours()
                    let currentmm = new Date(this.serverTime * 1000).getMinutes()
                    let currentss = new Date(this.serverTime * 1000).getSeconds()
                    if(currentHH == '00' && currentmm == '00'&& currentss == '00'){
                        window.location.reload()
                    }
                }, 1000)
            },
            logout() {
                window.location.href = "/admin/auth/logout";
            },
            doubleclick(event){
                this.isBig = !this.isBig;
                if(!this.isBig){
                    this.setMap()
                    return
                }
                let self = this
                let offsetX = event.offsetX
                let offsetY = event.offsetY
                let mapBlock = document.querySelector('.map-block')
                let mapId = document.querySelector('.map-id')
                let mapBlockHeight = mapBlock.clientHeight
                let mapBlockWidth = mapBlock.clientWidth
                let mapIdHeight = mapId.clientHeight
                let mapIdWidth = mapId.clientWidth
                let left = - event.offsetX + mapBlockWidth/2
                let top = - event.offsetY + mapBlockHeight/2
                setTimeout(() => {
                    if(left>0)  left = 0
                    if(left< mapBlockWidth-mapIdWidth)  left =  mapBlockWidth-mapIdWidth
                    if(top>0)  top = 0
                    if(top< mapBlockHeight-mapIdHeight)  top = mapBlockHeight-mapIdHeight
                    mapId.style.transform = `scale(${1})`
                    mapId.style.left = `${left}px`
                    mapId.style.top = `${top}px`
                },100)
                mapId.oncontextmenu = function (event) {
                    return false;
                }

                mapId.addEventListener("mousedown", function(event){
                    event.stopPropagation();
                    var code = event.which;
                    if(code != 3  || !self.isBig){
                        return
                    }
                    let left = parseInt(document.querySelector('.map-id').style.left)
                    let top = parseInt(document.querySelector('.map-id').style.top)
                    let ol = event.clientX
                    let ot = event.clientY
                    document.onmousemove = function(ev){
                        let l = left + ev.clientX - ol
                        let t =  top + ev.clientY - ot
                        if(l>0)  l = 0
                        if(l< mapBlockWidth-mapIdWidth)  l = mapBlockWidth-mapIdWidth
                        if(t>0)  t = 0
                        if(t< mapBlockHeight-mapIdHeight)  t =mapBlockHeight-mapIdHeight
                        mapId.style.left =  l + "px";
                        mapId.style.top = t + "px";
                    }
                    document.onmouseup = function(){
                        document.onmousemove = null;
                    }

                })
            },
            setMap() {
                let mapBlock = document.querySelector('.map-block')
                let mapId = document.querySelector('.map-id')
                let mapBlockHeight = mapBlock.clientHeight
                let mapBlockWidth = mapBlock.clientWidth
                let mapIdHeight = mapId.clientHeight
                let mapIdWidth = mapId.clientWidth
                let zoomVal = mapBlockHeight / mapIdHeight;
                let left = (mapBlockWidth - mapIdWidth) / 2
                let top = (mapBlockHeight - mapIdHeight) / 2
                mapId.style.transform = `scale(${zoomVal})`
                mapId.style.left = `${left}px`
                mapId.style.top = `${top}px`
                mapId.oncontextmenu = function (event) {
                    return false;
                }
            },
            getDetection() {
                let self = this
                http.post('/front/data/detection', {
                    page: 1,
                    isNg: 1,
                    pageSize: 20,
                }).then(function (res) {
                    if (res.result == "fail") {
                        self.$message.error(res.reason);
                    } else {
                        self.ngList = res.ngList
                        self.ngListCarmeta = res.ngList
                        self.screenData = res.screenData
                    }
                })
            },
            getFullDeptTree() {
                let self = this
                http.post('/front/data/getFullDeptTree', {
                }).then(function (res) {
                    if (res.result == "fail") {
                        self.$message.error(res.reason);
                    } else {
                        self.fullDeptTree = res.data
                    }
                })
            },
            search() {
                clearTimeout(this.filterTimer);
                this.filterTimer = setTimeout(() => {
                    this.$refs.tree.filter(this.searchValue);
                }, 200);
            },
            //过滤
            filterNode(value, data) {
                if (!value) return true;
                return data.label.indexOf(value) !== -1;
            },
            handleNodeClickUser(data) {
                if (data.type == 'user') {

                    let self = this;
                    http.post('/front/data/getUserVideoUrl', {
                        id: data.id
                    }).then(function (res) {
                        if (res.result == "fail") {
                            self.$message.error(res.reason);
                        } else {
                            self.openVideo(res);
                        }
                    })
                }
            },
            openVideo(data) {
                // this.videoDialogVisible = true;
                // this.videoUrl = data.video_url;
                // this.videoDesc = data.addr?`${data.gmt_create} ${data.addr} ${data.action}`:'';
                // setTimeout(() => {
                //     document.querySelector('#videoPlay').play();
                // },500)
                if (document.getElementById("qt_device") != null) {
                    cpp.startPlay(JSON.stringify(data))
                } else {
                    appVue.$message.error('请使用app查看');
                }
            },
            openImage(data) {
                this.imageDialogVisible = true;
                this.imageUrl = data.image_url;
                this.imageDesc = data.addr?`${data.gmt_create} ${data.addr} ${data.action}`:'';
            },
            videoDialogVisibleOpened() {

            },
            warningDialogVisibleOpened() {

            },
            action(item){
                this.initDetectionLogForm()
                this.detectionLogForm.action = item.action
                this.openWarning()
            },
            allAction(){
                this.initDetectionLogForm()
                this.openWarning()
            },
            initDetectionLogForm() {
                this.detectionLogForm = {
                    page: 1,
                    pageSize: 20,
                    startTime: '',
                    endTime: '',
                    userId: '',
                    addr: '',
                    action: '',
                }
            },
            openWarning() {
                let self = this;
                if(self.pageType == 'dashboard') {
                    self.warningDialogVisible = true;
                }

                http.post('/front/data/detectionLog', this.detectionLogForm).then(function (res) {
                    if (res.result == "fail") {
                        self.$message.error(res.reason);
                    } else {
                        self.warningList = res.list;
                        self.detectionLogTatal = Number(res.total);
                    }
                })
            },
            exportDetectionLog(){
                let self = this;
                http.post('/front/data/exportDetectionLog', this.detectionLogForm).then(function (res) {
                    if (res.result == "fail") {
                        self.$message.error(res.reason);
                    } else {
                        const link = document.createElement('a');
                        link.href = window.baseURL + res.url;
                        link.setAttribute('download', res.url.split('/').pop());
                        document.body.appendChild(link);
                        link.click();
                    }
                })
            },
            handleCurrentChange(page) {
                this.detectionLogForm.page = page
                this.openWarning()
            },
            indexMethod(index) {
                return ((this.detectionLogForm.page-1)*this.detectionLogForm.pageSize) + index+1
            },
            getUserList() {
                let self = this;
                http.post('/front/data/getUserList', {}).then(function (res) {
                    if (res.result == "fail") {
                        self.$message.error(res.reason);
                    } else {
                        self.userList = res.list
                    }
                })

            },
            getCameraNames() {
                let self = this;
                http.post('/front/data/getCameraNames', {}).then(function (res) {
                    if (res.result == "fail") {
                        self.$message.error(res.reason);
                    } else {
                        self.cameraNames = res.cameraNames
                    }
                })
            },
            getActions() {
                let self = this;
                http.post('/front/data/getActions', {}).then(function (res) {
                    if (res.result == "fail") {
                        self.$message.error(res.reason);
                    } else {
                        self.actions = res.actions
                    }
                })
            },
            getCameraList() {
                /* 实时监控 */
                let self = this;
                http.post('/front/data/cameraList', self.conciseCamera).then(function (res) {
                    if (res.result == "fail") {
                        self.$message.error(res.reason);
                    } else {
                        self.conciseCameraList = res.list
                    }
                })

            },
            getAllCameraList() {
                let self = this;
                http.post('/front/data/getAllCameraList', {}).then(function (res) {
                    if (res.result == "fail") {
                        self.$message.error(res.reason);
                    } else {
                        self.cameraList = res.list
                    }
                })

            },
            returnCameraIndex(data) {
                if(!data){
                    return
                }
                let coordinate = data.coordinate || ''
                let left = coordinate.split(',')[0]
                let top = coordinate.split(',')[1]
                return { top: `${top}px`, left: `${left}px` }

            },
            editCheck() {
                this.checkInfoDialogVisible = true
                this.autoCheckInfo = {
                    time: [],
                    times: '',
                    camera_list: [],
                    status: '',
                }
            },
            setAutoCheck() {
                /* 获取巡检详情 */
                let params = this.autoCheckInfo
                delete params.time
                params.id = this.inspectionIndex
                console.log(params)
                let self = this;
                http.post('/front/data/setAutoCheck', params).then(function (res) {
                    if (res.result == "fail") {
                        self.$message.error(res.reason);
                    } else {
                        self.checkInfoDialogVisible = false;
                        self.$message.success('编辑成功');
                        self.getAllAutoCheckList()
                    }
                })
            },
            getAutoCheck(id) {
                this.inspectionIndex = id
                this.checkInfoDialogVisible = true;
                /* 获取巡检详情 */
                let self = this;
                http.post('/front/data/getAutoCheck', { id: id }).then(function (res) {
                    if (res.result == "fail") {
                        self.$message.error(res.reason);
                    } else {
                        if (res.autoCheckInfo) {
                            self.autoCheckInfo = res.autoCheckInfo
                            self.autoCheckInfo.camera_list = res.autoCheckInfo.camera_list.split(',')
                        } else {
                            self.autoCheckInfo = {
                                times: '',
                                camera_list: [],
                                status: '',
                            }
                        }
                    }
                })
            },
            setAutoCheckStatus(id, status) {
                let self = this;
                http.post('/front/data/setAutoCheckStatus', { id: id, status: status }).then(function (res) {
                    if (res.result == "fail") {
                        self.$message.error(res.reason);
                    } else {
                        self.getAllAutoCheckList()
                        if(status == 'open'){
                            let autoCheckInfo = res.autoCheckInfo
                            autoCheckInfo.camera_list = autoCheckInfo.camera_list.split(',')
                            self.setAutoCheckALert(autoCheckInfo)
                        }else{
                            self.$message.success('停止成功');
                        }
                    }
                })
            },
            conciseCurrentChange(page) {
                this.conciseCamera.page = page;
                this.getCameraList()
            },
            /* 获取单个摄像头信息 */
            cameraClick(data) {
                let self = this;
                http.post('/front/data/getCameraVideoUrl', { id: data.camera_id }).then(function (res) {
                    if (res.result == "fail") {
                        self.$message.error(res.reason);
                    } else {
                        self.openVideo(res)
                    }
                })

            },
            cameraClickNgList(data,index){
                this.cameraClick(data)
                this.ngListCarmeta.splice(index,1)
            },
            getAllAutoCheckList(){
                let self = this
                http.post('/front/data/getAutoCheckList', {}).then(function (res) {
                    if (res.result == "fail") {
                        self.$message.error(res.reason);
                    } else {
                        self.setAutoCheckData(res.autoCheckList)
                    }
                })
            },
            setAutoCheckData(list){
                let self = this
                let serverTime=  new Date(this.serverTime * 1000)
                let currentHH = serverTime.getHours()
                let currentmm = serverTime.getMinutes()
                let currentYYMMDD = `${serverTime.getFullYear()}/${serverTime.getMonth()+1}/${serverTime.getDate()}`
                list.forEach(v=>{
                    let begin = parseInt(+new Date(`${currentYYMMDD} ${v.begin}:00`)/1000)
                    let end = parseInt(+new Date(`${currentYYMMDD} ${v.end}:00`)/1000)
                    let timeList = []
                    if(self.serverTime >= begin && self.serverTime < end){
                        appGetTimeList(self.serverTime,end,v.interval_times * 60).forEach(time=>{
                            timeList.push({
                                time:time, //时间戳
                                times:v.times,
                                camera_list:v.camera_list.split(','),
                            })
                        })
                    }
                    if(self.serverTime < begin){
                        appGetTimeList(begin,self.serverTime,v.interval_times * 60).forEach(time=>{
                            timeList.push({
                                time:time, //时间戳
                                times:v.times,
                                camera_list:v.camera_list.split(','),
                            })
                        })
                    }
                    self.autoCheckTimeList = timeList
                    console.log(timeList)

                })
            },
            setAutoCheckALert(find){
                let closeTime = 0
                find.camera_list.forEach((v,i)=>{
                    closeTime = find.times * 60 * 1000 * i + find.times * 1000
                    setTimeout(()=>{
                        this.cameraClick({camera_id:v})
                    },find.times *  60 * 1000 * i)
                })
                console.log(closeTime)
                setTimeout(() => {
                    this.videoDialogVisible = false;
                    if (document.getElementById("qt_device") != null) {
                        cpp.closeDialog()
                    } else {
                        appVue.$message.error('请使用app查看');
                    }
                },closeTime)
            },
            login(){
                http.post('/admin/auth/doLogin', appVue.loginForm)
                    .then(function (res) {
                        if(res.result == "fail"){
                            appVue.$message.error(res.reason);
                        }else{
                            window.localStorage.setItem('user',JSON.stringify(appVue.loginForm))
                            appVue.$message.success('登录成功');
                            setTimeout(()=>{
                                window.location.href = "/admin/index/index";
                            },500)
                        }
                    })

            },
            openTableItem(type,data){
                if(type == 'image'){
                    this.openImage(data)
                }else{
                    this.openVideo(data)
                }
                let self = this
                if(data.is_read == '0'){
                    http.post('/front/data/readDetectionLog', {
                        id:data.detection_log_id
                    }).then(function (res) {
                        if (res.result == "fail") {
                            self.$message.error(res.reason);
                        } else {
                            self.$set(data,'is_read' ,'1')
                        }
                    })
                }

            }
        },
        watch:{
            serverTime(val){
                let find = this.autoCheckTimeList.find(v=>v.time == val)
                if(find && this.pageType == 'concise'){
                    this.setAutoCheckALert(find)
                }
            }
        },
        mounted() {
            this.getConfig()
            this.getAllCameraList()
            this.getUserList()
            this.getCameraNames()
            this.getActions()
            this.getActionStatistics()
            this.getFullDeptTree()
            this.getAllAutoCheckList()
            this.getCameraList()
            this.$nextTick(() => {
                this.setMap()
            })
        },
    })
</script>

</html>