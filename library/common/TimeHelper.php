<?php
class TimeHelper {
    public static function GetDate($time, $dstzone, $srczone='Asia/Shanghai') {
        date_default_timezone_set($dstzone);
        $date = date('Y-m-d', $time);
        date_default_timezone_set('Asia/Shanghai');
        return $date;
   }
}