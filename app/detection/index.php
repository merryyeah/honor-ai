<?php
class index extends base {
    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $isNg = $_GET['isNg'] ? : 0;
        $data = array(
            array(
                'serialNo' => 1,
                'action' => '抽烟',
                'addr' => '在办公室',
                'imageUrl' => 'image_url.jpg',
                'videoUrl' => 'http://39.101.197.161/rtsp-hls',
                'status' => 'NG',
                'ip' => '127.0.0.1',
                'gmtCreate' => date('Y-m-d H:i:s'),
            ),
            array(
                'serialNo' => 2,
                'action' => '喝酒',
                'addr' => '在路边',
                'imageUrl' => 'image_url.jpg',
                'videoUrl' => 'http://39.101.197.161/rtsp-hls',
                'status' => 'NG',
                'ip' => '127.0.0.1',
                'gmtCreate' => date('Y-m-d H:i:s'),
            ),
            array(
                'serialNo' => 3,
                'action' => '打游戏',
                'addr' => '在办公室',
                'imageUrl' => 'image_url.jpg',
                'videoUrl' => 'http://39.101.197.161/rtsp-hls',
                'status' => 'NG',
                'ip' => '127.0.0.1',
                'gmtCreate' => date('Y-m-d H:i:s'),
            ),
            array(
                'serialNo' => 4,
                'action' => '不在工位',
                'addr' => '在街道口',
                'imageUrl' => 'image_url.jpg',
                'videoUrl' => 'http://39.101.197.161/rtsp-hls',
                'status' => 'NG',
                'ip' => '127.0.0.1',
                'gmtCreate' => date('Y-m-d H:i:s'),
            ),
            array(
                'serialNo' => 5,
                'action' => '聊天',
                'addr' => '在办公室',
                'imageUrl' => 'image_url.jpg',
                'videoUrl' => 'http://39.101.197.161/rtsp-hls',
                'status' => 'NG',
                'ip' => '127.0.0.1',
                'gmtCreate' => date('Y-m-d H:i:s'),
            ),
            array(
                'serialNo' => 6,
                'action' => '工作',
                'addr' => '在办公室',
                'imageUrl' => 'image_url.jpg',
                'videoUrl' => 'http://39.101.197.161/rtsp-hls',
                'status' => 'OK',
                'ip' => '127.0.0.1',
                'gmtCreate' => date('Y-m-d H:i:s'),
            ),
        );

        if ($isNg) {
            $data[] = array(
                'serialNo' => time(),
                'action' => '工作',
                'addr' => '在办公室',
                'imageUrl' => 'image_url.jpg',
                'videoUrl' => 'http://39.101.197.161/rtsp-hls',
                'status' => 'NG',
                'ip' => '127.0.0.1',
                'gmtCreate' => date('Y-m-d H:i:s'),
            );
        }

        return $this->renderJSON(CommonTool::successResult(array(
            'data' => $data
        )));
    }
}