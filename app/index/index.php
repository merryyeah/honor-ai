<?php
class index extends base {
    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->render(array(), 'index/index/index');
    }
}