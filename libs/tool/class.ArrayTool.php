<?php
class ArrayTool {
    public static function getSub(&$array, $keys) {
        if (empty($array)) {
            return [];
        }

        if (is_string($keys)) {
            $keys = explode(',', $keys);
        }
        $kc = count($keys);
        $key = $keys[0];

        $ret = array ();
        foreach ($array as $arr) {
            if ($kc === 1) {
                $ret[] = $arr[$key];
            } else {
                $temp = array ();
                foreach ($keys as $k) {
                    $temp[$k] = $arr[$k];
                }
                $ret[] = $temp;
            }
        }

        return $ret;
    }

    public static function changeKey(&$array, $keyColumn, $forceTwoLevel = false) {
        if (empty($array)) {
            return [];
        }

        $hasTwoLevel = false;
        if ($forceTwoLevel) {
            $hasTwoLevel = true;
        } else {
            $allKeys = array ();
            foreach ($array as $arr) {
                $key = $arr[$keyColumn];
                if (in_array($key, $allKeys)) {
                    $hasTwoLevel = true;
                    break;
                }
                $allKeys[] = $key;
            }
        }

        $ret = array ();
        foreach ($array as $arr) {
            $key = $arr[$keyColumn];
            if ($hasTwoLevel) {
                $ret[$key][] = $arr;
            } else {
                $ret[$key] = $arr;
            }
        }

        return $ret;
    }

    public static function changeKeyRow(&$array, $keyColumn, $saveFirst = true) {
        $ret = array ();
        if (empty($array)) {
            return [];
        }

        foreach ($array as $arr) {
            $key = $arr[$keyColumn];
            if (isset($ret[$key]) && $saveFirst) {
                continue;
            }
            $ret[$key] = $arr;
        }

        return $ret;
    }

    public static function mapNameValue($array, $keyField, $valueField) {
        if (empty($array)) {
            return [];
        }

        $ret = array();
        foreach ($array as $a) {
            $ret[$a[$keyField]] = $a[$valueField];
        }
        return $ret;
    }

    public static function mapNameValues($array, $keyField, $valueFields) {
        $ret = array();
        foreach ($array as $a) {
            $values = array();
            foreach ($valueFields as $valueField){
                $values[$valueField] = $a[$valueField];
            }
            $ret[$a[$keyField]] = $values;
        }
        return $ret;
    }
}