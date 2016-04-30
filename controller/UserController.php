<?php
require_once dirname(__FILE__)."/../library/MyController.php";

class UserController extends MyController {
    public function actionLogin() {
        $account = CHttpRequestInfo::Param('account');
        $password = UserService::GeneratePassword(CHttpRequestInfo::Param('password'));

        $ret = EpUserService::QueryOneAccount(array('AND'=>array('account'=>$account, 'password'=>$password)));
        if (empty($ret)) {
            OutputManager::OutputJson(array('code'=>-1, 'message'=>'login failed'));
            return;
        }
        unset($ret['password']);

        OutputManager::OutputJson(array('code'=>0, 'message'=>'success', 'data'=>$ret));
    }
}

