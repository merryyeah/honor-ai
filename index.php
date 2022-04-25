<?php
initAutoLoad();
define('APP_PATH', dirname(__FILE__));
session_start();

header('Access-Control-Allow-Credentials: true');
header('Access-Control-Expose-Headers: Content-Disposition');
header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
header('Access-Control-Allow-Headers: Authorization, Content-Type, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, X-Requested-With');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE');

$requestUri = $_SERVER['REQUEST_URI'];
list($route, $queryStr) = explode('?', $requestUri);
list($module, $class, $action) = explode('/', trim($route, '/'));
$module = strtolower($module ? : 'index');
$class = strtolower($class ? : 'index');
$action = strtolower($action ? : 'index');

$classUrl = sprintf('./app/%s/%s.php', $module, $class);
include $classUrl;
$obj = new $class();
$obj->$action();

function initAutoLoad() {
    spl_autoload_register("autoload");
}

function autoLoad($class) {
    $config = include('./config/config.php');
    $autoLoadDirs = $config['autoLoadDirs'];
    foreach ($autoLoadDirs as $autoLoadDir) {
        $classFile = $autoLoadDir . 'class.' . $class . '.php';
        if (file_exists($classFile)) {
            include_once $classFile;
            continue;
        }

        $classFile = $autoLoadDir . $class . '.class.php';
        if (file_exists($classFile)) {
            include_once $classFile;
            continue;
        }

        $classFile = $autoLoadDir . $class . '.php';
        if (file_exists($classFile)) {
            include_once $classFile;
        }
    }
}