<?php
class UserService extends BaseService {
    public function __construct() {
        parent::__construct();
    }

    public function getUserInfo($userName, $password) {
        return $this->db->select('*')->from('user')->where('name', $userName)->where('password', $password)->fetchOne();
    }

    public function getInfo($id) {
        return $this->db->select('*')->from('user')->where('user_id', $id)->fetchOne();
    }

    public function getList($pagination) {
        $row = $this->_setPaginationSQL($pagination)
            ->db->select('*')
            ->from('user')
            ->order( 'user_id', 'DESC')
            ->fetchAll();

        if ($row === FALSE) {
            return array(false, 0);
        }

        $total = $this->countUser();
        return array($row, $total);
    }

    private function countUser() {
        return $this->db->select('COUNT(*)')->from('user')->fetchValue();
    }

    public function userSave($id, $post) {
        $insert = array(
            'dept_id' => $post['deptId'],
            'phone' => $post['phone'],
            'name' => $post['name'],
            'password' => $post['password'],
            'gmt_create' => date('Y-m-d H:i:s'),
            'gmt_modified' => date('Y-m-d H:i:s')
        );

        $update = $insert;
        unset($update['gmt_create'], $update['password']);
        if ($id > 0) {
            return $this->db->update('user')->rows($update)->where('user_id', $id)->execute();
        } else {
            return $this->db->insert('user')->rows($insert)->execute();
        }
    }

    public function delUser($id) {
        return $this->db->delete('user')->where('user_id', $id)->execute();
    }

    public function resetPwd($id, $pwd) {
        return $this->db->update('user')->set('password', $pwd)->where('user_id', $id)->execute();
    }

    public function appendDataUserName($list, $field = 'user_id', $showName = 'userName') {
        if (empty($list)) {
            return [];
        }

        $userIds = array_filter(ArrayTool::getSub($list, $field));
        if (empty($userIds)) {
            return $list;
        }

        $userList = $this->db->select('user_id, name')->from('user')->where('user_id in (' . implode(',', $userIds) . ')')->fetchAll();
        $userMap = ArrayTool::mapNameValue($userList, 'user_id', 'name');

        foreach ($list as &$row) {
            $userId = $row['user_id'];
            if ($userMap[$userId]) {
                $row[$showName] = $userMap[$userId];
            }
        }

        return $list;
    }

    public function getAllUserList() {
        return $this->db->select('user_id, name')->from('user')->fetchAll();
    }
}