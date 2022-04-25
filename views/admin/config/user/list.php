<?php
include(APP_PATH . '/views/admin/common/header.html');
?>
<link href="/public/css/user.css" rel="stylesheet">

<body>
<div id="app" v-clock class="user-page">
    <div class="page-top">
        <h4>人员配置</h4>
        <el-button type="primary" size="small" @click="addItem">新增人员</el-button>
    </div>
    <div class="page-table">
        <el-table ref="table" v-if="height" :data="list" :height="height">
            <el-table-column label="姓名" prop="name"></el-table-column>
            <el-table-column label="电话" prop="phone"></el-table-column>
            <el-table-column label="部门" prop="deptName"></el-table-column>
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

    <el-dialog :title="isEdit?'编辑用户':'新增用户'" :visible.sync="dialogVisible_add" width="400px">
        <el-form ref="form" :model="form" label-width="80px">
            <el-form-item label="姓名">
                <el-input type="text" v-model="form.name"></el-input>
            </el-form-item>
            <el-form-item label="电话">
                <el-input type="text" v-model="form.phone"></el-input>
            </el-form-item>
            <el-form-item label="部门">
                <el-cascader style="width:100%"   :props="props" v-model="form.dept_id" :options="deptOptions" clearable></el-cascader>
            </el-form-item>

        </el-form>
        <span slot="footer" class="dialog-footer">
        <el-button @click="dialogVisible_add = false">取 消</el-button>
        <el-button type="primary" @click="confirmAdd">确 定</el-button>
      </span>
    </el-dialog>

</div>
</body>
<?php
include(APP_PATH . '/views/admin/common/footer.html');
?>
<script>
    let appVue =  new Vue({
        el: '#app',
        data: function () {
            return {
                dialogVisible_add: false,
                dialogVisible_map: false,
                height: 0,
                total: 100,
                pageSize: 20,
                currentPage: 1,
                isEdit:false,
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

            }
        },
        methods: {
            getList(page) {
                let self = this
                http.post('/admin/config/userList', {
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
                http.post('/admin/config/userSave', {
                    id: self.isEdit ? self.form.user_id : 0,
                    deptId: self.form.dept_id,
                    name: self.form.name,
                    phone: self.form.phone,
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
                this.$confirm(`<span>确定要删除人员 <i style="color:#58B2C5;">${data.name}</i> 吗</span>`, '提 示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    dangerouslyUseHTMLString: true,
                    type: 'warning',
                    reverseBtnOrder: true
                }).then(() => {

                    http.post('/admin/config/delUser', {
                        id: data.user_id,
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
                this.form.index = this.tempIndex
                this.dialogVisible_map = false;
            },
            openMap() {
                this.dialogVisible_map = true;
                let self = this
                setTimeout(function () {
                    this.tools = ImageTools.init(".setMapBlock");
                    //设置点位
                    function setMapIndex(x, y) {
                        $('.index-point').css({ 'top': `${y}px`, 'left': `${x}px`, 'display': 'block' })
                        self.tempIndex = `${x},${y}`
                    }
                    $('.setMapBlock-body').on('click', function (event) {
                        setMapIndex(tools.options.offsetX, tools.options.offsetY)
                    })
                }, 500)
            }
        },
        mounted() {
            this.height = document.querySelector('.page-table').clientHeight;
            this.getList()
            this.getDeptInfo()
        },
    })
</script>

</html>