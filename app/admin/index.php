<?php
class index extends base {
    public function __construct() {
        parent::__construct(true);
    }

    public function index() {
        $this->render(array('test' => '123'), 'admin/index/index');
    }
}
