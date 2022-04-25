<?php
class FrontService extends BaseService {
    public function __construct() {
        parent::__construct();
    }

    public function getActionStatistics() {
        return $this->db->select('action, count(1) as cnt')->from('detection_log')->group('action')->fetchAll();
    }

    public function detection($isNg = 0) {
        $curl = new CurlTool();
        $getRet = $curl->get(ConfigTool::C('apiUrl'), array('isNg' => $isNg));
        $getRet = json_decode($getRet, true);
        if (!CommonTool::isSuccessRet($getRet)) {
            return [];
        }
        $data = $getRet['data'];

        $serialNos = ArrayTool::getSub($data, 'serialNo');
        $existSerialNos = $this->getExistSerialNo($serialNos);
        $existSerialNos = ArrayTool::getSub($existSerialNos, 'detection_log_id');

        $ngList = [];
        foreach ($data as $row) {
            if (in_array($row['serialNo'], $existSerialNos)) {
                continue;
            }

            if ($row['status'] == 'NG') {
                $ngList[] = $row;
            }

            $cameraInfo = $this->db->select('camera_name')->from('camera')->where('ip', $row['ip'])->fetchOne();
            $insert = array(
                'detection_log_id' => $row['serialNo'],
                'action' => $row['action'],
                'addr' => $cameraInfo ? $cameraInfo['camera_name'] : $row['addr'],
                'status' => $row['status'],
                'image_url' => ConfigTool::C('apiImageUrl') . $row['imageUrl'],
                'video_url' => $row['videoUrl'],
                'camera_ip' => $row['ip'],
                'gmt_detection' => $row['gmtCreate'],
                'gmt_create' => date('Y-m-d H:i:s'),
                'gmt_modified' => date('Y-m-d H:i:s')
            );

            $this->db->insert('detection_log')->rows($insert)->execute();
        }

        return $ngList;
    }

    private function getExistSerialNo($serialNos) {
        if (empty($serialNos)) {
            return [];
        }

        return $this->db->select('detection_log_id')->from('detection_log')->where('detection_log_id in (' . implode(',', $serialNos) . ')')->fetchAll();
    }

    public function getScreenData() {
        return $this->db->select('*')->from('detection_log')->order('detection_log_id', 'desc')->limit(4)->fetchAll();
    }

    public function getDetectionLog($where, $pagination = array()) {
        $row = $this->_setWhereSQL($where)->_setPaginationSQL($pagination)
            ->db->select('*')
            ->from('detection_log')
            ->order( 'detection_log_id', 'DESC')
            ->fetchAll();

        if ($row === FALSE) {
            return array(false, 0);
        }

        $total = $this->countDetectionLog($where);
        return array($row, $total);
    }

    /**
     * SQL Where条件
     * @param array $condition
     * @return $this
     */
    private function _setWhereSQL ($condition = array()) {
        if (isset($condition['startTime']) AND $condition['startTime']) {
            $this->db->where("gmt_detection >= '" . $condition['startTime'] . "'");
        }

        if (isset($condition['endTime']) AND $condition['endTime']) {
            $this->db->where("gmt_detection <= '" . $condition['endTime'] . "'");
        }

        if (isset($condition['userId']) AND $condition['userId']) {
            $this->db->where("target_user_id", $condition['userId']);
        }

        if (isset($condition['addr']) AND $condition['addr']) {
            $this->db->where("addr LIKE '%{$condition['addr']}%'");
        }

        if (isset($condition['action']) AND $condition['action']) {
            $this->db->where("action LIKE '%{$condition['action']}%'");
        }

        if (isset($condition['status']) AND $condition['status']) {
            $this->db->where("status", $condition['status']);
        }

        return $this;
    }

    private function countDetectionLog($where) {
        return $this->_setWhereSQL($where)->db->select('COUNT(*)')->from('detection_log')->fetchValue();
    }

    public function setAutoCheck($data) {
        $id = $data['id'];
        $autoCheckInfo = $this->db->select('*')->from('auto_check')->where('auto_check_id', $id)->fetchOne();
        $insert = array(
            'auto_check_id' => $id,
            'begin' => $data['begin'],
            'end' => $data['end'],
            'status' => $data['status'] ? : StatusConst::autoCheckStatusOpen,
            'times' => $data['times'],
            'interval_times' => $data['interval_times'],
            'camera_list' => $data['camera_list'],
            'gmt_create' => date('Y-m-d H:i:s'),
            'gmt_modified' => date('Y-m-d H:i:s'),
        );
        $update = $insert;
        unset($update['gmt_create']);
        if ($autoCheckInfo) {
            return $this->db->update('auto_check')->rows($update)->where('auto_check_id', $id)->execute();
        } else {
            return $this->db->insert('auto_check')->rows($insert)->execute();
        }
    }

    public function getAutoCheckInfo($id) {
        return $this->db->select('*')->from('auto_check')->where('auto_check_id', $id)->fetchOne();
    }

    public function setAutoCheckStatus($id, $status) {
        $autoCheckInfo = $this->getAutoCheckInfo($id);
        if (empty($autoCheckInfo)) {
            return CommonTool::failResult('请先设置自动巡检');
        }

        $this->db->update('auto_check')->set('status', $status)->where('auto_check_id', $id)->execute();
        $autoCheckInfo = $this->getAutoCheckInfo($id);
        return CommonTool::successResult('autoCheckInfo', $autoCheckInfo);
    }

    public function getAutoCheckList() {
        return $this->db->select('*')->from('auto_check')->where('status', StatusConst::autoCheckStatusOpen)->fetchAll();
    }

    public function checkAutoCheckConflict($post) {
        $id = intval($post['id']);
        $conflictData = $this->db->select('*')->from('auto_check')->where('begin <="' . $post['end'] . '"')->where('end >= "' . $post['begin'] . '"')->where('auto_check_id != ' . $id)->fetchOne();
        if (!empty($conflictData)) {
            return CommonTool::failResult('该时段已配置自动巡检');
        }

        return CommonTool::successResult();
    }

    public function getActions() {
        $actions = [
            '' => '全部',
        ];
        $list = $this->db->select('distinct(action)')->from('detection_log')->fetchAll();
        if ($list) {
            foreach ($list as $row) {
                $actions[$row['action']] = $row['action'];
            }
        }

        return $actions;
    }

    public function readDetectionLog($id) {
        return $this->db->update('detection_log')->set('is_read', 1)->where('detection_log_id', $id)->execute();
    }

    public function getCameraInfoByUserId($userId) {
        return $this->db->select('*')->from('camera')->where('user_id', $userId)->fetchOne();
    }
}