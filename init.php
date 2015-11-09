<?php

define( 'DS', DIRECTORY_SEPARATOR );
define( 'ROOT', dirname( __FILE__ )) ;
define( 'WEBNAME', substr(ROOT, strrpos(ROOT, DS) + 1));
define( 'LIBRARY_DIR', ROOT.DS.'library') ;

function requiredir($dir)
{
    if (file_exists($dir) && $handle = opendir($dir))
    {
        while(false !== ($file = readdir($handle)))
        {
            if(0===substr_compare($file, ".php", strrpos($file,'.php')))
            {
                //echo $dir.DS.$file."\n";
                require_once($dir.DS.$file);
            }
        }
        closedir($handle);
    }
}

requiredir(LIBRARY_DIR.DS."common");
requiredir(LIBRARY_DIR.DS."base");
requiredir(LIBRARY_DIR.DS."http");
requiredir(LIBRARY_DIR.DS."phpmailer");
requiredir(LIBRARY_DIR.DS."SMSVerifyCode");
requiredir(LIBRARY_DIR.DS."library");

requiredir(ROOT.DS."service");
requiredir(ROOT.DS."service".DS."dataservice");

require_once(ROOT.DS."config".DS."main.php");


