<?php
require_once dirname(__FILE__).'/medoo.php';

/*
    base functions: query, insert, update, delete, count, sum
*/
class MedooManager {
    private static $default_db_config = array(
        'database_type' => 'mysql',
        'server' => '127.0.0.1',
        'username' => 'root',
        'password' => 'root',
        'charset' => 'utf8',
        'engine' => 'MyISAM',
        'option' => array(PDO::MYSQL_ATTR_FOUND_ROWS=>TRUE),
    );
    private static $db_config = array();
    private static $dbs = array();

    public static function setDbConfig($db_config) {
        self::$db_config = $db_config;
    }

    public static function beginTransaction($dbname) {
        self::connect($dbname);
        self::$dbs[$dbname]->pdo->beginTransaction();
    }

    public static function endTransaction($dbname) {
        self::connect($dbname);
        $ret = self::$dbs[$dbname]->pdo->commit();
        if (false === $ret) {
            self::$dbs[$dbname]->pdo->rollBack();
        }
        return $ret;
    }

/*
    Query all columns
*/
    public static function query($dbname, $table, $where) {
        self::connect($dbname);
        self::dealWhere($where);
        if (self::HasEmptyArray($where) ||
            (isset($where['AND']) && self::HasEmptyArray($where['AND'])) ) {
            return array();
        }

        $tBegin = Log::getLogTime();
        $ret = self::$dbs[$dbname]->select($table, '*', $where);
        $tEnd = Log::getLogTime();

        $sql = self::lastSql($dbname);
        if ($ret === false) {
            $error_msg = self::error($dbname);
            self::writeLog('database['.$dbname.']   sql['.$sql.'], error message['.var_export($error_msg,true).']', 'error', $tEnd-$tBegin);
        } else {
            self::writeLog('database['.$dbname.']   sql['.$sql.']    ret['.var_export($ret,true).']', 'info', $tEnd-$tBegin);
        }

        return $ret;
    }

/*
    return one object, instead of array
    $add: insert if record not exist
*/
    public static function queryOne($dbname, $table, $where, $add=false, $params=array()) {
        if ($add===true && empty($params)) {
            throw new Exception('bad parameter', 70007);
        }

        $ret = self::query($dbname, $table, $where);
        if (count($ret) > 1) {
            //throw new Exception('invalid query result set', 70007);
        }

        if (count($ret) === 1) {
            return $ret[0];
        } else if ($add===true) {
            if ('0' === self::insert($dbname, $table, $params)) {
                throw new Exception('insert failed', 70007);
            }
            return $params;
        } else {
            return array();
        }
    }

    public static function countAndQuery($dbname, $table, $where) {
        $count = self::count($dbname, $table, $where);
        if ($count == 0) {
            return array('count'=>0, 'list'=>array());
        }

        $list = self::query($dbname, $table, $where);
        return array('count'=>$count, 'list'=>$list);
    }

    public static function insert($dbname, $table, $datas) {
        self::valid($dbname, $table, $datas);
        self::connect($dbname);

        $tBegin = Log::getLogTime();
        $ret =  self::$dbs[$dbname]->insert($table, $datas);
        $tEnd = Log::getLogTime();

        $sql = self::lastSql($dbname);
        if ($ret === '0') {
            $error_msg = self::error($dbname);
            self::writeLog('database['.$dbname.']   sql['.$sql.'], error message['.var_export($error_msg,true).']', 'error', $tEnd-$tBegin);
        } else {
            self::writeLog('database['.$dbname.']   sql['.$sql.']    ret['.$ret.']', 'info', $tEnd-$tBegin);
        }

        return $ret;
    }

    public static function update($dbname, $table, $data, $where = null) {
        self::valid($dbname, $table, $data);
        self::connect($dbname);
        self::dealWhere($where);

        $tBegin = Log::getLogTime();
        $ret = self::$dbs[$dbname]->update($table, $data, $where);
        $tEnd = Log::getLogTime();

        $sql = self::lastSql($dbname);
        if ($ret === false) {
            $error_msg = self::error($dbname);
            self::writeLog('database['.$dbname.']   sql['.$sql.'], error message['.var_export($error_msg,true).']', 'error', $tEnd-$tBegin);
        } else {
            self::writeLog('database['.$dbname.']   sql['.$sql.']    ret['.$ret.']', 'info', $tEnd-$tBegin);
        }

        return $ret;
    }

    public static function delete($dbname, $table, $where) {
        self::connect($dbname);
        self::dealWhere($where);

        $tBegin = Log::getLogTime();
        $ret = self::$dbs[$dbname]->delete($table, $where);
        $tEnd = Log::getLogTime();

        $sql = self::lastSql($dbname);
        if ($ret === false) {
            $error_msg = self::error($dbname);
            self::writeLog('database['.$dbname.']   sql['.$sql.'], error message['.var_export($error_msg,true).']', 'error', $tEnd-$tBegin);
        } else {
            self::writeLog('database['.$dbname.']   sql['.$sql.']    ret['.$ret.']', 'info', $tEnd-$tBegin);
        }

        return $ret;
    }

    public static function count($dbname, $table, $where) {
        self::connect($dbname);
        self::dealWhere($where);
        if (isset($where['LIMIT'])) {
            unset($where['LIMIT']);
        }
        $tBegin = Log::getLogTime();
        $ret =  self::$dbs[$dbname]->count($table, $where);
        $tEnd = Log::getLogTime();

        $sql = self::lastSql($dbname);
        if ($ret === false) {
            $error_msg = self::error($dbname);
            self::writeLog('database['.$dbname.']   sql['.$sql.'], error message['.var_export($error_msg,true).']', 'error', $tEnd-$tBegin);
        } else {
            self::writeLog('database['.$dbname.']   sql['.$sql.']    ret['.$ret.']', 'info', $tEnd-$tBegin);
        }

        return $ret;
    }

    public static function sum($dbname, $table, $join, $column=null, $where=null) {
        self::connect($dbname);
        self::dealWhere($where);

        $tBegin = Log::getLogTime();
        $ret = self::$dbs[$dbname]->sum($table, $join, $column, $where);
        $tEnd = Log::getLogTime();

        $sql = self::lastSql($dbname);
        if ($ret === false) {
            $error_msg = self::error($dbname);
            self::writeLog('database['.$dbname.']   sql['.$sql.'], error message['.var_export($error_msg,true).']', 'error', $tEnd-$tBegin);
        } else {
            self::writeLog('database['.$dbname.']   sql['.$sql.']    ret['.$ret.']', 'info', $tEnd-$tBegin);
        }

        return $ret;
    }

    public static function log($dbname) {
        self::hasdb($dbname);
        return self::$dbs[$dbname]->log();
    }

    public static function lastSql($dbname) {
        $sql = self::log($dbname);
        return $sql[count($sql)-1];
    }

    public static function error($dbname) {
        self::hasdb($dbname);
        return self::$dbs[$dbname]->error();
    }

    protected static function connect($dbname) {
        self::hasdb($dbname);
        if (!isset(self::$dbs[$dbname])) {
            foreach (self::$default_db_config as $key=>$value) {
                if (!isset(self::$db_config[$dbname][$key])) {
                    self::$db_config[$dbname][$key] = $value;
                }
            }
            self::$dbs[$dbname] = new Medoo(self::$db_config[$dbname]);
        }
    }

    protected static function valid($dbname, $table, $datas) {
        self::hasdb($dbname);
        if (!isset(self::$db_config[$dbname]['tables']) || !isset(self::$db_config[$dbname]['tables'][$table])) {
            return;
        }

        if (!isset($datas[0])) {
            $datas = array($datas);
        }
        $tableConfig = self::$db_config[$dbname]['tables'][$table];
        foreach ($datas as $row) {
            foreach ($row as $field => $value) {
                if (isset($tableConfig[$field]) && is_array($tableConfig[$field])
                    && isset($tableConfig[$field]['valid']) && strlen($tableConfig[$field]['valid'])!=0
                    && !preg_match($tableConfig[$field]['valid'], $value)) {
                    throw new Exception('field valid failed: '.$dbname.'.'.$table.'.'.$field, $tableConfig[$field]['errcode'], 70005);
                }
            }
        }
    }

    protected static function hasdb($dbname) {
        if (!isset(self::$db_config[$dbname])) {
            throw new Exception('database not defined: '.$dbname, 70004);
        }
    }

    private static function dealWhere(&$where) {
        /* empty AND causes medoo error */
        if (empty($where['AND'])) {
            unset($where['AND']);
        }

        if (isset($where['LIMIT'])) {
            $limit = &$where['LIMIT'];
            if (!isset($limit['startno'])) {
                $limit['startno'] = 0;
            }
            if (!isset($limit['limit'])) {
                $limit['limit'] = 10000;
            }
            krsort($limit);
            $limit = array_values($limit);
        }
    }

    private static function writeLog($message,$type='info',$time=0) {
        Log::WriteLog($message, $type, $time, 'dbinfo');
    }

    private static function HasEmptyArray($arr) {
        foreach ($arr as $value) {
            if (is_array($value) && empty($value)) {
                return true;
            }
        }
        return false;
    }
}
