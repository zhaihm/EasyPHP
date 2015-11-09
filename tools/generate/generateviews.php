<?php
function DoGenerateView($tableinfo, $viewpath)
{
    $dir = $viewpath.DS.$tableinfo["shorttable"];    if((!file_exists($dir)) || (!is_dir($dir))) {
        mkdir($dir, 0775);
    }

    $controller = new CBaseController();
    $content =  $controller->renderPartial(ROOT.DS."template".DS."list.php", $tableinfo, true);
    $newfile = $dir.DS.$tableinfo["shorttable"]."list.php";
    FileHelper::FilePutContents($newfile,$content,FILE_USE_INCLUDE_PATH);
    echo "generate $newfile\n";

    $content =  $controller->renderPartial(ROOT.DS."template".DS."add.php", $tableinfo, true);
    $newfile = $dir.DS.'add'.$tableinfo["shorttable"].".php";
    FileHelper::FilePutContents($newfile,$content,FILE_USE_INCLUDE_PATH);
    echo "generate $newfile\n";


    $content =  $controller->renderPartial(ROOT.DS."template".DS."edit.php", $tableinfo, true);
    $newfile = $dir.DS.'edit'.$tableinfo["shorttable"].".php";
    FileHelper::FilePutContents($newfile,$content,FILE_USE_INCLUDE_PATH);
    echo "generate $newfile\n";
}
