<?php
function DoGenerateDao($database, $tables, $path)
{
    $classname = GetClassName($database)."Db";
    $phpcontent = "<?php\nclass $classname extends CBaseMysqlDb\n{\n".
            "\tprivate static \$instance = null;\n\n".
            "\tpublic static function GetInstance()\n\t{\n".
            "\t\tif(self::\$instance == null){self::\$instance = new {$classname}();}\n".
            "\t\treturn self::\$instance;\n\t}\n".

            "\tpublic function __construct()\n\t{\n".
            "\t\tparent::__construct(ConfigManager::\$config['params']['database']['$database']['host'],\n".
            "\t\t\t\tConfigManager::\$config['params']['database']['$database']['user'],\n".
            "\t\t\t\tConfigManager::\$config['params']['database']['$database']['password'],\n".
            "\t\t\t\tConfigManager::\$config['params']['database']['$database']['database']);\n\t}\n\n";

    foreach($tables as $key => $tablename)
    {
        $nameinmethod = ucwords(GetShortTableName($tablename));

        $phpcontent .= "\tpublic function query{$nameinmethod}(\$conditions,\$start=0,\$num=0,\$ex='')\n\t{\n".
                "\t\t\$ex .= (\$start==0 && \$num==0) ? '' : \" limit {\$start},{\$num}\";\n".
                "\t\treturn \$this->queryByArray('{$tablename}',\$conditions,\$ex);\n\t}\n".

                "\tpublic function queryRecent{$nameinmethod}(\$params)\n\t{\n".
                "\t\treturn \$this->queryByArray('{$tablename}',\$params, ' order by createtime desc limit 1');\n\t}\n".

                "\tpublic function add{$nameinmethod}(\$params)\n\t{\n".
                "\t\treturn \$this->addArray('{$tablename}',\$params);\n\t}\n".

                "\tpublic function update{$nameinmethod}(\$params,\$conditions)\n\t{\n".
                "\t\treturn \$this->updateArray('{$tablename}',\$params,\$conditions);\n\t}\n".

                "\tpublic function delete{$nameinmethod}(\$conditions)\n\t{\n".
                "\t\treturn \$this->deleteByArray('{$tablename}',\$conditions);\n\t}\n".

                "\tpublic function count{$nameinmethod}(\$conditions)\n\t{\n".
                "\t\treturn \$this->countByArray('{$tablename}',\$conditions);\n\t}\n".

                "\tpublic function search{$nameinmethod}(\$conditions,\$start=0,\$num=0,\$ex='')\n\t{\n".
                "\t\t\$ex .= (\$start==0 && \$num==0) ? '' : \" limit {\$start},{\$num}\";\n".
                "\t\treturn \$this->queryLikeByArray('{$tablename}',\$conditions,\$ex);\n\t}\n\n";
    }
    $phpcontent  .= "}";

    $newfile = $path.DS."{$classname}.php";
    FileHelper::FilePutContents ($newfile, $phpcontent,FILE_USE_INCLUDE_PATH);
    echo "generate $newfile\n";
}
