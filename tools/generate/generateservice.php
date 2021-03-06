<?php
function DoGenerateService($database, $tableinfos, $path)
{
    $classname = GetClassName($database)."Service";

    $phpcontent = "<?php\n/* This file is automatically generated, DO NOT modify */\nclass $classname\n{\n";

    foreach($tableinfos as $key => $info)
    {
        $primarykey = &$info['primarykey'];
        $nameinmethod = ucwords(GetShortTableName($info['tablename']));
        $tablename = $info['tablename'];

        $phpcontent .= "\tpublic static function Query{$nameinmethod}ById(\$id) {\n".
                    "\t\tMedooManager::setDbConfig(ConfigManager::\$db_config);\n".
                    "\t\t\$ret = MedooManager::queryOne('$database','$tablename',array('{$primarykey}'=>\$id));\n".
                    "\t\treturn \$ret;\n\t}\n".

                    "\tpublic static function Query{$nameinmethod}ByIdList(\$id) {\n".
                    "\t\tMedooManager::setDbConfig(ConfigManager::\$db_config);\n".
                    "\t\tif (!is_array(\$id)) {\n\t\t\t\$id = array(\$id);\n\t\t}\n".
                    "\t\treturn MedooManager::query('$database','$tablename',array('{$primarykey}'=>\$id));\n\t}\n".

                    "\tpublic static function Query{$nameinmethod}(\$conditions) {\n".
                    "\t\tMedooManager::setDbConfig(ConfigManager::\$db_config);\n".
                    "\t\treturn MedooManager::query('$database','$tablename',\$conditions);\n\t}\n".

                    "\tpublic static function QueryOne{$nameinmethod}(\$conditions) {\n".
                    "\t\tMedooManager::setDbConfig(ConfigManager::\$db_config);\n".
                    "\t\treturn MedooManager::queryOne('$database','$tablename',\$conditions);\n\t}\n".

                    "\tpublic static function Add{$nameinmethod}(\$params) {\n".
                    "\t\tMedooManager::setDbConfig(ConfigManager::\$db_config);\n".
                    "\t\treturn MedooManager::insert('$database','$tablename',\$params);\n\t}\n".

                    "\tpublic static function Update{$nameinmethod}ById(\$id, \$params) {\n".
                    "\t\tMedooManager::setDbConfig(ConfigManager::\$db_config);\n".
                    "\t\tif (!is_array(\$id)) {\n\t\t\t\$id = array(\$id);\n\t\t}\n".
                    "\t\treturn MedooManager::update('$database','$tablename',\$params,array('{$primarykey}'=>\$id));\n\t}\n".

                    "\tpublic static function Delete{$nameinmethod}ById(\$id) {\n".
                    "\t\tMedooManager::setDbConfig(ConfigManager::\$db_config);\n".
                    "\t\tif (!is_array(\$id)) {\n\t\t\t\$id = array(\$id);\n\t\t}\n".
                    "\t\treturn MedooManager::delete('$database','$tablename',array('{$primarykey}'=>\$id));\n\t}\n".

                    "\tpublic static function Count{$nameinmethod}(\$conditions=array()) {\n".
                    "\t\tMedooManager::setDbConfig(ConfigManager::\$db_config);\n".
                    "\t\treturn MedooManager::count('$database','$tablename',\$conditions);\n\t}\n".

                    "\tpublic static function CountAndQuery{$nameinmethod}(\$conditions=array()) {\n".
                    "\t\tMedooManager::setDbConfig(ConfigManager::\$db_config);\n".
                    "\t\treturn MedooManager::countAndQuery('$database','$tablename',\$conditions);\n\t}\n".

                    "\tpublic static function Sum{$nameinmethod}(\$column, \$conditions=array()) {\n".
                    "\t\tMedooManager::setDbConfig(ConfigManager::\$db_config);\n".
                    "\t\treturn MedooManager::sum('$database','$tablename',\$column,\$conditions);\n\t}\n".

                    "\tpublic static function AddOrUpdate{$nameinmethod}(\$params,\$conditions) {\n".
                    "\t\tMedooManager::setDbConfig(ConfigManager::\$db_config);\n".
                    "\t\t\$ret = self::Query{$nameinmethod}(\$conditions);\n".
                    "\t\tif(empty(\$ret)) {\n\t\t\treturn self::Add{$nameinmethod}(array_merge(\$params,\$conditions));\n\t\t}\n".
                    "\t\t\$info = \$ret[0];\n\t\t\$to_update = false;\n".
                    "\t\tforeach(\$params as \$key => \$value) {\n".
                    "\t\t\tif (\$info[\$key] != \$value){\n\t\t\t\t\$to_update = true;\n\t\t\t\tbreak;\n\t\t\t}\n\t\t}\n".
                    "\t\tif(\$to_update){\n\t\t\treturn self::Update{$nameinmethod}(\$params,\$conditions);\n\t\t}\n".
                    "\t\treturn true;\n\t}\n";
    }
    $phpcontent  .= "}";

    $newfile = $path.DS."{$classname}.php";
    FileHelper::FilePutContents ($newfile, $phpcontent,FILE_USE_INCLUDE_PATH);
    echo "generate $newfile\n";
}
