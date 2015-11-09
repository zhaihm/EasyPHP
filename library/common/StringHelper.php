<?php
class StringHelper
{
    public static function GetSubContent($html, $beginstr, $endstr, &$pos = 0) {
        $begin = strpos($html, $beginstr, $pos);
        if ($begin === false) {
            return false;
        }
        $begin += strlen($beginstr);
        $end = strpos($html, $endstr, $begin);
        if ($end === false || $end <= $begin) {
            return false;
        }
        $pos = $end + strlen($endstr);
        return substr($html, $begin, $end - $begin);
    }
    public static function GetFileName($format) {
        return strftime($format, time());
    }

}