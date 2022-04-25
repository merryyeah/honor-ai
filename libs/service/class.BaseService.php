<?php
class BaseService {
    /**
     * @var Db
     */
    public $db;
    public function __construct() {
        $this->db = Db::get();
    }

    /**
     * SQL分页查询
     *
     * @param array $pagination
     */
    public function _setPaginationSQL($pagination = array() ) {
        if (isset($pagination['page']) AND isset($pagination['pageSize']) ) {
            $page      = isset( $pagination['page'] ) ? intval($pagination['page']) : 1;
            $pageSize  = isset( $pagination['pageSize']  ) ? intval($pagination['pageSize'])  : 10;
            $this->db->page($page, $pageSize);
        } elseif ( isset($pagination['limit']) ) {
            $this->db->limit( intval($pagination['limit']) );
        }
        return $this;
    }
}