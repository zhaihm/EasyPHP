<?php
class MyController extends CBaseController
{
    public function init()
    {
        //header("Content-Type: text/xml;text/html");
        parent::init();
        Log::WriteLog('request['.CHttpRequestInfo::GetFullUrl().']','debug');

        /** todo       check signature     */
    }
    public function validateuser() {
        $userid = CHttpRequestInfo::Param('userid');
        $ret = EpUserService::QueryUserById($userid);
        if (empty($ret)) {
            OutputManager::OutputJson(array('code'=>-1,'message'=>'no such userid['.$userid.']'));
            die();
        }
    }
}