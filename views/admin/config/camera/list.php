<?php
include(APP_PATH . '/views/admin/common/header.html');
?>
<link href="/public/css/camera.css?v=20220420V2" rel="stylesheet">
<body>
<div id="app" v-clock class="camera-page">
    <div class="page-top">
        <h4>摄像头配置</h4>
        <el-button type="primary" size="small" @click="addItem">新增摄像头</el-button>
    </div>
    <div class="page-table">
        <el-table ref="table" v-if="height" :data="list" :height="height">
            <el-table-column label="相机编号" prop="camera_id"></el-table-column>
            <el-table-column label="相机名称" prop="camera_name"></el-table-column>
            <el-table-column label="部门" prop="deptName"></el-table-column>
            <el-table-column label="ip" prop="ip"></el-table-column>
            <el-table-column label="用户名" prop="user_name"></el-table-column>
            <el-table-column label="密码" prop="password">
                <template slot-scope="scope">
                    <div v-if="scope.row.passwordShow" style="display:flex;align-items:center;">{{scope.row.password}}
                        <img style="width:20px;display: inline-block;cursor: pointer;" @click="passwordShow(scope.row,false)" src="/public/image/login/睁眼.png" alt="">
                    </div>
                    <div v-else style="display:flex;align-items:center;">**********
                        <img style="width:20px;display: inline-block;cursor: pointer;" @click="passwordShow(scope.row,true)"  src="/public/image/login/闭眼.png" alt="">
                    </div>
                </template>
            </el-table-column>
            <el-table-column label="坐标" prop="coordinate"></el-table-column>
            <el-table-column label="负责人" prop="userName"></el-table-column>
            <el-table-column label="操作">
                <template slot-scope="scope">
                    <el-button type="text" size="small" @click="edit(scope.row)">编辑</el-button>
                    </el-button>
                    <el-button type="text" size="small" @click="remove(scope.row)">删除</el-button>
                    </el-button>
                </template>
            </el-table-column>
        </el-table>
    </div>
    <div class="page-pagination">
        <el-pagination @size-change="handleSizeChange" @current-change="handleCurrentChange" :current-page="currentPage"
                       :page-sizes="[20, 50, 80, 100]" :page-size="pageSize" layout="total, sizes, prev, pager, next, jumper"
                       :total="total">
        </el-pagination>
    </div>

    <el-dialog :title="isEdit?'编辑摄像头':'新增摄像头'" :visible.sync="dialogVisible_add" width="400px">
        <el-form ref="form" :model="form" label-width="80px">
            <el-form-item label="相机编号" v-if="isEdit">
                <el-input type="number" disabled placeholder="请输入" v-model="form.camera_id"></el-input>
            </el-form-item>
            <el-form-item label="相机名字">
                <el-input type="text" placeholder="请输入" v-model="form.camera_name"></el-input>
            </el-form-item>
            <el-form-item label="部门">
                <el-cascader style="width:100%" :props="props" v-model="form.dept_id" :options="deptOptions" clearable>
                </el-cascader>
            </el-form-item>
            <el-form-item label="ip">
                <el-input type="text" placeholder="请输入" v-model="form.ip"></el-input>
            </el-form-item>
            <el-form-item label="用户名">
                <el-input type="text" placeholder="请输入" v-model="form.user_name"></el-input>
            </el-form-item>
            <el-form-item label="密码">
                <el-input type="text" placeholder="请输入" v-model="form.password"></el-input>
            </el-form-item>
            <el-form-item label="地图坐标">
                <el-input type="text" placeholder="请点击设置" disabled @click.native="openMap(form.coordinate)"
                          v-model="form.coordinate"></el-input>
            </el-form-item>
            <el-form-item label="负责人">
                <el-select style="width:100%" v-model="form.user_id" clearable filterable placeholder="请选择">
                    <el-option :label="u.name" :value="String(u.user_id)" v-for="u in userList"></el-option>
                </el-select>
            </el-form-item>
        </el-form>
        <span slot="footer" class="dialog-footer">
        <el-button @click="dialogVisible_add = false">取 消</el-button>
        <el-button type="primary" @click="confirmAdd">确 定</el-button>
      </span>
    </el-dialog>
    <!-- 设置地图 -->
    <el-dialog title="提示" :destroy-on-close="true" :visible.sync="dialogVisible_map" width="800">
        <div class="setMapBlock-body">
            <div id="rightClickDiv">
                <div class="addProductSpan liHover">选择位置</div>
            </div>
            <div class="setMapBlock" @dblclick="doubleclick"  @click="mapClick">
                <img src="/public/image/home/map.png" />
                <div class="index-point" style="top:1px;left:1px">
                    <img src="/public/image/home/camera.png">
                </div>
            </div>
        </div>
        <span slot="footer" class="dialog-footer">
        <el-button @click="dialogVisible_map = false">取 消</el-button>
        <el-button type="primary" @click="setIndex()">确 定</el-button>
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
                dialogVisible_add: false,
                dialogVisible_map: false,
                height: 0,
                total: 0,
                pageSize: 20,
                currentPage: 1,
                isEdit: false,
                form: {
                    dep: []
                },
                props:{
                    label: 'label',
                    value: 'id',
                    children: 'children',
                    checkStrictly:true,
                    emitPath:false,
                },
                deptOptions: [],
                list: [],
                userList: []
            }
        },
        methods: {
            getList(page) {
                let self = this
                http.post('/admin/config/cameraList', {
                    page: page || 0,
                    pageSize: self.pageSize,
                }).then(function (res) {
                    if (res.result == "fail") {
                        self.$message.error(res.reason);
                    } else {
                        self.list = res.list
                        self.total = Number(res.total)
                    }
                })
            },
            getUser() {
                let self = this
                http.post('/admin/config/userList', {
                    page: 1,
                    pageSize: 10000,
                }).then(function (res) {
                    if (res.result == "fail") {
                        self.$message.error(res.reason);
                    } else {
                        self.userList = res.list
                    }
                })

                this.total = 100
            },
            getDeptInfo() {
                let self = this
                http.post('/admin/config/getDeptTree', {}).then(function (res) {
                    if (res.result == "fail") {
                        self.$message.error(res.reason);
                    } else {
                        self.deptOptions = res.deptTree
                    }
                })
            },
            handleSizeChange(val) {
                this.pageSize = val;
                this.getList(this.currentPage)
            },
            handleCurrentChange(val) {
                this.getList(val)
            },
            confirmAdd() {
                let self = this
                //保存数据
                http.post('/admin/config/cameraSave', {
                    id: self.isEdit ? self.form.camera_id : 0,
                    deptId: self.form.dept_id,
                    userId: self.form.user_id,
                    cameraName: self.form.camera_name,
                    ip: self.form.ip,
                    coordinate: self.form.coordinate,
                    userName: self.form.user_name,
                    password: self.form.password
                }).then(function (res) {
                    if (res.result == "fail") {
                        self.$message.error(res.reason);
                    } else {
                        self.dialogVisible_add = false
                        //添加完成后重新获取数据
                        self.getList()
                    }
                })

            },
            addItem(data) {
                this.isEdit = false;
                this.form = {

                }
                this.dialogVisible_add = true;
            },
            edit(data) {
                this.isEdit = true;
                this.form = {
                    ...data,
                }
                this.dialogVisible_add = true;
            },
            remove(data) {
                let self = this
                this.$confirm(`<span>确定要删除 <i style="color:#58B2C5;">${data.camera_name}</i> 吗</span>`, '提 示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    dangerouslyUseHTMLString: true,
                    type: 'warning',
                    reverseBtnOrder: true
                }).then(() => {
                    http.post('/admin/config/delCamera', {
                        id: data.camera_id,
                    }).then(function (res) {
                        if (res.result == "fail") {
                            self.$message.error(res.reason);
                        } else {
                            self.getList()
                            self.$message({
                                type: 'success',
                                message: '删除成功!'
                            });
                        }
                    })
                })
            },
            setIndex() {
                console.log(this.tempIndex)
                this.form.coordinate = this.tempIndex
                this.dialogVisible_map = false;
            },
            openMap() {
                this.dialogVisible_map = true;
                setTimeout(() => {
                    this.setMap()

                },100)
                // let self = this
                // setTimeout(function () {
                //   this.tools = ImageTools.init(".setMapBlock");
                //   //设置点位
                //   function setMapIndex(x, y) {
                //     $('.index-point').css({ 'top': `${y}px`, 'left': `${x}px`, 'display': 'block' })
                //     self.tempIndex = `${x},${y}`
                //   }
                //   $('.setMapBlock-body').on('click', function (event) {
                //     setMapIndex(tools.options.offsetX, tools.options.offsetY)
                //   })
                // }, 500)
            },
            mapClick(event){
                let self = this
                let offsetX = event.offsetX
                let offsetY = event.offsetY
                function setMapIndex(x, y) {
                    $('.index-point').css({ 'top': `${y}px`, 'left': `${x}px`, 'display': 'block' })
                    self.tempIndex = `${x},${y}`
                }
                setMapIndex(offsetX,offsetY)
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
                let mapBlock = document.querySelector('.setMapBlock-body')
                let mapId = document.querySelector('.setMapBlock')
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
                    let left = parseInt(document.querySelector('.setMapBlock').style.left)
                    let top = parseInt(document.querySelector('.setMapBlock').style.top)
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
                let mapBlock = document.querySelector('.setMapBlock-body')
                let mapId = document.querySelector('.setMapBlock')
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
            passwordShow(data,type){
                this.$set(data,'passwordShow',type)
            },
        },
        mounted() {
            this.height = document.querySelector('.page-table').clientHeight;
            this.getList()
            this.getUser()
            this.getDeptInfo()
        },
    })
</script>

</html>