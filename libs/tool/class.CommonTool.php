<?php
class CommonTool {
    public static function isSuccessRet($ret) {
        return $ret['result'] == 'success';
    }

    public static function isFailRet($ret) {
        return $ret['result'] == 'fail';
    }

    public static function failResult($reason) {
        return array (
            'result' => 'fail',
            'reason' => $reason
        );
    }

    public static function failCodeResult($code, $reason) {
        return array (
            'result' => 'fail',
            'code' => $code,
            'reason' => $reason
        );
    }

    public static function successResult($key = null, $value = null) {
        $base = array (
            'result' => 'success',
        );

        if (is_array($key)) {
            return array_merge($base, $key);
        } else if (is_scalar($key)) {
            $base[$key] = $value;
        }

        return $base;
    }

    public static function anyEmpty() {
        $args = func_get_args();
        foreach ($args as $arg) {
            if (empty($arg)) {
                return true;
            }
        }
        return false;
    }

    public static function allEmpty() {
        $args = func_get_args();
        foreach ($args as $arg) {
            if (!empty($arg)) {
                return false;
            }
        }
        return true;
    }

    public static function getCurl($timeout = 5, $setopt = array(), $forceNew = false) {
        static $curl = null;
        if (empty($curl) || $forceNew === true) {
            $setopt['timeOut'] = $timeout;
            $setopt['ssl'] = true;
            $curl = new CurlTool($setopt);
            $headers = array (
                'Connection: keep-alive',
                'Cache-Control: max-age=0',
                'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.153 Safari/537.36',
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Encoding: gzip,deflate,sdch',
                'Accept-Language: zh-CN,zh;q=0.8,de;q=0.6,en;q=0.4,es;q=0.2,nl;q=0.2,pt;q=0.2,ru;q=0.2,zh-TW;q=0.2,fr;q=0.2,ja;q=0.2'
            );
            $curl->setCurlOpt(CURLOPT_HTTPHEADER, $headers);
        }

        return $curl;
    }

    public static function safeArrayMerge($arr1, $arr2) {
        $arr1 = is_array($arr1) ? $arr1 : [];
        $arr2 = is_array($arr2) ? $arr2 : [];
        return array_merge($arr1, $arr2);
    }

    /**
     * 导出Excel(csv)文件
     * @param array $data 要导出的数据
     * @param array $headList 第一行列名
     * @param string $fileName 输出Excel表格文件名
     * @param string $exportUrl 直接输出到浏览器 or 输出到指定路径文件下
     * @return string
     */
    public static function exportCsv(array $data, array $headList, $fileName = '', $exportUrl = 'php://output')
    {
        set_time_limit(0);// 取消脚本运行时间的限制
        ini_set('memory_limit', '256M');// 设置php内存限制

        $fileName = empty($fileName) ? date('YmdHis') : $fileName;
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $fileName . '.csv"');
        header('Cache-Control: max-age=0');

        // 打开PHP文件句柄,php://output 表示直接输出到浏览器
        $fp = fopen($exportUrl, 'w+');

        // 输出Excel列名信息
        foreach ($headList as $key => $value) {
            // CSV的Excel支持GBK编码，一定要转换，否则乱码
            try {
                $headList[$key] = iconv('utf-8', 'gbk', $value);
            } catch (Exception $e) {
                $headList[$key] = mb_convert_encoding($value, "GBK", "UTF-8");
            }
        }

        // 将数据通过fputcsv写到文件句柄
        fputcsv($fp, $headList);

        // 计数器
        $num = 0;
        // 每隔$limit行，刷新一下输出buffer，不要太大，也不要太小，大数据量时处理
        $limit = 100000;
        // 逐行取出数据，不浪费内存
        $count = count($data);

        for ($i = 0; $i < $count; $i++) {
            $num++;

            // 刷新一下输出buffer，防止由于数据过多造成问题
            if ($limit == $num) {
                ob_flush();
                flush();// 刷新buffer
                $num = 0;
            }

            $row = $data[$i];
            foreach ($row as $k => $v) {
                try {
                    $row[$k] = iconv('utf-8', 'gbk', $v);
                } catch (Exception $e) {
                    $row[$k] = mb_convert_encoding($v, "GBK", "UTF-8");
                }
            }
            fputcsv($fp, $row);
        }
        return $fileName;
    }
}