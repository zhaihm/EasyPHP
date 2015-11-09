<?php

function DoGenerateController($dbname,$table,$fields,$path,$primarykey,$filterfields=array("idx","createtime"))
{
    $paramlist = "\t\t\$params = array();\n";
    foreach($fields as $key => $fieldname)
    {
        if(in_array($fieldname,$filterfields)){continue;}
        $paramlist .= "\t\t".'$params[\''.$fieldname.'\'] = CHttpRequestInfo::Get(\''.$fieldname.'\');'."\n";
    }

    $serviceclass = GetClassName($dbname);
    $shorttable = GetShortTableName($table);

    $listmethod = "\tpublic function actionIndex()\n\t{\n".
                $paramlist .
                "\t\t\$startno = CHttpRequestInfo::Get('startno',0);\n".
                "\t\t\$limit = CHttpRequestInfo::Get('limit',0);\n".
                "\n".
                "\t\tforeach(\$params as \$key=>&\$value){\n".
                "\t\t\ttrim(\$value);\n".
                "\t\t\tif(\$value === ''){unset(\$params[\$key]);}\n\t\t}\n\n".
                "\t\t\$list = ".ucfirst($serviceclass)."Service::Search".ucfirst($shorttable)."(\$params,\$startno,\$limit);\n".
                "\t\t\$this->render('{$shorttable}list',array('searchvalues'=>\$params,'list'=>\$list));\n".
                "\t}\n";

    $addpage = "\tpublic function actionAdd".ucfirst($shorttable)."Page()\n\t{\n".
                "\t\t\$this->renderPartial('add{$shorttable}');\n".
                "\t}\n";

    $updatepage = "\tpublic function actionUpdate".ucfirst($shorttable)."Page()\n\t{\n".
                "\t\t\$id = CHttpRequestInfo::Get('{$primarykey}');\n" .
                "\t\t\$info = ".ucfirst($serviceclass)."Service::Get".ucfirst($shorttable)."By{$primarykey}(\$id);\n".
                "\t\t\$this->renderPartial('edit{$shorttable}',array('info'=>\$info));\n".
                "\t}\n";

    $addmethod = "\tpublic function actionAdd".ucfirst($shorttable)."()\n\t{\n".
                $paramlist."\n" .
                "\t\tforeach(\$params as \$key=>&\$value){\n".
                    "\t\t\ttrim(\$value);\n".
                    "\t\t\tif(\$value === ''){\n".
                        "\t\t\t\tOutputManager::output(array('code'=>-1,'message'=>\$key.' cannot be empty'),'json');\n".
                        "\t\t\t\treturn;\n".
                    "\t\t\t}\n".
                "\t\t}\n".
                "\t\t\$params['createtime'] = date('Y-m-d H:i:s');\n\n".
                "\t\tif(false === ".ucfirst($serviceclass)."Service::Add".ucfirst($shorttable)."(\$params))\n\t\t{\n".
                "\t\t\tOutputManager::output(array('code'=>-1,'message'=>'add {$shorttable} failed'),'json');\n".
                "\t\t\treturn;\n\t\t}\n".
                "\t\tOutputManager::output(array('code'=>0,'message'=>'success'),'json');\n".
                "\t}\n";

    $updatemethod = "\tpublic function actionUpdate".ucfirst($shorttable)."()\n\t{\n" .
                "\t\t\$conditions['{$primarykey}'] = CHttpRequestInfo::Get('{$primarykey}');\n" .
                "\t\tif(empty(\$conditions['{$primarykey}']))\n\t\t{\n" .
                "\t\t\tOutputManager::output(array('code'=>-11,'message'=>'{$primarykey} is empty'),'json');\n" .
                "\t\t\treturn;\n\t\t}\n" .
                $paramlist."\n" .
                "\t\tforeach(\$params as \$key=>&\$value){\n".
                    "\t\t\ttrim(\$value);\n".
                    "\t\t\tif(\$value === ''){\n".
                        "\t\t\t\tunset(\$params[\$key]);\n".
                    "\t\t\t}\n".
                "\t\t}\n".
                "\t\tif (empty(\$params)) {\n".
                    "\t\t\tOutputManager::output(array('code'=>-1,'message'=>'no fields to update'),'json');\n".
                    "\t\t\treturn;\n".
                "\t\t}\n".
                "\t\tif(false === ".ucfirst($serviceclass)."Service::Update".ucfirst($shorttable)."(\$params,\$conditions))\n\t\t{\n".
                "\t\t\tOutputManager::output(array('code'=>-1,'message'=>'update {$shorttable} failed'),'json');\n".
                "\t\t\treturn;\n\t\t}\n" .
                "\t\tOutputManager::output(array('code'=>0,'message'=>'success'),'json');\n" .
                "\t}\n";

    $deletemethod = "\tpublic function actionDelete".ucfirst($shorttable)."()\n\t{\n" .
                "\t\t\$conditions['{$primarykey}'] = CHttpRequestInfo::Get('{$primarykey}');\n" .
                "\t\tif(empty(\$conditions['{$primarykey}']))\n\t\t{\n" .
                "\t\t\tOutputManager::output(array('code'=>-11,'message'=>'{$primarykey} is empty'),'json');\n" .
                "\t\t\treturn;\n\t\t}\n" .
                "\t\t\$params = array();\n".
                "\t\t\$params['status'] = '0';\n".
                "\t\tif(false === ".ucfirst($serviceclass)."Service::Update".ucfirst($shorttable)."(\$params, \$conditions))\n\t\t{\n".
                "\t\t\tOutputManager::output(array('code'=>-1,'message'=>'delete {$shorttable} failed'),'json');\n".
                "\t\t\treturn;\n\t\t}\n" .
                "\t\tOutputManager::output(array('code'=>0,'message'=>'success'),'json');\n" .
                "\t}\n";

    $queryinfo = "\tpublic function actionQuery".ucfirst($shorttable)."()\n\t{\n" .
                "\t\t\$$primarykey = CHttpRequestInfo::Get('{$primarykey}');\n" .
                "\t\tif(empty(\$$primarykey))\n\t\t{\n" .
                "\t\t\tOutputManager::output(array('code'=>-11,'message'=>'{$primarykey} is empty'),'json');\n" .
                "\t\t\treturn;\n\t\t}\n" .
                "\t\t\$info = ".ucfirst($serviceclass)."Service::Get".ucfirst($shorttable)."By{$primarykey}(\$$primarykey);\n".
                "\t\tOutputManager::output(array('code'=>0,'message'=>'success','data'=>\$info),'json');\n" .
                "\t}\n";
/*
    $deletemethod = "\tpublic function actionDelete".ucfirst($shorttable)."()\n\t{\n" .
                "\t\t\$conditions['{$primarykey}'] = CHttpRequestInfo::Get('{$primarykey}');\n" .
                "\t\tif(empty(\$conditions['{$primarykey}']))\n\t\t{\n" .
                "\t\t\tOutputManager::output(array('code'=>-11,'message'=>'{$primarykey} is empty'),'json');\n" .
                "\t\t\treturn;\n\t\t}\n" .
                "\t\tif(false === ".ucfirst($serviceclass)."Service::Delete".ucfirst($shorttable)."(\$conditions))\n\t\t{\n".
                "\t\t\tOutputManager::output(array('code'=>-1,'message'=>'delete {$shorttable} failed'),'json');\n".
                "\t\t\treturn;\n\t\t}\n" .
                "\t\tOutputManager::output(array('code'=>0,'message'=>'success'),'json');\n" .
                "\t}\n";
*/
    $php  =  "<?php\n".
             "class ".ucfirst($shorttable)."Controller extends MyController\n{\n".
             "{$listmethod}\n\n{$addpage}\n\n{$updatepage}\n\n{$addmethod}\n\n{$updatemethod}\n\n{$deletemethod}\n\n{$queryinfo}\n\n".
              "}";
    $newfile = $path.DS.ucfirst($shorttable)."Controller.php";
    FileHelper::FilePutContents($newfile,$php,FILE_USE_INCLUDE_PATH);
    echo "generate $newfile\n";
}