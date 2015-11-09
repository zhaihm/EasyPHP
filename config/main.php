<?php
date_default_timezone_set("Asia/Shanghai");
error_reporting(E_ALL);
define('__MY_ENV', 'DEV');
define('LOG_DIR', ROOT . DS . '..' . DS . 'log' . DS . WEBNAME . '_business');

class ConfigManager
{
    public static $baseurl = '';
    public static $config = array();
    public static $db_config = array();
}

ConfigManager::$db_config = array(
    'ep_user' => array(
        'database_name' => 'ep_user',
        'database_type' => 'mysql',
        'server' => '127.0.0.1',
        'username' => 'root',
        'password' => 'root',
        'charset' => 'utf8',
        'engine' => 'MyISAM',
        'option' => array(PDO::MYSQL_ATTR_FOUND_ROWS=>TRUE),
    ),
);

ConfigManager::$config = array(
    'params' => array(
        'sessiontype' => 'file',//'file',

        'language_mapping' => array('zh-cn'=>'cn', 'zh-tw'=>'tw', 'en-us'=>'en'),

        'logconfigs' => Array(
            'all' => Array(
                'dir' => LOG_DIR,
                'filename' => 'all.log',
                'levels' => Array('error' => 100, 'warning' => 100, 'info' => 100, 'debug' => 100),
                'timeLevel' => 1,
            ),
            'interface' => Array(
                'dir' => LOG_DIR,
                'filename' => 'interface.log',
                'levels' => Array('error' => 100, 'warning' => 100, 'info' => 100, 'debug' => 100),
                'timeLevel' => 0,
            ),
            'dbinfo' => Array(
                'dir' => LOG_DIR,
                'filename' => 'dbinfo.log',
                'levels' => Array('error' => 100, 'warning' => 100, 'info' => 100, 'debug' => 100),
                'timeLevel' => 0,
            ),
            'monitor' => Array(
                'dir' => LOG_DIR,
                'filename' => 'monitor.log',
                'levels' => Array('error' => 100, 'warning' => 100, 'info' => 100, 'debug' => 100),
                'timeLevel' => 0,
            ),
        ),

        'memcache_addrs' => array(
            1 => array(
                'ip' => '127.0.0.1',
                'port' => 11211,
                ),
                /*
            2 => array(
                'ip' => '127.0.0.1',
                'port' => 11215,
            ),*/
        ),
    ),
);

