<?php
include(APP_PATH . '/views/admin/common/header.html');
?>
<link href="/public/css/department.css" rel="stylesheet">

<body>
<div id="app" v-clock class="department-page">
    <div class="page-top">
        <h4>部门配置</h4>
        <el-button type="primary" size="small" @click="addDept">新增部门</el-button>
    </div>
    <div class="page-table">
        <el-tree :data="list" node-key="id" :expand-on-click-node="false">
        <span class="custom-tree-node" slot-scope="{ node, data }">
          <span>{{ node.label }}</span>
          <span><i class="el-icon-edit" @click="() => eidtName(node,data)"></i></i></span>
          <span class="custom-tree-node-right">
            <el-button type="text" size="mini" @click="() => deptInfo(node,data)">
              详情
            </el-button>
            <el-button type="text" size="mini" @click="() => append(node,data)">
              新增子部门
            </el-button>
            <el-button type="text" size="mini" @click="() => remove(node, data)">
              删除
            </el-button>
          </span>
        </span>
        </el-tree>

    </div>
    <el-dialog :title="tableInfo.label" :visible.sync="dialogVisible" width="360">
        <el-table :data="tableList" height="500">
            <el-table-column property="name" label="姓名"  ></el-table-column>
            <el-table-column property="phone" label="电话" ></el-table-column>
        </el-table>
        <!-- <el-pagination
          layout="prev, pager, next"
          :current-page.sync="currentPage"
          @current-change="handleCurrentChange"
          :total="total">
        </el-pagination> -->
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
                tableInfo:{},
                tableList:[],
                currentPage:1,
                total:0,
                list: []
            }
        },
        methods: {
            getDeptTree() {
                let self = this
                http.post('/admin/config/getDeptTree', {
                }).then(function (res) {
                    if (res.result == "fail") {
                        self.$message.error(res.reason);
                    } else {
                        self.list = res.deptTree
                    }
                })
            },
            savedept(data) {
                let self = this;
                return new Promise((resolve, reject) => {
                    http.post('/admin/config/deptSave', data).then(function (res) {
                        if (res.result == "fail") {
                            reject(false);
                            self.$message.error(res.reason);
                        } else {
                            resolve(res.deptInfo)
                            self.$message({
                                type: 'success',
                                message: '保存成功'
                            });
                        }
                    })
                })
            },
            addDept() {
                let self = this
                this.$prompt('新增一级部门', '', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    inputPattern: /^.{1,100}$/,
                    inputErrorMessage: '请输入内容'
                }).then(({ value }) => {
                    self.savedept({
                        id: 0,
                        parentId: 0,
                        deptName: value
                    }).then((deptInfo) => {
                        const newChild = { id: deptInfo.dept_id, label: value, children: [] };
                        self.list.push(newChild)
                    })

                }).catch(() => {

                });
            },
            eidtName(node, data) {
                let self = this
                this.$prompt('编辑部门', '', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    inputValue: data.label,
                    inputPattern: /^.{1,100}$/,
                    inputErrorMessage: '请输入内容'
                }).then(({ value }) => {
                    data.label = value
                    //用data.id  去更新 后端
                    self.savedept({
                        id: data.id,
                        deptName: value
                    })
                }).catch(() => {

                });
            },
            append(node, data) {
                this.$prompt('新增部门', '', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    inputPattern: /^.{1,100}$/,
                    inputErrorMessage: '请输入内容'
                }).then(({ value }) => {
                    this.savedept({
                        id: 0,
                        parentId: data.id,
                        deptName: value
                    }).then((deptInfo) => {
                        const newChild = { id: deptInfo.dept_id, label: value, children: [] };
                        if (!data.children) {
                            this.$set(data, 'children', []);
                        }
                        data.children.push(newChild);
                    })
                }).catch(() => {

                });


            },

            remove(node, data) {
                this.$confirm(`<span>确定要删除 <i style="color:#58B2C5;">${data.label}</i> 吗</span>`, '提 示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    dangerouslyUseHTMLString: true,
                    type: 'warning',
                    reverseBtnOrder: true
                }).then(() => {
                    http.post('/admin/config/delDept', {
                        id: data.id,
                    }).then(function (res) {
                        if (res.result == "fail") {
                            self.$message.error(res.reason);
                        } else {
                            const parent = node.parent;
                            const children = parent.data.children || parent.data;
                            const index = children.findIndex(d => d.id === data.id);
                            children.splice(index, 1);
                        }
                    })
                })
            },
            getTableList(page){
                let self = this
                http.post('/admin/config/deptUser', {
                    page: page || 0,
                    deptId:this.tableInfo.id
                }).then(function (res) {
                    if (res.result == "fail") {
                        self.$message.error(res.reason);
                    } else {
                        self.tableList = res.deptUsers || []
                        // self.total = Number(res.total)
                    }
                })
            },
            deptInfo(node, data) {
                this.dialogVisible = true
                this.tableInfo = data
                this.getTableList(1)
            },
            handleCurrentChange(val) {
                console.log(`当前页: ${val}`);
            },
            openChangePassword() {
                this.dialogVisible = true
            },
            confirm() {

            },
        },
        mounted() {
            this.getDeptTree()
        },
    })
</script>

</html>