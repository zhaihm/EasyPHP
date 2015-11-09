<?php

function GetPrimaryKey($fieldset) {
    foreach($fieldset as $key => $info) {
        if($info['Key'] == 'PRI') {
            return $info['Field'];
        }
    }
    return "idx";
}
function GetShortTableName($tablename) {
    $table = strtolower($tablename);
    if(0===substr_compare($table,'tb_',0,3)){
        $shorttable = substr($table,3);
    }else if(0===substr_compare($table,'_t',strlen($table)-3,2)){
        $shorttable = substr($table,0,strlen($table)-2);
    }else{
        $shorttable = $table;
    }
    return $shorttable;
}
function GetClassName($database) {
    $pos = strpos($database, '_');
    if ($pos === false) {
        $classname = ucwords($database);
    } else {
        $classname = ucwords(substr($database, 0, $pos)).ucwords(substr($database, $pos+1));
    }
    return $classname;
}