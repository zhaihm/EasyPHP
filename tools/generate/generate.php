<?php

define( 'DS', DIRECTORY_SEPARATOR );
define( 'CURRENT_DIR', dirname( __FILE__ ));
define( 'ROOT', dirname(dirname(CURRENT_DIR)));
define( 'WEBNAME', substr(ROOT, strrpos(ROOT, DS) + 1));
require_once(ROOT."/library/base/CBaseMysqlDb.php");
require_once(ROOT."/library/common/Log.php");
require_once(ROOT."/library/common/FileHelper.php");
require_once(ROOT."/library/http/CHttpRequestInfo.php");
require_once(CURRENT_DIR."/generatecontroller.php");
require_once(CURRENT_DIR."/generatedao.php");
require_once(CURRENT_DIR."/generateservice.php");
require_once(CURRENT_DIR."/generateviews.php");
require_once(CURRENT_DIR."/func.php");

$commands = array(
    'all' => array('function'=>'GenerateAll'),
    'dao' => array('function'=>'GenerateDao'),
    'service' => array('function'=>'GenerateService'),
    'controller' => array('function'=>'GenerateController'),
    'view' => array('function'=>'GenerateView'),

);

if ($argc == 3 && in_array(@$argv[1], array_keys($commands))) {
    $obj = new GenerateTool($argv[2]);
    $obj->$commands[$argv[1]]['function']();
} else {
    print("Usage: ".$argv[0]. " ".implode('|',array_keys($commands))." dbname\n");
}

class GenerateTool {
    private $controllerpath;
    private $servicepath;
    private $daopath;
    private $viewpath;
    private $dbname;
    private $tables;
    private $conn;

    public function __construct($dbname) {
        $this->dbname = $dbname;
        $this->controllerpath = ROOT.DS."controller_generated";
        $this->servicepath = ROOT.DS.DS."service".DS."dataservice";
        $this->daopath = ROOT.DS."dao".DS."db";
        $this->viewpath = ROOT.DS."views";

        $this->conn = new CBaseMysqlDb("127.0.0.1","root","root",$dbname);
        $ret = $this->conn->select("show tables;");
        if (empty($ret)) {
            echo "database[{$dbname}] no exists\n";
            die();
        }
        foreach($ret as $key => $info){
            $this->tables[] = $info["Tables_in_".strtolower($dbname)];
        }
    }
    public function GenerateController() {
        foreach($this->tables as $key=>$tablename) {
            $fieldset = $this->conn->select("desc $tablename;");
            $fields = array();
            foreach($fieldset as $key => $info) {
                $fields[] = $info["Field"];
            }
            $shorttable = GetShortTableName($tablename);
            $primarykey = GetPrimaryKey($fieldset);

            DoGenerateController($this->dbname, $tablename, $fields, $this->controllerpath, $primarykey, array($primarykey, 'createtime'));
        }
    }
    public function GenerateDao() {
        DoGenerateDao($this->dbname, $this->tables, $this->daopath);
    }
    public function GenerateService() {
        $tableinfos;
        foreach($this->tables as $key=>$tablename) {
            $fieldset = $this->conn->select("desc $tablename;");
            $tableinfos[] = array('tablename'=>$tablename, 'primarykey'=>GetPrimaryKey($fieldset));
        }
        DoGenerateService($this->dbname, $tableinfos, $this->servicepath);
    }
    public function GenerateView() {
        $tableconfigs = array();

        foreach($this->tables as $key=>$tablename) {
            $tableinfo = $tableconfigs[$tablename];
            $fieldset = $this->conn->select("desc $tablename;");
            $tableinfo['fields'] = array();
            $tableinfo['tablename'] = $tablename;
            foreach($fieldset as $key => $info) {
                $tableinfo['fields'][] = $info["Field"];
            }
            $tableinfo['primarykey'] = GetPrimaryKey($fieldset);
            $tableinfo['shorttable'] = GetShortTableName($tablename);
            //$primarykey = GetPrimaryKey($fieldset);
            DoGenerateView($tableinfo, $this->viewpath);
        }
    }
}
