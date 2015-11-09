<?php
class CommonHelper {
    public static function requiredir($dir) //TODO: require dir recursively
    {
        if (file_exists($dir) && $handle = opendir($dir))
        {
            while(false !== ($file = readdir($handle)))
            {
                if(0===substr_compare($file, ".php", strrpos($file,'.php')))
                {
                    //echo $dir.DS.$file."\n";
                    require_once($dir.'/'.$file);
                }
            }
            closedir($handle);
        }
    }
}

