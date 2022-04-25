<?php
class ConfigTool {
    private static $config;
    public function __construct() {
        self::initConfig();
    }

    private static function initConfig() {
        $config = include(dirname(dirname(dirname(__FILE__))) . '/config/config.php');
        self::$config = $config['app'];
    }

    public static function C($configName) {
        self::initConfig();
        return self::$config[$configName];
    }
}