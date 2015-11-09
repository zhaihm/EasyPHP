<?php
class FileHelper
{
    public static function FilePutContents($filename, $content)
    {
        self::MakeDir(dirname($filename));
        file_put_contents($filename, $content);
    }
    public static function MakeDir($dir) {
        if (!is_dir(dirname($dir))) {
            self::MakeDir(dirname($dir));
        }
        if (!is_dir($dir)) {
            mkdir($dir, 0755);
        }
    }
    public static function Remove($srcfile, $destfile)
    {
        $pos = strrpos($file, '/');
        self::MakeDir(substr($destfile, 0, $pos));
        rename($destfile, $srcfile);
    }
}