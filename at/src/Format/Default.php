<?php
class YC_ATM_Template_Format_Default{

    public function money($amount) {
        return round($amount);
    }

    public function moneyfromcent($amount) {
        return round($amount / 100);
    }

    public function date($timestamp) {
        return date("Y-m-d", $timestamp);
    }

    public function datetime($timestamp) {
        return date("Y-m-d H:i", $timestamp);
    }
    public function time($timestamp) {
        return date("H:i", $timestamp); 
    }

    public function timelength($seconds) {
        $hours = floor($seconds / 3600);
        $minutes = ceil(($seconds % 3600) / 60);
        $ret = '';
        if($hours > 0) {
            $ret = $hours . "小时";
        }
        if($minutes > 0) {
            $ret .= $minutes . "分钟";
        }
        if($ret == "") {
            $ret = "0小时";
        }
        return $ret;
    }

    public function km($meters) {
        return rtrim($meters / 1000, '0');
    }

}
