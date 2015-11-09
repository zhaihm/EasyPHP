<?php
class DbHelper {
    public static function GetPagingConditions($and=array(), $orderby='') {
        $params = CHttpRequestInfo::ParamArray(array('limit','startno','begintime','endtime'), false);

        $conditions = array();
        if (isset($params['limit']) && isset($params['startno'])) {
            $conditions['LIMIT'] = array($params['limit'], $params['startno']);
        } else if (isset($params['limit']) && !isset($params['startno'])) {
            $conditions['LIMIT'] = array($params['limit']);
        }

        if (!empty($and)) {
            $conditions['AND'] = $and;
        }
        if (isset($params['begintime'])) {
            $conditions['AND'][$timekey.'[>=]'] = $params['begintime'].' 00:00:00';
        }
        if (isset($params['endtime'])) {
            $conditions['AND'][$timekey.'[<=]'] = $params['endtime'].' 23:59:59';
        }

        if (!empty($orderby)) {
            $conditions['ORDER'] = $orderby.' DESC';
        }

        return $conditions;
    }
}

