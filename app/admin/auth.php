<?php
class auth extends base {
    /**
     * @var UserService
     */
    private $userService;
    public function __construct() {
        parent::__construct();

        $this->userService = new UserService();
    }

    public function login() {
        if (!empty($_SESSION[SessionConst::UserId])) {
            return $this->redirect('/admin/index/index');
        }

        $this->render(array(), '/admin/auth/login');
    }

    public function userInfo() {
        $userId = $_SESSION[SessionConst::UserId];
        $userInfo = $this->userService->getInfo($userId);

        return $this->renderJSON(CommonTool::successResult(array(
            'userInfo' => $userInfo,
        )));
    }

    /**
     * 登录
     * @param userName 用户名
     * @param password 密码
     */
    public function doLogin() {
        $userName = $_POST['userName'] ? : '';
        $password = $_POST['password'] ? : '';
        if (CommonTool::anyEmpty($userName, $password)) {
            return $this->renderJSON(CommonTool::failResult('用户名密码必填'));
        }

        $userInfo = $this->userService->getUserInfo($userName, $password);
        if (empty($userInfo)) {
            return $this->renderJSON(CommonTool::failResult('用户名或密码错误'));
        }

        $_SESSION[SessionConst::UserId] = $userInfo['user_id'];
        $_SESSION[SessionConst::UserName] = $userInfo['name'];
        return $this->renderJSON(CommonTool::successResult());
    }

    public function logout() {
        $sessionId = session_id();
        if (!empty($sessionId)) {
            session_unset();
            session_destroy();
        }

        if (isset($_GET['redirectUrl']) && $_GET['redirectUrl']) {
            return $this->redirect($_GET['redirectUrl']);
        }

        return $this->redirect('/admin/auth/login');
    }

    /**
     * 重置密码
     * @param newPwd 新密码
     * @param newConfirmPwd 新密码确认
     */
    public function updatePwd() {
        $userId = $_SESSION[SessionConst::UserId];
        $newPwd = $_POST['newPwd'];
        $newConfirmPwd = $_POST['newConfirmPwd'];

        if (CommonTool::anyEmpty($newPwd, $newConfirmPwd)) {
            return $this->renderJSON(CommonTool::failResult('参数不全'));
        }

        if ($newPwd != $newConfirmPwd) {
            return $this->renderJSON(CommonTool::failResult('确认密码与新密码不一致'));
        }

        $resetRet = $this->userService->resetPwd($userId, $newConfirmPwd);
        return $this->renderJSON($resetRet);
    }
}