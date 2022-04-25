<?php
class ConfigService extends BaseService{
    public function __construct() {
        parent::__construct();
    }

    public function getAllCameraList() {
        return $this->db->select('*')->from('camera')->fetchAll();
    }

    public function getCameraList($pagination = []) {
        $row = $this->_setPaginationSQL($pagination)
            ->db->select('*')
            ->from('camera')
            ->order( 'camera_id', 'DESC')
            ->fetchAll();

        if ($row === FALSE) {
            return array(false, 0);
        }

        $total = $this->countCamera();
        return array($row, $total);
    }

    private function countCamera() {
        return $this->db->select('COUNT(*)')->from('camera')->fetchValue();
    }

    public function getCameraInfo($id) {
        return $this->db->select('*')->from('camera')->where('camera_id', $id)->fetchOne();
    }

    public function checkCameraConflict($post) {
        $id = intval($post['id']);
        $conflictData = $this->db->select('*')->from('camera')->where('ip = "' . $post['ip'] . '"')->where('camera_id !=' . $id)->fetchOne();
        if (!empty($conflictData)) {
            return CommonTool::failResult('该ip已配置');
        }

        return CommonTool::successResult();
    }

    public function cameraSave($id, $post) {
        $insert = array(
            'dept_id' => $post['deptId'],
            'user_id' => $post['userId'],
            'camera_name' => $post['cameraName'],
            'ip' => $post['ip'],
            'user_name' => $post['userName'],
            'password' => $post['password'],
            'coordinate' => $post['coordinate'],
            'gmt_create' => date('Y-m-d H:i:s'),
            'gmt_modified' => date('Y-m-d H:i:s')
        );

        $update = $insert;
        unset($update['gmt_create']);
        if ($id > 0) {
            return $this->db->update('camera')->rows($update)->where('camera_id', $id)->execute();
        } else {
            return $this->db->insert('camera')->rows($insert)->execute();
        }
    }

    public function delCamera($id) {
        return $this->db->delete('camera')->where('camera_id', $id)->execute();
    }

    public function appendDataDeptName($list) {
        if (empty($list)) {
            return [];
        }

        $deptMap = $this->getDeptPathMap();

        foreach ($list as &$row) {
            $deptId = $row['dept_id'];
            if ($deptMap[$deptId]) {
                $row['deptName'] = $deptMap[$deptId];
            }
        }

        return $list;
    }

    public function getDeptList() {
        return $this->db->select('*')->from('dept')->order('dept_id')->fetchAll();
    }

    /**
     * @param bool $deptList
     * @param int $fid
     * @return array
     */
    public function getDeptTree($deptList = false, $fid = 0 ) {
        if (empty($deptList)) {
            $tempDeptList = $this->getDeptList();
            foreach ($tempDeptList as $deptInfo) {
                $deptList[$deptInfo['parent_id']][] = $deptInfo;
            }
        }

        $tree = array();
        if (!is_array($deptList[$fid])) return $tree;

        foreach ($deptList[$fid] as $child) {
            $deptId = $child['dept_id'];
            $childData = array(
                'id' => $deptId,
                'label' => $child['dept_name'],
            );

            if (isset($deptList[$deptId]) && is_array($deptList[$deptId])) {
                $childData['children'] = $this->getDeptTree($deptList, $deptId);
            }

            $tree[] = $childData;
        }

        return $tree;
    }

    public function getFullDeptTree($deptList = false, $fid = 0 ) {
        if (empty($deptList)) {
            $tempDeptList = $this->getDeptList();
            foreach ($tempDeptList as $deptInfo) {
                $deptList[$deptInfo['parent_id']][] = $deptInfo;
            }
        }

        $tree = array();
        if (!is_array($deptList[$fid])) return $tree;

        foreach ($deptList[$fid] as $child) {
            $deptId = $child['dept_id'];
            $childData = array(
                'id' => 'dept_'  . $deptId,
                'type' => 'dept',
                'label' => $child['dept_name'],
            );

            if (isset($deptList[$deptId]) && is_array($deptList[$deptId])) {
                $childData['children'] = $this->getFullDeptTree($deptList, $deptId);
            }

            $members = $this->getDeptMembers($deptId);
            if ($members) {
                foreach ($members as $member) {
                    $childData['children'][] = array(
                        'id' => 'user_' . $member['user_id'],
                        'type' => 'user',
                        'label' => $member['name'],
                    );
                }
            }

            $tree[] = $childData;
        }

        return $tree;
    }

    private function getDeptMembers($deptId) {
        return $this->db->select('user_id, name')->from('user')->where('dept_id', $deptId)->fetchAll();
    }

    public function getDeptPathMap() {
        $deptPathMap = [];

        $deptList = $this->getDeptList();
        $deptList = ArrayTool::changeKeyRow($deptList, 'dept_id');

        foreach ($deptList as $deptId => $deptInfo) {
            $deptPath = $this->getDeptPath($deptList, $deptId);
            $deptPathMap[$deptId] = $deptPath;
        }

        return $deptPathMap;
    }

    private function getDeptPath($deptList, $deptId) {
        if (empty($deptList[$deptId])) {
            return '';
        }
        $deptInfo = $deptList[$deptId];
        $deptPath = $deptInfo['dept_name'];
        if ($deptInfo['parent_id']) {
            $parentPath = $this->getDeptPath($deptList, $deptInfo['parent_id']);
            $deptPath = $parentPath ? ($parentPath . '/' . $deptPath) : $deptPath;
        }

        return $deptPath;
    }

    public function getDeptInfo($id) {
        return $this->db->select('*')->from('dept')->where('dept_id', $id)->fetchOne();
    }

    public function deptSave($id, $post, $operator) {
        $insert = array(
            'dept_name' => $post['deptName'],
            'parent_id' => $post['parentId'],
            'operator' => $operator,
            'gmt_create' => date('Y-m-d H:i:s'),
            'gmt_modified' => date('Y-m-d H:i:s')
        );

        $update = $insert;
        unset($update['gmt_create']);
        if ($id > 0) {
            return $this->db->update('dept')->rows($update)->where('dept_id', $id)->execute();
        } else {
            return $this->db->insert('dept')->rows($insert)->execute();
        }
    }

    public function delDept($id) {
        return $this->db->delete('dept')->where('dept_id', $id)->execute();
    }

    public function getDeptUsers($deptId = 0, $pagination = []) {
        $deptIds = $this->getAllDeptIds($deptId);
        if (empty($deptIds)) {
            return [];
        }

        $rows = $this->_setPaginationSQL($pagination)->db->select('*')->from('user')->where('dept_id in (' . implode(',', $deptIds) . ')')->fetchAll();
        $total = $this->db->select('COUNT(*)')->from('user')->where('dept_id in (' . implode(',', $deptIds) . ')')->fetchValue();
        return array($rows, $total);
    }

    private function getAllDeptIds($deptId = 0) {
        $deptIds = [$deptId];
        $deptIdList = $this->db->select('dept_id')->from('dept')->where('parent_id', $deptId)->fetchAll();
        if (empty($deptIdList)) {
            return $deptIds;
        }

        foreach ($deptIdList as $deptInfo) {
            $sonDeptIds = $this->getAllDeptIds($deptInfo['dept_id']);
            $deptIds = CommonTool::safeArrayMerge($deptIds, $sonDeptIds);
        }

        return $deptIds;
    }

    public function appendDataCameraInfoByIp($ngList) {
        if (empty($ngList)) {
            return [];
        }

        $ips = array_filter(ArrayTool::getSub($ngList, 'ip'));
        if (empty($ips)) {
            return [];
        }

        $cameraList = $this->db->select('*')->from('camera')->where('ip in ("' . implode('","', $ips) . '")')->fetchAll();
        $cameraList = ArrayTool::changeKeyRow($cameraList, 'ip');

        foreach ($ngList as &$ngInfo) {
            $ngIp = $ngInfo['ip'];
            if (!isset($cameraList[$ngIp])) {
                continue;
            }

            $ngInfo['cameraInfo'] = $cameraList[$ngIp];
        }

        return $ngList;
    }

    public function getCameraNames() {
        list($list) = $this->getCameraList();

        $cameraNames = [];
        if ($list) {
            foreach ($list as $row) {
                $cameraName = $row['camera_name'];
                $cameraNames[$cameraName] = $cameraName;
            }
        }

        return $cameraNames;
    }
}