<?php
class config extends base {
    /**
     * @var ConfigService
     */
    private $configService;

    /**
     * @var UserService
     */
    private $userService;

    public function __construct() {
        parent::__construct(true);

        $this->configService = new ConfigService();
        $this->userService = new UserService();
    }

    public function camera() {
        $this->render(array(), 'admin/config/camera/list');
    }

    public function cameraList() {
        $page = $_POST['page'] ? : 1;
        $pageSize = $_POST['pageSize'] ? : 20;

        $pagination = array('page'=> $page, 'pageSize'=> $pageSize);
        list($list, $total) = $this->configService->getCameraList($pagination);
        $list = $this->configService->appendDataDeptName($list);
        $list = $this->userService->appendDataUserName($list);
        $list = $this->userService->appendDataUserName($list, 'opeator', 'opeatorName');

        $data = array(
            'list' => $list,
            'page' => $page,
            'pageSize' => $pageSize,
            'total' => $total,
        );

        return $this->renderJSON(CommonTool::successResult($data));
    }

    public function cameraInfo() {
        $id = $_POST['id'];

        if (empty($id)) {
            return $this->renderJSON(CommonTool::failResult('请传摄像头编号'));
        }

        $cameraInfo = $this->configService->getCameraInfo($id);

        $data = array(
            'cameraInfo' => $cameraInfo,
        );

        return $this->renderJSON(CommonTool::successResult($data));
    }

    public function getDeptTree() {
        $deptTree = $this->configService->getDeptTree();

        $data = array(
            'deptTree' => $deptTree,
        );

        return $this->renderJSON(CommonTool::successResult($data));
    }

    public function cameraSave() {
        $post = $_POST;
        $id = $post['id'] ? : 0;

        $checkRet = $this->checkCameraValid($post);
        if (CommonTool::isFailRet($checkRet)) {
            return $this->renderJSON($checkRet);
        }

        $saveRet = $this->configService->cameraSave($id, $post);
        return $this->renderJSON($saveRet ? CommonTool::successResult() : CommonTool::failResult('保存失败'));
    }

    public function delCamera() {
        $post = $_POST;
        $id = $post['id'] ? : 0;

        $delRet = $this->configService->delCamera($id);
        return $this->renderJSON($delRet ? CommonTool::successResult() : CommonTool::failResult('删除失败'));
    }

    private function checkCameraValid($post) {
        if (empty($post['deptId'])) {
            return CommonTool::failResult('请选择摄像头所属部门');
        }

        if (empty($post['userId'])) {
            return CommonTool::failResult('请选择摄像头所属责任人');
        }

        if (empty($post['cameraName'])) {
            return CommonTool::failResult('请填写摄像头名称');
        }

        if (empty($post['ip'])) {
            return CommonTool::failResult('请填写摄像头ip');
        }

        if (empty($post['userName'])) {
            return CommonTool::failResult('请填写摄像头账号');
        }

        if (empty($post['password'])) {
            return CommonTool::failResult('请填写摄像头密码');
        }

        if (empty($post['coordinate'])) {
            return CommonTool::failResult('请选择摄像头坐标');
        }

        $checkConflictRet = $this->configService->checkCameraConflict($post);
        if (CommonTool::isFailRet($checkConflictRet)) {
            return $checkConflictRet;
        }

        return CommonTool::successResult();
    }

    public function dept() {
        $this->render(array(), 'admin/config/dept/list');
    }

    public function deptInfo() {
        $id = $_POST['id'];

        if (empty($id)) {
            return $this->renderJSON(CommonTool::failResult('请选择部门'));
        }

        $deptInfo = $this->configService->getDeptInfo($id);
        if (empty($deptInfo)) {
            return $this->renderJSON(CommonTool::failResult('无效部门选择'));
        }

        return $this->renderJSON(CommonTool::successResult(array(
            'deptInfo' => $deptInfo,
        )));
    }

    public function deptSave() {
        $post = $_POST;
        $id = $post['id'] ? : 0;

        if (empty($post['deptName'])) {
            return $this->renderJSON(CommonTool::failResult('请填写部门名称'));
        }

        if ($id) {
            $deptInfo = $this->configService->getDeptInfo($id);
            if (empty($deptInfo)) {
                return $this->renderJSON(CommonTool::failResult('无效部门信息'));
            }
        }

        $saveRet = $this->configService->deptSave($id, $post, $_SESSION[SessionConst::UserId]);
        if (empty($saveRet)) {
            return $this->renderJSON(CommonTool::failResult('保存失败'));
        }

        $deptId = $id ? : $saveRet;
        $newDeptInfo = $this->configService->getDeptInfo($deptId);
        return $this->renderJSON( CommonTool::successResult(array(
            'deptInfo' => $newDeptInfo,
        )));
    }

    public function delDept() {
        $post = $_POST;
        $id = $post['id'] ? : 0;

        $delRet = $this->configService->delDept($id);
        return $this->renderJSON($delRet ? CommonTool::successResult() : CommonTool::failResult('删除失败'));
    }

    public function user() {
        $this->render(array(), 'admin/config/user/list');
    }

    public function userList() {
        $page = $_POST['page'] ? : 1;
        $pageSize = $_POST['pageSize'] ? : 20;

        $pagination = array('page'=> $page, 'pageSize'=> $pageSize);
        list($list, $total) = $this->userService->getList($pagination);
        $list = $this->configService->appendDataDeptName($list);

        $data = array(
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'pageSize' => $pageSize
        );

        return $this->renderJSON(CommonTool::successResult($data));
    }

    public function userInfo() {
        $id = $_POST['id'];

        if (empty($id)) {
            return $this->renderJSON(CommonTool::failResult('无效用户id'));
        }

        $userInfo = $this->userService->getInfo($id);
        $data = array(
            'userInfo' => $userInfo,
        );

        return $this->renderJSON(CommonTool::successResult($data));
    }

    public function userSave() {
        $post = $_POST;
        $id = $post['id'] ? : 0;

        $checkRet = $this->checkUserValid($post);
        if (CommonTool::isFailRet($checkRet)) {
            return $this->renderJSON($checkRet);
        }

        $saveRet = $this->userService->userSave($id, $post);
        return $this->renderJSON($saveRet ? CommonTool::successResult() : CommonTool::failResult('保存失败'));
    }

    public function delUser() {
        $post = $_POST;
        $id = $post['id'] ? : 0;

        $delRet = $this->userService->delUser($id);
        return $this->renderJSON($delRet ? CommonTool::successResult() : CommonTool::failResult('删除失败'));
    }

    private function checkUserValid($post) {
        if (empty($post['deptId'])) {
            return CommonTool::failResult('请选择部门');
        }

        if (empty($post['name'])) {
            return CommonTool::failResult('请填写姓名');
        }

        if (empty($post['phone'])) {
            return CommonTool::failResult('请填写手机号');
        }

        return CommonTool::successResult();
    }
    
    public function deptUser() {
        $deptId = $_POST['deptId'] ? : 0;
        $page = $_POST['page'] ? : 1;
        $pageSize = $_POST['pageSize'] ? : 20;

        $pagination = array('page'=> $page, 'pageSize'=> $pageSize);
        list($deptUsers, $total) = $this->configService->getDeptUsers($deptId, $pagination);

        $data = array(
            'deptUsers' => $deptUsers,
            'total' => $total,
            'page' => $page,
            'pageSize' => $pageSize
        );

        return $this->renderJSON(CommonTool::successResult($data));
    }

    public function account() {
        $this->render(array(), 'admin/config/user/account');
    }
}