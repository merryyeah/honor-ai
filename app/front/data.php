<?php
class data extends base {
    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var ConfigService
     */
    private $configService;

    /**
     * @var FrontService
     */
    private $frontService;
    public function __construct() {
        parent::__construct();

        $this->userService = new UserService();
        $this->configService = new ConfigService();
        $this->frontService = new FrontService();
    }

    public function config() {
        $data = array(
            'detectionIntervalTime' => ConfigTool::C('detectionIntervalTime'),
            'detectionUrl' => ConfigTool::C('detectionUrl'),
            'serviceTime' => time(),
        );

        return $this->renderJSON(CommonTool::successResult($data));
    }

    public function detection() {
        $isNg = $_POST['isNg'] ? : 0;
        $ngList = $this->frontService->detection($isNg);
        if ($ngList) {
            $ngList = $this->configService->appendDataCameraInfoByIp($ngList);
        }

        $screenData = $this->frontService->getScreenData();
        return $this->renderJSON(CommonTool::successResult(array(
            'screenData' => $screenData,
            'ngList' => $ngList,
        )));
    }

    public function exportDetectionLog() {
        $where = $this->getDetectionLogWhere();

        list($list) = $this->frontService->getDetectionLog($where);
        $list = $this->userService->appendDataUserName($list, 'target_user_id');
        $data = [];
        $titleArr = array(
            '序号', '状态', '行为', '创建时间', '地点', '图片', '视频', '负责人', '等级'
        );
        if ($list) {
            foreach ($list as $row) {
                $data[] = array(
                    $row['detection_log_id'],
                    $row['is_read'] ? '已读' : '未读',
                    $row['action'],
                    $row['gmt_create'],
                    $row['addr'],
                    $row['image_url'],
                    $row['video_url'],
                    $row['userName'],
                    $row['score'],
                );
            }
        }

        $fileName = '异常数据导出' . date('YmdHis') . '.csv';
        $exportUrl = '/public/export/' . $fileName;
        CommonTool::exportCsv($data, $titleArr, $fileName, APP_PATH . $exportUrl);

        return $this->renderJSON(CommonTool::successResult('url', $exportUrl));
    }

    private function getDetectionLogWhere() {
        $startTime = $_POST['startTime'] ? : '';
        $endTime = $_POST['endTime'] ? : '';
        $addr = $_POST['addr'] ? : '';
        $userId = $_POST['userId'] ? : '';
        $action = $_POST['action'] ? : '';
        $where = array(
            'startTime' => $startTime,
            'endTime' => $endTime,
            'addr' => $addr,
            'userId' => $userId,
            'action' => $action,
            'status' => 'NG'
        );

        return $where;
    }

    public function detectionLog() {
        $page = $_POST['page'] ? : 1;
        $pageSize = $_POST['pageSize'] ? : 20;

        $where = $this->getDetectionLogWhere();

        $pagination = array('page'=> $page, 'pageSize'=> $pageSize);
        list($list, $total) = $this->frontService->getDetectionLog($where, $pagination);
        $list = $this->userService->appendDataUserName($list, 'target_user_id');

        $data = array(
            'list' => $list,
            'page' => $page,
            'pageSize' => $pageSize,
            'total' => $total,
        );

        return $this->renderJSON(CommonTool::successResult($data));
    }

    public function cameraList() {
        $page = $_POST['page'] ? : 1;
        $pageSize = $_POST['pageSize'] ? : 20;

        $pagination = array('page'=> $page, 'pageSize'=> $pageSize);
        list($list, $total) = $this->configService->getCameraList($pagination);
        $list = $this->configService->appendDataDeptName($list);

        $data = array(
            'list' => $list,
            'page' => $page,
            'pageSize' => $pageSize,
            'total' => $total,
        );

        return $this->renderJSON(CommonTool::successResult($data));
    }

    public function getAllCameraList() {
        $list = $this->configService->getAllCameraList();
        return $this->renderJSON(CommonTool::successResult(array(
            'list' => $list
        )));
    }

    public function getActionStatistics() {
        $data = $this->frontService->getActionStatistics();

        return $this->renderJSON(CommonTool::successResult(array(
            'data' => $data
        )));
    }

    public function getFullDeptTree() {
        $data = $this->configService->getFullDeptTree();

        return $this->renderJSON(CommonTool::successResult(array(
            'data' => $data
        )));
    }

    public function getUserList() {
        $list = $this->userService->getAllUserList();

        return $this->renderJSON(CommonTool::successResult(array(
            'list' => $list
        )));
    }

    public function setAutoCheck() {
        $post = $_POST;
        $checkRet = $this->checkAutoCheckValid($post);
        if (CommonTool::isFailRet($checkRet)) {
            return $this->renderJSON($checkRet);
        }

        $this->frontService->setAutoCheck($post);
        return $this->renderJSON(CommonTool::successResult());
    }

    private function checkAutoCheckValid($post) {
        if (empty($post['id']) || !in_array($post['id'], array(1, 2, 3, 4))) {
            return CommonTool::failResult('请选择编辑内容');
        }

        if (empty($post['begin'])) {
            return CommonTool::failResult('请选择开始时间');
        }

        if (empty($post['end'])) {
            return CommonTool::failResult('请选择结束时间');
        }

        if ($post['begin'] > $post['end']) {
            return CommonTool::failResult('开始时间必需小于结束时间');
        }

        if (empty($post['camera_list'])) {
            return CommonTool::failResult('请选择摄像头');
        }

        if (empty($post['times'])) {
            return CommonTool::failResult('请选择播放时长');
        }

        if (empty($post['interval_times'])) {
            return CommonTool::failResult('请选择播放时间间隔');
        }

        $checkConflictRet = $this->frontService->checkAutoCheckConflict($post);
        if (CommonTool::isFailRet($checkConflictRet)) {
            return $checkConflictRet;
        }

        return CommonTool::successResult();
    }

    public function getCameraVideoUrl() {
        $id = $_POST['id'];
        $cameraInfo = $this->configService->getCameraInfo($id);
        $videoInfo = array(
            'windowTitle' => $cameraInfo['camera_name'],
            'rtspUrl' => sprintf('rtsp://%s:%s@%s/Streaming/Channels/101', $cameraInfo['user_name'], $cameraInfo['password'], $cameraInfo['ip'])
        );

        return $this->renderJSON(CommonTool::successResult($videoInfo));
    }

    public function getUserVideoUrl() {
        $id = $_POST['id'];
        list(, $userId) = explode('_', $id);
        $cameraInfo = $this->frontService->getCameraInfoByUserId($userId);
        $videoInfo = array(
            'windowTitle' => $cameraInfo['camera_name'],
            'rtspUrl' => sprintf('rtsp://%s:%s@%s/Streaming/Channels/101', $cameraInfo['user_name'], $cameraInfo['password'], $cameraInfo['ip'])
        );

        return $this->renderJSON(CommonTool::successResult($videoInfo));
    }

    public function getAutoCheck() {
        $id = $_POST['id'];
        if (empty($id) || !in_array($id, array(1, 2, 3, 4))) {
            return $this->renderJSON(CommonTool::failResult('请选择编辑内容'));
        }

        $autoCheckInfo = $this->frontService->getAutoCheckInfo($id);

        return $this->renderJSON(CommonTool::successResult(array(
            'autoCheckInfo' => $autoCheckInfo
        )));
    }

    public function setAutoCheckStatus() {
        $id = $_POST['id'];
        $status = $_POST['status'];
        if (empty($id) || !in_array($id, array(1, 2, 3, 4))) {
            return $this->renderJSON(CommonTool::failResult('请选择编辑内容'));
        }

        if (!in_array($status, array(StatusConst::autoCheckStatusStop, StatusConst::autoCheckStatusOpen))) {
            return $this->renderJSON(CommonTool::failResult('状态参数异常'));
        }

        $setRet = $this->frontService->setAutoCheckStatus($id, $status);

        return $this->renderJSON($setRet);
    }

    public function getAutoCheckList() {
        $autoCheckList = $this->frontService->getAutoCheckList();

        return $this->renderJSON(CommonTool::successResult(array(
            'autoCheckList' => $autoCheckList,
        )));
    }

    public function getCameraNames() {
        $cameraNames = $this->configService->getCameraNames();

        return $this->renderJSON(CommonTool::successResult('cameraNames', $cameraNames));
    }

    public function getActions() {
        $actions = $this->frontService->getActions();

        return $this->renderJSON(CommonTool::successResult('actions', $actions));
    }

    public function readDetectionLog() {
        $id = $_POST['id'];

        if ($id) {
            $this->frontService->readDetectionLog($id);
        }

        return $this->renderJSON(CommonTool::successResult());
    }
}