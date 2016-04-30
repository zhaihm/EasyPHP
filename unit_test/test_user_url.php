<?php
require_once("TestUrlHelper.php");

$commands = array(
    'login' => array(
        'url'=>'http://127.0.0.1:8008/EasyPHP/User/Login',
        'params'=>array(array('account'=>'test', 'password'=>'123456'),
                        array('account'=>'test2', 'password'=>'123456'))
    ),
);

TestUrlHelper::DoTest($argc, $argv, $commands);
