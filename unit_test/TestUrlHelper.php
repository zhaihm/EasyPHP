<?php
require_once dirname(__FILE__)."/../init.php";

class TestUrlHelper {
    public static function DoTest($argc, $argv, $commands, $common_params=array()) {
        if ($argc == 2) {
            if (@$argv[1] == 'all') {
                foreach ($commands as $key => $value) {
                    self::Execute($commands[$key], $common_params);
                }
            } else if (in_array(@$argv[1], array_keys($commands))) {
                self::Execute($commands[$argv[1]], $common_params);
            } else {
                print("command not found\n");
            }
        } else {
            $indent = "                     ";
            print("Usage: ".$argv[0]. " \n$indent".implode("\n$indent",array_keys($commands))."\n\n");
        }
    }

    private static function Execute($info, $common_params) {
        foreach ($info['params'] as $param) {
            $attributes = array();
            $param += $common_params;
            foreach ($param as $key => $value) {
                $attributes[] = $key . '=' . urlencode($value);
            }
            $url = $info['url'].'?'. implode($attributes, '&');
            $html = CHttp::GetRequest($url, 'http', 10);

            $json = json_decode($html);
            //if (!isset($json) || $json->{'code'} != 0) {
                echo $info['url']."\n".$html."\n\n";
            //}
        }
    }
}

